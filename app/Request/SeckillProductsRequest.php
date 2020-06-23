<?php

declare(strict_types=1);

namespace App\Request;

use Hyperf\Validation\Request\FormRequest;

class SeckillProductsRequest extends FormRequest
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
                ];
                return $rules;
                break;
            case 'POST':
                $rules = [
                    'title' => 'required|string|between:2,50',
                    'description' => 'required|string|between:2,1000',
                    'image' => 'required|url',
                    'on_sale' => 'required|integer|boolean',
                    'long_title' => 'required|string|between:2,1000',
                    'category_id' => 'nullable|exists:categories,id',
                    'price' => 'required|numeric',
                    'start_at' => 'required|date',
                    'end_at' => 'required|date',
                    'items' => 'required|array',
                    'items.*.id' => 'nullable|exists:product_skus,id',
                    'items.*.title' => 'required|string|between:2,50',
                    'items.*.description' => 'required|string|between:2,1000',
                    'items.*.price' => 'required|numeric',
                    'items.*.stock' => 'required|integer|min:0',
                ];
                return $rules;
                break;
            case 'PATCH':
                $rules = [
                    'title' => 'nullable|string|between:2,50',
                    'description' => 'nullable|string|between:2,1000',
                    'image' => 'nullable|url',
                    'on_sale' => 'nullable|integer|boolean',
                    'price' => 'nullable|numeric',
                    'start_at' => 'nullable|date',
                    'end_at' => 'nullable|date',
                    'category_id' => 'nullable|exists:categories,id',
                    'items' => 'nullable|array',
                    'items.*.id' => 'nullable|exists:product_skus,id',
                    'items.*.title' => 'required|string|between:2,50',
                    'items.*.description' => 'required|string|between:2,1000',
                    'items.*.price' => 'required|numeric',
                    'items.*.stock' => 'required|integer|min:0',
                ];
                return $rules;
                break;
            default:
                return [];

        }
    }

    public function messages(): array
    {
        return [

        ];
    }
}
