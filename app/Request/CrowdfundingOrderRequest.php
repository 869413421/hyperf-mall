<?php

declare(strict_types=1);

namespace App\Request;

use App\Model\CrowdfundingProduct;
use App\Model\Product;
use App\Model\ProductSku;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Validation\Request\FormRequest;
use Hyperf\Validation\Rule;
use Phper666\JwtAuth\Jwt;

class CrowdfundingOrderRequest extends FormRequest
{
    /**
     * @Inject()
     * @var Jwt
     */
    private $jwt;

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
                    'address_id' => [
                        'required',
                        Rule::exists('user_addresses', 'id')->where('user_id', $this->jwt->getTokenObj()->getClaim('id'))
                    ],
                    'items' => 'required|array',
                    'items.*.sku_id' => [
                        'required',
                        function ($attribute, $value, $fail)
                        {
                            $sku = ProductSku::getFirstById($value);
                            if (!$sku)
                            {
                                $fail('商品不存在');
                                return;
                            }

                            if (!$sku->product->on_sale)
                            {
                                $fail('商品没上架');
                                return;
                            }

                            if ($sku->product->type !== Product::TYPE_CROWDFUNDING)
                            {
                                $fail('商品不支持众筹');
                                return;
                            }

                            if ($sku->product->crowdfunding->status !== CrowdfundingProduct::STATUS_FUNDING)
                            {
                                $fail('众筹已结束');
                                return;
                            }

                            if ($sku->stock === 0)
                            {
                                $fail('没有库存');
                                return;
                            }

                            preg_match('/items\.(\d+)\.sku_id/', $attribute, $m);
                            $index = $m[1];
                            // 根据索引找到用户所提交的购买数量
                            $amount = $this->input('items')[$index]['amount'];
                            if ($amount > 0 && $sku->stock < $amount)
                            {
                                $fail("{$sku->title}库存不足");
                                return;
                            }
                        }
                    ],
                    'items.*.amount' => 'required|integer|min:1',
                    'code' => [
                        'nullable',
                        Rule::exists('coupon_codes')
                    ]
                ];
                break;
        }
    }

}
