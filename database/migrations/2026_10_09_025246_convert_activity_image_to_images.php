<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->json('images')->nullable()->after('description');
        });

        // Move each existing single image into the new list
        DB::table('activities')->whereNotNull('image')->get()->each(function ($row) {
            DB::table('activities')
                ->where('id', $row->id)
                ->update(['images' => json_encode([$row->image])]);
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->string('image')->nullable()->after('description');
        });

        // Keep only the first photo when rolling back
        DB::table('activities')->whereNotNull('images')->get()->each(function ($row) {
            $list = json_decode($row->images, true) ?: [];
            DB::table('activities')
                ->where('id', $row->id)
                ->update(['image' => $list[0] ?? null]);
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn('images');
        });
    }
};