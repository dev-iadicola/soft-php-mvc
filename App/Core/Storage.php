<?php

namespace App\Core;

use App\Core\Exception\StorageException;
use Exception;

class Storage
{
    protected string $root;
    protected array $storagePaths = [];
    protected string $diskPath = '';
    protected string $diskName = '';
    protected array $file = [];
    protected string $targetFile = '';

    protected string $filename='';
    
    public function __construct()
    {
    }
  
    /**
     * Seleziona un "disk" (path) dal config
     */
    public function disk(string $diskName): self
    {
     
        $this->diskPath = mvc()->config->storage[$diskName];
        
        if (!isset($this->diskPath)) {
            throw new StorageException("Disk '{$diskName}' not configured.");
        }
       if(is_dir($this->diskPath) === false){
           mkdir($this->diskPath, 0755, true);
           
        }
        if ( is_dir($this->diskPath) === false) {
            throw new StorageException("path disk of '{$diskName}' not exist: {$this->diskPath}");
        }

        return $this;
    }

    /**
     * Salva un file caricato (array $_FILES) nel disk selezionato
     */
    public function put(array $file): bool
    {
        if (empty($this->diskPath)) {
            throw new StorageException("Disk non selezionato.");
        }

        if (empty($file['tmp_name']) || empty($file['name'])) {
            throw new StorageException("File non valido.");
        }

        $this->file = $file;
        $this->filename = $file['name'];
        $this->targetFile = $this->diskPath . $this->filename;

        if (!move_uploaded_file($file['tmp_name'], $this->targetFile)) {
            throw new StorageException("Errore nel salvataggio del file.");
        }

        return true;
    }

    /**
     * Elimina un file dato il path relativo al root
     */
    public function delete($absolutePath): bool
    {
        $filePath = baseRoot(). $this->diskPath. $absolutePath; 


        if (!file_exists($filePath)){
           throw new StorageException("File '{$filePath}' non trovato");
        }

        return unlink($filePath);
    }

    public function deleteIfFileExist($absolutePaht):bool{
       
        if($this->fileExists($absolutePaht)){
            $filePath = baseRoot(). $this->diskPath. $absolutePaht; 
            return unlink($filePath);
        }
        return false;
    }

    public function fileExists(?string $nameFile = null): bool
    {
        if (is_null($nameFile) || empty($nameFile)) {
           return is_file($this->diskPath. $this->diskName);
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
