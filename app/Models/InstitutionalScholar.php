<?php

namespace App\Models;

use App\Traits\LogsAllActivity;
use App\Traits\HasScholarProfile;
use Illuminate\Database\Eloquent\Model;

class InstitutionalScholar extends Model
{
    use LogsAllActivity, HasScholarProfile;

    protected $table = 'institutional_scholars';

    // Scholarship types allowed to submit accomplishment reports
    public const ACCOMPLISHMENT_ELIGIBLE_TYPES = [
        'Talents',
        'Supreme Student Government',
        'Sports',
    ];

    protected $fillable = [
        'student_id',
        'department_head_id',
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'extension_name',
        'sex',
        'birthdate',
        'program',
        'year_level',
        'type_of_scholarship',
        'batch_no',
        'ip_group',
        'pwd',
        'benefit',
        'status',
        'term_id',
        'revocation_reason',
        'revoked_at',
    ];

    protected $casts = [
        'birthdate'  => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];
}