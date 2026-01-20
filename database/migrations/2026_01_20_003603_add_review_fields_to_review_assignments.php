<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('review_assignments', function (Blueprint $table) {
        $table->json('scores')->nullable();
        $table->decimal('overall_score', 5, 2)->nullable();
        $table->text('comments_author')->nullable();
        $table->text('comments_chair')->nullable();
        $table->unsignedTinyInteger('confidence')->nullable();
        $table->text('summary')->nullable();
        $table->text('strengths')->nullable();
        $table->text('weaknesses')->nullable();
        $table->text('suggestions')->nullable();
        $table->string('recommendation')->nullable();
        $table->timestamp('started_at')->nullable();
        $table->timestamp('submitted_at')->nullable();
        $table->timestamp('due_date')->nullable();
        $table->boolean('is_anonymous')->default(true);
    });
}

public function down()
{
    Schema::table('review_assignments', function (Blueprint $table) {
        $table->dropColumn([
            'scores','overall_score','comments_author','comments_chair',
            'confidence','summary','strengths','weaknesses','suggestions',
            'recommendation','started_at','submitted_at','due_date','is_anonymous'
        ]);
    });
}

};
