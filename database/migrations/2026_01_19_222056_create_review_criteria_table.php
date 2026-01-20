<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('review_criteria', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('weight')->default(100); // Weight in percentage (e.g., 100 = 100%)
            $table->integer('min_score')->default(1);
            $table->integer('max_score')->default(5);
            $table->boolean('is_active')->default(true);
            $table->json('options')->nullable(); // For custom labels, etc.
            $table->timestamps();
            
            // Add conference year if needed
            $table->string('conference_year')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('review_criteria');
    }
};