<?php

namespace App\Trait;

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
        ]);

        return new NewAccessToken($token, $plainTextToken);
    }
}
