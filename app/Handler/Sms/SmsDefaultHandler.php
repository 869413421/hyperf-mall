<?php
/**
 * Created by PhpStorm.
 * User: 简美
 * Date: 2020/4/2
 * Time: 13:43
 */

namespace App\Handler\Sms;

use App\Exception\ServiceException;
use Hyperf\Guzzle\ClientFactory;
use Hyperf\Utils\ApplicationContext;

class SmsDefaultHandler implements SmsInterface
{
    private $clientFactory;

    private $client;

    private $host = "http://dingxin.market.alicloudapi.com";

    private $path = "/dx/sendSms";

    private $appCode;

    private $tmpId;

    public function __construct(array $option)
    {

        $this->appCode = $option['appCode'];
        $this->tmpId = $option['tmpId'];

        $this->clientFactory = ApplicationContext::getContainer()->get(ClientFactory::class);
        $this->client = $this->clientFactory->create(['headers' => [
            'Authorization' => 'APPCODE ' . $this->appCode
        ]]);

        if (empty($this->appCode))
        {
            throw new \Exception('appCode null');
        }

        if (empty($this->tmpId))
        {
            throw new \Exception('tmpId null');
        }
    }

    public function send($phone, $params = [])
    {
        $paramsStr = '';
        foreach ($params as $k => $v)
        {
            $paramsStr .= $k . ':' . $v . ',';
        }
        $paramsStr = trim($paramsStr, ',');
        $queryParams = "mobile={$phone}&param={$paramsStr}&tpl_id={$this->tmpId}";

        $url = $this->host . $this->path . '?' . $queryParams;
        try
        {
            $responseContent = $this->client->request('POST', $url)->getBody()->getContents();
            $responseArray = json_decode($responseContent);
            if ($responseArray->return_code != 0000)
            {
                throw new ServiceException(0, '发送失败:' . $responseContent);
            }
        }
        catch (\Exception $exception)
        {
            throw new \Exception(0, $exception->getMessage());
        }

    }
}