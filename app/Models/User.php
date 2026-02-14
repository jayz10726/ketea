<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department',
        'phone',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // Relationships
    public function issuancesReceived()
    {
        return $this->hasMany(Issuance::class, 'issued_to');
    }

    public function issuancesGiven()
    {
        return $this->hasMany(Issuance::class, 'issued_by');
    }

    // Role helpers
    public function isAdmin(): bool       { return $this->role === 'admin'; }
    public function isStorekeeper(): bool { return $this->role === 'storekeeper'; }
    public function isStaff(): bool       { return $this->role === 'staff'; }
}