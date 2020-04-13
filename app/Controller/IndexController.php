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

use App\Constants\ResponesCode;
use App\Constants\ResponseCode;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;


class IndexController extends AbstractController
{
    public function index(RequestInterface $request, ResponseInterface $response)
    {
        $user = $this->request->input('user', 'Hyperf');

        return $response->json(responseSuccess(ResponseCode::CREATE_ED, ['user' => $user]));
    }
}
