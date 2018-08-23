<?php

/*
 * presets: symfony
 */

namespace App;

use App\Notifications\ResetPassword;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->activation_token = str_random(20);
        });
    }

    public function gravatar($size = '110')
    {
        $hash = md5(trim($this->email));

        return "http://www.gravatar.com/avatar/{$hash}?s={$size}";
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function statuses()
    {
        return $this->hasMany(Status::class);
    }

    public function feed()
    {
        $userIds = Auth::user()->followings->pluck('id')->toArray();
        array_push($userIds, Auth::user()->id);

        return Status::whereIn('user_id', $userIds)
            ->with('user')
            ->latest();
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'user_id', 'follower_id');
    }

    public function followings()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'user_id');
    }

    public function follow($userIds)
    {
        if (!is_array($userIds)) {
            $userIds = [$userIds];
        }
        $this->followings()->sync($userIds, false);
    }

    public function unfollow($userIds)
    {
        if (!is_array($userIds)) {
            $userIds = [$userIds];
        }
        $this->followings()->detach($userIds, false);
    }

    public function isFollowing($userId)
    {
        return $this->followings->contains($userId);
    }
}
