<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace App\Controller;

use App\Constants\ResponseCode;
use App\Facade\Redis;
use App\Utils\ElasticSearch;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Utils\Collection;


class IndexController extends AbstractController
{
    /**
     * @Inject()
     * @var ElasticSearch
     */
    private $es;

    public function index(RequestInterface $request, ResponseInterface $response)
    {
        $user = $this->request->input('user', 'Hyperf');

        return $response->json(responseSuccess(ResponseCode::CREATE_ED, ['user' => $user]));
    }

    public function test()
    {
        $this->es->searchEs();
        $key = $this->request->input('key');
        $value = $this->request->input('value');
        return $this->response->json([$key => $value]);
    }
}
