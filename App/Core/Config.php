<?php

namespace App\Core;

use Exception;
use App\Core\Support\Collection\ConfigCollection;
use RuntimeException;

class Config
{


   
    /**
     * Summary of env
     * @param string $env 
     */
    public static function env($envFile):void
    {
        if(! is_readable($envFile)){
            throw new RuntimeException("Missing .env file: $envFile");
        }

        $fileEnv = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($fileEnv as $line) {
            $line = trim($line);
            // Ingore lines starting with # (comments) or without an '=' sign
            if ($line=== '' || $line[0] === '#' || strpos($line, '=') === false) { 
                continue;
            }
            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value,"\"' ");
            putenv("$key=$value");
        }
    }
    

    public static function dir($dir): ConfigCollection
    {
        if (!is_dir($dir)) {
            throw new RuntimeException("Expected a valid configuration directory: $dir");
        }
        $conf = [];
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue; // Skip current and parent direcotry
            $nameFile = pathinfo($file, PATHINFO_FILENAME);
            $conf[$nameFile] = include $dir . '/' . $file;
           
            if(!is_array($conf[$nameFile])){
                throw new RuntimeException("Required array in file $nameFile.php");
            }
        }
        if(count($conf) === 0 ){
            throw new Exception("No files found in directory: " . $dir);
        }

        // Return a Collection of the files in 'config' dir. 
        //To access the file, simply call Mvc::$mvc->config->$fileName where $filename must be present in the config foler, and return the array of the file.
        return new ConfigCollection($conf);
    }




    public static function updateEnv($envFile, $key, $value)
    {
        $envVars = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $newEnvVars = [];
        $found = false;

        foreach ($envVars as $envVar) {
            $envVar = trim($envVar);
            if ($envVar === '' || $envVar[0] === '#' || strpos($envVar, '=') === false) {
                $newEnvVars[] = $envVar;
                continue;
            }

            list($currentKey, $currentValue) = explode('=', $envVar, 2);
            if ($currentKey === $key) {
                $newEnvVars[] = $key . '=' . $value;
                $found = true;
            } else {
                $newEnvVars[] = $envVar;
            }
        }

        if (!$found) {
            $newEnvVars[] = $key . '=' . $value;
        }

        file_put_contents($envFile, implode(PHP_EOL, $newEnvVars));
    }
}
