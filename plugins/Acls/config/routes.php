<?php
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

Router::plugin('Acls', ['path' => '/acls'], function (RouteBuilder $routes) {
    $routes->connect('/permissions/add/:acoId/:roleId', ['controller' => 'Permissions', 'action' => 'add'])
        ->setPatterns(['acoId' => '\d+', 'roleId' => '\d+'])
        ->setPass(['acoId', 'roleId']);
    $routes->connect('/permissions/add/:acoId', ['controller' => 'Permissions', 'action' => 'add'])
        ->setPatterns(['acoId' => '\d+'])
        ->setPass(['acoId']);
    $routes->fallbacks('DashedRoute');
});
