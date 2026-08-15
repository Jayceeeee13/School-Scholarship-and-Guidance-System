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
        Schema::table('anecdotals', function (Blueprint $table) {
            $table->foreign(['personnel_id'], 'anecdotals_ibfk_2')->references(['id'])->on('personnels')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['counseling_logforms_id'], 'anecdotals_ibfk_3')->references(['id'])->on('counseling_logforms')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anecdotals', function (Blueprint $table) {
            $table->dropForeign('anecdotals_ibfk_2');
            $table->dropForeign('anecdotals_ibfk_3');
        });
    }
};
