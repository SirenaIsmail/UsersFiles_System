<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;
    protected $fillable =[
        'name',
        'user_id',
    ];


    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function usergroups()
    {
        return $this->hasMany(UserGroup::class, 'group_id', 'id');
    }

    public function groupfiles()
    {
        return $this->hasMany(File::class, 'group_id', 'id');
    }

}
