<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Review;
use App\Models\ReviewAssignment;

return new class extends Migration
{
    public function up()
    {
        Review::chunk(100, function ($reviews) {
            foreach ($reviews as $review) {
                ReviewAssignment::updateOrCreate(
                    [
                        'paper_id' => $review->paper_id,
                        'reviewer_id' => $review->reviewer_id,
                    ],
                    [
                        'status' => $review->status,
                        'assigned_at' => $review->assigned_at,
                        'deadline' => $review->due_date,

                        'overall_score' => $review->overall_score,
                        'scores' => $review->scores,
                        'comments_author' => $review->comments_author,
                        'comments_chair' => $review->comments_chair,
                        'confidence' => $review->confidence,
                        'summary' => $review->summary,
                        'strengths' => $review->strengths,
                        'weaknesses' => $review->weaknesses,
                        'suggestions' => $review->suggestions,
                        'recommendation' => $review->recommendation,
                        'started_at' => $review->started_at,
                        'submitted_at' => $review->submitted_at,
                        'due_date' => $review->due_date,
                        'is_anonymous' => $review->is_anonymous,
                    ]
                );
            }
        });
    }

    public function down() {}
};
