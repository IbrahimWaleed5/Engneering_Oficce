<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EngineerWorkImage extends Model
{
    protected $fillable = [
        'engineer_work_id',
        'image_path',
        'caption',
        'sort_order',
    ];

    public function work()
    {
        return $this->belongsTo(
            EngineerWork::class,
            'engineer_work_id'
        );
    }
}
