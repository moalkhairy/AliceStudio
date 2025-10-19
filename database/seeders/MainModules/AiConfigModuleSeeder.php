<?php

namespace Database\Seeders\MainModules;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AiConfigModuleSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // Permissions
        foreach (['studios', 'groups', 'sections', 'choices'] as $table) {
            foreach (['browse', 'read', 'add', 'edit', 'delete'] as $action) {
                $key = "{$action}_{$table}";
                $exists = DB::table('permissions')->where('key', $key)->first();
                if (!$exists) {
                    DB::table('permissions')->insert([
                        'key' => $key, 'table_name' => $table, 'created_at' => $now, 'updated_at' => $now
                    ]);
                }
            }
        }

        foreach (['steps'] as $table) {
            foreach (['browse','read','add','edit','delete'] as $action) {
                $key = "{$action}_{$table}";
                if (!DB::table('permissions')->where('key',$key)->exists()) {
                    DB::table('permissions')->insert([
                        'key'=>$key,'table_name'=>$table,'created_at'=>now(),'updated_at'=>now()
                    ]);
                }
            }
        }

        // Parent menu "AI Config"
        $parent = DB::table('menu_items')->whereNull('parent_id')->where('title', 'AI Config')->first();
        $parentId = $parent?->id ?? DB::table('menu_items')->insertGetId([
            'menu_id' => 1, 'title' => 'AI Config', 'url' => '', 'target' => '_self', 'icon_class' => 'bx bx-bot',
            'color' => null, 'parent_id' => null, 'order' => 5, 'route' => null, 'parameters' => null,
            'created_at' => $now, 'updated_at' => $now
        ]);

        // Children
        $ensureItem = function ($title, $route, $icon, $order) use ($parentId, $now) {
            $exists = DB::table('menu_items')->where('title', $title)->where('route', $route)->first();
            if (!$exists) {
                DB::table('menu_items')->insert([
                    'menu_id' => 1, 'title' => $title, 'url' => '', 'target' => '_self', 'icon_class' => $icon,
                    'color' => null, 'parent_id' => $parentId, 'order' => $order, 'route' => $route, 'parameters' => null,
                    'created_at' => $now, 'updated_at' => $now
                ]);
            }
        };

        $ensureItem('Studios', 'voyager.studios.index', 'bx bx-building', 1);
        $ensureItem('Groups', 'voyager.groups.index', 'bx bx-category', 2);
        $ensureItem('Sections', 'voyager.sections.index', 'bx bx-grid', 3);
        $ensureItem('Choices', 'voyager.choices.index', 'bx bx-list-check', 4);
        $ensureItem('Steps', 'voyager.steps.index', 'bx bx-navigation', 5);

        // Minimal studio
        if (!DB::table('studios')->where('code', 'model')->exists()) {
            DB::table('studios')->insert([
                'code' => 'model', 'name' => 'Model Studio', 'description' => 'Human model image generation.',
                'order' => 1, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now
            ]);
        }
    }
}
