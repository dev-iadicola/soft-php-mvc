<?php

namespace App\Core;

use \App\Core\Mvc;
use App\Core\Services\CsrfService;
use App\Core\Services\SessionStorage;

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
    public function render(string $page, array $variables = [], array|null $message = ['message' => '']): string
    {
        if ($message === null) {
            $message = ['message' => ''];
        }

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

        // Sostituzione dei placeholder {{page}} con un file.php
        $pageContent = $this->renderContent($pageContent, $message);
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
        $content = new IncludeDirectiveHandler($this)->cleanPlaceHolders($content, $variables);
        return $content;
    }


    /**
     * Renderizza una vista.
     *
     * @param string $view Nome della vista (senza estensione)
     * @param array $variables Variabili da passare alla vista
     * @return string Contenuto della vista
     */
    private function renderContent(string $content, array|null $message): string
    {
        $chiavi = array_keys($message);
        $chiavi = array_map(fn($chiave) => "{{" . $chiave . "}}", $chiavi);

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
        extract($values);
        extract($variables);
        extract(SessionStorage::getInstance()->getAll()); // per visualizzare i messaggi di errore e successo
        $views = $this->mvc->config->folder->views;
        ob_start();
        include "$views/$folder/$item.php";
        return ob_get_clean();
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

        // * replace placeholder @csrf and @delete
        $content = $this->processCsrf($content);
        $content = $this->processDelete($content);
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
                $includePath = str_replace('.', '/', $includeContent); // get the content in @include()
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
}
