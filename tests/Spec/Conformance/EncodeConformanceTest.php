<?php

declare(strict_types=1);

namespace HelgeSverre\Toon\Tests\Spec\Conformance;

use HelgeSverre\Toon\EncodeOptions;
use HelgeSverre\Toon\Toon;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Runs every official encode fixture against Toon::encode().
 */
final class EncodeConformanceTest extends TestCase
{
    #[DataProvider('fixtures')]
    public function test_encode_fixture(Fixture $fixture): void
    {
        $reason = $fixture->unsupportedReason();
        if ($reason !== null) {
            $this->markTestIncomplete($reason);
        }

        $options = new EncodeOptions(indent: $fixture->indent, delimiter: $fixture->delimiter);

        $this->assertSame($fixture->expected, Toon::encode($fixture->input, $options));
    }

    /**
     * @return array<string, array{0: Fixture}>
     */
    public static function fixtures(): array
    {
        return FixtureLoader::load('encode');
    }
}
