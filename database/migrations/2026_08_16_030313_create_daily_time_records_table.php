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
        Schema::create('daily_time_records', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('scholar_id')->index('dtr_scholar_id_foreign');
            $table->string('office_assigned', 150)->nullable();
            $table->date('date');
            $table->time('am_in')->nullable();
            $table->string('am_in_location', 150)->nullable();
            $table->time('am_out')->nullable();
            $table->string('am_out_location', 150)->nullable();
            $table->time('pm_in')->nullable();
            $table->string('pm_in_location', 150)->nullable();
            $table->time('pm_out')->nullable();
            $table->string('pm_out_location', 150)->nullable();
            $table->decimal('total_hours', 5)->nullable();
            $table->string('status', 20)->default('pending');
            $table->text('remarks')->nullable();
            $table->integer('approved_by_id')->nullable()->index('dtr_approved_by_id_foreign');
            $table->timestamp('approved_at')->nullable();
            $table->integer('received_by_id')->nullable()->index('dtr_received_by_id_foreign');
            $table->timestamp('received_at')->nullable();
            $table->timestamps();
            $table->decimal('am_in_latitude', 10, 7)->nullable();
            $table->decimal('am_in_longitude', 10, 7)->nullable();
            $table->string('am_in_address')->nullable();
            $table->boolean('am_in_verified')->default(false);
            $table->decimal('am_out_latitude', 10, 7)->nullable();
            $table->decimal('am_out_longitude', 10, 7)->nullable();
            $table->string('am_out_address')->nullable();
            $table->boolean('am_out_verified')->default(false);
            $table->decimal('pm_in_latitude', 10, 7)->nullable();
            $table->decimal('pm_in_longitude', 10, 7)->nullable();
            $table->string('pm_in_address')->nullable();
            $table->boolean('pm_in_verified')->default(false);
            $table->decimal('pm_out_latitude', 10, 7)->nullable();
            $table->decimal('pm_out_longitude', 10, 7)->nullable();
            $table->string('pm_out_address')->nullable();
            $table->boolean('pm_out_verified')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_time_records');
    }
};
