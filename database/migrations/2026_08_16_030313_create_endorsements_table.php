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
        Schema::create('endorsements', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('counseling_appointment_id')->nullable();
            $table->integer('referral_id')->nullable();
            $table->date('date');
            $table->string('to_where', 200);
            $table->string('from_where', 200);
            $table->text('issue');
            $table->integer('personnel_id');
            $table->string('received_by', 200)->nullable();
            $table->date('receive_date')->nullable();
            $table->timestamps(6);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('endorsements');
    }
};
