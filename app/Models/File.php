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
        'forID',
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

    public function appoFor(){
        return $this->belongsTo(User::class,'forID','id');
    }

    public function history()
    {
        return $this->hasMany(FileHistory::class, 'file_id', 'id');
    }


}
