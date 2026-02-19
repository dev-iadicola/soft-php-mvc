<?php
namespace App\Core\Controllers;

use App\Core\Facade\Session;
use App\Core\Http\Attributes\AttributeMiddleware;
use App\Core\Http\Attributes\ControllerAttr;
use \App\Core\Mvc;
use App\Core\Services\SessionStorage;
use App\Core\Validator;
use App\Core\Storage;

/**
 * Base abstract controller providing core functionalities for
 * rendering views, handling redirects, and setting session messages.
 * Includes several deprecated methods with alternative recommendations.
 */
#[ControllerAttr(['web'])]
abstract class BaseController
{
    protected Mvc $mvc; 
    public function __construct( ?Mvc $mvc = null)
    {
       $this->mvc = $mvc ?? Mvc::$mvc;
    }

 

    /**
     * @deprecated use a global view('namedire.namefile') function.
     * 
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

    /**
     * Summary of redirect
     * @deprecated use @global response()->redirect($var); 
     * @param mixed $var
     * @return BaseController
     */
    public function redirect(?string $var = null): static
    {
        $this->mvc->response->redirect($var);
        return $this;
    }

    /**
     * @deprecated use global response
     * how: response()->redirect()->setStaus($num);
     * Summary of statusCode413
     * @return void
     */
    public function statusCode413()
    {
        $this->mvc->response->set413();
    }



    /**
     * Summary of withError
     * @deprecated use @method  response()->withError();
     * @param mixed $message
     * @return BaseController
     */
    public function withError($message): static
    {
        Session::setFlash('error', $message);
        return $this;
    }
    /**
     * Summary of withError
     * @deprecated use @method  response()->withSuccess();
     * @param mixed $message
     * @return BaseController
     */
    public function withSuccess($message)
    {
        Session::setFlash('success', $message);
    }
    /**
     * Summary of withError
     * @deprecated use @method  response()->withWarning();
     * @param mixed $message
     * @return BaseController
     */
    public function withWarning($message):void
    {
        Session::setFlash('warning', $message);
    }

    /**
     * Summary of setLayout
     * Use this for the set layout page.
     * @param string $layout
     * @return void
     */
    public function setLayout(string $layout): void
    {
        if (str_contains($layout, '.php')) {
            $layout = str_replace('.php', '', $layout);
        }

        Mvc::$mvc->view->layout = $layout;
    }


}