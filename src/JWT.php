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

use Firebase\JWT\JWT as FirebaseJWT;
use Hyperf\HttpServer\Contract\RequestInterface;
use Leonsw\Http\BadRequestException;
use Psr\Container\ContainerInterface;
use Psr\SimpleCache\CacheInterface;
use Throwable;

class JWT
{
    protected RequestInterface $request;

    protected CacheInterface $cache;

    public function __construct(ContainerInterface $container)
    {
        $this->request = $container->get(RequestInterface::class);
        $this->cache = $container->get(CacheInterface::class);
    }

    public function getSecret()
    {
        return config('jwt.secret', 'leonsw');
    }

    public function getTTL()
    {
        return config('jwt.ttl', 7200);
    }

    public function getAlg()
    {
        return config('jwt.alg', 'HS256');
    }

    /**
     * 生成token.
     * @param mixed $user
     * @return string
     */
    public function createToken($user, string $iss = 'api'): Token
    {
        $secret = $this->getSecret();
        $ttl = $this->getTTL();
        $time = time();
        $payload = [
            'iss' => $iss,
            'aud' => $user,
            'iat' => $time,
            'nbf' => $time,
            'exp' => $time + $ttl,
        ];
        $token = FirebaseJWT::encode($payload, $secret);
//        $this->cache->set($iss . '.jwt.' . $userId, $token, $ttl);

        return new Token($token, $ttl, $iss);
    }

    public function refreshToken(): Token
    {
        try {
            $token = $this->getToken();
            $decoded = $this->decode($token);
            if ($decoded) {
                return $this->createToken($decoded->aud, $decoded->iss);
            }
        } catch (Throwable $e) {
            throw new BadRequestException('Refresh token fail: ' . $e->getMessage());
        }
        throw new BadRequestException('Refresh token fail!');
    }

    public function logout(): void
    {
        try {
            $token = $this->getToken();
            $decoded = $this->decode($token);
//            if ($decoded) {
//                $this->cache->delete($this->getCacheKey($decoded->aud, $decoded->iss));
//            }
        } catch (Throwable $e) {
            throw new BadRequestException('Logout fail: ' . $e->getMessage());
        }
    }

    /**
     * 根据token获取 user .
     * @throws Throwable
     * @return mixed
     */
    public function getUserByToken(string $token)
    {
        try {
            $decoded = $this->decode($token);
            if ($decoded) {
                return $decoded->aud;
            }
        } catch (Throwable $e) {
            throw new BadRequestException('Logout fail: ' . $e->getMessage());
        }
    }

    /**
     * 校验token.
     * @return bool
     */
    public function validateToken(string $token)
    {
        try {
            $decoded = $this->decode($token);
            if ($decoded) {
                return true;
//                $userId = $decoded->aud;
//                $cacheToken = $this->cache->get($this->getCacheKey($userId, $decoded->iss));
//                if ($token == $cacheToken) {
//                    return true;
//                }
            }
        } catch (Throwable $e) {
            return false;
        }
        return false;
    }

    public function decode(string $token)
    {
        $secret = $this->getSecret();
        $alg = $this->getAlg();
        return FirebaseJWT::decode($token, $secret, [$alg]);
    }

    /**
     * 获取token.
     * @param $request
     * @return string
     */
    public function getToken()
    {
        $prefix = 'Bearer';
        $token = '';
        $header = $this->request->getHeaderLine('Authorization');
        $token = ucfirst($header);
        $arr = explode("{$prefix} ", $token);
        return $arr[1] ?? '';
    }

    protected function getCacheKey($key, $iss)
    {
        return $iss . '.jwt.' . $key;
    }
}
