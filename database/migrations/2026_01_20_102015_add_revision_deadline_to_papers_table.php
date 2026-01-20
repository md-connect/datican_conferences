<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('papers', function (Blueprint $table) {
            // Add revision_deadline column after decision_notes
            $table->date('revision_deadline')->nullable()->after('decision_notes');
            
            // Also make sure other decision-related columns exist
            if (!Schema::hasColumn('papers', 'decision_made_at')) {
                $table->timestamp('decision_made_at')->nullable()->after('revision_deadline');
            }
            
            if (!Schema::hasColumn('papers', 'decision_made_by')) {
                $table->foreignId('decision_made_by')->nullable()->constrained('users')->after('decision_made_at');
            }
        });
    }

    public function down()
    {
        Schema::table('papers', function (Blueprint $table) {
            $table->dropColumn(['revision_deadline', 'decision_made_at']);
            $table->dropForeign(['decision_made_by']);
            $table->dropColumn('decision_made_by');
        });
    }
};