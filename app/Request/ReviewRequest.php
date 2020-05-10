<?php

declare(strict_types=1);

namespace App\Request;

use App\Model\OrderItem;
use Hyperf\Validation\Request\FormRequest;
use Hyperf\Validation\Rule;

class ReviewRequest extends FormRequest
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
            'reviews' => 'required|array',
            'reviews.*.id' => [
                'required',
                function ($attribute, $value, $fail)
                {
                    $order = OrderItem::query()
                        ->where('id', $value)
                        ->where('order_id', $this->route('order_id'))
                        ->whereHas('order', function ($query)
                        {
                            $query->where('user_id', authUser()->id);
                        })
                        ->first();

                    if (!$order)
                    {
                        $fail('订单不存在');
                        return;
                    }
                },

            ],
            'reviews.*.rating' => 'required|integer|between:1,5',
            'reviews.*.review' => 'required|string'
        ];
    }
}
