<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\StringNotation\SingleQuoteFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

// https://github.com/easy-coding-standard/easy-coding-standard/blob/main/README.md
// https://tomasvotruba.com/blog/2018/06/04/how-to-migrate-from-php-code-sniffer-to-easy-coding-standard/
// https://github.com/easy-coding-standard/easy-coding-standard?tab=readme-ov-file
// https://tomasvotruba.com/blog/zen-config-in-ecs

return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/Tests',
    ])

    // add a single rule
    ->withRules([
        SingleQuoteFixer::class,
    ])

    // add sets - group of rules
    ->withPreparedSets(
        psr12: true,
        arrays: true,
        comments: true,
        docblocks: true,
        spaces: true,
        namespaces: true,
    )
;
