<?php
/**
 * @package     Helpers package
 *
 * @subpackage  <Paths helper functions>
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2023 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    helpers
 *
 * @since       2023.11.14
 */

if (!function_exists('base_path')) {
    /**
     * base_path().
     *
     * @param string $path Base path detail
     *
     * @return string
     */
    function base_path($path = '')
    {
        return  dirname(dirname(dirname(dirname(__DIR__)))) . DIRECTORY_SEPARATOR . "{$path}";
    }
}

if (!function_exists('app_path')) {
    /**
     * app_path().
     *
     * @param string $path App path detail
     *
     * @return string
     */
    function app_path($path = '')
    {
        return base_path("app/{$path}");
    }
}

if (!function_exists('core_path')) {
    /**
     * core_path().
     *
     * @param string $path Core path detail
     *
     * @return string
     */
    function core_path($path = '')
    {
        if (file_exists(base_path("vendor/sebastiancostiug/core/src/{$path}"))) {
            return base_path("vendor/sebastiancostiug/core/src/{$path}");
        } else {
            return app_path("{$path}");
        }
    }
}

if (!function_exists('config_path')) {
    /**
     * config_path().
     *
     * @param string $path Config path detail
     *
     * @return string
     */
    function config_path($path = '')
    {
        return app_path("config/{$path}");
    }
}

if (!function_exists('modules_path')) {
    /**
     * modules_path().
     *
     * @param string $path Modules path detail
     *
     * @return string
     */
    function modules_path($path = '')
    {
        return app_path("modules/{$path}");
    }
}

if (!function_exists('migrations_path')) {
    /**
     * migrations_path().
     *
     * @param string $path Modules path detail
     *
     * @return string
     */
    function migrations_path($path = '')
    {
        return app_path("migrations/{$path}");
    }
}

if (!function_exists('views_path')) {
    /**
     * views_path().
     *
     * @param string $path Views path detail
     *
     * @return string
     */
    function views_path($path = '')
    {
        return app_path("views/{$path}");
    }
}

if (!function_exists('storage_path')) {
    /**
     * storage_path().
     *
     * @param string $path Storage path detail
     *
     * @return string
     */
    function storage_path($path = '')
    {
        return base_path("storage/{$path}");
    }
}

if (!function_exists('runtime_path')) {
    /**
     * runtime_path().
     *
     * @param string $path Runtime path detail
     *
     * @return string
     */
    function runtime_path($path = '')
    {
        return base_path("runtime/{$path}");
    }
}

if (!function_exists('logs_path')) {
    /**
     * logs_path().
     *
     * @param string $path Logs path detail
     *
     * @return string
     */
    function logs_path($path = '')
    {
        return runtime_path("logs/{$path}");
    }
}

if (!function_exists('cache_path')) {
    /**
     * cache_path().
     *
     * @param string $path Cache path detail
     *
     * @return string
     */
    function cache_path($path = '')
    {
        return runtime_path("cache/{$path}");
    }
}

if (!function_exists('public_path')) {
    /**
     * public_path().
     *
     * @param string $path Public path detail
     *
     * @return string
     */
    function public_path($path = '')
    {
        return base_path("public_path/{$path}");
    }
}

if (!function_exists('resources_path')) {
    /**
     * resources_path().
     *
     * @param string $path Resources path detail
     *
     * @return string
     */
    function resources_path($path = '')
    {
        return base_path("resources/{$path}");
    }
}

if (!function_exists('routes_path')) {
    /**
     * routes_path().
     *
     * @param string $path Routes path detail
     *
     * @return string
     */
    function routes_path($path = '')
    {
        return app_path("routes/{$path}");
    }
}
