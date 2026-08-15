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
        Schema::table('scholars', function (Blueprint $table) {
            $table->foreign(['user_id'], 'fk_scholars_user')->references(['id'])->on('users')->onUpdate('restrict')->onDelete('set null');
            $table->foreign(['department_head_id'])->references(['id'])->on('users')->onUpdate('restrict')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scholars', function (Blueprint $table) {
            $table->dropForeign('fk_scholars_user');
            $table->dropForeign('scholars_department_head_id_foreign');
        });
    }
};
