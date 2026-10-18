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
    ) {}

    public static function fromJson(object $test): self
    {
        /** @var array{name: string, input: mixed, expected?: mixed, shouldError?: bool, options?: object} $fields */
        $fields = get_object_vars($test);
        /** @var array{indentSize?: int, delimiter?: string, strict?: bool} $options */
        $options = get_object_vars($fields['options'] ?? new \stdClass);

        return new self(
            name: $fields['name'],
            input: $fields['input'],
            expected: $fields['expected'] ?? null,
            shouldError: $fields['shouldError'] ?? false,
            indent: $options['indentSize'] ?? 2,
            delimiter: $options['delimiter'] ?? ',',
            strict: $options['strict'] ?? true,
        );
    }
}
