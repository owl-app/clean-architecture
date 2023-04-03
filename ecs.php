<?php

declare(strict_types=1);

use SlevomatCodingStandard\Sniffs\Commenting\InlineDocCommentDeclarationSniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $config): void {
    $config->import('vendor/sylius-labs/coding-standard/ecs.php');

    $config->parallel();
    $config->paths([
        __DIR__ . '/apps/api/src',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ]);
    $config->skip([
        InlineDocCommentDeclarationSniff::class . '.MissingVariable',
        InlineDocCommentDeclarationSniff::class . '.NoAssignment',
    ]);
};