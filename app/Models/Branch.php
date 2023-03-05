<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Branch extends Model
{
    use HasFactory, Notifiable;

    protected $table = 'branch';

    protected $fillable = [
        'name',
        'location',
    ];

    protected $hidden = [
        'updated_at'
    ];

    public function user()
    {
        return $this->hasMany(User::class, 'branch_id', 'id');
    }

    public function academic_year()
    {
        return $this->hasMany(AcademicYear::class, 'branch_id', 'id');
    }

    public function classroom()
    {
        return $this->hasMany(User::class, 'branch_id', 'id');
    }

    public function notice()
    {
        return $this->hasMany(Notice::class, 'branch_id', 'id');
    }

    public function library_shelf()
    {
        return $this->hasMany(LibraryShelf::class, 'branch_id', 'id');
    }

    public function class()
    {
        return $this->hasMany(Batch::class, 'branch_id', 'id');
    }
}
