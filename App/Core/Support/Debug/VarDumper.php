<?php

declare(strict_types=1);

namespace App\Core\Support\Debug;

use App\Core\Helpers\Path;
use App\Utils\Enviroment;
use ReflectionClass;
use Symfony\Component\VarDumper\VarDumper as ComponentVarDumper;

class VarDumper
{
    /**
     * Summary of __callStatic
     * It allows we to have a facade
     *
     * @param  mixed  $method
     * @param  mixed  $args
     */
    public static function __callStatic($method, $args)
    {
        $istance = new self();

        return $istance->$method(...$args);
    }

    /**
     * * serve SOLO per trovare metodo debug() o VarDumper::debug()
     */
    private function debugTrace(): void
    {
        // Ottieni l'intero stack trace
        $traces = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 7);
        $fileWhereDebug = '';
        $lineWhereDebug = 0;
        // origini da escludere.
        $excludedFiles = [
            Path::root('/App/Core/Support/Debug/VarDumper.php'),
            Path::root('/utils/helpers.php'),
            Path::root('/utils/var_dumper.php'),
            Path::root('/App/Core/Support/Debug/VarDumper.php'),
            Path::root('App/Core/Support/Debug/VarDumper.php'),
        ];
        foreach ($traces as $trace) {
            // file che non deve controllare.

            // Salta i file esclusi dal trace
            if (in_array(needle: $trace['file'], haystack: $excludedFiles, strict: true)) {
                continue;
            }

            // carico linee files
            $lines = @file($trace['file']);
            if ( ! $lines) {
                continue;
            }

            foreach ($lines as $num => $content) {
                // trovato
                // * rimuovi spazi e tab
                $trimmed = trim($content);
                // * Salta se è una riga vuota o commentata
                if (
                    $trimmed === '' ||
                    str_starts_with($trimmed, '//') ||
                    str_starts_with($trimmed, '#') ||
                    str_starts_with($trimmed, '/*') ||
                    str_contains($trimmed, '/*') && str_contains($trimmed, '*/')
                ) {
                    continue;
                }
                // * trova il metodo!
                if (str_contains($content, 'debug(') || str_contains($content, 'VarDumper::debug(')) {
                    $fileWhereDebug = $trace['file'];
                    $lineWhereDebug = ($num + 1);

                    // appena trova il file, esce da entrambi i cicli
                    break 2;
                }
            }
        }

        // * Mostra a vidoe

        echo '========================================= <br>';

        echo 'Debug delcared in: ' . str_replace(baseRoot(), '', $fileWhereDebug) . ' at line ' . $lineWhereDebug . ' <br>';

        echo '========================================= <br>';
    }

    private function softdb(...$vars): void
    {
        ob_start();

        echo '<pre style="background:#111;color:#0f0;padding:10px;border-radius:6px;font-size:13px;line-height:1.4; font-size:16px;">';
        $this->debugTrace();
        $this->renderVar($vars);
        echo '</pre>';

        $output = ob_get_clean();
        echo $output;
    }

    private function debug(...$vars): void
    {

        ob_start();

        echo '<pre style="background:#111;color:#0f0;padding:10px;border-radius:6px;font-size:13px;line-height:1.4; font-size:16px;">';
        $this->debugTrace();
        $this->renderVar($vars);
        echo '</pre>';

        $output = ob_get_clean();
        echo $output;

        exit;
    }

    private function logger(...$vars): void
    {

        // ob_start();

        // echo '<pre style="background:#111;color:#0f0;padding:10px;border-radius:6px;font-size:13px;line-height:1.4; font-size:16px;">';
        // $this->debugTrace();
        // $this->renderVar($vars);
        // echo '</pre>';

        // $output = ob_get_clean();
        // echo $output;
    }

    /**
     * @return never
     */
    private function dd(mixed $var = 'DD'): void
    {

        if ( ! Enviroment::isDebug()) {
            return;
        }
        self::dump($var);
        exit(0);
    }

    /**
     * Allows you to see the data you need, without interruption
     *
     * @param  mixed  $var
     */
    private function dump(...$vars): void
    {
        if ( ! Enviroment::isDebug()) {
            return;
        }
        foreach ($vars as $var) {
            ComponentVarDumper::dump($var);
        }
    }

    /**
     * Summary of renderVar
     * The skeleton, but also the musculature of the dumper
     *
     * @param  mixed  $var
     * @param  mixed  $indent
     * @param  mixed  $countLoop
     * @return void
     */
    private function renderVar($var, $indent = 0, $countLoop = 0): void
    {
        $maxDepth = 13;
        $pad = str_repeat('&nbsp;', $indent); // HTML-friendly indent
        if ($countLoop > $maxDepth) {
            echo "<span class='vardump-limit'>[...]</span><br>";

            return;
        }

        if (is_array($var)) {
            echo "<span class='vardump-type'>[array]</span>(" . count($var) . ') [<br>';
            foreach ($var as $key => $value) {
                echo "{$pad}<span class='vardump-key'>[{$key}]</span> => ";
                $this->renderVar($value, $indent + 1, $countLoop + 1);
            }
            echo "{$pad}]<br>";
        } elseif (is_object($var)) {
            $class = get_class($var);
            echo "<span class='vardump-type'>[object]</span>({$class}){ {$this->getFileName($var)} <br>";

            foreach ((array) $var as $key => $value) {

                $prop = preg_replace('/^\0.+\0/', '', $key);
                echo "{$pad}<span class='vardump-key'>{$prop}</span> => ";
                $this->renderVar($value, $indent + 1, $countLoop + 1);
            }
            echo "{$pad}}<br>";
        } elseif (is_string($var)) {
            echo "<span class='vardump-string'>[string]: '" . htmlspecialchars($var) . "'</span><br>";
        } elseif (is_int($var) || is_float($var)) {
            // if (is_octal($var)) {
            //     $var = decoct($var);
            //     "<span class='vardump-number'>[octal]: {$var}</span><br>";
            // } else
            echo "<span class='vardump-number'>[numeric]: {$var}</span><br>";
        } elseif (is_bool($var)) {
            echo "<span class='vardump-type'>[boolean]: " . ($var ? 'true' : 'false') . '</span><br>';
        } elseif (null === $var) {
            echo "<span class='vardump-type'>null</span><br>";
        } else {
            echo "<span class='vardump-type'>unknown type</span><br>";
        }
    }

    private function getFileName($var): void
    {
        $reflector = new ReflectionClass(get_class($var));
        $fullPath = $reflector->getFileName();
        $baseroot = baseRoot();
        $fileName = str_replace($baseroot, '', $fullPath);
        echo "<span class='vardump-file'>defined in " . $fileName . '</span> <br>';
    }
}
