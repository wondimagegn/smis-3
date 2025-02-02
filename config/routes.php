<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */

/**    
 * map controller resources
 */

//Router::mapResources('dashboard',array('prefix' => 'api'));
Router::mapResources('dashboard', array(
   array('action' => 'index', 'method' => 'GET', 'id' => false),
   array('action' => 'getApprovalRejectGrade', 'method' => 'POST', 'id' => false),
   array('action' => 'getApprovalRejectGradeChange', 'method' => 'POST', 'id' => false),
   array('action' => 'disptachedAssignedCourseList', 'method' => 'POST', 'id' => false),
   array('action' => 'addDropRequestList', 'method' => 'POST', 'id' => false),
   array('action' => 'clearanceWithdrawSubRequest', 'method' => 'POST', 'id' => false),
   array('action' =>'getBackupAccountRequest', 'method' => 'POST', 'id' => false),
   array('action' =>'getProfileNotComplete', 'method' => 'POST', 'id' => false),
));

Router::mapResources('auto_messages',array(array('action' => 'delete', 'method' => 'GET', 'id' => true),));


//Router::mapResources('dashboard');

// enable rest
CakePlugin::routes();
Router::parseExtensions();
//Router::parseExtensions('csv');

Router::setExtensions(array('pdf','json'));
// Router::setExtensions(array('json'),true);
//Router::setExtensions();

Router::connect('/', array('controller' => 'dashboard', 'action' => 'index'));

/**
 * Load all plugin routes. See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
//CakePlugin::routes();
//CakePlugin::load(array('Acls')); 
/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
 require CAKE . 'Config' . DS . 'routes.php';