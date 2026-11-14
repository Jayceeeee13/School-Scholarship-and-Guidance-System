<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Students extends Model
{
    protected $fillable = [
        'user_id',
        'student_id',
        'first_name',
        'middle_name',
        'last_name',
        'extension_name',
        'birth_date',
        'gender_id',
        'program_id',
        'year_level',
        'term_id',
        'fathers_firstname',
        'fathers_middlename',
        'fathers_lastname',
        'mothers_firstname',
        'mothers_middlename',
        'mothers_lastname',
        'address',
        'zipcode',
        'disability',
        'contact_no',
        'email',
        'ip_group',
    ];

    public function gender()
    {
        return $this->belongsTo(Gender::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    protected static function booted(): void
    {
        // A student created without a term (for example from the admin
        // "New Student" form) is recorded on the active term. Imports and
        // anything else that passes a term_id keep the term they chose.
        static::creating(function (Students $student) {
            if (empty($student->term_id)) {
                $student->term_id = Term::where('is_active', true)->value('id');
            }
        });

        static::created(function (Students $student) {
            $user = User::where('last_name', $student->last_name)
                ->whereDate('birthdate', $student->birth_date)
                ->whereDoesntHave('student')
                ->first();

            if ($user) {
                $student->update(['user_id' => $user->id]);
            }
        });
    }

    // Student belongs to a user account
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}