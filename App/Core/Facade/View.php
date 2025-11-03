<?php 
namespace App\Core\Facade;

use App\Core\View as CoreView;

class View {
    private static CoreView $view; 
    
    public static function make(string $view, array $variables = [], array|null $message = ['message' => '']) : void
    {   
        $content = mvc()->view->render(page: $view, variables: $variables, message: $message);
        mvc()->response->setContent($content);
    }
}