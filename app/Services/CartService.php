<?php
/**
 * Created by PhpStorm.
 * User: 简美
 * Date: 2020/4/10
 * Time: 10:32
 */

namespace App\Services;


use App\Model\CartItem;
use App\Model\User;

class CartService
{
    public function add(User $user, $skuId, $amount)
    {
        $cart = $user->cartItems()->where('product_sku_id', $skuId)->first();

        if ($cart)
        {
            $cart->update(['amount' => $cart->amount + $amount]);
        }
        else
        {
            $cart = new CartItem(['amount' => $amount]);
            $cart->User()->associate($user);
            $cart->ProductSku()->associate($skuId);
            $cart->save();
        }
    }
}