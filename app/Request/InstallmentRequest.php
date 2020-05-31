<?php

declare(strict_types=1);

namespace App\Request;

use Hyperf\Validation\Request\FormRequest;
use Hyperf\Validation\Rule;

class InstallmentRequest extends FormRequest
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
                    'count' => [
                        'required',
                        Rule::in(array_keys(config('installment_fee_rate')))
                    ],
                ];
                break;
            default:
                return [];
                break;
        }
    }

}
