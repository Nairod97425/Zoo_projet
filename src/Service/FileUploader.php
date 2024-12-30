<?php
//src/Service/FileUploader.php
namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private string $uploadsDirectory;

    public function __construct(string $uploadsDirectory)
    {
        $this->uploadsDirectory = $uploadsDirectory;
    }

    public function upload(UploadedFile $file): string
    {
        $filename = uniqid() . '.' . $file->guessExtension();
        $file->move($this->uploadsDirectory, $filename);

        return $filename;
    }
}
