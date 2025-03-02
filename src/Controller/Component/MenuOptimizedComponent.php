<?php
namespace App\Controller\Component;
use Cake\Controller\Component;
use Cake\Cache\Cache;
use Cake\Event\EventInterface;
use Cake\Utility\Inflector;
use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use Cake\Controller\ComponentRegistry;


class MenuOptimizedComponent extends Component
{

    protected $_defaultConfig = [
        'defaultMenuParent' => null,
        'autoLoad' => true,
        'cacheKey' => 'menu_storage',
        'cacheTime' => '+1 day',
        'cacheConfig' => 'menu_component',
        'aclSeparator' => '/',
        'aclPath' => 'controllers/',
        'excludeActions' => ['view', 'edit', 'delete', 'admin_edit', 'admin_delete', 'admin_view'],

        'components' => ['Auth', 'Acl']
    ];

    protected $rawMenus = [];
    protected $_rebuildMenus = false;
    protected $excludedMethods = [];
    public $menu = [];

    public function __construct(ComponentRegistry $registry, array $config = [])
    {
        parent::__construct($registry, $config);

    }

    public function initialize(array $config): void
    {
        parent::initialize($config);

        // Check if user is logged in and clear cache if there are new user assignments
        debug($this->Auth);
        if ($this->Auth && $this->Auth->user() && $this->Auth->user('id')) {
            $dir = new Folder(Configure::read('Utility.cache'));
            if ($this->Auth->user('role_id') == 3) {
                $files = $dir->findRecursive('menu_storagerole3_menu_storage');
            } elseif ($this->Auth->user('role_id') == 2) {
                $files = $dir->findRecursive('menu_storagerole2_menu_storage');
            } else {
                $files = $dir->findRecursive('menu_storageuser' . $this->Auth->user('id') . '.*');
            }

            if (empty($files)) {
                $this->Session->delete('permissionLists');
                $this->_rebuildMenus = true;
            }
        }
    }

    public function startup(): void
    {
        Cache::setConfig($this->getConfig('cacheConfig'), [
            'engine' => 'File',
            'duration' => $this->getConfig('cacheTime'),
            'prefix' => $this->getConfig('cacheKey')
        ]);

        if (!$this->Auth || !$this->Auth->user()) {
            return;
        }


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

        if ($this->getConfig('autoLoad')) {
            $this->loadCache();
            $this->constructMenu($this->Auth->user());
            $this->writeCache();
        }
    }

    public function writeCache(): bool
    {
        $data = ['menus' => $this->rawMenus];
        if (Cache::write($this->getConfig('cacheKey'), $data, $this->getConfig('cacheConfig'))) {
            return true;
        }
        $this->log('Menu Component - Could not write Menu cache.');
        return false;
    }

    public function loadCache(): bool
    {
        if ($data = Cache::read($this->getConfig('cacheKey'), $this->getConfig('cacheConfig'))) {
            $this->rawMenus = $this->_mergeMenuCache($data['menus']);
            return true;
        }
        $this->_rebuildMenus = true;
        return false;
    }

    public function clearCache(): bool
    {
        return Cache::delete($this->getConfig('cacheKey'), $this->getConfig('cacheConfig'));
    }

    public function constructMenu($aro): void
    {
        $aroKey = is_array($aro) ?
            ($aro['role_id'] == 3 ? 'Role3' : ($aro['role_id'] == 2 ? 'Role2' : 'User' . $aro['id'])) :
            'User' . $aro['id'];

        $cacheKey = $aroKey . '_' . $this->getConfig('cacheKey');
        $completeMenu = Cache::read($cacheKey, $this->getConfig('cacheConfig'));

        if (!$completeMenu || $this->_rebuildMenus) {
            $this->generateRawMenus();
            $menu = [];

            foreach ($this->rawMenus as $item) {
                $aco = Inflector::camelize($item['url']['controller']);
                if (isset($item['url']['action'])) {
                    $aco = $this->getConfig('aclPath') . $aco . $this->getConfig('aclSeparator') . $item['url']['action'];
                }

                if ($this->check($aro, $aco)) {
                    $menu[$item['id']] = $item;
                }
            }

            $completeMenu = $this->_formatMenu($menu);
            Cache::write($cacheKey, $completeMenu, $this->getConfig('cacheConfig'));
        }

        $this->menu = $completeMenu;
    }

    public function generateRawMenus(): void
    {
        $cakeAdmin = Configure::read('Routing.prefixes.0');
        $this->createExclusions();

        $controllers = $this->permissions();

        if (!empty($controllers)) {
            foreach ($controllers as $ctrlName => $actions) {
                // In CakePHP 3, we don't need App::import as we use namespaces
                // Controller should be loaded via proper namespace
                $ctrlClass = "App\\Controller\\" . $ctrlName . 'Controller';

                $methods = $actions['action'];

                // Use reflection instead of get_class_vars for better practice in CakePHP 3
                $reflection = new \ReflectionClass($ctrlClass);
                $classVars = $reflection->getDefaultProperties();
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
                        $human = empty($menuOptions['alias']) || !isset($menuOptions['alias'][$action])
                            ? Inflector::humanize(Inflector::underscore($action))
                            : $menuOptions['alias'][$action];

                        $url = [
                            'controller' => $ctrlCamel,
                            'action' => $action
                        ];

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
                    $action = $adminController ? $cakeAdmin . '_index' : 'index';

                    $url = [
                        'controller' => $ctrlCamel,
                        'action' => $action,
                        'admin' => $adminController,
                    ];

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

    public function permissions(): ?array
    {
        // In CakePHP 3, Session is typically accessed through the request object
        return $this->getController()->getRequest()->getSession()->read('reformatePermission');
    }

    public function check($aro, string $aco, string $action = "*"): bool
    {
        if ($aro === null || $aco === null) {
            return false;
        }

        $permissionLists = $this->getController()->getRequest()->getSession()->read('permissionLists');

        if (!empty($permissionLists)) {
            return in_array($aco, $permissionLists);
        }
        return false;
    }

    public function getControllers(): array
    {
        // Configure::listObjects() is removed in CakePHP 3
        // You would typically implement this differently, perhaps using the App class
        // Here's a basic implementation:
        $controllers = [];
        $folder = new \Cake\Filesystem\Folder(APP . 'Controller');
        $files = $folder->find('.*Controller\.php');

        foreach ($files as $file) {
            $controllers[] = str_replace('Controller.php', '', $file);
        }
        return $controllers;
    }

    public function filterMethods(array $methods, array $remove = []): array
    {
        if (!empty($remove)) {
            $remove = array_map('strtolower', $remove);
        }

        $exclusions = array_merge($this->excludedMethods, $remove);

        if (!empty($methods)) {
            foreach ($methods as $k => $method) {
                $method = strtolower($method);

                if (strpos($method, '_') === 0) {
                    unset($methods[$k]);
                    continue;
                }

                if (in_array($method, $exclusions)) {
                    unset($methods[$k]);
                }
            }
        }

        return array_values($methods);
    }

    public function setOptions(array $controllerVars)
    {
        $cakeAdmin = Configure::read('Routing.prefixes.0');
        $menuOptions = $controllerVars['menuOptions'] ?? [];

        $exclude = [
            'view',
            'edit',
            'delete',
            $cakeAdmin . '_edit',
            $cakeAdmin . '_delete',
            $cakeAdmin . '_view'
        ];

        $defaults = [
            'exclude' => $exclude,
            'alias' => [],
            'parent' => $this->getConfig('defaultMenuParent'),
            'controllerButton' => true
        ];

        // Set::merge is removed in CakePHP 3, use array_merge instead
        $menuOptions = array_merge($defaults, $menuOptions);

        if (in_array('*', (array)$menuOptions['exclude'])) {
            return false;
        }

        return $menuOptions;
    }

    public function createExclusions(): void
    {
        $methods = array_merge(
            get_class_methods(\Cake\Controller\Controller::class),
            $this->getConfig('excludeActions')
        );
        $this->excludedMethods = array_map('strtolower', $methods);
    }

    public function addMenu(array $menu): void
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

    public function beforeRender(EventInterface $event)
    {
        $controller = $event->getSubject(); // Get controller instance
        $controller->set('menuoptimized', $this->menu);
    }


    /**
     * Make a Unique Menu item key
     *
     * @param mixed ...$parts Variable number of arguments
     * @return string Unique key name
     */
    protected function _createId(...$parts): string
    {
        if (is_array($parts[0])) {
            $parts = $parts[0];
        }

        return Inflector::variable(implode('-', $parts));
    }

    /**
     * Recursive function to construct Menu
     *
     * @param array $menu Menu items to format
     * @return array Formatted menu structure
     */
    protected function _formatMenu(array $menu): array
    {
        $out = [];
        $idMap = [];

        if (!empty($menu)) {
            foreach ($menu as $item) {
                $item['children'] = [];
                $id = $item['id'];
                $parentId = $item['parent'];

                if (isset($idMap[$id]['children'])) {
                    // am() function isn't available in CakePHP 3, use array_merge instead
                    $idMap[$id] = array_merge($item, $idMap[$id]);
                } else {
                    $idMap[$id] = array_merge($item, ['children' => []]);
                }

                if ($parentId) {
                    $idMap[$parentId]['children'][] = &$idMap[$id];
                } else {
                    $out[] = &$idMap[$id];
                }
            }
        }

        usort($out, [$this, '_sortMenu']);
        return $out;
    }

    /**
     * Sort the menu before returning it. Used with usort()
     *
     * @param array $one First menu item
     * @param array $two Second menu item
     * @return int Comparison result
     */
    protected function _sortMenu(array $one, array $two): int
    {
        if ($one['weight'] === $two['weight']) {
            return 1;
        }
        return ($one['weight'] < $two['weight']) ? -1 : 1;
    }

    /**
     * Merge the Cached menus with the Menus added in Controller::beforeFilter to ensure they are unique
     *
     * @param array $cachedMenus Cached menu items
     * @return array Merged Menus
     */
    protected function _mergeMenuCache(array $cachedMenus): array
    {
        $cacheCount = count($cachedMenus); // sizeOf() replaced with count()
        $currentCount = count($this->rawMenus);
        $tmp = [];

        if ($currentCount > 0) {
            for ($i = 0; $i < $currentCount; $i++) {
                $exist = false;
                $addedMenu = $this->rawMenus[$i];

                if ($cacheCount > 0) {
                    for ($j = 0; $j < $cacheCount; $j++) {
                        if ($addedMenu['id'] === $cachedMenus[$j]['id']) {
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

}
