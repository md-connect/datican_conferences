<?php

namespace Database\Seeders;

use App\Models\ReviewCriterion;
use Illuminate\Database\Seeder;

class ReviewCriterionSeeder extends Seeder
{
    public function run()
    {
        $criteria = [
            [
                'name' => 'Novelty and Originality',
                'description' => 'How novel and original is the contribution?',
                'weight' => 25, // 25%
                'min_score' => 1,
                'max_score' => 5,
                'is_active' => true,
                'options' => [
                    'labels' => [
                        1 => 'Not novel',
                        2 => 'Slightly novel',
                        3 => 'Somewhat novel',
                        4 => 'Quite novel',
                        5 => 'Highly novel'
                    ]
                ]
            ],
            [
                'name' => 'Technical Quality',
                'description' => 'Soundness of methodology and technical execution',
                'weight' => 20,
                'min_score' => 1,
                'max_score' => 5,
                'is_active' => true,
                'options' => [
                    'labels' => [
                        1 => 'Poor',
                        2 => 'Below average',
                        3 => 'Adequate',
                        4 => 'Good',
                        5 => 'Excellent'
                    ]
                ]
            ],
            [
                'name' => 'Empirical Evaluation',
                'description' => 'Quality and thoroughness of evaluation',
                'weight' => 20,
                'min_score' => 1,
                'max_score' => 5,
                'is_active' => true,
                'options' => [
                    'labels' => [
                        1 => 'Inadequate',
                        2 => 'Limited',
                        3 => 'Adequate',
                        4 => 'Good',
                        5 => 'Excellent'
                    ]
                ]
            ],
            [
                'name' => 'Clarity and Presentation',
                'description' => 'Quality of writing, organization, and figures',
                'weight' => 15,
                'min_score' => 1,
                'max_score' => 5,
                'is_active' => true,
                'options' => [
                    'labels' => [
                        1 => 'Confusing',
                        2 => 'Somewhat clear',
                        3 => 'Clear',
                        4 => 'Very clear',
                        5 => 'Exceptionally clear'
                    ]
                ]
            ],
            [
                'name' => 'Relevance to Conference',
                'description' => 'How relevant is this work to the conference themes?',
                'weight' => 10,
                'min_score' => 1,
                'max_score' => 5,
                'is_active' => true,
                'options' => [
                    'labels' => [
                        1 => 'Irrelevant',
                        2 => 'Somewhat relevant',
                        3 => 'Relevant',
                        4 => 'Highly relevant',
                        5 => 'Perfect fit'
                    ]
                ]
            ],
            [
                'name' => 'Significance of Contribution',
                'description' => 'Potential impact on the field',
                'weight' => 10,
                'min_score' => 1,
                'max_score' => 5,
                'is_active' => true,
                'options' => [
                    'labels' => [
                        1 => 'Minimal',
                        2 => 'Limited',
                        3 => 'Moderate',
                        4 => 'Significant',
                        5 => 'Transformative'
                    ]
                ]
            ],
        ];

        foreach ($criteria as $criterion) {
            ReviewCriterion::create($criterion);
        }
    }
}