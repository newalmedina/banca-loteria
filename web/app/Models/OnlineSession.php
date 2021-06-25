<?php


namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/*
You need to place this Online::updateCurrent(); somewhere on your code, as this will make sure that the session
entry for the current user get's updated, just an example, you can place it on your app/routes.php file.

Getting all the Guest users
$guests = OnlineSession::guests()->get();

Getting the # of Guest users
$totalGuests = OnlineSession::guests()->count();

Getting the Registered users
$registered = OnlineSession::registered()->get();

Getting the # of Registered users
$totalRegistered = OnlineSession::registered()->count();

Getting a registered user information while looping
foreach ($registered as $online)
{
    var_dump($online->user->email);
}
 */
class OnlineSession extends Model
{
    public $incrementing = false;

    protected $hidden = ['payload'];

    protected $fillable = [
        'id',
        'last_online_at',
        'user_id'
    ];


    /**
     * {@inheritDoc}
     */
    public $table = 'online_sessions';

    /**
     * {@inheritDoc}
     */
    public $timestamps = false;

    public function getIdAttribute()
    {
        return $this->id;
    }

    /**
     * Returns all the guest users.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeGuests($query)
    {
        // Config::get('custom.activity_limit')
        return $query->whereNull('user_id')
            ->where('last_online_at', '>=', strtotime(Carbon::now()->subMinutes(5)));
    }

    /**
     * Returns all the registered users.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeRegistered($query)
    {
        //return $query->whereNotNull('user_id')->with('user');
        // Config::get('custom.activity_limit')
        return $query->whereNotNull('user_id')
            ->where('last_online_at', '>=', strtotime(Carbon::now()->subMinutes(5)))
            ->with('user');
    }

    /**
     * Updates the session of the current user.
     *
     * @param Builder $query
     * @return int
     */
    public function scopeUpdateCurrent($query)
    {
        return $query->where('id', session()->getId())->update(array(
            'user_id' => auth()->check() ? auth()->user()->id : null
        ));
    }


    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
