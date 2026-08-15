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
        Schema::create('personnels', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('first_name', 500);
            $table->string('middle_name', 500)->nullable();
            $table->string('last_name', 500);
            $table->integer('age');
            $table->date('birthdate');
            $table->string('contact_no', 20);
            $table->string('address', 100);
            $table->string('email', 100);
            $table->timestamps(6);
            $table->timestamp('archived_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personnels');
    }
};
