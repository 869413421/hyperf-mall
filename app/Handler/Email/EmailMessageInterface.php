<?php
/**
 * Created by PhpStorm.
 * User: 简美
 * Date: 2020/3/31
 * Time: 11:49
 */

namespace App\Handler\Email;


interface EmailMessageInterface
{
    /**
     * 发送邮件
     * @return mixed
     */
    public function send();

    /**
     * 设置邮件标题
     * @param $subject
     * @return mixed
     */
    public function subject($subject);

    /**
     * 设置接收邮件用户
     * @param $address
     * @return mixed
     */
    public function address($address);

    /**
     * 邮件内容
     * @param $body
     * @return mixed
     */
    public function body($body);

    /**
     * 邮件内容
     * @param $altBody
     * @return mixed
     */
    public function altBody($altBody);

    /**
     * 内容是否是html
     * @param $isHtml
     * @return mixed
     */
    public function isHtml($isHtml);
}