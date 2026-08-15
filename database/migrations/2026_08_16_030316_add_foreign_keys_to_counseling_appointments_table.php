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
        Schema::table('counseling_appointments', function (Blueprint $table) {
            $table->foreign(['time_slot_id'], 'counseling_appointments_ibfk_1')->references(['id'])->on('counseling_time_slots')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['mode_of_counseling_id'], 'counseling_appointments_ibfk_2')->references(['id'])->on('mode_of_counselings')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['support_needed_id'], 'counseling_appointments_ibfk_3')->references(['id'])->on('support_neededs')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['parent_appointment_id'])->references(['id'])->on('counseling_appointments')->onUpdate('restrict')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('counseling_appointments', function (Blueprint $table) {
            $table->dropForeign('counseling_appointments_ibfk_1');
            $table->dropForeign('counseling_appointments_ibfk_2');
            $table->dropForeign('counseling_appointments_ibfk_3');
            $table->dropForeign('counseling_appointments_parent_appointment_id_foreign');
        });
    }
};
