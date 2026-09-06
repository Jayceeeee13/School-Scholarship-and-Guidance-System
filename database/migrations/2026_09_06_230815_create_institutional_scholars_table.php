<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institutional_scholars', function (Blueprint $table) {
            $table->id();
            $table->string('student_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('face_reference_picture')->nullable();
            $table->dateTime('face_registered_at')->nullable();
            $table->string('first_name', 200);
            $table->string('middle_name', 200)->nullable();
            $table->string('last_name', 200);
            $table->string('extension_name', 200)->nullable();
            $table->string('sex');
            $table->date('birthdate');
            $table->string('program', 200);
            $table->string('year_level');
            $table->string('type_of_scholarship');
            $table->integer('batch_no')->nullable();
            $table->string('ip_group', 200)->nullable();
            $table->string('pwd', 200)->nullable();
            $table->string('benefit')->nullable();
            $table->string('status')->default('active');
            $table->text('revocation_reason')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->unsignedBigInteger('term_id')->nullable();
            $table->unsignedBigInteger('department_head_id')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('term_id');
            $table->index('department_head_id');
            $table->index('type_of_scholarship');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institutional_scholars');
    }
};