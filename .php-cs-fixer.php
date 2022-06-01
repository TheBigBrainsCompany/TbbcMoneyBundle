<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests');

$config = new PhpCsFixer\Config();
return $config->setRules([
    '@Symfony' => true,
    'strict_param' => true,
    'array_syntax' => ['syntax' => 'short'],
    '@PHP80Migration:risky' => true,
    'php_unit_construct' => true,
    'php_unit_strict' => true,
])
    ->setRiskyAllowed(true)
    ->setFinder($finder);