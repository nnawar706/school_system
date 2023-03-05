<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibraryShelf extends Model
{
    use HasFactory;

    protected $table = 'library_shelf';

    protected $fillable = [
        'branch_id',
        'name',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function branch() {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
