<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\MainController;
use App\Models\Section;
use App\Models\Studio;
use App\Models\Group;
use Illuminate\Http\Request;

class SectionController extends MainController
{
    public function __construct()
    {
        $this->moduleName = 'sections';
        $this->model = Section::class;
    }

    public function index(Request $request)
    {
        $this->checkPermission('browse');

        $q = Section::query()->with(['studio', 'group']);
        if ($studioId = $request->get('studio_id')) $q->where('studio_id', $studioId);
        if ($groupId = $request->get('group_id')) $q->where('group_id', $groupId);
        if ($gender = $request->get('gender_scope')) $q->where('gender_scope', $gender);
        if ($search = $request->get('q')) {
            $q->where(function ($w) use ($search) {
                $w->where('name', 'like', "%$search%")->orWhere('code', 'like', "%$search%");
            });
        }

        $sections = $q->orderBy('order')->paginate(20)->withQueryString();
        $studios = Studio::select('id', 'name')->orderBy('order')->get();
        $groups = Group::select('id', 'name')->orderBy('order')->get();

        return view('admin.sections.index', compact('sections', 'studios', 'groups'));
    }

    public function create()
    {
        $this->checkPermission('add');
        $studios = Studio::select('id', 'name')->orderBy('order')->get();
        $groups = Group::select('id', 'name')->orderBy('order')->get();
        return view('admin.sections.create', compact('studios', 'groups'));
    }

    public function store(Request $request)
    {
        $this->checkPermission('add');
        $data = $request->validate([
            'studio_id' => 'required|exists:studios,id',
            'group_id' => 'nullable|exists:groups,id',
            'code' => 'required|string',
            'name' => 'required|string',
            'help_text' => 'nullable|string',
            'input_type' => 'required|in:chips,text,textarea,upload',
            'selection_mode' => 'required|in:single,multiple',
            'min_select' => 'nullable|integer|min:0',
            'max_select' => 'nullable|integer|min:0',
            'gender_scope' => 'required|in:male,female,both',
            'is_required' => 'nullable|boolean',
            'is_general' => 'nullable|boolean',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $exists = Section::where('studio_id', $data['studio_id'])->where('code', $data['code'])->exists();
        if ($exists) return back()->withInput()->with('error', 'Code must be unique within the studio.');

        Section::create($data);
        return redirect()->route('voyager.sections.index')->with('success', 'Created successfully.');
    }

    public function edit($id)
    {
        $this->checkPermission('edit');
        $section = Section::findOrFail($id);
        $studios = Studio::select('id', 'name')->orderBy('order')->get();
        $groups = Group::select('id', 'name')->orderBy('order')->get();
        return view('admin.sections.edit', compact('section', 'studios', 'groups'));
    }

    public function update(Request $request, $id)
    {
        $this->checkPermission('edit');
        $section = Section::findOrFail($id);

        $data = $request->validate([
            'studio_id' => 'required|exists:studios,id',
            'group_id' => 'nullable|exists:groups,id',
            'code' => 'required|string',
            'name' => 'required|string',
            'help_text' => 'nullable|string',
            'input_type' => 'required|in:chips,text,textarea,upload',
            'selection_mode' => 'required|in:single,multiple',
            'min_select' => 'nullable|integer|min:0',
            'max_select' => 'nullable|integer|min:0',
            'gender_scope' => 'required|in:male,female,both',
            'is_required' => 'nullable|boolean',
            'is_general' => 'nullable|boolean',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $exists = Section::where('studio_id', $data['studio_id'])
            ->where('code', $data['code'])
            ->where('id', '!=', $section->id)
            ->exists();
        if ($exists) return back()->withInput()->with('error', 'Code must be unique within the studio.');

        $section->update($data);
        return redirect()->route('voyager.sections.index')->with('success', 'Updated successfully.');
    }

    public function show($id)
    {
        $this->checkPermission('read');
        $section = Section::with('choices')->findOrFail($id);
        return view('admin.sections.show', compact('section'));
    }

    public function destroy($id)
    {
        $this->checkPermission('delete');
        Section::findOrFail($id)->delete();
        return redirect()->route('voyager.sections.index')->with('success', 'Deleted successfully.');
    }
}
