<?php
//src/Service/FileUploader.php
namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploaderAnimal
{
    private string $imagesDirectory;

    public function __construct(string $imagesDirectory)
    {
        $this->imagesDirectory = $imagesDirectory;
    }

    public function upload(UploadedFile $file): string
    {
        $newFilename = uniqid() . '.' . $file->guessExtension();

        // Débogage : Affichez le chemin complet où le fichier sera déplacé
        $targetPath = $this->imagesDirectory . '/' . $newFilename;
        dump($targetPath); // Pour voir le chemin dans les logs

        $file->move($this->imagesDirectory, $newFilename);

        return $newFilename;
    }
}
