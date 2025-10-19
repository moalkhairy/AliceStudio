<?php
// app/Services/WizardService.php
namespace App\Services;

class WizardService
{
    public function build(): array
    {
        // TODO: Replace with your real DB reads from admin tables.
        // Keep keys stable (we will POST by these keys).
        return [
            'steps' => [
                [
                    'key' => 'step_1',
                    'title' => 'Describe Your Image',
                    'subtitle' => 'What do you want to create?',
                    'tip' => 'Be specific: subject, style, lighting, background.',
                    'groups' => [
                        [
                            'key' => 'g_uploads',
                            'exclusive_between_sections' => false,
                            'sections' => [
                                // This slot is reserved for the image upload UI (handled on the front).
                                // Keep for ordering metadata if you want.
                            ],
                        ],
                        [
                            'key' => 'g_prompt',
                            'exclusive_between_sections' => false,
                            'sections' => [
                                ['key' => 'main_prompt', 'label' => 'Main Prompt', 'type' => 'textarea', 'gender' => 'both'],
                                ['key' => 'negative_prompt', 'label' => 'Negative Prompt', 'type' => 'text', 'gender' => 'both'],
                                ['key' => 'num_images', 'label' => 'Number of Images', 'type' => 'select', 'options' => [
                                    ['key' => '1', 'label' => '1'], ['key' => '2', 'label' => '2'], ['key' => '4', 'label' => '4']
                                ], 'gender' => 'both', 'selection' => 'single'],
                                ['key' => 'quick_tags', 'label' => 'Quick Tags', 'type' => 'chips', 'options' => [
                                    ['key' => 'high_detail', 'label' => 'High Detail'],
                                    ['key' => 'professional', 'label' => 'Professional'],
                                    ['key' => 'studio_quality', 'label' => 'Studio Quality'],
                                    ['key' => 'clean_bg', 'label' => 'Clean Background'],
                                ], 'gender' => 'both', 'selection' => 'multiple'],
                            ],
                        ],
                    ],
                ],
                [
                    'key' => 'step_2',
                    'title' => 'Choose Your Style',
                    'subtitle' => 'Pick the visual style',
                    'tip' => 'Photoreal works best for products.',
                    'groups' => [
                        [
                            'key' => 'g_style',
                            'exclusive_between_sections' => true, // only one section in this group (example)
                            'sections' => [
                                ['key' => 'visual_style', 'label' => 'Visual Style', 'type' => 'chips', 'selection' => 'single', 'gender' => 'both', 'options' => [
                                    ['key' => 'photoreal', 'label' => 'Photoreal', 'icon' => '📸'],
                                    ['key' => 'cinematic', 'label' => 'Cinematic', 'icon' => '🎬'],
                                    ['key' => 'illustration', 'label' => 'Illustration', 'icon' => '🎨'],
                                    ['key' => 'product', 'label' => 'Product', 'icon' => '📦'],
                                    ['key' => 'anime', 'label' => 'Anime', 'icon' => '🌸'],
                                    ['key' => 'cyberpunk', 'label' => 'Cyberpunk', 'icon' => '🎭'],
                                ]],
                                ['key' => 'color_palette', 'label' => 'Color Palette', 'type' => 'chips', 'selection' => 'single', 'gender' => 'both', 'options' => [
                                    ['key' => 'neutral', 'label' => 'Neutral'],
                                    ['key' => 'vibrant', 'label' => 'Vibrant'],
                                    ['key' => 'pastel', 'label' => 'Pastel'],
                                    ['key' => 'mono', 'label' => 'Monochrome'],
                                ]],
                            ],
                        ],
                    ],
                ],
                // … Add your remaining steps mapped from admin …
                [
                    'key' => 'step_6',
                    'title' => 'Generate & Export',
                    'subtitle' => 'Final settings and generate',
                    'tip' => 'Review your settings. You can go back to edit.',
                    'groups' => [
                        [
                            'key' => 'g_export',
                            'exclusive_between_sections' => false,
                            'sections' => [
                                ['key' => 'aspect_ratio', 'label' => 'Aspect Ratio', 'type' => 'select', 'selection' => 'single', 'gender' => 'both', 'options' => [
                                    ['key' => '1:1', 'label' => '1:1 Square'],
                                    ['key' => '16:9', 'label' => '16:9 Wide'],
                                    ['key' => '9:16', 'label' => '9:16 Portrait'],
                                    ['key' => '4:3', 'label' => '4:3 Classic'],
                                ]],
                                ['key' => 'resolution', 'label' => 'Resolution', 'type' => 'select', 'selection' => 'single', 'gender' => 'both', 'options' => [
                                    ['key' => '512', 'label' => '512px'],
                                    ['key' => '1024', 'label' => '1024px'],
                                    ['key' => '1536', 'label' => '1536px'],
                                    ['key' => '2048', 'label' => '2048px'],
                                ]],
                                ['key' => 'remove_bg', 'label' => 'Remove Background', 'type' => 'boolean', 'gender' => 'both'],
                                ['key' => 'upscale_x2', 'label' => 'Upscale ×2', 'type' => 'boolean', 'gender' => 'both'],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
