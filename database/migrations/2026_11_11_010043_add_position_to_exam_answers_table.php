<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_answers', function (Blueprint $table) {
            // Order in which the question was displayed to the examinee
            $table->unsignedInteger('position')->nullable()->after('is_correct');
        });
    }

    public function down(): void
    {
        Schema::table('exam_answers', function (Blueprint $table) {
            $table->dropColumn('position');
        });
    }
};