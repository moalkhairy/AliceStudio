<?php

namespace Database\Seeders\MainModules;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModelStudioContentSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $id = fn($t, $w) => DB::table($t)->where($w)->value('id');

        $ensureStudio = function (string $code, string $name) use ($now, $id) {
            $sid = $id('studios', ['code' => $code]);
            if (!$sid) $sid = DB::table('studios')->insertGetId([
                'code' => $code, 'name' => $name, 'description' => null, 'order' => 1, 'is_active' => true,
                'created_at' => $now, 'updated_at' => $now
            ]);
            return $sid;
        };

        $ensureGroup = function (int $studioId, array $g) use ($now, $id) {
            $gid = $id('groups', ['code' => $g['code']]);
            $data = array_merge([
                'studio_id' => $studioId, 'description' => null, 'exclusive_sections' => false,
                'order' => 0, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now
            ], $g);
            if (!$gid) $gid = DB::table('groups')->insertGetId($data);
            else DB::table('groups')->where('id', $gid)->update(array_diff_key($data, array_flip(['studio_id', 'code'])) + ['updated_at' => $now]);
            return $gid;
        };

        $ensureSection = function (int $studioId, array $s) use ($now, $id) {
            $sid = $id('sections', ['studio_id' => $studioId, 'code' => $s['code']]);
            $data = array_merge([
                'studio_id' => $studioId, 'group_id' => null, 'help_text' => null, 'min_select' => null, 'max_select' => null,
                'is_required' => false, 'is_general' => false, 'gender_scope' => 'both', 'order' => 0, 'is_active' => true,
                'created_at' => $now, 'updated_at' => $now
            ], $s);
            if (!$sid) $sid = DB::table('sections')->insertGetId($data);
            else DB::table('sections')->where('id', $sid)->update(array_diff_key($data, array_flip(['studio_id', 'code'])) + ['updated_at' => $now]);
            return $sid;
        };

        $ensureChoice = function (int $sectionId, array $c, int $order) use ($now) {
            $slug = $c['slug'] ?? str($c['label'])->slug('_');
            $cid = DB::table('choices')->where('section_id', $sectionId)->where('slug', $slug)->value('id');
            $row = [
                'section_id' => $sectionId, 'label' => $c['label'], 'slug' => $slug,
                'token' => $c['token'] ?? $c['label'], 'negative_token' => $c['negative_token'] ?? null,
                'weight' => $c['weight'] ?? null, 'icon_url' => $c['icon_url'] ?? null,
                'gender_scope' => $c['gender_scope'] ?? 'both',
                'is_default' => $c['is_default'] ?? false, 'order' => $order, 'is_active' => $c['is_active'] ?? true,
                'created_at' => $now, 'updated_at' => $now
            ];
            if (!$cid) DB::table('choices')->insert($row);
            else DB::table('choices')->where('id', $cid)->update($row);
        };

        // Studio
        $studioId = $ensureStudio('model', 'Model Studio');

        // GROUP: Model Style (exclusive)
        $modelStyleGroupId = $ensureGroup($studioId, [
            'code' => 'model_style', 'name' => 'Model Style',
            'description' => 'Choose from one style section (exclusive).',
            'exclusive_sections' => true, 'order' => 10
        ]);

        // STANDALONE SECTIONS (no group) — both unless specified
        $sections = [];
        $sections['model_description'] = $ensureSection($studioId, [
            'code' => 'model_description', 'name' => 'Model Description', 'input_type' => 'textarea', 'selection_mode' => 'single',
            'gender_scope' => 'both', 'order' => 1
        ]);
        $sections['appearance'] = $ensureSection($studioId, [
            'code' => 'appearance', 'name' => 'Appearance', 'input_type' => 'chips', 'selection_mode' => 'multiple',
            'gender_scope' => 'both', 'order' => 2
        ]);
        $sections['facial_hair'] = $ensureSection($studioId, [
            'code' => 'facial_hair', 'name' => 'Facial Hair', 'input_type' => 'chips', 'selection_mode' => 'multiple',
            'gender_scope' => 'male', 'order' => 3
        ]);
        $sections['age'] = $ensureSection($studioId, [
            'code' => 'age', 'name' => 'Age', 'input_type' => 'chips', 'selection_mode' => 'multiple',
            'gender_scope' => 'both', 'order' => 4
        ]);
        $sections['ethnicity'] = $ensureSection($studioId, [
            'code' => 'ethnicity', 'name' => 'Ethnicity', 'input_type' => 'chips', 'selection_mode' => 'multiple',
            'gender_scope' => 'both', 'order' => 5
        ]);
        $sections['hair_color'] = $ensureSection($studioId, [
            'code' => 'hair_color', 'name' => 'Hair Color', 'input_type' => 'chips', 'selection_mode' => 'multiple',
            'gender_scope' => 'both', 'order' => 6
        ]);
        $sections['hair_style'] = $ensureSection($studioId, [
            'code' => 'hair_style', 'name' => 'Hair Style', 'input_type' => 'chips', 'selection_mode' => 'multiple',
            'gender_scope' => 'both', 'order' => 7
        ]);
        $sections['pose_gesture'] = $ensureSection($studioId, [
            'code' => 'pose_gesture', 'name' => 'Pose & Gesture', 'input_type' => 'chips', 'selection_mode' => 'multiple',
            'gender_scope' => 'both', 'order' => 8
        ]);
        $sections['scene_description'] = $ensureSection($studioId, [
            'code' => 'scene_description', 'name' => 'Scene Description', 'input_type' => 'textarea', 'selection_mode' => 'single',
            'gender_scope' => 'both', 'order' => 9
        ]);
        $sections['scene_tags'] = $ensureSection($studioId, [
            'code' => 'scene_tags', 'name' => 'Scene Tags', 'input_type' => 'chips', 'selection_mode' => 'multiple',
            'gender_scope' => 'both', 'order' => 10
        ]);
        // General
        $sections['lighting_style'] = $ensureSection($studioId, [
            'code' => 'lighting_style', 'name' => 'Lighting Style', 'input_type' => 'chips', 'selection_mode' => 'single',
            'is_general' => true, 'gender_scope' => 'both', 'order' => 11
        ]);
        $sections['accessory_proportion'] = $ensureSection($studioId, [
            'code' => 'accessory_proportion', 'name' => 'Accessory Proportion', 'input_type' => 'chips', 'selection_mode' => 'single',
            'is_general' => true, 'gender_scope' => 'both', 'order' => 12
        ]);
        $sections['aspect_ratio'] = $ensureSection($studioId, [
            'code' => 'aspect_ratio', 'name' => 'Aspect Ratio', 'input_type' => 'chips', 'selection_mode' => 'single',
            'is_general' => true, 'gender_scope' => 'both', 'order' => 13
        ]);
        $sections['accessory_type'] = $ensureSection($studioId, [
            'code' => 'accessory_type', 'name' => 'Accessory Type', 'input_type' => 'chips', 'selection_mode' => 'multiple',
            'is_general' => true, 'gender_scope' => 'both', 'order' => 14
        ]);
        $sections['accessory_image'] = $ensureSection($studioId, [
            'code' => 'accessory_image', 'name' => 'Accessory Image', 'input_type' => 'upload', 'selection_mode' => 'single',
            'is_general' => true, 'gender_scope' => 'both', 'order' => 15
        ]);
        $sections['accessory_type_text'] = $ensureSection($studioId, [
            'code' => 'accessory_type_text', 'name' => 'Accessory Type (Text)', 'input_type' => 'text', 'selection_mode' => 'single',
            'is_general' => true, 'gender_scope' => 'both', 'order' => 16
        ]);

        // MODEL STYLE — sections inside group (exclusive)
        // Female
        $female = [
            'ms_f_elegant_formal' => ['name' => 'Elegant & Formal', 'choices' => ['Glamour', 'Evening Wear', 'Haute Couture', 'Romantic']],
            'ms_f_professional' => ['name' => 'Professional', 'choices' => ['Corporate Chic', 'Business Professional']],
            'ms_f_casual_modern' => ['name' => 'Casual & Modern', 'choices' => ['Effortless Chic', 'Streetwear', 'Sporty', 'Beach Vibe']],
            'ms_f_alt_edgy' => ['name' => 'Alternative & Edgy', 'choices' => ['Grunge', 'Punk', 'Gothic', 'Western']],
            'ms_f_artistic' => ['name' => 'Artistic & Aesthetic', 'choices' => ['Bohemian', 'Vintage', 'Ethereal', 'Cinematic', 'Minimalist', 'Dark Academia']],
        ];
        foreach ($female as $code => $meta) {
            $sid = $ensureSection($studioId, [
                'code' => $code, 'name' => $meta['name'], 'group_id' => $modelStyleGroupId,
                'input_type' => 'chips', 'selection_mode' => 'multiple', 'gender_scope' => 'female', 'order' => 200
            ]);
            $i = 1;
            foreach ($meta['choices'] as $label) $ensureChoice($sid, ['label' => $label], $i++);
        }

        // Male
        $male = [
            'ms_m_classic_refined' => ['name' => 'Classic & Refined', 'choices' => ['Dapper', 'Smart Casual', 'Preppy']],
            'ms_m_professional' => ['name' => 'Professional (Men)', 'choices' => ['Business Professional']],
            'ms_m_modern_urban' => ['name' => 'Modern & Urban', 'choices' => ['Streetwear', 'Minimalist', 'Techwear']],
            'ms_m_rugged_alt' => ['name' => 'Rugged & Alternative', 'choices' => ['Rugged', 'Punk', 'Skater', 'Western', 'Bohemian']],
        ];
        foreach ($male as $code => $meta) {
            $sid = $ensureSection($studioId, [
                'code' => $code, 'name' => $meta['name'], 'group_id' => $modelStyleGroupId,
                'input_type' => 'chips', 'selection_mode' => 'multiple', 'gender_scope' => 'male', 'order' => 210
            ]);
            $i = 1;
            foreach ($meta['choices'] as $label) $ensureChoice($sid, ['label' => $label], $i++);
        }

        // CHOICES for standalone sections
        $i = 1;
        foreach (['with glasses', 'with tattoos', 'athletic'] as $l) $ensureChoice($sections['appearance'], ['label' => $l], $i++);
        $i = 1;
        foreach (['beard', 'mustache'] as $l) $ensureChoice($sections['facial_hair'], ['label' => $l], $i++);

        $i = 1;
        foreach (['teenage', 'young adult', 'middle-aged', 'senior'] as $l) $ensureChoice($sections['age'], ['label' => $l], $i++);
        $i = 1;
        foreach (['East Asian', 'South Asian', 'Black', 'Caucasian', 'Hispanic', 'Middle Eastern', 'Indigenous'] as $l) $ensureChoice($sections['ethnicity'], ['label' => $l], $i++);
        $i = 1;
        foreach (['blonde hair', 'brown hair', 'black hair', 'red hair'] as $l) $ensureChoice($sections['hair_color'], ['label' => $l], $i++);
        $i = 1;
        foreach (['long hair', 'short hair', 'curly hair', 'straight hair'] as $l) $ensureChoice($sections['hair_style'], ['label' => $l], $i++);

        $pose = [
            'Confident', 'Happy', 'Cheerful', 'Elegant', 'Thoughtful', 'Powerful', 'Playful', 'Serene',
            'Leaning Casually', 'Hands in Pockets', 'Adjusting Cufflinks', 'Thoughtful Gaze', 'Relaxed Smile', 'Powerful Stance', 'Dynamic Motion',
        ];
        $pose = array_values(array_unique($pose));
        $i = 1;
        foreach ($pose as $l) $ensureChoice($sections['pose_gesture'], ['label' => $l], $i++);

        $i = 1;
        foreach (['urban', 'nature', 'studio', 'indoor', 'outdoor', 'night', 'day'] as $l) $ensureChoice($sections['scene_tags'], ['label' => $l], $i++);

        $i = 1;
        foreach ([['Automatic', true], ['Soft Natural', false], ['Dramatic Studio', false], ['Golden Hour', false], ['Cinematic', false], ['Backlit', false], ['Rim Lighting', false]] as [$l, $d])
            $ensureChoice($sections['lighting_style'], ['label' => $l, 'is_default' => $d], $i++);

        $i = 1;
        foreach ([['Delicate / Small', true], ['Natural Size', false], ['Prominent / Large', false]] as [$l, $d])
            $ensureChoice($sections['accessory_proportion'], ['label' => $l, 'is_default' => $d], $i++);

        $i = 1;
        foreach ([['1:1', '1_1', true], ['16:9', '16_9', false], ['9:16', '9_16', false]] as [$l, $slug, $d])
            $ensureChoice($sections['aspect_ratio'], ['label' => $l, 'slug' => $slug, 'is_default' => $d], $i++);

        $i = 1;
        foreach (['Earrings', 'Bracelet', 'Necklace', 'Rings', 'Anklet', 'Hat', 'Scarf', 'Watch', 'Bag', 'Glasses', 'Jewelry', 'Belt', 'Shoes', 'Socks'] as $l)
            $ensureChoice($sections['accessory_type'], ['label' => $l], $i++);
    }
}
