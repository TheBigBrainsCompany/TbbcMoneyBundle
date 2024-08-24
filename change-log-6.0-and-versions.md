Versions from 6.0
-----------------

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
