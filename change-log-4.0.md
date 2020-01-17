General:

- Updated to `moneyphp/money` v.3.0
  - `MoneyObject->getCurrency->getName()` should be changed to `MoneyObject->getCurrency->getCode()`
  - `MoneyObject->getAmount()` now returns a `string` instead of a `int`
- Removed autoregistration of custom Doctrine Money type. You should add it by hand,
as described in installation instructions. Checkout [discussion](https://github.com/TheBigBrainsCompany/TbbcMoneyBundle/issues/38#issuecomment-256012838) for details.

Twig filters:

- removed `amount | money_get_currency` filter, use `amount.currency` instead
- removed `currency | currency_name` filter, user `currency.code` instead
