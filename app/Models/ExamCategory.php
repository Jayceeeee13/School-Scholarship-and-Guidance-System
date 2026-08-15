<?php
// app/Models/ExamCategory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ExamCategory extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function questions()
    {
        return $this->hasMany(ExamQuestion::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}