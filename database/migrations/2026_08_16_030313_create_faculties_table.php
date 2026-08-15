<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('faculties', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('faculty_number', 200);
            $table->string('first_name', 200);
            $table->string('middle_name', 200)->nullable();
            $table->string('last_name', 200);
            $table->string('department', 200);
            $table->date('birth_date');
            $table->integer('age');
            $table->string('gender', 200);
            $table->string('address', 200);
            $table->string('email', 200);
            $table->string('phone_number', 200);
            $table->timestamp('created_at', 6)->nullable();
            $table->timestamp('update_at', 6)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faculties');
    }
};
