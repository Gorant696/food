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

    static function store()
    {
        return [
            'slug' => 'required|string|regex:/^[a-z0-9_]+$/',
            'food_trans_hr' => 'required|array',
            'food_trans_hr.language_id' => 'required|integer|exists:languages,id',
            'food_trans_hr.title' => 'required|string',
            'food_trans_hr.description' => 'required|string',
            'food_trans_hr.category_id' => 'sometimes|integer|exists:categories,id',
            'food_trans_en' => 'required|array',
            'food_trans_en.language_id' => 'required|integer|exists:languages,id',
            'food_trans_en.title' => 'required|string',
            'food_trans_en.description' => 'required|string',
            'food_trans_en.category_id' => 'sometimes|integer|exists:categories,id',
            'tags' => 'sometimes|array',
            'tags.*' => 'integer|exists:tags,id',
            'ingredients' => 'sometimes|array',
            'ingredients.*' => 'integer|exists:ingredients,id'
        ];
    }

    static function delete()
    {
        return [
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:foods,id'
        ];
    }

    static function restore()
    {
        return [
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:foods,id'
        ];
    }
}

