<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'role_id',
        'name',
        'email',
        'password',
        'phone',
        'address',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function donasis()
    {
        return $this->hasMany(Donasi::class);
    }

    public function kunjungans()
    {
        return $this->hasMany(Kunjungan::class);
    }

    public function artikels()
    {
        return $this->hasMany(Artikel::class);
    }

    public function reimbursements()
    {
        return $this->hasMany(Reimbursement::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function hasRole($roleName)
    {
        return $this->role && $this->role->name === $roleName;
    }

    public function assignRole($roleName)
    {
        $role = Role::where('name', $roleName)->first();
        if ($role) {
            $this->role()->associate($role);
            $this->save();
        }
        return $this;
    }

    public function syncRoles($roleNames)
    {
        $roleName = is_array($roleNames) ? reset($roleNames) : $roleNames;
        return $this->assignRole($roleName);
    }

    public function getRoleNames()
    {
        return collect($this->role ? [$this->role->name] : []);
    }

    public function scopeRole($query, $roles)
    {
        $roles = is_array($roles) ? $roles : [$roles];
        return $query->whereHas('role', function($q) use ($roles) {
            $q->whereIn('name', $roles);
        });
    }
}
