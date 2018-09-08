<?php
if (!is_file($autoloadFile = __DIR__.'/../vendor/autoload.php')) {
    throw new \LogicException('Could not find autoload.php in vendor/. Did you run "composer install --dev"?');
}
require $autoloadFile;
// we should include AppKernel stub here, because only tests should know about it class and nobody else
require 'app/AppKernel.php';
