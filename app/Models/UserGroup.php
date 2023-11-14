<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    use HasFactory;
    protected $fillable=[
        'group_id',
        'file_id'
    ];

    public function groups()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
