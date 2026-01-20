<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Review assignments table
        if (!Schema::hasTable('review_assignments')) {
            Schema::create('review_assignments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('paper_id')->constrained()->onDelete('cascade');
                $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('assigned_by')->constrained('users');
                $table->enum('status', ['pending', 'accepted', 'declined', 'completed'])->default('pending');
                $table->dateTime('assigned_at')->nullable();
                $table->dateTime('deadline')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
                
                $table->unique(['paper_id', 'reviewer_id']);
                $table->index(['reviewer_id', 'status']);
            });
        }

        // Bids table
        if (!Schema::hasTable('bids')) {
            Schema::create('bids', function (Blueprint $table) {
                $table->id();
                $table->foreignId('paper_id')->constrained()->onDelete('cascade');
                $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade');
                $table->enum('preference', [
                    'very_high',
                    'high',
                    'medium',
                    'low',
                    'very_low',
                    'conflict',
                    'no_bid'
                ])->default('no_bid');
                $table->text('comments')->nullable();
                $table->json('expertise_scores')->nullable();
                $table->timestamps();
                
                $table->unique(['paper_id', 'reviewer_id']);
                $table->index(['reviewer_id', 'preference']);
            });
        }

        // Reviewer expertise table (note: plural name from migration)
        if (!Schema::hasTable('reviewer_expertises')) {
            Schema::create('reviewer_expertises', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('topic');
                $table->enum('level', ['expert', 'proficient', 'familiar', 'basic'])->default('familiar');
                $table->integer('confidence')->default(3);
                $table->timestamps();
                
                $table->unique(['user_id', 'topic']);
            });
        }

        // Discussions table
        if (!Schema::hasTable('discussions')) {
            Schema::create('discussions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('paper_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('parent_id')->nullable()->constrained('discussions')->onDelete('cascade');
                $table->text('content');
                $table->enum('type', ['general', 'review', 'rebuttal', 'decision', 'meta'])->default('general');
                $table->enum('visibility', ['public', 'reviewers', 'chairs', 'authors'])->default('reviewers');
                $table->boolean('is_resolved')->default(false);
                $table->json('metadata')->nullable();
                $table->timestamps();
                
                $table->index(['paper_id', 'type']);
                $table->index(['paper_id', 'visibility']);
                $table->index(['paper_id', 'is_resolved']);
            });
        }

        // Discussion participants table (not in your list, but needed)
        if (!Schema::hasTable('discussion_participants')) {
            Schema::create('discussion_participants', function (Blueprint $table) {
                $table->id();
                $table->foreignId('discussion_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->enum('role', ['author', 'reviewer', 'chair', 'observer'])->default('reviewer');
                $table->boolean('has_unread')->default(false);
                $table->timestamps();
                
                $table->unique(['discussion_id', 'user_id']);
            });
        }

        // Camera ready table (note: plural name from migration)
        if (!Schema::hasTable('camera_readies')) {
            Schema::create('camera_readies', function (Blueprint $table) {
                $table->id();
                $table->foreignId('paper_id')->constrained()->onDelete('cascade');
                $table->string('file_path');
                $table->string('file_name');
                $table->integer('file_size');
                $table->enum('format', ['pdf', 'docx', 'latex'])->default('pdf');
                $table->text('copyright_form_path')->nullable();
                $table->boolean('copyright_signed')->default(false);
                $table->json('author_order')->nullable();
                $table->json('metadata')->nullable();
                $table->text('changes_summary')->nullable();
                $table->enum('status', ['pending', 'submitted', 'approved', 'rejected'])->default('pending');
                $table->text('rejection_reason')->nullable();
                $table->foreignId('submitted_by')->constrained('users');
                $table->foreignId('approved_by')->nullable()->constrained('users');
                $table->dateTime('submitted_at')->nullable();
                $table->dateTime('approved_at')->nullable();
                $table->timestamps();
                
                $table->unique(['paper_id']);
                $table->index(['status', 'submitted_at']);
            });
        }

        // Proceedings tracks table (missing from your list)
        if (!Schema::hasTable('proceedings_tracks')) {
            Schema::create('proceedings_tracks', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('short_name');
                $table->text('description')->nullable();
                $table->foreignId('chair_id')->nullable()->constrained('users');
                $table->integer('order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Paper proceedings table (missing from your list)
        if (!Schema::hasTable('paper_proceedings')) {
            Schema::create('paper_proceedings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('paper_id')->constrained()->onDelete('cascade');
                $table->foreignId('proceedings_track_id')->constrained()->onDelete('cascade');
                $table->integer('page_start')->nullable();
                $table->integer('page_end')->nullable();
                $table->string('doi')->nullable();
                $table->text('citation')->nullable();
                $table->timestamps();
                
                $table->unique(['paper_id']);
            });
        }
    }

    public function down()
    {
        // Don't drop tables in down migration
    }
};