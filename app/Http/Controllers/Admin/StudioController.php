<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\MainController;
use App\Models\Studio;
use Illuminate\Http\Request;

class StudioController extends MainController
{
    public function __construct()
    {
        $this->moduleName = 'studios';
        $this->model = Studio::class;
    }

    public function index(Request $request)
    {
        $this->checkPermission('browse');
        $q = Studio::query();

        if ($search = $request->get('q')) {
            $q->where(function ($w) use ($search) {
                $w->where('name', 'like', "%$search%")
                    ->orWhere('code', 'like', "%$search%");
            });
        }

        $studios = $q->orderBy('order')->paginate(15)->withQueryString();

        return view('admin.studios.index', compact('studios'));
    }

    public function create()
    {
        $this->checkPermission('add');
        return view('admin.studios.create');
    }

    public function store(Request $request)
    {
        $this->checkPermission('add');
        $data = $request->validate([
            'code' => 'required|string|unique:studios,code',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        Studio::create($data);
        return redirect()->route('voyager.studios.index')->with('success', 'Created successfully.');
    }

    public function edit($id)
    {
        $this->checkPermission('edit');
        $studio = Studio::findOrFail($id);
        return view('admin.studios.edit', compact('studio'));
    }

    public function update(Request $request, $id)
    {
        $this->checkPermission('edit');
        $studio = Studio::findOrFail($id);

        $data = $request->validate([
            'code' => 'required|string|unique:studios,code,' . $studio->id,
            'name' => 'required|string',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $studio->update($data);
        return redirect()->route('voyager.studios.index')->with('success', 'Updated successfully.');
    }

    public function destroy($id)
    {
        $this->checkPermission('delete');
        Studio::findOrFail($id)->delete();
        return redirect()->route('voyager.studios.index')->with('success', 'Deleted successfully.');
    }

    public function show($id)
    {
        $this->checkPermission('read');
        $studio = Studio::with(['sections'])->findOrFail($id);
        return view('admin.studios.show', compact('studio'));
    }
}
