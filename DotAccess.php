<?php

namespace dokuwiki\plugin\oauthgeneric;

/**
 * Dot notation access to arrays
 *
 * @see https://stackoverflow.com/a/39118759/172068
 */
class DotAccess
{

    /**
     * Get an item from an array using "dot" notation.
     *
     * @param \ArrayAccess|array $array
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($array, $key, $default = null)
    {
        if (!static::accessible($array)) {
            return $default;
        }
        if (is_null($key)) {
            return $array;
        }
        if (static::exists($array, $key)) {
            return $array[$key];
        }
        if (strpos($key, '.') === false) {
            return $array[$key] ?? $default;
        }
        foreach (explode('.', $key) as $segment) {
            if (static::accessible($array) && static::exists($array, $segment)) {
                $array = $array[$segment];
            } else {
                return $default;
            }
        }
        return $array;
    }

    /**
     * Determine whether the given value is array accessible.
     *
     * @param mixed $value
     * @return bool
     */
    protected static function accessible($value)
    {
        return is_array($value) || $value instanceof \ArrayAccess;
    }

    /**
     * Determine if the given key exists in the provided array.
     *
     * @param \ArrayAccess|array $array
     * @param string|int $key
     * @return bool
     */
    protected static function exists($array, $key)
    {
        if ($array instanceof \ArrayAccess) {
            return $array->offsetExists($key);
        }
        return array_key_exists($key, $array);
    }
}
