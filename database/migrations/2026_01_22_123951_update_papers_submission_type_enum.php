<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // For MySQL
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE papers MODIFY COLUMN submission_type ENUM('abstract_only', 'full_paper') DEFAULT 'full_paper'");
            DB::statement("ALTER TABLE papers MODIFY COLUMN status ENUM('draft', 'submitted', 'under_review', 'accepted', 'rejected', 'camera_ready', 'abstract_submitted') DEFAULT 'draft'");
        }
        
        // For PostgreSQL
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE papers ALTER COLUMN submission_type TYPE VARCHAR(255)");
            DB::statement("ALTER TABLE papers ADD CONSTRAINT papers_submission_type_check CHECK (submission_type IN ('abstract_only', 'full_paper'))");
            
            DB::statement("ALTER TABLE papers ALTER COLUMN status TYPE VARCHAR(255)");
            DB::statement("ALTER TABLE papers ADD CONSTRAINT papers_status_check CHECK (status IN ('draft', 'submitted', 'under_review', 'accepted', 'rejected', 'camera_ready', 'abstract_submitted'))");
        }
        
        // Make file fields nullable
        Schema::table('papers', function (Blueprint $table) {
            $table->string('file_path')->nullable()->change();
            $table->string('file_name')->nullable()->change();
            $table->integer('file_size')->nullable()->change();
        });
    }

    public function down()
    {
        // Revert changes if needed
        Schema::table('papers', function (Blueprint $table) {
            $table->string('file_path')->nullable(false)->change();
            $table->string('file_name')->nullable(false)->change();
            $table->integer('file_size')->nullable(false)->change();
        });
    }
};