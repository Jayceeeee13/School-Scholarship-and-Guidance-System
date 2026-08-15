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
        Schema::create('referral_invitations', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('referral_id');
            $table->date('session_date')->nullable();
            $table->integer('time_slot_id')->nullable();
            $table->string('purpose', 200)->nullable();
            $table->integer('personnel_id')->nullable();
            $table->enum('status', ['pending', 'accepted', 'declined'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_invitations');
    }
};
