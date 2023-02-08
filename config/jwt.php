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
return [
    # 非对称加密使用字符串,请使用自己加密的字符串
    'secret' => env('JWT_SECRET', 'leonsw'),

    # token过期时间，单位为秒
    'ttl' => env('JWT_TTL', 86400),

    # jwt的hearder加密算法  目前仅支持对称加密
    'alg' => env('JWT_ALG', 'HS256'),
];
