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
        Schema::create('classroom', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->foreign('branch_id')
                ->references('id')
                ->on('branch');
            $table->string('name', 10)->unique()->index();
            $table->tinyInteger('max_student');
            $table->tinyInteger('student_quantity')->default(0);
            $table->tinyInteger('active_status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classroom');
    }
};
