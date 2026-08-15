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
        Schema::create('counseling_logforms', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('counseling_appointments_id')->nullable()->index('counseling_appointments_id');
            $table->string('type', 20)->default('scheduled');
            $table->integer('support_needed_id')->nullable()->index('counseling_logforms_support_needed_id_foreign');
            $table->integer('walkin_student_id')->nullable()->index('counseling_logforms_walkin_student_id_foreign');
            $table->integer('referral_id')->nullable()->index('referral_id');
            $table->string('concern');
            $table->string('remarks', 200);
            $table->timestamp('archived_at')->nullable();
            $table->timestamps(6);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('counseling_logforms');
    }
};
