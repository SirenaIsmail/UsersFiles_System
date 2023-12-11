<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'role',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];



    public function files()
    {
        return $this->hasMany(File::class, 'user_id', 'id');
    }

    public function groups()
    {
        return $this->hasMany(Group::class, 'user_id', 'id');
    }

    public function usergroup()
    {
        return $this->hasMany(UserGroup::class, 'user_id', 'id');
    }

    public function userAppoFile()
    {
        return $this->hasMany(File::class, 'forID', 'id');
    }

    public function run()
    {
        User::factory()
            ->count(50)
            ->hasPosts(1)
            ->create();
    }
}
