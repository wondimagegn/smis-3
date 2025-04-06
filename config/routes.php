<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
use Cake\Http\Middleware\CsrfProtectionMiddleware;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Routing\Route\DashedRoute;

Router::defaultRouteClass(DashedRoute::class);

Router::scope('/', function (RouteBuilder $routes) {

    // Connect the root URL to DashboardController index action
    $routes->connect('/', ['controller' => 'Dashboard', 'action' => 'index']);

    $routes->setExtensions(['json']); // Enable .json requests
    $routes->connect('/dashboard/getMessageAjax',
        ['controller' => 'Dashboard', 'action' => 'getMessageAjax']
    );
    $routes->connect('/dashboard/getRankAjax',
        ['controller' => 'Dashboard', 'action' => 'getRankAjax']
    );

    $routes->connect('/dashboard/getStudentAssignedDormitory',
        ['controller' => 'Dashboard', 'action' => 'getStudentAssignedDormitory']
    );

    $routes->connect('/dashboard/getApprovalRejectGradeChange',
        ['controller' => 'Dashboard', 'action' => 'getApprovalRejectGradeChange']
    );


    $routes->connect('/dashboard/getApprovalRejectGrade',
        ['controller' => 'Dashboard', 'action' => 'getApprovalRejectGrade']
    );


    $routes->connect('/dashboard/disptachedAssignedCourseList',
        ['controller' => 'Dashboard', 'action' => 'disptachedAssignedCourseList']
    );


    $routes->connect('/dashboard/addDropRequestList',
        ['controller' => 'Dashboard', 'action' => 'addDropRequestList']
    );

    $routes->connect('/dashboard/clearanceWithdrawSubRequest',
        ['controller' => 'Dashboard', 'action' => 'clearanceWithdrawSubRequest']
    );


    $routes->connect('/dashboard/getBackupAccountRequest',
        ['controller' => 'Dashboard', 'action' => 'getBackupAccountRequest']
    );



    $routes->connect('/dashboard/getProfileNotComplete',
        ['controller' => 'Dashboard', 'action' => 'getProfileNotComplete']
    );



    // Explicit route for AJAX-based PATCH requests to edit auto message

    $routes->connect(
        '/auto-messages/mark-as-unread/:id',
        ['controller' => 'AutoMessages', 'action' => 'markAsUnread', '_method' => ['POST', 'PUT']],
        ['pass' => ['id'], 'id' => '[0-9a-f\-]+']
    );


/*
    // Define RESTful resource routes for Dashboard
    $routes->resources('Dashboard', [
        'map' => [
            ['action' => 'index', 'method' => 'GET', 'path' => ''],
            ['action' => 'getApprovalRejectGrade', 'method' => 'POST', 'path' => 'getApprovalRejectGrade'],
            ['action' => 'getApprovalRejectGradeChange', 'method' => 'POST', 'path' => 'getApprovalRejectGradeChange'],
            ['action' => 'disptachedAssignedCourseList', 'method' => 'POST', 'path' => 'disptachedAssignedCourseList'],
            ['action' => 'addDropRequestList', 'method' => 'POST', 'path' => 'addDropRequestList'],
            ['action' => 'clearanceWithdrawSubRequest', 'method' => 'POST', 'path' => 'clearanceWithdrawSubRequest'],
            ['action' => 'getBackupAccountRequest', 'method' => 'POST', 'path' => 'getBackupAccountRequest'],
            ['action' => 'getProfileNotComplete', 'method' => 'POST', 'path' => 'getProfileNotComplete'],
        ]
    ]);
    */

    $routes->connect('/dashboard/getMessageAjax',
        ['controller' => 'Dashboard', 'action' => 'getMessageAjax']);




    // Register scoped middleware for in scopes.
    $routes->registerMiddleware('csrf', new CsrfProtectionMiddleware([
        'httpOnly' => true,
    ]));
    $routes->applyMiddleware('csrf');
//    $routes->connect('/', ['controller' => 'Pages', 'action' => 'display', 'home']);
 //   $routes->connect('/pages/*', ['controller' => 'Pages', 'action' => 'display']);

    $routes->connect('/', array('controller' => 'Dashboard', 'action' => 'index'));

   // $routes->fallbacks(DashedRoute::class);

});

Router::defaultRouteClass(DashedRoute::class);

Router::scope('/', function ($routes) {
    $routes->connect('/', ['controller' => 'Dashboard', 'action' => 'index']);
    $routes->fallbacks(DashedRoute::class);
});
