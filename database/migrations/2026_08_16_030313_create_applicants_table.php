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
        Schema::create('applicants', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('user_id')->nullable()->index('user_id');
            $table->string('picture', 100)->nullable();
            $table->integer('type_of_application_id')->index('type_of_application_id');
            $table->string('first_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100);
            $table->string('extension_name', 200)->nullable();
            $table->integer('gender_id')->index('gender_id');
            $table->string('contact_no', 20);
            $table->date('birthdate');
            $table->integer('age');
            $table->integer('program_id')->index('program_id');
            $table->string('year_level', 200);
            $table->string('religion', 100);
            $table->string('facebook_account', 100);
            $table->string('fathers_name', 100);
            $table->string('fathers_contact_no', 16);
            $table->string('mothers_name', 100);
            $table->string('mothers_contact_no', 16);
            $table->string('guardian', 100);
            $table->string('guardian_contact_no', 16);
            $table->integer('type_of_scholarship_id')->index('type_of_scholarship_id');
            $table->text('interview')->nullable();
            $table->string('benefit', 200)->nullable();
            $table->string('status', 100)->default('pending');
            $table->timestamps(6);
            $table->timestamp('archived_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicants');
    }
};
