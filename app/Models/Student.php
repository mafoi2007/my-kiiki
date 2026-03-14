<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'matricule',
        'school_matricule',
        'full_name',
        'birth_date',
        'birth_place',
        'school_class_id',
        'status',
        'sex',
        'father_name',
        'mother_name',
        'photo_path',
        'active',
    ];

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }
}
