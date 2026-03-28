<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('review_assignments', function (Blueprint $table) {
            // Add new scoring criteria columns
            $table->integer('criteria_relevance')->nullable()->after('recommendation');
            $table->integer('criteria_originality')->nullable()->after('criteria_relevance');
            $table->integer('criteria_quality')->nullable()->after('criteria_originality');
            $table->integer('criteria_impact')->nullable()->after('criteria_quality');
            $table->integer('criteria_clarity')->nullable()->after('criteria_impact');
            $table->integer('criteria_contribution')->nullable()->after('criteria_clarity');
            $table->integer('total_score')->nullable()->after('criteria_contribution');
        });
    }

    public function down()
    {
        Schema::table('review_assignments', function (Blueprint $table) {
            $table->dropColumn([
                'criteria_relevance',
                'criteria_originality',
                'criteria_quality',
                'criteria_impact',
                'criteria_clarity',
                'criteria_contribution',
                'total_score'
            ]);
        });
    }
};