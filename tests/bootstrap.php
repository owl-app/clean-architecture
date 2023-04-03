<?php

declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;

if (file_exists(dirname(__DIR__) . '/apps/bootstrap.php')) {
    require dirname(__DIR__) . '/apps/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__) . '/.env.test');
}
