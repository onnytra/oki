<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assigntest extends Model
{
    use HasFactory;

    protected $fillable = [
        'is_done',
        'is_cheat',
        'cheat_reason',
        'exam_id',
        'user_id',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}
