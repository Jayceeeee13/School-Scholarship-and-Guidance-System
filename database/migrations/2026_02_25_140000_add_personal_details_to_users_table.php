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
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (! Schema::hasColumn('users', 'contact_no')) {
                    $table->string('contact_no', 20)->nullable()->after('role_id');
                }

                if (! Schema::hasColumn('users', 'course_and_year')) {
                    $table->string('course_and_year', 100)->nullable()->after('contact_no');
                }

                if (! Schema::hasColumn('users', 'gender_id')) {
                    $table->integer('gender_id')->nullable()->after('course_and_year');
                }

                if (! Schema::hasColumn('users', 'birthdate')) {
                    $table->date('birthdate')->nullable()->after('gender_id');
                }

                if (! Schema::hasColumn('users', 'address')) {
                    $table->string('address', 255)->nullable()->after('birthdate');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'address')) {
                    $table->dropColumn('address');
                }
                if (Schema::hasColumn('users', 'birthdate')) {
                    $table->dropColumn('birthdate');
                }
                if (Schema::hasColumn('users', 'gender_id')) {
                    $table->dropColumn('gender_id');
                }
                if (Schema::hasColumn('users', 'course_and_year')) {
                    $table->dropColumn('course_and_year');
                }
                if (Schema::hasColumn('users', 'contact_no')) {
                    $table->dropColumn('contact_no');
                }
            });
        }
    }
};

