<?php

namespace Database\Seeders\MainModules;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreditsModuleSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // ------------------------------------------------------------------
        // Permissions (coin_packages, client_orders, wallet_transactions)
        // ------------------------------------------------------------------
        foreach (['coin_packages', 'client_orders', 'wallet_transactions'] as $table) {
            foreach (['browse', 'read', 'add', 'edit', 'delete'] as $action) {
                $key = "{$action}_{$table}";
                if (!DB::table('permissions')->where('key', $key)->exists()) {
                    DB::table('permissions')->insert([
                        'key' => $key,
                        'table_name' => $table,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }

        // ------------------------------------------------------------------
        // Parent menu "Credits" (under menu_id = 1)
        // ------------------------------------------------------------------
        $parent = DB::table('menu_items')
            ->whereNull('parent_id')
            ->where('menu_id', 1)
            ->where('title', 'Credits')
            ->first();

        $parentId = $parent?->id ?? DB::table('menu_items')->insertGetId([
            'menu_id' => 1,
            'title' => 'Credits',
            'url' => '',
            'target' => '_self',
            'icon_class' => 'bx bx-wallet',
            'color' => null,
            'parent_id' => null,
            'order' => 6, // adjust ordering if needed
            'route' => null,
            'parameters' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // ------------------------------------------------------------------
        // Children (match our custom route names)
        // ------------------------------------------------------------------
        $ensureItem = function (string $title, string $route, string $icon, int $order) use ($parentId, $now) {
            $exists = DB::table('menu_items')
                ->where('menu_id', 1)
                ->where('parent_id', $parentId)
                ->where('route', $route)
                ->first();

            if (!$exists) {
                DB::table('menu_items')->insert([
                    'menu_id' => 1,
                    'title' => $title,
                    'url' => '',
                    'target' => '_self',
                    'icon_class' => $icon,
                    'color' => null,
                    'parent_id' => $parentId,
                    'order' => $order,
                    'route' => $route,
                    'parameters' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        };

        $ensureItem('Coin Packages', 'voyager.coin-packages.index', 'bx bx-purchase-tag', 1);
        $ensureItem('Client Orders', 'voyager.client-orders.index', 'bx bx-basket', 2);
        $ensureItem('Wallet Transactions', 'voyager.wallet-transactions.index', 'bx bx-transfer', 3);

        // ------------------------------------------------------------------
        // Seed sample Coin Packages (optional but handy)
        // ------------------------------------------------------------------
        $seedPkg = function ($title, $coins, $price, $discount, $final, $sort) use ($now) {
            if (!DB::table('coin_packages')->where('title', $title)->exists()) {
                DB::table('coin_packages')->insert([
                    'title' => $title,
                    'coins' => $coins,
                    'price' => $price,
                    'discount_percent' => $discount,
                    'final_price' => $final, // will be recomputed by observer on edit; OK to seed
                    'currency' => 'AED',
                    'is_active' => 1,
                    'sort_order' => $sort,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        };

        $seedPkg('Starter 10', 10, 9.90, 0, 9.90, 1);
        $seedPkg('Value 25', 25, 24.90, 10, 22.41, 2);
        $seedPkg('Pro 60', 60, 54.90, 20, 43.92, 3);
    }
}
