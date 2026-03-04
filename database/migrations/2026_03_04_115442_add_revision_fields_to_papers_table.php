<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add fields to papers table
        Schema::table('papers', function (Blueprint $table) {
            $table->boolean('needs_revision')->default(false)->after('status');
            $table->timestamp('revision_requested_at')->nullable()->after('needs_revision');
            $table->timestamp('revision_submitted_at')->nullable()->after('revision_requested_at');
            $table->text('revision_notes')->nullable()->after('revision_submitted_at');
            $table->boolean('has_revision_recommendations')->default(false)->after('revision_notes');
            $table->integer('revision_recommendation_count')->default(0)->after('has_revision_recommendations');
            $table->integer('version')->default(1)->after('revision_recommendation_count');
            $table->boolean('all_reviews_completed')->default(false)->after('version');
            $table->timestamp('abstract_accepted_at')->nullable()->after('all_reviews_completed');
            $table->date('full_paper_deadline')->nullable()->after('abstract_accepted_at');
        });
        
        // Add fields to review_assignments table
        Schema::table('review_assignments', function (Blueprint $table) {
            $table->text('revision_suggestions')->nullable()->after('suggestions');
            $table->boolean('is_revision_review')->default(false)->after('revision_suggestions');
            $table->unsignedBigInteger('original_review_id')->nullable()->after('is_revision_review');
            $table->integer('paper_version')->nullable()->after('original_review_id');
            
            $table->foreign('original_review_id')
                  ->references('id')
                  ->on('review_assignments')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('papers', function (Blueprint $table) {
            $table->dropColumn([
                'needs_revision',
                'revision_requested_at',
                'revision_submitted_at',
                'revision_notes',
                'has_revision_recommendations',
                'revision_recommendation_count',
                'version',
                'all_reviews_completed',
                'abstract_accepted_at',
                'full_paper_deadline',
            ]);
        });
        
        Schema::table('review_assignments', function (Blueprint $table) {
            $table->dropForeign(['original_review_id']);
            $table->dropColumn([
                'revision_suggestions',
                'is_revision_review',
                'original_review_id',
                'paper_version',
            ]);
        });
    }
};