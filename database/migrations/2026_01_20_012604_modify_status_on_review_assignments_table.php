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
    \Illuminate\Support\Facades\Schema::table('review_assignments', function (\Illuminate\Database\Schema\Blueprint $table) {
        $table->enum('status', ['pending', 'accepted', 'in_progress', 'completed', 'declined'])
              ->default('pending')
              ->change();
    });
}

public function down()
{
    \Illuminate\Support\Facades\Schema::table('review_assignments', function (\Illuminate\Database\Schema\Blueprint $table) {
        $table->string('status')->default('pending')->change();
    });
}

};
