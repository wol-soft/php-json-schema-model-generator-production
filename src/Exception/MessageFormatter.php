<?php

declare(strict_types=1);

namespace PHPModelGenerator\Exception;

/**
 * Shared helpers for assembling validation exception messages: rendering a single value for
 * embedding in message text, and the indentation/bullet-list conventions used by every exception
 * that wraps one or more nested exceptions (array items, tuples, additional/pattern properties,
 * composition branches, nested objects, schema dependencies).
 */
class MessageFormatter
{
    /**
     * Render a value for embedding in a validation exception message. Every value handled here
     * (const/enum values, provided input) originates from decoded JSON, so JSON notation is used
     * uniformly for scalars, arrays and objects alike, rather than PHP's var_export/print_r syntax.
     */
    public static function format(mixed $value): string
    {
        $encoded = json_encode($value);

        return $encoded !== false ? $encoded : var_export($value, true);
    }

    /**
     * Render a count together with its correctly pluralized unit, e.g. "1 element" / "2 elements".
     */
    public static function pluralize(int $count, string $singular, ?string $plural = null): string
    {
        return "$count " . ($count === 1 ? $singular : ($plural ?? "{$singular}s"));
    }

    /**
     * Quote each item and join them with $glue. Used both for inline comma-separated lists
     * wrapped in brackets by the caller (expected types, denied additional properties) and for
     * bullet lists (missing dependency attributes) by passing a "\n  - " glue.
     *
     * @param string[] $items
     */
    public static function quotedList(array $items, string $glue = ', '): string
    {
        return join($glue, array_map(fn(string $item): string => "'$item'", $items));
    }

    /**
     * Join sibling exceptions' messages into a "* "-bulleted, indented list: each exception's own
     * message becomes one bullet, with any of its internal newlines shifted one level deeper so a
     * multi-line sibling message nests correctly instead of colliding with the next bullet.
     *
     * Callers own the leading bullet marker for the first entry (it's usually part of a literal
     * header string immediately before this call) — this only joins the entries themselves.
     *
     * @param ValidationException[] $exceptions
     */
    public static function bulletList(array $exceptions): string
    {
        return implode(
            "\n    * ",
            str_replace(
                "\n",
                "\n    ",
                array_map(fn(ValidationException $exception): string => $exception->getMessage(), $exceptions),
            ),
        );
    }

    /**
     * Flatten one arbitrary nested exception's whole message into bullets one level deeper than
     * the caller's own list. Unlike bulletList() (which combines several sibling messages under
     * their own bullets), this takes a single message that may itself already be a multi-bullet
     * list (e.g. wrapping an InvalidItemException) and re-indents every line of it: lines already
     * indented (continuations within a bullet) shift deeper, lines that aren't indented (the
     * message's own top-level bullets, or a single-line message) become bullets at the new level.
     */
    public static function flattenNestedMessage(string $message): string
    {
        return (string) preg_replace(
            "/\n([^\s])/m",
            "\n  - $1",
            (string) preg_replace("/\n\s/m", "\n     ", $message),
        );
    }
}
