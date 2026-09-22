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
        'role',
        'role_type',
        'restaurant_id',
        'phone',
        'status',
        'permissions',
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

    public function isOwner(): bool
    {
        return ($this->role === 'RES' && $this->role_type === 'ADMIN');
    }

    public function getSubscriptionRestaurantId(): ?int
    {
        if (empty($this->restaurant_id)) {
            return null;
        }
        $rest = $this->restaurant;
        if (!$rest) {
            $rest = \App\Models\RestaurantMaster::find($this->restaurant_id);
        }
        if ($rest && !empty($rest->parent_id)) {
            return (int) $rest->parent_id;
        }
        return (int) $this->restaurant_id;
    }

    public function getMainRestaurant()
    {
        if (empty($this->restaurant_id)) {
            return null;
        }
        $rest = $this->restaurant ?: \App\Models\RestaurantMaster::find($this->restaurant_id);
        if (!$rest) {
            return null;
        }
        return $rest->getMainRestaurant();
    }

    public function getAvailableOutlets()
    {
        if (!$this->isOwner()) {
            return collect([$this->restaurant ?: \App\Models\RestaurantMaster::find($this->restaurant_id)])->filter();
        }

        $mainRest = $this->getMainRestaurant();
        if (!$mainRest) {
            return collect();
        }

        // Return main restaurant plus all non-deleted outlets
        $outlets = \App\Models\RestaurantMaster::where('parent_id', $mainRest->id)
            ->where('status', '!=', 'D')
            ->orderBy('id', 'asc')
            ->get();

        return collect([$mainRest])->merge($outlets);
    }

    public function hasMultiOutletAccess(): bool
    {
        $subRestId = $this->getSubscriptionRestaurantId();
        if (!$subRestId) {
            return false;
        }

        $activeSub = \App\Models\Subscription::where('user_id', $subRestId)
            ->whereIn('status', ['active', 'completed'])
            ->with('plan')
            ->first();

        if ($activeSub && $activeSub->plan) {
            return ($activeSub->plan->multi_outlet_checkbox === 'Y');
        }

        return false;
    }

    /**
     * Get the appropriate dashboard/destination URL based on user role and subscription status.
     *
     * @return string
     */
    public function getDashboardRedirectUrl()
    {
        if ($this->role === 'SA') {
            return route('admin.dashboard');
        }

        $subRestaurantId = $this->getSubscriptionRestaurantId();
        if (!empty($subRestaurantId)) {
            $active = \Illuminate\Support\Facades\DB::table('subscriptions')
                ->where('user_id', $subRestaurantId)
                ->where(function ($query) {
                    $query->where('status', 'active')
                          ->orWhere(function ($q) {
                              $q->where('status', 'completed')
                                ->whereDate('end_date', '>=', now());
                          });
                })
                ->first();

            if (!$active) {
                return route('select.plan.page');
            }
        }

        return route('dashboard');
    }
}
