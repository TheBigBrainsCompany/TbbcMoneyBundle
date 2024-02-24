Versions from 5.0
-----------------

### 2022-06-01 : updates for 5.0.0 version

- Added support to `moneyphp/money` v.4.0
- Bumped Symfony version to ^5.4 | ^6.0 
- Bumped PHP version to PHP ^8.0

**BC breaking changes**
  - Dropped support for unsupported symfony versions
  - Dropped support for unsupported PHP versions
  - Dropped support for yahoo finance provider (they don't offer this anymore)
  - Dropped support for Google provider (doesn't exist anymore)

- Drop `YahooFinanceRatioProvider` ratio provider support
    - Remove `tbbc_money.ratio_provider.yahoo_finance.class` container parameter
    - Remove `tbbc_money.ratio_provider.yahoo_finance` service definition
- Drop `GoogleRatioProvider` ratio provider support
    - Remove `tbbc_money.ratio_provider.google.class` container parameter
    - Remove `tbbc_money.ratio_provider.google` service definition

**Internal Developer things**
- Added psalm level 1 (highest level)
- Added php cs fixer
- Added GitHub actions (but also kept travis)
- Changed the paths around, a bit more streamlined
- Redone tests (100% coverage), though most of the tests is actually still the same, just PHP 8 and phpunit 9.5 upgraded