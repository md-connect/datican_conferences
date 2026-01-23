<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Add middle_name after first_name
            $table->string('middle_name')->nullable()->after('first_name');
            // Add institution if it doesn't exist
            if (!Schema::hasColumn('users', 'institution')) {
                $table->string('institution')->nullable()->after('last_name');
            }

            // Add department after institution or last_name
            $table->string('department')->nullable()->after('institution');

            // Add bio and research_interests
            $table->text('bio')->nullable()->after('department');
            $table->text('research_interests')->nullable()->after('bio');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'middle_name',
                'institution',
                'department', 
                'bio',
                'research_interests'
            ]);
        });
    }
};