<?php

declare(strict_types=1);

namespace App\Request;

use Hyperf\Validation\Request\FormRequest;


class UserAddressesRequest extends FormRequest
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
                return [
                    'province' => 'required|string|max:50|min:2',
                    'city' => 'required|string|max:50|min:2',
                    'district' => 'required|string|max:50|min:2',
                    'address' => 'required|string|max:50|min:5',
                    'zip' => 'required|integer',
                    'contact_name' => 'required|string|max:10|min:2',
                    'contact_phone' => 'required|integer',
                ];
                break;

            case 'PATCH':
                return [
                    'province' => 'nullable|string|max:50|min:2',
                    'city' => 'nullable|string|max:50|min:2',
                    'district' => 'nullable|string|max:50|min:2',
                    'address' => 'nullable|string|max:50|min:5',
                    'zip' => 'nullable|integer',
                    'contact_name' => 'nullable|string|max:10|min:2',
                    'contact_phone' => 'nullable|integer',
                ];
                break;

            case 'DELETE':
                return [];
                break;
        }
    }
}
