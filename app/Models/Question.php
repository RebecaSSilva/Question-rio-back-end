<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'field_slug',
        'field_title',
        'field_description',
        'field_type',
        'is_last',
        'mandatory',
        'value_key',
        'form_id',
    ];

    public function forms()
    {
        return $this->belongsTo(Form::class, 'form_id', 'id');
    }
}
