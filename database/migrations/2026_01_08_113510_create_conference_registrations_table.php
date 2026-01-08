<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conference_registrations', function (Blueprint $table) {
            $table->id();
            $table->enum('title', ['Prof.', 'Dr.', 'Mr.', 'Mrs.', 'Miss']);
            $table->string('firstname');
            $table->string('middlename')->nullable();
            $table->string('lastname');
            $table->string('email')->unique();
            $table->string('phone_number');
            $table->string('institution');
            $table->enum('gender', ['Male', 'Female']);
            $table->boolean('is_datican_member')->default(false);
            $table->enum('datican_status', ['PI', 'Faculty', 'Trainer', 'PhD Student', 'MSc. Student'])->nullable();
            $table->boolean('is_presenting_paper')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conference_registrations');
    }
};