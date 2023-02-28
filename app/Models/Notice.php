<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notice extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'notice';

    protected $fillable = [
        'branch_id',
        'notice_type_id',
        'title',
        'details'
    ];

    protected $hidden = [
        'deleted_at'
    ];

    protected $dates = ['deleted_at'];

    public function branch() {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function notice_type() {
        return $this->belongsTo(NoticeType::class, 'notice_type_id');
    }
}
