<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'assigntest_id',
        'question_id',
        'option_id',
        'score',
    ];

    public function assigntest()
    {
        return $this->belongsTo(Assigntest::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function questionoption()
{
    return $this->belongsTo(Questionoptions::class, 'option_id');
}
}
