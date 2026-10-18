<?php

declare(strict_types=1);

namespace HelgeSverre\Toon\Tests\Spec\Conformance;

use RuntimeException;

/**
 * Loads the official toon-format/spec conformance fixtures stored in
 * tests/Fixtures/spec/{encode,decode}/ (refresh with `just update-spec-fixtures`).
 */
final class FixtureLoader
{
    /**
     * @return array<string, array{0: Fixture}>
     */
    public static function load(string $direction): array
    {
        $files = glob(__DIR__.'/../../Fixtures/spec/'.$direction.'/*.json');
        if ($files === false || $files === []) {
            throw new RuntimeException("No {$direction} fixtures found");
        }
        sort($files);

        $cases = [];
        foreach ($files as $file) {
            // Decode as objects so `{}` and `[]` in the input stay different.
            /** @var object{tests: list<object>} $data */
            $data = json_decode((string) file_get_contents($file), false, 512, JSON_THROW_ON_ERROR);
            foreach ($data->tests as $test) {
                $fixture = Fixture::fromJson($test);
                $cases[basename($file).': '.$fixture->name] = [$fixture];
            }
        }

        return $cases;
    }
}
