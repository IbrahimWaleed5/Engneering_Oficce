<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
protected $fillable = [
    'consultation_number',
    'customer_id',
    'consultation_type_id',
    'engineer_id',
    'title',
    'description',
    'final_price',
    'status',
    'payment_status',
    'customer_file',
    'engineer_file',
];
public function consultationType()
{
    return $this->belongsTo(
        ConsultationType::class
    );
}

public function customer()
{
    return $this->belongsTo(
        User::class,
        'customer_id'
    );
}
public function engineer()
{
    return $this->belongsTo(User::class, 'engineer_id');
}
public function messages()
{
    return $this->hasMany(
        ConsultationMessage::class
    )->latest();
}

public function files()
{
    return $this->hasMany(
        ConsultationFile::class
    );
}

}

