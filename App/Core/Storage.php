<?php

namespace App\Core;

use App\Core\Exception\StorageException;
use Exception;
/**
 * this class is @deprecated, use App\Core\Facade\Storage
 * 
 */
class Storage
{
    protected string $root;
    private array $config;
    protected array $storagePaths = [];
    protected string $diskPath = '';
    protected string $diskName = '';
    protected array $file = [];
    private string $targetFile = '';
    protected string $filename = '';

    private int $dirPermission = 0775;
    private int $filePermission = 0644;

    public function __construct(string $disk)
    {
        $this->config = Mvc::$mvc->config->storage;
        $this->disk($disk);
    }
    /** 
     _____________________
     | DIRECTORY PERMISSIONS
     ____________________
     | Set the dir permission by the methods your selected.
     * 
     */

    public function setDirPermissionPublic(): self
    {
        $this->dirPermission = 0777; // al can write in the direcotry 
        return $this;
    }
    public function setDirPremissionGroup(): self
    {
        $this->dirPermission = 0775; // owner and group can write in the directory
        return $this;
    }
    public function setDirPerimssionOwner(): self
    {
        $this->dirPermission = 0700;
        return $this;
    }
    public function setDirPerimssiReadOnly(): self
    {
        $this->dirPermission = 0555;
        return $this;
    }

    /** 
     _____________________
     | FILE PERMISSIONS
     ____________________
     | Set the file permission by the methods your selected.
     * 
     */
    public function setFilePermissionPublic(): self
    {
        $this->filePermission = 0666; // leggibile/scrivibile da tutti
        return $this;
    }

    public function setFilePermissionGroup(): self
    {
        $this->filePermission = 0664; // owner + gruppo scrivono
        return $this;
    }

    public function setFilePermissionOwner(): self
    {
        $this->filePermission = 0600; // solo il proprietario
        return $this;
    }

    public function setFilePermissionReadOnly(): self
    {
        $this->filePermission = 0444; // sola lettura per tutti
        return $this;
    }

    /** 
     _____________________
     | GET PERMISSIONS
     ____________________
     | GET the file  and dir permission by the methods your selected.
     * 
     */
    public function GetPerimissons(): array
    {
        return [
            "dir"   =>  $this->dirPermission,
            "file"  =>  $this->filePermission
        ];
    }


    /** 
     _____________________
     | GET 
     ____________________
     * 
     */

    public function getTargetFile()
    {
        $this->targetFile;
    }

    public function setTargetFile(){
        
    }
    private function resolvePath(string $path): string
    {
        return rtrim($this->diskPath, DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR
            . ltrim($path, DIRECTORY_SEPARATOR);
    }
    /** 
     _____________________
     | CORE LOGIC
     ____________________
     * 
     */

    /**
     * Select a "disk" (path) by the config/storage
     */
    public function disk(string $diskName): self
    {
        if (!array_key_exists($diskName, $this->config)) {
            throw new StorageException("Disk '{$diskName}' not configured in config/storage");
        }
        $this->diskPath = $this->config[$diskName];
        // if the dir not exist, create
        if (is_dir($this->diskPath) === false) {
            mkdir($this->diskPath, $this->dirPermission, true);
        }

        return $this;
    }

    /**
     * save a file uploaded (array $_FILES) into the disk selected
     */
    public function put(array $file): bool
    {
        if (empty($this->diskPath)) {
            throw new StorageException("Disk not selected. use method disk() ");
        }

        if (empty($file['tmp_name']) || empty($file['name'])) {
            throw new StorageException("Invalid file.");
        }

        $this->file = $file;
        $this->filename = basename($file['name']);
        $this->targetFile = rtrim($this->diskPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $this->filename;

        if (!move_uploaded_file($file['tmp_name'], $this->targetFile)) {
            throw new StorageException("Error saving the file.");
        }

        // set the perimssion selected. 
        if (!@chmod($this->targetFile, $this->filePermission)) {
            throw new StorageException("Unable to set permissions $this->filePermission on the file: $this->targetFile");
        }

        return true;
    }

    public function putContent(string $filename, string $content): bool
    {
        if (empty($this->diskPath))
            throw new StorageException("Select a disk with method disk().");

        $this->filename = basename($filename);
        $this->targetFile = rtrim($this->diskPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $this->filename;

        file_put_contents($this->targetFile, $content);


        try {
            @chmod($this->targetFile, $this->filePermission);
        } catch (StorageException $e) {
            throw new StorageException("Error: {$e->getMessage()}\n
            Trace:  {$e->getTraceAsString()}\n
             File: {$e->getFile()}\n
            at line {$e->getLine()}");
        }

        return true;
    }

    /**
     * Elimina un file dato il path relativo al root
     */
    public function delete($pathfile): bool
    {
        $filePath = $this->resolvePath($pathfile);

        if (!file_exists($filePath)) {
            throw new StorageException("File '{$filePath}' not found.");
        }

        return unlink($filePath);
    }

 


    public function deleteIfExist($absolutePaht): bool
    {

        if ($this->fileExists($absolutePaht)) {
            $filePath = baseRoot() . $this->diskPath . $absolutePaht;
            return unlink($filePath);
        }
        return false;
    }

    public function fileExists(?string $nameFile = null): bool
    {
        if (is_null($nameFile) || empty($nameFile)) {
            return is_file($this->diskPath . $this->diskName);
        }
        $filePath = $this->diskPath . $nameFile;

        return is_file($filePath);
    }



    public function getAbsolutePath(): string
    {
        if (empty($this->targetFile)) {
            throw new StorageException("File not found.");
        }
        return $this->targetFile;
    }

    /**
     * Restituisce il path relativo del file salvato rispetto al root
     */
    public function getRelativePath(): string
    {
        $relativePath = str_replace(baseRoot(), '', $this->targetFile);
        return $relativePath;
    }

    /**
     * Verifica che il file caricato sia un'immagine
     */
}
