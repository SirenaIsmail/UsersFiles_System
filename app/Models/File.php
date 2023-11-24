<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;
    protected $fillable =[
        'name',
        'path',
        'status',
        'user_id',
        'group_id'
    ];

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id', 'id');
    }


}
