<?php

namespace App\Http\Controllers\Admin;

use App\Models\ClientOrder;
use Illuminate\Http\Request;

class ClientOrderController extends MainController
{
    public function __construct()
    {
        $this->moduleName = 'client_orders'; // Voyager slug
        $this->model = ClientOrder::class;
    }

    public function index(Request $request)
    {
        $this->checkPermission('browse');

        $q = ClientOrder::query()->with(['client', 'package']);

        // Search by client email/name or payment_ref
        if ($search = $request->get('q')) {
            $q->where(function ($w) use ($search) {
                $w->where('payment_ref', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($wc) use ($search) {
                        $wc->where('email', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('package', function ($wp) use ($search) {
                        $wp->where('title', 'like', "%{$search}%");
                    });
            });
        }

        // Status filter (paid/pending/failed)
        if ($status = $request->get('status')) {
            $q->where('status', $status);
        }

        // Date range filters
        if ($from = $request->get('from')) {
            $q->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->get('to')) {
            $q->whereDate('created_at', '<=', $to);
        }

        // Currency filter
        if ($currency = $request->get('currency')) {
            $q->where('currency', $currency);
        }

        // Package filter
        if ($packageId = $request->get('package_id')) {
            $q->where('coin_package_id', (int)$packageId);
        }

        // Client filter
        if ($clientId = $request->get('client_id')) {
            $q->where('client_id', (int)$clientId);
        }

        $orders = $q->orderByDesc('id')->paginate(15)->withQueryString();

        return view('admin.client-orders.index', compact('orders'));
    }

    public function show($id)
    {
        $this->checkPermission('read');

        $order = ClientOrder::with(['client', 'package'])->findOrFail($id);
        return view('admin.client-orders.show', compact('order'));
    }

    /* -------- Read-only: block mutations -------- */

    public function create()
    {
        $this->checkPermission('add');
        return redirect()->route('voyager.client-orders.index')
            ->with('error', 'Orders are read-only in admin.');
    }

    public function store(Request $request)
    {
        $this->checkPermission('add');
        return redirect()->route('voyager.client-orders.index')
            ->with('error', 'Orders are read-only in admin.');
    }

    public function edit($id)
    {
        $this->checkPermission('edit');
        return redirect()->route('voyager.client-orders.index')
            ->with('error', 'Orders are read-only in admin.');
    }

    public function update(Request $request, $id)
    {
        $this->checkPermission('edit');
        return redirect()->route('voyager.client-orders.index')
            ->with('error', 'Orders are read-only in admin.');
    }

    public function destroy($id)
    {
        $this->checkPermission('delete');
        return redirect()->route('voyager.client-orders.index')
            ->with('error', 'Orders are read-only in admin.');
    }
}
