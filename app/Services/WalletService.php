<?php

namespace App\Services;

use App\Models\Client;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class WalletService
{
    /**
     * Adjust coins for a client and record a ledger entry.
     * $delta: +N credit, -N debit
     */
    public function adjust(Client $client, int $delta, string $type, ?array $meta = []): WalletTransaction
    {
        if ($delta === 0) {
            throw new InvalidArgumentException('Delta must be non-zero.');
        }
        return DB::transaction(function () use ($client, $delta, $type, $meta) {
            // lock row for update
            $fresh = Client::query()->where('id', $client->id)->lockForUpdate()->first();
            $newBalance = max(0, ($fresh->coins ?? 0) + $delta); // forbid negative wallet

            $fresh->coins = $newBalance;
            $fresh->save();

            return WalletTransaction::create([
                'client_id' => $fresh->id,
                'type' => $type,
                'amount' => $delta,
                'balance_after' => $newBalance,
                'meta' => $meta,
            ]);
        });
    }
}
