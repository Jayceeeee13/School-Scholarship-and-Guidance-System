<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('type_of_scholarships', function (Blueprint $table) {
            $table->boolean('uses_dtr')->default(false);
        });

        // keep current behavior: Student Representatives already use DTR
        DB::table('type_of_scholarships')
            ->where('name', 'Student Representatives')
            ->update(['uses_dtr' => true]);
    }

    public function down(): void
    {
        Schema::table('type_of_scholarships', function (Blueprint $table) {
            $table->dropColumn('uses_dtr');
        });
    }
};