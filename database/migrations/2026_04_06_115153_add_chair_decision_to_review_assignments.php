<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('review_assignments', function (Blueprint $table) {
            $table->enum('chair_decision', [
                'accept', 
                'accept_with_minor_revision', 
                'accept_with_major_revision', 
                'reject'
            ])->nullable()->after('recommendation');
            $table->text('chair_decision_notes')->nullable()->after('chair_decision');
            $table->timestamp('chair_decision_made_at')->nullable()->after('chair_decision_notes');
            $table->foreignId('chair_decision_made_by')->nullable()->after('chair_decision_made_at')
                  ->constrained('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('review_assignments', function (Blueprint $table) {
            $table->dropForeign(['chair_decision_made_by']);
            $table->dropColumn([
                'chair_decision',
                'chair_decision_notes',
                'chair_decision_made_at',
                'chair_decision_made_by'
            ]);
        });
    }
};