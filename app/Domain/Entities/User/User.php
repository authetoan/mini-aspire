<?php

namespace App\Domain\Entities\User;

use App\Domain\Entities\Loan\Loan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Define the relationship between the User and Loan models
    public function loans()
    {
        return $this->hasMany(Loan::class, 'customer_id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    // Method to check if the user has a specific role
    public function hasRole($roleName)
    {
        return $this->roles->contains('name', $roleName);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(Role::ADMIN);
    }
}
