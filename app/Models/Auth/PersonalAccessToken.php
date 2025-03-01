<?php

namespace App\Models\Auth;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

/**
 * @property int $empresa_id
 * @property string $nconn
 *
 * @mixin \Eloquent
 */
class PersonalAccessToken extends SanctumPersonalAccessToken
{
    protected $connection = 'mysql';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'token',
        'abilities',
        'expires_at',
        'empresa_id',
        'nconn',
        'ip_address',
        'platform',
        'platform_version',
        'browser',
        'browser_vesion',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'token',
        'empresa_id',
        'nconn',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'abilities' => 'json',
            'last_used_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public static function getToken($token)
    {
        return SanctumPersonalAccessToken::findToken($token);
    }

    public static function getNconn($token)
    {
        $token = self::getToken($token);

        return $token ? $token->nconn : null;
    }

    public static function getEmpresaId($token): string | int | null
    {
        $token = self::getToken($token);

        return $token ? $token->empresa_id : null;
    }
}
