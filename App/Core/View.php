<?php

namespace App\Core;

use \App\Core\Mvc;
use App\Core\Services\SessionService;

class View
{

    public string $layout = 'default';

    public function __construct(public Mvc $mvc) {}


    public function setLayout(string $layout)
    {
        $this->layout = $layout;
    }

    // Rimpiazziamo i placeholder nelle pagine php
    public function render(string $page, array $values = ['message' => ''], array $variables = []): string
    {
        $page = str_replace('.', '/', $page);

        $layoutValue = [
            'page' => $page,
            'menu' => $this->mvc->config->menu,
        ];

        // Ricerca layouts e page
        $layoutContent = $this->getViewContent("layouts", $this->layout, $layoutValue);
        $pageContent = $this->getViewContent("pages", $page, $layoutValue, $variables);

        // Ricambia Includes 
        $pageContent = $this->processIncludes($pageContent, variables: $variables);
        $layoutContent = $this->processIncludes($layoutContent, $variables);
        // eseguito due volte nel caso si trattasse di un placeholder include con all'interno unaltro placeholder include
        $pageContent =  $this->processIncludes($pageContent, variables: $variables);



        // Sostituzione dei placeholder {{page}} con un file.php
        $pageContent = $this->renderContent($pageContent, $values);
        return $this->renderContent($layoutContent, [
            'page' => $pageContent,
            'footer' => "Applicazione web MVC con PHP"
        ]);
    }

    // Nuova funzione per passare direttamente le variabili alla vista
    private function renderView(string $folder, string $item, array $variables): string
    {
        ob_start();
        extract($variables);
        include "$folder/$item.php";
        return ob_get_clean();
    }


    // Nuova funzione per passare direttamente le variabili alla vista


    /**
     * Renderizza una vista.
     *
     * @param string $view Nome della vista (senza estensione)
     * @param array $variables Variabili da passare alla vista
     * @return string Contenuto della vista
     */


    private function renderContent(string $content, array $values): string
    {
        $chiavi = array_keys($values);
        $chiavi = array_map(fn($chiave) => "{{" . $chiave . "}}", $chiavi);
        foreach ($values as $key => $value) {
            if ($value instanceof Component) {
                $values[$key] = $this->renderComponent($value);
            }
        }
        $valori = array_values($values);
        return str_replace($chiavi, $valori, $content);
    }



    public function renderComponent(Component $componente): string
    {
        $nomeComponente = $componente->getName();
        $componentContent = $this->getViewContent("components", $nomeComponente);
        $content = '';
        foreach ($componente->getItems() as $item) {
            $content .= $this->renderContent($componentContent, $item);
        }
        return $content;
    }



    private function getViewContent(string $folder, string $item, array $values = [], array $variables = []): string
    {
        extract($values);
        extract($variables);
        extract(SessionService::getAll()); // per visualizzare i messaggi di errore e successo
        $views = $this->mvc->config->folder->views;
        ob_start();
        include "$views/$folder/$item.php";
        return ob_get_clean();
    }

    private function processIncludes(string $content, array $variables, int $depth = 0): string
    {
        // Evita loop infiniti con inclusioni cicliche
        if ($depth > 10) {
            throw new \Exception("Inclusion depth too high — possible recursive loop.");
        }

        $pattern = '/@include\(\s*\'([^\']+)\'\s*\)/';

        // Finché troviamo delle inclusioni, le processiamo
        while (preg_match_all($pattern, $content, $matches)) {
            foreach ($matches[1] as $includeContent) {
                $includePath = str_replace('.', '/', $includeContent);
                $includeFileContent = $this->getViewContent('', $includePath, [], $variables);



                if ($includeFileContent !== null) {
                    // ✨ Ricorsione qui
                    $processedInclude = $this->processIncludes($includeFileContent, $variables, $depth + 1);
                    $content = str_replace("@include('$includeContent')", $processedInclude, $content);
                } else {
                    echo "File incluso non trovato: $includePath<br>";
                }
            }
        }



        // Pulizia finale (opzionale)
        $content = $this->processDeleteInclude(content: $content);
        return $content;
    }



    private function processDeleteInclude(string $content)
    {
        $inputDelete = "<input type=\"hidden\" name=\"_method\" value=\"DELETE\">";
        return str_replace('@delete', $inputDelete, $content);
    }
}
