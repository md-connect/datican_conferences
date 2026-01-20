<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeConfidenceColumnTypeInReviewAssignments extends Migration
{
    public function up()
    {
        // Option A: Change to VARCHAR
        Schema::table('review_assignments', function (Blueprint $table) {
            $table->string('confidence', 20)->nullable()->change();
        });
        
        // Option B: Change to ENUM (more restrictive)
        // \DB::statement("ALTER TABLE review_assignments MODIFY COLUMN confidence ENUM('expert', 'familiar', 'passing', 'knowledgeable') NULL");
    }
    
    public function down()
    {
        Schema::table('review_assignments', function (Blueprint $table) {
            // Change back to integer if needed
            $table->integer('confidence')->nullable()->change();
        });
    }
}