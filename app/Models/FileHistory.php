<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use mysql_xdevapi\Table;

class FileHistory extends Model
{
    use HasFactory;

    protected $table = 'files_history';

    protected $fillable=[
        'file_name',
        'action',
        'file_id',
        'user_id',
    ];


    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function file()
    {
        return $this->belongsTo(File::class, 'file_id');
    }
}
