<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('papers', function (Blueprint $table) {
            $table->boolean('needs_reviewer')->default(false)->after('status');
        });
    }

    public function down()
    {
        Schema::table('papers', function (Blueprint $table) {
            $table->dropColumn('needs_reviewer');
        });
    }
};