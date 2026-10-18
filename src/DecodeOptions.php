<?php

declare(strict_types=1);

namespace HelgeSverre\Toon;

use InvalidArgumentException;

final class DecodeOptions
{
    /**
     * Create new decoding options.
     *
     * @param  int  $indent  Expected number of spaces per indentation level (default: 2, minimum: 1)
     * @param  bool  $strict  Enable strict mode validation (default: true)
     * @param  string  $expandPaths  'safe' turns dotted keys (a.b.c: 1) into nested objects, 'off' keeps them as they are (default: off)
     *
     * @throws InvalidArgumentException If indent is less than 1 or expandPaths is invalid
     */
    public function __construct(
        public readonly int $indent = 2,
        public readonly bool $strict = true,
        public readonly string $expandPaths = Constants::MODE_OFF,
    ) {
        // §12: depth is measured in indentSize-space units; indent 0 makes every
        // line depth 0 and cannot recover nesting, so it is not a valid setting.
        if ($this->indent < 1) {
            throw new InvalidArgumentException('Indent must be a positive integer (at least 1)');
        }

        if (! in_array($this->expandPaths, [Constants::MODE_OFF, Constants::MODE_SAFE], true)) {
            throw new InvalidArgumentException("expandPaths must be 'off' or 'safe'");
        }
    }

    /**
     * Create options with default values.
     *
     * @return self Default options (indent: 2, strict: true)
     */
    public static function default(): self
    {
        return new self;
    }

    /**
     * Create options with lenient validation.
     *
     * Disables strict mode for more forgiving parsing:
     * - Allows count mismatches
     * - Allows irregular indentation (floor division)
     * - Allows blank lines in arrays
     * - Allows tabs in indentation
     * - Allows empty input
     *
     * Ideal for hand-written TOON or exploratory parsing.
     *
     * @return self Lenient options (indent: 2, strict: false)
     */
    public static function lenient(): self
    {
        return new self(
            indent: 2,
            strict: false,
        );
    }

    /**
     * Create a copy with different indentation.
     *
     * @param  int  $indent  Expected number of spaces per indentation level
     * @return self New instance with updated indent
     */
    public function withIndent(int $indent): self
    {
        return new self($indent, $this->strict, $this->expandPaths);
    }

    /**
     * Create a copy with different strict mode setting.
     *
     * @param  bool  $strict  Enable/disable strict mode validation
     * @return self New instance with updated strict setting
     */
    public function withStrict(bool $strict): self
    {
        return new self($this->indent, $strict, $this->expandPaths);
    }
}
