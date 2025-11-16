<?php

namespace App\Utility;
use Cake\ORM\Table;


trait ConditionBuilderTrait
{

    /**
     * Builds query conditions for a model, supporting array or scalar inputs.
     *
     * @param string $model The model name (e.g., 'Sections').
     * @param array $fields Array of field names and their values (e.g., ['field1' => $value1, 'field2' => $value2]).
     * @param array $fallbackFields Fields that use a fallback (e.g., ['year_level_id' => [null, 0, '']]). Optional.
     * @return array Conditions array for CakePHP query builder.
     */
    public function buildConditions(string $model, array $fields, array $fallbackFields = []): array
    {
        $conditions = [];
        foreach ($fields as $field => $config) {
            // Handle simple value or config array
            $value = is_array($config) && isset($config['value']) ? $config['value'] : $config;
            $operator = is_array($config) && isset($config['operator']) ? $config['operator'] : (is_array($value) ? 'IN' : '');
            $type = is_array($config) && isset($config['type']) ? $config['type'] : null;

            // Apply type casting if specified
            if ($type === 'integer' && $value !== null && !is_array($value)) {
                $value = is_numeric($value) ? (int)$value : null; // Cast to int or null if non-numeric
            } elseif ($type === 'integer' && is_array($value)) {
                $value = array_map(function ($v) {
                    return is_numeric($v) ? (int)$v : null;
                }, $value);
                $value = array_filter($value); // Remove nulls from array
            }

            $key = "$model.$field" . ($operator ? " $operator" : '');
            $conditions[$key] = (isset($fallbackFields[$field]) && !$value) ? $fallbackFields[$field] : $value;
        }
        return $conditions;
    }
}
