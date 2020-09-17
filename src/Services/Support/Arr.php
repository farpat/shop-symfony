<?php

namespace App\Services\Support;


class Arr
{
    public static function get(array $attributes, object $object): array
    {
        $array = [];
        foreach ($attributes as $attribute) {
            $methodNameWithGet = 'get' . Str::getPascalCase($attribute);
            $methodName = Str::getCamelCase($attribute);

            if (method_exists($object, $methodNameWithGet)) {
                $array[$attribute] = call_user_func([$object, $methodNameWithGet]);
            } elseif (method_exists($object, $methodName)) {
                $array[$attribute] = call_user_func([$object, $methodName]);
            }
        }

        return $array;
    }
}