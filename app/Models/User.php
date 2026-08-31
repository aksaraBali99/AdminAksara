<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    const ROLE_OWNER = 'owner';
    const ROLE_FINANCE = 'finance';
    const ROLE_HR = 'hr';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user is owner.
     */
    public function isOwner(): bool
    {
        return $this->role === self::ROLE_OWNER;
    }

    /**
     * Check if user is finance.
     */
    public function isFinance(): bool
    {
        return $this->role === self::ROLE_FINANCE;
    }

    /**
     * Check if user is hr.
     */
    public function isHr(): bool
    {
        return $this->role === self::ROLE_HR;
    }

    /**
     * Check if user has access to admin features.
     */
    public function hasAdminAccess(): bool
    {
        return $this->isOwner();
    }
}
