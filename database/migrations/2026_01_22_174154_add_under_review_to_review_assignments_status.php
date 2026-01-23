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
            DB::statement("ALTER TABLE review_assignments MODIFY COLUMN status ENUM('pending', 'accepted', 'under_review', 'in_progress', 'completed', 'declined') DEFAULT 'pending'");
        }
        
        // For PostgreSQL
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE review_assignments ALTER COLUMN status TYPE VARCHAR(255)");
            DB::statement("ALTER TABLE review_assignments ADD CONSTRAINT review_assignments_status_check CHECK (status IN ('pending', 'accepted', 'under_review', 'in_progress', 'completed', 'declined'))");
        }
        
        // For SQLite (if needed)
        if (DB::connection()->getDriverName() === 'sqlite') {
            // SQLite doesn't support ALTER COLUMN for ENUM, so we need to recreate the table
            // This is more complex and might require creating a new table
        }
    }

    public function down()
    {
        // Revert if needed
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE review_assignments MODIFY COLUMN status ENUM('pending', 'accepted', 'in_progress', 'completed', 'declined') DEFAULT 'pending'");
        }
    }
};