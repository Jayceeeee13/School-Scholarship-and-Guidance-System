<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requirement_type_of_scholarship', function (Blueprint $table) {
            $table->id();
            $table->integer('requirement_id');
            $table->integer('type_of_scholarship_id');
            $table->timestamps();

            $table->foreign('requirement_id')->references('id')->on('requirements')->cascadeOnDelete();
            $table->foreign('type_of_scholarship_id')->references('id')->on('type_of_scholarships')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requirement_type_of_scholarship');
    }
};