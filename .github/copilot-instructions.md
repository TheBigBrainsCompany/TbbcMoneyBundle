# GitHub Copilot Instructions

This is a Symfony bundle integrating the moneyphp/money library for handling monetary values using Fowler's Money pattern.

## Code Standards

- **PHP 8.1+** with strict types (`declare(strict_types=1)`)
- **Symfony 5.4+, 6.x, 7.x** compatibility required
- Use **constructor property promotion** where appropriate
- Type hints are mandatory for all parameters and return types
- Prefer `readonly` properties when values don't change

## What NOT to Review

- **Style issues**: We use ECS (Easy Coding Standard) for code style
- **Type safety**: We use PHPStan at max level with strict rules
- **Code quality**: We use Rector for automated refactoring
- **Unused code, complexity**: Covered by our static analysis tools

## Focus Areas

1. **Money pattern correctness**: Ensure proper handling of Money objects, Currency, and decimal precision
2. **Currency conversion logic**: Verify ratio calculations and conversions are mathematically sound
3. **Doctrine integration**: Check entity mappings and type conversions are correct
4. **Form type integration**: Ensure Symfony form types handle Money objects properly
5. **API contracts**: Verify interfaces are implemented correctly
6. **Business logic**: Focus on domain logic errors that tools can't catch
7. **Security**: Flag any potential security issues (though this is a domain library)
8. **Breaking changes**: Alert on changes that could break backward compatibility

## Domain Knowledge

- Money amounts are stored as integers (e.g., 500 = 5.00 EUR)
- Reference currency is used for all conversions
- Ratios must be strictly positive floats
- Currency codes follow ISO 4217 standard
- Support for Doctrine ORM, MongoDB ODM, and CSV storage
