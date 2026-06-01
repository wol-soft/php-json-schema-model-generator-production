<?php

declare(strict_types=1);

namespace PHPModelGenerator\Traits;

trait CompositionEvaluationTrait
{
    /**
     * Collects the names of properties evaluated by a composition branch.
     *
     * Checks each declared property name against $modelData (key-exists), then
     * tests each pattern encoding (base64-encoded regex) against the remaining
     * model keys. Returns a flat list of unique property names that matched.
     *
     * @param array    $modelData            The raw model input data.
     * @param string[] $declaredPropertyNames Property names declared in `properties`.
     * @param string[] $patternEncodings      Base64-encoded patternProperties patterns.
     *
     * @return string[]
     */
    protected function collectEvaluatedProperties(
        array $modelData,
        array $declaredPropertyNames,
        array $patternEncodings,
    ): array {
        $evaluated = [];

        foreach ($declaredPropertyNames as $propertyName) {
            if (array_key_exists($propertyName, $modelData)) {
                $evaluated[] = $propertyName;
            }
        }

        foreach ($patternEncodings as $encoding) {
            foreach (array_keys($modelData) as $propertyName) {
                if (!in_array($propertyName, $evaluated, true)
                    && preg_match(base64_decode($encoding), (string) $propertyName)
                ) {
                    $evaluated[] = $propertyName;
                }
            }
        }

        return $evaluated;
    }
}
