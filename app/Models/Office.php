<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Office extends Model
{
    protected $fillable = [
        'owner_user_id',
        'name',
        'slug',
        'email',
        'phone',
        'commercial_registration',
        'license_number',
        'country',
        'city',
        'address',
        'logo_path',
        'cover_path',
        'description',
        'status',
        'subscription_status',
        'monthly_subscription_amount',
        'subscription_currency',
        'subscription_starts_at',
        'subscription_ends_at',
        'approved_at',
        'approved_by',
        'rejection_reason',
        'suspended_at',
        'suspended_by',
        'suspension_reason',
        'closed_at',
        'closed_by',
        'closure_reason',
    ];

    protected $casts = [
        'monthly_subscription_amount' => 'decimal:2',
        'subscription_starts_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'approved_at' => 'datetime',
        'suspended_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Office $office) {
            if (! $office->slug) {
                $baseSlug = Str::slug($office->name);
                $slug = $baseSlug;
                $counter = 2;

                while (
                    static::query()
                        ->where('slug', $slug)
                        ->exists()
                ) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }

                $office->slug = $slug;
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'owner_user_id'
        );
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'approved_by'
        );
    }

    public function suspender(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'suspended_by'
        );
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(
            OfficeSubscription::class
        );
    }

    public function membershipApplications(): HasMany
    {
        return $this->hasMany(
            OfficeMembershipApplication::class
        );
    }

    public function members(): HasMany
    {
        return $this->hasMany(
            OfficeMember::class
        );
    }

    public function activeMembers(): HasMany
    {
        return $this->members()
            ->where('status', 'active');
    }

    public function consultations(): HasMany
    {
        return $this->hasMany(
            Consultation::class,
            'assigned_office_id'
        );
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(
            ConsultationOfficeAssignment::class
        );
    }

    public function isOperational(): bool
    {
        return $this->status === 'active'
            && $this->subscription_status === 'active'
            && $this->subscription_ends_at !== null
            && $this->subscription_ends_at->isFuture();
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }
}
