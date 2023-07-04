<?php

namespace Bengr\Auth\Models;

use Illuminate\Database\Eloquent\Model;

class AuthToken extends Model
{
    protected $casts = [
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime'
    ];

    protected $fillable = [
        'name',
        'access_token',
        'refresh_token',
        'expires_at'
    ];

    protected $hidden = [
        'access_token',
        'refresh_tokne',
    ];

    public function tokenable()
    {
        return $this->morphTo('tokenable');
    }

    public static function findToken($token)
    {
        return static::where('access_token', hash('sha256', $token))->first();
    }
}
