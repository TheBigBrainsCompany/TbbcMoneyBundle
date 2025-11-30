Versions from 6.0
-----------------

### 2025-11-10 : updates for 6.1.1 version

**New features**
- Allow doctrine/doctrine-bundle ^2|^3
- Migrate config from xml to php, support Symfony 8

### 2025-11-05 : updates for 6.1.0 version

**Internal Developer things**
- Added docker compose with images for all supported PHP versions for easier development
- Add PHP ECS for style fix/check and Rector for automatic upgrades
- Replaced Psalm with PHPStan
- Rector and ECS automatically runs in CI
- Add support for PHP 8.5 in CI and docker

### 2024-06-19 : updates for 6.0.0 version

**New features**
- Add support for MongoDB
- Add support for doctrine/orm 3 and doctrine/dbal 4

**BC breaking changes**
- Bumped minimum PHP version to PHP ^8.1
- Drop support for moneyphp/money < 4.5 
- Drop `symfony/templating` and template function (use twig)
- Make `PairManagerInterface` readonly in commands

**Internal Developer things**
- Migrate PHPUnit to version 10.
