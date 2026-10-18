# AGENTS.md

Quick reference for AI coding agents working on TOON PHP.

## Commands

- **Run all tests**: `just test` or `vendor/bin/phpunit`
- **Run single test file**: `vendor/bin/phpunit tests/PrimitivesTest.php`
- **Run single test method**: `vendor/bin/phpunit --filter testEncodesStringsWithoutQuotes`
- **Test with coverage**: `just test-coverage`
- **Format code**: `just fix` (Mago formatter)
- **Static analysis**: `just analyse` (Mago analyzer)
- **Dev cycle**: `just dev` (format + test)
- **Pre-PR**: `just pr` (format + analyse + test)

## Architecture

- **Main entry**: `src/Toon.php` - static `encode()` and `decode()` methods
- **Encoding pipeline**: Normalize → Encoders → Primitives → LineWriter
- **Decoding pipeline**: Decoder classes in `src/Decoder/`
- **No runtime dependencies** - only dev deps (PHPUnit, Mago)
- **Spec compliance**: All changes MUST conform to `docs/SPEC.md` (TOON v3.3)

## Code Style

- **PHP 8.1+** with `declare(strict_types=1);` in all files
- **Type safety**: Precise docblock types (`positive-int`, `list<...>`, `@phpstan-assert-if-true`) instead of `assert()`, Mago analyzer clean (no baseline)
- **Namespace**: `HelgeSverre\Toon`
- **Tests go in**: `tests/` only (never create test files elsewhere)
- **Docs go in**: `docs/` only (no root-level reports)
