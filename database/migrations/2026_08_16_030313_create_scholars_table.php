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
        Schema::create('scholars', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('student_id')->nullable();
            $table->integer('user_id')->nullable()->index('fk_scholars_user');
            $table->string('face_reference_picture')->nullable();
            $table->dateTime('face_registered_at')->nullable();
            $table->string('first_name', 200);
            $table->string('middle_name', 200)->nullable();
            $table->string('last_name', 200);
            $table->string('extension_name', 200)->nullable();
            $table->string('sex', 200);
            $table->date('birthdate');
            $table->string('program', 200);
            $table->string('year_level');
            $table->string('type_of_scholarship');
            $table->integer('batch_no')->nullable();
            $table->string('ip_group', 200)->nullable();
            $table->string('pwd', 200)->nullable();
            $table->string('benefit')->nullable();
            $table->string('status');
            $table->text('revocation_reason')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->integer('term_id')->nullable();
            $table->integer('department_head_id')->nullable()->index('scholars_department_head_id_foreign');
            $table->timestamps(6);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scholars');
    }
};
