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
     *               getEvaluatedProperties().
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

        foreach ($patternPatterns as $pattern) {
            foreach ($modelKeys as $modelKey) {
                if (!isset($seen[$modelKey]) && preg_match($pattern, (string) $modelKey)) {
                    $evaluated[] = $modelKey;
                    $seen[$modelKey] = true;
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
                    foreach ($slot->getEvaluatedProperties() as $propertyName) {
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
}
