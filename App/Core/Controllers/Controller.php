<?php
namespace App\Core\Controllers;

use App\Core\Facade\Session;
use App\Core\Http\Attributes\AttributeMiddleware;
use \App\Core\Mvc;
use App\Core\Services\SessionStorage;
use App\Core\Validator;
use App\Core\Storage;

/**
 *  sommario di Controller
 * 
 * Tramite questa classe diamo la  base per 
 * i controllers che estenderanno questa classe
 * 
 * 
 */


abstract class Controller
{
    protected Mvc $mvc; 
    public function __construct( ?Mvc $mvc = null)
    {
       $this->mvc = $mvc ?? mvc();
    }

 

    /**
     * reindirizzamento alla cartella
     * @var $view inserire il file con estensione php per
     * visualizzare la pagina
     * @var array $message  
     * All'interno di questo array insieramo  tutti i valori che sostituiranno 
     * i placceholders. esempio {{page}} verrà sostituiro da una variabile con indice page presente in un array
     *  
     * per maggiori particolari,andare nel file View presente su /App/Core/View
     */
    public function render(string $view, array $variables = [], array|null $message = ['message' => ''])
    {

        $content = $this->mvc->view->render(page: $view, variables: $variables, message: $message);
        $this->mvc->response->setContent($content);
    }

    public function redirect(?string $var = null): static
    {
        $this->mvc->response->redirect($var);
        return $this;
    }

    public function statusCode413()
    {
        $this->mvc->response->set413();
    }



  
    public function withError($message): static
    {
        Session::setFlash('error', $message);
        return $this;
    }

    public function withSuccess($message)
    {
        Session::setFlash('success', $message);
    }

    public function withWarning($message)
    {
        Session::setFlash('warning', $message);
    }

    /**
     * Modifica il Layout della pagina
     */

    protected function setLayout(string $layout)
    {
        if (str_contains($layout, '.php')) {
            $layout = str_replace('.php', '', $layout);
        }

        $this->mvc->view->layout = $layout;
    }


}