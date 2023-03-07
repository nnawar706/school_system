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
        Schema::create('library_book_list', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('library_shelf_id');
            $table->foreign('library_shelf_id')
                ->references('id')
                ->on('library_shelf');
            $table->unsignedBigInteger('library_book_category_id');
            $table->foreign('library_book_category_id')
                ->references('id')
                ->on('library_book_category');
            $table->unsignedBigInteger('reader_type_id');
            $table->foreign('reader_type_id')
                ->references('id')
                ->on('reader_type');
            $table->string('ISBN_no', 30)->unique();
            $table->string('title', 255);
            $table->string('author', 255);
            $table->string('publisher', 255)->nullable();
            $table->integer('cost_price');
            $table->integer('quantity');
            $table->integer('stock_amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('library_book_list');
    }
};
