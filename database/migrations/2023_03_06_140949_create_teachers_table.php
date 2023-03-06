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
        Schema::create('teacher', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users');
            $table->unsignedBigInteger('designation_id');
            $table->foreign('designation_id')
                ->references('id')
                ->on('designation');
            $table->string('name', 255);
            $table->string('email', 255)->unique();
            $table->string('phone_no', 20)->unique();
            $table->string('nid', 20)->unique();
            $table->unsignedBigInteger('religion_id');
            $table->foreign('religion_id')
                ->references('id')
                ->on('religion');
            $table->unsignedBigInteger('expertise_subject_id');
            $table->foreign('expertise_subject_id')
                ->references('id')
                ->on('subject');
            $table->date('dob');
            $table->unsignedBigInteger('gender_id');
            $table->foreign('gender_id')
                ->references('id')
                ->on('gender');
            $table->string('profile_photo_url')->unique();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher');
    }
};
