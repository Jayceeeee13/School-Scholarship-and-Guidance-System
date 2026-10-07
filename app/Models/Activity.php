<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image',
        'activity_date',
    ];

    protected $casts = [
        'activity_date' => 'date',
    ];
}