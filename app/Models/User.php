<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\VerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Repositories\FinanceOperationRepository;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use Notifiable;

    public const USER_ROLE = 'user';
    public const ADMIN_ROLE = 'admin';

    public static $roles = [self::USER_ROLE, self::ADMIN_ROLE];

    protected $fillable = [
        'name', 'surname', 'phone', 'birth_date',
        'city', 'email', 'password', 'referral',
        'role', 'available_amount', 'current_amount'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'birth_date' => 'date',
        'is_master' => 'boolean'
    ];

    public function followers()
    {
        return $this->hasMany('App\Models\User', 'referral');
    }

    public function withdrawals()
    {
        return $this->hasMany('App\Models\Withdrawal');
    }

    public function transactions()
    {
        return $this->hasMany('App\Models\Transaction');
    }

    public function confirmed_transactions()
    {
        return $this->hasMany('App\Models\Transaction')
            ->where('confirmed', true);
    }

    public function inviter()
    {
        return $this->belongsTo('App\Models\User', 'referral');
    }

    public function lessons()
    {
        return $this->belongsToMany('App\Models\Lesson')
            ->withPivot('home_work')
            ->withTimestamps();
    }

    public function tables()
    {
        return $this->belongsToMany('App\Models\Table')
            ->using('App\Pivots\TableUser')
            ->withPivot('id')
            ->withTimestamps();
    }

    public function emotions()
    {
        return $this->belongsToMany('App\Models\Emotion')
            ->withTimestamps();
    }

    public function promocode()
    {
        return $this->belongsTo('App\Models\Promocode');
    }

    public function subscriptions()
    {
        return $this->hasMany('App\Models\Subscription');
    }

    public function activeSubscriptions()
    {
        return $this
            ->hasMany('App\Models\Subscription')
            ->where('ends_at', '>', now());
    }

    public function getHasSubscriptionAttribute()
    {
        return $this->activeSubscriptions->isNotEmpty();
    }

    public function getSubscriptionAttribute()
    {
        return $this->activeSubscriptions->first();
    }

    public function sendEmailVerificationNotification() {
        $this->notify(new VerifyEmail);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function isAdmin()
    {
        return $this->role === self::ADMIN_ROLE;
    }
}