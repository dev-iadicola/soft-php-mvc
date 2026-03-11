<?php ob_start() ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .vardump-container {
            color: #f8f8f2;
            font-family: Consolas, Monaco, monospace;
            font-size: 14px;
            overflow-x: auto;
            white-space: pre;
            line-height: 1.4;
            padding: 1rem;
        }

        .vardump-node {
            margin-left: 1rem;
        }

        .vardump-limit {
            color: chocolate;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        }

        .vardump-toggle {
            cursor: pointer;
            color: #0077cc;
            user-select: none;
        }

        .vardump-children {
            margin-left: 1rem;
        }

        .big-font {
            font-size: 22px;
        }

        .vardump-key {
            color: #8be9fd;
        }

        .vardump-type {
            color: #bd93f9;
        }

        .vardump-string {
            color: #f1fa8c;
        }

        .vardump-number {
            color: #ff79c6;
        }

        .vardump-parents {
            color: rgb(255, 115, 0);
            font-size: 13px;
        }

        .vardump-file {
            color: gray;
            font-style: italic;
        }

        .debug-trace {
            background: #2d2d2d;
            color: #f8f8f2;
            font-family: Consolas, Monaco, monospace;
            font-size: 13px;
            margin: 2px;
            padding: 15px;
            border-left: 4px solid #ff79c6;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(255, 121, 198, 0.3);
            margin-top: 6px;
        }

        .backtrace {
            margin-bottom: 10px;
            font-weight: bold;
            color: #ff79c6;
        }

        /* Colors */

        .azure {
            color: #8be9fd;
        }

        .green {
            color: #50fa7b;
        }

        .yellow {
            color: #f1fa8c;
        }

        .indaco {
            color: #bd93f9;
        }
    </style>
</head>

<body>
<h1 class="backtrace-title">🧩 Debug Backtrace</h1>

<?php
// Recupera il backtrace attuale
$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
$trace = array_reverse($trace);

// Mostra ogni step
echo '<div class="debug-trace">';
foreach ($trace as $i => $step) {
    $file = $step['file'] ?? '[internal]';
    $line = $step['line'] ?? 'n/a';
    $function = $step['function'] ?? 'unknown';

    echo "<div>";
    echo "<span class='green'>{$function}()</span> ";
    echo "<span class='yellow'>in</span> ";
    echo "<span class='indaco'>{$file}</span>:";
    echo "<span class='yellow'>{$line}</span><br>";
    echo "</div><br>";
}
echo '</div>';

echo '<div class="vardump-container">';
        
        echo '</div>';

        ?>
</body>

</html>

<?php ob_end_flush(); ?>