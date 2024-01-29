Versions from 6.0
-----------------

### 2023-10-31 : updates for 6.0.0 version

**New features**

**BC breaking changes**

- Drop `YahooFinanceRatioProvider` ratio provider support
  - Remove `tbbc_money.ratio_provider.yahoo_finance.class` container parameter
  - Remove `tbbc_money.ratio_provider.yahoo_finance` service definition
- Drop `GoogleRatioProvider` ratio provider support
  - Remove `tbbc_money.ratio_provider.google.class` container parameter
  - Remove `tbbc_money.ratio_provider.google` service definition

**Internal Developer things**
