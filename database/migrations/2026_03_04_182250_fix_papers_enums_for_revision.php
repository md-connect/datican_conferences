<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Fix decision ENUM - add 'revise'
        DB::statement("ALTER TABLE papers MODIFY COLUMN decision ENUM(
            'accept',
            'reject',
            'revise',
            'minor_revisions',
            'major_revisions'
        ) NULL");
    }

    public function down()
    {

        // Revert decision ENUM to original
        DB::statement("ALTER TABLE papers MODIFY COLUMN decision ENUM(
            'accept',
            'minor_revisions',
            'major_revisions',
            'reject'
        ) NULL");
    }
};