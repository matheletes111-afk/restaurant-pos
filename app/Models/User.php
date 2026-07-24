<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
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
        'permissions' => 'array',
    ];

    public function restaurant()
    {
        return $this->hasOne('App\Models\RestaurantMaster','id','restaurant_id');
    }

    public function hasPermission($menuKey, $action = 'view')
    {
        // Restaurant ADMIN and Super Admin have all access by default
        if ($this->role !== 'RES' || $this->role_type === 'ADMIN') {
            return true;
        }

        $perms = $this->permissions;
        if (!is_array($perms)) {
            return false;
        }

        // Support legacy permission where full module key is stored (meaning full access)
        if (in_array($menuKey, $perms)) {
            return true;
        }

        // Support granular permission check
        return in_array($menuKey . '.' . $action, $perms);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
