<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EngineerWork extends Model
{
protected $fillable = [
    'engineer_id',
    'title',
    'description',
    'location',
    'completion_year',
    'project_type',
    'status',
    'is_featured',
    'admin_note',
];

    protected $casts = [
        'is_featured' => 'boolean',
        'completion_year' => 'integer',
    ];

    public function engineer()
    {
        return $this->belongsTo(
            User::class,
            'engineer_id'
        );
    }

    public function images()
    {
        return $this->hasMany(
            EngineerWorkImage::class
        )->orderBy('sort_order');
    }

    public function coverImage()
    {
        return $this->hasOne(
            EngineerWorkImage::class
        )->orderBy('sort_order');
    }
}
