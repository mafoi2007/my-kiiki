<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLES = [
        'cellule_informatique',
        'chef_etablissement',
        'censeur',
        'surveillant_general',
        'econome',
        'enseignant',
        'parent',
    ];       
         
    protected $fillable = [
        'name',
       'login',
        'role',
        'password',
    ];
     
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function isRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function children(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'parent_student', 'parent_id', 'student_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(TeacherAssignment::class, 'teacher_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'recipient_id');
    }
}
