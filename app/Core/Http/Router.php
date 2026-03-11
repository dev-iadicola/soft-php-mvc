<?php

declare(strict_types=1);

namespace App\Core\Http;

use App\Core\Http\Request;
use App\Core\Mvc;
use App\Core\Exception\NotFoundException;
use App\Core\Http\Helpers\RouteCollection;
use App\Core\Support\Collection\ConfigCollection;

/**
 * Router — Orchestratore: carica > matcha > dispatcha
 */
class Router
{
    public Request $request;
    public Mvc $mvc;
    public ConfigCollection $config;

    private RouteMatcher $matcher;
    private RouteDispatcher $dispatcher;
    private RouteLoader $loader;

    public function __construct(?Mvc $mvc = null)
    {
        $this->mvc = $mvc ?? mvc();
        $this->request = $mvc->request ?? mvc()->request;
        $this->config = $mvc->config ?? mvc()->config;
        $this->loader = new RouteLoader($this->config->get('controllers'));
        $this->matcher = new RouteMatcher();
        $this->dispatcher = new RouteDispatcher($this->request);
    }

    /**
     * Carica tutti i controller e genera una collection di rotte.
     */
    public function boot(): RouteCollection
    {
        $cachePath = $this->getCachePath();

        if (file_exists($cachePath)) {
            return RouteCache::loadFromFile($cachePath);
        }

        return $this->loader->load();
    }

    /**
     * Risolvi la richiesta corrente: carica rotte, matcha, dispatcha.
     *
     * @throws NotFoundException
     */
    public function resolve(): void
    {
        $routeCollection = $this->boot();

        $route = $this->matcher->match($this->request, $routeCollection);

        if ($route === null) {
            throw new NotFoundException(
                "No Route found for method " . $this->request->getRequestMethod()
                . " and request '{$this->request->uri()}'."
            );
        }

        $this->dispatcher->dispatch($route);
    }

    /**
     * Ritorna il RouteLoader (utile per CLI e testing).
     */
    public function getLoader(): RouteLoader
    {
        return $this->loader;
    }

    private function getCachePath(): string
    {
        return baseRoot() . '/storage/cache/routes.cache.php';
    }
}
