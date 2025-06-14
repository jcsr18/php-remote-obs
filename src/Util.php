<?php

namespace Jcsr18\PhpRemoteObs;

class Util
{
    public static function generateRequestId(string $prefix): string
    {
        return uniqid($prefix);
    }

    public static function generateObsAuth(string $salt, string $challenge, string $password): string
    {
        $secret = base64_encode(hash('sha256', $password.$salt, true));

        return base64_encode(hash('sha256', $secret.$challenge, true));
    }
}