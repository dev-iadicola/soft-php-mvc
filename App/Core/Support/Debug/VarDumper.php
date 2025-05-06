<?php
namespace App\Core\Support\Debug;

use App\Core\Design\Stilize;

class VarDumper
{
    public static function __callStatic($method, $args)
    {
        $istance = new self();

        return $istance->$method(...$args);
    }


        private function dd($var)
    {
        self::dump($var);
        exit(0);
    }

    private function dump($var)
    {
       Stilize::get('vardump.css');
        echo '<div class="vardump-container">';
        $this->renderVar($var);
        echo '</div>';
    }

    private function exitInfiniteLoop(): void
    {

        echo "";

    }
    private function renderVar($var, $indent = 0, $countLoop = 0)
    {
        $pad = str_repeat('--', $indent);



        if (is_array($var)) {
            echo "{$pad}<span class='vardump-type'>array</span> (" . count($var) . ") [\n";
            foreach ($var as $key => $value) {
                if ($countLoop >= 3) {
                    $this->exitInfiniteLoop();
                } else {
                   
                    echo $pad . "    <span class='vardump-key'>[{$key}]</span> => ";
                    $this->renderVar($value, $indent + 1, $countLoop + 1);
                }


            }
            echo "{$pad}]\n";
        } elseif (is_object($var)) {
            $class = get_class($var);
            echo "{$pad}<span class='vardump-type'>object</span>({$class}) {\n";
            foreach ((array) $var as $key => $value) {
                if ($countLoop <= 3) {
                    $prop = str_replace("\0", '', $key);
                    echo $pad . "    <span class='vardump-key'>{$prop}</span> => ";
                    $this->renderVar($value, $indent + 1, $countLoop +1);
                } else {
                    $this->exitInfiniteLoop();
                }
            }
            echo "{$pad}}\n";
        } elseif (is_string($var)) {
            echo "<span class='vardump-string'>'" . htmlspecialchars($var) . "'</span>\n";
        } elseif (is_int($var) || is_float($var)) {
            echo "<span class='vardump-number'>{$var}</span>\n";
        } elseif (is_bool($var)) {
            echo $var ? "<span class='vardump-type'>true</span>\n" : "<span class='vardump-type'>false</span>\n";
        } elseif (is_null($var)) {
            echo "<span class='vardump-type'>null</span>\n";
        } else {
            echo "<span class='vardump-type'>unknown type</span>\n";
        }
    }
}
