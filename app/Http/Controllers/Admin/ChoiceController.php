<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\MainController;
use App\Models\Choice;
use App\Models\Section;
use Illuminate\Http\Request;

class ChoiceController extends MainController
{
    public function __construct()
    {
        $this->moduleName = 'choices';
        $this->model = Choice::class;
    }

    public function index(Request $request)
    {
        $this->checkPermission('browse');

        $q = Choice::query()->with('section');

        if ($sectionId = $request->get('section_id')) $q->where('section_id', $sectionId);
        if ($search = $request->get('q')) {
            $q->where(function ($w) use ($search) {
                $w->where('label', 'like', "%$search%")
                    ->orWhere('slug', 'like', "%$search%")
                    ->orWhere('token', 'like', "%$search%");
            });
        }

        $choices = $q->orderBy('order')->paginate(25)->withQueryString();
        $sections = Section::select('id', 'name')->orderBy('order')->get();

        return view('admin.choices.index', compact('choices', 'sections'));
    }

    public function create()
    {
        $this->checkPermission('add');
        $sections = Section::select('id', 'name')->orderBy('order')->get();
        return view('admin.choices.create', compact('sections'));
    }

    public function store(Request $request)
    {
        $this->checkPermission('add');

        $data = $request->validate([
            'section_id' => 'required|exists:sections,id',
            'label' => 'required|string',
            'slug' => 'required|string',
            'token' => 'nullable|string',
            'negative_token' => 'nullable|string',
            'weight' => 'nullable|numeric',
            'icon_url' => 'nullable|url',
            'gender_scope' => 'required|in:male,female,both',
            'is_default' => 'nullable|boolean',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $exists = Choice::where('section_id', $data['section_id'])->where('slug', $data['slug'])->exists();
        if ($exists) return back()->withInput()->with('error', 'Slug must be unique within the section.');

        Choice::create($data);
        return redirect()->route('voyager.choices.index')->with('success', 'Created successfully.');
    }

    public function edit($id)
    {
        $this->checkPermission('edit');
        $choice = Choice::findOrFail($id);
        $sections = Section::select('id', 'name')->orderBy('order')->get();
        return view('admin.choices.edit', compact('choice', 'sections'));
    }

    public function update(Request $request, $id)
    {
        $this->checkPermission('edit');
        $choice = Choice::findOrFail($id);

        $data = $request->validate([
            'section_id' => 'required|exists:sections,id',
            'label' => 'required|string',
            'slug' => 'required|string',
            'token' => 'nullable|string',
            'negative_token' => 'nullable|string',
            'weight' => 'nullable|numeric',
            'icon_url' => 'nullable|url',
            'gender_scope' => 'required|in:male,female,both',
            'is_default' => 'nullable|boolean',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $exists = Choice::where('section_id', $data['section_id'])
            ->where('slug', $data['slug'])
            ->where('id', '!=', $choice->id)
            ->exists();
        if ($exists) return back()->withInput()->with('error', 'Slug must be unique within the section.');

        $choice->update($data);
        return redirect()->route('voyager.choices.index')->with('success', 'Updated successfully.');
    }

    public function destroy($id)
    {
        $this->checkPermission('delete');
        Choice::findOrFail($id)->delete();
        return redirect()->route('voyager.choices.index')->with('success', 'Deleted successfully.');
    }
}
