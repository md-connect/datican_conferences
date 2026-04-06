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
        DB::statement("
            ALTER TABLE papers 
            MODIFY decision ENUM(
                'accept',
                'reject',
                'accept_with_minor_revision',
                'accept_with_major_revision'
            )
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('papers', function (Blueprint $table) {
            //
        });
    }
};
