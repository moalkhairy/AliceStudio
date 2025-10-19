<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudioResource;
use App\Http\Resources\StepResource;
use App\Models\Studio;
use App\Models\Step;
use App\Models\Section;
use App\Models\Choice;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudioPublicController extends Controller
{
    // -------- READ --------
    public function show(Request $request, Studio $studio)
    {
        $gender = $request->query('gender');

        $studio->load([
            'steps' => fn($q) => $q->orderBy('order'),
            'steps.stepSections' => function ($q) {
                $q->orderBy('order')->whereHas('section', fn($qq) => $qq->where('is_active', 1));
            },
            'steps.stepSections.section' => function ($q) use ($gender) {
                $q->with([
                    'group',
                    'choices' => function ($qc) use ($gender) {
                        $qc->orderBy('order')->where('is_active', 1);
                        if ($gender) {
                            $qc->where(function ($w) use ($gender) {
                                $w->where('gender_scope', $gender)->orWhere('gender_scope', 'both');
                            });
                        }
                    }
                ])->where('is_active', 1);

                if ($gender) {
                    $q->where(function ($w) use ($gender) {
                        $w->where('gender_scope', $gender)->orWhere('gender_scope', 'both');
                    });
                }
            },
        ]);

        return new StudioResource($studio);
    }

    public function steps(Studio $studio)
    {
        $studio->load(['steps' => fn($q) => $q->orderBy('order')]);
        return StepResource::collection($studio->steps);
    }

    public function sections(Request $request, Step $step)
    {
        $gender = $request->query('gender');

        $step->load([
            'stepSections' => fn($q) => $q->orderBy('order')->whereHas('section', fn($qq) => $qq->where('is_active', 1)),
            'stepSections.section' => function ($q) use ($gender) {
                $q->with([
                    'group',
                    'choices' => function ($qc) use ($gender) {
                        $qc->orderBy('order')->where('is_active', 1);
                        if ($gender) {
                            $qc->where(function ($w) use ($gender) {
                                $w->where('gender_scope', $gender)->orWhere('gender_scope', 'both');
                            });
                        }
                    }
                ])->where('is_active', 1);

                if ($gender) {
                    $q->where(function ($w) use ($gender) {
                        $w->where('gender_scope', $gender)->orWhere('gender_scope', 'both');
                    });
                }
            },
        ]);

        return response()->json([
            'data' => $step->stepSections->pluck('section')->filter()->values(),
        ]);
    }

    // -------- GENERATE (NEW) --------

    /**
     * POST /api/v1/studios/{code}/generate
     * Body:
     * {
     *   "gender": "male|female|both",
     *   "selections": {
     *      "<section_code>": { "type": "chips|text|textarea|upload", "values": ["slug"...], "value": "string" }
     *   }
     * }
     * Returns: { "image_url": "https://..." }
     */
    public function generate(Request $request, Studio $studio)
    {
        $data = $request->validate([
            'gender' => ['nullable', Rule::in(['male', 'female', 'both'])],
            'selections' => ['required', 'array'],
        ]);

        $gender = $data['gender'] ?? null;
        $selections = $data['selections'];

        // Validate that provided section codes belong to this studio
        $sectionCodes = array_keys($selections);
        $sections = Section::where('studio_id', $studio->id)
            ->whereIn('code', $sectionCodes)
            ->with('choices:id,section_id,slug,token,negative_token,weight')
            ->get()
            ->keyBy('code');

        // Assemble prompt from selections
        $positiveParts = [];
        $negativeParts = [];

        foreach ($selections as $sectionCode => $payload) {
            $sec = $sections->get($sectionCode);
            if (!$sec) continue; // ignore unknown

            $type = $payload['type'] ?? 'chips';

            if ($type === 'chips') {
                $slugs = collect($payload['values'] ?? [])->filter()->values();
                if ($slugs->isNotEmpty()) {
                    $map = $sec->choices->whereIn('slug', $slugs->all());
                    foreach ($map as $c) {
                        if (!empty($c->token)) {
                            $positiveParts[] = trim($c->token);
                        }
                        if (!empty($c->negative_token)) {
                            $negativeParts[] = trim($c->negative_token);
                        }
                    }
                }
            } elseif ($type === 'text' || $type === 'textarea') {
                $val = trim((string)($payload['value'] ?? ''));
                if ($val !== '') $positiveParts[] = $val;
            }
            // uploads are omitted here; handle separately if you want to send image refs to the model
        }

        $positive = trim(collect($positiveParts)->filter()->implode(', '));
        $negative = trim(collect($negativeParts)->filter()->implode(', '));

        // TODO: Replace this stub with your real Gemini call.
        // Example: $imageUrl = app(GeminiService::class)->generate($positive, $negative, $gender, $selections);
        // For now we return a placeholder URL so the UI flow works end-to-end.
        $imageUrl = 'https://images.unsplash.com/photo-1612198182706-f09f36c5a3a3?q=80&w=1200&auto=format&fit=crop';

        return response()->json([
            'prompt' => $positive,
            'negative' => $negative,
            'image_url' => $imageUrl,
        ]);
    }
}
