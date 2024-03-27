<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_id',
        'field_slug',
        'field_title',
        'field_type',
        'value',
        'is_last',
        'value_key',
        'public_user_id',
    ];

    public function forms()
    {
        return $this->belongsTo(Form::class, 'form_id', 'id');
    }

}
