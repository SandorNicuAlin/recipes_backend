<?php

namespace App\Services;

class WorkWithArrays
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

    public static function merge_quantities_for_duplicate_name(array $arr): array
    {
        $result_array = [];
        foreach($arr as $element) {
            $exist = false;
            foreach($result_array as $result_element) {
                if ($element['name'] === $result_element['name']) {
                    $exist = true;
                    $result_array[array_search($result_element, $result_array, true)]['quantity'] =
                        (string)(
                            (float)$result_element['quantity'] +
                            (float)$element['quantity']
                        );

                }
            }
            if(!$exist) {
                $result_array[] = $element;
            }
        }
        return $result_array;
    }
}
