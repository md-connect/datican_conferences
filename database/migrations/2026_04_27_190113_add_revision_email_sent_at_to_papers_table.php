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
        Schema::table('papers', function (Blueprint $table) {
            $table->timestamp('revision_email_sent_at')->nullable()->after('revision_notes');
        });
    }

    public function down()
    {
        Schema::table('papers', function (Blueprint $table) {
            $table->dropColumn('revision_email_sent_at');
        });
    }
};
