<?php

namespace App\Services\Support;


use App\Entity\ModuleParameter;

class Str
{
    public static function getFormattedPrice (array $currencyParameter, float $price): string
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
}