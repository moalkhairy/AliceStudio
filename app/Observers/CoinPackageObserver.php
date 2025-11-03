<?php


namespace App\Observers;

use App\Models\CoinPackage;

class CoinPackageObserver
{
    public function saving(CoinPackage $package): void
    {
        $discount = (int)($package->discount_percent ?? 0);
        if ($discount < 0) $discount = 0;
        if ($discount > 100) $discount = 100;

        $price = (float)$package->price;
        $final = $price * (100 - $discount) / 100;
        // round to 2 decimals
        $package->final_price = round($final, 2);

        // default currency if empty
        if (!$package->currency) {
            $package->currency = 'AED';
        }
    }
}
