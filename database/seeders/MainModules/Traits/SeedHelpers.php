<?php

namespace Database\Seeders\MainModules\Traits;

use Illuminate\Support\Facades\DB;

trait SeedHelpers
{
    protected function ensurePermission($table, $key, $label)
    {
        $exists = DB::table('permissions')->where('key', $key)->first();
        if (!$exists) {
            DB::table('permissions')->insert([
                'key' => $key,
                'table_name' => $table,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    protected function ensureMenuItem($parentId, $title, $route, $icon = 'bx bx-right-arrow', $order = 1)
    {
        $exists = DB::table('menu_items')->where('title',$title)->where('route',$route)->first();
        if ($exists) return $exists->id;

        return DB::table('menu_items')->insertGetId([
            'menu_id' => 1, // main admin menu (adjust if needed)
            'title' => $title,
            'url' => '',
            'target' => '_self',
            'icon_class' => $icon,
            'color' => null,
            'parent_id' => $parentId,
            'order' => $order,
            'route' => $route,
            'parameters' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function ensureParentMenu($title, $icon = 'bx bx-cog', $order = 20)
    {
        $existing = DB::table('menu_items')->where('title',$title)->whereNull('parent_id')->first();
        if ($existing) return $existing->id;

        return DB::table('menu_items')->insertGetId([
            'menu_id' => 1,
            'title' => $title,
            'url' => '',
            'target' => '_self',
            'icon_class' => $icon,
            'color' => null,
            'parent_id' => null,
            'order' => $order,
            'route' => null,
            'parameters' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
