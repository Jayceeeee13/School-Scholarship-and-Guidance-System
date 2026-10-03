<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('counseling_logforms', 'term_id')) {
            Schema::table('counseling_logforms', function (Blueprint $table) {
                $table->unsignedBigInteger('term_id')->nullable()->after('id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('counseling_logforms', 'term_id')) {
            Schema::table('counseling_logforms', function (Blueprint $table) {
                $table->dropColumn('term_id');
            });
        }
    }
};