<?php

namespace App\Core;

use Exception;

class Storage
{
    protected string $targetDirFormImages;
    public string $targetDir;
    protected string $targetFile = '';
    private array $file = [];

    protected string $root = '';

    // Usa la configurazione passata tramite il costruttore
    public function __construct()
    {
        // Imposta il targetDirFormImages basato sulla configurazione
        $this->targetDirFormImages = mvc()->config->folder->images;
        $this->targetDir = mvc()->config->folder->storage;
        $this->root = mvc()->config->folder->root;

        
    }

    public function setTargetDir($var)
    {
        $var = convertDotToSlash($var);
        $this->targetDir = $var;
    }

    public function storeFile($file)
    {
        $this->file = $file;
        //var_dump($file); 
        $targetPath = $this->targetDirFormImages . basename($file["name"]);

        $isStore = move_uploaded_file($file['tmp_name'], $this->targetDirFormImages . basename($file["name"]));
        if ($isStore) {
            $this->targetFile = $targetPath;
        } else {
            $this->targetFile = '';
        }
      
        return $isStore;
    }

    public function storageImage( array $file)
    {
        $this->file = $file;
        $this->targetFile = $this->targetDirFormImages . basename($file["name"]);
        $imageFileType = strtolower(pathinfo($this->targetFile, PATHINFO_EXTENSION));

        if (!$this->verifyImage()) {
            throw new Exception("The file is not an image");
        }

        return $this->storageImageInUploadFolder();
    }

    public function verifyImage()
    {
        return getimagesize($this->file["tmp_name"]);
    }

    private function storageImageInUploadFolder()
    {
        if (!move_uploaded_file($this->file["tmp_name"], $this->targetFile)) {
        
            throw new Exception("Failed to upload file.");
        }
    }

    public function getPathImg()
    {
        //var_dump($this->targetFile); exit;
        return str_replace( $this->root, '', $this->targetFile);
    }

    public function deleteFile($naemFile)
    {
       
        $filePath = $this->root. $naemFile;
   
        if (file_exists($filePath)) {
            unlink($filePath);
            return true;
        } else {
            echo "File does not exist: " . $filePath;
        }
        return false;
    }
}
