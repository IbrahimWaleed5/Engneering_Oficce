<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


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

}
