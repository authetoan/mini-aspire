<?php

namespace App\Domain\Entities\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    const ADMIN = 'admin';
    const CUSTOMER = 'customer';

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
