<?php
namespace App\Core\Support\Debug;

class VarDumper {

    public static function style(){
        echo '<div style="
        background: #1e1e1e;
        color: #f8f8f2;
        padding: 20px;
        margin: 20px;
        font-family: Consolas, Monaco, monospace;
        font-size: 14px;
        border-left: 5px solid #50fa7b;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(80, 250, 123, 0.3);
        white-space:1rem;

    ">';
    }
    public static function dd($vars) {
        self::illustration($vars);
        exit(1);
    }

    public static function dump($vars) {
        self::illustration($vars);
    }

    protected static function illustration($vars) {
       
        self::style();

        if(!is_array($vars) && !is_object($vars)){
            var_dump($vars);
        }else{
            foreach ($vars as $key => $var) {
                echo '<div style="margin-bottom: 15px;">';
                echo '<code><span style="color:dodgerblue">' . htmlspecialchars((string)$key) . '</span> => ';
            
                if (is_scalar($var)) {
                    // Se è un tipo semplice (stringa, int, bool, float)
                    echo htmlspecialchars((string)$var);
                } elseif (is_array($var)) {
                    echo '<pre>' . htmlspecialchars(print_r($var, true)) . '</pre>';
                } elseif (is_object($var)) {
                    echo '<pre>' . htmlspecialchars(print_r($var, true)) . '</pre>';
                } else {
                    echo '<em>Tipo non gestito</em>';
                }
            
                echo '</code><br></div>';
            }
            
       
        }
    
        echo '</div>';
    }
    
    
    
}
