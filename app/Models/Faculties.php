<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faculties extends Model
{
    protected $fillable = [
        'faculty_number',
        'first_name',
        'middle_name',
        'last_name',
        'department',
        'birth_date',
        'age',
        'gender',
        'address',
        'email',
        'phone_number',


    ];




}
