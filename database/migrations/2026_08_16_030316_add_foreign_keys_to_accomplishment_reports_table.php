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
        Schema::table('accomplishment_reports', function (Blueprint $table) {
            $table->foreign(['scholar_id'], 'fk_accomplishment_reports_scholar')->references(['id'])->on('scholars')->onUpdate('restrict')->onDelete('cascade');
            $table->foreign(['term_id'], 'fk_accomplishment_reports_term')->references(['id'])->on('terms')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accomplishment_reports', function (Blueprint $table) {
            $table->dropForeign('fk_accomplishment_reports_scholar');
            $table->dropForeign('fk_accomplishment_reports_term');
        });
    }
};
