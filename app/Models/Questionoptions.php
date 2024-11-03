<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Questionoptions extends Model
{
    use HasFactory;

    protected $fillable = [
        'option',
        'is_true',
        'question_id',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}
