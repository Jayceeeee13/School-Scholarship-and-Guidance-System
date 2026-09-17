<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_time_records', function (Blueprint $table) {
            $table->string('attendance_status', 20)->nullable()->after('status');
            $table->text('attendance_notes')->nullable()->after('attendance_status');
        });
    }

    public function down(): void
    {
        Schema::table('daily_time_records', function (Blueprint $table) {
            $table->dropColumn(['attendance_status', 'attendance_notes']);
        });
    }
};