<?php

declare(strict_types=1);

namespace App\Controller;

use App\Handler\File\FileUploadHandler;
use App\Request\FileRequest;

class FileController extends BaseController
{
    public function uploadAvatar(FileRequest $request, FileUploadHandler $uploadHandler)
    {
        $file = $request->file('avatar');
        $path = $uploadHandler->uploadFile($file, 'avatar');
        if (!$path)
        {
            return $this->response->json(responseError(0, '上传失败'));
        }

        return $this->response->json(responseSuccess(200, '上传成功', [
            'path' => $path
        ]));
    }

}
