<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'nis',
        'name',
        'email',
        'phone_number',
        'school',
        'password',
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    public static function booted(){
        static::creating(function ($user) {
            $user->password = bcrypt($user->password);
        });
        static::updating(function ($user) {
            if ($user->isDirty('password')) {
                $user->password = bcrypt($user->password);
            }
        });
    }

    public function assigntests()
    {
        return $this->hasMany(Assigntest::class);
    }
}
