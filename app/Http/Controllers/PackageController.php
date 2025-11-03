<?php

namespace App\Http\Controllers;

use App\Models\CoinPackage;
use App\Models\ClientOrder;
use App\Services\WalletService;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $packages = CoinPackage::query()->where('is_active', 1)->orderBy('sort_order')->get();
        return view('client.packages.index', compact('packages'));
    }

    public function buy(Request $request, CoinPackage $package, WalletService $wallet)
    {
        $client = auth('client')->user();

        // treat as paid (mock)
        $order = ClientOrder::create([
            'client_id' => $client->id,
            'coin_package_id' => $package->id,
            'currency' => $package->currency,
            'price' => $package->price,
            'discount_percent' => $package->discount_percent,
            'final_price' => $package->final_price,
            'status' => 'paid',
            'payment_ref' => 'MOCK-' . now()->format('YmdHis'),
        ]);

        $wallet->adjust($client, +$package->coins, 'package_purchase', [
            'order_id' => $order->id,
            'package_id' => $package->id,
            'title' => $package->title,
        ]);

        return redirect()->route('client.packages.index')->with('success', 'Coins added to your balance.');
    }
}
