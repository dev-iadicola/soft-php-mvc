<?php 
namespace App\Core\Facade;

use App\Core\Mvc;
use App\Core\View as CoreView;

class View {
    private static CoreView $view; 
    
    public static function make(string $view, array $variables = [], array|null $message = ['message' => '']) : void
    {   
        $content = Mvc::$mvc->view->render(page: $view, variables: $variables, message: $message);
        Mvc::$mvc->response->setContent($content);
    }
}