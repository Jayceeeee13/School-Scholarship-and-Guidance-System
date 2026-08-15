<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();

            $table->string('student_number')->unique();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');

            $table->date('birth_date')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();

            $table->string('email')->unique()->nullable();
            $table->string('phone_number')->nullable();

            $table->string('address')->nullable();

            $table->string('course')->nullable();
            $table->string('year_level')->nullable();
            $table->string('section')->nullable();

            $table->enum('status', ['active', 'inactive', 'graduated'])->default('active');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
