<?php

declare(strict_types=1);
/*
 * This file is part of Leonsw.
 *
 * @link     https://leonsw.com
 * @document https://docs.leonsw.com
 * @contact  leonsw.com@gmail.com
 * @license  https://leonsw.com/LICENSE
 */
namespace TianMiaos\JWTAuth;

class Token
{
    public $token;

    public $ttl;

    public $iss;

    public function __construct(string $token, int $ttl, string $iss)
    {
        $this->token = $token;
        $this->ttl = $ttl;
        $this->iss = $iss;
    }
}
