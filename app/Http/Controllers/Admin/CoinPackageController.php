<?php

namespace App\Http\Controllers\Admin;

use App\Models\CoinPackage;
use Illuminate\Http\Request;

class CoinPackageController extends MainController
{
    public function __construct()
    {
        $this->moduleName = 'coin_packages'; // Voyager slug
        $this->model = CoinPackage::class;
    }

    public function index(Request $request)
    {
        $this->checkPermission('browse');

        $q = CoinPackage::query();

        // Filters
        if ($search = $request->get('q')) {
            $q->where(function ($w) use ($search) {
                $w->where('title', 'like', "%{$search}%")
                    ->orWhere('currency', 'like', "%{$search}%");
            });
        }

        if (!is_null($request->get('active'))) {
            $active = (int)$request->get('active');
            $q->where('is_active', $active ? 1 : 0);
        }

        if ($currency = $request->get('currency')) {
            $q->where('currency', $currency);
        }

        $packages = $q->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.coin-packages.index', compact('packages'));
    }

    public function create()
    {
        $this->checkPermission('add');
        return view('admin.coin-packages.create');
    }

    public function store(Request $request)
    {
        $this->checkPermission('add');

        $data = $request->validate([
            'title' => 'required|string|max:190',
            'coins' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'discount_percent' => 'nullable|integer|min:0|max:100',
            'currency' => 'nullable|string|max:8',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // Observer computes final_price & defaults
        CoinPackage::create($data);

        return redirect()
            ->route('voyager.coin-packages.index')
            ->with('success', 'Package created successfully.');
    }

    public function edit($id)
    {
        $this->checkPermission('edit');
        $package = CoinPackage::findOrFail($id);
        return view('admin.coin-packages.edit', compact('package'));
    }

    public function update(Request $request, $id)
    {
        $this->checkPermission('edit');
        $package = CoinPackage::findOrFail($id);

        $data = $request->validate([
            'title' => 'required|string|max:190',
            'coins' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'discount_percent' => 'nullable|integer|min:0|max:100',
            'currency' => 'nullable|string|max:8',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $package->update($data);

        return redirect()
            ->route('voyager.coin-packages.index')
            ->with('success', 'Package updated successfully.');
    }

    public function destroy($id)
    {
        $this->checkPermission('delete');
        CoinPackage::findOrFail($id)->delete();

        return redirect()
            ->route('voyager.coin-packages.index')
            ->with('success', 'Package deleted successfully.');
    }

    public function show($id)
    {
        $this->checkPermission('read');
        $package = CoinPackage::findOrFail($id);

        // If you want to show related orders for this package in the show page, you can eager-load later.
        return view('admin.coin-packages.show', compact('package'));
    }
}
