<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('papers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('abstract');
            $table->string('keywords')->nullable();
            $table->string('topic_area');
            $table->enum('submission_type', [
                'full_paper', 
                'short_paper', 
                'poster', 
                'demo',
                'workshop',
                'tutorial'
            ])->default('full_paper');
            $table->string('file_path');
            $table->string('file_name');
            $table->integer('file_size');
            $table->enum('status', [
                'draft',
                'submitted',
                'under_review',
                'accepted',
                'rejected',
                'camera_ready'
            ])->default('draft');
            $table->string('anonymous_id')->unique()->nullable();
            $table->boolean('is_anonymous')->default(true);
            $table->text('author_comments')->nullable();
            $table->enum('decision', [
                'accept',
                'minor_revisions',
                'major_revisions',
                'reject'
            ])->nullable();
            $table->text('decision_notes')->nullable();
            $table->dateTime('decision_at')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->dateTime('review_due_date')->nullable();
            $table->string('conference_year');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            
            $table->index(['conference_year', 'status']);
            $table->index('anonymous_id');
            $table->index('submitted_at');
        });

        // Paper Authors Pivot Table
        Schema::create('paper_authors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paper_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('is_corresponding')->default(false);
            $table->integer('author_order')->default(0);
            $table->timestamps();
            
            $table->unique(['paper_id', 'user_id']);
            $table->index(['paper_id', 'is_corresponding']);
        });

        // Paper Registrations Pivot Table
        Schema::create('paper_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paper_id')->constrained()->onDelete('cascade');
            $table->foreignId('conference_registration_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['paper_id', 'conference_registration_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('paper_registrations');
        Schema::dropIfExists('paper_authors');
        Schema::dropIfExists('papers');
    }
};