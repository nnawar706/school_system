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
        Schema::create('transportation', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transport_route_id');
            $table->foreign('transport_route_id')
                ->references('id')
                ->on('transport_route');
            $table->unsignedBigInteger('driver_id');
            $table->foreign('driver_id')
                ->references('id')
                ->on('driver');
            $table->string('vehicle_reg_no', 30)->unique();
            $table->time('pickup_time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transportation');
    }
};
