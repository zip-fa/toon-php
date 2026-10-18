<?php

declare(strict_types=1);

namespace HelgeSverre\Toon\Tests\Spec;

use HelgeSverre\Toon\Toon;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Regression tests for the first field of a list-item object. Anything nested
 * under that field used to be written at the same depth as the item's other
 * fields, so it got merged into them on decode.
 */
final class ListItemFirstFieldTest extends TestCase
{
    public function test_nested_object_as_first_field_is_indented_below_siblings(): void
    {
        $input = [
            'items' => [
                ['user' => ['id' => 1, 'name' => 'Ada'], 'status' => 'ok'],
                ['user' => ['id' => 2, 'name' => 'Bob'], 'status' => 'no'],
            ],
        ];

        $expected = <<<'TOON'
        items[2]:
          - user:
              id: 1
              name: Ada
            status: ok
          - user:
              id: 2
              name: Bob
            status: no
        TOON;

        $this->assertSame($expected, Toon::encode($input));
        $this->assertSame($input, Toon::decode(Toon::encode($input)));
    }

    #[DataProvider('firstFieldShapes')]
    public function test_first_field_shape(array $input, string $expected, ?array $decoded = null): void
    {
        $encoded = Toon::encode($input);

        $this->assertSame($expected, $encoded);
        $this->assertSame($decoded ?? $input, Toon::decode($encoded));
    }

    /**
     * @return array<string, array{0: array<mixed>, 1: string, 2?: array<mixed>}>
     */
    public static function firstFieldShapes(): array
    {
        return [
            'deeply nested object' => [
                ['items' => [['user' => ['id' => 1, 'profile' => ['city' => 'Oslo']], 'status' => 'ok']]],
                "items[1]:\n  - user:\n      id: 1\n      profile:\n        city: Oslo\n    status: ok",
            ],
            'list array of objects' => [
                ['items' => [['tags' => [['a' => 1], ['b' => 2]], 'status' => 'ok']]],
                "items[1]:\n  - tags[2]:\n      - a: 1\n      - b: 2\n    status: ok",
            ],
            'mixed list array' => [
                ['items' => [['mixed' => [1, ['x' => 1]], 'status' => 'ok']]],
                "items[1]:\n  - mixed[2]:\n      - 1\n      - x: 1\n    status: ok",
            ],
            'array of arrays' => [
                ['items' => [['matrix' => [[1, 2], [3, 4]], 'status' => 'ok']]],
                "items[1]:\n  - matrix[2]:\n      - [2]: 1,2\n      - [2]: 3,4\n    status: ok",
            ],
            'tabular array' => [
                ['items' => [['rows' => [['id' => 1, 'n' => 'a'], ['id' => 2, 'n' => 'b']], 'status' => 'ok']]],
                "items[1]:\n  - rows[2]{id,n}:\n      1,a\n      2,b\n    status: ok",
            ],
            'empty array' => [
                ['items' => [['data' => [], 'status' => 'ok']]],
                "items[1]:\n  - data: []\n    status: ok",
            ],
            'empty object' => [
                ['items' => [['meta' => new \stdClass, 'status' => 'ok']]],
                "items[1]:\n  - meta:\n    status: ok",
                // PHP has no separate empty-object value, so it decodes to []
                ['items' => [['meta' => [], 'status' => 'ok']]],
            ],
        ];
    }
}
