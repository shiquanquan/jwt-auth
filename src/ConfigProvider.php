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

class ConfigProvider
{
    public function __invoke(): array
    {
        $configSourcePath = __DIR__ . '/../config/jwt.php';
        $configDestinationPath = BASE_PATH . '/config/autoload/jwt.php';

        return [
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'The config of jwt',
                    'source' => $configSourcePath,
                    'destination' => $configDestinationPath,
                ],
            ],
            'jwt' => file_exists($configDestinationPath) ? [] : require $configSourcePath,
        ];
    }
}
