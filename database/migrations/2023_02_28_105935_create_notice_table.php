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
        Schema::create('notice', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->foreign('branch_id')
                ->references('id')
                ->on('branch');
            $table->unsignedBigInteger('notice_type_id');
            $table->foreign('notice_type_id')
                ->references('id')
                ->on('notice_type');
            $table->string('title', 255);
            $table->text('details');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notice');
    }
};
