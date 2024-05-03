<?php
/**
 * @package     Helpers package
 *
 * @subpackage  Paths helper functions
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

if (!function_exists('assets_path')) {
    /**
     * assets_path().
     *
     * @param string $path Database path detail
     *
     * @return string
     */
    function assets_path($path = '')
    {
        return app_path("assets/{$path}");
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

if (!function_exists('vendor_path')) {
    /**
     * vendor_path().
     *
     * @param string $path Vendor path detail
     *
     * @return string
     */
    function vendor_path($path = '')
    {
        return base_path("vendor/{$path}");
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

if (!function_exists('classes_from_path')) {
    /**
     * classes_from_path().
     *
     * @param string $path Path detail
     *
     * @return string
     */
    function classes_from_path($path)
    {
        throw_when(!file_exists($path), ["Path {$path} does not exist"]);

        $classes = [];
        $handle = opendir($path);
        while (($file = readdir($handle)) !== false) {
            if ($file != '.' && $file != '..' && is_file($path . DIRECTORY_SEPARATOR . $file)) {
                //get namespace from file
                $namespace = file_get_contents($path . DIRECTORY_SEPARATOR . $file);
                $namespace = explode("\n", $namespace);
                $namespace = array_filter($namespace, function ($line) {
                    return strpos($line, 'namespace') !== false;
                });
                $namespace = array_values($namespace);
                $namespace = str_replace('namespace ', '', $namespace[0]);
                $namespace = str_replace(';', '', $namespace);
                $namespace = str_replace("\r", '', $namespace);
                $namespace = str_replace("\n", '', $namespace);
                $namespace = str_replace("\t", '', $namespace);
                $namespace = str_replace(' ', '', $namespace);

                $class = str_replace('.php', '', $file);
                $classes[] = "{$namespace}\\{$class}";
            }
        }
        closedir($handle);

        return $classes;
    }
}
