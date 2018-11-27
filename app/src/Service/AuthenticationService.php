<?php

namespace App\Service;

use Firebase\JWT\JWT;

class AuthenticationService
{
    const ALGORITHM = 'HS256';
    const EXPIRATION = 600;

    private $secret;

    public function __construct(string $secret)
    {
        $this->secret = $secret;
    }

    public function login(): string
    {
        $now = time();
        $token = ['iat' => $now, 'exp' => $now + self::EXPIRATION];

        return JWT::encode($token, $this->secret, self::ALGORITHM);
    }

    public function validate(string $token): void
    {
        JWT::decode($token, $this->secret, [self::ALGORITHM]);
    }
}
