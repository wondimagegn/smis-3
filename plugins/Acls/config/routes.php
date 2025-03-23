<?php
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Routing\Route\DashedRoute;

Router::plugin(
    'Acls',
    ['path' => '/acls'],
    function (RouteBuilder $routes) {
      // $routes->fallbacks(DashedRoute::class);

        $routes->connect('/', ['controller' => 'Acls', 'action' => 'index']);
        $routes->fallbacks('DashedRoute');
    }
);
