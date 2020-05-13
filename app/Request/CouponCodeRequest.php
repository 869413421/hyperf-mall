<?php

declare(strict_types=1);

namespace App\Request;

use Hyperf\Validation\Request\FormRequest;

class CouponCodeRequest extends FormRequest
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
            case 'POST':
                $rule = [
                    'name' => 'required|string|between:2,50',
                    'type' => 'required|string|in:fixed,percent',
                    'value' => 'required|numeric',
                    'total' => 'required|integer|min:0',
                    'min_amount' => 'required|numeric',
                    'not_before' => 'nullable|date',
                    'not_after' => 'required_with:not_before|after_or_equal:not_before',
                    'enabled' => 'required|boolean'
                ];
                $type = $this->getInputData()['type'];
                if ($type === 'percent')
                {
                    $rule['value'] = 'required|numeric|between:1,99';
                }
                else
                {
                    $rule['value'] = 'required|numeric|min:0.01';
                }
                return $rule;
                break;
            case 'PATCH':
                $rule = [
                    'name' => 'nullable|string|between:2,50',
                    'type' => 'nullable|string|in:fixed,percent',
                    'value' => 'required_with:type|numeric',
                    'total' => 'nullable|integer|min:0',
                    'min_amount' => 'nullable|numeric',
                    'not_before' => 'nullable|date',
                    'not_after' => 'required_with:not_before|after_or_equal:not_before',
                    'enabled' => 'nullable|boolean'
                ];
                array_key_exists('type', $this->getInputData()) ? $type = $this->getInputData()['type'] : null;
                if ($type && $type === 'percent')
                {
                    $rule['value'] = 'required_with:type|numeric|between:1,99';
                }
                else
                {
                    $rule['value'] = 'required_with:type|numeric|min:0.01';
                }
                return $rule;
                break;
        }
    }
}
