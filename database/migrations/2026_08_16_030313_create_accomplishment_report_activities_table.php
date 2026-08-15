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
        Schema::create('accomplishment_report_activities', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('accomplishment_report_id')->index('fk_activities_report');
            $table->integer('seq');
            $table->date('activity_date')->nullable();
            $table->string('venue')->nullable();
            $table->text('activity')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accomplishment_report_activities');
    }
};
