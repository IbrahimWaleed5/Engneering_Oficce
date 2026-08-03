<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Consultation extends Model
{
    protected $fillable = [
        'consultation_number',
        'customer_id',
        'consultation_type_id',
        'engineer_id',
        'assigned_office_id',
        'office_assigned_by',
        'office_assigned_at',
        'title',
        'description',
        'final_price',
        'status',
        'payment_status',
        'customer_file',
        'engineer_file',
        'started_at',
        'expected_delivery_at',
        'delivered_at',
    ];

    protected $casts = [
        'final_price' => 'decimal:2',
        'office_assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'expected_delivery_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function consultationType(): BelongsTo
    {
        return $this->belongsTo(
            ConsultationType::class
        );
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'customer_id'
        );
    }

    public function engineer(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'engineer_id'
        );
    }

    public function assignedOffice(): BelongsTo
    {
        return $this->belongsTo(
            Office::class,
            'assigned_office_id'
        );
    }

    public function officeAssigner(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'office_assigned_by'
        );
    }

    public function officeAssignments(): HasMany
    {
        return $this->hasMany(
            ConsultationOfficeAssignment::class
        )->latest('assigned_at');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(
            ConsultationMessage::class
        )->latest();
    }

    public function files(): HasMany
    {
        return $this->hasMany(
            ConsultationFile::class
        );
    }

    public function payments(): HasMany
    {
        return $this->hasMany(
            Payment::class
        );
    }

    public function review(): HasOne
    {
        return $this->hasOne(
            EngineerReview::class
        );
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(
            Invoice::class
        );
    }

    public function conversation(): HasOne
    {
        return $this->hasOne(
            Conversation::class
        );
    }
}
