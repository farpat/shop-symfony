<?php

namespace App\Services\Support;


class Str
{
    /**
     * The cache of getSnakeCase-cased words.
     *
     * @var array
     */
    protected static $snakeCache = [];

    /**
     * The cache of camel-cased words.
     *
     * @var array
     */
    protected static $camelCache = [];

    /**
     * The cache of getPascalCase-cased words.
     *
     * @var array
     */
    protected static $pascalCache = [];

    public static function getFormattedPrice(array $currencyParameter, float $price): string
    {
        switch ($currencyParameter['style']) {
            case 'left':
                $number = number_format($price, 2);
                return $currencyParameter['symbol'] . ' ' . $number;
            case 'right':
                $number = number_format($price, 2, ',', ' ');
                return $number . ' ' . $currencyParameter['symbol'];
            default:
                return (string)$price;
        }
    }

    /**
     * Convert a string to camel case.
     *
     * @param string $string
     *
     * @return string
     */
    public static function getCamelCase(string $string)
    {
        if (isset(static::$camelCache[$string])) {
            return static::$camelCache[$string];
        }

        return static::$camelCache[$string] = lcfirst(static::getPascalCase($string));
    }

    /**
     * Convert a string to pascal case.
     *
     * @param string $string
     *
     * @return string
     */
    public static function getPascalCase(string $string)
    {
        $key = $string;

        if (isset(static::$pascalCache[$key])) {
            return static::$pascalCache[$key];
        }

        $string = ucwords(str_replace(['-', '_'], ' ', $string));

        return static::$pascalCache[$key] = str_replace(' ', '', $string);
    }

    /**
     * Convert a string to snake case.
     *
     * @param string $string
     * @param string $delimiter
     *
     * @return string
     */
    public static function getSnakeCase($string, $delimiter = '_')
    {
        $key = $string;

        if (isset(static::$snakeCache[$key][$delimiter])) {
            return static::$snakeCache[$key][$delimiter];
        }

        if (!ctype_lower($string)) {
            $string = preg_replace('/\s+/u', '', ucwords($string));
            $string = mb_strtolower(preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, $string), 'UTF-8');
        }

        return static::$snakeCache[$key][$delimiter] = $string;
    }

    /**
     * Determine if a given string ends with a given substring.
     *
     * @param string $haystack
     * @param string|string[] $needles
     *
     * @return bool
     */
    public static function endsWith($haystack, $needles)
    {
        foreach ((array)$needles as $needle) {
            if (substr($haystack, -strlen($needle)) === (string)$needle) {
                return true;
            }
        }

        return false;
    }
}