<?php
// app/Http/Controllers/ClientPackageController.php
namespace App\Http\Controllers;

use App\Models\CoinPackage;
use App\Models\ClientOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClientPackageController extends Controller
{
    public function index()
    {
        $packages = CoinPackage::where('is_active', true)->orderBy('sort_order')->get();
        return view('client.packages.index', compact('packages'));
    }

    // Mocked success payment → immediately credit coins
    public function buy(Request $request, CoinPackage $package)
    {
        $client = Auth::guard('client')->user();

        // Create order as paid
        $order = ClientOrder::create([
            'client_id' => $client->id,
            'coin_package_id' => $package->id,
            'currency' => $package->currency,
            'price' => $package->price,
            'discount_percent' => $package->discount_percent,
            'final_price' => $package->final_price,
            'status' => 'paid',
            'payment_ref' => 'MOCK-' . uniqid(),
        ]);

        // credit coins atomically and log ledger
        DB::transaction(function () use ($client, $package, $order) {
            $client->increment('coins', $package->coins);

            $client->walletTransactions()->create([
                'type' => 'package_purchase',
                'amount' => +$package->coins,
                'balance_after' => $client->coins, // reflects latest
                'meta' => [
                    'order_id' => $order->id,
                    'package_id' => $package->id,
                    'title' => $package->title,
                ],
            ]);
        });

        return redirect()->route('client.dashboard')->with('status', 'Package purchased. Coins added!');
    }
}
