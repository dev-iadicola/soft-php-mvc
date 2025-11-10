<?php

namespace App\Core;

use App\Core\Facade\Storage;
use Throwable;
use \App\Core\Mvc;
use RuntimeException;
use App\Core\Helpers\Log;
use App\Core\Services\CsrfService;


class View
{

    public string $layout = 'default';

    public function __construct(public Mvc $mvc) {}


    public function setLayout(string $layout)
    {
        $this->layout = $layout;
    }
    /**
     * documented function render 
     * 
     *  * Renders a page with layout and placeholders
     * 
     * @param string $page the fle page path in C:*\views\pages\{your_string_point}.php
     * @param array $variables varibale you want use compact($user->email)
     * @param array $message other probably deprecable in future
     * @return string the page render
     */
    public function render(string $page, array $variables = [], array|null $message = []): string
    {


        $page = convertDotToSlash($page);


        $layoutValue = [
            'page' => $page,
            'menu' => $this->mvc->config->menu,
        ];


        // Ricerca layouts e page
        $layoutContent = $this->getViewContent("layouts", $this->layout, $layoutValue);
        $pageContent = $this->getViewContent("pages", $page, $layoutValue, $variables);
        // Ricambia Includes 
        $layoutContent = $this->processContent($layoutContent, $variables); // * $this->processIncludes($layoutContent, $variables);
        $pageContent = $this->processContent($pageContent, $variables); // * $this->processIncludes($pageContent, variables: $variables);

        ini_set('display_errors', 1);
        error_reporting(E_ALL);

        // Sostituzione dei placeholder <<page>> con un file.php
        // $pageContent = $this->renderContent($pageContent, $message);
        return $this->renderContent($layoutContent, [
            'page' => $pageContent,
        ]);
    }
    /**
     * Process content, 
     *  Manages all directive for all placeholders
     * @param string $content the page
     * @param array $variables the viarbiles
     * @return string $content the final page.
     */
    private function processContent(string $content, array $variables): string
    {

        // step 1: seach all @include (recursive)
        // step 2: search all @csrf
        // step 3: search all @delete
        // setp 4 search all placheolder  {{$John}} for convertn<<?= $john >> 
        $content = (new IncludeDirectiveHandler($this))->cleanPlaceHolders($content, $variables);
        return $content;
    }


    /**
     * Manipulation of the file, render a view
     *
     * @param string $view file name of the view witohout exstension
     * @param array $variables value to insert into the view
     * @return string view content.
     */
    private function renderContent(string $content, array|null $message = []): string
    {

        $chiavi = array_keys($message);
        $chiavi = array_map(fn($chiave) => "<<" . $chiave . ">>", $chiavi);

        foreach ($message as $key => $value) {
            if ($value instanceof Component) {
                $message[$key] = $this->renderComponent($value);
            }
        }
        $valori = array_values($message);
        return str_replace($chiavi, $valori, $content);
    }



    private function renderComponent(Component $componente): string
    {
        $nomeComponente = $componente->getName();
        $componentContent = $this->getViewContent("components", $nomeComponente);
        $content = '';
        foreach ($componente->getItems() as $item) {
            $content .= $this->renderContent($componentContent, $item);
        }
        return $content;
    }

    public function getViewContent(string $folder, string $item, array $values = [], array $variables = []): string
    {
        if (!isset($page) && isset($values['page'])) {
            $page = $values['page'];
        }

        extract($values);
        extract($variables);
        // per visualizzare i messaggi di errore e successo
        $views = $this->mvc->config->folder->views;
        // The full path and file with the content 
        // Easet Egg
        $quotes = [
            "it vanished like my weekend plans",
            "Have you tried turning it off and on again?",
            "it rage-quit the project",
            "it was sacrificed to the compiler gods",
            "Perhaps in another timeline, it exists",
            "it went to fetch coffee and never came back",
            "AI said it's fine, so we're doomed",
            "it's not missing, it's just on vacation",
            "unexpected behavior is now expected",
            "it refused to render without coffee",
            "your bug report just became a trilogy",
            "runtime decided the problem it's me, not you"
        ];
        $message = $quotes[array_rand($quotes)];
        $originFile = "$views/$folder/$item.php";
        
        $originFilePath = $originFile;
        if (!file_exists($originFile)) {
            $debugNameFile = str_replace(baseRoot(), '', $file);
            throw new RuntimeException("View file $debugNameFile not found... $message ");
        }
        // Read the file content
        $content = file_get_contents($originFile);
        // compile blade-like syntax to php
        // {{{ var }}} -> unescaped echo
        $content = preg_replace('/\{\{\{\s*(.*?)\s*\}\}\}/s', '<?php echo $1; ?>', $content);
        // {{ var }} -> escaped echo
        $content = preg_replace('/\{\{\s*(.*?)\s*\}\}/s', '<?= htmlspecialchars($1, ENT_QUOTES, "UTF-8") ?>', $content);


        // $previousLevel = error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

        // ob_start();
        // eval('?/>' . $content ?? ' ');
        // $output = ob_get_clean();

        // // Restore original error level
        // error_reporting($previousLevel);

        // return $output;

        // try {
        //     ob_start();
        //     $compiled = explode("\n", $content);
        //     $compiledWithMarkers = '';

        //     foreach ($compiled as $i => $line) {
        //         $compiledWithMarkers .= "// line: " . ($i + 1) . " in " . basename($file) . "\n" . $line . "\n";
        //     }

        //     eval("? >$compiledWithMarkers");

        //     $output = ob_get_clean();
        //     return $output;
        // } catch (\Throwable $e) {
        //     //  Log utile per sapere quale view ha causato l'errore
        //     Log::exception($e);
        //     Log::debug(['view_file' => $file]);
        //     throw new RuntimeException("Error rendering view: {$file}
        //      \nmessage: {$e->getMessage()} \ncode: {$e->getCode()}", 0, $e);
        // }

        // unic name of compiled file
        $compiledFile = 'cache/view/'.md5($originFile) . '.php';
        // * use storage for save temp file 
        Storage::make('private')->put($compiledFile, $content);


        // Esecuzione sicura del file compilato
        try {
            ob_start();
            include Storage::make('private')->path($compiledFile);
            $output = ob_get_clean();
            return $output;
        } catch (Throwable $e) {
            $errorLine = $e->getLine();
            throw new RuntimeException(
                "Error rendering view: {$originFilePath}\n" .
                    "Message: {$e->getMessage()}\n" .
                    "Occurred in: (line {$errorLine})",
                $e->getCode(),
                $e
            );
        }
    }
}

#region DIRECTIVE HANDLER
interface DirectiveHandler
{
    public function handle(string $content, ?array $variables = []): string;
}
abstract class BaseDirectiveHandler
{
    public function __construct(protected View $view) {}
}
class IncludeDirectiveHandler extends BaseDirectiveHandler
{
    public function cleanPlaceHolders(string $content, ?array $variables = []): string
    {

        // * replace placeholder @csrf, @patch and @delete
        $content = $this->processCsrf($content);
        $content = $this->processDelete($content);
        $content = $this->processPatch($content);
        $content = $this->processPut($content);
        // replace in frontend {{ $json }} $john >> in <?= $john ?/> or funcrion 


        // This pattern captures directives such as:
        // @include('partials.header')  
        // @include("partials.header")  
        //
        // Detail Operation:
        // - @include\(        → searhc the "@include(" word
        // - \s*              → Alllow for option spaces afther the parenthesis
        // - [\'"]            → Start with single or double quotation mark (')  (")
        // - ([^\'"]+)        → capture ev.thing that si not a quotation mark, util colsing (the file path) 
        // - [\'"]            → closes the stirng with the same quotation amrks.
        // - \s*\)            → Allow optional spaces before the closing parenthesis (
        // - /                → end og the pattern
        // * Example of use: @include('file') or @include("file"), ignoring the spaces.
        $pattern = '/@include\(\s*[\'"]([^\'"]+)[\'"]\s*\)/';


        // * As long as we find inclusions, we processed them.
        while (preg_match_all($pattern, $content, $matches)) {
            foreach ($matches[1] as $includeContent) {
                $includePath = ltrim(str_replace('.', '/', $includeContent), '/'); // get the content in @include()
                $includeFileContent = $this->view->getViewContent('', $includePath, [], $variables);

                // * If the string include() exist in file, recursive for clear all recirusive
                if ($includeFileContent !== null) {

                    $processedInclude = $this->cleanPlaceHolders($includeFileContent, $variables);
                    $content = str_replace("@include('$includeContent')", $processedInclude, $content);
                } else {
                    echo "File include don't found: $includePath<br>";
                }
            }
        }

        return $content;
    }



    /**
     * Summary of processCsrf
     * @param string $content il contenuto php della pagina.
     * @return string il contenuto coin @csrf viene sostituito con un campo input hidden
     */
    private function processCsrf(string $content): string
    {
        $token = (new CsrfService())->getToken();
        $input = "<input type='hidden' name='_token' value='{$token}'>";
        return str_replace('@csrf', $input, $content);
    }



    /**
     * Summary of processDelete
     * Replace the placeholder @delete with the real html input.
     * @param string $content
     * @return string
     */
    private function processDelete(string $content): string
    {
        $inputDelete = "<input type=\"hidden\" name=\"_method\" value=\"DELETE\">";
        return str_replace('@delete', $inputDelete, $content);
    }

    private function processPatch(string $content): string
    {
        $inputPatch = "<input type=\"hidden\" name=\"_method\" value=\"PATCH\">";
        return str_replace('@patch', $inputPatch, $content);
    }

    private function processPut(string $content): string
    {
        $inputPut = "<input type=\"hidden\" name=\"_method\" value=\"PATCH\">";
        return str_replace('@put', $inputPut, $content);
    }
}
