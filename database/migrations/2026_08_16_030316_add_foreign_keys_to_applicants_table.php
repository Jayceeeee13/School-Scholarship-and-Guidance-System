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
        Schema::table('applicants', function (Blueprint $table) {
            $table->foreign(['type_of_application_id'], 'applicants_ibfk_1')->references(['id'])->on('type_of_applications')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['gender_id'], 'applicants_ibfk_2')->references(['id'])->on('genders')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['type_of_scholarship_id'], 'applicants_ibfk_3')->references(['id'])->on('type_of_scholarships')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropForeign('applicants_ibfk_1');
            $table->dropForeign('applicants_ibfk_2');
            $table->dropForeign('applicants_ibfk_3');
        });
    }
};
