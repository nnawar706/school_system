<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibraryBookList extends Model
{
    use HasFactory;

    protected $table = 'library_book_list';

    protected $fillable = [
        'library_shelf_id',
        'library_book_category_id',
        'reader_type_id',
        'ISBN_no',
        'title',
        'author',
        'publisher',
        'cost_price',
        'quantity',
        'stock_amount',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function library_shelf()
    {
        return $this->belongsTo(LibraryShelf::class, 'library_shelf_id');
    }

    public function library_book_category()
    {
        return $this->belongsTo(LibraryBookCategory::class, 'library_book_category_id');
    }

    public function reader_type()
    {
        return $this->belongsTo(ReaderType::class, 'reader_type_id');
    }
}
