<?php

namespace App\Trait;

use Carbon\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\NewAccessToken;

trait HasStorableApiTokens
{
    use HasApiTokens;

    public function createToken(string $name, array $abilities = ['*'], $store = false)
    {
        $plainTextToken = md5($name . time());
        $token          = $this->tokens()->create([
            'name'       => $name,
            'token'      => hash('sha256', $plainTextToken),
            'kept_token' => $store ? $plainTextToken : null,
            'abilities'  => $abilities,
            'expires_at' => Carbon::now()->addMinutes(config('sanctum.expiration'))
        ]);

        return new NewAccessToken($token, $plainTextToken);
    }
}
