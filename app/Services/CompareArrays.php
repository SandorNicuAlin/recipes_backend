<?php

namespace App\Services;

class CompareArrays
{
    public static function compare_name_and_quantity(array $array1, array $array2): bool
    {
        foreach($array1 as $el1) {
            $exist = false;
            foreach($array2 as $el2) {
                if($el1['name'] === $el2['name'] && (float)$el2['quantity'] >= (float)$el1['quantity']) {
                    $exist = true;
                    break;
                }
            }
            if(!$exist) {
                return false;
            }
        }
        return true;
    }
}
