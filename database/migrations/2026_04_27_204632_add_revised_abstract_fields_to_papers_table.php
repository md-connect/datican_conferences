<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('papers', function (Blueprint $table) {
            $table->string('revised_abstract_file_path')->nullable()->after('file_path');
            $table->string('revised_abstract_file_name')->nullable()->after('revised_abstract_file_path');
            $table->integer('revised_abstract_file_size')->nullable()->after('revised_abstract_file_name');
            $table->timestamp('revised_abstract_uploaded_at')->nullable()->after('revised_abstract_file_size');
            $table->text('revised_abstract_content')->nullable()->after('revised_abstract_uploaded_at');
        });
    }

    public function down()
    {
        Schema::table('papers', function (Blueprint $table) {
            $table->dropColumn([
                'revised_abstract_file_path',
                'revised_abstract_file_name',
                'revised_abstract_file_size',
                'revised_abstract_uploaded_at',
                'revised_abstract_content'
            ]);
        });
    }
};