<?php

namespace App\Models;

use App\Models\EngineerReview;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory;
    use Notifiable;
    use TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'country_code',
        'dial_code',
        'phone',
        'phone_verified_at',
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
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'engineer_active_until' => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    public function employeeProfile()
    {
        return $this->hasOne(EmployeeProfile::class);
    }

    public function consultations()
    {
        return $this->hasMany(
            Consultation::class,
            'customer_id'
        );
    }

    public function sentMessages()
    {
        return $this->hasMany(
            ConsultationMessage::class,
            'sender_id'
        );
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

    public function reviews(): HasMany
    {
        return $this->hasMany(
            Review::class,
            'customer_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | المكاتب الهندسية
    |--------------------------------------------------------------------------
    */

    public function ownedOffice(): HasOne
    {
        return $this->hasOne(
            Office::class,
            'owner_user_id'
        );
    }

    public function officeApplication(): HasOne
    {
        return $this->hasOne(
            OfficeApplication::class
        )->latestOfMany();
    }

    public function officeMembershipApplications(): HasMany
    {
        return $this->hasMany(
            OfficeMembershipApplication::class,
            'engineer_id'
        );
    }

    public function officeMemberships(): HasMany
    {
        return $this->hasMany(
            OfficeMember::class
        );
    }

    public function activeOfficeMembership(): HasOne
    {
        return $this->hasOne(
            OfficeMember::class
        )
            ->where('status', 'active')
            ->latestOfMany();
    }

    public function managedOfficeMemberships(): HasMany
    {
        return $this->officeMemberships()
            ->whereIn(
                'office_role',
                ['owner', 'manager']
            )
            ->where('status', 'active');
    }

    public function isOfficeOwner(): bool
    {
        return $this->role === 'office_owner'
            && $this->ownedOffice !== null;
    }

    /*
    |--------------------------------------------------------------------------
    | المحادثات العامة
    |--------------------------------------------------------------------------
    */

    public function conversations(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                Conversation::class,
                'conversation_participants'
            )
            ->withPivot([
                'last_read_at',
                'is_muted',
            ])
            ->withTimestamps();
    }

    public function conversationParticipants(): HasMany
    {
        return $this->hasMany(
            ConversationParticipant::class
        );
    }

    public function conversationMessages(): HasMany
    {
        return $this->hasMany(
            ConversationMessage::class,
            'sender_id'
        );
    }

    public function createdConversations(): HasMany
    {
        return $this->hasMany(
            Conversation::class,
            'created_by'
        );
    }
}
