<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportSetting extends Model
{
    protected $fillable = [
        'support_employee_id',
        'updated_by',
    ];

    public function supportEmployee(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'support_employee_id'
        );
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'updated_by'
        );
    }

    public static function current(): self
    {
        return self::query()->firstOrCreate([]);
    }
}
