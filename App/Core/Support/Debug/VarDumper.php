<?php
namespace App\Core\Support\Debug;

use App\Core\Design\Stilize;

class VarDumper
{
    /**
     * Summary of __callStatic
     * It allows we to have a facade
     * @param mixed $method
     * @param mixed $args
     */
    public static function __callStatic($method, $args)
    {
        $istance = new self();

        return $istance->$method(...$args);
    }

    /**
     * Summary of dd
     * Dump and Die
     * @param mixed $var
     * @return never
     */
    private function dd($var)
    {
        self::dump($var);
        exit(0);
    }

    /**
     * Summary of dump
     * Allows you to see the data you need, without interruption 
     * @param mixed $var
     * @return void
     */
    private function dump($var)
    {
        Stilize::get('vardump.css');
        echo '<div class="vardump-container">';
        $this->renderVar($var);
        echo '</div>';
    }
    /**
     * Summary of renderVar
     * The skeleton, but also the musculature of the dumper
     * @param mixed $var
     * @param mixed $indent
     * @param mixed $countLoop
     * @return void
     */
    private function renderVar($var, $indent = 0, $countLoop = 0)
    {
        $maxDepth = 10;
        $pad = str_repeat(' ', $indent); // HTML-friendly indent
        if ($countLoop > $maxDepth) {
            echo "<span class='vardump-limit'>[...]</span><br>";
            return;
        }
    
        if (is_array($var)) {
            echo "<span class='vardump-type'>array</span>(" . count($var) . ") [<br>";
            foreach ($var as $key => $value) {
                echo "{$pad}<span class='vardump-key'>[{$key}]</span> => ";
                $this->renderVar($value, $indent + 1, $countLoop + 1);
            }
            echo "{$pad}]<br>";
        } elseif (is_object($var)) {
            $class = get_class($var);
            echo "<span class='vardump-type'>object</span>({$class}) {<br>";
            foreach ((array) $var as $key => $value) {
                $prop = preg_replace('/^\0.+\0/', '', $key);
                echo "{$pad}<span class='vardump-key'>{$prop}</span> => ";
                $this->renderVar($value, $indent + 1, $countLoop + 1);
            }
            echo "{$pad}}<br>";
        } elseif (is_string($var)) {
            echo "<span class='vardump-string'>'" . htmlspecialchars($var) . "'</span><br>";
        } elseif (is_int($var) || is_float($var)) {
            echo "<span class='vardump-number'>{$var}</span><br>";
        } elseif (is_bool($var)) {
            echo "<span class='vardump-type'>" . ($var ? "true" : "false") . "</span><br>";
        } elseif (is_null($var)) {
            echo "<span class='vardump-type'>null</span><br>";
        } else {
            echo "<span class='vardump-type'>unknown type</span><br>";
        }
    }
}
