<?php
namespace App\Core\Http;
use App\Controllers\Admin\DashBoardController;
use \App\Core\Http\Request;
use \App\Core\Mvc;
use \App\Core\Exception\NotFoundException;


class Router {

    public Request $request;

   
    public function __construct(public Mvc $mvc) {
        $this->request = $this->mvc->request; // Inizializza la proprietà request
    }

    public function getRoute() {
        $method = $this->mvc->request->getRequestMethod();
        $path = $this->mvc->request->getRequestPath();
        $routes = $this->mvc->config['routes'];
        
        
    
        if (isset($routes[$method][$path])) {
            error_log("Matched static route: $path");
            return [$routes[$method][$path], []];
        }
    
        foreach ($routes[$method] as $route => $response) {
            $routeRegex = preg_replace('/\{[^\}]+\}/', '([^/]+)', $route);
            if (preg_match('#^' . $routeRegex . '$#', $path, $matches)) {
                array_shift($matches);
                error_log("Matched dynamic route: $route");
                return [$response, $matches];
            }
        }
    
        return false;
    }
    

    public function resolve() {
        $route = $this->getRoute();
        if (!$route) throw new NotFoundException();
        $this->dispatch($route);
    }

    public function dispatch($route) {

        list($response, $params) = $route;
        $controller = $response[0];
        $method = $response[1];

        if (!class_exists($controller)) {
            throw new \Exception("Controller class $controller not found");
        }

        $request = $this->request;


        // var_dump(['controller' => $controller], ['method' => $method], ['req' =>$request],['param' =>  $params] );

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
