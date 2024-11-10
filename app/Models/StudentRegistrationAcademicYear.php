<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentRegistrationAcademicYear extends Model
{
    use HasFactory;
    protected $fillable = ['academic_year'];

    public function registrationFees(): HasMany
    {
        return $this->hasMany(StudentRegistrationFee::class, 'academic_year_id');
    }
}
