<?php

declare(strict_types=1);

namespace PHPModelGenerator\Traits;

trait CompositionEvaluationTrait
{
    /**
     * Collects the names of properties evaluated by a composition branch.
     *
     * Checks each declared property name against $modelData (key-exists), then tests each
     * pattern against the remaining model keys. Returns the list of unique property names
     * that matched, in declaration order followed by pattern-match order.
     *
     * The dedup set is kept separately so PHP's array-key coercion of numeric strings does
     * not corrupt the returned list's value types.
     *
     * @param array    $modelData             The raw model input data.
     * @param string[] $declaredPropertyNames Property names declared in `properties`.
     * @param string[] $patternPatterns       patternProperties regexes (PCRE-ready).
     *
     * @return string[]
     */
    protected function collectEvaluatedProperties(
        array $modelData,
        array $declaredPropertyNames,
        array $patternPatterns,
    ): array {
        $evaluated = [];
        $seen = [];

        foreach ($declaredPropertyNames as $propertyName) {
            if (array_key_exists($propertyName, $modelData)) {
                $evaluated[] = $propertyName;
                $seen[$propertyName] = true;
            }
        }

        foreach ($patternPatterns as $pattern) {
            foreach (array_keys($modelData) as $propertyName) {
                if (!isset($seen[$propertyName]) && preg_match($pattern, (string) $propertyName)) {
                    $evaluated[] = $propertyName;
                    $seen[$propertyName] = true;
                }
            }
        }

        return $evaluated;
    }
}
