<?php

namespace App\Models;

use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    public static function booted(){
        static::creating(function ($admin) {
            $admin->password = bcrypt($admin->password);
        });
        static::updating(function ($admin) {
            if ($admin->isDirty('password')) {
                $admin->password = bcrypt($admin->password);
            }
        });
        static::deleting(function ($admin) {
            if (Admin::count() === 1) {
                Notification::make()
                    ->danger()
                    ->title('Cannot delete last admin')
                    ->send();
                return false;
            }
        });
    }
}
