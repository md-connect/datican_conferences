<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Modify the ENUM to include 'needs_revision'
        DB::statement("ALTER TABLE papers MODIFY COLUMN status ENUM(
            'draft',
            'submitted',
            'under_review',
            'accepted',
            'rejected',
            'camera_ready',
            'abstract_submitted',
            'needs_revision'
        ) DEFAULT 'draft'");
    }

    public function down()
    {
        // Revert to original ENUM (remove 'needs_revision')
        DB::statement("ALTER TABLE papers MODIFY COLUMN status ENUM(
            'draft',
            'submitted',
            'under_review',
            'accepted',
            'rejected',
            'camera_ready',
            'abstract_submitted'
        ) DEFAULT 'draft'");
    }
};