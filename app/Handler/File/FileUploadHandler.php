<?php
/**
 * Created by PhpStorm.
 * User: 简美
 * Date: 2020/4/9
 * Time: 17:39
 */

namespace App\Handler\File;


use App\Exception\ServiceException;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpMessage\Upload\UploadedFile;
use Hyperf\Utils\Filesystem\Filesystem;
use Hyperf\Utils\Str;

class FileUploadHandler
{
    /**
     * @Inject()
     * @var Filesystem
     */
    private $fileSystem;

    public function uploadFile(UploadedFile $file, $saveDir)
    {
        $allowedArr = [
            'jpg',
            'png',
            'mp3',
            'mp4'
        ];

        $ext = $file->getExtension();
        if (!in_array($ext, $allowedArr))
        {
            throw new ServiceException(403, '上传格式不允许');
        }

        $folderPath = $saveDir . '/' . date('Ymd') . '/';
        $filePath = config('storage_path') . $folderPath;

        if (!$this->fileSystem->isDirectory($filePath))
        {
            $this->fileSystem->makeDirectory($filePath, 0755, true);
        }

        $fileName = Str::random(16) . '.' . $ext;
        $filePath = $filePath . $fileName;

        if (!$this->fileSystem->move($file->getRealPath(), $filePath))
        {
            throw new ServiceException(0, '上传失败');
        }

        return config('host') . $folderPath . $fileName;
    }
}