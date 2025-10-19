<?php

namespace App\Http\Controllers\Admin;

use App\Models\Group;
use App\Models\Studio;
use Illuminate\Http\Request;

class GroupController extends MainController
{
    public function __construct()
    {
        $this->moduleName = 'groups';
        $this->model = Group::class;
    }

    public function index(Request $request)
    {
        $this->checkPermission('browse');

        $q = Group::query()->with('studio');
        if ($studioId = $request->get('studio_id')) $q->where('studio_id', $studioId);
        if ($search = $request->get('q')) {
            $q->where(function ($w) use ($search) {
                $w->where('name', 'like', "%$search%")->orWhere('code', 'like', "%$search%");
            });
        }
        $groups = $q->orderBy('order')->paginate(20)->withQueryString();
        $studios = Studio::select('id', 'name')->orderBy('order')->get();

        return view('admin.groups.index', compact('groups', 'studios'));
    }

    public function create()
    {
        $this->checkPermission('add');
        $studios = Studio::select('id', 'name')->orderBy('order')->get();
        return view('admin.groups.create', compact('studios'));
    }

    public function store(Request $request)
    {
        $this->checkPermission('add');
        $data = $request->validate([
            'studio_id' => 'required|exists:studios,id',
            'code' => 'required|string|unique:groups,code',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'exclusive_sections' => 'nullable|boolean',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);
        Group::create($data);
        return redirect()->route('voyager.groups.index')->with('success', 'Created successfully.');
    }

    public function edit($id)
    {
        $this->checkPermission('edit');
        $group = Group::findOrFail($id);
        $studios = Studio::select('id', 'name')->orderBy('order')->get();
        return view('admin.groups.edit', compact('group', 'studios'));
    }

    public function update(Request $request, $id)
    {
        $this->checkPermission('edit');
        $group = Group::findOrFail($id);
        $data = $request->validate([
            'studio_id' => 'required|exists:studios,id',
            'code' => 'required|string|unique:groups,code,' . $group->id,
            'name' => 'required|string',
            'description' => 'nullable|string',
            'exclusive_sections' => 'nullable|boolean',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);
        $group->update($data);
        return redirect()->route('voyager.groups.index')->with('success', 'Updated successfully.');
    }

    public function destroy($id)
    {
        $this->checkPermission('delete');
        Group::findOrFail($id)->delete();
        return redirect()->route('voyager.groups.index')->with('success', 'Deleted successfully.');
    }

    public function show($id)
    {
        $this->checkPermission('read');
        $group = Group::with('sections')->findOrFail($id);
        return view('admin.groups.show', compact('group'));
    }
}
