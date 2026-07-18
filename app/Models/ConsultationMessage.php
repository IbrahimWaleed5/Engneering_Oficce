<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultationMessage extends Model
{
    protected $fillable = [
        'consultation_id',
        'sender_id',
        'message',
        'attachment',
    ];

    public function consultation()
    {
        return $this->belongsTo(
            Consultation::class
        );
    }

    public function sender()
    {
        return $this->belongsTo(
            User::class,
            'sender_id'
        );
    }
}
