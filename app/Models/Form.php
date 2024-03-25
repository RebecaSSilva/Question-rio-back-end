<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'url',
        'button_color',
        'question_color',
        'answer_color',
        'background_color',
        'background_image',
        'logo',
        'font',
    ];

    public function user()
    {
        return $this->belongsTo(Form::class, 'user_id', 'id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}
