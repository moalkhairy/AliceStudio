<?php

namespace App\Http\Controllers;

use App\Models\Step;
use App\Models\StepSection;
use App\Models\Studio;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use GuzzleHttp\Client;

class CreatorController extends Controller
{
    public function index(Request $request)
    {
        // 1) Pick studio (by ?studio=code or first active)
        $studioQuery = Studio::query();
        if ($request->filled('studio')) {
            $studioQuery->where('code', $request->string('studio'));
        } else {
            $studioQuery->where('is_active', 1);
        }

        // Load steps & groups (orders respected if you have order columns)
        $studio = $studioQuery
            ->with([
                'steps' => fn($q) => $q->orderBy('order'),
                'groups' => fn($q) => $q->orderBy('order'),
            ])
            ->firstOrFail();

        // 2) Build steps payload by reading StepSection pivot explicitly
        $stepsPayload = $studio->steps->map(function ($step) use ($studio) {
            // Pull StepSection rows for this step in pivot order
            $pivotRows = StepSection::with([
                'section.group',
                'section.choices' => fn($q) => $q->where('is_active', 1)->orderBy('order'),
            ])
                ->where('step_id', $step->id)
                ->where('is_active', 1)
                ->orderBy('order')
                ->get();

            // Collect Sections from pivot rows
            $sections = $pivotRows->pluck('section')->filter();

            // Group sections by their group_id (may be null → we treat as its own bucket)
            $sectionsByGroupId = $sections->groupBy(function ($section) {
                return optional($section->group)->id ?? 'nogroup';
            });

            // Determine group order: use studio->groups order, then put 'nogroup' at the end
            $orderedGroupIds = $studio->groups->pluck('id')->toArray();
            $orderedBuckets = [];

            foreach ($orderedGroupIds as $gid) {
                if ($sectionsByGroupId->has($gid)) {
                    $orderedBuckets[] = $gid;
                }
            }
            if ($sectionsByGroupId->has('nogroup')) {
                $orderedBuckets[] = 'nogroup';
            }

            // Map groups → sections payload
            $groupsPayload = collect($orderedBuckets)->map(function ($bucketId) use ($sectionsByGroupId, $studio) {
                $group = $bucketId === 'nogroup'
                    ? null
                    : $studio->groups->firstWhere('id', $bucketId);

                $sectionsPayload = $sectionsByGroupId[$bucketId]->map(function ($section) {
                    // Keys and defaults
                    $sectionKey = $section->code ?: Str::slug($section->name ?? ('section_' . $section->id), '_');

                    $options = $section->choices->map(function ($choice) {
                        $optKey = $choice->slug ?: ($choice->token ?: Str::slug($choice->label ?? ('choice_' . $choice->id), '_'));
                        return [
                            'key' => $optKey,
                            'label' => $choice->label ?? Str::headline($optKey),
                            'icon' => $choice->icon_url ?: null,
                        ];
                    })->values();

                    return [
                        'key' => $sectionKey,
                        'label' => $section->name ?? Str::headline($sectionKey),
                        'type' => $section->input_type ?? 'chips',          // chips|select|text|textarea|checkbox|boolean
                        'selection' => $section->selection_mode ?? 'single',     // single|multiple
                        'gender' => strtolower($section->gender_scope ?? 'both'), // male|female|both
                        'options' => $options,
                    ];
                })->values();

                // Be flexible about the exclusivity field name
                $exclusive = false;
                if ($group) {
                    $exclusive = (bool)($group->exclusive_sections
                        ?? $group->is_exclusive
                        ?? $group->exclusive
                        ?? false);
                }

                return [
                    'exclusive' => $exclusive,
                    'sections' => $sectionsPayload,
                ];
            })->filter(fn($g) => count($g['sections']) > 0)->values();

            return [
                'title' => $step->title ?? 'Step',
                'subtitle' => $step->subtitle,
                'tip' => $step->tip_text,
                'groups' => $groupsPayload,
            ];
        })->values();

        $wizard = ['steps' => $stepsPayload];

        return view('creator.index', compact('wizard'));
    }


    public function generate(Request $request)
    {
        $request->validate([
            'gender' => 'required|in:male,female',
            'selections' => 'required|string', // JSON string from the front
            // images[] might exist but we ignore them here since we send prompt-only
        ]);

        $gender = strtolower($request->string('gender'));
        $selections = json_decode($request->string('selections'), true) ?: [];

        // ---- Extract helpers from your JSON structure (same keys you sent earlier) ----
        // selections = { appearance:{type:'chips',values:[...]}, age:{...}, ... }
        $getOne = function (array $obj, string $key): ?string {
            $b = $obj[$key] ?? null;
            if (!$b || !is_array($b)) return null;
            $vals = $b['values'] ?? null;
            if (is_array($vals) && count($vals)) return (string)$vals[0];
            return null;
        };

        $getMany = function (array $obj, string $key): array {
            $b = $obj[$key] ?? null;
            if (!$b || !is_array($b)) return [];
            $vals = $b['values'] ?? null;
            return is_array($vals) ? array_values(array_filter($vals, 'strlen')) : [];
        };

        // Basic attributes (mirrors your TS builder parts)
        $age = $getOne($selections, 'age');           // e.g. young_adult
        $ethnicity = $getOne($selections, 'ethnicity');     // e.g. south_asian
        $hairStyleK = $getOne($selections, 'hair_style');    // e.g. long_hair
        $hairColorK = $getOne($selections, 'hair_color');    // e.g. blonde_hair
        $appearance = $getMany($selections, 'appearance');   // e.g. [with_glasses]
        $poses = $getMany($selections, 'pose_gesture'); // e.g. [confident]
        $sceneTags = $getMany($selections, 'scene_tags');   // e.g. [urban]
        $lightingK = $getOne($selections, 'lighting_style');// e.g. soft_natural
        $propK = $getOne($selections, 'accessory_proportion'); // e.g. natural_size
        $accTypeK = $getOne($selections, 'accessory_type');// e.g. earrings

        // Optional extra controls if you’re sending them from the UI
        $aspectRatio = $getOne($selections, 'aspect_ratio') ?? '1:1';   // 16:9 | 9:16 | 1:1
        // If you use “proportion” radio in UI, it’s already mapped in accessory_proportion

        // ---- Label mappers (human-readable phrases) ----
        $genderLabel = $gender === 'male' ? 'man' : 'woman';

        $ageMap = [
            'teen' => 'teen', 'young_adult' => 'young adult', 'adult' => 'adult', 'middle_aged' => 'middle-aged', 'senior' => 'senior',
        ];
        $ethMap = [
            'east_asian' => 'East Asian', 'south_asian' => 'South Asian', 'middle_eastern' => 'Middle Eastern',
            'black' => 'Black', 'white' => 'White', 'latino' => 'Latino', 'mediterranean' => 'Mediterranean',
        ];
        $hairStyleMap = [
            'short_hair' => 'short', 'long_hair' => 'long', 'curly_hair' => 'curly', 'wavy_hair' => 'wavy', 'straight_hair' => 'straight',
        ];
        $hairColorMap = [
            'blonde_hair' => 'blonde', 'black_hair' => 'black', 'brown_hair' => 'brown', 'red_hair' => 'red',
            'gray_hair' => 'gray', 'silver_hair' => 'silver', 'dyed_hair' => 'dyed',
        ];
        $appearanceMap = [
            'with_glasses' => 'glasses',
            'with_freckles' => 'freckles',
            'with_tattoos' => 'tattoos',
            'with_piercing' => 'a piercing',
        ];
        $poseMap = [
            'confident' => 'confident', 'casual' => 'casual', 'elegant' => 'elegant', 'dynamic' => 'dynamic',
            'hands_on_hips' => 'hands-on-hips',
        ];
        $sceneMap = [
            'urban' => 'an urban outdoor setting', 'studio' => 'a clean studio background',
            'indoor' => 'a modern indoor environment', 'night' => 'a nighttime city ambience',
            'beach' => 'a sunny beach with clear blue water',
        ];
        $lightingTextMap = [
            // EXACT strings from your TS switch (soft / dramatic / golden / cinematic / backlit / rim / automatic)
            'soft_natural' => 'The lighting should be soft, natural, and diffused, like from an overcast sky or a large window, creating gentle shadows and a flattering, realistic look.',
            'dramatic' => 'Employ dramatic studio lighting with high contrast, deep shadows, and sharp highlights (chiaroscuro) to create a moody, high-fashion, and impactful image.',
            'golden' => 'The scene must be illuminated by the warm, soft, and directional light of the \'golden hour\' (just after sunrise or before sunset), casting long shadows and creating a dreamy, evocative, and warm-toned atmosphere.',
            'cinematic' => 'The lighting should be cinematic and dramatic, using strong key lights and fill lights to create a moody, film-like atmosphere with rich colors and deep, narrative-driven shadows.',
            'backlit' => 'The main light source should be placed behind the model, creating a bright outline (halo effect) around their silhouette and separating them from the background. This should create a sense of depth and romance.',
            'rim' => 'Utilize strong rim lighting from the side or back to trace the contours of the model and accessory with a thin, bright line of light, emphasizing their shape and creating a dramatic separation from the background.',
            'automatic' => 'The lighting should be professional and flattering, consistent with a high-end advertising campaign.',
        ];
        $propTextMap = [
            // EXACT semantics from your TS (small / large / natural)
            'small_size' => 'Crucially, the accessory must be rendered as very small and delicate on the model. It should be a subtle accent, not a dominant feature, appearing much smaller than its typical size.',
            'large_size' => 'Slightly increase the size of the accessory to make it a prominent focal point. It should be noticeably larger than average, creating a bold statement, but must remain believable and aesthetically pleasing, avoiding unrealistic exaggeration.',
            'natural_size' => 'Render the accessory in a natural, realistic, and true-to-life proportion, perfectly scaled to the model\'s features as it would be worn in reality. Ensure the size is coherent and believable.',
        ];
        $aspectMap = [
            '16:9' => 'The final output image must be rendered in a widescreen landscape format with a 16:9 aspect ratio.',
            '9:16' => 'The final output image must be rendered in a tall portrait format with a 9:16 aspect ratio.',
            '1:1' => 'The final output image must be rendered in a square format with a 1:1 aspect ratio.',
        ];

        // ---- Build model identity (matches your TS buildModelDescription approach) ----
        $identityBits = [];
        if ($age && isset($ageMap[$age])) $identityBits[] = $ageMap[$age];
        if ($ethnicity && isset($ethMap[$ethnicity])) $identityBits[] = $ethMap[$ethnicity];
        $identityBits[] = $genderLabel; // man/woman
        $coreIdentity = trim(implode(' ', array_filter($identityBits))) ?: 'a model';

        // with-clauses: hair + appearance (strip “ hair” like TS)
        $hairParts = [];
        if ($hairStyleK && isset($hairStyleMap[$hairStyleK])) $hairParts[] = $hairStyleMap[$hairStyleK];
        if ($hairColorK && isset($hairColorMap[$hairColorK])) $hairParts[] = $hairColorMap[$hairColorK];
        $withClauses = [];
        if (!empty($hairParts)) $withClauses[] = trim(implode(' ', $hairParts)) . ' hair';
        foreach ($appearance as $a) {
            $label = $appearanceMap[$a] ?? null;
            if ($label) $withClauses[] = $label;
        }
        $withText = '';
        if (!empty($withClauses)) {
            $withText = ' with ' . $this->listEnglish($withClauses);
        }

        // pose text
        $poseLabels = array_values(array_filter(array_map(fn($p) => $poseMap[$p] ?? null, $poses)));
        $poseText = '';
        if (!empty($poseLabels)) {
            $poseText = ', posing in a ' . $this->listEnglish($poseLabels) . ' manner';
        }

        // scene text
        $sceneBits = array_values(array_filter(array_map(fn($s) => $sceneMap[$s] ?? null, $sceneTags)));
        $sceneSentence = '';
        if (!empty($sceneBits)) {
            $sceneSentence = 'The scene is ' . $this->listEnglish($sceneBits) . '.';
        }

        // accessory noun
        $accNoun = $accTypeK ? str_replace('_', ' ', $accTypeK) : 'accessory';

        // proportion, lighting, aspect texts
        $propText = $propTextMap[$propK] ?? $propTextMap['natural_size'];
        $lightingText = $lightingTextMap[$lightingK] ?? $lightingTextMap['automatic'];
        $aspectText = $aspectMap[$aspectRatio] ?? $aspectMap['1:1'];

        // ---- FINAL PROMPT (exact phrasing from your TS version) ----
        $modelLine =
            "A professional, high-resolution, and photorealistic photograph of {$coreIdentity}{$withText}{$poseText}, " .
            "prominently featuring the {$accNoun}.";

        $replicaLine =
            "The primary goal is to create a stunning product shot where the accessory is the undeniable focal point. " .
            "It is critical that the generated accessory is an absolutely identical, flawless replica of the real item. " .
            "Replicate every detail with extreme precision: color, texture, shape, materials, and any unique features like gemstones or clasps. " .
            "Pay special attention to replicating intricate textures (like fabric weaves, leather grain, or metallic finishes), small embellishments " .
            "(such as tiny jewels, engravings, or stitching), and specific material properties (like glossiness, matte finishes, or transparency). " .
            "Do not alter, simplify, or reinterpret the accessory in any way.";

        $integrationLine =
            "The accessory must be physically and realistically integrated with the model, appearing as if it is truly being worn. " .
            "Pay meticulous attention to contact points, shadows, and interactions with skin, hair, and clothing. " .
            "Ensure realistic shadows, reflections, and perspective that enhance the accessory's appearance.";

        $lightingLine = $lightingText;
        $propLine = $propText;
        $aspectLine = $aspectText;

        $prompt = trim(
            $modelLine . " " .
            $replicaLine . " " .
            $integrationLine . " " .
            ($sceneSentence ? ($sceneSentence . " ") : "") .
            $propLine . " " .
            $lightingLine . " " .
            "While the image must be hyper-realistic, the accessory must remain the hero of the image. " .
            $aspectLine
        );

        $imageParts = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                if (!$file->isValid()) continue;
                $mime = $file->getMimeType() ?: 'image/png'; // fallback
                $bytes = file_get_contents($file->getRealPath());
                $b64 = base64_encode($bytes);

                // Only allow common formats
                if (!in_array($mime, ['image/png', 'image/jpeg', 'image/webp'])) {
                    // you can skip or normalize; here we skip unsupported types
                    continue;
                }
                $imageParts[] = [
                    'inlineData' => [
                        'mimeType' => $mime,
                        'data' => $b64, // NOTE: base64 only, no "data:image/..." prefix
                    ],
                ];
            }
        }

        // ---------- If you ONLY want to return the prompt to the frontend, uncomment:
        // return response()->json(['status' => 'ok', 'prompt' => $prompt]);


        $apiKey = config('services.gemini.key');

        // Use an image-capable model; text-only input is fine.
        $model = 'gemini-2.5-flash-image-preview';

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        $parts = array_merge($imageParts, [['text' => $prompt]]);

        // We send ONLY the prompt text; ask for an image response
        $payload = [
            'contents' => [[
                'role' => 'user',
                'parts' => $parts,
            ]],
            // optional tuning only (no response_mime_type here)
            'generationConfig' => [
                'temperature' => 0.9,
                'topP' => 0.95,
                'candidateCount' => 1,
                // 'maxOutputTokens' => 2048, // optional
            ],
        ];


        $resp = Http::withHeaders(['Content-Type' => 'application/json'])->post($url, $payload);

        if ($resp->failed()) {
            return response()->json([
                'status' => 'error',
                'prompt' => $prompt,
                'api_error' => $resp->json() ?: $resp->body(),
            ], $resp->status());
        }

        $json = $resp->json();

        // Find the first image part (inlineData)
        $parts = data_get($json, 'candidates.0.content.parts', []);
        $imagePart = collect($parts)->first(function ($p) {
            return isset($p['inlineData']['data']) && isset($p['inlineData']['mimeType']);
        });

        if (!$imagePart) {
            // If the model returned text instead of an image, surface it for debugging
            $maybeText = collect($parts)->firstWhere('text')["text"] ?? null;
            return response()->json([
                'status' => 'error',
                'prompt' => $prompt,
                'message' => 'No image returned by the model.',
                'model_text' => $maybeText,
                'raw' => $json,
            ], 422);
        }

        $mime = $imagePart['inlineData']['mimeType'] ?? 'image/png';
        $base64 = $imagePart['inlineData']['data'];
        $binary = base64_decode($base64);

        // Persist the image to /storage/app/public/...
        $ext = str_contains($mime, 'png') ? 'png' : (str_contains($mime, 'jpeg') ? 'jpg' : 'webp');
        $path = 'generated/' . date('Y/m/') . uniqid('img_', true) . "." . $ext;

        Storage::disk('public')->put($path, $binary);
        $imageUrl = Storage::disk('public')->url($path);

        app(\App\Services\WalletService::class)->adjust(
            auth('client')->user(),
            -1,
            'image_generation',
            ['prompt' => $prompt] // optional
        );

        return response()->json([
            'status' => 'ok',
            'image_url' => $imageUrl,
            'prompt' => $prompt, // useful for logging/debug
        ]);

        // ---------- Otherwise: send PROMPT-ONLY to Gemini (text) ----------
//        $apiKey = config('services.gemini.key');
//        $model = config('services.gemini.model', 'gemini-2.0-flash');
//
//        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";
//
//        // The ONLY valid JSON shape for prompt-only:
//        $payload = [
//            'contents' => [
//                [
//                    'role' => 'user',
//                    'parts' => [
//                        ['text' => $prompt],
//                    ],
//                ],
//            ],
//        ];
//
//        $resp = Http::withHeaders([
//            'Content-Type' => 'application/json',
//        ])
//            ->post($url, $payload);
//
//        if ($resp->failed()) {
//            // Surface API error body for debugging
//            return response()->json([
//                'status' => 'error',
//                'prompt' => $prompt,
//                'api_error' => $resp->json() ?: $resp->body(),
//            ], $resp->status());
//        }
//
//        $json = $resp->json();
//        $reply = data_get($json, 'candidates.0.content.parts.0.text');
//
//        return response()->json([
//            'status' => 'ok',
//            'prompt' => $prompt,
//            'gemini_reply' => $reply,
//            'raw' => $json, // optional; remove in prod
//        ]);
    }

    // Simple Oxford-comma joiner
    private function listEnglish(array $items): string
    {
        $items = array_values(array_filter($items, 'strlen'));
        $n = count($items);
        if ($n === 0) return '';
        if ($n === 1) return $items[0];
        if ($n === 2) return $items[0] . ' and ' . $items[1];
        return implode(', ', array_slice($items, 0, -1)) . ', and ' . end($items);
    }

// Natural English conjunction: A, B, and C (mirrors TS ListFormat effect)
    private
    function conjunct(array $items): string
    {
        $items = array_values(array_filter(array_map(fn($x) => trim((string)$x), $items)));
        $n = count($items);
        if ($n === 0) return '';
        if ($n === 1) return $items[0];
        if ($n === 2) return "{$items[0]} and {$items[1]}";
        $last = array_pop($items);
        return implode(', ', $items) . ", and {$last}";
    }

    public
    function submit(Request $request)
    {
        $studio = Studio::byCode($request->get('studio', 'model'));
        abort_unless($studio, 404);

        $current = (int)$request->input('current_step_index', 0);

        // Merge incoming inputs into session (keyed by section code)
        $incoming = $request->input('sections', []); // text/radios/checkboxes
        $filesMap = $request->files->get('sections', []); // uploads

        // Normalize checkbox arrays, keep strings as-is
        $normalized = [];
        foreach ($incoming as $code => $value) {
            $normalized[$code] = is_array($value) ? array_values(array_filter($value, fn($v) => $v !== null && $v !== '')) : $value;
        }

        // Persist files as UploadedFile arrays under their section codes
        // (Validation happens per-section in your final generate(); here we just store names in session to keep UX)
        $saved = Session::get('creator.inputs', []);
        $saved = array_replace_recursive($saved, $normalized);
        Session::put('creator.inputs', $saved);

        // Simple nav
        $nav = $request->input('nav', 'next'); // 'next' | 'back'
        $stepsCount = $studio->steps()->count();

        if ($nav === 'back') {
            $current = max(0, $current - 1);
        } else {
            $current = min($stepsCount - 1, $current + 1);
        }

        return redirect()->route('creator.show', [
            'studio' => $studio->code,
            'step' => $current,
            'gender' => $request->get('gender'),
        ])->withInput();
    }

//    public function generate(Request $request)
//    {
//        $studio = Studio::byCode($request->get('studio', 'model'));
//        abort_unless($studio, 404);
//
//        // Collect everything (session + current page)
//        $inputs = array_replace_recursive(
//            (array)Session::get('creator.inputs', []),
//            (array)$request->input('sections', [])
//        );
//
//        // Attach files (limit 3 per upload section on server)
//        $files = $request->files->get('sections', []);
//        foreach ($files as $code => $arr) {
//            if (is_array($arr)) {
//                $files[$code] = array_slice($arr, 0, 3);
//            }
//        }
//
//        // Basic validation example (tighten as needed using your DB metadata)
//        // e.g., ensure arrays for multi-select, strings for single/text, file types, etc.
//        // ... (keep minimal for now)
//
//        // >>> Your place to call Gemini / image service <<<
//        // Example payload structure you asked for (key/value pairs by section code):
//        // $payload = [
//        //     'gender'  => $request->get('gender'),
//        //     'inputs'  => $inputs,         // ['hair' => 'short', 'style'=>['cinematic','portrait'], 'prompt' => '...']
//        //     'uploads' => $files,          // ['reference_images' => [UploadedFile,...]]
//        // ];
//        //
//        // $imageUrl = app(YourGenerator::class)->generate($payload);
//
//        // For now we just echo back a “generated” placeholder
//        $imageUrl = null; // replace with real result URL/path
//        // If you store generated image in storage and return a path, pass it to the view below.
//
//        // Reset session selections if you prefer after generation
//        // Session::forget('creator.inputs');
//
//        return back()->with([
//            'generated_image_url' => $imageUrl,
//            'submitted_payload' => [
//                'gender' => $request->get('gender'),
//                'inputs' => $inputs,
//                // Don't flash raw files in session; you’ll process them directly above.
//            ],
//        ]);
//    }
}
