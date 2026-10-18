<?php

declare(strict_types=1);

namespace HelgeSverre\Toon\Tests\Spec;

use HelgeSverre\Toon\DecodeOptions;
use HelgeSverre\Toon\EncodeOptions;
use HelgeSverre\Toon\Exceptions\DecodeException;
use HelgeSverre\Toon\Toon;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Path expansion cases that the official fixtures don't cover.
 */
final class PathExpansionTest extends TestCase
{
    public function test_expansion_is_off_by_default(): void
    {
        $this->assertSame(['a.b' => 1], Toon::decode('a.b: 1'));
    }

    public function test_keys_inside_list_items_are_expanded(): void
    {
        $toon = "items[1]:\n  - a.b: 1\n    a.c: 2";

        $this->assertSame(
            ['items' => [['a' => ['b' => 1, 'c' => 2]]]],
            Toon::decode($toon, new DecodeOptions(expandPaths: 'safe')),
        );
    }

    public function test_keys_with_non_identifier_parts_stay_literal(): void
    {
        $this->assertSame(
            ['a.1' => 1, 'a.b-c' => 2],
            Toon::decode("a.1: 1\na.b-c: 2", new DecodeOptions(expandPaths: 'safe')),
        );
    }

    public function test_expanded_key_merges_with_a_nested_object(): void
    {
        $this->assertSame(
            ['a' => ['x' => 1, 'b' => 2]],
            Toon::decode("a:\n  x: 1\na.b: 2", new DecodeOptions(expandPaths: 'safe')),
        );
    }

    public function test_conflict_inside_a_nested_object_is_an_error_in_strict_mode(): void
    {
        $this->expectException(DecodeException::class);
        Toon::decode("a:\n  b: 1\na.b.c: 2", new DecodeOptions(expandPaths: 'safe'));
    }

    public function test_folded_output_round_trips(): void
    {
        $input = ['user' => ['profile' => ['name' => 'Ada', 'tags' => ['x', 'y']]], 'id' => 1];

        $encoded = Toon::encode($input, new EncodeOptions(keyFolding: 'safe'));

        $this->assertSame($input, Toon::decode($encoded, new DecodeOptions(expandPaths: 'safe')));
    }

    public function test_with_methods_keep_expansion_option(): void
    {
        $options = (new DecodeOptions(expandPaths: 'safe'))
            ->withIndent(4)
            ->withStrict(false);

        $this->assertSame('safe', $options->expandPaths);
    }

    public function test_rejects_unknown_expansion_mode(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new DecodeOptions(expandPaths: 'always');
    }
}
