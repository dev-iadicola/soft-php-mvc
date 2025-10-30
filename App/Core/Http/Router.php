<?php

namespace App\Core\Http;

use App\Controllers\Admin\DashBoardController;
use \App\Core\Http\Request;
use \App\Core\Mvc;
use \App\Core\Exception\NotFoundException;
use App\Core\Support\Collection\BuildAppFile;

/**
 * Summary of Router
 * Orchestratore: carica > Registra > Matcha > Dispathca
 */
class Router
{

    public Request $request;
    public Mvc $mvc;
    public BuildAppFile $config;

    #Nuove implementazioni
    private RouteRegister $registry;
    private RouteMatcher $matcher;
    private RouteDispatcher $dispatcher;

    public function __construct(?Mvc $mvc = null)
    {
        $this->mvc = $mvc ?? mvc();
        $this->request =  $mvc->request ?? mvc()->request;
        $this->config = $mvc->config ?? mvc()->config;
        $this->registry = new RouteRegister();
        $this->matcher = new RouteMatcher();
        $this->dispatcher = new RouteDispatcher();
    }

    /**
     * Summary of boot
     * @return void
     */
    private function boot()
    {
        //dd($this->config->folder->controllers);
        $routes = RouteLoader::load(
            controllersPath: $this->config->folder->controllers . "/*.php",
            controllerNameSpace: "App\\Controllers"
        );
        dd($routes);
        $this->registry->register($routes);
    }
    public function handle()
    {
        $this->boot();
        // ritorna la rotta da selezionare sendo la richiesta svolta.
        $route = $this->matcher->match($this->request->uri(), $this->request->getRequestMethod(), $this->registry);
    
        // Esegui il controller e la sua action
        $response = $this->dispatcher->dispatch($route);

        // // Se invece la action restituisce qualcosa (es. array), lo puoi gestire facoltativamente.
        // if ($response instanceof \App\Core\Http\Response) {
        //     $response->send();
        // }
    }





    public function getRoute()
    {
        $method = $this->request->getRequestMethod();
        $path = $this->request->uri();
        $routes = $this->config->routes;



        if (isset($routes[$method][$path])) {
            return [$routes[$method][$path], []];
        }

        foreach ($routes[$method] as $route => $response) {
            $routeRegex = preg_replace('/\{[^\}]+\}/', '([^/]+)', $route);
            if (preg_match('#^' . $routeRegex . '$#', $path, $matches)) {
                array_shift($matches);
                return [$response, $matches];
            }
        }

        return false;
    }

    public function resolve()
    {
        $route = $this->getRoute();
        if (!$route) throw new NotFoundException();
        $this->dispatch($route);
    }

    public function dispatch(array $route)
    {


        list($response, $params) = $route;
        $controller = $response[0]; // result: App\Controller\HomeController
        $method = $response[1]; // string to page view

        if (!class_exists($controller)) {
            throw new \Exception("Controller class $controller not found");
        }
        // return: array of path, method and body of request if is post
        $request = $this->request;

        $instance = new $controller($this->mvc);

        if (!method_exists($instance, $method)) {
            throw new \Exception("Method $method not found in controller $controller");
        }
        $this->mvc->middleware->execute(); // controllo middleware


        call_user_func_array([$instance, $method], array_merge([$request], $params));
    }


    /*  public function dispatch($route) {
        list($response, $params) = $route;
        $controller = $response[0];
        $method = $response[1];
    
        if (!class_exists($controller)) {
            throw new \Exception("Controller class $controller not found");
        }
    
        $instance = new $controller($this->mvc);
    
        if (!method_exists($instance, $method)) {
            throw new \Exception("Method $method not found in controller $controller");
        }
    
        $this->mvc->middleware->execute(); // controllo middleware
    
        
        call_user_func_array([$instance, $method], array_merge([$this->request], $params)); // passa i parametri dinamici
    } */
}
