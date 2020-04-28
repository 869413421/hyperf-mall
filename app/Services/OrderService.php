<?php
/**
 * Created by PhpStorm.
 * User: 简美
 * Date: 2020/4/10
 * Time: 10:32
 */

namespace App\Services;

use App\Exception\ServiceException;
use App\Model\Order\Order;
use App\Model\Product\ProductSku;
use App\Model\User\User;
use App\Model\User\UserAddress;
use Carbon\Carbon;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;

class OrderService
{
    /**
     * @Inject()
     * @var OrderQueueService
     */
    private $orderQueueService;

    public function createOrder(User $user, $orderData): Order
    {
        $order = Db::transaction(function () use ($user, $orderData)
        {
            $address = UserAddress::getFirstById($orderData['address_id']);
            $address->update(['last_used_at' => Carbon::now()]);

            $order = new Order([
                'address' => [
                    'address' => $address->full_address,
                    'zip' => $address->zip,
                    'contact_name' => $address->contact_name,
                    'contact_phone' => $address->contact_phone,
                ],
                'remark' => array_key_exists('remark', $orderData) ? $orderData['remark'] : '',
                'total_amount' => 0
            ]);
            $order->user()->associate($user);
            $order->save();

            $totalAmount = 0;
            $skuIds = [];
            foreach ($orderData['items'] as $data)
            {
                $skuIds[] = $data['sku_id'];
                $productSku = ProductSku::getFirstById($data['sku_id']);

                /**@var $item \App\Model\Order\OrderItem * */
                $item = $order->items()->make([
                    'price' => $productSku->price,
                    'amount' => $data['amount'],
                ]);

                $item->product()->associate($productSku->product_id);
                $item->productSku()->associate($productSku);
                $item->save();

                $totalAmount += $productSku->price * $item['amount'];

                if ($productSku->decreaseStock($data['amount']) <= 0)
                {
                    throw new ServiceException(403, "{$productSku->title},库存不足");
                };
            }

            $order->update(['total_amount' => $totalAmount]);
            $this->orderQueueService->pushCloseOrderJod($order, 500);
            $user->cartItems()->whereIn('product_sku_id', $skuIds)->delete();

            return $order;
        });

        return $order;
    }
}