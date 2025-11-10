<?php 
namespace App\Core\Contract;
/**
 * Interface DriveInterface 
 * defines a standard contract for all storage drivers. 
 * Each Eriver (local, S3, FTP, etc.)
 * Pattern: strategy
 */
interface DriveInterface {
    /**
     * Summary of write
     * Write data to the give path.
     * @return bool check if the file be saved.
     */
    public function write(string $path, string $content, array $optionFileSystem):bool;

    /**
     * Summary of read
     * @param string $path the path to read the content of the file
     * @return ?string return content file.
     */
    public function read(string $path): ?string;

    public function exists(string $path): bool;

    /**
     * set file visibility
     */
    public function setVisibility(string $path, string $visibility):bool;

    public function path(string $path):string;
    public function delete(string $path): bool;

}