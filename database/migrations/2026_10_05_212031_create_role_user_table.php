<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_user', function (Blueprint $table) {
            $table->increments('id');

            // Plain signed INT to match users.id and roles.id
            $table->integer('user_id');
            $table->integer('role_id');

            $table->timestamps();

            $table->unique(['user_id', 'role_id']);

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('role_id')->references('id')->on('roles')->cascadeOnDelete();
        });

        // Copy each existing user's single role into the pivot table
        DB::statement('
            INSERT INTO role_user (user_id, role_id, created_at, updated_at)
            SELECT id, role_id, NOW(), NOW()
            FROM users
            WHERE role_id IS NOT NULL
        ');
    }

    public function down(): void
    {
        Schema::dropIfExists('role_user');
    }
};