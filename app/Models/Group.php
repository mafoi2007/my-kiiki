<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'school_class_subject')
            ->withPivot(['subject_id', 'coefficient'])
            ->withTimestamps();
    }
}