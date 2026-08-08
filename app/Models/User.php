<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable implements MustVerifyEmail, PasskeyUser
{
    use HasFactory;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use PasskeyAuthenticatable;

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

        /*
         * Email 2FA
         */
        'email_two_factor_enabled',
        'email_two_factor_verified_at',

        /*
         * Moderation
         */
        'warnings_count',
        'suspended_at',
        'suspension_reason',
        'suspension_source',

        /*
         * Engineer membership
         */
        'engineer_membership_status',
        'engineer_active_until',
    ];

    protected $hidden = [
        'password',
        'remember_token',

        /*
         * Fortify TOTP
         */
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

            /*
             * Fortify TOTP
             */
            'two_factor_confirmed_at' => 'datetime',

            /*
             * Email OTP 2FA
             */
            'email_two_factor_enabled' => 'boolean',
            'email_two_factor_verified_at' => 'datetime',

            /*
             * Moderation
             */
            'warnings_count' => 'integer',
            'suspended_at' => 'datetime',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Employee
    |--------------------------------------------------------------------------
    */

    public function employeeProfile()
    {
        return $this->hasOne(EmployeeProfile::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Consultations
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | Engineer
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | Engineer Reviews
    |--------------------------------------------------------------------------
    */

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
    | Engineering Offices
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
                [
                    'owner',
                    'manager',
                ]
            )
            ->where(
                'status',
                'active'
            );
    }

    public function isOfficeOwner(): bool
    {
        return $this->role === 'office_owner'
            && $this->ownedOffice !== null;
    }

    /*
    |--------------------------------------------------------------------------
    | General Conversations
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

    /*
    |--------------------------------------------------------------------------
    | Technical Support
    |--------------------------------------------------------------------------
    */

    public function supportTickets(): HasMany
    {
        return $this->hasMany(
            SupportTicket::class,
            'user_id'
        );
    }

    public function assignedSupportTickets(): HasMany
    {
        return $this->hasMany(
            SupportTicket::class,
            'assigned_employee_id'
        );
    }

    public function supportMessages(): HasMany
    {
        return $this->hasMany(
            SupportMessage::class,
            'sender_id'
        );
    }

    public function createdKnowledgeBaseArticles(): HasMany
    {
        return $this->hasMany(
            KnowledgeBaseArticle::class,
            'created_by'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Email Two-Factor Authentication
    |--------------------------------------------------------------------------
    */

    public function emailTwoFactorCodes(): HasMany
    {
        return $this->hasMany(
            EmailTwoFactorCode::class,
            'user_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Content Moderation / Warnings
    |--------------------------------------------------------------------------
    */

    public function contentModerations(): HasMany
    {
        return $this->hasMany(
            ContentModeration::class,
            'user_id'
        );
    }

    public function warnings(): HasMany
    {
        return $this->hasMany(
            UserWarning::class,
            'user_id'
        );
    }

    public function activeWarnings(): HasMany
    {
        return $this->hasMany(
            UserWarning::class,
            'user_id'
        )->whereIn(
            'status',
            [
                'active',
                'confirmed',
            ]
        );
    }

    public function issuedWarnings(): HasMany
    {
        return $this->hasMany(
            UserWarning::class,
            'issued_by'
        );
    }

    public function reviewedWarnings(): HasMany
    {
        return $this->hasMany(
            UserWarning::class,
            'reviewed_by'
        );
    }

    public function reviewedModerations(): HasMany
    {
        return $this->hasMany(
            ContentModeration::class,
            'reviewed_by'
        );
    }

    public function activeWarningsCount(): int
    {
        return $this
            ->activeWarnings()
            ->count();
    }

    public function hasReachedWarningLimit(): bool
    {
        return $this->activeWarningsCount() >= 3;
    }

    public function isSuspendedForReview(): bool
    {
        return $this->status ===
            'suspended_pending_review';
    }

    public function canUploadContent(): bool
    {
        return in_array(
            $this->status,
            [
                'active',
                'approved',
            ],
            true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Moderation Appeals
    |--------------------------------------------------------------------------
    */

    public function moderationAppeals(): HasMany
    {
        return $this->hasMany(
            ModerationAppeal::class
        );
    }

    public function reviewedModerationAppeals(): HasMany
    {
        return $this->hasMany(
            ModerationAppeal::class,
            'reviewed_by'
        );
    }

    public function hasPendingModerationAppeal(): bool
    {
        return $this
            ->moderationAppeals()
            ->whereIn(
                'status',
                [
                    'pending',
                    'under_review',
                ]
            )
            ->exists();
    }
}
