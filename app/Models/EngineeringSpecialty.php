<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EngineeringSpecialty extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function employees()
    {
        return $this->hasMany(
            EmployeeProfile::class,
            'specialty_id'
        );
    }

    public function consultationTypes()
    {
        return $this->hasMany(
            ConsultationType::class,
            'specialty_id'
        );
    }
}
