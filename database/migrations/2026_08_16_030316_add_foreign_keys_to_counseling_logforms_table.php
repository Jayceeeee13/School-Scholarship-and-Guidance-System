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
        Schema::table('counseling_logforms', function (Blueprint $table) {
            $table->foreign(['counseling_appointments_id'], 'counseling_logforms_ibfk_1')->references(['id'])->on('counseling_appointments')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['support_needed_id'])->references(['id'])->on('support_neededs')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['walkin_student_id'])->references(['id'])->on('students')->onUpdate('restrict')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('counseling_logforms', function (Blueprint $table) {
            $table->dropForeign('counseling_logforms_ibfk_1');
            $table->dropForeign('counseling_logforms_support_needed_id_foreign');
            $table->dropForeign('counseling_logforms_walkin_student_id_foreign');
        });
    }
};
