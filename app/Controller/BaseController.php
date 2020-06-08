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

use Hyperf\Contract\LengthAwarePaginatorInterface;

class BaseController extends AbstractController
{
    public function getPaginateData(LengthAwarePaginatorInterface $paginateData)
    {
        return [
            'list' => $paginateData->items(),
            'currentPage' => $paginateData->currentPage(),
            'lastPage' => $paginateData->lastPage(),
            'total' => $paginateData->total(),
            'pageSize' => $paginateData->perPage()
        ];
    }

    public function getPageSize()
    {
        return $this->request->input('pageSize') == null ? 10 : (int)$this->request->input('pageSize');
    }
}
