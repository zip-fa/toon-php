<?php

declare(strict_types=1);

namespace HelgeSverre\Toon\Tests\Spec\Conformance;

use HelgeSverre\Toon\DecodeOptions;
use HelgeSverre\Toon\Exceptions\DecodeException;
use HelgeSverre\Toon\Toon;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Runs every official decode fixture against Toon::decode().
 */
final class DecodeConformanceTest extends TestCase
{
    #[DataProvider('fixtures')]
    public function test_decode_fixture(Fixture $fixture): void
    {
        $reason = $fixture->unsupportedReason();
        if ($reason !== null) {
            $this->markTestIncomplete($reason);
        }

        $input = (string) $fixture->input;
        $options = new DecodeOptions(indent: $fixture->indent, strict: $fixture->strict);

        if ($fixture->shouldError) {
            $this->expectException(DecodeException::class);
            Toon::decode($input, $options);

            return;
        }

        $this->assertSame(self::canonicalize($fixture->expected), self::canonicalize(Toon::decode($input, $options)));
    }

    /**
     * @return array<string, array{0: Fixture}>
     */
    public static function fixtures(): array
    {
        return FixtureLoader::load('decode');
    }

    /**
     * Turns fixture objects into arrays and compares numbers by value,
     * so 1000 and 1000.0 count as equal.
     */
    private static function canonicalize(mixed $value): mixed
    {
        if ($value instanceof \stdClass) {
            $value = get_object_vars($value);
        }
        if (is_array($value)) {
            return array_map(self::canonicalize(...), $value);
        }
        if (is_int($value)) {
            return (float) $value;
        }

        return $value;
    }
}
