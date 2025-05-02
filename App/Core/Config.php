<?php

namespace App\Core;

use App\Core\Support\Collection\BuildAppFile;

class Config
{

    public static function file($file)
    {
        /**
         * Summary of loadFile
         * @param string $file //string
         * @return bool
         * 
         * verifica che sia un file
         */
        if (!is_file($file)) {
            return false;
        }
        return include $file;
    }

   
    /**
     * Summary of env
     * @param string $env // il fil .env
     * @return string ritrona il parametro 
     * 
     */
    public static function env($envFile)
    {
    
        $envVars = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($envVars as $envVar) {
            $envVar = trim($envVar);
            // Debug: Output linea corrente
           // echo "Processing: '$envVar'\n"; // decommenta per il debug
            // Ignora le righe che iniziano con # (commenti) o che non contengono un '='
            if ($envVar === '' || $envVar[0] === '#' || strpos($envVar, '=') === false) {
               // echo "Skipped: '$envVar'\n"; //decommenta per il debug
                continue;
            }
            putenv($envVar);
        }
    }

    public static function dir($dir)
    {
        if (!is_dir($dir)) {
            return false;
        }
        $conf = [];
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue; // evitiamo di inserire cartella corrente o cartella parent
            $nomeFile = pathinfo($file, PATHINFO_FILENAME);
            $conf[$nomeFile] = include $dir . '/' . $file;
        }
        return new BuildAppFile($conf);
    }

    protected static function fromFile($files){
        
           
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
