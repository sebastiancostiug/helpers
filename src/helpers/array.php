<?php
/**
 * @package     Helpers package
 *
 * @subpackage  Array Helper functions
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2023 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    helpers
 *
 * @since       2024.01.07
 */

if (!function_exists('dot')) {
    /**
     * dot().
     *
     * Flatten a multi-dimensional associative array with dots.
     *
     * @param array  $array   The array to flatten
     * @param string $prepend The string to prepend to the keys
     *
     * @return array
     */
    function array_dot(array $array, $prepend = ''): array
    {
        $results = [];

        foreach ($array as $key => $value) {
            if (is_array($value) && !empty($value)) {
                $results = array_merge($results, array_dot($value, $prepend . $key . '.'));
            } else {
                $results[$prepend . $key] = $value;
            }
        }

        return $results;
    }
}

if (!function_exists('array_additive_merge')) {
    /**
     * array_additive_merge().
     *
     * Merge two arrays additively.
     *
     * @param array $array1 The first array
     * @param array $array2 The second array
     *
     * @return array
     */
    function array_additive_merge(array $array1, array $array2): array
    {
        $result = $array1;

        foreach ($array2 as $key => $value) {
            if (is_numeric($key)) {
                $result[] = $value;
            } elseif (is_array($value) && isset($result[$key]) && is_array($result[$key])) {
                $result[$key] = array_additive_merge($result[$key], $value);
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
