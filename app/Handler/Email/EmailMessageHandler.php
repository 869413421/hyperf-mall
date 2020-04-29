<?php
/**
 * Created by PhpStorm.
 * User: 简美
 * Date: 2020/3/31
 * Time: 11:48
 */

namespace App\Handler\Email;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class EmailMessageHandler implements EmailMessageInterface
{
    /**
     * @var PHPMailer
     */
    private $mail;

    protected $chartSet;

    protected $host;

    protected $smtpAuth;

    protected $form;

    protected $userName;

    protected $passWord;

    protected $smtpSecure;

    protected $port;

    protected $subject;

    protected $body;

    protected $altBody;

    protected $address = [];

    protected $isHtml = false;

    public function __construct($option = [])
    {
        $this->initConfig($option);
    }

    private function initConfig($option)
    {
        foreach ($option as $key => $value)
        {
            $this->$key = $value;
        }


        $this->mail = new PHPMailer();
        $this->mail->CharSet = empty($this->chartSet) ? 'UTF-8' : $this->chartSet;
        $this->mail->SMTPDebug = 0;
        $this->mail->isSMTP();
        $this->mail->Host = $this->host;
        $this->mail->SMTPAuth = empty($this->smtpAuth) ? true : $this->smtpAuth;;
        $this->mail->Username = $this->userName;
        $this->mail->Password = $this->passWord;
        $this->mail->SMTPSecure = $this->smtpSecure;
        $this->mail->Port = $this->port;
    }

    public function send()
    {
        try
        {
            $this->mail->setFrom($this->form);

            foreach ($this->address as $address)
            {
                $this->mail->addAddress($address);
            }

            $this->mail->addReplyTo($this->form);
            $this->mail->isHTML($this->isHtml);
            $this->mail->Subject = $this->subJect;
            $this->mail->Body = $this->body;
            if (!empty($this->altBody))
            {
                $this->mail->AltBody = $this->altBody;
            }

            $this->mail->send();

        }
        catch (\Exception $exception)
        {
            throw new Exception($exception->getMessage(), 0);
        }
    }

    public function subject($subJect)
    {
        $this->subJect = $subJect;
        return $this;
    }

    public function address($address)
    {
        $this->address[] = $address;
        return $this;
    }

    public function body($body)
    {
        $this->body = $body;
        return $this;
    }

    public function altBody($altBody)
    {
        $this->altBody = $altBody;
        return $this;
    }

    public function isHtml($isHtml)
    {
        $this->isHtml = $isHtml;
        return $this;
    }
}