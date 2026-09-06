<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accomplishment_reports', function (Blueprint $table) {
            $table->string('scholar_type')->nullable()->after('scholar_id');
        });

        // Backfill existing rows to point at the original Scholars model,
        // so nothing breaks before the data migration command runs.
        DB::table('accomplishment_reports')
            ->whereNull('scholar_type')
            ->update(['scholar_type' => \App\Models\Scholars::class]);
    }

    public function down(): void
    {
        Schema::table('accomplishment_reports', function (Blueprint $table) {
            $table->dropColumn('scholar_type');
        });
    }
};