<?php

declare(strict_types=1);

namespace App\Request;

use App\Model\OrderItem;
use Hyperf\Validation\Request\FormRequest;
use Hyperf\Validation\Rule;

class HandleRefundRequest extends FormRequest
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
        return [
            'agree' => 'required|integer',
            'reason' => 'required_if:agree,0'
        ];
    }
}
