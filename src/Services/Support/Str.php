<?php

namespace App\Services\Support;


class Str
{
    /**
     * The cache of getSnakeCase-cased words.
     *
     * @var array<string, string>
     */
    protected static $snakeCache = [];

    /**
     * The cache of camel-cased words.
     *
     * @var array<string, string>
     */
    protected static $camelCache = [];

    /**
     * The cache of getPascalCase-cased words.
     *
     * @var array<string, string>
     */
    protected static $pascalCache = [];

    /**
     * @param array{'style': string, 'code': string, 'symbol': string} $currencyParameter
     */
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
     */
    public static function getSnakeCase(string $string, string $delimiter = '_'): string
    {
        $key = $string;

        if (isset(static::$snakeCache[$key][$delimiter])) {
            return static::$snakeCache[$key][$delimiter];
        }

        if (!ctype_lower($string)) {
            $string = (string)preg_replace('/\s+/u', '', ucwords($string));
            $string = mb_strtolower(preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, $string), 'UTF-8');
        }

        static::$snakeCache[$key][$delimiter] = $string;
        return $string;
    }

    /**
     * Determine if a given string ends with a given substring.
     *
     * @param string $haystack
     * @param string|string[] $needles
     * @return bool
     */
    public static function endsWith($haystack, $needles)
    {
        foreach ((array)$needles as $needle) {
            if ($needle !== '' && substr($haystack, -strlen($needle)) === (string)$needle) {
                return true;
            }
        }

        return false;
    }


    /**
     * Determine if a given string starts with a given substring.
     *
     * @param string|string[] $needles
     * @return bool
     */
    public static function startsWith(string $haystack, $needles): bool
    {
        foreach ((array)$needles as $needle) {
            if ((string)$needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0) {
                return true;
            }
        }

        return false;
    }
}