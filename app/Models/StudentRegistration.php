<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class StudentRegistration extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'registration_number',
        'nik',
        'family_card_number',
        'nisn',
        'full_name',
        'gender',
        'birth_place',
        'birth_date',
        'religion',
        'child_order',
        'siblings_count',
        'child_status',
        'height',
        'weight',
        'blood_type',
        'address',
        'village',
        'district',
        'city',
        'province',
        'postal_code',
        'email',
        'uniform_size',
        'residence_status',
        'transportation',
        'previous_school_type',
        'previous_school_address',
        'diploma_number',
        'diploma_date',
        'graduation_year',
        'student_phone',
        'father_name',
        'father_nik',
        'father_occupation',
        'father_income',
        'mother_name',
        'mother_nik',
        'mother_occupation',
        'mother_income',
        'parents_address',
        'parents_phone',
        'guardian_name',
        'guardian_occupation',
        'guardian_income',
        'guardian_address',
        'guardian_phone',
        'kks_number',
        'kip_number',
        'reference_source',
        'selected_major',
        'registration_status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'birth_date' => 'date',
        'diploma_date' => 'date',
        'graduation_year' => 'integer',
        'child_order' => 'integer',
        'siblings_count' => 'integer',
        'height' => 'integer',
        'weight' => 'integer',
        'father_income' => 'decimal:2',
        'mother_income' => 'decimal:2',
        'guardian_income' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'deleted_at',
    ];

    /**
     * Enum values for gender
     */
    const GENDER_MALE = 'male';
    const GENDER_FEMALE = 'female';

    /**
     * Enum values for registration status
     */
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    /**
     * Generate unique registration number
     */
    public static function generateRegistrationNumber(): string
    {
        $year = date('Y');
        $lastRegistration = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastRegistration ? intval(substr($lastRegistration->registration_number, -4)) + 1 : 1;

        return sprintf('REG%s%04d', $year, $sequence);
    }

    /**
     * Get full name attribute
     */
    public function getFullNameAttribute($value): string
    {
        return ucwords(strtolower($value));
    }

    /**
     * Get age attribute
     */
    public function getAgeAttribute(): int
    {
        return $this->birth_date->age;
    }

    /**
     * Get total parent income
     */
    public function getTotalParentIncomeAttribute(): float
    {
        return $this->father_income + $this->mother_income;
    }

    /**
     * Scope a query to only include pending registrations
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('registration_status', self::STATUS_PENDING);
    }

    /**
     * Scope a query to only include approved registrations
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('registration_status', self::STATUS_APPROVED);
    }

    /**
     * Scope a query to only include rejected registrations
     */
    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('registration_status', self::STATUS_REJECTED);
    }

    /**
     * Scope a query to filter by religion
     */
    public function scopeByReligion(Builder $query, string $religion): Builder
    {
        return $query->where('religion', strtolower($religion));
    }

    /**
     * Scope a query to filter by major
     */
    public function scopeByMajor(Builder $query, string $major): Builder
    {
        return $query->where('selected_major', $major);
    }

    /**
     * Scope a query to filter by age range
     */
    public function scopeByAgeRange(Builder $query, int $minAge, int $maxAge): Builder
    {
        $minDate = Carbon::now()->subYears($maxAge)->startOfDay();
        $maxDate = Carbon::now()->subYears($minAge)->endOfDay();

        return $query->whereBetween('birth_date', [$minDate, $maxDate]);
    }

    /**
     * Check if registration can be edited
     */
    public function canBeEdited(): bool
    {
        return $this->registration_status === self::STATUS_PENDING;
    }

    /**
     * Check if student has complete parent information
     */
    public function hasCompleteParentInfo(): bool
    {
        return !empty($this->father_name) &&
               !empty($this->father_nik) &&
               !empty($this->mother_name) &&
               !empty($this->mother_nik);
    }

    /**
     * Check if student has guardian
     */
    public function hasGuardian(): bool
    {
        return !empty($this->guardian_name);
    }

    /**
     * Check if student has social support
     */
    public function hasSocialSupport(): bool
    {
        return !empty($this->kks_number) || !empty($this->kip_number);
    }
}
