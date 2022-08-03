<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;

class FormValidation
{
    public static function validate($request, array $rules): \Illuminate\Contracts\Validation\Validator
    {
        $input = $request->only(...array_keys($rules));
        return Validator::make($input, $rules);
    }
}
