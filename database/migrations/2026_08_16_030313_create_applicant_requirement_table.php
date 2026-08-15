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
        Schema::create('applicant_requirement', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('applicant_id')->index('applicant_id');
            $table->integer('requirement_id')->index('requirement_id');
            $table->boolean('is_submitted')->default(true);
            $table->string('file_path', 500)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps(6);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicant_requirement');
    }
};
