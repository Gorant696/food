<?php

namespace App\Meta;

/*
	Class responsible for validating parameters from request
*/

class ValidateRequest
{
    static function index()
    {
        return [
            'tags' => 'sometimes|array',
            'tags.*' => 'integer|exists:tags,id',
            'category_id' => 'sometimes|integer|exists:categories,id',
            'language_id' => 'required|integer|exists:languages,id',
            'per_page' => 'sometimes|integer',
            'page' => 'sometimes|integer',
            'with' => 'sometimes|array|',
            'with.*' => 'in:tags,ingredients,categories',
            "diff_time" => 'sometimes|date'
        ];
    }
}

