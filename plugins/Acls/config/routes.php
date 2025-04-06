<?php
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

Router::plugin('Acls', ['path' => '/acls'], function (RouteBuilder $routes) {

    $routes->fallbacks('DashedRoute');
});
