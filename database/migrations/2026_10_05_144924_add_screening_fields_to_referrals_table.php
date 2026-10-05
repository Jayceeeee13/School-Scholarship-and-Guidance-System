<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('referrals', function (Blueprint $table) {
            $table->string('reason_for_referral')->nullable()->after('case_presented');
            $table->string('reason_for_referral_other')->nullable()->after('reason_for_referral');

            $table->unsignedTinyInteger('urgency_level')->nullable()->after('reason_for_referral_other');

            $table->string('attempted_intervention')->nullable()->after('urgency_level');

            $table->string('relationship_with_student_other')->nullable()->after('attempted_intervention');
        });
    }

    public function down(): void
    {
        Schema::table('referrals', function (Blueprint $table) {
            $table->dropColumn([
                'reason_for_referral',
                'reason_for_referral_other',
                'urgency_level',
                'attempted_intervention',
                'relationship_with_student_other',
            ]);
        });
    }
};