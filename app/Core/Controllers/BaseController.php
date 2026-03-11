<?php

declare(strict_types=1);

namespace App\Core\Controllers;

use App\Core\Facade\Session;
use App\Core\Http\Attributes\AttributeMiddleware;
use App\Core\Http\Attributes\ControllerAttr;
use App\Core\Support\ErrorMessageFormatter;
use \App\Core\Mvc;
use App\Core\Services\SessionStorage;
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
     */
    public function render(string $view, array $variables = [], array|null $message = ['message' => '']): void
    {

        $content = $this->mvc->view->render(page: $view, variables: $variables, message: $message);
        $this->mvc->response->setContent($content);
    }

    /**
     * @deprecated use @global response()->redirect($var);
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
     */
    public function statusCode413(): static
    {
        $this->mvc->response->set413();
        return $this;
    }



    /**
     * @deprecated use @method  response()->withError();
     */
    public function withError(string|array $message): static
    {
        Session::setFlash('error', ErrorMessageFormatter::format($message));
        return $this;
    }
    /**
     * @deprecated use @method  response()->withSuccess();
     */
    public function withSuccess(string $message): void
    {
        Session::setFlash('success', $message);
    }
    /**
     * @deprecated use @method  response()->withWarning();
     */
    public function withWarning(string $message): void
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
