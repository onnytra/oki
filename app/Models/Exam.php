<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'rules',
        'start',
        'end',
        'true',
        'false',
        'empty',
        'show_result',
        'status_exam',
    ];

    public function assigntests()
    {
        return $this->hasMany(Assigntest::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }
}
