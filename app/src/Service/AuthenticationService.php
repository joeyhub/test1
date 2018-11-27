<?php

namespace App\Service;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpFoundation\Request;
use Firebase\JWT\JWT;

class AuthenticationService
{
    const ALGORITHM = 'HS256';
    const EXPIRATION = 600;
    const HEADER = 'Authorization';
    const PREFIX = 'Bearer ';

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

    public function validate(Request $request): void
    {
        // Note: We can request it be sent in other ways and make life easier.
        $header = $request->headers->get(self::HEADER);
        $length = strlen(self::PREFIX);

        if (!is_string($header) || strncmp($header, self::PREFIX, $length)) {
            throw new AccessDeniedHttpException('Authorization token not found.');
        }

        $token = substr($header, $length);

        try {
            JWT::decode($token, $this->secret, [self::ALGORITHM]);
        } catch (\UnexpectedValueException $exception) {
            throw new AccessDeniedHttpException($exception->getMessage(), $exception);
        }
    }
}
