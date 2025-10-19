<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\MainController;
use App\Models\Step;
use App\Models\Studio;
use App\Models\Section;
use App\Models\StepSection;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class StepController extends MainController
{
    public function __construct()
    {
        $this->moduleName = 'steps';
        $this->model = Step::class;
    }

    public function index(Request $request)
    {
        $this->checkPermission('browse');

        $q = Step::query()->with('studio');
        if ($studioId = $request->get('studio_id')) {
            $q->where('studio_id', $studioId);
        }
        if ($search = $request->get('q')) {
            $q->where(function ($w) use ($search) {
                $w->where('title', 'like', "%$search%")
                    ->orWhere('code', 'like', "%$search%");
            });
        }

        $steps = $q->orderBy('order')->paginate(20)->withQueryString();
        $studios = Studio::select('id', 'name')->orderBy('order')->get();

        return view('admin.steps.index', compact('steps', 'studios'));
    }

    public function create()
    {
        $this->checkPermission('add');
        $studios = Studio::select('id', 'name')->orderBy('order')->get();
        return view('admin.steps.create', compact('studios'));
    }

    public function store(Request $request)
    {
        $this->checkPermission('add');

        $data = $request->validate([
            'studio_id' => ['required', 'exists:studios,id'],
            'code' => ['required', 'string', Rule::unique('steps', 'code')->where('studio_id', $request->studio_id)],
            'title' => ['required', 'string'],
            'subtitle' => ['nullable', 'string'],
            'icon' => ['nullable', 'string'],
            'tip_text' => ['nullable', 'string'],
            'order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        Step::create($data);
        return redirect()->route('voyager.steps.index')->with('success', 'Step created.');
    }

    public function edit($id)
    {
        $this->checkPermission('edit');
        $step = Step::findOrFail($id);
        $studios = Studio::select('id', 'name')->orderBy('order')->get();
        return view('admin.steps.edit', compact('step', 'studios'));
    }

    public function update(Request $request, $id)
    {
        $this->checkPermission('edit');
        $step = Step::findOrFail($id);

        $data = $request->validate([
            'studio_id' => ['required', 'exists:studios,id'],
            'code' => ['required', 'string', Rule::unique('steps', 'code')->where('studio_id', $request->studio_id)->ignore($step->id)],
            'title' => ['required', 'string'],
            'subtitle' => ['nullable', 'string'],
            'icon' => ['nullable', 'string'],
            'tip_text' => ['nullable', 'string'],
            'order' => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // guard: do not allow changing studio if the step already has assignments, unless same
        if ($step->studio_id != $data['studio_id']) {
            $hasAssignments = StepSection::where('step_id', $step->id)->exists();
            if ($hasAssignments) {
                return back()->withInput()->with('error', 'Clear sections from this step before changing the Studio.');
            }
        }

        $step->update($data);
        return redirect()->route('voyager.steps.index')->with('success', 'Step updated.');
    }

    public function show($id)
    {
        $this->checkPermission('read');

        $step = Step::with(['studio', 'stepSections.section.group'])->findOrFail($id);

        // Eligible sections for "Add Sections" picker (strict: cannot be in any other step in the same studio)
        $assignedSectionIds = StepSection::where('studio_id', $step->studio_id)->pluck('section_id')->all();

        $eligible = Section::query()
            ->where('studio_id', $step->studio_id)
            ->where('is_active', true)
            ->whereNotIn('id', $assignedSectionIds)
            ->orderBy('order')
            ->get(['id', 'name', 'code', 'gender_scope', 'group_id']);

        return view('admin.steps.show', compact('step', 'eligible'));
    }

    public function destroy($id)
    {
        $this->checkPermission('delete');
        Step::findOrFail($id)->delete();
        return redirect()->route('voyager.steps.index')->with('success', 'Step deleted.');
    }

    // -------------------------
    // Pivot actions (Step → Sections)
    // -------------------------

    public function addSections(Request $request, $id)
    {
        $this->checkPermission('edit');
        $step = Step::findOrFail($id);

        $validated = $request->validate([
            'section_ids' => ['required', 'array', 'min:1'],
            'section_ids.*' => ['integer', 'exists:sections,id'],
        ]);

        $sectionIds = $validated['section_ids'];

        DB::transaction(function () use ($step, $sectionIds) {
            // current max order
            $max = StepSection::where('step_id', $step->id)->max('order') ?? 0;

            foreach ($sectionIds as $sid) {
                // guard: same studio
                $sectionStudio = DB::table('sections')->where('id', $sid)->value('studio_id');
                if ($sectionStudio != $step->studio_id) {
                    continue; // skip invalid
                }

                // strict: section cannot belong to any step in this studio
                $already = StepSection::where('studio_id', $step->studio_id)
                    ->where('section_id', $sid)->exists();
                if ($already) continue;

                StepSection::create([
                    'studio_id' => $step->studio_id,
                    'step_id' => $step->id,
                    'section_id' => $sid,
                    'order' => ++$max,
                    'is_active' => true,
                ]);
            }
        });

        return redirect()->route('voyager.steps.show', $step->id)->with('success', 'Sections added.');
    }

    public function removeSection($id, $pivotId)
    {
        $this->checkPermission('edit');
        $step = Step::findOrFail($id);
        $pivot = StepSection::where('id', $pivotId)->where('step_id', $step->id)->firstOrFail();
        $pivot->delete();
        return redirect()->route('voyager.steps.show', $step->id)->with('success', 'Section removed from step.');
    }

    public function togglePivot($id, $pivotId)
    {
        $this->checkPermission('edit');
        $step = Step::findOrFail($id);
        $pivot = StepSection::where('id', $pivotId)->where('step_id', $step->id)->firstOrFail();
        $pivot->is_active = !$pivot->is_active;
        $pivot->save();

        return redirect()->route('voyager.steps.show', $step->id)->with('success', 'Section visibility toggled for this step.');
    }

    public function moveUp($id, $pivotId)
    {
        $this->checkPermission('edit');
        $step = Step::findOrFail($id);
        $pivot = StepSection::where('id', $pivotId)->where('step_id', $step->id)->firstOrFail();

        $above = StepSection::where('step_id', $step->id)
            ->where('order', '<', $pivot->order)
            ->orderBy('order', 'desc')->first();

        if ($above) {
            [$pivot->order, $above->order] = [$above->order, $pivot->order];
            $pivot->save();
            $above->save();
        }
        return redirect()->route('voyager.steps.show', $step->id);
    }

    public function moveDown($id, $pivotId)
    {
        $this->checkPermission('edit');
        $step = Step::findOrFail($id);
        $pivot = StepSection::where('id', $pivotId)->where('step_id', $step->id)->firstOrFail();

        $below = StepSection::where('step_id', $step->id)
            ->where('order', '>', $pivot->order)
            ->orderBy('order', 'asc')->first();

        if ($below) {
            [$pivot->order, $below->order] = [$below->order, $pivot->order];
            $pivot->save();
            $below->save();
        }
        return redirect()->route('voyager.steps.show', $step->id);
    }
}
