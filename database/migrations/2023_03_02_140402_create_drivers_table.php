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
        Schema::create('driver', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('license_no', 30)->index();
            $table->string('phone_no', 30)->unique();
            $table->string('nid_no', 20)->unique();
            $table->string('photo_url')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver');
    }
};
