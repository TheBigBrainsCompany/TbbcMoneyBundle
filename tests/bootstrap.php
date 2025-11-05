<?php

declare(strict_types=1);

use Symfony\Component\ErrorHandler\ErrorHandler;

# PHPUnit 11 needs this, otherwise there is an error 'Test code or tested code did not remove its own exception handlers'
ErrorHandler::register(null, false);
