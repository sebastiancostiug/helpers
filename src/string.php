<?php
/**
 * @package     Helpers package
 *
 * @subpackage  String manipulation helper functions
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2023 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    helpers
 *
 * @since       2023.11.14
 */

if (!function_exists('str_after')) {
    /**
     * Return the remainder of a string after the first occurrence of a given value.
     *
     * @param  string $subject Subject
     * @param  string $search  Search
     *
     * @return string
     */
    function str_after($subject, $search)
    {
        return $search === '' ? $subject : array_reverse(explode($search, $subject, 2))[0];
    }
}

if (!function_exists('str_before')) {
    /**
     * Get the portion of a string before the first occurrence of a given value.
     *
     * @param  string $subject Subject
     * @param  string $search  Search
     *
     * @return string
     */
    function str_before($subject, $search)
    {
        if ($search === '') {
            return $subject;
        }

        $result = strstr($subject, (string) $search, true);

        return $result === false ? $subject : $result;
    }
}

if (!function_exists('str_between')) {
    /**
     * Determine if a given string contains a given substring.
     *
     * @param  string $haystack Haystack
     * @param  string $start    Start
     * @param  string $end      End
     *
     * @return string
     */
    function str_between($haystack, $start, $end)
    {
        $haystack = ' ' . $haystack;
        $ini      = strpos($haystack, $start);
        if ($ini == 0) {
            return '';
        }

        $ini += strlen($start);
        $len = strpos($haystack, $end, $ini) - $ini;

        return substr($haystack, $ini, $len);
    }
}

if (!function_exists('str_is')) {
    /**
     * Determine if a given string matches a given pattern.
     *
     * @param  string|iterable<string> $pattern Pattern
     * @param  string                  $value   Value
     *
     * @return boolean
     */
    function str_is($pattern, $value)
    {
        $value = (string) $value;

        if (! is_iterable($pattern)) {
            $pattern = [$pattern];
        }

        foreach ($pattern as $pattern) {
            $pattern = (string) $pattern;

            // If the given value is an exact match we can of course return true right
            // from the beginning. Otherwise, we will translate asterisks and do an
            // actual pattern match against the two strings to see if they match.
            if ($pattern === $value) {
                return true;
            }

            $pattern = preg_quote($pattern, '#');

            // Asterisks are translated into zero-or-more regular expression wildcards
            // to make it convenient to check if the strings starts with the given
            // pattern such as "library/*", making any string check convenient.
            $pattern = str_replace('\*', '.*', $pattern);

            if (preg_match('#^' . $pattern . '\z#u', $value) === 1) {
                return true;
            }
        }

        return false;
    }
}

if (!function_exists('is_json')) {
    /**
     * isJson
     * check if string is valid JSON
     *
     * @param string $string String to check
     *
     * @return boolean
     */
    function is_json($string)
    {
        json_decode($string);

        return (json_last_error() == JSON_ERROR_NONE);
    }
}

if (!function_exists('limit_string')) {
    /**
     * limit_string
     * Truncate a text and return it (for display)
     * Default is 25 chars
     *
     * @param string  $string Text to process
     * @param integer $limit  Number of characters to truncate to
     *
     * @return string
     */
    function limit_string($string, $limit = 25)
    {
        $len = strlen($string);
        if ($len <= $limit) {
            return $string;
        } else {
            return substr($string, 0, $limit - 1) . ' ...';
        }
    }
}

if (!function_exists('replace_diacritics')) {
    /**
     * Replace diacritics.
     *
     * @param string $string String
     * @return string
     */
    function replace_diacritics($string)
    {
        $diacritics     = explode(',', 'Ă,Â,Î,Ş,Ș,Ţ,Ț,ă,â,î,ş,ș,ţ,ț,ä,Ä,ß,ë,Ë,ö,Ö,ü,Ü');
        $non_diacritics = explode(',', 'A,A,I,S,S,T,T,a,a,i,s,s,t,t,ae,AE,ss,ee,EE,oe,OE,ue,UE');

        return str_replace($diacritics, $non_diacritics, $string);
    }
}

if (!function_exists('pluralize')) {
    /**
     * pluralize.
     *
     * @param string $string String
     * @return string
     */
    function pluralize($string)
    {
        $lastLetter = strtolower($string[strlen($string) - 1]);
        switch ($lastLetter) {
            case 'y':
                return substr($string, 0, -1) . 'ies';
            case 's':
                return $string . 'es';
            default:
                return $string . 's';
        }
    }
}
