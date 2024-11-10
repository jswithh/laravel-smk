<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentRegistrationFee extends Model
{
    use HasFactory;
    protected $fillable = ['academic_year_id
    ', 'name', 'amount'];

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(StudentRegistrationAcademicYear::class, 'academic_year_id');
    }
}
