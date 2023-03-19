<?php

declare(strict_types=1);

use Owl\Apps\Api\ApiKernel;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

require_once dirname(__DIR__).'../../bootstrap.php';

if ($_SERVER['APP_DEBUG']) {
    umask(0000);

    Debug::enable();
}

// Support CloudFlare Flexible SSL
if ($_SERVER['HTTP_CF_VISITOR'] ?? false) {
    $_SERVER['HTTP_X_FORWARDED_PROTO'] = \json_decode($_SERVER['HTTP_CF_VISITOR'], true)['scheme'];
}

$kernel = new ApiKernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);