<?php
/**
 * @package     Helpers package
 *
 * @subpackage  Application Helper functions
 *
 * @author      Sebastian Costiug <sebastian@overbyte.dev>
 * @copyright   2019-2023 Sebastian Costiug
 * @license     https://opensource.org/licenses/BSD-3-Clause
 *
 * @category    helpers
 *
 * @since       2023.11.14
 */

if (!function_exists('env')) {
    /**
     * env().
     *
     * @param string $key     The environment variable needed
     * @param mixed  $default The default Value for the environment variable needed if it is not set
     *
     * @return mixed
     */
    function env($key, mixed $default = false)
    {
        $value = getenv($key);

        throw_when(!$value && ($default === false), ["{$key} .env variable not set."]);

        return $value ? $value : $default;
    }
}

if (!function_exists('dev_env')) {
    /**
     * dev_env().
     *
     * @return boolean
     */
    function dev_env()
    {
        return in_array(filter_input(INPUT_SERVER, 'HTTP_X_REAL_IP'), explode(' ', env('DEV_IPS', '127.0.0.1'))) || env('APP_DEBUG', 0);
    }
}

if (!function_exists('dd')) {
    /**
     * Dump & die.
     *
     * @param string $content The content to be dumped
     *
     * @return void
     */
    function dd($content)
    {
        array_map(function ($content) {
            echo '<pre>';
            var_dump($content);
            echo '</pre>';
            echo '<hr>';
        }, func_get_args());
        die;
    }
}

if (!function_exists('dwd')) {
    /**
     * Dump without die.
     *
     * @param string $content The content to be dumped
     *
     * @return void
     */
    function dwd($content)
    {
        array_map(function ($content) {
            echo '<pre>';
            var_dump($content);
            echo '</pre>';
            echo '<hr>';
        }, func_get_args());
    }
}

if (!function_exists('throw_when')) {
    /**
     * throw_when.
     *
     * @param boolean      $fails         Fails
     * @param array|string $exceptionInfo An array of [message, errors, code, previous] in this order OR just a string message
     * @param string       $exception     Exception class to be thrown
     *
     * @return void
     *
     * @throws Exception If fails is true
     */
    function throw_when(bool $fails, array|string $exceptionInfo, string $exception = Exception::class)
    {
        if (!$fails) {
            return;
        }

        $exceptionInfo = is_array($exceptionInfo) ? $exceptionInfo : [$exceptionInfo];

        if (!class_exists($exception) && !is_subclass_of($exception, \Exception::class)) {
            $exception = \Exception::class;
            $exceptionInfo = [reset($exceptionInfo)];
        }

        throw new $exception(...$exceptionInfo);
    }
}

if (!function_exists('class_basename')) {
    /**
     * class_basename.
     *
     * @param string $class Class
     *
     * @return string
     */
    function class_basename($class)
    {
        $class = is_object($class) ? get_class($class) : $class;

        return basename(str_replace('\\', '/', $class ?? ''));
    }
}

if (!function_exists('config')) {
    /**
     * Application config.
     *
     * @param  string $path Path
     *
     * @return mixed
     */
    function config($path = null)
    {
        $config = [];
        $folder = scandir(config_path());
        $configFiles = array_slice($folder, 2, count($folder));
        $coreFolder = scandir(core_path('config'));
        $coreConfigFiles = array_slice($coreFolder, 2, count($coreFolder));

        foreach ($coreConfigFiles as $file) {
            throw_when(str_after($file, '.') !== 'php', ['Config files must be .php files.']);

            data_set($config, str_before($file, '.php'), require core_path("config/$file"));
        }

        foreach ($configFiles as $file) {
            throw_when(str_after($file, '.') !== 'php', ['Config files must be .php files.']);

            if (isset($config[str_before($file, '.php')])) {
                $config[str_before($file, '.php')] = array_additive_merge($config[str_before($file, '.php')], require config_path($file));
            } else {
                data_set($config, str_before($file, '.php'), require config_path($file));
            }
        }

        return data_get($config, $path);
    }
}

if (!function_exists('configure')) {
    /**
     * configure.
     *
     * @param object $object Object
     * @param array  $config Config
     *
     * @return object
     */
    function configure(object $object, array $config)
    {
        foreach ($config as $key => $value) {
            $object->{$key} = $value;
        }

        return $object;
    }

}

if (!function_exists('accessible')) {
    /**
     * Determine whether the given value is array accessible.
     *
     * @param  mixed $value Value
     *
     * @return boolean
     */
    function accessible(mixed $value)
    {
        return is_array($value) || $value instanceof ArrayAccess;
    }
}

if (! function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @param  mixed $value   Value
     * @param  mixed ...$args Args
     *
     * @return mixed
     */
    function value(mixed $value, mixed ...$args)
    {
        return $value instanceof Closure ? $value(...$args) : $value;
    }
}

if (! function_exists('flatten_iterable')) {
    /**
     * Return a unidimensional array from an itterble value.
     *
     * @param  mixed $value Value
     *
     * @return array
     */
    function flatten_iterable(mixed $value)
    {
        if (is_iterable($value)) {
            $result = [];
            array_walk_recursive($array, function ($value) use (&$result) {
                $result[] = $value;
            });
            return $result;
        }
        return [$value];
    }
}

if (!function_exists('data_get')) {
    /**
     * Get an item from an array or object using "dot" notation.
     *
     * @param  mixed                     $target  Target
     * @param  string|array|integer|null $key     Key
     * @param  mixed                     $default Default
     *
     * @return mixed
     */
    function data_get(mixed $target, $key, mixed $default = null)
    {
        if (is_null($key)) {
            return $target;
        }

        $key = is_array($key) ? $key : explode('.', $key);

        while (!is_null($segment = array_shift($key))) {
            if ($segment === '*') {
                if (is_iterable($target)) {
                    $target = flatten_iterable($target);
                } elseif (!is_array($target)) {
                    return value($default);
                }

                $result = [];

                foreach ($target as $item) {
                    $result[] = data_get($item, $key);
                }

                return in_array('*', $key) ? array_merge(...$result) : $result;
            }

            if ((is_array($target) || $target instanceof ArrayAccess) && array_key_exists($segment, $target)) {
                $target = $target[$segment];
            } elseif (is_object($target) && isset($target->{$segment})) {
                $target = $target->{$segment};
            } else {
                return value($default);
            }
        }

        return $target;
    }
}

if (!function_exists('data_set')) {
    /**
     * Set an item on an array or object using dot notation.
     *
     * @param  mixed        $target    Target
     * @param  string|array $key       Key
     * @param  mixed        $value     Value
     * @param  boolean      $overwrite Overwrite
     *
     * @return mixed
     */
    function data_set(mixed &$target, $key, mixed $value, $overwrite = true)
    {
        $segments = is_array($key) ? $key : explode('.', $key);

        if (($segment = array_shift($segments)) === '*') {
            if (!(is_array($target) || $target instanceof ArrayAccess)) {
                $target = [];
            }

            if ($segments) {
                foreach ($target as &$inner) {
                    data_set($inner, $segments, $value, $overwrite);
                }
            } elseif ($overwrite) {
                foreach ($target as &$inner) {
                    $inner = $value;
                }
            }
        } elseif (is_array($target) || $target instanceof ArrayAccess) {
            if ($segments) {
                if (!array_key_exists($segment, $target)) {
                    $target[$segment] = [];
                }

                data_set($target[$segment], $segments, $value, $overwrite);
            } elseif ($overwrite || !array_key_exists($segment, $target)) {
                $target[$segment] = $value;
            }
        } elseif (is_object($target)) {
            if ($segments) {
                if (!isset($target->{$segment})) {
                    $target->{$segment} = [];
                }

                data_set($target->{$segment}, $segments, $value, $overwrite);
            } elseif ($overwrite || !isset($target->{$segment})) {
                $target->{$segment} = $value;
            }
        } else {
            $target = [];

            if ($segments) {
                data_set($target[$segment], $segments, $value, $overwrite);
            } elseif ($overwrite) {
                $target[$segment] = $value;
            }
        }

        return $target;
    }
}

if (!function_exists('data_fill')) {
    /**
     * data_fill.
     *
     * @param  mixed        $target Target
     * @param  string|array $key    Key
     * @param  mixed        $value  Value
     *
     * @return string
     */
    function data_fill(mixed &$target, $key, mixed $value)
    {
        return data_set($target, $key, $value, false);
    }
}

if (!function_exists('log_to_file')) {
    /**
     * log_to_file
     *
     * @param string $file        File without full path or extension to serve as log (will be saved in storage/logs folder with .log extension)
     * @param mixed  ...$messages Strings to write to log
     *
     * @return integer|boolean number of bytes that were written or false on error
     */
    function log_to_file($file, mixed ...$messages)
    {
        $file = runtime_path('logs') . DIRECTORY_SEPARATOR . $file . '.log';

        $message = date('Y-m-d H:i:s') . PHP_EOL . implode(PHP_EOL, $messages) . PHP_EOL . '----------' . PHP_EOL . PHP_EOL;

        if (is_file($file) && filesize($file) >= 500000) {
            rename($file, "$file.old");
        }

        return file_put_contents($file, $message, FILE_APPEND);
    }
}

if (!function_exists('call_api')) {
    /**
     * call API
     *
     * @param string $method Method (should be: GET, POST, PUT, DELETE)
     * @param string $url    Base URL
     * @param array  $params URL Query params as [key => value] array
     * @param array  $auth   Auth params as [type => [key => value]] array (Type should be ONE OF: basic, bearer, key or text)
     * @param array  $data   Data as [type => [key => value]] array (Type should be ONE OF: application/json, application/x-www-form-urlencoded, multipart/form-data, application/octet-stream)
     *
     * @return array|false curl response converted to associative array or false on error
     */
    function call_api($method, $url, array $params = [], array $auth = [], array $data = [])
    {
        $curlHandle = curl_init();

        //METHOD
        switch ($method) {
            case 'POST':
                curl_setopt($curlHandle, CURLOPT_POST, 1);
                curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, 'POST');
                break;

            case 'PUT':
                curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, 'PUT');
                break;

            default:
                break;
        }

        // URL PARAMETERS:
        if (!empty($params)) {
            $url = sprintf('%s?%s', $url, \http_build_query($params));
        }

        // HEADERS
        $headers = [];

        // DATA
        if (!empty($data)) {
            foreach ($data as $type => $values) {
                if ($type !== 'resource') {
                    $headers[] = 'Content-Type: ' . $type;
                }
                switch ($type) {
                    case 'resource':
                        $stream = fopen('php://memory', 'r+');
                        fwrite($stream, $values);
                        $dataLength = ftell($stream);
                        rewind($stream);

                        curl_setopt($curlHandle, CURLOPT_INFILE, $stream);
                        curl_setopt($curlHandle, CURLOPT_INFILESIZE, $dataLength);
                        curl_setopt($curlHandle, CURLOPT_UPLOAD, 1);
                        break;

                    case 'application/json':
                        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, json_encode($values));
                        break;

                    case 'application/x-www-form-urlencoded':
                        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, \http_build_query($values));
                        break;

                    default:
                        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $values);
                        break;
                }
            }
        }

        //AUTHENTICATION
        if (!empty($auth)) {
            foreach ($auth as $type => $values) {
                switch ($type) {
                    case 'basic':
                        curl_setopt($curlHandle, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                        curl_setopt($curlHandle, CURLOPT_USERPWD, $values['username'] . ':' . $values['password']);
                        break;

                    case 'bearer':
                        $headers[] = 'Authorization: Bearer ' . $values;
                        break;

                    case 'key':
                        $headers[] = 'X-API-KEY: ' . $values;
                        break;

                    default:
                        break;
                }
            }
        }

        // OPTIONS:
        curl_setopt($curlHandle, CURLOPT_URL, $url);
        curl_setopt($curlHandle, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curlHandle, CURLOPT_USERAGENT, env('APP_NAME', 'Slim 4 Base'));
        curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($curlHandle, CURLOPT_TIMEOUT, 10);

        // CURL DEBUG OPTIONS
        // curl_setopt($curlHandle, CURLOPT_VERBOSE, true);
        // $streamVerboseHandle = fopen('php://temp', 'w+');
        // curl_setopt($curlHandle, CURLOPT_STDERR, $streamVerboseHandle);

        // EXECUTE:
        $count = 0;
        do {
            $result       = curl_exec($curlHandle);
            $responseCode = curl_getinfo($curlHandle, CURLINFO_RESPONSE_CODE);

            // CURL DEBUG OPTIONS
            // if (curl_errno($curlHandle)) {
            //     $errorMessage = curl_error($curlHandle);
            // }

            $count++;
            sleep(1);
        } while (!$result && $count < 3);

        curl_close($curlHandle);

        // CURL DEBUG OPTIONS
        // if (!empty($errorMessage)) {
        //     echo $errorMessage;
        //     die;
        // }
        // if (!$result) {
        //     rewind($streamVerboseHandle);
        //     $verboseLog = stream_get_contents($streamVerboseHandle);
        //     echo "cUrl verbose information:\n",
        //     '<pre>', htmlspecialchars($verboseLog), "</pre>\n";
        //     die;
        // }

        return [
            'response' => (is_json($result) ? json_decode($result, true) : $result),
            'code'     => $responseCode,
        ];
    }

    if (!function_exists('is_api_client')) {
        /**
         * is_api_client
         *
         * @return boolean
         */
        function is_api_client()
        {
            // List of known API clients
            $apiClients = ['Insomnia', 'Thunder Client', 'Postman', 'curl', 'wget', 'httpie', 'Paw', 'SoapUI', 'Restlet', 'Fiddler', 'Charles', 'Advanced REST Client', 'ARC', 'HTTP Toolkit', 'Hoppscotch'];

            // List of known web browsers
            $browsers = ['Mozilla', 'Chrome', 'Safari', 'Opera', 'MSIE', 'Edge', 'Firefox', 'Netscape', 'Konqueror', 'Lynx', 'Links', 'w3m'];

            // Check if the 'Accept' header is set to 'application/json'
            if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
                return true;
            }

            // Check if the 'Content-Type' header is set to 'application/json'
            if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
                return true;
            }

            // Check if a custom 'X-API-Client' header is set
            if (isset($_SERVER['HTTP_X_API_CLIENT'])) {
                return true;
            }

            // Check if a 'api_client' parameter is included in the request URL
            if (isset($_GET['api_client'])) {
                return true;
            }

            // Check if the 'User-Agent' header contains the name of known API clients
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            foreach ($apiClients as $client) {
                if (stripos($userAgent, $client) !== false) {
                    return true;
                }
            }

            // Check if the 'User-Agent' header contains the name of known web browsers
            foreach ($browsers as $browser) {
                if (stripos($userAgent, $browser) !== false) {
                    return false;
                }
            }

            // If none of the above checks passed, assume the request is not from an API client
            return false;
        }
    }

    if (!function_exists('get_client_ip')) {
        /**
         * get_client_ip
         *
         * @return string
         */
        function get_client_ip()
        {
            $ipaddress = '';
            if (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
                $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
            } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
                $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
            } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
                $ipaddress = $_SERVER['HTTP_FORWARDED'];
            } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $ipaddress = $_SERVER['REMOTE_ADDR'];
            } else {
                $ipaddress = 'UNKNOWN';
            }

            return $ipaddress;
        }
    }
}
