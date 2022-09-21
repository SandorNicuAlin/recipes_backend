<?php

namespace App\Services;

class CompareArrays
{
    public static function compare_name_and_quantity(array $array1, array $array2): bool
    {
        $result = true;
        foreach($array1 as $el1) {
            $exist = false;
            foreach($array2 as $el2) {
//                if(($el1['name'] !== $el2['name'] || (int)$el2['quantity'] < (int)$el1['quantity']) || ($el1['name'] === $el2['name'] || (int)$el2['quantity'] < (int)$el1['quantity'])) {
//                    continue;
//                }
                $exist = true;
                break;
            }
            $result = $exist;
        }
        return $result;
    }
}
