<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

final class Upload
{

    public function __construct(private string $directory)
    {
    }

    public function upload(UploadedFile $file)
    {

        $fileName = md5(uniqid()) . '.' . $file->guessExtension();

        $file->move(
            $this->directory,
            $fileName
        );

        return $fileName;
    }

    public function remove(string $fileName): bool
    {
        $filePath = $this->directory . '/' . $fileName;

        if (file_exists($filePath)) {
            return unlink($filePath);
        }

        return false;
    }
}
