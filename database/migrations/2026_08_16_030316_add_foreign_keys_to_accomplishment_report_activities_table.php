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
        Schema::table('accomplishment_report_activities', function (Blueprint $table) {
            $table->foreign(['accomplishment_report_id'], 'fk_activities_report')->references(['id'])->on('accomplishment_reports')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accomplishment_report_activities', function (Blueprint $table) {
            $table->dropForeign('fk_activities_report');
        });
    }
};
