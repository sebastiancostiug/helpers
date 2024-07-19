<?php
/**
 *
 * @package     Common
 *
 * @subpackage  Singleton
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2023 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    common classes
 *
 * @since       2023-10-29
 *
 */

namespace overbyte\common;

/**
 * Implements the Singleton design pattern, allowing only one instance of a class to be created.
 */
trait Singleton
{
    /**
     * Singleton pattern implementation.
     *
     * @var object|null $instance The instance of the class.
     */
    protected static $instance = null;

    /**
     * Singleton constructor.
     *
     * @param array $params The parameters to pass to the constructor.
     *
     * @return mixed
     */
    abstract protected function __construct(array $params = []);

    /**
     * Get the instance of the class.
     *
     * @param array $params The parameters to pass to the constructor.
     *
     * @return object The instance of the class.
     */
    final public static function getInstance(array $params = []): object
    {
        if (static::$instance === null) {
            static::$instance = new static($params);
        }
        return static::$instance;
    }

    /**
     * Clears the instance of the Singleton class.
     *
     * @return void
     */
    protected function clearInstance(): void
    {
        self::$instance = null;
    }

    /**
     * Prevents the Singleton class from being cloned.
     *
     * @return void
     */
    private function __clone(): void
    {
        trigger_error('Class singleton ' . get_class($this) . ' cant be cloned.');
    }

    /**
     * The __wakeup() method is declared final to prevent any child classes from overriding it.
     * It triggers an error message when an attempt is made to serialize the singleton object.
     *
     * @return void
     */
    final public function __wakeup(): void
    {
        trigger_error('Class singleton ' . get_class($this) . ' cant be serialized.');
    }
}
