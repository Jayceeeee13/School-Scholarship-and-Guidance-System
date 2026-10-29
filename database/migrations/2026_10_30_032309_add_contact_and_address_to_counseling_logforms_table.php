<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('counseling_logforms', function (Blueprint $table) {
            if (! Schema::hasColumn('counseling_logforms', 'contact_no')) {
                $table->string('contact_no', 20)->nullable()->after('concern');
            }
            if (! Schema::hasColumn('counseling_logforms', 'address')) {
                $table->text('address')->nullable()->after('contact_no');
            }
        });
    }

    public function down(): void
    {
        Schema::table('counseling_logforms', function (Blueprint $table) {
            $table->dropColumn(['contact_no', 'address']);
        });
    }
};