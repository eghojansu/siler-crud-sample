<?php

namespace App;

use Siler\Container;
use Siler\Http;
use Siler\Twig;
use function Siler\array_get;

/**
 * Record some benchmark
 *
 * @param  string  $id
 * @param  boolean $end
 * @return float|void
 */
function record($id, $end = false)
{
    static $tables = [];

    if ($end) {
        return microtime(true) - $tables[$id];
    }

    $tables[$id] = microtime(true);
}

/**
 * Load INI-style config to Container
 *
 * @param  string $file
 * @return void
 */
function env_load($file)
{
    if (file_exists($file)) {
        Container\set('env', parse_ini_file($file, true));
    }
}

/**
 * Get env value
 *
 * @param  string $key
 * @param  mixed  $default
 * @return mixed
 */
function env($key = null, $default = null)
{
    return array_get(Container\get('env', []), $key, $default);
}

/**
 * Render twig template and stop script
 *
 * @param  string  $template Path without twig extension
 * @param  array   $data
 * @param  integer $status
 * @param  boolean $stop
 * @return void
 */
function render($template, array $data = [], $status = 200, $stop = true)
{
    Http\Response\html(
        Twig\render($template.'.twig', $data),
        $status
    );

    if ($stop) {
        // no more output or matching activity
        die;
    }
}

/**
 * Wrap Siler/Http/Response/redirect and stop script
 *
 * @param  string $path
 */
function redirect($path = null)
{
    Http\Response\redirect($path);

    // no more output or matching activity
    die;
}

/**
 * Make password
 *
 * @param  string  $plain
 * @return string|bool
 */
function password($plain)
{
    return password_hash($plain, PASSWORD_BCRYPT);
}

/**
 * User access helper
 *
 * @param  string|array $key
 * @return void|mixed
 */
function user($key = null)
{
    if (is_array($key)) {
        $_SESSION['user'] = $key + [
            'login' => true,
        ];
    } elseif ('logout' === $key) {
        unset($_SESSION['user']);
    } else {
        return array_get($_SESSION['user'] ?? [], $key);
    }
}

/**
 * Validate input from $source
 *
 * @param  array  $rules
 * @param  string $errorKey
 * @param  string $source
 * @return array
 */
function input(array $rules, $errorKey = 'error', $source = '_POST')
{
    $result = [];
    foreach ($rules as $key => $defs) {
        $defs = explode('|', $defs);
        $var  = $GLOBALS[$source][$key] ?? null;

        foreach ($defs as $def) {
            switch ($def) {
                case 'required':
                    if (empty($var)) {
                        $result[$errorKey][] = "{$key} should not be blank.";
                    }
                    break;
                case 'datetime':
                    try {
                        $o = new \DateTime($var);
                        $date = $o->format('Y-m-d H:i:s');
                    } catch (\Exception $e) {
                        $date = $var;
                        $result[$errorKey][] = "{$key} was not a valid date.";
                    }
                    $var = $date;
                    break;
                default:
                    $var = call_user_func_array($def, [$var]);
                    break;
            }
        }

        $result[$key] = $var;
    }

    return $result;
}

/**
 * Simple slug function
 *
 * @param  string $text
 * @return string
 */
function slug($text)
{
    return strtolower(strtr($text, ' ', '-'));
}
