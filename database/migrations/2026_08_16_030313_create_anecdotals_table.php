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
        Schema::create('anecdotals', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('counseling_logforms_id')->index('counseling_logforms_id');
            $table->text('area_concern');
            $table->text('concern');
            $table->text('intervention');
            $table->integer('personnel_id')->index('personnel_id');
            $table->timestamps(6);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anecdotals');
    }
};
