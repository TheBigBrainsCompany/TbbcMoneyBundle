<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Identical\FlipTypeControlToUseExclusiveTypeRector;
use Rector\Config\RectorConfig;
use Rector\Php53\Rector\Ternary\TernaryToElvisRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;

//
// https://github.com/rectorphp/rector
// https://getrector.com/documentation/integration-to-new-project
// https://getrector.com/blog
// https://getrector.com/blog/5-new-features-in-rector-20
//
// Another good tool from the rector team is installed to be able to use for manual refactors: https://github.com/rectorphp/swiss-knife
//
return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/Tests',
    ])
    ->withPhpSets(php81: true)
    ->withImportNames(importShortClasses: false)
    ->withAttributesSets()
    // https://getrector.com/documentation/set-lists
    ->withPreparedSets(
        deadCode: true,
        //codeQuality: true,
        //codingStyle: true,
        typeDeclarations: true,
        privatization: true,
        //instanceOf: true,
        //strictBooleans: true,
        rectorPreset: true,
        phpunitCodeQuality: true,
        doctrineCodeQuality: true,
        symfonyCodeQuality: true,
        symfonyConfigs: true,
    )
    ->withComposerBased(
        phpunit: true,
    )

    // DTOs looks a bit ugly with this, lets consider if we want this
    ->withSkip([
        ClassPropertyAssignToConstructorPromotionRector::class,
        FlipTypeControlToUseExclusiveTypeRector::class,
        // this is conflicting with our phpstan rules - either they should change or this needs to be skipped
        TernaryToElvisRector::class,
    ]);
