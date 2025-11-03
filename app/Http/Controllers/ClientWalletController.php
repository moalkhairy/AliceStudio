<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class ClientWalletController extends Controller
{
    public function index()
    {
        $client = Auth::guard('client')->user();
        $tx = $client->walletTransactions()->latest()->paginate(15);
        return view('client.wallet.index', compact('tx'));
    }
}
