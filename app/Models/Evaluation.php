<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = ['school_class_id', 'sequence_number', 'starts_at', 'ends_at', 'is_open'];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date',
        'is_open' => 'boolean',
    ];

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }
}
