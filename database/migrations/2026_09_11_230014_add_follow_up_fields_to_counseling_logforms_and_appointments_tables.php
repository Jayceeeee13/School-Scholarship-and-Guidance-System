<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('counseling_logforms', 'follow_up_required')) {
            Schema::table('counseling_logforms', function (Blueprint $table) {
                $table->boolean('follow_up_required')
                    ->default(false)
                    ->after('remarks');
            });
        }

        if (! Schema::hasColumn('counseling_appointments', 'source_logform_id')) {
            Schema::table('counseling_appointments', function (Blueprint $table) {
                $table->integer('source_logform_id')
                    ->nullable()
                    ->after('parent_appointment_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('counseling_appointments', 'source_logform_id')) {
            Schema::table('counseling_appointments', function (Blueprint $table) {
                $table->dropColumn('source_logform_id');
            });
        }

        if (Schema::hasColumn('counseling_logforms', 'follow_up_required')) {
            Schema::table('counseling_logforms', function (Blueprint $table) {
                $table->dropColumn('follow_up_required');
            });
        }
    }
};