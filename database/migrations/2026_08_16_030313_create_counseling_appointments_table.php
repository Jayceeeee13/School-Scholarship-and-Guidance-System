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
        Schema::create('counseling_appointments', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('parent_appointment_id')->nullable()->index('counseling_appointments_parent_appointment_id_foreign');
            $table->integer('student_id')->nullable();
            $table->string('last_name', 500);
            $table->string('first_name', 500);
            $table->string('middle_name', 500)->nullable();
            $table->string('course_and_year', 500);
            $table->string('contact_no', 20);
            $table->string('present_address', 200);
            $table->date('counseling_date');
            $table->integer('time_slot_id')->index('time_slot_id');
            $table->integer('mode_of_counseling_id')->index('mode_of_counseling_id');
            $table->integer('support_needed_id')->index('support_needed_id');
            $table->string('concern', 500);
            $table->string('status', 100)->nullable()->default('pending');
            $table->timestamps(6);
            $table->timestamp('archived_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('counseling_appointments');
    }
};
