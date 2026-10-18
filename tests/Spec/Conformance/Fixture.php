<?php

declare(strict_types=1);

namespace HelgeSverre\Toon\Tests\Spec\Conformance;

/**
 * One test case from a spec fixture file.
 */
final class Fixture
{
    public function __construct(
        public readonly string $name,
        public readonly mixed $input,
        public readonly mixed $expected,
        public readonly bool $shouldError,
        public readonly int $indent,
        public readonly string $delimiter,
        public readonly bool $strict,
        public readonly string $keyFolding,
        public readonly string $expandPaths,
    ) {}

    public static function fromJson(object $test): self
    {
        /** @var array{name: string, input: mixed, expected?: mixed, shouldError?: bool, options?: object} $fields */
        $fields = get_object_vars($test);
        /** @var array{indent?: int, delimiter?: string, strict?: bool, keyFolding?: string, expandPaths?: string} $options */
        $options = get_object_vars($fields['options'] ?? new \stdClass);

        return new self(
            name: $fields['name'],
            input: $fields['input'],
            expected: $fields['expected'] ?? null,
            shouldError: $fields['shouldError'] ?? false,
            indent: $options['indent'] ?? 2,
            delimiter: $options['delimiter'] ?? ',',
            strict: $options['strict'] ?? true,
            keyFolding: $options['keyFolding'] ?? 'off',
            expandPaths: $options['expandPaths'] ?? 'off',
        );
    }

    /**
     * Key folding and path expansion are not implemented, so fixtures that
     * turn them on are reported as incomplete.
     */
    public function unsupportedReason(): ?string
    {
        if ($this->keyFolding !== 'off') {
            return "keyFolding={$this->keyFolding} is not implemented";
        }
        if ($this->expandPaths !== 'off') {
            return "expandPaths={$this->expandPaths} is not implemented";
        }

        return null;
    }
}
