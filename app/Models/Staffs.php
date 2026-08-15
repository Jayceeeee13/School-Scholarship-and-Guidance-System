<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staffs extends Model
{
    protected $fillable = [
        'staff_number',
        'first_name',
        'middle_name',
        'last_name',
        'position',
        'birth_date',
        'age',
        'gender',
        'address',
        'email',
        'phone_number',
    ];
}
