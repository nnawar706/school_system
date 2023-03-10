<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibraryBookCategory extends Model
{
    use HasFactory;

    protected $table = 'library_book_category';

    protected $fillable = [
        'name',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function library_book_list()
    {
        return $this->hasMany(LibraryBookList::class, 'library_book_category_id', 'id');
    }
}
