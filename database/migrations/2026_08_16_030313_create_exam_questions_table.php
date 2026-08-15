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
        Schema::create('exam_questions', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('exam_id')->index('exam_id');
            $table->integer('exam_category_id')->index('exam_category_id');
            $table->text('question');
            $table->integer('points')->default(1);
            $table->integer('order')->default(0);
            $table->timestamps(6);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_questions');
    }
};
