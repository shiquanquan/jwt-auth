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

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;
use Leonsw\Http\BadRequestException;
use Leonsw\JWTAuth\JWT;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SigninMiddleware implements MiddlewareInterface
{
    #[Inject]
    protected HttpResponse $response;

    #[Inject]
    protected JWT $jwt;

    protected $service;

    public function __construct($service)
    {
        $this->service = $service;
    }

    /**
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Throwable
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);
        $userId = $this->jwt->getUidByToken($response->token);
        $model = $this->service->find((int) $userId);

        if (! $model) {
            throw new BadRequestException('用户不存在');
        }

        return $response;
    }
}
