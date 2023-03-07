<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReaderType extends Model
{
    use HasFactory;

    protected $table = 'reader_type';

    protected $fillable = [
        'name',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function library_book_list()
    {
        return $this->hasMany(LibraryBookList::class, 'reader_type_id', 'id');
    }
}
