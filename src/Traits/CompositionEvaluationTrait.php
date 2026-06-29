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

    /**
     * Computes the list of model-data keys not evaluated by any local applicator or any
     * successful composition branch. Used by the generated unevaluatedProperties validator.
     *
     * Generation-time guarantee: the generator skips emitting any unevaluatedProperties
     * validator when the same schema declares a non-false `additionalProperties`, because that
     * keyword already claims every remaining model key. This method therefore never needs to
     * handle the additionalProperties shortcut at runtime.
     *
     * The composition-branch slots in `$this->_compositionEvaluations` use a typed-value shape:
     *   - null      branch failed; contributes nothing.
     *   - true      branch declared a non-false `additionalProperties` and succeeded; every
     *               remaining model key is treated as evaluated.
     *   - string[]  inline branch succeeded; the listed names are evaluated.
     *   - object    nested-schema instance for a successful branch; queried via
     *               `_getEvaluatedProperties()` (underscore-prefixed and marked `#[Internal]`
     *               on the generated class so it cannot collide with a user-declared
     *               `evaluatedProperties` property's auto-generated getter).
     *
     * @param array    $modelData                Raw model input data.
     * @param string[] $declaredPropertyNames    Names declared in the local `properties`.
     * @param string[] $patternPatterns          Local `patternProperties` regexes (PCRE-ready).
     * @param int[]    $compositionValidatorKeys Indexes into `_compositionEvaluations` to consult.
     *
     * @return string[] Names of keys not evaluated by any local applicator or successful branch.
     */
    protected function collectUnevaluatedKeys(
        array $modelData,
        array $declaredPropertyNames,
        array $patternPatterns,
        array $compositionValidatorKeys,
    ): array {
        $modelKeys = array_keys($modelData);
        $evaluated = [];
        $seen = [];

        foreach ($declaredPropertyNames as $propertyName) {
            if (array_key_exists($propertyName, $modelData)) {
                $evaluated[] = $propertyName;
                $seen[$propertyName] = true;
            }
        }

        // patternProperties annotates per-key only when the key's value-validation against
        // the pattern's subschema succeeded — a key that matched the regex but whose value
        // failed must NOT be credited as evaluated, otherwise an enclosing
        // unevaluatedProperties accumulator would incorrectly skip it. `_patternProperties`
        // is the authoritative record of successfully-validated pattern entries — its
        // rollback discipline in PatternProperties.phptpl removes entries when validation
        // fails. A key that matches a pattern regex but whose stored value differs from the
        // current model value indicates the most recent attempt failed (and was rolled
        // back), so the key is treated as unevaluated.
        foreach ($patternPatterns as $pattern) {
            foreach ($modelKeys as $modelKey) {
                if (isset($seen[$modelKey]) || !preg_match($pattern, (string) $modelKey)) {
                    continue;
                }

                foreach ($this->_patternProperties ?? [] as $patternEntries) {
                    if (
                        array_key_exists($modelKey, $patternEntries)
                        && $patternEntries[$modelKey] === $modelData[$modelKey]
                    ) {
                        $evaluated[] = $modelKey;
                        $seen[$modelKey] = true;
                        break;
                    }
                }
            }
        }

        foreach ($compositionValidatorKeys as $compositionValidatorIndex) {
            foreach ($this->_compositionEvaluations[$compositionValidatorIndex] ?? [] as $slot) {
                if ($slot === null) {
                    continue;
                }
                if ($slot === true) {
                    return [];
                }
                if (is_object($slot)) {
                    foreach ($slot->_getEvaluatedProperties() as $propertyName) {
                        if (!isset($seen[$propertyName])) {
                            $evaluated[] = $propertyName;
                            $seen[$propertyName] = true;
                        }
                    }
                    continue;
                }
                foreach ($slot as $propertyName) {
                    if (!isset($seen[$propertyName])) {
                        $evaluated[] = $propertyName;
                        $seen[$propertyName] = true;
                    }
                }
            }
        }

        return array_values(array_diff($modelKeys, $evaluated));
    }

    /**
     * Computes the list of array indices not evaluated by any local applicator (items,
     * additionalItems, contains) or any successful composition branch. Used by the generated
     * unevaluatedItems validator.
     *
     * Generation-time guarantees from UnevaluatedItemsValidatorFactory skip emission entirely
     * for the three array-side dead-code shapes — `items: false`, `items: {schema}`, and
     * `additionalItems: false` with tuple `items` — so this method never needs to short-circuit
     * those cases at runtime.
     *
     * `$this->_compositionAnnotated[$slotKey]` carries the union of indices claimed by
     * successful branches of one composition validator. The composition template writes the
     * union at end-of-IIFE wholesale: an empty entry means the composition failed or
     * contributed no claims. Each composition validator on the property owns its own slot key
     * (e.g. `tags_0` for the first composition on `tags`); the caller passes the list of slot
     * keys it cares about.
     *
     * `$this->_evaluatedItemIndices[$propertyName]` carries indices contributed by sibling
     * array-side validators (items, additionalItems, contains, and an inner unevaluatedItems
     * validator running within a successful composition branch).
     *
     * @param array    $value             The raw array value being validated.
     * @param string   $propertyName      Name of the array property; used to key into
     *                                    `_evaluatedItemIndices`.
     * @param string[] $compositionSlotKeys Slot keys into `_compositionAnnotated` to consult.
     *
     * @return int[] Zero-based indices of array entries not evaluated by any applicator.
     */
    protected function collectUnevaluatedIndices(
        array $value,
        string $propertyName,
        array $compositionSlotKeys,
    ): array {
        $evaluated = ($this->_evaluatedItemIndices ?? [])[$propertyName] ?? [];

        foreach ($compositionSlotKeys as $slotKey) {
            $evaluated += $this->_compositionAnnotated[$slotKey] ?? [];
        }

        $unevaluated = [];
        foreach (array_keys($value) as $index) {
            if (!isset($evaluated[$index])) {
                $unevaluated[] = $index;
            }
        }

        return $unevaluated;
    }
}
