<?php

declare(strict_types=1);

namespace App\Controller\Center;

use App\Controller\BaseController;
use Donjan\Permission\Models\Role;

class RoleController extends BaseController
{
    public function show()
    {
        $data = $this->getPaginateData(Role::query()->orderBy('created_at', 'DESC')->paginate());
        return $this->response->json(responseSuccess(200, '', $data));
    }
}
