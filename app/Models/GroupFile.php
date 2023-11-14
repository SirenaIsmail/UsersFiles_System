<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupFile extends Model
{
    use HasFactory;
    protected $fillable=[
      'group_id',
      'file_id'
    ];


    public function files()
    {
        return $this->belongsTo(File::class, 'file_id');
    }

    public function groups()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }
}
