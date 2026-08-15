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
        Schema::table('daily_time_records', function (Blueprint $table) {
            $table->foreign(['approved_by_id'], 'dtr_approved_by_id_foreign')->references(['id'])->on('users')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['received_by_id'], 'dtr_received_by_id_foreign')->references(['id'])->on('users')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['scholar_id'], 'dtr_scholar_id_foreign')->references(['id'])->on('scholars')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_time_records', function (Blueprint $table) {
            $table->dropForeign('dtr_approved_by_id_foreign');
            $table->dropForeign('dtr_received_by_id_foreign');
            $table->dropForeign('dtr_scholar_id_foreign');
        });
    }
};
