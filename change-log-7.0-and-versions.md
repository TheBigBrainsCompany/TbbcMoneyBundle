Versions from 7.0
-----------------

### 2025-12-17 : updates for 7.0.0 version

**New features**
- Allow doctrine/doctrine-bundle ^2|^3
- Migrate config from xml to php, support Symfony 8

**BC breaking changes**
- Bumped minimum PHP version to PHP ^8.2
- Bumped minimum Symfony version to Symfony ^6.4
- The old string based service ids are removed, use class based service ids instead injecting `PairManagerInterface` instead of `tbbc_money.pair_manager `, 
  the same goes with any other `tbbc_money.` service id.
