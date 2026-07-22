<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\EngineerReview;


class User extends Authenticatable
{
    use HasFactory, Notifiable;

protected $fillable = [
    'name',
    'phone',
    'email',
    'password',
    'profile_photo',
    'role',
    'status',
    'engineer_membership_status',
    'engineer_active_until',
];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'engineer_active_until' => 'datetime',
        ];
    }

    public function employeeProfile()
    {
        return $this->hasOne(EmployeeProfile::class);
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class, 'customer_id');
    }

    public function sentMessages()
    {
        return $this->hasMany(ConsultationMessage::class, 'sender_id');
    }
    public function assignedConsultations()
{
    return $this->hasMany(
        Consultation::class,
        'engineer_id'
    );
}
public function engineerWorks()
{
    return $this->hasMany(
        EngineerWork::class,
        'engineer_id'
    );
}
public function engineerApplications()
{
    return $this->hasMany(
        EngineerApplication::class
    );
}
public function hasActiveEngineerMembership(): bool
{
    return $this->role === 'engineer'
        && $this->engineer_membership_status === 'active'
        && $this->engineer_active_until !== null
        && $this->engineer_active_until->isFuture();
}

public function isInactiveEngineer(): bool
{
    return $this->role === 'engineer'
        && ! $this->hasActiveEngineerMembership();
}
public function receivedEngineerReviews()
{
    return $this->hasMany(
        EngineerReview::class,
        'engineer_id'
    );
}

public function writtenEngineerReviews()
{
    return $this->hasMany(
        EngineerReview::class,
        'customer_id'
    );
}

public function getEngineerRatingAverageAttribute(): float
{
    return round(
        (float) $this
            ->receivedEngineerReviews()
            ->avg('rating'),
        1
    );
}

public function getEngineerReviewsCountAttribute(): int
{
    return $this
        ->receivedEngineerReviews()
        ->count();
}
}
