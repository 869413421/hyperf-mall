<?php

declare(strict_types=1);

namespace App\Controller\Center;

use App\Controller\BaseController;
use App\Model\User\User;

class AdminController extends BaseController
{
    public function show()
    {
        $data = $this->getPaginateData(User::with('roles')->paginate());
        return $this->response->json(responseSuccess(200, '', $data));
    }
}
