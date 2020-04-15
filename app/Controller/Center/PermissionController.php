<?php

declare(strict_types=1);

namespace App\Controller\Center;

use App\Controller\BaseController;
use App\Model\Permission\Permission;

class PermissionController extends BaseController
{
    public function show()
    {
        $data = $this->getPaginateData(Permission::query()->paginate());
        return $this->response->json(responseSuccess(200, '', $data));
    }
}
