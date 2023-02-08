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
namespace TianMiaos\JWTAuth\Middleware;

use Leonsw\Http\UnauthorizedException;
use Leonsw\HttpServer\Middleware;
use Leonsw\JWTAuth\Exception\TokenValidException;
use Leonsw\JWTAuth\JWT;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class JWTAuthMiddleware extends Middleware
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $jwt = $this->container->get(JWT::class);
        try {
            $token = $jwt->getToken();
            if (! $jwt->validateToken($token)) {
                throw new TokenValidException('Token authentication does not pass', 401);
            }
            $user = $jwt->getUserByToken($token);
            $request = $request->withAttribute('user_id', $user->id)->withAttribute('user', $user);
            return $handler->handle($request);
        } catch (TokenValidException $e) {
            // 防止捕获其他中间件异常
            throw new UnauthorizedException($e->getMessage());
        }
    }
}
