<?php
namespace App\Controller\Component;

use Cake\Cache\Cache;
use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use Cake\Utility\Inflector;

class MenuOptimizedComponent extends Component
{

    public $components = ['Acl', 'Auth', 'Flash'];
    public $defaultMenuParent = null;
    public $autoLoad = true;
    public $cacheKey = 'menu_storage';
    public $cacheTime = '+1 day';
    public $cacheConfig = 'menu_component';
    public $aclSeparator = '/';
    public $aclPath = 'controllers/';
    public $excludeActions = ['view', 'edit', 'delete', 'admin_edit', 'admin_delete', 'admin_view'];

    public $excludedMethods = [];
    public $menu = [];
    public $rawMenus = [];
    protected $_rebuildMenus = false;

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        parent::__construct($registry, $config);
        $this->Auth = $this->getController()->Auth;
    }

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $session = $this->getController()->getRequest()->getSession();

        // Check if user is logged in
        if ($this->Auth->user('id')) {
            $dir = new Folder(Configure::read('Utility.cache'));

            $roleId = $this->Auth->user('role_id');
            if ($roleId == 3) {
                $files = $dir->findRecursive('menu_storagerole3_menu_storage');
            } elseif ($roleId == 2) {
                $files = $dir->findRecursive('menu_storagerole2_menu_storage');
            } else {
                $files = $dir->findRecursive('menu_storageuser' . $this->Auth->user('id') . '.*');
            }

            if (empty($files)) {
                $session->delete('permissionLists');
                $this->_rebuildMenus = true;
            }
        }
    }

    public function startup()
    {

        if (!$this->Auth->user()) {
            return;
        }

        // Add menu items
        $this->menu = $this->addMenu([
            'title' => 'Exam Schedule View',
            'parent' => 'examSchedule',
            'url' => ['controller' => 'ExamSchedules', 'action' => 'college_exam_schedule_view']
        ]);

        $this->menu = $this->addMenu([
            'title' => 'Basic Profile',
            'parent' => 'dashboard',
            'url' => ['controller' => 'Students', 'action' => 'profile']
        ]);

        // add custom menu, this happens when we need to promot some action
        $this->menu = $this->addMenu(array(
            'title' => 'Exam Schedule View',
            'parent' => 'examSchedule',
            'url' => array(
                'controller' => 'examSchedules',
                'action' => 'college_exam_schedule_view',
            )
        ));

        $this->menu = $this->addMenu(array(
            'title' => 'Grade View By Course',
            'parent' => 'examGrades',
            'url' => array(
                'controller' => 'courseRegistrations',
                'action' => 'grade_view_by_course',
            )
        ));

        $this->menu = $this->addMenu(array(
            'title' => 'Issue/Reset Password',
            'parent' => 'acceptedStudents',
            'url' => array(
                'controller' => 'students',
                'action' => 'department_issue_password',
            )
        ));

        $this->menu = $this->addMenu(array(
            'title' => 'Issue/Reset Password',
            'parent' => 'acceptedStudents',
            'url' => array(
                'controller' => 'students',
                'action' => 'freshman_issue_password',
            )
        ));

        $this->menu = $this->addMenu(array(
            'title' => 'Basic Profile',
            'parent' => 'dashboard',
            'url' => array(
                'controller' => 'students',
                'action' => 'profile',
            )
        ));

        $this->menu = $this->addMenu(array(
            'title' => 'Academic Profile',
            'parent' => 'dashboard',
            'url' => array(
                'controller' => 'students',
                'action' => 'student_academic_profile',
            )
        ));

        $this->menu = $this->addMenu(array(
            'title' => 'Add Grade Type',
            'parent' => 'gradeSettings',
            'url' => array(
                'controller' => 'grade_types',
                'action' => 'add',
            ),
        ));

        $this->menu = $this->addMenu(array(
            'title' => 'Delegate Grade Scale',
            'parent' => 'gradeSettings',
            'url' => array(
                'controller' => 'colleges',
                'action' => 'registrar_delegate_scale',
            ),
        ));

        $this->menu = $this->addMenu(array(
            'title' => 'View Grade Types',
            'parent' => 'gradeSettings',
            'url' => array(
                'controller' => 'grade_types',
                'action' => 'index',
            ),
        ));

        $this->menu = $this->addMenu(array(
            'title' => 'Add/Delete Class Period Course Constraints',
            'parent' => 'courseConstraint',
            'url' => array(
                'controller' => 'ClassPeriodCourseConstraints',
                'action' => 'add',
            ),
        ));

        $this->menu = $this->addMenu(array(
            'title' => 'Add/Delete Class Room Class Period Constraints',
            'parent' => 'courseConstraint',
            'url' => array(
                'controller' => 'classRoomClassPeriodConstraints',
                'action' => 'add',
            ),
        ));

        $this->menu = $this->addMenu(array(
            'title' => 'Add/Delete Class Room Course Constraints',
            'parent' => 'courseConstraint',
            'url' => array(
                'controller' => 'classRoomCourseConstraints',
                'action' => 'add',
            ),
        ));

        $this->menu = $this->addMenu(array(
            'title' => 'Add/Delete Instructor Class Period Constraints',
            'parent' => 'courseConstraint',
            'url' => array(
                'controller' => 'instructorClassPeriodCourseConstraints',
                'action' => 'add',
            ),
        ));

        $this->menu = $this->addMenu(array(
            'title' => 'Generate Course Schedule',
            'parent' => 'courseSchedule',
            'url' => array(
                'controller' => 'courseSchedules',
                'action' => 'generate',
            ),
        ));

        $this->menu = $this->addMenu(array(
            'title' => 'Manual Course Schedule',
            'parent' => 'courseSchedule',
            'url' => array(
                'controller' => 'courseSchedules',
                'action' => 'manual_update_schedule',
            ),
        ));

        $this->menu = $this->addMenu(array(
            'title' => 'Cancel Course Schedule',
            'parent' => 'courseSchedule',
            'url' => array(
                'controller' => 'courseSchedules',
                'action' => 'cancel_auto_generated_schedule',

            ),
        ));

        $this->menu = $this->addMenu(array(
            'title' => 'View Course Schedule',
            'parent' => 'courseSchedule',
            'url' => array(
                'controller' => 'courseSchedules',
                'action' => 'index',
            ),
        ));

        $this->menu = $this->addMenu(array(
            'title' => 'Add/Delete Course Exam Gap Constraints',
            'parent' => 'examConstraint',
            'url' => array(
                'controller' => 'courseExamGapConstraints',
                'action' => 'add',
            ),
        ));

        $this->menu = $this->addMenu(array(
            'title' => 'Add/Delete Course Exam Session Constraints',
            'parent' => 'examConstraint',
            'url' => array(
                'controller' => 'courseExamConstraints',
                'action' => 'add',
            ),
        ));

        $this->menu = $this->addMenu(array(
            'title' => 'Add/Delete Exam Room Session Constraints',
            'parent' => 'examConstraint',
            'url' => array(
                'controller' => 'examRoomConstraints',
                'action' => 'add',
            ),
        ));

        $this->menu = $this->addMenu(array(
            'title' => 'Add/Delete Exam Room Course Constraints',
            'parent' => 'examConstraint',
            'url' => array(
                'controller' => 'examRoomCourseConstraints',
                'action' => 'add',
            ),
        ));

        $this->menu = $this->addMenu(array(
            'title' => 'Add/Delete Instructor Exam Exclude Date Constraints',
            'parent' => 'examConstraint',
            'url' => array(
                'controller' => 'instructorExamExcludeDateConstraints',
                'action' => 'add',
            ),
        ));

        $this->menu = $this->addMenu(array(
            'title' => 'Add/Delete Instructor Number of Exam Constraints',
            'parent' => 'examConstraint',
            'url' => array(
                'controller' => 'instructorNumberOfExamConstraints',
                'action' => 'add',
            ),
        ));

        $this->menu = $this->addMenu(array(
            'title' => 'Add/Edit Course Number Of Sessions',
            'parent' => 'courseSchedule',
            'url' => array(
                'controller' => 'publishedCourses',
                'action' => 'add_course_session',
            ),
        ));

        $this->menu = $this->addMenu(array(
            'title' => 'Manage Student Medical Card Number',
            'parent' => 'healthService',
            'url' => array(
                'controller' => 'students',
                'action' => 'manage_student_medical_card_number',
            ),
        ));

        $this->menu = $this->addMenu(array(
            'title' => 'Temporary Degree',
            'parent' => 'certificates',
            'url' => array(
                'controller' => 'graduateLists',
                'action' => 'temporary_degree',
            ),
        ));

        $this->menu = $this->addMenu(array(
            'title' => 'Mass Certificate Print',
            'parent' => 'certificates',
            'url' => array(
                'controller' => 'graduateLists',
                'action' => 'mass_certificate_print',
            ),
        ));

        $this->menu = $this->addMenu(array(
            'title' => 'Student Name Change',
            'parent' => 'graduation',
            'url' => array(
                'controller' => 'students',
                'action' => 'name_list',
            ),
        ));

        $this->menu = $this->addMenu(array(
            'title' => 'Language Proficiency',
            'parent' => 'certificates',
            'url' => array(
                'controller' => 'graduateLists',
                'action' => 'language_proficiency',
            ),
        ));

        $this->menu = $this->addMenu(array(
            'title' => 'To Whom It May Concern',
            'parent' => 'certificates',
            'url' => array(
                'controller' => 'graduateLists',
                'action' => 'to_whom_it_may_concern',
            ),
        ));

        $this->menu = $this->addMenu(array(
            'title' => 'Graduation Certificate',
            'parent' => 'certificates',
            'url' => array(
                'controller' => 'graduateLists',
                'action' => 'graduation_certificate',
            ),
        ));

        $this->menu = $this->addMenu(array(
            'title' => 'Student Copy',
            'parent' => 'certificates',
            'url' => array(
                'controller' => 'examGrades',
                'action' => 'student_copy',
            ),
        ));

        $this->menu = $this->addMenu(array(
            'title' => 'Supplementary Exam',
            'parent' => 'makeupExams',
            'url' => array(
                'controller' => 'examGradeChanges',
                'action' => 'department_makeup_exam_result',
            ),
        ));

        $this->menu = $this->addMenu(array(
            'title' => 'Manage Grade Change',
            'parent' => 'examGrades',
            'url' => array(
                'controller' => 'examGradeChanges',
                'action' => 'manage_department_grade_change',
            ),
        ));

        $this->menu = $this->addMenu(array(
            'title' => 'Cancel Auto Grade Change',
            'parent' => 'examGrades',
            'url' => array(
                'controller' => 'examGradeChanges',
                'action' => 'cancel_auto_grade_change',
            ),
        ));

        $this->menu = $this->addMenu(array(
            'title' => 'Manage Grade Change',
            'parent' => 'examGrades',
            'url' => array(
                'controller' => 'examGradeChanges',
                'action' => 'manage_department_grade_change',
            ),
        ));

        $this->menu = $this->addMenu(array(
            'title' => 'Manage Grade Change',
            'parent' => 'examGrades',
            'url' => array(
                'controller' => 'examGradeChanges',
                'action' => 'manage_college_grade_change',
            ),
        ));

        $this->menu = $this->addMenu(array(
            'title' => 'Manage Grade Change',
            'parent' => 'examGrades',
            'url' => array(
                'controller' => 'examGradeChanges',
                'action' => 'manage_registrar_grade_change',
            ),
        ));

        $this->menu = $this->addMenu(array(
            'title' => 'Supplementary Exam',
            'parent' => 'examGrades',
            'url' => array(
                'controller' => 'examGradeChanges',
                'action' => 'department_makeup_exam_result',
            ),
        ));

        $this->menu = $this->addMenu(array(
            'title' => 'Freshman Supplementary Exam',
            'parent' => 'examGrades',
            'url' => array(
                'controller' => 'examGradeChanges',
                'action' => 'freshman_makeup_exam_result',
            ),
        ));

        $this->menu = $this->addMenu(array(
            'title' => 'Manage Freshman Grade Change',
            'parent' => 'examGrades',
            'url' => array(
                'controller' => 'examGradeChanges',
                'action' => 'manage_freshman_grade_change',
            ),
        ));


        if ($this->autoLoad) {
            //$this->loadCache();
            $this->constructMenu($this->Auth->user());
            //$this->writeCache();
        }
    }

    public function writeCache()
    {

        $data = ['menus' => array_unique($this->rawMenus, SORT_REGULAR)];

        if (Cache::write($this->cacheKey, $data, $this->cacheConfig)) {
            return true;
        }

        Log::error('Menu Component - Could not write Menu cache.');
        return false;
    }

    public function loadCache()
    {

        $data = Cache::read($this->cacheKey, $this->cacheConfig);

        if ($data) {
            $this->rawMenus = $this->_mergeMenuCache($data['menus']);
            return true;
        }

        $this->_rebuildMenus = true;
        return false;
    }

    public function clearCache()
    {

        return Cache::delete($this->cacheKey, $this->cacheConfig);
    }

    public function constructMenu($aro)
    {

        if (is_array($aro)) {
            $roleId = $this->Auth->user('role_id');
            $aroKey = ($roleId == 3) ? 'Role3' : (($roleId == 2) ? 'Role2' : 'User' . $aro['id']);
        }

        // $cacheKey = $aroKey . '_' . $this->cacheKey;
        // $completeMenu = Cache::read($cacheKey, $this->cacheConfig);
        $completeMenu = false;

        if (!$completeMenu || $this->_rebuildMenus) {
            $this->generateRawMenus();

            $menu = [];
            foreach ($this->rawMenus as $item) {
                $aco = Inflector::camelize($item['url']['controller']);
                if (!empty($item['url']['action'])) {
                    $aco = $this->aclPath . $aco . $this->aclSeparator .
                        $item['url']['action'];
                }
                if ($this->check($aro, $aco)) {
                    if (!isset($menu[$item['id']])) {
                        $menu[$item['id']] = $item;
                    }
                }
            }

            $completeMenu = $this->_formatMenu($menu);
            //  Cache::write($cacheKey, $completeMenu, $this->cacheConfig);
        }

        $this->menu = $completeMenu;
    }

    public function generateRawMenus()
    {

        $cakeAdmin = Configure::read('Routing.prefixes')[0] ?? null;
        $this->createExclusions();

        // Get all controllers dynamically
        $Controllers = $this->permissions();

        if (!empty($Controllers)) {
            foreach ($Controllers as $ctrlName => $actions) {
                // Load controller dynamically
                $ctrlClass = 'App\\Controller\\' . $ctrlName . 'Controller';

                if (!class_exists($ctrlClass)) {
                    continue; // Skip if the controller does not exist
                }

                $methods = $actions['action'] ?? [];

                $classVars = get_class_vars($ctrlClass);
                $menuOptions = $this->setOptions($classVars);

                if ($menuOptions === false) {
                    continue;
                }

                $methods = $this->filterMethods($methods, $menuOptions['exclude']);
                $ctrlCamel = Inflector::variable($ctrlName);
                $ctrlHuman = Inflector::humanize(Inflector::underscore($ctrlCamel));
                $adminController = false;

                if (!empty($methods)) {
                    foreach ($methods as $action) {
                        $camelAction = Inflector::variable($action);
                        $human = $menuOptions['alias'][$action] ?? Inflector::humanize(Inflector::underscore($action));

                        $url = ['controller' => $ctrlCamel, 'action' => $action];

                        if ($cakeAdmin) {
                            $url[$cakeAdmin] = false;
                        }

                        if (strpos($action, $cakeAdmin . '_') !== false && $cakeAdmin) {
                            $url[$cakeAdmin] = true;
                            $adminController = true;
                        }

                        $parent = $menuOptions['controllerButton'] ? $ctrlCamel : $menuOptions['parent'];

                        $this->rawMenus[] = [
                            'parent' => $parent,
                            'id' => $this->_createId($ctrlCamel, $action),
                            'title' => $human,
                            'url' => $url,
                            'weight' => 0,
                        ];
                    }
                }

                if ($menuOptions['controllerButton']) {
                    // Use admin index if available
                    $action = $adminController ? $cakeAdmin . '_index' : 'index';

                    $url = [
                        'controller' => $ctrlCamel,
                        'action' => $action,
                        'admin' => $adminController,
                    ];

                    // Core menu item
                    $menuItem = [
                        'parent' => $menuOptions['parent'],
                        'id' => $ctrlCamel,
                        'title' => $ctrlHuman,
                        'url' => $url,
                        'weight' => $menuOptions['weight'] ?? 0
                    ];
                    $this->rawMenus[] = $menuItem;
                }
            }
        }
    }


    public function permissions()
    {

        return $this->getController()->getRequest()->getSession()->read('reformatePermission'); // ✅ FIXED SESSION USAGE

    }

    public function check($aro, $aco, $action = "*")
    {

        $permissionLists = $this->getController()->getRequest()->getSession()->read(
            'permissionLists'
        ); // ✅ FIXED SESSION USAGE
        return isset($permissionLists) && !empty($permissionLists) && in_array($aco, $permissionLists);
    }

    public function addMenu($menu)
    {
        $defaults = [
            'title' => null,
            'url' => null,
            'parent' => null,
            'id' => null,
            'weight' => 0,
        ];

        $menu = array_merge($defaults, $menu);
        if (!$menu['id'] && isset($menu['url'])) {
            $menu['id'] = $this->_createId($menu['url']);
        }

        if (!$menu['title'] && isset($menu['url']['action'])) {
            $menu['title'] = Inflector::humanize($menu['url']['action']);
        }

        $this->rawMenus[] = $menu;
    }

    public function beforeRender()
    {

        $this->getController()->set('menuoptimized', $this->menu);
    }

    protected function _createId()
    {

        $args = func_get_args(); // Get function arguments
        $flattened = [];

        foreach ($args as $arg) {
            if (is_array($arg)) {
                $flattened = array_merge($flattened, $arg); // Flatten nested arrays
            } else {
                $flattened[] = $arg;
            }
        }

        return Inflector::variable(implode('-', $flattened)); // Ensure only strings are passed
    }

    protected function _formatMenu($menu)
    {

        $out = [];
        $idMap = [];

        foreach ($menu as $item) {
            $item['children'] = [];
            $id = $item['id'];
            $parentId = $item['parent'];

            if (isset($idMap[$id]['children'])) {
                $idMap[$id] = array_merge_recursive($item, $idMap[$id]);
            } else {
                $idMap[$id] = array_merge_recursive($item, ['children' => []]);
            }

            if ($parentId) {
                $idMap[$parentId]['children'][] = &$idMap[$id];
            } else {
                $out[] = &$idMap[$id];
            }
        }

        usort($out, [$this, '_sortMenu']);
        return $out;
    }

    protected function _sortMenu($one, $two)
    {

        return $one['weight'] <=> $two['weight'];
    }


    protected function _mergeMenuCache($cachedMenus)
    {

        $cacheCount = count($cachedMenus);
        $currentCount = count($this->rawMenus);
        $tmp = [];

        if ($currentCount !== 0) {
            foreach ($this->rawMenus as $addedMenu) {
                $exist = false;

                if ($cacheCount !== 0) {
                    foreach ($cachedMenus as $cachedItem) {
                        if ($addedMenu['id'] === $cachedItem['id']) {
                            $exist = true;
                            break;
                        }
                    }
                }

                if (!$exist) {
                    $tmp[] = $addedMenu;
                }
            }
        }

        if (!empty($tmp)) {
            $this->_rebuildMenus = true;
        }

        return array_merge($cachedMenus, $tmp);
    }

    public function createExclusions()
    {

        // Get methods from AppController (instead of 'Controller')
        $appControllerMethods = get_class_methods('App\Controller\AppController');

        // Ensure it includes all inherited methods
        if (!$appControllerMethods) {
            $appControllerMethods = [];
        }

        // Merge with excluded actions
        $methods = array_merge($appControllerMethods, $this->excludeActions);

        // Convert all method names to lowercase
        $this->excludedMethods = array_map('strtolower', $methods);
    }

    public function filterMethods(array $methods, array $remove = []): array
    {

        // Convert removed method names to lowercase
        $remove = array_map('strtolower', $remove);

        // Ensure excluded methods exist
        $exclusions = isset($this->excludedMethods) ? array_merge($this->excludedMethods, $remove) : $remove;

        // Filter methods
        return array_values(array_filter($methods, function ($method) use ($exclusions) {

            $method = strtolower($method);

            // Ignore private/protected methods
            if (strpos($method, '_') === 0) {
                return false;
            }

            // Remove methods that exist in exclusions
            return !in_array($method, $exclusions);
        }));
    }

    public function setOptions($controllerVars)
    {

        $cakeAdmin = Configure::read('Routing.prefixes')[0] ?? '';

        // Ensure menuOptions exists and is an array
        $menuOptions = $controllerVars['menuOptions'] ?? [];

        // Define default exclusions and options
        $exclude = ['view', 'edit', 'delete', "{$cakeAdmin}_edit", "{$cakeAdmin}_delete", "{$cakeAdmin}_view"];

        $defaults = [
            'exclude' => $exclude,
            'alias' => [],
            'parent' => $this->defaultMenuParent,
            'controllerButton' => true
        ];

        // Merge user-defined options with defaults
        $menuOptions = array_merge($defaults, $menuOptions);

        // If '*' is in exclude list, return false
        if (in_array('*', (array)$menuOptions['exclude'], true)) {
            return false;
        }

        return $menuOptions;
    }

}
