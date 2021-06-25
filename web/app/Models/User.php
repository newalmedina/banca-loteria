<?php

namespace App\Models;

use Carbon\Carbon;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Request;
use Illuminate\Notifications\Notifiable;
use Laratrust\Traits\LaratrustUserTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Notifications\AdminResetPasswordNotification;
use App\Notifications\FrontResetPasswordNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use LaratrustUserTrait;
    use Notifiable;
    use HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password',
        'last_login_at',
        'last_login_ip'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function userProfile()
    {
        return $this->hasOne('App\Models\UserProfile', 'user_id');
    }

    public function getCreatedAtFormattedAttribute()
    {
        try {
            if (!empty($this->created_at)) {
                return $this->created_at->format('d/m/Y');
            }
        } catch (\Exception $ex) {
        }

        return "";
    }

    public function scopeActive($query)
    {
        return $query->where('users.active', 1)->where('users.confirmed', 1);
    }

    /**
     * Scope with Profiles
     *
     * @param  string  $token
     * @return void
     */
    public function scopeUserProfiles($query)
    {
        return $query->join('user_profiles', 'user_profiles.user_id', '=', 'users.id');
    }


    public static function existUserLogin($username, $user_id = 0)
    {
        return(self::where("username", '=', $username)
                ->where('id', '<>', $user_id)
                ->count()>0);
    }

    /**
     * Scope que recibe el tipo de role y filtra.
     *
     * @param $query
     * @param $search. Role a buscar
     * @return mixed
     */
    public function scopeWithRole($query, $search)
    {
        return $query->whereHas('roles', function ($q) use ($search) {
            $q->where('name', '=', $search);
        });
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */

    public function sendPasswordResetNotification($token)
    {
        if (Request::route()->getPrefix() == "/admin" || substr(Request::route()->getPrefix(), 0, 6) === "admin/") {
            $this->notify(new AdminResetPasswordNotification($token));
        } else {
            $this->notify(new FrontResetPasswordNotification($token));
        }
    }

    public function online()
    {
        $online = false;
        try {
            $online = ($this->last_online_at > Carbon::now()->subMinutes(5)->format('Y-m-d H:i:s'));
        } catch (\Exception $ex) {
        }
        return $online;
    }

    public static function findByUsername($username)
    {
        return self::where('username', $username)->first();
    }
}
