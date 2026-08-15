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
        Schema::create('staffs', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('staff_number', 200);
            $table->string('first_number', 200);
            $table->string('middle_number', 200)->nullable();
            $table->string('last_name', 200);
            $table->string('position', 200);
            $table->date('birth_date');
            $table->integer('age');
            $table->string('gender', 200);
            $table->string('address', 200);
            $table->string('email', 200);
            $table->string('phone_number', 200);
            $table->timestamps(6);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staffs');
    }
};
