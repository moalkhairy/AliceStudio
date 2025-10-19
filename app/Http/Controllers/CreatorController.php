<?php
// app/Http/Controllers/CreatorController.php
namespace App\Http\Controllers;

use App\Models\Step;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CreatorController extends Controller
{
    public function index()
    {
        $steps = Step::query()
            ->where('is_active', 1)
            ->orderBy('order')
            ->with(['sections' => function ($q) {
                $q->where('sections.is_active', 1)
                    ->orderBy('step_sections.order')
                    ->with(['group', 'choices']);
            }])
            ->get();

        // Map: step → (sections grouped by section->group)
        $wizard = [
            'steps' => $steps->map(function ($step) {
                // group sections by group code (nullable => "_general")
                $byGroup = $step->sections->groupBy(function ($sec) {
                    return optional($sec->group)->code ?? '_general';
                });

                $groups = $byGroup->map(function ($sections, $groupCode) {
                    $g = optional($sections->first()->group);

                    return [
                        'key' => $groupCode,
                        'name' => $g->name ?? 'General',
                        'exclusive' => (bool)($g->exclusive_sections ?? false),
                        'sections' => $sections->map(function ($sec) {
                            return [
                                'key' => $sec->code,
                                'label' => $sec->name,
                                'help' => (string)$sec->help_text,
                                'type' => $sec->input_type,        // 'chips' | 'text' | 'textarea' | 'upload'
                                'selection' => $sec->selection_mode,    // 'single' | 'multiple'
                                'gender' => $sec->gender_scope,      // 'male' | 'female' | 'both'
                                'min' => $sec->min_select,
                                'max' => $sec->max_select,
                                'required' => (bool)$sec->is_required,
                                'options' => $sec->choices->map(fn($c) => [
                                    'key' => $c->slug,
                                    'label' => $c->label,
                                    'icon' => $c->icon_url,
                                    // tokens/weights available if you need them
                                ])->values(),
                            ];
                        })->values(),
                    ];
                })->values();

                return [
                    'key' => $step->code,
                    'title' => $step->title,
                    'subtitle' => (string)$step->subtitle,
                    'tip' => (string)$step->tip_text,
                    'groups' => $groups,
                ];
            })->values(),
        ];

        return view('creator.index', compact('wizard'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'gender' => 'required|in:male,female',
            'selections' => 'required|json',
            'images' => 'nullable|array|max:3',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $gender = $request->string('gender');
        $selections = json_decode($request->string('selections'), true) ?: [];

        // Store files
        $urls = [];
        foreach (($request->file('images') ?? []) as $file) {
            $path = $file->store('creator/uploads', 'public');
            $urls[] = Storage::disk('public')->url($path);
        }

        // TODO: Validate selections against DB + rules if needed (server-side)
        // TODO: Call your Gemini image generator here with $gender, $selections, $urls

        // Placeholder result:
        $imageUrl = 'https://images.unsplash.com/photo-1520975682031-6de7c8d4eb8f?q=80&w=1200&auto=format&fit=crop';

        return response()->json([
            'status' => 'ok',
            'image_url' => $imageUrl,
        ]);
    }
}
