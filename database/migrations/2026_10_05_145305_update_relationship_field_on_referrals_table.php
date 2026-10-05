<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('referrals', function (Blueprint $table) {
            if (Schema::hasColumn('referrals', 'relationship_with_student')) {
                $table->dropColumn('relationship_with_student');
            }

            $table->foreignId('relationship_type_id')
                ->nullable()
                ->after('attempted_intervention')
                ->constrained('relationship_types')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('referrals', function (Blueprint $table) {
            $table->dropConstrainedForeignId('relationship_type_id');
            $table->string('relationship_with_student')->nullable();
        });
    }
};