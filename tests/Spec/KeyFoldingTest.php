<?php

declare(strict_types=1);

namespace HelgeSverre\Toon\Tests\Spec;

use HelgeSverre\Toon\EncodeOptions;
use HelgeSverre\Toon\Toon;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Key folding cases that the official fixtures don't cover.
 */
final class KeyFoldingTest extends TestCase
{
    public function test_folding_is_off_by_default(): void
    {
        $this->assertSame("a:\n  b: 1", Toon::encode(['a' => ['b' => 1]]));
    }

    public function test_chains_inside_a_wider_object_are_folded(): void
    {
        $input = ['a' => ['x' => ['y' => 1], 'z' => 2]];

        $this->assertSame("a:\n  x.y: 1\n  z: 2", Toon::encode($input, new EncodeOptions(keyFolding: 'safe')));
    }

    public function test_chain_stops_at_an_object_with_several_keys(): void
    {
        $input = ['a' => ['b' => ['c' => 1, 'd' => 2]]];

        $this->assertSame("a.b:\n  c: 1\n  d: 2", Toon::encode($input, new EncodeOptions(keyFolding: 'safe')));
    }

    public function test_flatten_depth_limits_folding_below_a_folded_key(): void
    {
        $input = ['a' => ['b' => ['c' => 1, 'd' => ['e' => ['f' => 2]]]]];

        $this->assertSame(
            "a.b:\n  c: 1\n  d.e:\n    f: 2",
            Toon::encode($input, new EncodeOptions(keyFolding: 'safe', flattenDepth: 4)),
        );
    }

    public function test_numeric_keys_are_not_folded(): void
    {
        $input = ['a' => ['1' => ['c' => 1]]];

        $this->assertSame("a:\n  \"1\":\n    c: 1", Toon::encode($input, new EncodeOptions(keyFolding: 'safe')));
    }

    public function test_with_methods_keep_folding_options(): void
    {
        $options = (new EncodeOptions(keyFolding: 'safe', flattenDepth: 3))
            ->withIndent(4)
            ->withDelimiter('|');

        $this->assertSame('safe', $options->keyFolding);
        $this->assertSame(3, $options->flattenDepth);
    }

    public function test_rejects_unknown_folding_mode(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new EncodeOptions(keyFolding: 'always');
    }

    public function test_rejects_negative_flatten_depth(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new EncodeOptions(flattenDepth: -1);
    }
}
