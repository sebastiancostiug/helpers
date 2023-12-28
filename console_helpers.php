<?php
/**
 * @package     Slim 4 Base
 *
 * @subpackage  <Application Helper functions>
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2023 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    helpers
 * @see         https://www.slimframework.com/docs/v4/
 *
 * @since       2023.11.14
 */

if (!function_exists('execute')) {
    /**
     * execute()
     * Execute a command and return the output as an array or string depending on the format parameter
     *
     * @param string  $command     Command
     * @param string  $format      Format
     * @param boolean $prependCode Prepend the exit code to the output
     *
     * @return array|string
     */
    function execute(string $command, string $format = 'string', $prependCode = false) : array|string
    {
        $output = [];

        exec($command . ' 2>&1', $output, $code);

        if ($prependCode) {
            array_unshift($output, $code);
        }

        if ($format === 'array') {
            return $output;
        }

        return implode(PHP_EOL, $output);
    }
}
