# TOON Specification Compliance

**Library:** toon-php 4.4.0
**Spec Version:** TOON Specification v4.4 (`docs/SPEC.md`)

The library passes every encode and decode fixture from the official
[toon-format/spec](https://github.com/toon-format/spec) repository at tag `v4.4.0`.
The fixtures live in `tests/Fixtures/spec/` and run through
`tests/Spec/Conformance/`. Refresh them with `just update-spec-fixtures TAG=...`.

## Encoder

- UTF-8 output with LF line endings, space indentation and no trailing spaces or newline.
- Arrays of primitives are written inline (`tags[3]: a,b,c`), uniform object arrays as
  tables (`users[2]{id,name}:`), and everything else as list items.
- Object columns that share one shape inside a table become nested field groups
  (`orders[2]{id,customer{name,country}}:`).
- An object with two or more uniform object values is written as a keyed table
  (`users[2:]{name,role}:` followed by `u1: Alice,admin` rows).
- Empty arrays are written as `key: []`, and as `[]` at the root.
- Tab and pipe delimiters appear in every header (`[3|]`, `{a|b}`).
- Strings are quoted only when needed: empty strings, surrounding whitespace, the literals
  `true`, `false` and `null`, anything that looks like a number, structural characters,
  the active delimiter, control characters, or a leading `-` or `#`.
- Numbers use plain decimals for `0` and `1e-6 <= |n| < 1e21`, exponent notation outside
  that range, and the shortest digits that read back as the same float. `-0` becomes `0`,
  and `NAN` and `INF` become `null`.

## Decoder

- A byte-order mark at the start and CR before LF are ignored, and invalid UTF-8 is rejected.
- Lines whose first non-space character is `#` are comments. A `#` later in a line is data.
- Root form is detected from the first line: a keyless header gives a root array or keyed
  object, a lone `[]` gives an empty array, a single scalar line gives a primitive, and
  anything else is an object.
- Array headers are parsed strictly. Malformed headers, such as `[03]`, `[#3]`, a space
  before the bracket, or an empty field name, are syntax errors in both modes.
- Values are split on the active delimiter only, outside quotes.
- Quoted strings accept `\\`, `\"`, `\n`, `\r`, `\t` and `\uXXXX`. Any other escape, a lone
  surrogate, or text after the closing quote is an error.
- Strict mode (the default) also rejects length and row-width mismatches, blank lines inside
  arrays, tabs or uneven indentation, indentation jumps, and duplicate keys.
- With `strict: false` the decoder accepts tabs and uneven indentation, recovers from
  indentation jumps, skips the length checks, and keeps the last value for a duplicate key.

## PHP-specific behaviour

- **Numbers:** integers that fit in a PHP `int` decode to `int`. Larger integers and all
  decimals or exponents decode to `float`, so integers beyond `PHP_INT_MAX` lose precision.
- **Objects:** objects decode to associative arrays. An empty object and an empty array both
  decode to `[]`, and an empty document decodes to `[]`.
- **Keys:** PHP turns numeric string keys such as `"1"` into integers. The encoder writes them
  quoted (`"1": a`), and decoding gives an integer key again. Key order is kept.
- **Encoding PHP values:** `DateTimeInterface` becomes an ISO 8601 string, backed enums
  become their value, pure enums their case name, `JsonSerializable` and `toJSON()` objects
  their serialized value, and other objects their public properties.
