<?php

namespace Database\Seeders\MainModules;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StepsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $id = fn($t, $w) => DB::table($t)->where($w)->value('id');

        $ensureStep = function (int $studioId, array $s) use ($now, $id) {
            $sid = $id('steps', ['studio_id' => $studioId, 'code' => $s['code']]);
            $data = array_merge([
                'studio_id' => $studioId, 'subtitle' => null, 'icon' => null, 'tip_text' => null,
                'order' => 0, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now
            ], $s);
            if (!$sid) $sid = DB::table('steps')->insertGetId($data);
            else DB::table('steps')->where('id', $sid)->update(array_diff_key($data, array_flip(['studio_id', 'code'])) + ['updated_at' => $now]);
            return $sid;
        };

        $link = function (int $studioId, int $stepId, array $sectionCodes) {
            $max = DB::table('step_sections')->where('step_id', $stepId)->max('order') ?? 0;
            foreach ($sectionCodes as $code) {
                $secId = DB::table('sections')->where('studio_id', $studioId)->where('code', $code)->value('id');
                if (!$secId) continue;

                // strict: section cannot appear in another step in this studio
                $existsAny = DB::table('step_sections')->where('studio_id', $studioId)->where('section_id', $secId)->exists();
                if ($existsAny) continue;

                DB::table('step_sections')->insert([
                    'studio_id' => $studioId,
                    'step_id' => $stepId,
                    'section_id' => $secId,
                    'order' => ++$max,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        };

        // Studio
        $studioId = $id('studios', ['code' => 'model']);
        if (!$studioId) return;

        // Steps (titles match your wizard)
        $steps = [
            'prompt' => ['title' => 'Describe Your Image', 'subtitle' => 'What do you want to create?', 'icon' => '🖊️', 'tip_text' => 'Be specific about subject, style, lighting, and background.', 'order' => 1],
            'style' => ['title' => 'Choose Your Style', 'subtitle' => 'Pick the visual style', 'icon' => '🎨', 'tip_text' => 'Photoreal for products; illustration for creative use.', 'order' => 2],
            'camera' => ['title' => 'Camera & Composition', 'subtitle' => 'Angle and framing', 'icon' => '📷', 'tip_text' => 'Eye-level or 45° works best for products.', 'order' => 3],
            'lighting' => ['title' => 'Lighting Setup', 'subtitle' => 'Set up your lighting', 'icon' => '💡', 'tip_text' => 'Soft studio lighting fits most scenarios.', 'order' => 4],
            'effects' => ['title' => 'Effects & Polish', 'subtitle' => 'Finishing touches', 'icon' => '✨', 'tip_text' => 'Subtle shadows look most natural.', 'order' => 5],
            'generate' => ['title' => 'Generate & Export', 'subtitle' => 'Final settings', 'icon' => '⚙️', 'tip_text' => 'Review settings before generating.', 'order' => 6],
        ];

        $stepIds = [];
        foreach ($steps as $code => $meta) {
            $stepIds[$code] = $ensureStep($studioId, array_merge(['code' => $code], $meta));
        }

        // Map sections to steps using the codes we seeded earlier
        // (If any section code is missing in your DB, it will be skipped harmlessly.)

        // 1) Prompt
        $link($studioId, $stepIds['prompt'], [
            'model_description', 'negative_prompt', 'scene_tags'
        ]);

        // 2) Style
        $styleList = [
            'visual_style', 'color_palette',
            // female style buckets
            'ms_f_elegant_formal', 'ms_f_professional', 'ms_f_casual_modern', 'ms_f_alt_edgy', 'ms_f_artistic',
            // male style buckets
            'ms_m_classic_refined', 'ms_m_professional', 'ms_m_modern_urban', 'ms_m_rugged_alt',
        ];
        $link($studioId, $stepIds['style'], $styleList);

        // 3) Camera
        $link($studioId, $stepIds['camera'], [
            'camera_angle', 'lens', 'aperture', 'composition'
        ]);

        // 4) Lighting
        $link($studioId, $stepIds['lighting'], [
            'lighting_style', 'lighting_effects'
        ]);

        // 5) Effects
        $link($studioId, $stepIds['effects'], [
            'background_type', 'shadow_type'
        ]);

        // 6) Generate
        $link($studioId, $stepIds['generate'], [
            'aspect_ratio', 'resolution', 'output_options'
        ]);
    }
}
