<?php

namespace App\Http\Controllers\Admin;

use App\Models\WalletTransaction;
use Illuminate\Http\Request;

class WalletTransactionController extends MainController
{
    public function __construct()
    {
        $this->moduleName = 'wallet_transactions'; // Voyager slug
        $this->model = WalletTransaction::class;
    }

    public function index(Request $request)
    {
        $this->checkPermission('browse');

        $q = WalletTransaction::query()->with(['client']);

        // Global search: by type, client name/email, id
        if ($search = $request->get('q')) {
            $q->where(function ($w) use ($search) {
                $w->where('type', 'like', "%{$search}%")
                    ->orWhere('id', $search)
                    ->orWhereHas('client', function ($wc) use ($search) {
                        $wc->where('email', 'like', "%{$search}%")
                            ->orWhere('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by specific type (signup_bonus, package_purchase, image_generation, generation_refund, admin_adjustment)
        if ($type = $request->get('type')) {
            $q->where('type', $type);
        }

        // Credit / Debit filter
        if ($sign = $request->get('sign')) {
            if ($sign === 'credit') $q->where('amount', '>', 0);
            if ($sign === 'debit') $q->where('amount', '<', 0);
        }

        // Client filter
        if ($clientId = $request->get('client_id')) {
            $q->where('client_id', (int)$clientId);
        }

        // Date range
        if ($from = $request->get('from')) {
            $q->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->get('to')) {
            $q->whereDate('created_at', '<=', $to);
        }

        $transactions = $q->orderByDesc('id')->paginate(15)->withQueryString();

        return view('admin.wallet-transactions.index', compact('transactions'));
    }

    public function show($id)
    {
        $this->checkPermission('read');

        $transaction = WalletTransaction::with(['client'])->findOrFail($id);
        return view('admin.wallet-transactions.show', compact('transaction'));
    }

    /* -------- Read-only: block mutations -------- */

    public function create()
    {
        $this->checkPermission('add');
        return redirect()->route('voyager.wallet-transactions.index')
            ->with('error', 'Wallet transactions are read-only in admin.');
    }

    public function store(Request $request)
    {
        $this->checkPermission('add');
        return redirect()->route('voyager.wallet-transactions.index')
            ->with('error', 'Wallet transactions are read-only in admin.');
    }

    public function edit($id)
    {
        $this->checkPermission('edit');
        return redirect()->route('voyager.wallet-transactions.index')
            ->with('error', 'Wallet transactions are read-only in admin.');
    }

    public function update(Request $request, $id)
    {
        $this->checkPermission('edit');
        return redirect()->route('voyager.wallet-transactions.index')
            ->with('error', 'Wallet transactions are read-only in admin.');
    }

    public function destroy($id)
    {
        $this->checkPermission('delete');
        return redirect()->route('voyager.wallet-transactions.index')
            ->with('error', 'Wallet transactions are read-only in admin.');
    }
}
