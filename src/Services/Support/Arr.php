<?php

namespace App\Services\Support;


class Arr
{
    /**
     * @param string[] $attributes
     * @param object $object
     * @return array<string, mixed>
     */
    public static function get(array $attributes, object $object): array
    {
        $array = [];
        foreach ($attributes as $attribute) {
            $methodNameWithGet = 'get' . Str::getPascalCase($attribute);
            $methodName = Str::getCamelCase($attribute);

            $callableWithGet = [$object, $methodNameWithGet];
            $callableWithoutGet = [$object, $methodName];
            if (is_callable($callableWithGet)) {
                $array[$attribute] = call_user_func($callableWithGet);
            } elseif (is_callable($callableWithoutGet)) {
                $array[$attribute] = call_user_func($callableWithoutGet);
            }
        }

        return $array;
    }
}