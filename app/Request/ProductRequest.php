<?php

declare(strict_types=1);

namespace App\Request;

use Hyperf\Validation\Request\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        switch ($this->getMethod())
        {
            case 'GET':
                $rules = [
                    'search' => 'nullable|string|between:2,50',
                    'order' => 'nullable|string|in:asc,desc',
                    'field' => 'nullable|string|in:sold_count,rating,review_count,price',
                    'type' => 'nullable|string|in:normal,crowdfunding,seckill',
                ];
                return $rules;
                break;
            case 'POST':
                $rules = [
                    'title' => 'required|string|between:2,50',
                    'description' => 'required|string|between:2,1000',
                    'image' => 'required|url',
                    'on_sale' => 'required|integer|boolean',
                    'category_id' => 'nullable|exists:categories,id',
                    'long_title' => 'required|string|between:2,1000',
                    'price' => 'required|numeric',
                    'items' => 'required|array',
                    'items.*.title' => 'required|string|between:2,50',
                    'items.*.description' => 'required|string|between:2,1000',
                    'items.*.price' => 'required|numeric',
                    'items.*.stock' => 'required|integer|min:0',
                    'properties' => 'nullable|array',
                    'properties.*.name' => 'required|string|between:2,10',
                    'properties.*.value' => 'required|string|between:2,20',
                ];
                return $rules;
                break;
            case 'PATCH':
                $rules = [
                    'title' => 'nullable|string|between:2,50',
                    'description' => 'nullable|string|between:2,10000',
                    'image' => 'nullable|url',
                    'on_sale' => 'nullable|integer|boolean',
                    'price' => 'nullable|numeric',
                    'long_title' => 'nullable|string|between:2,1000',
                    'category_id' => 'nullable|exists:categories,id',
                    'items' => 'required|array',
                    'items.*.id' => 'nullable|exists:product_skus,id',
                    'items.*.title' => 'required|string|between:2,50',
                    'items.*.description' => 'required|string|between:2,1000',
                    'items.*.price' => 'required|numeric',
                    'items.*.stock' => 'required|numeric',
                    'properties' => 'nullable|array',
                    'properties.*.name' => 'nullable|string|between:2,10',
                    'properties.*.value' => 'nullable|string|between:2,20',
                ];
                return $rules;
                break;
            case 'DELETE':
                return [
                ];

        }
    }

    public function messages(): array
    {
        return [

        ];
    }
}
