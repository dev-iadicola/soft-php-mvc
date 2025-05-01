<?php
namespace App\Core\Support\Debug;

class VarDumper {

    public static function dd($vars) {
        self::illustration($vars);
        exit;
    }

    public static function dump($vars) {
        self::illustration($vars);
    }

    protected static function illustration($vars) {
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
            white-space: pre-wrap;
        ">';
    
        foreach ($vars as $var) {
            ob_start();
            var_dump($var);
            $dump = ob_get_clean();
    
            // Escapa HTML PRIMA delle regex
            $dump = htmlspecialchars($dump, ENT_QUOTES, 'UTF-8');
    
            // Colora object(ClassName)
            $dump = preg_replace_callback(
                '/object\(&quot;?([^)&]+)&quot;?\)/',
                function ($matches) {
                    return '<span style="color: #ff5555;">object(' . $matches[1] . ')</span>';
                },
                $dump
            );
    
            // Colora array(n)
            $dump = preg_replace_callback(
                '/array\((\d+)\)/',
                function ($matches) {
                    return '<span style="color: #f1fa8c;">array(' . $matches[1] . ')</span>';
                },
                $dump
            );
    
            // Mostra con stili
            echo '<div style="margin-bottom: 15px;">
                <code>' . nl2br($dump) . '</code>
            </div>';
        }
    
        echo '</div>';
    }
    
    
    
}
