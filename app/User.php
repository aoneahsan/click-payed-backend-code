<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Spatie\Permission\Traits\HasRoles;

use App\Model\UserAccount;
use App\Model\UserDetails;
use App\Model\UserAchievements;
use App\Model\UserTransactionHistory;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'phone_number', 'profile_img', 'role', 'firebase_token', 'referer_code', 'referer_user_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime'
    ];

    public function getTokken()
    {
        // return $this->createToken($request->device_name)->plainTextToken;
        return $this->createToken('mobile')->plainTextToken;
    }

    public function getProfileImg()
    {
        return $this->profile_img;
    }

    public function account()
    {
        return $this->hasOne('App\Model\UserAccount');
    }

    public function details()
    {
        return $this->hasOne('App\Model\UserDetails');
    }

    public function achievements()
    {
        return $this->hasOne('App\Model\UserAchievements');
    }

    public function transactions()
    {
        return $this->hasMany('App\Model\UserTransactionHistory');
    }

    public function referals()
    {
        return $this->hasMany('App\User', 'referer_user_id', 'id');
    }
}
