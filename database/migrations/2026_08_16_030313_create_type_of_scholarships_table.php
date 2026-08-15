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
        Schema::create('type_of_scholarships', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name', 100);
            $table->integer('slots')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps(6);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('type_of_scholarships');
    }
};
