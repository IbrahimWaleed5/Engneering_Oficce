<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultationType extends Model
{
    use HasFactory;

    protected $fillable = [
        'specialty_id',
        'name',
        'description',
        'price',
        'estimated_days',
    ];

    public function specialty()
    {
        return $this->belongsTo(
            EngineeringSpecialty::class,
            'specialty_id'
        );
    }
}
