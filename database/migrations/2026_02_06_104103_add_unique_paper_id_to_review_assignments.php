<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('review_assignments', function (Blueprint $table) {
            $table->unique('paper_id');
        });
    }

    public function down()
    {
        Schema::table('review_assignments', function (Blueprint $table) {
            $table->dropUnique(['paper_id']);
        });
    }
};
