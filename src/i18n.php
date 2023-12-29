<?php
/**
 * @package     Helpers package
 *
 * @subpackage  <Internationalization Helper functions>
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2023 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    helpers
 *
 * @since       2023.11.14
 */

if (!function_exists('convert_timezone')) {
    /**
     * Convert datetime string from the app timezone in another timezone(by default in UTC).
     *
     * @param string $datetime   Datetime to be converted
     * @param string $requiredTz The timezone to convert to
     *
     * @return string
     */
    function convert_timezone($datetime, $requiredTz = 'UTC')
    {
        $dtObject = new \DateTime($datetime, new \DateTimeZone(env('APP_TIMEZONE', 'UTC')));
        $dtObject->setTimezone(new \DateTimeZone($requiredTz));

        return $dtObject->format('Y-m-d H:i:s');
    }
}
