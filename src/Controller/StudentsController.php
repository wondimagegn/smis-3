<?php
namespace App\Controller;

use Cake\ORM\TableRegistry;
use Cake\Datasource\ConnectionManager;
use Cake\Http\Exception\NotFoundException;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Utility\Security;
use Cake\Utility\Inflector;
use Cake\Filesystem\Folder;
use Cake\Filesystem\File;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Cake\I18n\Time;

use PDO;

class StudentsController extends AppController
{
    public $menuOptions = [
        'parent' => 'placement',
        'exclude' => [
            'add',
            'search',
            'search_profile',
            'name_change',
            'correct_name',
            'profile_not_build_list',
            'get_course_registered_and_add',
            'get_possible_sup_registered_and_add',
            'deleteStudentFromGraduateListForCorrection',
            'activate_deactivate_profile'
        ],
        'alias' => [
            'index' => 'List Admitted Students',
            'department_issue_password' => 'Issue/Reset Password',
            'freshman_issue_password' => 'Issue/Reset Password',
            'name_list' => 'Correct Student Name',
            'id_card_print' => 'Print Student ID Card',
            'move_batch_student_to_department' => 'Move Batch Student to Other Department',
            'admit_all' => 'Admit Accepted Students',
            'mass_import_one_time_passwords' => 'Import One Time Password'
        ]
    ];

    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Paginator');
        $this->loadComponent('Flash');
        $this->loadComponent('AcademicYear');
        $this->loadComponent('EthiopicDateTime');
       // $this->viewBuilder()->setHelpers(['DatePicker', 'Media.Media', 'Xls']);
    }

    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);

        $this->Auth->allow([
            'ajax_get_department',
            'change',
            'get_regions',
            'get_cities',
            'ajax_update',
            'ajax_check_ecardnumber',
            'get_course_registered_and_add',
            'get_possible_sup_registered_and_add',
            'auto_yearlevel_update',
            'student_lists',
            'search',
            'search_profile',
            'get_modal_box',
            'print_record',
            'get_countries',
            'get_zones',
            'get_woredas',
        ]);
    }

    public function beforeRender(\Cake\Event\EventInterface $event)
    {
        parent::beforeRender($event);

        $current_academicyear = $defaultacademicyear = $this->AcademicYear->currentAcademicyear();
        $acyear_array_data = $this->AcademicYear->academicYearInArray(APPLICATION_START_YEAR, explode('/', $current_academicyear)[0]);
        $acYearMinuSeparated = $this->AcademicYear->acYearMinuSeparated(APPLICATION_START_YEAR, explode('/', $current_academicyear)[0] + 1);
        $defaultacademicyearMinusSeparted = str_replace('/', '-', $defaultacademicyear);

        $programsTable = TableRegistry::getTableLocator()->get('Programs');
        $programTypesTable = TableRegistry::getTableLocator()->get('ProgramTypes');

        $programs = $programsTable->find('list')
            ->where(['Programs.id IN' => $this->program_ids, 'Programs.active' => 1])
            ->toArray();
        $programTypes = $programTypesTable->find('list')
            ->where(['ProgramTypes.id IN' => $this->program_type_ids, 'ProgramTypes.active' => 1])
            ->toArray();

        $yearLevels = $this->year_levels;

        if ($this->role_id == ROLE_DEPARTMENT) {
            $yearLevelsTable = TableRegistry::getTableLocator()->get('YearLevels');
            $yearLevels = $yearLevelsTable->find('list')
                ->where(['YearLevels.department_id IN' => $this->department_ids, 'YearLevels.name IN' => $yearLevels])
                ->toArray();
        }

        $this->set(compact('acyear_array_data', 'defaultacademicyear', 'acYearMinuSeparated',
            'programTypes', 'defaultacademicyearMinusSeparted', 'programs', 'yearLevels'));

        if ($this->request->getData('User.password')) {
            $this->request->getData('User.password', null);
        }
    }

    private function _initSearch()
    {
        $session = $this->request->getSession();

        if ($this->request->getData('Search')) {
            $searchSession = $this->request->getData('Search');

            if ($this->request->getData('getacceptedstudent') || $this->request->getData('Search.getacceptedstudent')) {
                $searchSession['getacceptedstudent'] = $this->request->getData('getacceptedstudent') ?? $this->request->getData('Search.getacceptedstudent');
                $this->request->withData('AcceptedStudent.getacceptedstudent', null);
            }

            $session->write('search_data', $searchSession);
        } elseif ($session->check('search_data')) {
            $this->request->withData($session->read('search_data'));

            if ($this->request->getData('Search.getacceptedstudent')) {
                $this->request->withData('getacceptedstudent', $this->request->getData('Search.getacceptedstudent'));
                $this->request->withData('Search.getacceptedstudent', null);
            }
        }
    }
    private function _initSearchIndex()
    {
        $session = $this->request->getSession();

        if ($this->request->getData('Search')) {
            $session->write('search_data_index', $this->request->getData('Search'));
        } elseif ($session->check('search_data_index')) {
            $this->request->withData('Search', $session->read('search_data_index'));
        }
    }

    private function _initClearSessionFilters()
    {
        $session = $this->request->getSession();
        $session->delete('search_data');
        $session->delete('search_data_student');
        $session->delete('search_data_index');
    }

    private function _initSearchStudent()
    {
        $session = $this->request->getSession();

        if ($this->request->getData('Student')) {
            $session->write('search_data_student', $this->request->getData('Student'));
        } elseif ($session->check('search_data_student')) {
            $this->request->withData('Student', $session->read('search_data_student'));
        }

        if ($this->request->getData('Display')) {
            $session->delete('display_field_student');
            $session->write('display_field_student', $this->request->getData('Display'));
        }
    }

    public function search()
    {
        $this->_initSearchStudent();

        $url = ['action' => 'index'];

        $this->request->withData('Display', null);

        if ($this->request->getData()) {
            foreach ($this->request->getData() as $k => $v) {
                if (!empty($v)) {
                    foreach ($v as $kk => $vv) {
                        if (!empty($vv) && is_array($vv)) {
                            foreach ($vv as $kkk => $vvv) {
                                $url[$k . '.' . $kk . '.' . $kkk] = str_replace('/', '-', trim($vvv));
                            }
                        } else {
                            $url[$k . '.' . $kk] = str_replace('/', '-', trim($vv));
                        }
                    }
                }
            }
        }

        return $this->redirect($url);
    }
    public function searchProfile()
    {
        $this->_initSearchStudent();

        $url = ['action' => 'profile_not_build_list'];

        $this->request->getData('Display', null);

        if ($this->request->getData()) {
            foreach ($this->request->getData() as $k => $v) {
                if (!empty($v)) {
                    foreach ($v as $kk => $vv) {
                        if (!empty($vv) && is_array($vv)) {
                            foreach ($vv as $kkk => $vvv) {
                                $url[$k . '.' . $kk . '.' . $kkk] = str_replace('/', '-', trim($vvv));
                            }
                        } else {
                            $url[$k . '.' . $kk] = str_replace('/', '-', trim($vv));
                        }
                    }
                }
            }
        }

        return $this->redirect($url);
    }

    /**
     * Lists students with search and pagination.
     *
     * @param array|null $data Optional search data.
     * @return \Cake\Http\Response|null
     */
    public function index($data = null)
    {
        $session = $this->request->getSession();
        $limit = $this->request->getQuery('Search.limit', 100);
        $name = $this->request->getQuery('Search.name', '');
        $options = [];

        // Handle query parameters from URL
        if ($this->request->getQueryParams()) {
            $this->processQueryParams();
            $this->_initSearchIndex();
        }

        // Handle passed data
        if ($data && !empty($data['Search'])) {
            $this->request->withData('Search', $data['Search']);
            $this->_initSearchIndex();
        }

        // Initialize search if no params or data
        $this->_initSearchIndex();

        // Clear filters on explicit search
        if ($this->request->getData('search')) {
            $this->request->withQueryParams([])->withData([]);
            $this->_initClearSessionFilters();
            $this->_initSearchIndex();
        }

        // Restore page if set
        if ($this->request->getQuery('page') && !$this->request->getData('search')) {
            $this->request->withData('Search.page', $this->request->getQuery('page'));
        }

        // Update limit from request
        if ($this->request->getData('Search.limit')) {
            $limit = (int)$this->request->getData('Search.limit');
        }

        // Initialize tables
        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
        $collegesTable = TableRegistry::getTableLocator()->get('Colleges');

        // Build query conditions based on user role
        $roleId = $session->read('Auth.User.role_id');
        $departments = $colleges = [];


        switch ($roleId) {
            case ROLE_DEPARTMENT:
                $departments = $this->getDepartmentsByIds([$this->department_id], $departmentsTable);
                $options['conditions']['Students.department_id'] = $this->department_id;
                $this->request->withData('Search.department_id', $this->department_id);
                break;

            case ROLE_COLLEGE:
                if (!$this->onlyPre) {
                    $departments = $this->getDepartmentsByCollegeIds($this->college_ids, $departmentsTable);
                }
                $options['conditions']['Students.college_id IN'] = $this->college_ids;
                if ($this->request->getData('Search.department_id')) {
                    $options['conditions']['Students.department_id'] = $this->request->getData('Search.department_id');
                }
                $this->request->withData('Search.college_id', $this->college_id);
                break;

            case ROLE_REGISTRAR:
                if (!empty($this->department_ids)) {
                    $departments = $this->getDepartmentsByIds($this->department_ids, $departmentsTable);
                    $options['conditions']['Students.department_id IN'] = $this->department_ids;
                    if ($this->request->getData('Search.department_id')) {
                        $options['conditions']['Students.department_id'] = $this->request->getData('Search.department_id');
                    }
                } elseif (!empty($this->college_ids)) {
                    $colleges = $this->getCollegesByIds($this->college_ids, $collegesTable);
                    $options['conditions']['Students.college_id IN'] = $this->college_ids;
                    $options['conditions']['Students.department_id IS'] = null;
                    if ($this->request->getData('Search.college_id')) {
                        $options['conditions']['Students.college_id'] = $this->request->getData('Search.college_id');
                    }
                }
                $options['conditions']['Students.program_id IN'] = $this->program_ids;
                $options['conditions']['Students.program_type_id IN'] = $this->program_type_ids;
                break;

            case ROLE_STUDENT:
                $this->request->withQueryParams([])->withData([]);
                return $this->redirect(['action' => 'index']);

            default:
                $departments = $this->getAllActiveDepartments($departmentsTable);
                $colleges = $this->getAllActiveColleges($collegesTable);
                $this->applyDefaultConditions($options, $departmentsTable);
                break;
        }

        // Apply search filters
        $this->applySearchFilters($options, $name);

        // Query students
        $students = [];
        if (!empty($options['conditions'])) {

            $query = $studentsTable->find()
                ->where($options['conditions'])
                ->contain([
                    'Departments' => ['fields' => ['id', 'name', 'shortname', 'college_id', 'institution_code']],
                    'Colleges' => [
                        'fields' => ['id', 'name', 'shortname', 'institution_code', 'campus_id'],
                        'Campuses' => ['fields' => ['id', 'name', 'campus_code']]
                    ],
                    'Programs' => ['fields' => ['id', 'name', 'shortname']],
                    'AcceptedStudents' => ['fields' => ['id']],
                    'ProgramTypes' => ['fields' => ['id', 'name', 'shortname']],
                    'Curriculums' => ['fields' => ['id', 'name', 'year_introduced', 'type_credit', 'english_degree_nomenclature', 'active']],
                    'Specializations' => ['fields' => ['id', 'name']],
                    'Regions' => ['fields' => ['id', 'name', 'short']],
                    'Zones' => ['fields' => ['id', 'name', 'short']],
                    'Woredas' => ['fields' => ['id', 'name', 'code']],
                    'Cities' => ['fields' => ['id', 'name', 'short']]
                ])
                ->order([
                    'Students.admissionyear' => 'DESC',
                    'Students.department_id' => 'ASC',
                    'Students.program_type_id' => 'ASC',
                    'Students.studentnumber' => 'ASC',
                    'Students.first_name' => 'ASC',
                    'Students.middle_name' => 'ASC',
                    'Students.last_name' => 'ASC',
                    'Students.created' => 'DESC'
                ]);


            $this->paginate = [
                'limit' => $limit,
                'maxLimit' => $limit,
                'page' => $this->request->getData('Search.page', 1),
                'sort' => $this->request->getData('Search.sort'),
                'direction' => $this->request->getData('Search.direction')
            ];

            try {
                $students = $this->paginate($query);
                if (!empty($students)) {
                    $session->delete('students');
                    $session->write('students', $students->toArray());
                }
            } catch (NotFoundException $e) {
                $this->request->withData('Search', [
                    'page' => null,
                    'sort' => null,
                    'direction' => null
                ])->withData('Student', [
                    'page' => null,
                    'sort' => null,
                    'direction' => null
                ])->withQueryParams([]);
                $this->_initSearchIndex();
                return $this->redirect(['action' => 'index']);
            }
        }

        // Set flash message if no results
        $turn_off_search = empty($students) && !empty($options['conditions']) ? false : true;
        if (empty($students) && !empty($options['conditions'])) {
            $this->Flash->info(__('No Student is found with the given search criteria.'));
        }

        // Set view variables
        $this->set(compact('students', 'colleges', 'departments', 'turn_off_search', 'limit', 'name'));
    }

    /**
     * Processes query parameters for search filters.
     */
    private function processQueryParams()
    {
        $searchParams = [
            'limit', 'name', 'department_id', 'college_id', 'academicyear',
            'gender', 'program_id', 'program_type_id', 'status', 'page',
            'sort', 'direction'
        ];

        foreach ($searchParams as $param) {
            if ($value = $this->request->getQuery("Search.$param")) {
                if ($param === 'name' || $param === 'academicyear') {
                    $value = str_replace('-', '/', trim($value));
                }
                $this->request->withData("Search.$param", $value);
            }
        }
    }

    /**
     * Retrieves departments by IDs.
     *
     * @param array $ids Department IDs
     * @param \Cake\ORM\Table $departmentsTable Departments table
     * @return array
     */
    private function getDepartmentsByIds(array $ids, $departmentsTable): array
    {
        return $departmentsTable->find('list')
            ->where(['Departments.id IN' => $ids, 'Departments.active' => 1])
            ->toArray();
    }

    /**
     * Retrieves departments by college IDs.
     *
     * @param array $collegeIds College IDs
     * @param \Cake\ORM\Table $departmentsTable Departments table
     * @return array
     */
    private function getDepartmentsByCollegeIds(array $collegeIds, $departmentsTable): array
    {
        return $departmentsTable->find('list')
            ->where(['Departments.college_id IN' => $collegeIds, 'Departments.active' => 1])
            ->toArray();
    }

    /**
     * Retrieves colleges by IDs.
     *
     * @param array $ids College IDs
     * @param \Cake\ORM\Table $collegesTable Colleges table
     * @return array
     */
    private function getCollegesByIds(array $ids, $collegesTable): array
    {
        return $collegesTable->find('list')
            ->where(['Colleges.id IN' => $ids, 'Colleges.active' => 1])
            ->toArray();
    }

    /**
     * Retrieves all active departments.
     *
     * @param \Cake\ORM\Table $departmentsTable Departments table
     * @return array
     */
    private function getAllActiveDepartments($departmentsTable): array
    {
        return $departmentsTable->find('list')
            ->where(['Departments.active' => 1])
            ->toArray();
    }

    /**
     * Retrieves all active colleges.
     *
     * @param \Cake\ORM\Table $collegesTable Colleges table
     * @return array
     */
    private function getAllActiveColleges($collegesTable): array
    {
        return $collegesTable->find('list')
            ->where(['Colleges.active' => 1])
            ->toArray();
    }

    /**
     * Applies default conditions for non-specific roles.
     *
     * @param array &$options Query options
     * @param \Cake\ORM\Table $departmentsTable Departments table
     */
    private function applyDefaultConditions(array &$options, $departmentsTable)
    {
        if ($this->request->getData('Search.department_id')) {
            $options['conditions']['Students.department_id'] = $this->request->getData('Search.department_id');
        } elseif ($this->request->getData('Search.college_id')) {
            $options['conditions']['Students.college_id'] = $this->request->getData('Search.college_id');
            $this->request->withData('Search.departments', $departmentsTable->find('list')
                ->where(['Departments.college_id' => $this->request->getData('Search.college_id'), 'Departments.active' => 1])
                ->toArray());
        } else {
            if (!empty($this->department_ids) && !empty($this->college_ids)) {
                $options['conditions']['OR'] = [
                    'Students.department_id IN' => $this->department_ids,
                    'Students.college_id IN' => $this->college_ids
                ];
            } elseif (!empty($this->department_ids)) {
                $options['conditions']['Students.department_id IN'] = $this->department_ids;
            } elseif (!empty($this->college_ids)) {
                $options['conditions']['Students.college_id IN'] = $this->college_ids;
            }
        }
    }

    /**
     * Applies search filters to query options.
     *
     * @param array &$options Query options
     * @param string $name Search name
     */
    private function applySearchFilters(array &$options, string $name)
    {

        if ($selectedAcademicYear = $this->request->getData('Search.academicyear')) {
            $options['conditions']['Students.academicyear'] = $selectedAcademicYear;
        }

        if ($programId = $this->request->getData('Search.program_id')) {
            $options['conditions']['Students.program_id'] = $programId;
        } elseif ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR && !empty($this->program_ids)) {
            $options['conditions']['Students.program_id IN'] = $this->program_ids;
        }

        if ($programTypeId = $this->request->getData('Search.program_type_id')) {
            $options['conditions']['Students.program_type_id'] = $programTypeId;
        } elseif ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR && !empty($this->program_type_ids)) {
            $options['conditions']['Students.program_type_id IN'] = $this->program_type_ids;
        }

        if ($name) {
            $options['conditions']['OR'] = [
                'Students.first_name LIKE' => "%$name%",
                'Students.middle_name LIKE' => "%$name%",
                'Students.last_name LIKE' => "%$name%",
                'Students.studentnumber LIKE' => "$name%"
            ];
        }

        if ($gender = $this->request->getData('Search.gender')) {
            $options['conditions']['Students.gender LIKE'] = $gender;
        }

        if ($status = $this->request->getData('Search.status')) {
            $options['conditions']['Students.graduated'] = $status;
        }

        // Default conditions
        if (empty($options['conditions'])) {
            $options['conditions'] = [
                'Students.id IS NOT NULL',
                'Students.graduated' => 0
            ];
        }

    }

    public function view($student_id = null)
    {
        if (!$student_id) {
            $this->Flash->error(__('Invalid student'));
            return $this->redirect(['action' => 'index']);
        }

        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $student_id_to_check = $this->role_id == ROLE_STUDENT ? $this->student_id : $student_id;

        $check_student_admitted = $studentsTable->find()
            ->where(['Students.id' => $student_id_to_check])
            ->count();

        if ($check_student_admitted == 0) {
            $this->Flash->info(__('You Student ID Not Found.'));
            return $this->redirect(['action' => 'index']);
        }

        $studentDetail = $studentsTable->find()
            ->where(['Students.id' => $student_id_to_check])
            ->contain([
                'Users',
                'AcceptedStudents',
                'Programs',
                'ProgramTypes',
                'Contacts',
                'Countries',
                'Regions',
                'Zones',
                'Woredas',
                'Cities',
                'Departments',
                'Colleges',
                'EslceResults',
                'EheeceResults',
                'Attachments',
                'HigherEducationBackgrounds',
                'HighSchoolEducationBackgrounds',
                'GraduateLists'
            ])
            ->first();

        if (!empty($studentDetail->department) && $studentDetail->department->is_name_changed) {
            $department_id_to_check = $studentDetail->department->id ?? $studentDetail->department_id;
            $date_to_check = $studentDetail->graduate_list->graduate_date ?? ($studentDetail->admissionyear ?? date('Y-m-d'));
            $date_to_check = strtotime($date_to_check) !== false ? $date_to_check : date('Y-m-d');
            $academic_year_to_check = $studentDetail->academicyear ?? $this->AcademicYear->currentAcademicyear();

            $departmentNameChangeTable = TableRegistry::getTableLocator()->get('DepartmentNameChanges');
            $getDepartmentNameChangeIfExists = $departmentNameChangeTable->getDepartmentNameChangeIfExists($department_id_to_check, $date_to_check, $academic_year_to_check);

            if (!empty($getDepartmentNameChangeIfExists['Department'])) {
                $studentDetail->department = $getDepartmentNameChangeIfExists['Department'];
            }
        }

        if (empty($this->request->getData())) {
            $this->request->withData($studentsTable->find()
                ->where(['Students.id' => $student_id_to_check])
                ->contain([
                    'Users',
                    'AcceptedStudents',
                    'Programs',
                    'ProgramTypes',
                    'Contacts',
                    'Countries',
                    'Regions',
                    'Zones',
                    'Woredas',
                    'Cities',
                    'Departments',
                    'Colleges',
                    'EslceResults',
                    'EheeceResults',
                    'Attachments',
                    'HigherEducationBackgrounds',
                    'HighSchoolEducationBackgrounds'
                ])
                ->first()
                ->toArray());
        }

        $this->request->withData('Student.gender', strtolower($this->request->getData('AcceptedStudent.sex') ?? $this->request->getData('Student.gender')));

        $regions = $studentsTable->Regions->find('list')->toArray();
        $countries = $studentsTable->Countries->find('list')->toArray();
        $cities = $studentsTable->Cities->find('list')->toArray();
        $zones = $studentsTable->Zones->find('list')->toArray();
        $woredas = $studentsTable->Woredas->find('list')->toArray();
        $colleges = $collegesTable->find('list')
            ->where(['Colleges.id' => $studentDetail->college_id])
            ->toArray();
        $departments = !empty($studentDetail->department_id) && is_numeric($studentDetail->department_id) && $studentDetail->department_id > 0
            ? $departmentsTable->find('list')
                ->where(['Departments.id' => $studentDetail->department_id])
                ->toArray()
            : [];
        $contacts = $studentsTable->Contacts->find('list')
            ->where(['Contacts.student_id' => $this->student_id])
            ->toArray();
        $users = $studentsTable->Users->find('list')
            ->where(['Users.username' => $studentDetail->studentnumber])
            ->toArray();
        $programs = $programsTable->find('list')
            ->where(['Programs.id' => $studentDetail->program_id])
            ->toArray();
        $programTypes = $programTypesTable->find('list')
            ->where(['ProgramTypes.id' => $studentDetail->program_type_id])
            ->toArray();

        $this->set(compact('studentDetail', 'contacts', 'users', 'colleges', 'departments', 'programs', 'programTypes', 'regions', 'countries', 'zones', 'woredas', 'cities'));
    }

    public function edit($id = null)
    {
        if (!$id) {
            $this->Flash->error(__('Invalid Student ID'));
            return $this->redirect(['action' => 'index']);
        }

        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $check_student_id = $studentsTable->find()
            ->where(['Students.id' => $id])
            ->count();

        if (!$check_student_id) {
            $this->Flash->error(__('Invalid Student ID'));
            return $this->redirect(['action' => 'index']);
        }

        if ($this->Auth->user('role_id') != ROLE_REGISTRAR) {
            $this->Flash->error(__('You are not eligible to edit any student records. This incident will be reported to system administrators. Please don\'t try this again.'));
            return $this->redirect(['action' => 'index']);
        }

        $check_eligibility_to_edit = 0;
        if (!empty($this->department_ids)) {
            $check_eligibility_to_edit = $studentsTable->find()
                ->where(['Students.department_id IN' => $this->department_ids, 'Students.id' => $id])
                ->count();
        } elseif (!empty($this->college_ids)) {
            $check_eligibility_to_edit = $studentsTable->find()
                ->where(['Students.college_id IN' => $this->college_ids, 'Students.id' => $id])
                ->count();
        }

        if ($check_eligibility_to_edit == 0) {
            $this->Flash->error(__('You are not eligible to edit the selected student profile. This happens when you are trying to edit a student\'s profile which you are not assigned to edit.'));
            return $this->redirect(['action' => 'index']);
        }

        $studentDetail = $studentsTable->find()
            ->where(['Students.id' => $id])
            ->contain([
                'Users',
                'AcceptedStudents',
                'Programs',
                'ProgramTypes',
                'Contacts',
                'Departments',
                'Colleges',
                'EslceResults',
                'EheeceResults',
                'Attachments',
                'HigherEducationBackgrounds',
                'HighSchoolEducationBackgrounds',
                'Countries',
                'Regions',
                'Cities',
                'Zones',
                'Woredas',
                'GraduateLists'
            ])
            ->first();

        if (!empty($studentDetail->department) && $studentDetail->department->is_name_changed) {
            $department_id_to_check = $studentDetail->department->id ?? $studentDetail->department_id;
            $date_to_check = $studentDetail->graduate_list->graduate_date ?? ($studentDetail->admissionyear ?? date('Y-m-d'));
            $date_to_check = strtotime($date_to_check) !== false ? $date_to_check : date('Y-m-d');
            $academic_year_to_check = $studentDetail->academicyear ?? $this->AcademicYear->current_academicyear();

            $departmentNameChangeTable = TableRegistry::getTableLocator()->get('DepartmentNameChanges');
            $getDepartmentNameChangeIfExists = $departmentNameChangeTable->getDepartmentNameChangeIfExists($department_id_to_check, $date_to_check, $academic_year_to_check);

            if (!empty($getDepartmentNameChangeIfExists['Department'])) {
                $studentDetail->department = $getDepartmentNameChangeIfExists['Department'];
            }
        }

        $student_admission_year = (int)($studentDetail->accepted_student->academicyear
            ? explode('/', $studentDetail->accepted_student->academicyear)[0]
            : ($studentDetail->academicyear
                ? explode('/', $studentDetail->academicyear)[0]
                : explode('/', $this->AcademicYear->current_academicyear())[0]));

        $studentStatusPatternTable = TableRegistry::getTableLocator()->get('StudentStatusPatterns');
        $isGraduatingClassStudent = $studentStatusPatternTable->isEligibleForExitExam($id);

        if ($this->request->is(['post', 'put']) && $this->request->getData('updateStudentDetail')) {
            $this->request->getData('User', null);
            $this->request->getData('AcceptedStudent', null);
            $this->request->getData('College', null);
            $this->request->getData('GraduateList', null);
            $this->request->getData('Department', null);

            if ($this->Auth->user('is_admin') == 1) {
                if (strcasecmp(trim($studentDetail->accepted_student->sex), trim($this->request->getData('Student.gender'))) != 0) {
                    if ($studentDetail->accepted_student->id) {
                        $acceptedStudentsTable = TableRegistry::getTableLocator()->get('AcceptedStudents');
                        $acceptedStudent = $acceptedStudentsTable->get($studentDetail->accepted_student->id);
                        $acceptedStudent->sex = strtolower(trim($this->request->getData('Student.gender')));
                        $acceptedStudentsTable->save($acceptedStudent);
                    }
                } else {
                    $this->request->withData('Student.gender', $this->_normalizeGender($studentDetail->accepted_student->sex));
                }
            } else {
                $this->request->withData('Student.gender', $this->_normalizeGender($studentDetail->accepted_student->sex));
            }

            if (!empty($studentDetail->user->username) && !empty($this->request->getData('Student.email'))) {
                $this->request->withData('User.email', trim($this->request->getData('Student.email')));
                if ($studentDetail->user_id) {
                    $this->request->withData('User.id', $studentDetail->user_id);
                } else {
                    $usersTable = TableRegistry::getTableLocator()->get('Users');
                    $student_user_id = $usersTable->find()
                        ->select(['id'])
                        ->where(['Users.username LIKE' => $studentDetail->studentnumber, 'Users.role_id' => ROLE_STUDENT])
                        ->first();

                    if ($student_user_id) {
                        $this->request->withData('User.id', $student_user_id->id);
                    }
                }
            }

            if (!empty($this->request->getData('Student.phone_mobile')) && !empty($this->request->getData('Student.email'))) {
                $this->request->withData($studentsTable->unsetEmpty($this->request->getData()->toArray()));

                if (empty($this->request->getData('Student.city_id'))) {
                    $this->request->getData('Student.city_id', null);
                }

                if ($this->request->getData('Contact') && (
                        empty($this->request->getData('Contact.0.first_name')) ||
                        empty($this->request->getData('Contact.0.middle_name')) ||
                        empty($this->request->getData('Contact.0.last_name')) ||
                        empty($this->request->getData('Contact.0.phone_mobile'))
                    )) {
                    $this->request->getData('Contact', null);
                }

                if ($this->request->getData('Attachment') && (
                        empty($this->request->getData('Attachment.0.file.name')) ||
                        $this->request->getData('Attachment.0.file.error')
                    )) {
                    $this->request->getData('Attachment', null);
                }

                if ($this->request->getData('HighSchoolEducationBackground') && (
                        empty($this->request->getData('HighSchoolEducationBackground.0.name')) ||
                        empty($this->request->getData('HighSchoolEducationBackground.0.town')) ||
                        empty($this->request->getData('HighSchoolEducationBackground.0.region_id'))
                    )) {
                    $this->request->getData('HighSchoolEducationBackground', null);
                }

                if ($this->request->getData('HigherEducationBackground') && (
                        empty($this->request->getData('HigherEducationBackground.0.name')) ||
                        empty($this->request->getData('HigherEducationBackground.0.field_of_study')) ||
                        empty($this->request->getData('HigherEducationBackground.0.diploma_awarded')) ||
                        empty($this->request->getData('HigherEducationBackground.0.cgpa_at_graduation'))
                    )) {
                    $this->request->getData('HigherEducationBackground', null);
                }

                if ($this->request->getData('EheeceResult') && (
                        empty($this->request->getData('EheeceResult.0.subject')) ||
                        empty($this->request->getData('EheeceResult.0.mark'))
                    )) {
                    $this->request->getData('EheeceResult', null);
                }

                if ($this->request->getData('EslceResult') && (
                        empty($this->request->getData('EslceResult.0.subject')) ||
                        empty($this->request->getData('EslceResult.0.grade')) ||
                        empty($this->request->getData('EslceResult.0.exam_year'))
                    )) {
                    $this->request->getData('EslceResult', null);
                }

                $this->request->getData('updateStudentDetail', null);

                $student = $studentsTable->patchEntity($studentsTable->get($id), $this->request->getData(), ['validate' => 'first']);
                if ($studentsTable->save($student)) {
                    $this->Flash->success(__('Student Profile has been updated.'));
                    return $this->redirect(['action' => 'index']);
                } else {
                    $this->Flash->error(__('Student profile could not be saved. Please, try again.'));
                }
            } else {
                $emailPlaceholder = strtolower(str_replace('/', '.', $studentDetail->studentnumber)) . INSTITUTIONAL_EMAIL_SUFFIX;
                if (empty($this->request->getData('Student.phone_mobile')) && empty($this->request->getData('Student.email'))) {
                    $this->Flash->error(__('Please provide student mobile phone number and personal email address. You can use {0} if the student doesn\'t have a personal email address like Gmail, Yahoo, Hotmail, etc.', $emailPlaceholder));
                } elseif (empty($this->request->getData('Student.phone_mobile'))) {
                    $this->Flash->error(__('Please provide your mobile phone number.'));
                } else {
                    $this->Flash->error(__('Please provide student personal email address. You can use {0} if the student doesn\'t have a personal email address like Gmail, Yahoo, Hotmail, etc.', $emailPlaceholder));
                }
            }
        }

        if (empty($this->request->getData())) {
            $this->request->withData($studentsTable->find()
                ->where(['Students.id' => $id])
                ->contain([
                    'Users',
                    'AcceptedStudents',
                    'Programs',
                    'ProgramTypes',
                    'Departments',
                    'Colleges',
                    'Contacts',
                    'EslceResults',
                    'EheeceResults',
                    'Attachments',
                    'HigherEducationBackgrounds',
                    'HighSchoolEducationBackgrounds',
                    'Countries',
                    'Regions',
                    'Cities',
                    'Zones',
                    'Woredas'
                ])
                ->first()
                ->toArray());

            $this->request->withData('Student.gender', $this->_normalizeGender($studentDetail->accepted_student->sex));

            if (!empty($this->request->getData('EheeceResult.0.exam_year')) && !$this->AcademicYear->isValidDateWithinYearRange($this->request->getData('EheeceResult.0.exam_year'), $student_admission_year - 10, $student_admission_year)) {
                $require_update = true;
                $require_update_fields[$rupdt_key]['field'] = 'EHEECE Exam Taken Date';
                $require_update_fields[$rupdt_key]['previous_value'] = $this->request->getData('EheeceResult.0.exam_year');
                $this->request->withData('EheeceResult.0.exam_year', $student_admission_year . '-07-01');
                $require_update_fields[$rupdt_key]['auto_corrected_value'] = $this->request->getData('EheeceResult.0.exam_year');
                $require_update_fields[$rupdt_key]['reason'] = 'EHEECE Exam Taken Date is not a valid date.';

                if ((int)explode('-', $studentDetail->eheece_result[0]->exam_year)[0] > $student_admission_year) {
                    $require_update_fields[$rupdt_key]['reason'] = 'EHEECE Exam Taken Date can\'t be after Student Admission Year.';
                }

                $rupdt_key++;
            } elseif (empty($studentDetail->eheece_result)) {
                $this->request->withData('EheeceResult.0.exam_year', $student_admission_year . '-07-01');
            }

            $maximum_estimated_graduation_year_limit = $student_admission_year;

            if ($studentDetail->program_id == PROGRAM_UNDERGRADUATE || $studentDetail->program_id == PROGRAM_PHD) {
                $maximum_estimated_graduation_year_limit = $student_admission_year + 6;
            } elseif ($studentDetail->program_id == PROGRAM_POST_GRADUATE) {
                $maximum_estimated_graduation_year_limit = $studentDetail->program_type_id == PROGRAM_TYPE_REGULAR
                    ? $student_admission_year + 3
                    : $student_admission_year + 6;
            }

            if (!empty($studentDetail->curriculum_id) && $studentDetail->curriculum_id > 0) {
                $coursesTable = TableRegistry::getTableLocator()->get('Courses');
                $get_curriculum_year_level_count = $coursesTable->find()
                    ->where(['Courses.curriculum_id' => $studentDetail->curriculum_id])
                    ->group(['Courses.year_level_id'])
                    ->count();

                if ($studentDetail->program_id == PROGRAM_UNDERGRADUATE || $studentDetail->program_type_id != PROGRAM_TYPE_REGULAR) {
                    if ($get_curriculum_year_level_count) {
                        $maximum_estimated_graduation_year_limit = $student_admission_year + ($get_curriculum_year_level_count * 2);
                    }
                }

                if (!empty($this->request->getData('Student.estimated_grad_date')) && !$this->AcademicYear->isValidDateWithinYearRange($this->request->getData('Student.estimated_grad_date'), $student_admission_year, $student_admission_year + ($get_curriculum_year_level_count * 2))) {
                    $require_update = true;
                    $require_update_fields[$rupdt_key]['field'] = 'Estimated Graduation Date';
                    $require_update_fields[$rupdt_key]['previous_value'] = $this->request->getData('Student.estimated_grad_date');
                    $this->request->withData('Student.estimated_grad_date', ($student_admission_year + $get_curriculum_year_level_count) . '-08-01');
                    $require_update_fields[$rupdt_key]['auto_corrected_value'] = $this->request->getData('Student.estimated_grad_date');
                    $require_update_fields[$rupdt_key]['reason'] = 'Estimated Graduation Date is not a valid date.';

                    if ((int)explode('-', $studentDetail->estimated_grad_date)[0] > ($student_admission_year + ($get_curriculum_year_level_count * 2))) {
                        $require_update_fields[$rupdt_key]['reason'] = 'Estimated Graduation Date can\'t be after ' . ($student_admission_year + ($get_curriculum_year_level_count * 2)) . ' G.C. (Double of student\'s attached curriculum year levels, ' . $get_curriculum_year_level_count . ' X 2 years)';
                    } elseif ((int)explode('-', $studentDetail->estimated_grad_date)[0] < ($student_admission_year + $get_curriculum_year_level_count)) {
                        $require_update_fields[$rupdt_key]['reason'] = 'Estimated Graduation Date can\'t be before ' . ($student_admission_year + $get_curriculum_year_level_count) . ' G.C.';
                    }

                    $rupdt_key++;
                } elseif (empty($studentDetail->estimated_grad_date)) {
                    $this->request->withData('Student.estimated_grad_date', ($student_admission_year + $get_curriculum_year_level_count) . '-08-01');
                }
            } elseif (empty($studentDetail->estimated_grad_date) || is_null($studentDetail->estimated_grad_date)) {
                $this->request->withData('Student.estimated_grad_date', $maximum_estimated_graduation_year_limit . '-08-01');
            }
        }

        $foreign_students_region_ids = $studentsTable->Regions->find('list')
            ->where(['Regions.country_id !=' => COUNTRY_ID_OF_ETHIOPIA])
            ->toArray();

        $regions = [];
        $zones = [];
        $woredas = [];
        $cities = [];
        $foreign_student = 0;
        $country_id_of_region = COUNTRY_ID_OF_ETHIOPIA;
        $region_id_of_student = '';

        if ($studentDetail->accepted_student->region_id || $studentDetail->region_id) {
            $region_id_of_student = $studentDetail->accepted_student->region_id ?? $studentDetail->region_id;
            $country_id_of_region = $studentsTable->Regions->find()
                ->select(['country_id'])
                ->where(['Regions.id' => $region_id_of_student])
                ->first()
                ->country_id;

            $countries = $studentsTable->Countries->find('list')
                ->where(['Countries.id' => $country_id_of_region])
                ->toArray();
            $regions = $studentsTable->Regions->find('list')
                ->where(['Regions.id' => $region_id_of_student, 'Regions.country_id' => $country_id_of_region])
                ->toArray();
            $zones = $studentsTable->Zones->find('list')
                ->where(['Zones.region_id' => $region_id_of_student])
                ->toArray();
            $city_zone_ids = $studentsTable->Cities->find('list')
                ->where(['Cities.region_id' => $region_id_of_student])
                ->select(['zone_id'])
                ->toArray();
            $woredas = $studentsTable->Woredas->find('list')
                ->where(['Woredas.zone_id IN' => (!empty($zones) ? array_keys($zones) : $city_zone_ids)])
                ->toArray();
            $cities = $studentsTable->Cities->find('list')
                ->where([
                    'OR' => [
                        'Cities.id' => $studentDetail->city_id,
                        'Cities.zone_id IN' => (!empty($zones) ? array_keys($zones) : ($studentDetail->accepted_student->zone_id ?? $studentDetail->zone_id)),
                        'Cities.region_id' => $region_id_of_student
                    ]
                ])
                ->toArray();
        } else {
            $countries = $studentsTable->Countries->find('list')->toArray();
            $regions = $studentsTable->Regions->find('list')
                ->where(['Regions.active' => 1])
                ->toArray();
            $zones = $studentsTable->Zones->find('list')
                ->where(['Zones.active' => 1])
                ->toArray();
            $woredas = $studentsTable->Woredas->find('list')
                ->where(['Woredas.active' => 1])
                ->toArray();
            $cities = $studentsTable->Cities->find('list')
                ->where(['Cities.active' => 1])
                ->toArray();
        }

        if (empty($regions)) {
            $regions = $studentsTable->Regions->find('list')
                ->where(['Regions.country_id' => $country_id_of_region])
                ->toArray();
        }

        if (empty($zones)) {
            $zones = $studentsTable->Zones->find('list')->toArray();
        }

        if (empty($woredas)) {
            $woredas = $studentsTable->Woredas->find('list')->toArray();
        }

        if (empty($cities)) {
            $cities = $studentsTable->Cities->find('list')
                ->where(['Cities.region_id' => $region_id_of_student ?: array_keys($regions)])
                ->toArray();
        }

        if (!empty($foreign_students_region_ids) && (
                ($studentDetail->accepted_student->region_id && in_array($studentDetail->accepted_student->region_id, $foreign_students_region_ids)) ||
                ($studentDetail->region_id && in_array($studentDetail->region_id, $foreign_students_region_ids))
            )) {
            $foreign_student = 1;
        }

        $colleges = $studentsTable->Colleges->find('list')
            ->where(['Colleges.id' => $studentDetail->college_id])
            ->toArray();
        $departments = !empty($studentDetail->department_id) && is_numeric($studentDetail->department_id) && $studentDetail->department_id > 0
            ? $studentsTable->Departments->find('list')
                ->where(['Departments.id' => $studentDetail->department_id])
                ->toArray()
            : [];
        $regionsAll = $studentsTable->Regions->find('list')
            ->where(['Regions.active' => 1, 'Regions.country_id' => $country_id_of_region])
            ->toArray();
        $zonesAll = $studentsTable->Zones->find('list')
            ->where(['Zones.active' => 1])
            ->toArray();
        $woredasAll = $studentsTable->Woredas->find('list')
            ->where(['Woredas.active' => 1])
            ->toArray();
        $citiesAll = $studentsTable->Cities->find('list')
            ->where(['Cities.active' => 1])
            ->toArray();

        if ($this->request->getData('Contact.0.region_id')) {
            $citiesAll = $studentsTable->Cities->find('list')
                ->where(['Cities.region_id' => $this->request->getData('Contact.0.region_id'), 'Cities.active' => 1])
                ->toArray();
        }

        $contacts = $studentsTable->Contacts->find('list')
            ->where(['Contacts.student_id' => $this->student_id])
            ->toArray();
        $users = $studentsTable->Users->find('list')
            ->where(['Users.username' => $studentDetail->studentnumber])
            ->toArray();
        $programs = $studentsTable->Programs->find('list')
            ->where(['Programs.id' => $studentDetail->program_id])
            ->toArray();
        $programTypes = $studentsTable->ProgramTypes->find('list')
            ->where(['ProgramTypes.id' => $studentDetail->program_type_id])
            ->toArray();

        $studentDetail->country_id = $country_id_of_region;

        $student_mobile_phone_number_error = '';
        if (!empty($this->request->getData('Student.phone_mobile')) && empty($this->_formatEthiopianPhoneNumber($this->request->getData('Student.phone_mobile')))) {
            $student_mobile_phone_number_error = 'The provided student mobile phone number ' . $this->request->getData('Student.phone_mobile') . ' is not a valid mobile phone number. Please update that.';
        }

        $this->set(compact(
            'studentDetail', 'contacts', 'users', 'colleges', 'departments', 'programs', 'programTypes',
            'countries', 'regions', 'zones', 'woredas', 'cities', 'regionsAll', 'zonesAll', 'woredasAll', 'citiesAll',
            'foreign_student', 'student_mobile_phone_number_error', 'require_update', 'require_update_fields',
            'student_admission_year', 'maximum_estimated_graduation_year_limit', 'isGraduatingClassStudent'
        ));
    }

    public function admitAll()
    {
        $this->_initSearch();
        $last_success_message = '';
        $session = $this->request->getSession();

        if ($this->request->is('post') && !empty($this->request->getData('admit'))) {
            $data['Search'] = $this->request->getData('Search');
            $atleast_select_one = array_sum($this->request->getData('AcceptedStudent.approve', []));

            if ($atleast_select_one > 0) {
                $this->request->getData('Student.SelectAll', null);
                $admittedStudentsLists = [];
                $selectedAdmittedCount = 0;
                $selected_students = [];

                foreach ($this->request->getData('AcceptedStudent.approve', []) as $id => $selected) {
                    if ($selected == 1) {
                        $selected_students[] = $id;
                        $studentsTable = TableRegistry::getTableLocator()->get('Students');
                        $basicData = $studentsTable->AcceptedStudents->find()
                            ->where(['AcceptedStudents.id' => $id])
                            ->first();
                        $checkForAcceptedDuplication = $studentsTable->find()
                            ->where([
                                'OR' => [
                                    'Students.accepted_student_id' => $basicData->id,
                                    'Students.studentnumber' => $basicData->studentnumber
                                ]
                            ])
                            ->first();

                        if ($basicData && !$checkForAcceptedDuplication) {
                            $admittedStudentsLists['Student'][$selectedAdmittedCount] = [
                                'first_name' => $basicData->first_name,
                                'middle_name' => $basicData->middle_name,
                                'last_name' => $basicData->last_name,
                                'user_id' => $basicData->user_id,
                                'accepted_student_id' => $basicData->id,
                                'gender' => $basicData->sex,
                                'studentnumber' => $basicData->studentnumber,
                                'country_id' => $studentsTable->Regions->find()
                                    ->select(['country_id'])
                                    ->where(['Regions.id' => $basicData->region_id])
                                    ->first()
                                    ->country_id,
                                'region_id' => $basicData->region_id,
                                'program_id' => $basicData->program_id,
                                'college_id' => $basicData->college_id,
                                'original_college_id' => $basicData->college_id,
                                'department_id' => $basicData->department_id,
                                'program_type_id' => $basicData->program_type_id,
                                'curriculum_id' => $basicData->curriculum_id,
                                'high_school' => $basicData->high_school,
                                'moeadmissionnumber' => $basicData->moeadmissionnumber,
                                'benefit_group' => $basicData->benefit_group,
                                'academicyear' => $basicData->academicyear,
                                'admissionyear' => $basicData->created
                            ];
                        }

                        $selectedAdmittedCount++;
                    }
                }

                $studentsTable = TableRegistry::getTableLocator()->get('Students');
                $entities = $studentsTable->newEntities($admittedStudentsLists['Student'], ['validate' => 'first']);
                if ($studentsTable->saveMany($entities)) {
                    $last_success_message = 'All selected ' . count($admittedStudentsLists['Student']) . ' student(s) are admitted Successfully.';
                    $this->Flash->success($last_success_message);

                    $this->request->withData([]);
                    $this->request->withData('Search', $data['Search']);
                    $this->request->withData('getacceptedstudent', true);

                    $this->_initClearSessionFilters();
                    $session->write('search_data', $this->request->getData());
                    $session->write('search_data_index', $this->request->getData());

                    $this->request->getData('Student.SelectAll', null);

                    return $this->redirect(['action' => 'admit_all']);
                } else {
                    $this->Flash->error(__('Could not admit the selected student(s). Please, try again.'));
                }
            } else {
                $this->Flash->error(__('Please select at least one student to admit.'));
            }
        }

        if ($this->request->is('post') && !empty($this->request->getData('getacceptedstudent'))) {
            $this->_initClearSessionFilters();
            $this->_initSearch();

            if (!empty($this->request->getData('Search.academicyear'))) {
                $conditions = [];
                $ssacdemicyear = $this->request->withData('AcceptedStudent.academicyear', $this->request->getData('Search.academicyear'))->getData('AcceptedStudent.academicyear');
                $pprogram_id = $this->request->withData('AcceptedStudent.program_id', $this->request->getData('Search.program_id'))->getData('AcceptedStudent.program_id');
                $pprogram_type_id = $this->request->withData('AcceptedStudent.program_type_id', $this->request->getData('Search.program_type_id'))->getData('AcceptedStudent.program_type_id');
                $name = $this->request->withData('AcceptedStudent.name', $this->request->getData('Search.name'))->getData('AcceptedStudent.name');
                $college_ids = !empty($this->college_ids) ? $this->college_ids : [];
                $department_ids = !empty($this->department_ids) ? $this->department_ids : [];

                $studentsTable = TableRegistry::getTableLocator()->get('Students');
                if (!empty($college_ids)) {
                    $conditions = [
                        'AcceptedStudents.academicyear LIKE' => $ssacdemicyear . '%',
                        'AcceptedStudents.first_name LIKE' => '%' . $name . '%',
                        'AcceptedStudents.college_id' => !empty($this->request->getData('Search.college_id')) ? $this->request->getData('Search.college_id') : $college_ids,
                        'AcceptedStudents.program_id' => $pprogram_id,
                        'AcceptedStudents.program_type_id' => $pprogram_type_id,
                        'AcceptedStudents.studentnumber IS NOT NULL',
                        'Students.id IS NULL',
                        'AcceptedStudents.id NOT IN' => $studentsTable->subquery()
                            ->select(['accepted_student_id'])
                            ->from('students')
                            ->where(['accepted_student_id IS NOT NULL'])
                    ];
                } elseif (!empty($department_ids)) {
                    $conditions = [
                        'AcceptedStudents.academicyear LIKE' => $ssacdemicyear . '%',
                        'AcceptedStudents.first_name LIKE' => '%' . $name . '%',
                        'AcceptedStudents.department_id' => !empty($this->request->getData('Search.department_id')) ? $this->request->getData('Search.department_id') : $department_ids,
                        'AcceptedStudents.program_id' => $pprogram_id,
                        'AcceptedStudents.program_type_id' => $pprogram_type_id,
                        'AcceptedStudents.studentnumber IS NOT NULL',
                        'Students.id IS NULL',
                        'AcceptedStudents.id NOT IN' => $studentsTable->subquery()
                            ->select(['accepted_student_id'])
                            ->from('students')
                            ->where(['accepted_student_id IS NOT NULL'])
                    ];
                }

                if (!empty($conditions)) {
                    $limit = $this->request->getData('Search.limit', 1000);
                    $this->request->withData('Search.limit', $limit);

                    $this->paginate = [
                        'limit' => $limit,
                        'maxLimit' => $limit,
                        'contain' => [
                            'Students' => ['fields' => ['id']],
                            'Departments' => ['fields' => ['id', 'name']],
                            'Colleges' => ['fields' => ['id', 'name']]
                        ],
                        'fields' => [
                            'AcceptedStudents.id',
                            'AcceptedStudents.full_name',
                            'AcceptedStudents.sex',
                            'AcceptedStudents.studentnumber',
                            'AcceptedStudents.program_id',
                            'AcceptedStudents.college_id',
                            'AcceptedStudents.department_id',
                            'AcceptedStudents.EHEECE_total_results',
                            'AcceptedStudents.academicyear'
                        ]
                    ];

                    $acceptedStudents = $this->paginate($studentsTable->AcceptedStudents->find()->where($conditions));
                    $this->set('acceptedStudents', $acceptedStudents);

                    if (!empty($acceptedStudents)) {
                        $this->_initClearSessionFilters();
                        $this->request->withData('getacceptedstudent', true);
                        $this->_initSearch();
                    } else {
                        if ($last_success_message || $this->request->getData('admit')) {
                            $this->Flash->success($last_success_message . ' All students with the given search criteria have been admitted and no more new accepted students are found that need admission for now. Check admitted students list for more or change search criteria to admit other non-admitted students.');
                        } else {
                            $this->Flash->success(__('Either all students have been admitted or no new accepted student is found that needs admission for now with the given search criteria. Check admitted students list for more.'));
                            $this->_initClearSessionFilters();
                            $this->request->withData('getacceptedstudent', true);
                            $this->request->withData('Search', $this->request->getData('Search'));
                            $session->write('search_data_index', $this->request->getData('Search'));
                            return $this->redirect(['action' => 'index']);
                        }
                    }

                    $this->set('admitsearch', true);
                } else {
                    $this->Flash->error(__('You don\'t have privilege to admit students in the given criteria.'));
                }
            }
        }

        $colleges = [];
        $departments = [];
        if ($this->role_id == ROLE_REGISTRAR || $this->Auth->user('Role.parent_id') == ROLE_REGISTRAR) {
            $college_ids = !empty($this->college_ids) ? $this->college_ids : [];
            $department_ids = !empty($this->department_ids) ? $this->department_ids : [];

            if (!empty($college_ids)) {
                $colleges = $studentsTable->Colleges->find('list')
                    ->where(['Colleges.id IN' => $college_ids, 'Colleges.active' => 1])
                    ->toArray();
                $departments = $studentsTable->Departments->find('list')
                    ->where(['Departments.college_id IN' => $college_ids, 'Departments.active' => 1])
                    ->toArray();
                $this->set('college_level', true);
            } elseif (!empty($department_ids)) {
                $departments = $studentsTable->Departments->find('list')
                    ->where(['Departments.id IN' => $department_ids, 'Departments.active' => 1])
                    ->toArray();
                $colleges = $studentsTable->Colleges->find('list')
                    ->where(['Colleges.id IN' => $college_ids, 'Colleges.active' => 1])
                    ->toArray();
                $this->set('department_level', true);
            }
        } else {
            $colleges = $studentsTable->Colleges->find('list')
                ->where(['Colleges.active' => 1])
                ->toArray();
            $departments = $studentsTable->Departments->find('list')
                ->where(['Departments.active' => 1])
                ->toArray();
        }

        $programs = $studentsTable->Programs->find('list')
            ->where(['Programs.id IN' => $this->program_ids, 'Programs.active' => 1])
            ->toArray();
        $programTypes = $studentsTable->ProgramTypes->find('list')
            ->where(['ProgramTypes.id IN' => $this->program_type_id])
            ->toArray();

        $this->_initSearch();
        $this->set(compact('colleges', 'departments', 'programs', 'programTypes'));
    }

    public function admit($id = null)
    {
        $session = $this->request->getSession();
        $studentsTable = TableRegistry::getTableLocator()->get('Students');

        if ($id) {
            $check_eligibility_to_edit = 0;
            if (!empty($this->department_ids)) {
                $check_eligibility_to_edit = $studentsTable->AcceptedStudents->find()
                    ->where([
                        'AcceptedStudents.department_id IN' => $this->department_ids,
                        'AcceptedStudents.program_type_id' => $this->program_type_id,
                        'AcceptedStudents.program_id' => $this->program_id,
                        'AcceptedStudents.id' => $id
                    ])
                    ->count();
            } elseif (!empty($this->college_ids)) {
                $check_eligibility_to_edit = $studentsTable->AcceptedStudents->find()
                    ->where([
                        'AcceptedStudents.college_id IN' => $this->college_ids,
                        'AcceptedStudents.program_type_id' => $this->program_type_id,
                        'AcceptedStudents.program_id' => $this->program_id,
                        'AcceptedStudents.id' => $id
                    ])
                    ->count();
            }

            if ($check_eligibility_to_edit == 0) {
                $this->Flash->error(__('You are not eligible to admit the student. This happens when you are trying to admit students which you are not assigned.'));
                return $this->redirect(['action' => 'index']);
            }

            $studentnumber = $studentsTable->AcceptedStudents->find()
                ->where(['AcceptedStudents.id' => $id])
                ->select(['studentnumber'])
                ->first();

            if (empty($studentnumber->studentnumber)) {
                $this->Flash->error(__('You cannot admit students before generating student number, please generate student number.'));
                return $this->redirect(['controller' => 'AcceptedStudents', 'action' => 'generate']);
            }

            $isAdmitted = $studentsTable->isAdmitted($id);
            if ($isAdmitted) {
                $this->Flash->error(__('You have already admitted the students.'));
                return $this->redirect(['action' => 'admit']);
            }
        } else {

            if ($this->request->getSession()->has('search_data')) {
                $this->request->withData('getacceptedstudent', true);
            }
        }

        if ($this->request->is('post') && $this->request->getData('admit')) {
            $isAdmitted = $studentsTable->isAdmitted($id);
            if (!$isAdmitted) {
                $this->request->withData('User.role_id', ROLE_STUDENT);
                $this->request->withData('User.username', $this->request->getData('Student.studentnumber'));
                $this->request->withData('User.first_name', $this->request->getData('Student.first_name'));
                $this->request->withData('User.last_name', $this->request->getData('Student.last_name'));
                $this->request->withData('User.middle_name', $this->request->getData('Student.middle_name'));
                $this->request->withData('User.email', $this->request->getData('Student.email'));

                if ($this->request->getData('HigherEducationBackground')) {
                    $save_higher_education = false;
                    foreach ($this->request->getData('HigherEducationBackground') as $v) {
                        if (!empty($v['name']) || !empty($v['diploma_awarded']) || !empty($v['date_graduated']) || !empty($v['cgpa_at_graduation'])) {
                            $save_higher_education = true;
                        }
                    }
                    if (!$save_higher_education) {
                        $this->request->getData('HigherEducationBackground', null);
                    }
                }

                if ($this->request->getData('Student.program_id') != PROGRAM_UNDERGRADUATE || $this->request->getData('Student.program_type_id') != PROGRAM_TYPE_REGULAR) {
                    if ($this->request->getData('HighSchoolEducationBackground')) {
                        $save_highschool_education = false;
                        foreach ($this->request->getData('HighSchoolEducationBackground') as $v) {
                            if (!empty($v['name']) || !empty($v['region']) || !empty($v['town']) || !empty($v['zone']) || !empty($v['school_level'])) {
                                $save_highschool_education = true;
                            }
                        }
                        if (!$save_highschool_education) {
                            $this->request->getData('HighSchoolEducationBackground', null);
                        }
                    }
                }

                $data = $studentsTable->unsetEmpty($this->request->getData()->toArray());
                $student = $studentsTable->newEntity($data, ['validate' => 'first']);

                if ($studentsTable->save($student)) {
                    $this->Flash->success(__('The student has been saved'));
                    return $this->redirect(['action' => 'admit']);
                } else {
                    $this->Flash->error(__('The student could not be saved. Please, try again.'));
                    $this->set('id', $this->request->getData('Student.accepted_student_id'));
                }
            } else {
                $this->Flash->error(__('The student has already been admitted'));
                return $this->redirect(['action' => 'edit', $id]);
            }
            $this->set('admitsearch', true);
        }

        if ($this->request->is('post') && $this->request->getData('getacceptedstudent')) {
            if ($this->request->getData('AcceptedStudent.academicyear')) {
                $conditions = [];
                $ssacdemicyear = $this->request->getData('AcceptedStudent.academicyear');
                $college_ids = !empty($this->college_ids) ? $this->college_ids : [];
                $department_ids = !empty($this->department_ids) ? $this->department_ids : [];

                if (!empty($college_ids)) {
                    $conditions = [
                        'AcceptedStudents.academicyear LIKE' => $ssacdemicyear . '%',
                        'AcceptedStudents.college_id' => $this->request->getData('AcceptedStudent.college_id', $college_ids),
                        'AcceptedStudents.id NOT IN' => $studentsTable->subquery()
                            ->select(['accepted_student_id'])
                            ->from('students')
                            ->where(['accepted_student_id IS NOT NULL'])
                    ];
                } elseif (!empty($department_ids)) {
                    $conditions = [
                        'AcceptedStudents.academicyear LIKE' => $ssacdemicyear . '%',
                        'AcceptedStudents.department_id' => $this->request->getData('AcceptedStudent.department_id', $department_ids),
                        'AcceptedStudents.id NOT IN' => $studentsTable->subquery()
                            ->select(['accepted_student_id'])
                            ->from('students')
                            ->where(['accepted_student_id IS NOT NULL'])
                    ];
                }

                $conditions['AcceptedStudents.program_id'] = $this->request->getData('AcceptedStudent.program_id', $this->program_id);
                $conditions['AcceptedStudents.program_type_id'] = $this->request->getData('AcceptedStudent.program_type_id', $this->program_type_id);

                if (!empty($conditions)) {
                    $this->paginate = [
                        'limit' => 50000,
                        'contain' => ['Students', 'Colleges', 'Departments', 'Programs', 'ProgramTypes', 'Regions', 'Users']
                    ];

                    $acceptedStudents = $this->paginate($studentsTable->AcceptedStudents->find()->where($conditions));
                    $this->set('acceptedStudents', $acceptedStudents);

                    if (!empty($acceptedStudents)) {
                        $this->set('admitsearch', true);
                    } else {
                        $this->Flash->info(__('No data is found with your search criteria'));
                    }

                    $this->request->withData('getacceptedstudent', true);
                } else {
                    $this->Flash->error(__('You don\'t have privilege to admit students in the given criteria.'));
                }

                $curriculums = TableRegistry::getTableLocator()->get('Curriculums')->find('list', [
                    'keyField' => 'id',
                    'valueField' => 'curriculum_detail',
                    'conditions' => [
                        'Curriculums.department_id' => $this->request->getData('Student.department_id'),
                        'Curriculums.program_id' => $this->request->getData('Student.program_id'),
                        'Curriculums.registrar_approved' => 1
                    ]
                ])->toArray();
                $this->set(compact('curriculums'));
            } else {
                $this->Flash->error(__('Please select academic year'));
            }
        }

        if ($id) {
            $is_student_id_exist = $studentsTable->AcceptedStudents->find()
                ->where(['AcceptedStudents.id' => $id])
                ->count();

            if ($is_student_id_exist) {
                $this->set(compact('id'));
                $this->set('admitsearch', true);
                $data = $studentsTable->AcceptedStudents->find()
                    ->where(['AcceptedStudents.id' => $id])
                    ->first();

                $data_import = [];
                if ($data) {
                    $data_import = [
                        'Student' => [
                            'accepted_student_id' => $data->id,
                            'first_name' => $data->first_name,
                            'middle_name' => $data->middle_name,
                            'last_name' => $data->last_name,
                            'studentnumber' => $data->studentnumber,
                            'region_id' => $data->region_id,
                            'zone_id' => $data->zone_id,
                            'woreda_id' => $data->woreda_id,
                            'original_college_id' => $data->original_college_id ?? $data->college_id,
                            'college_id' => $data->college_id,
                            'department_id' => $data->department_id,
                            'program_id' => $data->program_id,
                            'program_type_id' => $data->program_type_id,
                            'gender' => $data->sex,
                            'curriculum_id' => $data->curriculum_id
                        ],
                        'User' => [
                            'id' => $data->user->id,
                            'role_id' => $data->user->role_id
                        ]
                    ];

                    $this->request->withData($data_import);
                }
            }
        }

        $colleges = [];
        $departments = [];
        if ($this->role_id == ROLE_REGISTRAR) {
            $college_ids = !empty($this->college_ids) ? $this->college_ids : [];
            $department_ids = !empty($this->department_ids) ? $this->department_ids : [];

            if (!empty($college_ids)) {
                $colleges = $studentsTable->Colleges->find('list')
                    ->where(['Colleges.id IN' => $college_ids, 'Colleges.active' => 1])
                    ->toArray();
                $departments = $studentsTable->Departments->find('list')
                    ->where(['Departments.college_id IN' => $college_ids, 'Departments.active' => 1])
                    ->toArray();
                $this->set('college_level', true);
            } elseif (!empty($department_ids)) {
                $departments = $studentsTable->Departments->find('list')
                    ->where(['Departments.id IN' => $department_ids, 'Departments.active' => 1])
                    ->toArray();
                $colleges = $studentsTable->Colleges->find('list')
                    ->where(['Colleges.id IN' => $college_ids, 'Colleges.active' => 1])
                    ->toArray();
                $this->set('department_level', true);
            }
        } else {
            $colleges = $studentsTable->Colleges->find('list')
                ->where(['Colleges.active' => 1])
                ->toArray();
            $departments = $studentsTable->Departments->find('list')
                ->where(['Departments.active' => 1])
                ->toArray();
        }

        $regions = $studentsTable->Regions->find('list')
            ->where(['Regions.active' => 1])
            ->toArray();
        $countries = $studentsTable->Countries->find('list')->toArray();
        $cities = $studentsTable->Cities->find('list')
            ->where(['Cities.active' => 1])
            ->toArray();
        $zones = $studentsTable->Zones->find('list')
            ->where(['Zones.active' => 1])
            ->toArray();
        $woredas = $studentsTable->Woredas->find('list')
            ->where(['Woredas.active' => 1])
            ->toArray();

        $this->set(compact('colleges', 'departments', 'regions', 'countries', 'cities', 'zones', 'woredas'));
    }



    public function getCountries($regionId = null)
    {
        $this->viewBuilder()->setLayout('ajax');

        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $regionsTable = $studentsTable->Regions;
        $countriesTable = $studentsTable->Countries;

        if (!empty($regionId)) {
            $countryIds = $regionsTable->find('list', [
                'keyField' => 'country_id',
                'valueField' => 'country_id'
            ])
                ->where(['Regions.id' => $regionId])
                ->toArray();
            $countries = $countriesTable->find('list')
                ->where(['Countries.id IN' => $countryIds])
                ->toArray();
        } elseif ($this->request->getData('Student.region_id')) {
            $countryIds = $regionsTable->find('list', [
                'keyField' => 'country_id',
                'valueField' => 'country_id'
            ])
                ->where(['Regions.id' => $this->request->getData('Student.region_id')])
                ->toArray();
            $countries = $countriesTable->find('list')
                ->where(['Countries.id IN' => $countryIds])
                ->toArray();
        } elseif ($this->request->getData('Student.country_id')) {
            $countries = $countriesTable->find('list')
                ->where(['Countries.id' => $this->request->getData('Student.country_id')])
                ->toArray();
        } else {
            $countries = $countriesTable->find('list')->toArray();
        }

        $this->set(compact('countries'));
    }

    public function getRegions($countryId = null)
    {
        $this->viewBuilder()->setLayout('ajax');

        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $regionsTable = $studentsTable->Regions;

        if ($countryId) {
            $regions = $regionsTable->find('list')
                ->where(['Regions.country_id' => $countryId])
                ->toArray();
        } else {
            $regions = $regionsTable->find('list')
                ->where(['Regions.country_id' => $this->request->getData('Student.country_id')])
                ->toArray();
        }

        $this->set(compact('regions'));
    }

    public function getZones($regionId = null)
    {
        $this->viewBuilder()->setLayout('ajax');

        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $zonesTable = $studentsTable->Zones;

        if ($regionId) {
            $zones = $zonesTable->find('list')
                ->where(['Zones.region_id' => $regionId])
                ->toArray();
        } else {
            $zones = $zonesTable->find('list')
                ->where(['Zones.region_id' => $this->request->getData('Student.region_id')])
                ->toArray();
        }

        $this->set(compact('zones'));
    }

    public function getWoredas($zoneId = null)
    {
        $this->viewBuilder()->setLayout('ajax');

        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $woredasTable = $studentsTable->Woredas;

        if ($zoneId) {
            $woredas = $woredasTable->find('list')
                ->where(['Woredas.zone_id' => $zoneId])
                ->toArray();
        } else {
            $woredas = $woredasTable->find('list')
                ->where(['Woredas.zone_id' => $this->request->getData('Student.zone_id')])
                ->toArray();
        }

        $this->set(compact('woredas'));
    }

    public function getCities($regionId = null)
    {
        $this->viewBuilder()->setLayout('ajax');

        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $citiesTable = $studentsTable->Cities;

        if ($regionId) {
            $cities = $citiesTable->find('list')
                ->where(['Cities.region_id' => $regionId])
                ->toArray();
        } else {
            $cities = $citiesTable->find('list')
                ->where(['Cities.region_id' => $this->request->getData('Student.region_id')])
                ->toArray();
        }

        $this->set(compact('cities'));
    }

    public function ajaxGetDepartment()
    {
        $this->viewBuilder()->setLayout('ajax');

        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $departmentsTable = $studentsTable->Departments;

        $departments = $departmentsTable->find('list')
            ->where(['Departments.college_id' => $this->request->getData('Staff.college_id')])
            ->toArray();

        $this->set(compact('departments'));
    }

    public function issuePassword()
    {
        if ($this->request->is('post') && $this->request->getData('issuestudentidsearch')) {
            if (!empty($this->request->getData('Student.studentnumber'))) {
                $studentsTable = TableRegistry::getTableLocator()->get('Students');
                $students = [];

                if ($this->role_id == ROLE_STUDENT) {
                    $students = $studentsTable->find()
                        ->where([
                            'Students.studentnumber LIKE' => '%' . trim($this->request->getData('Student.studentnumber')) . '%',
                            'Students.department_id' => $this->department_id
                        ])
                        ->contain([
                            'Users',
                            'AcceptedStudents',
                            'Programs',
                            'Colleges',
                            'Departments',
                            'ProgramTypes'
                        ])
                        ->first();

                    if (!empty($students)) {
                        $this->set('students', $students);
                        $this->set('hide_search', true);
                        $this->set('student_number', $this->request->getData('Student.studentnumber'));
                    } else {
                        $this->Flash->warning(__('You are not eligible to issue/reset password. The student does not belong to your department.'));
                    }
                } elseif ($this->role_id == ROLE_COLLEGE) {
                    $students = $studentsTable->find()
                        ->where(['Students.studentnumber LIKE' => '%' . trim($this->request->getData('Student.studentnumber')) . '%'])
                        ->contain([
                            'Users',
                            'AcceptedStudents',
                            'Programs',
                            'Colleges',
                            'Departments',
                            'ProgramTypes'
                        ])
                        ->first();

                    if (!empty($students)) {
                        $students = $studentsTable->find()
                            ->where([
                                'Students.studentnumber LIKE' => '%' . trim($this->request->getData('Student.studentnumber')) . '%',
                                'Students.college_id' => $this->college_id,
                                'Students.department_id IS NULL'
                            ])
                            ->first();

                        if (empty($students)) {
                            $this->Flash->warning(__('You are not eligible to issue/reset password. The student has already been assigned to a department. The department is responsible for password issue or reset.'));
                        } else {
                            $this->set('students', $students);
                            $this->set('hide_search', true);
                            $this->set('student_number', $this->request->getData('Student.studentnumber'));
                        }
                    } else {
                        $this->Flash->error(__('Please enter a valid student number'));
                    }
                }
            } else {
                $this->Flash->error(__('Please enter student number'));
            }
        }
    }

    public function profile($studentId = null)
    {
        $studentsTable = TableRegistry::getTableLocator()->get('Students');

        $studID = ($this->role_id == ROLE_STUDENT && isset($this->student_id)) ? $this->student_id : ($studentId ?? 0);

        $checkStudentAdmitted = $studentsTable->find()
            ->where(['Students.id' => $studID])
            ->count();

        if ($checkStudentAdmitted == 0) {
            $this->Flash->info(__('Your profile will be available after the registrar finishes the admission data entry.'));
            return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
        }

        if ($this->Auth->user('role_id') == ROLE_STUDENT) {
            $requireUpdate = false;
            $requireUpdateFields = [];
            $rupdtKey = 0;

            $studentDetail = $studentsTable->find()
                ->where(['Students.id' => $studID])
                ->contain([
                    'Users',
                    'AcceptedStudents',
                    'Programs',
                    'ProgramTypes',
                    'Contacts',
                    'Departments',
                    'Colleges',
                    'EslceResults',
                    'EheeceResults',
                    'Attachments',
                    'HigherEducationBackgrounds',
                    'HighSchoolEducationBackgrounds',
                    'Countries',
                    'Regions',
                    'Cities',
                    'Zones',
                    'Woredas',
                    'GraduateLists'
                ])
                ->first();

            if (!empty($studentDetail->department) && $studentDetail->department->is_name_changed) {
                $departmentIdToCheck = $studentDetail->department->id ?? $studentDetail->department_id;
                $dateToCheck = $studentDetail->graduate_list->graduate_date ?? ($studentDetail->admissionyear ?? date('Y-m-d'));
                $dateToCheck = strtotime($dateToCheck) !== false ? $dateToCheck : date('Y-m-d');
                $academicYearToCheck = $studentDetail->academicyear ?? $this->AcademicYear->current_academicyear();

                $departmentNameChangeTable = TableRegistry::getTableLocator()->get('DepartmentNameChanges');
                $getDepartmentNameChangeIfExists = $departmentNameChangeTable->getDepartmentNameChangeIfExists($departmentIdToCheck, $dateToCheck, $academicYearToCheck);

                if (!empty($getDepartmentNameChangeIfExists['Department'])) {
                    $studentDetail->department = $getDepartmentNameChangeIfExists['Department'];
                }
            }

            $studentAdmissionYear = (int)($studentDetail->accepted_student->academicyear
                ? explode('/', $studentDetail->accepted_student->academicyear)[0]
                : ($studentDetail->academicyear
                    ? explode('/', $studentDetail->academicyear)[0]
                    : explode('/', $this->AcademicYear->current_academicyear())[0]));

            if ($this->Auth->user('id') != $studentDetail->user_id) {
                $this->Flash->error(__('There is a conflicting session, please login again.'));
                $this->Session->destroy();
                return $this->redirect($this->Auth->logout());
            }

            $studentStatusPatternTable = TableRegistry::getTableLocator()->get('StudentStatusPatterns');
            $isGraduatingClassStudent = $studentStatusPatternTable->isEligibleForExitExam($studID);

            if ($this->request->is(['post', 'put']) && $this->request->getData('updateStudentDetail')) {
                $this->request->getData('User', null);
                $this->request->getData('AcceptedStudent', null);
                $this->request->getData('College', null);
                $this->request->getData('GraduateList', null);
                $this->request->getData('Department', null);

                $this->request->withData('Student.gender', $this->_normalizeGender($studentDetail->accepted_student->sex));

                if (!empty($this->request->getData('Student.email'))) {
                    $this->request->withData('User.email', trim($this->request->getData('Student.email')));
                    if ($this->role_id == ROLE_STUDENT && $this->Auth->user('id') == $studentDetail->user_id) {
                        $this->request->withData('User.id', $this->Auth->user('id'));
                    } elseif ($studentDetail->user_id) {
                        $this->request->withData('User.id', $studentDetail->user_id);
                    } else {
                        $studentUserId = $studentsTable->Users->find()
                            ->select(['id'])
                            ->where(['Users.username LIKE' => $studentDetail->studentnumber, 'Users.role_id' => ROLE_STUDENT])
                            ->first();
                        if ($studentUserId) {
                            $this->request->withData('User.id', $studentUserId->id);
                        }
                    }
                }

                if (!empty($this->request->getData('Student.phone_mobile')) && !empty($this->request->getData('Student.email'))) {
                    $this->request->withData($studentsTable->unsetEmpty($this->request->getData()->toArray()));

                    if (empty($this->request->getData('Student.city_id'))) {
                        $this->request->getData('Student.city_id', null);
                    }

                    if ($this->request->getData('Attachment') && (empty($this->request->getData('Attachment.0.file.name')) || $this->request->getData('Attachment.0.file.error'))) {
                        $this->request->getData('Attachment', null);
                    }

                    if ($this->request->getData('HighSchoolEducationBackground') && (empty($this->request->getData('HighSchoolEducationBackground.0.name')) || empty($this->request->getData('HighSchoolEducationBackground.0.town')) || empty($this->request->getData('HighSchoolEducationBackground.0.region_id')))) {
                        $this->request->getData('HighSchoolEducationBackground', null);
                    }

                    if ($this->request->getData('HigherEducationBackground') && (empty($this->request->getData('HigherEducationBackground.0.name')) || empty($this->request->getData('HigherEducationBackground.0.field_of_study')) || empty($this->request->getData('HigherEducationBackground.0.diploma_awarded')) || empty($this->request->getData('HigherEducationBackground.0.cgpa_at_graduation')))) {
                        $this->request->getData('HigherEducationBackground', null);
                    }

                    if ($this->request->getData('EheeceResult') && (empty($this->request->getData('EheeceResult.0.subject')) || empty($this->request->getData('EheeceResult.0.mark')))) {
                        $this->request->getData('EheeceResult', null);
                    }

                    if ($this->request->getData('EslceResult') && (empty($this->request->getData('EslceResult.0.subject')) || empty($this->request->getData('EslceResult.0.grade')) || empty($this->request->getData('EslceResult.0.exam_year')))) {
                        $this->request->getData('EslceResult', null);
                    }

                    $this->request->getData('updateStudentDetail', null);

                    $student = $studentsTable->patchEntity($studentsTable->get($studID), $this->request->getData(), ['validate' => 'first']);
                    if ($studentsTable->save($student)) {
                        $this->Flash->success(__('Your Profile has been updated.'));
                        return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
                    } else {
                        $this->Flash->error(__('Your student profile could not be saved. Please, try again.'));
                    }
                } else {
                    $emailPlaceholder = strtolower(str_replace('/', '.', $studentDetail->studentnumber)) . INSTITUTIONAL_EMAIL_SUFFIX;
                    if (empty($this->request->getData('Student.phone_mobile')) && empty($this->request->getData('Student.email'))) {
                        $this->Flash->error(__('Please provide your mobile phone number and personal email address. You can use {0} if you don\'t have a personal email address like Gmail, Yahoo, Hotmail, etc.', $emailPlaceholder));
                    } elseif (empty($this->request->getData('Student.phone_mobile'))) {
                        $this->Flash->error(__('Please provide your mobile phone number.'));
                    } else {
                        $this->Flash->error(__('Please provide your personal email address. You can use {0} if you don\'t have a personal email address like Gmail, Yahoo, Hotmail, etc.', $emailPlaceholder));
                    }
                }
            }

            if (empty($this->request->getData())) {
                $this->request->withData($studentsTable->find()
                    ->where(['Students.id' => $studID])
                    ->contain([
                        'Users',
                        'AcceptedStudents',
                        'Programs',
                        'ProgramTypes',
                        'Departments',
                        'Colleges',
                        'Contacts',
                        'EslceResults',
                        'EheeceResults',
                        'Attachments',
                        'HigherEducationBackgrounds',
                        'HighSchoolEducationBackgrounds',
                        'Countries',
                        'Regions',
                        'Cities',
                        'Zones',
                        'Woredas'
                    ])
                    ->first()
                    ->toArray());
            }

            $this->request->withData('Student.gender', $this->_normalizeGender($studentDetail->accepted_student->sex ?? $studentDetail->gender));

            if (!empty($this->request->getData('EheeceResult.0.exam_year')) && !$this->AcademicYear->isValidDateWithinYearRange($this->request->getData('EheeceResult.0.exam_year'), $studentAdmissionYear - 10, $studentAdmissionYear)) {
                $requireUpdate = true;
                $requireUpdateFields[$rupdtKey]['field'] = 'EHEECE Exam Taken Date';
                $requireUpdateFields[$rupdtKey]['previous_value'] = $this->request->getData('EheeceResult.0.exam_year');
                $this->request->withData('EheeceResult.0.exam_year', $studentAdmissionYear . '-07-01');
                $requireUpdateFields[$rupdtKey]['auto_corrected_value'] = $this->request->getData('EheeceResult.0.exam_year');
                $requireUpdateFields[$rupdtKey]['reason'] = 'EHEECE Exam Taken Date is not a valid date.';

                if ((int)explode('-', $studentDetail->eheece_result[0]->exam_year)[0] > $studentAdmissionYear) {
                    $requireUpdateFields[$rupdtKey]['reason'] = 'EHEECE Exam Taken Date can\'t be after Student Admission Year.';
                }

                $rupdtKey++;
            } elseif (empty($studentDetail->eheece_result)) {
                $this->request->withData('EheeceResult.0.exam_year', $studentAdmissionYear . '-07-01');
            }

            $maximumEstimatedGraduationYearLimit = $studentAdmissionYear;

            if ($studentDetail->program_id == PROGRAM_UNDERGRADUATE || $studentDetail->program_id == PROGRAM_PHD) {
                $maximumEstimatedGraduationYearLimit = $studentAdmissionYear + 6;
            } elseif ($studentDetail->program_id == PROGRAM_POST_GRADUATE) {
                $maximumEstimatedGraduationYearLimit = $studentDetail->program_type_id == PROGRAM_TYPE_REGULAR
                    ? $studentAdmissionYear + 3
                    : $studentAdmissionYear + 6;
            }

            if (!empty($studentDetail->curriculum_id) && $studentDetail->curriculum_id > 0) {
                $coursesTable = TableRegistry::getTableLocator()->get('Courses');
                $getCurriculumYearLevelCount = $coursesTable->find()
                    ->where(['Courses.curriculum_id' => $studentDetail->curriculum_id])
                    ->group(['Courses.year_level_id'])
                    ->count();

                if ($studentDetail->program_id == PROGRAM_UNDERGRADUATE || $studentDetail->program_type_id != PROGRAM_TYPE_REGULAR) {
                    if ($getCurriculumYearLevelCount) {
                        $maximumEstimatedGraduationYearLimit = $studentAdmissionYear + ($getCurriculumYearLevelCount * 2);
                    }
                }

                if (!empty($this->request->getData('Student.estimated_grad_date')) && !$this->AcademicYear->isValidDateWithinYearRange($this->request->getData('Student.estimated_grad_date'), $studentAdmissionYear, $studentAdmissionYear + ($getCurriculumYearLevelCount * 2))) {
                    $requireUpdate = true;
                    $requireUpdateFields[$rupdtKey]['field'] = 'Estimated Graduation Date';
                    $requireUpdateFields[$rupdtKey]['previous_value'] = $this->request->getData('Student.estimated_grad_date');
                    $this->request->withData('Student.estimated_grad_date', ($studentAdmissionYear + $getCurriculumYearLevelCount) . '-08-01');
                    $requireUpdateFields[$rupdtKey]['auto_corrected_value'] = $this->request->getData('Student.estimated_grad_date');
                    $requireUpdateFields[$rupdtKey]['reason'] = 'Estimated Graduation Date is not a valid date.';

                    if ((int)explode('-', $studentDetail->estimated_grad_date)[0] > ($studentAdmissionYear + ($getCurriculumYearLevelCount * 2))) {
                        $requireUpdateFields[$rupdtKey]['reason'] = 'Estimated Graduation Date can\'t be after ' . ($studentAdmissionYear + ($getCurriculumYearLevelCount * 2)) . ' G.C. (Double of student\'s attached curriculum year levels, ' . $getCurriculumYearLevelCount . ' X 2 years)';
                    } elseif ((int)explode('-', $studentDetail->estimated_grad_date)[0] < ($studentAdmissionYear + $getCurriculumYearLevelCount)) {
                        $requireUpdateFields[$rupdtKey]['reason'] = 'Estimated Graduation Date can\'t be before ' . ($studentAdmissionYear + $getCurriculumYearLevelCount) . ' G.C.';
                    }

                    $rupdtKey++;
                } elseif (empty($studentDetail->estimated_grad_date)) {
                    $this->request->withData('Student.estimated_grad_date', ($studentAdmissionYear + $getCurriculumYearLevelCount) . '-08-01');
                }
            } elseif (empty($studentDetail->estimated_grad_date) || is_null($studentDetail->estimated_grad_date)) {
                $this->request->withData('Student.estimated_grad_date', $maximumEstimatedGraduationYearLimit . '-08-01');
            }

            $foreignStudentsRegionIds = $studentsTable->Regions->find('list', [
                'keyField' => 'id',
                'valueField' => 'id'
            ])
                ->where(['Regions.country_id !=' => COUNTRY_ID_OF_ETHIOPIA])
                ->toArray();

            $regions = [];
            $zones = [];
            $woredas = [];
            $cities = [];
            $foreignStudent = 0;
            $countryIdOfRegion = COUNTRY_ID_OF_ETHIOPIA;
            $regionIdOfStudent = '';

            if ($studentDetail->accepted_student->region_id || $studentDetail->region_id) {
                $regionIdOfStudent = $studentDetail->accepted_student->region_id ?? $studentDetail->region_id;
                $countryIdOfRegion = $studentsTable->Regions->find()
                    ->select(['country_id'])
                    ->where(['Regions.id' => $regionIdOfStudent])
                    ->first()
                    ->country_id;

                $countries = $studentsTable->Countries->find('list')
                    ->where(['Countries.id' => $countryIdOfRegion])
                    ->toArray();
                $regions = $studentsTable->Regions->find('list')
                    ->where(['Regions.id' => $regionIdOfStudent, 'Regions.country_id' => $countryIdOfRegion])
                    ->toArray();
                $zones = $studentsTable->Zones->find('list')
                    ->where(['Zones.region_id' => $regionIdOfStudent])
                    ->toArray();
                $cityZoneIds = $studentsTable->Cities->find('list', [
                    'keyField' => 'zone_id',
                    'valueField' => 'zone_id'
                ])
                    ->where(['Cities.region_id' => $regionIdOfStudent])
                    ->toArray();
                $woredas = $studentsTable->Woredas->find('list')
                    ->where(['Woredas.zone_id IN' => (!empty($zones) ? array_keys($zones) : $cityZoneIds)])
                    ->toArray();
                $cities = $studentsTable->Cities->find('list')
                    ->where([
                        'OR' => [
                            'Cities.id' => $studentDetail->city_id,
                            'Cities.zone_id IN' => (!empty($zones) ? array_keys($zones) : ($studentDetail->accepted_student->zone_id ?? $studentDetail->zone_id)),
                            'Cities.region_id' => $regionIdOfStudent
                        ]
                    ])
                    ->toArray();
            } else {
                $countries = $studentsTable->Countries->find('list')->toArray();
                $regions = $studentsTable->Regions->find('list')
                    ->where(['Regions.active' => 1])
                    ->toArray();
                $zones = $studentsTable->Zones->find('list')
                    ->where(['Zones.active' => 1])
                    ->toArray();
                $woredas = $studentsTable->Woredas->find('list')
                    ->where(['Woredas.active' => 1])
                    ->toArray();
                $cities = $studentsTable->Cities->find('list')
                    ->where(['Cities.active' => 1])
                    ->toArray();
            }

            if (empty($regions)) {
                $regions = $studentsTable->Regions->find('list')
                    ->where(['Regions.country_id' => $countryIdOfRegion])
                    ->toArray();
            }

            if (empty($zones)) {
                $zones = $studentsTable->Zones->find('list')->toArray();
            }

            if (empty($woredas)) {
                $woredas = $studentsTable->Woredas->find('list')->toArray();
            }

            if (empty($cities)) {
                $cities = $studentsTable->Cities->find('list')
                    ->where(['Cities.region_id' => $regionIdOfStudent ?: array_keys($regions)])
                    ->toArray();
            }

            if (!empty($foreignStudentsRegionIds) && (
                    ($studentDetail->accepted_student->region_id && in_array($studentDetail->accepted_student->region_id, $foreignStudentsRegionIds)) ||
                    ($studentDetail->region_id && in_array($studentDetail->region_id, $foreignStudentsRegionIds))
                )) {
                $foreignStudent = 1;
            }

            $colleges = $studentsTable->Colleges->find('list')
                ->where(['Colleges.id' => $studentDetail->college_id])
                ->toArray();
            $departments = !empty($studentDetail->department_id) && is_numeric($studentDetail->department_id) && $studentDetail->department_id > 0
                ? $studentsTable->Departments->find('list')
                    ->where(['Departments.id' => $studentDetail->department_id])
                    ->toArray()
                : [];
            $regionsAll = $studentsTable->Regions->find('list')
                ->where(['Regions.active' => 1, 'Regions.country_id' => $countryIdOfRegion])
                ->toArray();
            $zonesAll = $studentsTable->Zones->find('list')
                ->where(['Zones.active' => 1])
                ->toArray();
            $woredasAll = $studentsTable->Woredas->find('list')
                ->where(['Woredas.active' => 1])
                ->toArray();
            $citiesAll = $studentsTable->Cities->find('list')
                ->where(['Cities.active' => 1])
                ->toArray();

            if ($this->request->getData('Contact.0.region_id')) {
                $citiesAll = $studentsTable->Cities->find('list')
                    ->where(['Cities.region_id' => $this->request->getData('Contact.0.region_id'), 'Cities.active' => 1])
                    ->toArray();
            }

            $contacts = $studentsTable->Contacts->find('list')
                ->where(['Contacts.student_id' => $this->student_id])
                ->toArray();
            $users = $studentsTable->Users->find('list')
                ->where(['Users.username' => $studentDetail->studentnumber])
                ->toArray();
            $programs = $studentsTable->Programs->find('list')
                ->where(['Programs.id' => $studentDetail->program_id])
                ->toArray();
            $programTypes = $studentsTable->ProgramTypes->find('list')
                ->where(['ProgramTypes.id' => $studentDetail->program_type_id])
                ->toArray();

            $studentDetail->country_id = $countryIdOfRegion;

            $studentMobilePhoneNumberError = '';
            if (!empty($this->request->getData('Student.phone_mobile')) && empty($this->_formatEthiopianPhoneNumber($this->request->getData('Student.phone_mobile')))) {
                $studentMobilePhoneNumberError = 'Your provided mobile phone number ' . $this->request->getData('Student.phone_mobile') . ' is not a valid mobile phone number. Please update that.';
            }

            $this->set(compact(
                'studentDetail', 'contacts', 'users', 'colleges', 'departments', 'programs', 'programTypes',
                'countries', 'regions', 'zones', 'woredas', 'cities', 'regionsAll', 'zonesAll', 'woredasAll', 'citiesAll',
                'foreignStudent', 'requireUpdate', 'requireUpdateFields', 'studentAdmissionYear', 'maximumEstimatedGraduationYearLimit',
                'isGraduatingClassStudent', 'studentMobilePhoneNumberError'
            ));
        } else {
            if ($this->Auth->user('role_id') == ROLE_REGISTRAR) {
                $this->Flash->info(__('You can edit student profile on this page.'));
                return $this->redirect(['action' => 'edit', $studentId]);
            } else {
                $this->Flash->warning(__('You are not allowed to edit or view any student profile.'));
                return $this->redirect('/');
            }
        }
    }

    public function moveBatchStudentToDepartment()
    {
        $studentsTable = TableRegistry::getTableLocator()->get('Students');

        if ($this->request->is('post') && $this->request->getData('moveSelectedSection')) {
            $selectedSections = [];
            $done = 0;

            $targetDepartmentDetail = $studentsTable->Departments->find()
                ->where(['Departments.id' => $this->request->getData('AcceptedStudent.target_department_id')])
                ->first();

            $sourceDepartmentDetail = $studentsTable->Departments->find()
                ->where(['Departments.id' => $this->request->getData('AcceptedStudent.department_id')])
                ->first();

            foreach ($this->request->getData('AcceptedStudent.selected_section', []) as $secId) {
                if ($secId) {
                    $sectionsTable = TableRegistry::getTableLocator()->get('Sections');
                    $secDetail = $sectionsTable->find()
                        ->where(['Sections.id' => $secId])
                        ->contain(['YearLevels'])
                        ->first();

                    $yearLevelsTable = TableRegistry::getTableLocator()->get('YearLevels');
                    $yearLevelLists = $yearLevelsTable->find()
                        ->where([
                            'YearLevels.department_id' => $secDetail->department_id,
                            'YearLevels.id !=' => $secDetail->year_level_id
                        ])
                        ->toArray();

                    $studentsSectionsTable = TableRegistry::getTableLocator()->get('StudentsSections');
                    $studentListsInTheSection = $studentsSectionsTable->find('list', [
                        'keyField' => 'student_id',
                        'valueField' => 'student_id'
                    ])
                        ->where(['StudentsSections.section_id' => $secId])
                        ->toArray();

                    $acceptedStudentsList = $studentsTable->find('list', [
                        'keyField' => 'accepted_student_id',
                        'valueField' => 'accepted_student_id'
                    ])
                        ->where(['Students.id IN' => $studentListsInTheSection])
                        ->toArray();

                    $curriculums = $studentsTable->find()
                        ->select(['Students.curriculum_id'])
                        ->select(['total' => 'COUNT(Students.curriculum_id)'])
                        ->where(['Students.id IN' => $studentListsInTheSection])
                        ->group(['Students.curriculum_id'])
                        ->toArray();

                    $batchCurriculumC = 0;
                    $batchCurriculum = 0;
                    foreach ($curriculums as $cv) {
                        if ($cv->total > $batchCurriculumC) {
                            $batchCurriculumC = $cv->total;
                            $batchCurriculum = $cv->curriculum_id;
                        }
                    }

                    if (!empty($studentListsInTheSection)) {
                        $sectionLists = [$secDetail];
                        $sectAcademicYear = $secDetail->academicyear;

                        foreach ($yearLevelLists as $yv) {
                            $studentExamStatusTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');
                            $nextAcademicYear = $studentExamStatusTable->getNextSemster($sectAcademicYear);
                            $secDetailIn = $sectionsTable->find()
                                ->where([
                                    'Sections.year_level_id' => $yv->id,
                                    'Sections.department_id' => $secDetail->department_id,
                                    'Sections.program_id' => $secDetail->program_id,
                                    'Sections.program_type_id' => $secDetail->program_type_id,
                                    'Sections.academicyear' => $nextAcademicYear['academic_year'],
                                    'Sections.id IN' => $studentsSectionsTable->subquery()
                                        ->select(['section_id'])
                                        ->where(['student_id IN' => $studentListsInTheSection])
                                ])
                                ->contain(['YearLevels'])
                                ->first();

                            if ($secDetailIn) {
                                $sectionLists[] = $secDetailIn;
                                $sectAcademicYear = $secDetailIn->academicyear;
                            }
                        }

                        foreach ($sectionLists as $sv) {
                            $targetSectionYearLevel = $yearLevelsTable->find()
                                ->where([
                                    'YearLevels.name' => $sv->year_level->name,
                                    'YearLevels.department_id' => $this->request->getData('AcceptedStudent.target_department_id')
                                ])
                                ->first();

                            $countSectionStudent = $studentsSectionsTable->find()
                                ->where(['StudentsSections.section_id' => $sv->id])
                                ->count();

                            if ($countSectionStudent > 0 && $targetSectionYearLevel) {
                                $sectionsTable->updateAll(
                                    [
                                        'department_id' => $targetSectionYearLevel->department_id,
                                        'year_level_id' => $targetSectionYearLevel->id
                                    ],
                                    ['Sections.id' => $sv->id]
                                );

                                $publishedCoursesTable = TableRegistry::getTableLocator()->get('PublishedCourses');
                                $publishedCoursesTable->updateAll(
                                    [
                                        'department_id' => $targetSectionYearLevel->department_id,
                                        'year_level_id' => $targetSectionYearLevel->id
                                    ],
                                    [
                                        'section_id' => $sv->id,
                                        'year_level_id' => $sv->year_level_id
                                    ]
                                );

                                $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
                                $courseRegistrationsTable->updateAll(
                                    ['year_level_id' => $targetSectionYearLevel->id],
                                    [
                                        'section_id' => $sv->id,
                                        'year_level_id' => $sv->year_level_id
                                    ]
                                );

                                $courseAddsTable = TableRegistry::getTableLocator()->get('CourseAdds');
                                $courseAddsTable->updateAll(
                                    ['year_level_id' => $targetSectionYearLevel->id],
                                    [
                                        'published_course_id IN' => $publishedCoursesTable->subquery()
                                            ->select(['id'])
                                            ->where([
                                                'section_id' => $sv->id,
                                                'year_level_id' => $sv->year_level_id
                                            ]),
                                        'year_level_id' => $sv->year_level_id
                                    ]
                                );

                                if ($batchCurriculum) {
                                    $curriculumsTable = TableRegistry::getTableLocator()->get('Curriculums');
                                    $curriculumsTable->updateAll(
                                        ['department_id' => $targetDepartmentDetail->id],
                                        [
                                            'id' => $batchCurriculum,
                                            'department_id' => $sourceDepartmentDetail->id
                                        ]
                                    );

                                    $coursesTable = TableRegistry::getTableLocator()->get('Courses');
                                    $coursesTable->updateAll(
                                        [
                                            'department_id' => $targetDepartmentDetail->id,
                                            'year_level_id' => $targetSectionYearLevel->id
                                        ],
                                        [
                                            'curriculum_id' => $batchCurriculum,
                                            'year_level_id' => $sv->year_level_id
                                        ]
                                    );
                                }

                                $done++;
                            }
                        }
                    }

                    if ($done) {
                        $acceptedStudentsTable = TableRegistry::getTableLocator()->get('AcceptedStudents');
                        $acceptedStudentsTable->updateAll(
                            [
                                'department_id' => $targetDepartmentDetail->id,
                                'college_id' => $targetDepartmentDetail->college_id
                            ],
                            [
                                'id IN' => $acceptedStudentsList,
                                'department_id' => $sourceDepartmentDetail->id
                            ]
                        );

                        $studentsTable->updateAll(
                            [
                                'department_id' => $targetDepartmentDetail->id,
                                'college_id' => $targetDepartmentDetail->college_id
                            ],
                            [
                                'id IN' => $studentListsInTheSection,
                                'department_id' => $sourceDepartmentDetail->id
                            ]
                        );
                    }
                }
            }

            if ($done) {
                $this->Flash->success(__('The selected section students have successfully moved from {0} department to {1} department.', $sourceDepartmentDetail->name, $targetDepartmentDetail->name));
            } else {
                $this->Flash->error(__('No section is selected to move the students to the target department.'));
            }
        }

        if ($this->request->is('post') && $this->request->getData('getacceptedstudent')) {
            $everythingFine = false;
            if (empty($this->request->getData('AcceptedStudent.academicyear'))) {
                $this->Flash->error(__('Please select the academic year of the batch admitted.'));
            } elseif (empty($this->request->getData('AcceptedStudent.department_id'))) {
                $this->Flash->error(__('Please select the current student department you want to transfer to the target department.'));
            } elseif (empty($this->request->getData('AcceptedStudent.target_department_id'))) {
                $this->Flash->error(__('Please select the target student department you want to transfer the batch to.'));
            } elseif (empty($this->request->getData('AcceptedStudent.program_id'))) {
                $this->Flash->error(__('Please select the program you want to transfer.'));
            } elseif (empty($this->request->getData('AcceptedStudent.program_type_id'))) {
                $this->Flash->error(__('Please select the program type you want to transfer.'));
            } elseif ($this->request->getData('AcceptedStudent.department_id') == $this->request->getData('AcceptedStudent.target_department_id')) {
                $this->Flash->error(__('You have selected the same department for moving, please select a different target department.'));
            } else {
                $everythingFine = true;
            }

            if ($everythingFine) {
                $acceptedStudent = $studentsTable->AcceptedStudents->find('list', [
                    'keyField' => 'id',
                    'valueField' => 'id'
                ])
                    ->where([
                        'AcceptedStudents.department_id' => $this->request->getData('AcceptedStudent.department_id'),
                        'AcceptedStudents.program_type_id' => $this->request->getData('AcceptedStudent.program_type_id'),
                        'AcceptedStudents.program_id' => $this->request->getData('AcceptedStudent.program_id'),
                        'AcceptedStudents.academicyear' => $this->request->getData('AcceptedStudent.academicyear')
                    ])
                    ->toArray();

                $admittedStudent = $studentsTable->find('list')
                    ->where([
                        'Students.accepted_student_id IN' => $acceptedStudent,
                        'Students.id NOT IN' => $studentsTable->CourseExemptions->subquery()
                            ->select(['student_id'])
                    ])
                    ->toArray();

                $senateListsTable = TableRegistry::getTableLocator()->get('SenateLists');
                $graduatingCount = $senateListsTable->find()
                    ->where(['SenateLists.student_id IN' => $admittedStudent])
                    ->count();

                if ($graduatingCount == 0 && !empty($acceptedStudent)) {
                    $yearLevelsTable = TableRegistry::getTableLocator()->get('YearLevels');
                    $yearLevelId = $yearLevelsTable->find()
                        ->where([
                            'YearLevels.department_id' => $this->request->getData('AcceptedStudent.department_id'),
                            'YearLevels.name' => '1st'
                        ])
                        ->first();

                    $sectionsTable = TableRegistry::getTableLocator()->get('Sections');
                    $sectionLists = $sectionsTable->find()
                        ->where([
                            'Sections.department_id' => $this->request->getData('AcceptedStudent.department_id'),
                            'Sections.year_level_id' => $yearLevelId->id,
                            'Sections.program_id' => $this->request->getData('AcceptedStudent.program_id'),
                            'Sections.academicyear' => $this->request->getData('AcceptedStudent.academicyear'),
                            'Sections.program_type_id' => $this->request->getData('AcceptedStudent.program_type_id'),
                            'Sections.id IN' => $studentsTable->StudentsSections->subquery()
                                ->select(['section_id'])
                                ->where(['student_id IN' => $admittedStudent])
                        ])
                        ->contain(['YearLevels'])
                        ->order(['Sections.academicyear' => 'ASC'])
                        ->toArray();

                    $this->set(compact('sectionLists'));
                } else {
                    if ($graduatingCount > 0) {
                        $this->Flash->error(__('Some students have graduated in the selected section, so it is not possible to move to another department.'));
                    }
                }
            }
        }

        $acyearList = $this->AcademicYear->academicYearInArray(date('Y') - 7, date('Y') - 1);
        $colleges = $studentsTable->Colleges->find('list')->toArray();
        $departments = $studentsTable->Departments->find('list')->toArray();
        $programs = $studentsTable->Programs->find('list')->toArray();
        $programTypes = $studentsTable->ProgramTypes->find('list')->toArray();

        $this->set(compact('colleges', 'departments', 'programs', 'programTypes', 'acyearList'));
    }

    public function ajaxUpdate()
    {
        $this->viewBuilder()->setLayout('ajax');

        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $value = $this->request->getData('update_value');
        $field = $this->request->getData('element_id');

        $studentsTable->id = $this->student_id;
        if (!$studentsTable->saveField($field, $value)) {
            $this->set('error', true);
        }

        $student = $studentsTable->get($this->student_id);

        if (substr($field, -3) == '_id') {
            $newField = substr($field, 0, strlen($field) - 3);
            $modelName = Inflector::camelize($newField);

            $modelTable = TableRegistry::getTableLocator()->get(Inflector::pluralize($modelName));
            $displayField = $modelTable->displayField() ?? 'name';

            $value = $modelTable->find()
                ->select([$displayField])
                ->where(['id' => $value])
                ->first()
                ->{$displayField};
        }

        $this->set('value', $value);
    }

    public function getCourseRegisteredAndAdd($studentId = '')
    {
        $this->viewBuilder()->setLayout('ajax');

        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $publishedCourses = [];

        if ($studentId) {
            $publishedCourses = $studentsTable->getStudentRegisteredAndAddCourses($studentId);
        }

        $this->set(compact('publishedCourses'));
    }

    public function getPossibleSupRegisteredAndAdd($studentId = '')
    {
        $this->viewBuilder()->setLayout('ajax');

        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $publishedCourses = [];

        if ($studentId) {
            $publishedCourses = $studentsTable->getPossibleStudentRegisteredAndAddCoursesForSup($studentId);
        }

        $this->set(compact('publishedCourses'));
    }

    private function _normalizeGender($sex)
    {
        $sex = trim(strtolower($sex));
        if (in_array($sex, ['female', 'f'])) {
            return 'Female';
        } elseif (in_array($sex, ['male', 'm'])) {
            return 'Male';
        }
        return ucfirst($sex);
    }


    public function studentLists($studentId = null)
    {
        $studentsTable = TableRegistry::getTableLocator()->get('Students');

        $query = $studentsTable->find()
            ->select(['id', 'studentnumber', 'full_name', 'department_id'])
            ->where(['Students.id NOT IN' => $studentsTable->GraduateLists->find()->select(['student_id'])])
            ->contain(['StudentsSections' => ['conditions' => ['StudentsSections.archive' => false]]]);

        if ($studentId) {
            $query->where(['Students.id' => $studentId]);
        }

        $students = $query->toArray();

        $sectionsTable = TableRegistry::getTableLocator()->get('Sections');
        $sections = $sectionsTable->find()
            ->where(['Sections.archive' => 0])
            ->contain([
                'Programs' => ['fields' => ['id', 'name']],
                'ProgramTypes' => ['fields' => ['id', 'name']]
            ])
            ->toArray();

        $collegesTable = TableRegistry::getTableLocator()->get('Colleges');
        $colleges = $collegesTable->find()
            ->select(['id', 'name'])
            ->toArray();

        $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
        $departments = $departmentsTable->find()
            ->select(['id', 'name', 'college_id'])
            ->toArray();

        $this->set(compact('students', 'sections', 'colleges', 'departments'));
    }

    public function manageStudentMedicalCardNumber()
    {
        if ($this->request->is('post') && $this->request->getData('search')) {
            $studentnumber = $this->request->getData('Student.studentnumber');
            if (!empty($studentnumber)) {
                $studentsTable = TableRegistry::getTableLocator()->get('Students');
                $students = $studentsTable->getStudentDetailsForHealth($studentnumber);
                if (empty($students)) {
                    $this->Flash->error(__('There is no student with this ID. Please provide a correct student ID (format example: Reg/453/88).'));
                } else {
                    $this->set(compact('students'));
                }
            } else {
                $this->Flash->info(__('Please provide a student ID (format example: Reg/453/88).'));
            }
        }

        if ($this->request->is('post') && $this->request->getData('submit')) {
            $studentsTable = TableRegistry::getTableLocator()->get('Students');
            $student = $studentsTable->get($this->request->getData('Student.id'));
            $student->card_number = $this->request->getData('Student.card_number');

            if ($studentsTable->save($student)) {
                $this->Flash->success(__('The card number has been saved.'));
            } else {
                $this->Flash->error(__('The card number could not be saved. Please, try again.'));
            }

            $students = $studentsTable->getStudentDetailsForHealth($this->request->getData('Student.studentnumber'));
            $this->set(compact('students'));
        }
    }

    public function studentAcademicProfile($studentId = null)
    {



        // Load AcademicYear component
        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $otpsTable = TableRegistry::getTableLocator()->get('Otps');
        $moodleUsersTable = TableRegistry::getTableLocator()->get('MoodleUsers');
        $readmissionsTable = TableRegistry::getTableLocator()->get('Readmissions');
        $sectionsTable = TableRegistry::getTableLocator()->get('Sections');
        $studentExamStatusesTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');
        $studentStatusPatternsTable = TableRegistry::getTableLocator()->get('StudentStatusPatterns');

        $academicYR = $this->AcademicYear->currentAcademicYear();
        $isTheStudentDismissed = 0;
        $isTheStudentReadmitted = 0;
        $moodleUserDetails = [];
        $user = $this->Auth->user();
        $userRoleId = $user['role_id'] ?? null;
        $userId = $user['id'] ?? null;

        if ($userRoleId == ROLE_STUDENT) {
            $studentId = $this->Auth->user('student_id') ?? $this->request->getSession()->read('Auth.User.student_id');
            $studentSectionExamStatus = $studentsTable->getStudentSection($studentId);

            $otps = [];
            if (SHOW_OTP_TAB_ON_STUDENT_ACADEMIC_PROFILE_FOR_STUDENTS == 1) {
                $otps = $otpsTable->find()
                    ->where(['student_id' => $studentId, 'active' => 1])
                    ->order(['modified' => 'DESC', 'created' => 'DESC'])
                    ->toArray();

                if ($otps) {
                    $moodleIntegratedUser = false;
                    foreach ($otps as $otp) {
                        if ($otp->service == 'Elearning' && empty($otp->portal)) {
                            $moodleIntegratedUser = true;
                        }
                    }

                    if ($moodleIntegratedUser) {
                        $moodleUserDetails = $moodleUsersTable->find()
                            ->where(['table_id' => $studentId, 'role_id' => ROLE_STUDENT])
                            ->order(['created' => 'DESC'])
                            ->first() ?: [];
                    }
                }
            }

            if (!empty($studentSectionExamStatus['Section'])) {
                $section = $studentSectionExamStatus['Section'];
                if (!$section['archive'] && !$studentSectionExamStatus['StudentsSection']['archive']) {
                    $academicYR = $section['academicyear'];
                }
            }

            if (
                !empty($studentSectionExamStatus['StudentExamStatus']) &&
                $studentSectionExamStatus['StudentExamStatus']['academic_status_id'] == DISMISSED_ACADEMIC_STATUS_ID
            ) {
                $isTheStudentDismissed = 1;

                $possibleReadmissionYears = $studentStatusPatternsTable->getAcademicYearRange(
                    $studentSectionExamStatus['StudentExamStatus']['academic_year'],
                    $academicYR
                );

                $readmitted = $readmissionsTable->find()
                    ->where([
                        'student_id' => $studentId,
                        'registrar_approval' => 1,
                        'academic_commision_approval' => 1,
                        'academic_year IN' => $possibleReadmissionYears
                    ])
                    ->order(['academic_year' => 'DESC', 'semester' => 'DESC', 'modified' => 'DESC'])
                    ->first();

                if ($readmitted) {
                    $lastReadmittedAcademicYear = $readmitted->academic_year;
                    $isTheStudentReadmitted = 1;
                    $possibleAcademicYears = $studentStatusPatternsTable->getAcademicYearRange($lastReadmittedAcademicYear, $academicYR);
                    $this->set(compact('possibleAcademicYears'));
                }
            }

            $studentAcademicProfile = $studentsTable->getStudentRegisteredAddDropCurriculumResult($studentId, $academicYR);
            $studentAttendedSections = $sectionsTable->getStudentSectionHistory($studentId);

            $this->set(compact(
                'isTheStudentDismissed',
                'isTheStudentReadmitted',
                'studentAcademicProfile',
                'studentAttendedSections',
                'studentSectionExamStatus',
                'otps',
                'moodleUserDetails',
                'academicYR'
            ));

            if ($userRoleId == ROLE_STUDENT && $userId) {
                $isExitExamEligible = $studentStatusPatternsTable->isEligibleForExitExam($studentId);
                $isNotProfilePage = $this->request->getParam('action') !== 'profile';
                $isNotUsersPage = $this->request->getParam('controller') !== 'users';
                $isNotChangePwdPage = $this->request->getParam('action') !== 'changePwd';

                if (
                    ($isExitExamEligible || FORCE_ALL_STUDENTS_TO_FILL_BASIC_PROFILE == 1) &&
                    $isNotProfilePage &&
                    $isNotUsersPage &&
                    $isNotChangePwdPage &&
                    !$studentStatusPatternsTable->completedFillingProfileInformation($studentId)
                ) {
                    $this->Flash->warning(
                        __('Dear %s, before proceeding, you must complete your basic profile. If you encounter an error, are unable to update your profile on your own, or require further assistance, please report to the registrar record officer assigned to your department.', $user['first_name'])
                    );
                    return $this->redirect(['controller' => 'students', 'action' => 'profile']);
                }

                $studentDetails = $studentsTable->find()
                    ->select(['studentnumber', 'country_id', 'faida_identification_number', 'faida_alias_number'])
                    ->where(['id' => $studentId])
                    ->first();

                $isEthiopianStudent = !empty($studentDetails->country_id) && (int)$studentDetails->country_id == COUNTRY_ID_OF_ETHIOPIA;
                $isFaidaFinFilled = !empty($studentDetails->faida_identification_number);
                $isFaidaFanFilled = !empty($studentDetails->faida_alias_number);

                if (
                    $isEthiopianStudent &&
                    (!$isFaidaFinFilled || !$isFaidaFanFilled) &&
                    ($isExitExamEligible || FORCE_ALL_STUDENTS_TO_FILL_FAIDA_FIN == 1) &&
                    $isNotProfilePage &&
                    $isNotUsersPage &&
                    $isNotChangePwdPage
                ) {
                    $message = __('Dear %s, before proceeding, you must update your ', $user['first_name']);
                    if (!$isFaidaFinFilled && !$isFaidaFanFilled) {
                        $message .= __('Fayda Identification Number (FIN) and Fayda Alias Number (FAN). Ensure that you provide the correct 16-digit FAN, located on the front, and the 12-digit FIN, found on the back of your national Fayda ID card.');
                    } elseif (!$isFaidaFinFilled) {
                        $message .= __('Fayda Identification Number (FIN). Please ensure that you provide the correct 12-digit FIN, located on the back of your national Fayda ID card.');
                    } else {
                        $message .= __('Fayda Alias Number (FAN). Please ensure that you provide the correct 16-digit FAN, located on the front of your national Fayda ID card.');
                    }
                    $this->Flash->info($message);
                    return $this->redirect(['controller' => 'students', 'action' => 'profile']);
                }
            }
        } else {
            $checkIdIsValid = 0;
            if (!empty($studentId) && is_numeric($studentId)) {
                $studentId = (int)$studentId;
                $conditions = ['id' => $studentId];

                if ($userRoleId == ROLE_REGISTRAR && !$user['is_admin']) {
                    if (!empty($this->department_ids)) {
                        $conditions['program_type_id IN'] = $this->program_type_ids;
                        $conditions['program_id IN'] = $this->program_ids;
                        $conditions['department_id IN'] = $this->department_ids;
                    } elseif (!empty($this->college_ids)) {
                        $conditions['program_type_id IN'] = $this->program_type_ids;
                        $conditions['program_id IN'] = $this->program_ids;
                        $conditions['college_id IN'] = $this->college_ids;
                    }
                } elseif ($userRoleId == ROLE_DEPARTMENT) {
                    $conditions['department_id IN'] = $this->department_ids;
                } elseif ($userRoleId == ROLE_COLLEGE) {
                    $conditions['college_id IN'] = $this->college_ids;
                } elseif ($userRoleId == ROLE_SYSADMIN || ($userRoleId == ROLE_REGISTRAR && $user['is_admin'])) {
                    // No additional restrictions
                }

                $checkIdIsValid = $studentsTable->find()->where($conditions)->count();
            }

            debug($checkIdIsValid);

            if ($checkIdIsValid > 0) {
                $otps = [];
                if (SHOW_OTP_TAB_ON_STUDENT_ACADEMIC_PROFILE_FOR_STUDENTS == 1) {
                    $otps = $otpsTable->find()
                        ->where(['student_id' => $studentId, 'active' => 1])
                        ->order(['modified' => 'DESC', 'created' => 'DESC'])
                        ->toArray();

                    if ($otps) {
                        $moodleIntegratedUser = false;
                        foreach ($otps as $otp) {
                            if ($otp->service == 'Elearning' && empty($otp->portal)) {
                                $moodleIntegratedUser = true;
                            }
                        }

                        if ($moodleIntegratedUser) {
                            $moodleUserDetails = $moodleUsersTable->find()
                                ->where(['table_id' => $studentId, 'role_id' => ROLE_STUDENT])
                                ->order(['created' => 'DESC'])
                                ->first() ?: [];
                        }
                    }
                }

                $studentSectionExamStatus = $studentsTable->getStudentSection($studentId);

                if (!empty($studentSectionExamStatus['Section'])) {
                    $section = $studentSectionExamStatus['Section'];
                    if (!$section['archive'] && !$studentSectionExamStatus['StudentsSection']['archive']) {
                        $academicYR = $section['academicyear'];
                    }
                }

                if (
                    !empty($studentSectionExamStatus['StudentExamStatus']) &&
                    $studentSectionExamStatus['StudentExamStatus']['academic_status_id'] == DISMISSED_ACADEMIC_STATUS_ID
                ) {
                    $isTheStudentDismissed = 1;

                    $possibleReadmissionYears = $studentStatusPatternsTable->getAcademicYearRange(
                        $studentSectionExamStatus['StudentExamStatus']['academic_year'],
                        $academicYR
                    );

                    $readmitted = $readmissionsTable->find()
                        ->where([
                            'student_id' => $studentId,
                            'registrar_approval' => 1,
                            'academic_commision_approval' => 1,
                            'academic_year IN' => $possibleReadmissionYears
                        ])
                        ->order(['academic_year' => 'DESC', 'semester' => 'DESC', 'modified' => 'DESC'])
                        ->first();

                    if ($readmitted) {
                        $lastReadmittedAcademicYear = $readmitted->academic_year;
                        $isTheStudentReadmitted = 1;
                        $possibleAcademicYears = $studentExamStatusesTable->getAcademicYearRange($lastReadmittedAcademicYear, $academicYR);
                        $this->set(compact('possibleAcademicYears'));
                    }
                }

                $isStudentEverReadmitted = $readmissionsTable->find()
                    ->where([
                        'student_id' => $studentId,
                        'registrar_approval' => 1,
                        'academic_commision_approval' => 1
                    ])
                    ->count();

                $studentAcademicProfile = $studentsTable->getStudentRegisteredAddDropCurriculumResult($studentId, $academicYR);
                $studentAttendedSections = $sectionsTable->getStudentSectionHistory($studentId);

                $this->set(compact(
                    'isTheStudentDismissed',
                    'isTheStudentReadmitted',
                    'studentAcademicProfile',
                    'studentAttendedSections',
                    'studentSectionExamStatus',
                    'otps',
                    'moodleUserDetails',
                    'isStudentEverReadmitted',
                    'academicYR'
                ));
            }
        }

        if ( !empty($this->request->getData('continue'))) {

            $studentNumber = trim($this->request->getData('studentID', ''));
            if ($studentNumber) {
                $studentIdValid = $studentsTable->find()
                    ->where(['studentnumber' => $studentNumber])
                    ->count();

                $checkIdIsValid = 0;
                $conditions = ['studentnumber' => $studentNumber];

                if ($userRoleId == ROLE_REGISTRAR && !$user['is_admin']) {
                    if (!empty($this->department_ids)) {
                        $conditions['program_type_id IN'] = $this->program_type_ids;
                        $conditions['program_id IN'] = $this->program_ids;
                        $conditions['department_id IN'] = $this->department_ids;
                    } elseif (!empty($this->college_ids)) {
                        $conditions['program_type_id IN'] = $this->program_type_ids;
                        $conditions['program_id IN'] = $this->program_ids;
                        $conditions['college_id IN'] = $this->college_ids;
                    }
                } elseif ($userRoleId == ROLE_DEPARTMENT) {
                    $conditions['department_id IN'] = $this->department_ids;
                } elseif ($userRoleId == ROLE_COLLEGE) {
                    $conditions['college_id IN'] = $this->college_ids;
                } elseif ($userRoleId == ROLE_SYSADMIN || ($userRoleId == ROLE_REGISTRAR && $user['is_admin'])) {
                    // No additional restrictions
                }

                $checkIdIsValid = $studentsTable->find()->where($conditions)->count();

                if ($studentIdValid == 0) {
                    $this->Flash->warning(__('The provided Student ID is not valid.'));
                } elseif ($studentIdValid > 0 && $checkIdIsValid > 0) {
                    $studentId = $studentsTable->find()
                        ->select(['id'])
                        ->where(['studentnumber' => $studentNumber])
                        ->first()->id;

                    $otps = [];
                    if (SHOW_OTP_TAB_ON_STUDENT_ACADEMIC_PROFILE_FOR_STUDENTS == 1) {
                        $otps = $otpsTable->find()
                            ->where(['student_id' => $studentId, 'active' => 1])
                            ->order(['modified' => 'DESC', 'created' => 'DESC'])
                            ->toArray();

                        if ($otps) {
                            $moodleIntegratedUser = false;
                            foreach ($otps as $otp) {
                                if ($otp->service == 'Elearning' && empty($otp->portal)) {
                                    $moodleIntegratedUser = true;
                                }
                            }

                            if ($moodleIntegratedUser) {
                                $moodleUserDetails = $moodleUsersTable->find()
                                    ->where(['table_id' => $studentId, 'role_id' => ROLE_STUDENT])
                                    ->order(['created' => 'DESC'])
                                    ->first() ?: [];
                            }
                        }
                    }

                    $studentSectionExamStatus = $studentsTable->getStudentSection($studentId);

                    if (!empty($studentSectionExamStatus['Section'])) {
                        $section = $studentSectionExamStatus['Section'];
                        if (!$section['archive'] && !$studentSectionExamStatus['StudentsSection']['archive']) {
                            $academicYR = $section['academicyear'];
                        }
                    }

                    if (
                        !empty($studentSectionExamStatus['StudentExamStatus']) &&
                        $studentSectionExamStatus['StudentExamStatus']['academic_status_id'] == DISMISSED_ACADEMIC_STATUS_ID
                    ) {
                        $isTheStudentDismissed = 1;

                        $possibleReadmissionYears = $studentExamStatusesTable->getAcademicYearRange(
                            $studentSectionExamStatus['StudentExamStatus']['academic_year'],
                            $academicYR
                        );

                        $readmitted = $readmissionsTable->find()
                            ->where([
                                'student_id' => $studentId,
                                'registrar_approval' => 1,
                                'academic_commision_approval' => 1,
                                'academic_year IN' => $possibleReadmissionYears
                            ])
                            ->order(['academic_year' => 'DESC', 'semester' => 'DESC', 'modified' => 'DESC'])
                            ->first();

                        if ($readmitted) {
                            $lastReadmittedAcademicYear = $readmitted->academic_year;
                            $isTheStudentReadmitted = 1;
                            $possibleAcademicYears = $studentExamStatusesTable->getAcademicYearRange($lastReadmittedAcademicYear, $academicYR);
                            $this->set(compact('possibleAcademicYears'));
                        }
                    }

                    $isStudentEverReadmitted = $readmissionsTable->find()
                        ->where([
                            'student_id' => $studentId,
                            'registrar_approval' => 1,
                            'academic_commision_approval' => 1
                        ])
                        ->count();

                    $studentAcademicProfile = $studentsTable->getStudentRegisteredAddDropCurriculumResult($studentId, $academicYR);

                    unset($studentAcademicProfile['BasicInfo']['Student']['curriculum']);

                    $studentAttendedSections = $sectionsTable->getStudentSectionHistory($studentId);

                    $this->set(compact(
                        'isTheStudentDismissed',
                        'isTheStudentReadmitted',
                        'studentAcademicProfile',
                        'studentAttendedSections',
                        'studentSectionExamStatus',
                        'otps',
                        'moodleUserDetails',
                        'isStudentEverReadmitted',
                        'academicYR'
                    ));
                } else {
                    $this->Flash->warning(
                        $checkIdIsValid == 0
                            ? __('You don\'t have the privilege to view the selected student\'s profile.')
                            : __('The provided Student ID is not valid.')
                    );
                }
            } else {
                $this->Flash->error(__('Please provide Student ID to view Academic Profile.'));
            }
        }
    }

    public function getModalBox($studentId = null)
    {
        $this->viewBuilder()->setLayout('ajax');

        if ($this->Auth->user('id')) {
            $studentsTable = TableRegistry::getTableLocator()->get('Students');
            $checkIdIsValid = 0;

            if ($this->role_id == ROLE_STUDENT) {
                $checkIdIsValid = $studentsTable->find()
                    ->where(['Students.id' => $this->student_id])
                    ->count();
                $studentId = $this->student_id;
            } else {
                $checkIdIsValid = $studentsTable->find()
                    ->where(['Students.id' => $studentId])
                    ->count();
            }

            if ($checkIdIsValid > 0) {
                $academicYR = $this->AcademicYear->current_academicyear();
                $isTheStudentDismissed = 0;
                $isTheStudentReadmitted = 0;
                $otps = [];
                $moodleUserDetails = [];

                if (SHOW_OTP_TAB_ON_STUDENT_ACADEMIC_PROFILE_FOR_STUDENTS == 1) {
                    $otpsTable = TableRegistry::getTableLocator()->get('Otps');
                    $otps = $otpsTable->find()
                        ->where([
                            'Otps.student_id' => $studentId,
                            'Otps.active' => 1
                        ])
                        ->order(['Otps.modified' => 'DESC', 'Otps.created' => 'DESC'])
                        ->toArray();

                    if (!empty($otps)) {
                        $moodleIntegratedUser = false;
                        foreach ($otps as $otp) {
                            if ($otp->service == 'Elearning' && empty($otp->portal)) {
                                $moodleIntegratedUser = true;
                            }
                        }

                        if ($moodleIntegratedUser) {
                            $moodleUsersTable = TableRegistry::getTableLocator()->get('MoodleUsers');
                            $moodleUserDetails = $moodleUsersTable->find()
                                ->where([
                                    'MoodleUsers.table_id' => $studentId,
                                    'MoodleUsers.role_id' => ROLE_STUDENT
                                ])
                                ->order(['MoodleUsers.created' => 'DESC'])
                                ->first();
                        }
                    }
                }

                $studentSectionExamStatus = $studentsTable->getStudentSection($studentId, null, null);

                if (!empty($studentSectionExamStatus['Section']) && !$studentSectionExamStatus['Section']['archive'] && !$studentSectionExamStatus['Section']['StudentsSection']['archive']) {
                    $academicYR = $studentSectionExamStatus['Section']['academicyear'];
                }

                if (!empty($studentSectionExamStatus['StudentExamStatus']) && $studentSectionExamStatus['StudentExamStatus']['academic_status_id'] == DISMISSED_ACADEMIC_STATUS_ID) {
                    $isTheStudentDismissed = 1;

                    $studentExamStatusTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');
                    $possibleReadmissionYears = $studentExamStatusTable->getAcademicYearRange(
                        $studentSectionExamStatus['StudentExamStatus']['academic_year'],
                        $this->AcademicYear->current_academicyear()
                    );

                    $readmissionsTable = TableRegistry::getTableLocator()->get('Readmissions');
                    $readmitted = $readmissionsTable->find()
                        ->where([
                            'Readmissions.student_id' => $studentId,
                            'Readmissions.registrar_approval' => 1,
                            'Readmissions.academic_commision_approval' => 1,
                            'Readmissions.academic_year IN' => $possibleReadmissionYears
                        ])
                        ->order(['Readmissions.academic_year' => 'DESC', 'Readmissions.semester' => 'DESC', 'Readmissions.modified' => 'DESC'])
                        ->first();

                    if ($readmitted) {
                        $isTheStudentReadmitted = 1;
                        $possibleAcademicYears = $studentExamStatusTable->getAcademicYearRange($readmitted->academic_year, $academicYR);
                        $this->set(compact('possibleAcademicYears'));
                    }
                }

                $studentAcademicProfile = $studentsTable->getStudentRegisteredAddDropCurriculumResult($studentId, $academicYR);
                $sectionsTable = TableRegistry::getTableLocator()->get('Sections');
                $studentAttendedSections = $sectionsTable->getStudentSectionHistory($studentId);

                $this->set(compact('studentAttendedSections', 'studentAcademicProfile', 'studentSectionExamStatus', 'otps', 'moodleUserDetails', 'isTheStudentDismissed', 'isTheStudentReadmitted', 'academicYR'));
            }
        }
    }

    public function profileNotBuildList()
    {
        $limit = 100;
        $name = '';
        $page = '';
        $options = [];
        $session = $this->request->getSession();

        if ($this->request->getParam('pass')) {
            $passedArgs = $this->request->getParam('pass');

            if (!empty($passedArgs['Search.limit'])) {
                $limit = $this->request->getData('Search.limit', $passedArgs['Search.limit']);
            }

            if (!empty($passedArgs['Search.name'])) {
                $name = str_replace('-', '/', trim($passedArgs['Search.name']));
            }

            if (!empty($passedArgs['Search.department_id'])) {
                $this->request->withData('Search.department_id', $passedArgs['Search.department_id']);
            }

            if (!empty($passedArgs['Search.college_id'])) {
                $this->request->withData('Search.college_id', $passedArgs['Search.college_id']);
            }

            $selectedAcademicYear = !empty($passedArgs['Search.academicyear'])
                ? $this->request->getData('Search.academicyear', str_replace('-', '/', $passedArgs['Search.academicyear']))
                : '';

            if (!empty($passedArgs['Search.gender'])) {
                $this->request->withData('Search.gender', $passedArgs['Search.gender']);
            }

            if (!empty($passedArgs['Search.program_id'])) {
                $this->request->withData('Search.program_id', $passedArgs['Search.program_id']);
            }

            if (!empty($passedArgs['Search.program_type_id'])) {
                $this->request->withData('Search.program_type_id', $passedArgs['Search.program_type_id']);
            }

            if (!empty($passedArgs['Search.status'])) {
                $this->request->withData('Search.status', $passedArgs['Search.status']);
            }

            if (!empty($passedArgs['page'])) {
                $page = $this->request->getData('Search.page', $passedArgs['page']);
            }

            if (!empty($passedArgs['sort'])) {
                $this->request->withData('Search.sort', $passedArgs['sort']);
            }

            if (!empty($passedArgs['direction'])) {
                $this->request->withData('Search.direction', $passedArgs['direction']);
            }

            $this->_initSearchIndex();
        }

        $this->_initSearchIndex();

        if ($this->request->getData('search')) {
            $this->request->withParam('pass', []);
            $this->_initClearSessionFilters();
            $this->_initSearchIndex();
        }

        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
        $collegesTable = TableRegistry::getTableLocator()->get('Colleges');

        if ($this->request->getData()) {
            if (!empty($page) && !$this->request->getData('search')) {
                $this->request->withData('Search.page', $page);
            }

            if (!empty($this->request->getData('Search.limit'))) {
                $limit = $this->request->getData('Search.limit');
            }

            if ($this->Auth->user('role_id') == ROLE_DEPARTMENT) {
                $departments = $departmentsTable->find('list')
                    ->where(['Departments.id' => $this->department_id, 'Departments.active' => 1])
                    ->toArray();
                $options['conditions'][] = ['Students.department_id' => $this->department_id];
                $this->request->withData('Search.department_id', $this->department_id);
            } elseif ($this->Auth->user('role_id') == ROLE_COLLEGE) {
                $departments = [];
                if (!$this->onlyPre) {
                    $departments = $departmentsTable->find('list')
                        ->where(['Departments.college_id IN' => $this->college_ids, 'Departments.active' => 1])
                        ->toArray();
                }

                if (!empty($this->request->getData('Search.department_id'))) {
                    $options['conditions'][] = ['Students.department_id' => $this->request->getData('Search.department_id')];
                } else {
                    $options['conditions'][] = ['Students.college_id IN' => $this->college_ids];
                }

                $this->request->withData('Search.college_id', $this->college_id);
            } elseif ($this->Auth->user('role_id') == ROLE_REGISTRAR) {
                if (!empty($this->department_ids)) {
                    $colleges = [];
                    $departments = $departmentsTable->find('list')
                        ->where(['Departments.id IN' => $this->department_ids, 'Departments.active' => 1])
                        ->toArray();

                    if (!empty($this->request->getData('Search.department_id'))) {
                        $options['conditions'][] = ['Students.department_id' => $this->request->getData('Search.department_id')];
                    } else {
                        $options['conditions'][] = ['Students.department_id IN' => $this->department_ids];
                    }
                } elseif (!empty($this->college_ids)) {
                    $departments = [];
                    $colleges = $collegesTable->find('list')
                        ->where(['Colleges.id IN' => $this->college_ids, 'Colleges.active' => 1])
                        ->toArray();

                    if (!empty($this->request->getData('Search.college_id'))) {
                        $options['conditions'][] = ['Students.college_id' => $this->request->getData('Search.college_id'), 'Students.department_id IS NULL'];
                    } else {
                        $options['conditions'][] = ['Students.college_id IN' => $this->college_ids, 'Students.department_id IS NULL'];
                    }
                }
            } elseif ($this->Auth->user('role_id') == ROLE_STUDENT) {
                $this->request->withParam('pass', [])->withData([]);
                return $this->redirect(['action' => 'index']);
            } else {
                $departments = $departmentsTable->find('list')
                    ->where(['Departments.active' => 1])
                    ->toArray();
                $colleges = $collegesTable->find('list')
                    ->where(['Colleges.active' => 1])
                    ->toArray();

                if (!empty($this->request->getData('Search.department_id'))) {
                    $options['conditions'][] = ['Students.department_id' => $this->request->getData('Search.department_id')];
                } elseif (empty($this->request->getData('Search.department_id')) && !empty($this->request->getData('Search.college_id'))) {
                    $departments = $departmentsTable->find('list')
                        ->where(['Departments.college_id' => $this->request->getData('Search.college_id'), 'Departments.active' => 1])
                        ->toArray();
                    $options['conditions'][] = ['Students.college_id' => $this->request->getData('Search.college_id')];
                } else {
                    if (!empty($departments) && !empty($colleges)) {
                        $options['conditions'][] = [
                            'OR' => [
                                'Students.college_id IN' => $this->college_ids,
                                'Students.department_id IN' => $this->department_ids
                            ]
                        ];
                    } elseif (!empty($this->college_ids)) {
                        $options['conditions'][] = ['Students.college_id IN' => $this->college_ids];
                    } elseif (!empty($this->department_ids)) {
                        $options['conditions'][] = ['Students.department_id IN' => $this->department_ids];
                    }
                }
            }

            if (!empty($selectedAcademicYear)) {
                $options['conditions'][] = ['Students.academicyear' => $selectedAcademicYear];
            }

            if (!empty($this->request->getData('Search.program_id'))) {
                $options['conditions'][] = ['Students.program_id' => $this->request->getData('Search.program_id')];
            } elseif (empty($this->request->getData('Search.program_id')) && $this->Auth->user('role_id') == ROLE_REGISTRAR) {
                $options['conditions'][] = ['Students.program_id IN' => $this->program_ids];
            }

            if (!empty($this->request->getData('Search.program_type_id'))) {
                $options['conditions'][] = ['Students.program_type_id' => $this->request->getData('Search.program_type_id')];
            } elseif (empty($this->request->getData('Search.program_type_id')) && $this->Auth->user('role_id') == ROLE_REGISTRAR) {
                $options['conditions'][] = ['Students.program_type_id IN' => $this->program_type_ids];
            }

            if (!empty($name)) {
                $options['conditions'][] = [
                    'OR' => [
                        'Students.first_name LIKE' => '%' . $name . '%',
                        'Students.middle_name LIKE' => '%' . $name . '%',
                        'Students.last_name LIKE' => '%' . $name . '%',
                        'Students.studentnumber LIKE' => $name . '%'
                    ]
                ];
            }

            if (!empty($this->request->getData('Search.college_id')) && $this->Auth->user('role_id') != ROLE_REGISTRAR) {
                $departments = $departmentsTable->find('list')
                    ->where(['Departments.college_id' => $this->request->getData('Search.college_id'), 'Departments.active' => 1])
                    ->toArray();
            }

            if (!empty($this->request->getData('Search.gender'))) {
                $options['conditions'][] = ['Students.gender LIKE' => $this->request->getData('Search.gender')];
            }

            if (!empty($this->request->getData('Search.status'))) {
                $options['conditions'][] = ['Students.graduated' => $this->request->getData('Search.status')];
            }
        } else {
            $notBuildFor = date('Y-m-d', strtotime('-' . DAYS_BACK_PROFILE . ' days'));

            if ($this->Auth->user('role_id') == ROLE_COLLEGE) {
                $departments = [];
                if (!$this->onlyPre) {
                    $departments = $departmentsTable->find('list')
                        ->where(['Departments.college_id IN' => $this->college_ids, 'Departments.active' => 1])
                        ->toArray();
                }

                if (empty($departments)) {
                    $options['conditions'][] = ['Students.college_id IN' => $this->college_ids];
                } else {
                    $options['conditions'][] = [
                        'OR' => [
                            'Students.college_id IN' => $this->college_ids,
                            'Students.department_id IN' => $this->department_ids
                        ]
                    ];
                }

                $this->request->withData('Search.college_id', $this->college_id);
            } elseif ($this->Auth->user('role_id') == ROLE_DEPARTMENT) {
                $departments = $departmentsTable->find('list')
                    ->where(['Departments.id IN' => $this->department_ids])
                    ->toArray();
                $options['conditions'][] = ['Students.department_id IN' => $this->department_ids];
                $this->request->withData('Search.department_id', $this->department_id);
            } elseif ($this->Auth->user('role_id') == ROLE_REGISTRAR) {
                if (!empty($this->department_ids)) {
                    $colleges = [];
                    $departments = $departmentsTable->find('list')
                        ->where(['Departments.id IN' => $this->department_ids, 'Departments.active' => 1])
                        ->toArray();
                    $options['conditions'][] = [
                        'Students.department_id IN' => $this->department_ids,
                        'Students.program_id IN' => $this->program_ids,
                        'Students.program_type_id IN' => $this->program_type_ids
                    ];
                } elseif (!empty($this->college_ids)) {
                    $departments = [];
                    $colleges = $collegesTable->find('list')
                        ->where(['Colleges.id IN' => $this->college_ids, 'Colleges.active' => 1])
                        ->toArray();
                    $options['conditions'][] = [
                        'Students.college_id IN' => $this->college_ids,
                        'Students.department_id IS NULL',
                        'Students.program_id IN' => $this->program_ids,
                        'Students.program_type_id IN' => $this->program_type_ids
                    ];
                }
            } elseif ($this->Auth->user('role_id') == ROLE_STUDENT) {
                $options['conditions'][] = ['Students.id' => $this->student_id];
            } else {
                $departments = $departmentsTable->find('list')
                    ->where(['Departments.active' => 1])
                    ->toArray();
                $colleges = $collegesTable->find('list')
                    ->where(['Colleges.active' => 1])
                    ->toArray();

                if (!empty($departments) && !empty($colleges)) {
                    $options['conditions'][] = [
                        'OR' => [
                            'Students.department_id IN' => $this->department_ids,
                            'Students.college_id IN' => $this->college_ids
                        ]
                    ];
                } elseif (!empty($departments)) {
                    $options['conditions'][] = ['Students.department_id IN' => $this->department_ids];
                } elseif (!empty($colleges)) {
                    $options['conditions'][] = ['Students.college_id IN' => $this->college_ids];
                }
            }

            if (!empty($options['conditions'])) {
                $options['conditions'][] = ['Students.id IS NOT NULL'];
                $options['conditions'][] = ['Students.graduated' => 0];
                $options['conditions'][] = ['Students.created >=' => $notBuildFor];
            }
        }

        $students = [];
        if (!empty($options['conditions'])) {
            $options['conditions'][] = ['Students.id NOT IN' => $studentsTable->Contacts->find()->select(['student_id'])];

            $query = $studentsTable->find()
                ->where($options['conditions'])
                ->contain([
                    'Departments' => ['fields' => ['id', 'name', 'shortname', 'college_id', 'institution_code']],
                    'Colleges' => [
                        'fields' => ['id', 'name', 'shortname', 'institution_code', 'campus_id'],
                        'Campuses' => ['fields' => ['id', 'name', 'campus_code']]
                    ],
                    'Programs' => ['fields' => ['id', 'name', 'shortname']],
                    'AcceptedStudents' => ['fields' => ['id']],
                    'ProgramTypes' => ['fields' => ['id', 'name', 'shortname']],
                    'Contacts',
                    'Curriculums' => ['fields' => ['id', 'name', 'year_introduced', 'type_credit', 'english_degree_nomenclature', 'active']],
                    'Specializations' => ['fields' => ['id', 'name']],
                    'Regions' => ['fields' => ['id', 'name', 'short']],
                    'Zones' => ['fields' => ['id', 'name', 'short']],
                    'Woredas' => ['fields' => ['id', 'name', 'code']],
                    'Cities' => ['fields' => ['id', 'name', 'short']]
                ])
                ->order([
                    'Students.admissionyear' => 'DESC',
                    'Students.department_id' => 'ASC',
                    'Students.program_type_id' => 'ASC',
                    'Students.studentnumber' => 'ASC',
                    'Students.first_name' => 'ASC',
                    'Students.middle_name' => 'ASC',
                    'Students.last_name' => 'ASC',
                    'Students.created' => 'DESC'
                ]);

            $this->paginate = [
                'limit' => $limit,
                'maxLimit' => $limit,
                'page' => $page
            ];

            try {
                $students = $this->paginate($query);
                $this->set(compact('students'));

                if (!empty($students)) {

                    $session->delete('students');
                    $session->write('students', $students->toArray());
                }
            } catch (NotFoundException $e) {
                $this->request->getData('Search.page', null);
                $this->request->getData('Search.sort', null);
                $this->request->getData('Search.direction', null);
                $this->request->getData('Student.page', null);
                $this->request->getData('Student.sort', null);
                $this->request->getData('Student.direction', null);
                $this->request->withParam('pass', []);
                $this->_initSearchIndex();
                return $this->redirect(['action' => 'profile_not_build_list']);
            }
        }

        $turnOffSearch = empty($students) && !empty($options['conditions']) ? false : true;
        if (empty($students) && !empty($options['conditions'])) {
            $this->Flash->info(__('No Student is found with the given search criteria.'));
        }

        $this->set(compact('colleges', 'departments', 'turnOffSearch', 'limit', 'name'));
    }

    public function nameChange($id = null)
    {
        $studentsTable = TableRegistry::getTableLocator()->get('Students');

        if ($this->request->is('post') && $this->request->getData('searchStudentName')) {
            $everythingFine = true;
            $studentId = null;

            if (empty($this->request->getData('Student'))) {
                $this->Flash->error(__('Please provide the student number (ID) you want to change name.'));
                $everythingFine = false;
            }

            $departmentId = !empty($this->department_ids) ? $this->department_ids : ($this->department_id ?? null);
            $collegeId = null;

            if (!$departmentId && $this->role_id == ROLE_REGISTRAR) {
                $departmentId = !empty($this->department_ids) ? $this->department_ids : null;
                $collegeId = !empty($this->college_ids) ? $this->college_ids : null;
            }

            if ($everythingFine) {
                $checkIdIsValid = 0;
                if (!empty($departmentId)) {
                    $checkIdIsValid = $studentsTable->find()
                        ->where([
                            'Students.studentnumber LIKE' => trim($this->request->getData('Student.studentnumber')) . '%',
                            'Students.department_id IN' => (array)$departmentId
                        ])
                        ->count();
                } elseif (!empty($collegeId)) {
                    $checkIdIsValid = $studentsTable->find()
                        ->where([
                            'Students.studentnumber LIKE' => trim($this->request->getData('Student.studentnumber')) . '%',
                            'Students.college_id IN' => (array)$collegeId,
                            'Students.department_id IS NULL'
                        ])
                        ->count();
                }

                if ($checkIdIsValid > 0) {
                    $student = $studentsTable->find()
                        ->where([
                            'Students.studentnumber LIKE' => trim($this->request->getData('Student.studentnumber')) . '%',
                            'Students.department_id IN' => (array)$departmentId
                        ])
                        ->first();
                    $studentId = $student->id;
                } else {
                    $everythingFine = false;
                    $this->Flash->error(__('The provided student number is not valid or you don\'t have the privilege to change name for this student.'));
                }
            }

            if ($everythingFine) {
                $testData = $studentsTable->find()
                    ->where(['Students.id' => $studentId])
                    ->first();
                $this->request->withData($studentsTable->StudentNameHistories->reformat($testData));
            }
        }

        if ($this->request->is('post') && $this->request->getData('changeName')) {
            $studentNameHistoriesTable = TableRegistry::getTableLocator()->get('StudentNameHistories');
            $data = $studentNameHistoriesTable->reformat($this->request->getData());

            $isThereChangeInFullName = !(
                $data['StudentNameHistory']['to_first_name'] === $data['StudentNameHistory']['from_first_name'] &&
                $data['StudentNameHistory']['to_middle_name'] === $data['StudentNameHistory']['from_middle_name'] &&
                $data['StudentNameHistory']['to_last_name'] === $data['StudentNameHistory']['from_last_name']
            );

            if ($isThereChangeInFullName) {
                $historyEntity = $studentNameHistoriesTable->newEntity($data);
                if ($studentNameHistoriesTable->save($historyEntity)) {
                    $student = $studentsTable->get($data['StudentNameHistory']['student_id']);
                    $student->amharic_first_name = $data['StudentNameHistory']['to_amharic_first_name'];
                    $student->amharic_middle_name = $data['StudentNameHistory']['to_amharic_middle_name'];
                    $student->amharic_last_name = $data['StudentNameHistory']['to_amharic_last_name'];
                    $student->first_name = $data['StudentNameHistory']['to_first_name'];
                    $student->middle_name = $data['StudentNameHistory']['to_middle_name'];
                    $student->last_name = $data['StudentNameHistory']['to_last_name'];

                    if ($studentsTable->save($student)) {
                        $this->Flash->success(__('Student name change has been saved.'));
                        return $this->redirect($this->referer());
                    } else {
                        $this->Flash->error(__('Student name change could not be saved. Please, try again.'));
                        $studentNameHistoriesTable->delete($historyEntity);
                    }
                } else {
                    $this->Flash->error(__('Student name change could not be saved. Please, try again.'));
                    return $this->redirect($this->referer());
                }
            } else {
                $this->Flash->info(__('No change detected in previous and new student name. Nothing updated.'));
                return $this->redirect($this->referer());
            }
        }

        if (!$this->request->getData() && $id) {
            $testData = $studentsTable->find()
                ->where(['Students.id' => $id])
                ->first();
            $this->request->withData($studentsTable->StudentNameHistories->reformat($testData));
        }
    }

    public function departmentIssuePassword($sectionId = null)
    {
        $this->_issuePassword($sectionId, 0);
    }

    public function freshmanIssuePassword($sectionId = null)
    {
        $this->_issuePassword($sectionId, 1);
    }



    private function _issuePassword($sectionId = null, $freshmanProgram = 0)
    {
        $sectionsTable = TableRegistry::getTableLocator()->get('Sections');
        $studentsTable = TableRegistry::getTableLocator()->get('Students');

        $programs = $sectionsTable->Programs->find('list')->toArray();
        $programTypes = $sectionsTable->ProgramTypes->find('list')->toArray();

        if ($freshmanProgram == 0) {
            $yearLevels = $sectionsTable->YearLevels->find('list')
                ->where(['YearLevels.department_id' => $this->department_id])
                ->toArray();
        } else {
            $yearLevels = [0 => 'Pre/Freshman'];
        }

        $resetPasswordByEmail = (ALLOW_STUDENTS_TO_RESET_PASSWORD_BY_EMAIL == 1) ? 1 : 0;

        $sectionAcYears = $this->AcademicYear->academicYearInArray(
            explode('/', $this->AcademicYear->currentAcademicYear())[0] - ACY_BACK_FOR_SECTION_ADD,
            explode('/', $this->AcademicYear->currentAcademicYear())[0]
        );

        $departments = [0 => 0];
        $sections = [];
        $yearLevelSelected = null;
        $programId = null;
        $programTypeId = null;
        $studentsInSection = [];

        if ($this->request->is('post') && $this->request->getData('listSections')) {
            $this->_initSearchStudent();

            $options = [
                'conditions' => [
                    'Sections.status' => 0,
                    'Sections.program_id' => $this->request->getData('Student.program_id'),
                    'Sections.program_type_id' => $this->request->getData('Student.program_type_id')
                ],
                'order' => [
                    'Sections.academicyear' => 'DESC',
                    'Sections.year_level_id' => 'ASC',
                    'Sections.id' => 'ASC',
                    'Sections.name' => 'ASC'
                ],
                'contain' => ['YearLevels', 'Programs', 'ProgramTypes']
            ];

            if ($freshmanProgram == 1) {
                $options['conditions'] = array_merge($options['conditions'], [
                    'Sections.college_id' => $this->college_id,
                    'Sections.status' => 0,
                    'Sections.department_id IS NULL',
                    'Sections.academic_year' => $this->request->getData('Student.academic_year')
                ]);
            } else {
                $options['conditions'] = array_merge($options['conditions'], [
                    'Sections.department_id' => $this->department_id,
                    'Sections.year_level_id' => $this->request->getData('Student.year_level_id'),
                    'Sections.academic_year IN' => $sectionAcYears
                ]);
            }

            $sectionsDetailAll = $sectionsTable->find('all', $options)->toArray();

            if (!empty($sectionsDetailAll)) {
                foreach ($sectionsDetailAll as $secValue) {
                    $sections[$secValue->program->name][$secValue->id] = sprintf(
                        '%s (%s, %s)',
                        $secValue->name,
                        !empty($secValue->year_level->name) ? $secValue->year_level->name : ($secValue->program_id == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st'),
                        $secValue->academicyear
                    );
                }
            }

            if ($freshmanProgram == 1 && !empty($sections)) {
                $sections['pre'] = 'All';
                asort($sections);
            }

            if (empty($sections)) {
                $this->Flash->info(__('No section is found with the given search criteria.'));
            } else {
                $sections = [0 => '[Select Section]'] + $sections;
            }

            $yearLevelSelected = $this->request->getData('Student.year_level_id');
            $programId = $this->request->getData('Student.program_id');
            $programTypeId = $this->request->getData('Student.program_type_id');
        }

        if ($this->request->is('post') && $this->request->getData('issueStudentPassword') || (!empty($sectionId) && ($sectionId != 0 || strtolower($sectionId) == 'pre'))) {
            $this->_initSearchStudent();

            if ($this->request->getData('issueStudentPassword')) {
                $sectionId = $this->request->getData('Student.section_id');
            }

            $sectionDetail = null;
            if (!empty($sectionId) && $sectionId != 'pre' && $sectionId > 0) {
                $sectionDetail = $sectionsTable->find()
                    ->where(['Sections.id' => $sectionId])
                    ->contain([
                        'Programs' => ['fields' => ['id', 'name']],
                        'ProgramTypes' => ['fields' => ['id', 'name']],
                        'YearLevels' => ['fields' => ['id', 'name']]
                    ])
                    ->first();

                if (ALLOW_STUDENTS_TO_RESET_PASSWORD_BY_EMAIL == 'AUTO') {
                    $generalSettingsTable = TableRegistry::getTableLocator()->get('GeneralSettings');
                    $generalSettings = $generalSettingsTable->getAllGeneralSettingsByStudentByProgramIdOrBySectionID(null, null, null, $sectionId);
                    if (!empty($generalSettings['GeneralSetting'])) {
                        $resetPasswordByEmail = $generalSettings['GeneralSetting']['allowStudentsToResetPasswordByEmail'];
                    }
                }

                $yearLevelSelected = $sectionDetail->year_level_id;
                $programId = $sectionDetail->program_id;
                $programTypeId = $sectionDetail->program_type_id;
            }

            if (strtolower($sectionId) == 'pre') {
                $studentsInSection = $studentsTable->listStudentByAdmissionYear(
                    null,
                    $this->college_id,
                    $this->request->getData('Student.academic_year'),
                    $this->request->getData('Student.name'),
                    0
                );
                $this->request->withData('Student.section_id', 'pre');
            } else {
                $studentsInSection = $sectionsTable->getSectionStudents($sectionId, $this->request->getData('Student.name'));
                $this->request->withData('Student.section_id', $sectionId);
            }

            $options = [
                'conditions' => [
                    'Sections.status' => 0,
                    'Sections.program_id' => $this->request->getData('Student.program_id'),
                    'Sections.program_type_id' => $this->request->getData('Student.program_type_id')
                ],
                'order' => [
                    'Sections.academic_year' => 'DESC',
                    'Sections.year_level_id' => 'ASC',
                    'Sections.id' => 'ASC',
                    'Sections.name' => 'ASC'
                ],
                'contain' => ['YearLevels', 'Programs', 'ProgramTypes']
            ];

            if ($freshmanProgram == 1) {
                $options['conditions'] = array_merge($options['conditions'], [
                    'Sections.college_id' => $this->college_id,
                    'Sections.status' => 0,
                    'Sections.department_id IS NULL',
                    'Sections.academic_year' => $this->request->getData('Student.academic_year')
                ]);
            } else {
                $options['conditions'] = array_merge($options['conditions'], [
                    'Sections.department_id' => $this->department_id,
                    'Sections.year_level_id' => $this->request->getData('Student.year_level_id'),
                    'Sections.academic_year IN' => $sectionAcYears
                ]);
            }

            $sectionsDetailAll = $sectionsTable->find('all', $options)->toArray();

            if (!empty($sectionsDetailAll)) {
                foreach ($sectionsDetailAll as $secValue) {
                    $sections[$secValue->program->name][$secValue->id] = sprintf(
                        '%s (%s, %s)',
                        $secValue->name,
                        !empty($secValue->year_level->name) ? $secValue->year_level->name : ($secValue->program_id == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st'),
                        $secValue->academicyear
                    );
                }
            }

            if ($freshmanProgram == 1 && !empty($sections)) {
                $sections['pre'] = 'All';
                asort($sections);
            }

            if (empty($sections)) {
                $this->Flash->info(__('There is no section with the selected search criteria.'));
            } else {
                $sections = [0 => '[Select Section]'] + $sections;
            }
        }

        if ($this->request->is('post') && $this->request->getData('issueStudentPassword')) {
            $studentIds = [];

            if (!empty($this->request->getData('Student'))) {
                foreach ($this->request->getData('Student') as $key => $student) {
                    if (is_numeric($key) && !empty($student['student_id']) && !empty($student['gp'])) {
                        $studentDetail = [
                            'student_id' => $student['student_id'],
                            'flat_password' => (empty($this->request->getData('Student.common_password')) || strlen($this->request->getData('Student.common_password')) < 5)
                                ? $this->_generatePassword(5)
                                : $this->request->getData('Student.common_password')
                        ];
                        $studentDetail['hashed_password'] = (new DefaultPasswordHasher())->hash(trim($studentDetail['flat_password']));
                        $studentIds[] = $studentDetail;
                    }
                }
            }

            if (empty($studentIds)) {
                $this->Flash->error(__('You are required to select at least one student.'));
            } else {
                $studentPasswords = $studentsTable->getStudentPassword($studentIds);

                if (empty($studentPasswords)) {
                    $this->Flash->error(__('ERROR: Unable to issue password for the selected students. Please try again.'));
                } else {
                    $this->set(compact('studentPasswords'));

                    $sectionForFileName = !empty($sectionDetail)
                        ? sprintf(
                            '%s_%s_%s_%s_%s',
                            $sectionDetail->name,
                            $sectionDetail->year_level->name ?? '',
                            str_replace('/', '_', $sectionDetail->academicyear),
                            $sectionDetail->program->name,
                            $sectionDetail->program_type->name
                        )
                        : sprintf(
                            'All_%s_%s',
                            $this->request->getData('Student.program_id') == PROGRAM_REMEDIAL ? 'Remedial_Sections' : 'Pre_Freshman_Sections',
                            str_replace('/', '_', $this->request->getData('Student.academic_year'))
                        );

                    $this->set(compact('sectionForFileName', 'resetPasswordByEmail'));

                    $this->response = $this->response->withType('application/pdf');
                    $this->viewBuilder()->setLayout('pdf/default');

                    $template = $this->request->getData('Student.single_page') == 'yes'
                        ? 'mass_password_issue_single_page_pdf'
                        : 'issue_password_pdf';

                    $this->render($template);
                    return;
                }
            }
        }

        $this->set(compact(
            'programs',
            'programTypes',
            'departments',
            'yearLevels',
            'yearLevelSelected',
            'programId',
            'programTypeId',
            'sectionId',
            'sections',
            'studentsInSection'
        ));

        $this->render('issue_password_list');
    }

    private function _generatePassword($length = '')
    {
        $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $max = strlen($str);
        $length = (int)$length ?: rand(8, 12);

        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $str[random_int(0, $max - 1)];
        }

        return $password;
    }

    public function autoYearlevelUpdate()
    {
        $studentsTable = TableRegistry::getTableLocator()->get('Students');

        $studentsSections = $studentsTable->find()
            ->where(['Students.graduated' => 0])
            ->contain([
                'CourseRegistrations' => [
                    'sort' => [
                        'CourseRegistrations.academic_year' => 'DESC',
                        'CourseRegistrations.semester' => 'DESC',
                        'CourseRegistrations.id' => 'DESC'
                    ],
                    'limit' => 1
                ]
            ])
            ->select(['Students.id', 'Students.studentnumber', 'Students.full_name', 'Students.department_id', 'Students.program_id'])
            ->toArray();

        $studentList = [];
        $count = 0;

        foreach ($studentsSections as $student) {
            $studentList['Student'][$count]['id'] = $student->id;

            if (is_null($student->department_id) && (empty($student->course_registrations[0]->year_level_id) || empty($student->course_registrations))) {
                $studentList['Student'][$count]['yearLevel'] = 'Pre/1st';
            } elseif (empty($student->course_registrations) || empty($student->course_registrations[0]->year_level_id)) {
                $studentList['Student'][$count]['yearLevel'] = '1st';
            } else {
                $yearLevelsTable = TableRegistry::getTableLocator()->get('YearLevels');
                $yearLevel = $yearLevelsTable->find()
                    ->select(['name'])
                    ->where(['YearLevels.id' => $student->course_registrations[0]->year_level_id])
                    ->first();

                if ($yearLevel) {
                    $studentList['Student'][$count]['yearLevel'] = $yearLevel->name;
                }
            }

            $count++;
        }

        if (!empty($studentList['Student'])) {
            $studentsTable->saveMany($studentsTable->newEntities($studentList['Student']), ['validate' => false]);
        }
    }

    public function nameList()
    {
        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $session = $this->request->getSession();

        $this->paginate = [
            'contain' => ['Departments', 'Curriculums', 'ProgramTypes', 'Programs', 'Colleges']
        ];

        if ($this->request->is('post') && $this->request->getData('viewPDF')) {

            $searchSession = $session->check('search_data') ? $session->read('search_data') : null;

            $this->request->withData('Student', $searchSession);
        }

        if ($this->request->getParam('pass') && !empty($this->request->getParam('pass')['page'])) {
            $this->_initSearchName();
            $this->request->withData('Student.page', $this->request->getParam('pass')['page']);
            $this->_initSearchName();
        }

        if ($this->request->is('post') && $this->request->getData('listStudentsForNameChange')) {
            $this->_initSearchName();
        }

        if (!empty($this->request->getData('Student.department_id'))) {
            $departmentId = $this->request->getData('Student.department_id');
            $collegeId = explode('~', $departmentId);
            if (count($collegeId) > 1) {
                $this->paginate['conditions'][] = ['Students.college_id' => $collegeId[1]];
            } else {
                $this->paginate['conditions'][] = ['Students.department_id' => $departmentId];
            }
        }

        if (!empty($this->request->getData('Student.program_id'))) {
            $this->paginate['conditions'][] = ['Students.program_id' => $this->request->getData('Student.program_id')];
        }

        if (!empty($this->request->getData('Student.program_type_id'))) {
            $this->paginate['conditions'][] = ['Students.program_type_id' => $this->request->getData('Student.program_type_id')];
        }

        if (!empty($this->request->getData('Student.studentnumber'))) {
            unset($this->paginate['conditions']);
            $this->paginate['conditions'][] = ['Students.studentnumber' => $this->request->getData('Student.studentnumber')];
        }

        if (!empty($this->request->getData('Student.admission_year'))) {
            $this->paginate['conditions'][] = [
                'Students.admissionyear' => $this->AcademicYear->getAcademicYearBegainingDate($this->request->getData('Student.admission_year'), 'I')
            ];
        }

        if (!empty($this->request->getData('Student.name'))) {
            unset($this->paginate['conditions']);
            $this->paginate['conditions'][] = ['Students.first_name LIKE' => trim($this->request->getData('Student.name')) . '%'];
        }

        if (!empty($this->request->getData('Student.page'))) {
            $this->paginate['page'] = $this->request->getData('Student.page');
        }

        $studentsForNameList = [];
        if (!empty($this->request->getData()) && !empty($this->paginate['conditions'])) {
            $this->paginate($studentsTable);
            $studentsForNameList = $this->paginate($studentsTable);
        }

        if (empty($studentsForNameList) && !empty($this->request->getData())) {
            $this->Flash->info(__('There is no student in the system based on the given criteria.'));
        }

        $programs = $studentsTable->Programs->find('list')->toArray();
        $programTypes = $studentsTable->ProgramTypes->find('list')->toArray();
        $departments = $studentsTable->Departments->allDepartmentsByCollege2(1, $this->department_ids, $this->college_ids);

        $programs = [0 => 'All Programs'] + $programs;
        $programTypes = [0 => 'All Program Types'] + $programTypes;
        $departments = [0 => 'All University Students'] + $departments;

        $defaultDepartmentId = null;
        $defaultProgramId = null;
        $defaultProgramTypeId = null;

        if ($this->request->is('post') && $this->request->getData('viewPDF') && !empty($studentsForNameList)) {
            $studentsForNameListPdf = [];
            foreach ($studentsForNameList as $v) {
                $gDObj = new \DateTime($v->admissionyear);
                $admissionYear = explode('-', $v->admissionyear);
                $eGYear = $this->EthiopicDateTime->GetEthiopicYear(
                    $gDObj->format('j'),
                    $gDObj->format('n'),
                    $gDObj->format('Y')
                );
                $gAcademicYear = $this->AcademicYear->get_academicyear($admissionYear[1], $admissionYear[0]);
                $studentsForNameListPdf[
                sprintf(
                    '%s~%s~%s~%s(%s E.C)',
                    $v->department->name,
                    $v->program->name,
                    $v->program_type->name,
                    $gAcademicYear,
                    $eGYear
                )
                ][] = $v;
            }

            $this->set(compact('studentsForNameListPdf', 'defaultacademicyear'));
            $this->response = $this->response->withType('application/pdf');
            $this->viewBuilder()->setLayout('pdf');
            $this->render('name_list_pdf');
        } elseif ($this->request->is('post') && $this->request->getData('viewPDF')) {
            $this->Flash->info(__('EMPTY DATA: Unable to generate PDF.'));
        }

        $this->set(compact(
            'programs',
            'programTypes',
            'departments',
            'studentsForNameList',
            'defaultDepartmentId',
            'defaultProgramId',
            'defaultProgramTypeId',
        ));
    }

    private function _initSearchName()
    {
        if (!empty($this->request->getData('Student'))) {
            $this->Session->write('search_data', $this->request->getData('Student'));
        } elseif ($this->Session->check('search_data')) {
            $this->request->withData('Student', $this->Session->read('search_data'));
        }
    }

    public function correctName($id)
    {
        if (!$id) {
            $this->Flash->error(__('Invalid ID'));
            return $this->redirect($this->referer());
        }

        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $checkEligibilityToEdit = 0;

        if (!empty($this->college_ids)) {
            $checkEligibilityToEdit = $studentsTable->find()
                ->where([
                    'Students.college_id IN' => $this->college_ids,
                    'Students.id' => $id,
                    'Students.program_id IN' => $this->program_ids,
                    'Students.program_type_id IN' => $this->program_type_ids
                ])
                ->count();
        } elseif (!empty($this->department_ids)) {
            $checkEligibilityToEdit = $studentsTable->find()
                ->where([
                    'Students.department_id IN' => $this->department_ids,
                    'Students.id' => $id,
                    'Students.program_id IN' => $this->program_ids,
                    'Students.program_type_id IN' => $this->program_type_ids
                ])
                ->count();
        }

        if ($checkEligibilityToEdit == 0) {
            $this->Flash->error(__('You are not eligible to correct the student name. This happens when you are trying to edit a student\'s name which you are not assigned to edit.'));
            return $this->redirect($this->referer());
        }

        if ($this->request->is(['post', 'put']) && $this->request->getData('correctName')) {
            $student = $studentsTable->patchEntity($studentsTable->get($id), $this->request->getData());
            if ($studentsTable->save($student)) {
                $this->Flash->success(__('The student name has been updated.'));
            } else {
                $this->Flash->error(__('The student name could not be saved. Please check other required fields are updated in student profile and try again.'));
            }
            return $this->redirect($this->referer());
        }

        $studentDetail = $studentsTable->find()
            ->where(['Students.id' => $id])
            ->first();

        if (empty($this->request->getData())) {
            $this->request->withData($studentDetail->toArray());
        }

        $this->set(compact('studentDetail'));
    }

    private function _autoRegistrationUpdate($publishedCourseId)
    {
        $latestAcademicYear = $this->AcademicYear->current_academicyear();

        $publishedCoursesTable = TableRegistry::getTableLocator()->get('PublishedCourses');
        $publishedCourseDetail = $publishedCoursesTable->find()
            ->where(['PublishedCourses.id' => $publishedCourseId])
            ->first();

        $studentsSectionsTable = TableRegistry::getTableLocator()->get('StudentsSections');
        $studentsSections = $studentsSectionsTable->find()
            ->where(['StudentsSections.section_id' => $publishedCourseDetail->section_id])
            ->toArray();

        $studentList = [];
        $count = 0;

        foreach ($studentsSections as $v) {
            $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
            $registered = $courseRegistrationsTable->find()
                ->where([
                    'CourseRegistrations.published_course_id' => $publishedCourseId,
                    'CourseRegistrations.student_id' => $v->student_id
                ])
                ->first();

            if (empty($registered)) {
                $studentsTable = TableRegistry::getTableLocator()->get('Students');
                $passedOrFailed = $studentsTable->StudentExamStatuses->getStudentLastExamStatus($v->student_id, $latestAcademicYear);

                if (in_array($passedOrFailed, [1, 3])) {
                    $studentList['CourseRegistration'][$count] = [
                        'year_level_id' => $publishedCourseDetail->year_level_id,
                        'section_id' => $publishedCourseDetail->section_id,
                        'semester' => $publishedCourseDetail->semester,
                        'academic_year' => $publishedCourseDetail->academic_year,
                        'student_id' => $v->student_id,
                        'published_course_id' => $publishedCourseDetail->id,
                        'created' => $publishedCourseDetail->created,
                        'modified' => $publishedCourseDetail->modified
                    ];
                    $count++;
                }
            }
        }

        if (!empty($studentList['CourseRegistration'])) {
            $courseRegistrationsTable->saveMany($courseRegistrationsTable->newEntities($studentList['CourseRegistration']), ['validate' => false]);
        }
    }

    public function scanProfilePhoto()
    {
        if ($this->request->is('post') && $this->request->getData('Synchronize')) {
            $path = WWW_ROOT . 'media/transfer/img/';
            $allImages = $this->_getNewestFN($path);
            $count = 0;

            if (!empty($allImages)) {
                $studentsTable = TableRegistry::getTableLocator()->get('Students');
                $photosTable = TableRegistry::getTableLocator()->get('Photos');

                foreach ($allImages as $image) {
                    $imageFileName = str_replace($path, '', $image);
                    $studentnumberWithImage = str_replace('-', '/', $imageFileName);
                    $studentnumber = explode('.jpg', $studentnumberWithImage)[0];

                    $student = $studentsTable->find()
                        ->where(['Students.studentnumber' => $studentnumber])
                        ->first();

                    if ($student) {
                        $isUploadedAlready = $photosTable->find()
                            ->where([
                                'Photos.model' => 'Student',
                                'Photos.foreign_key' => $student->id,
                                'Photos.group' => 'profile'
                            ])
                            ->first();

                        $attachmentModel = [
                            'model' => 'Student',
                            'foreign_key' => $student->id,
                            'dirname' => 'img',
                            'basename' => $imageFileName,
                            'checksum' => md5($imageFileName),
                            'group' => 'profile'
                        ];

                        if ($isUploadedAlready) {
                            $attachmentModel['id'] = $isUploadedAlready->id;
                        }

                        $photoEntity = $isUploadedAlready
                            ? $photosTable->patchEntity($isUploadedAlready, $attachmentModel)
                            : $photosTable->newEntity($attachmentModel);

                        if ($photosTable->save($photoEntity)) {
                            $count++;
                        }
                    }
                }
            }

            if ($count) {
                $this->Flash->success(__('The dropped profile pictures of students have been synchronized, processing {0} file(s).', $count));
            }
        }
    }

    private function _getNewestFN($path)
    {
        $files = glob($path . '*.jpg');
        usort($files, [$this, '_filemtimeCompare']);
        return $files;
    }

    private function _filemtimeCompare($a, $b)
    {
        return filemtime($a) - filemtime($b);
    }


    public function massImportProfilePicture()
    {
        if ($this->request->is('post') && !empty($this->request->getData())) {
            $file = $this->request->getData('Student.xls');
            if ($file['type'] !== 'application/vnd.ms-excel' && $file['type'] !== 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                $this->Flash->error(__('Importing Error. Please save your Excel file as "Excel 97-2003 Workbook" or "Excel Workbook" type and import again. Current file format is: {0}', $file['type']));
                return;
            }

            try {
                $spreadsheet = IOFactory::load($file['tmp_name']);
                $sheet = $spreadsheet->getActiveSheet();
                $rows = $sheet->toArray();

                if (empty($rows)) {
                    $this->Flash->error(__('Importing Error. The Excel file you uploaded is empty.'));
                    return;
                }

                if (empty($rows[0])) {
                    $this->Flash->error(__('Importing Error. Please insert your field names (studentnumber, photonumber) at the first row of your Excel file.'));
                    return;
                }

                $requiredFields = ['studentnumber', 'photonumber'];
                $nonExistingFields = array_diff($requiredFields, $rows[0]);

                if (!empty($nonExistingFields)) {
                    $this->Flash->error(__('Importing Error. {0} is/are required in the Excel file at the first row.', implode(', ', $nonExistingFields)));
                    return;
                }

                $fieldsNameImportTable = $rows[0];
                $uploadMaps = [];
                $nonValidRows = [];

                for ($i = 1; $i < count($rows); $i++) {
                    $rowData = [];
                    foreach ($fieldsNameImportTable as $j => $fieldName) {
                        if ($fieldName === 'studentnumber' && empty(trim($rows[$i][$j] ?? ''))) {
                            $nonValidRows[] = "Please enter a valid student number on row number " . ($i + 1);
                            continue 2;
                        }
                        if ($fieldName === 'studentnumber') {
                            $rowData['studentnumber'] = trim($rows[$i][$j] ?? '');
                        }
                        if ($fieldName === 'photonumber') {
                            $rowData['photonumber'] = trim($rows[$i][$j] ?? '');
                        }
                    }
                    $uploadMaps[$rowData['studentnumber']] = $rowData['photonumber'];
                }

                $invalidStudentIds = [];
                $validStudentIds = [];
                $studentsTable = TableRegistry::getTableLocator()->get('Students');
                $photosTable = TableRegistry::getTableLocator()->get('Photos');

                if (!empty($uploadMaps)) {
                    $rowCount = 1;
                    foreach ($uploadMaps as $studentNumber => $photoNumber) {
                        $student = $studentsTable->find()
                            ->where(['Students.studentnumber' => $studentNumber])
                            ->first();

                        if ($student) {
                            foreach ($this->request->getData('Student.File', []) as $fv) {
                                if (stripos($fv['name'], $photoNumber) !== false) {
                                    $ext = strtolower(pathinfo($fv['name'], PATHINFO_EXTENSION));
                                    $filenameNew = str_replace('/', '-', $studentNumber) . '.' . $ext;
                                    $allowedExts = ['jpg', 'jpeg', 'png'];

                                    if (in_array($ext, $allowedExts)) {
                                        if (move_uploaded_file($fv['tmp_name'], WWW_ROOT . 'media/transfer/img/' . $filenameNew)) {
                                            $attachment = $photosTable->find()
                                                ->where([
                                                    'Photos.model' => 'Student',
                                                    'Photos.foreign_key' => $student->id
                                                ])
                                                ->first();

                                            $attachmentModel = [
                                                'model' => 'Student',
                                                'foreign_key' => $student->id,
                                                'dirname' => 'img',
                                                'basename' => $filenameNew,
                                                'checksum' => md5($filenameNew),
                                                'group' => 'profile'
                                            ];

                                            if ($attachment) {
                                                $attachmentModel['id'] = $attachment->id;
                                            }

                                            $photoEntity = $attachment ? $photosTable->patchEntity($attachment, $attachmentModel) : $photosTable->newEntity($attachmentModel);

                                            if ($photosTable->save($photoEntity)) {
                                                $validStudentIds[$studentNumber] = $rowCount;
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            $invalidStudentIds[$studentNumber] = $rowCount;
                        }

                        $rowCount++;
                    }
                }

                if (!empty($validStudentIds)) {
                    $this->Flash->success(__('Uploaded {0} profile pictures.', count($validStudentIds)));
                }
            } catch (\Exception $e) {
                $this->Flash->error(__('Importing Error. Failed to read the Excel file: {0}', $e->getMessage()));
            }
        }

        $photosTable = TableRegistry::getTableLocator()->get('Photos');
        $profilePictureUploaded = $photosTable->find()
            ->where([
                'Photos.group' => 'profile',
                'Photos.model' => 'Student',
                'Photos.foreign_key IN' => $studentsTable->find()->select(['id'])
            ])
            ->count();

        $totalStudentCount = $studentsTable->find()
            ->where(['Students.graduated' => 0])
            ->count();

        $this->set(compact('profilePictureUploaded', 'totalStudentCount'));
    }

    public function massImportStudentNationalId()
    {
        if ($this->request->is('post') && !empty($this->request->getData())) {
            $file = $this->request->getData('Student.xls');
            if ($file['type'] !== 'application/vnd.ms-excel' && $file['type'] !== 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                $this->Flash->error(__('Importing Error. Please save your Excel file as "Excel 97-2003 Workbook" or "Excel Workbook" type and import again. Current file format is: {0}', $file['type']));
                return;
            }

            try {
                $spreadsheet = IOFactory::load($file['tmp_name']);
                $sheet = $spreadsheet->getActiveSheet();
                $rows = $sheet->toArray();

                if (empty($rows)) {
                    $this->Flash->error(__('Importing Error. The Excel file you uploaded is empty.'));
                    return;
                }

                if (empty($rows[0])) {
                    $this->Flash->error(__('Importing Error. Please insert your field names (studentnumber, student_national_id) at the first row of your Excel file.'));
                    return;
                }

                $requiredFields = ['studentnumber', 'student_national_id'];
                $nonExistingFields = array_diff($requiredFields, $rows[0]);

                if (!empty($nonExistingFields)) {
                    $this->Flash->error(__('Importing Error. {0} is/are required in the Excel file at the first row.', implode(', ', $nonExistingFields)));
                    return;
                }

                $fieldsNameImportTable = $rows[0];
                $uploadMaps = [];
                $nonValidRows = [];

                for ($i = 1; $i < count($rows); $i++) {
                    $rowData = [];
                    foreach ($fieldsNameImportTable as $j => $fieldName) {
                        if ($fieldName === 'studentnumber' && empty(trim($rows[$i][$j] ?? ''))) {
                            $nonValidRows[] = "Please enter a valid student number on row number " . ($i + 1);
                            continue 2;
                        }
                        if ($fieldName === 'student_national_id' && empty(trim($rows[$i][$j] ?? ''))) {
                            $nonValidRows[] = "Please enter a valid Student National ID at row number " . ($i + 1);
                            continue 2;
                        }
                        if ($fieldName === 'studentnumber') {
                            $rowData['studentnumber'] = trim($rows[$i][$j] ?? '');
                        }
                        if ($fieldName === 'student_national_id') {
                            $rowData['student_national_id'] = trim($rows[$i][$j] ?? '');
                        }
                    }
                    $uploadMaps[$rowData['studentnumber']] = $rowData['student_national_id'];
                }

                $invalidStudentIds = [];
                $errorsToCorrect = [];
                $resultsToHtmlTable = [];
                $validStudentIds = [];
                $studentsTable = TableRegistry::getTableLocator()->get('Students');

                if (!empty($uploadMaps)) {
                    $rowCount = 1;
                    foreach ($uploadMaps as $studentNumber => $nationalId) {
                        $student = $studentsTable->find()
                            ->select(['id', 'full_name', 'accepted_student_id', 'user_id', 'graduated', 'studentnumber', 'student_national_id'])
                            ->where(['Students.studentnumber' => $studentNumber])
                            ->first();

                        $resultsToHtmlTable[$studentNumber] = [
                            'studentnumber' => $studentNumber,
                            'student_national_id' => $nationalId
                        ];

                        if ($student) {
                            $nationalIdExists = $studentsTable->find()
                                ->select(['id', 'full_name', 'accepted_student_id', 'user_id', 'graduated', 'studentnumber', 'student_national_id'])
                                ->where(['Students.student_national_id' => $nationalId])
                                ->first();

                            if (empty($student->student_national_id) && strlen($nationalId) > 7 && !$nationalIdExists) {
                                $student->student_national_id = $nationalId;
                                if ($studentsTable->save($student)) {
                                    $validStudentIds[$studentNumber] = $rowCount;
                                    $resultsToHtmlTable[$studentNumber]['status'] = 'Updated';
                                } else {
                                    $resultsToHtmlTable[$studentNumber]['status'] = 'Database Error: unable to save National ID. Please try again.';
                                }
                            } elseif ($student->student_national_id == $nationalId) {
                                $resultsToHtmlTable[$studentNumber]['status'] = 'Skipped: Existing Student ID to National ID Combination';
                            } elseif (!empty($student->student_national_id) && $student->student_national_id != $nationalId && $nationalIdExists) {
                                $resultsToHtmlTable[$studentNumber]['status'] = sprintf(
                                    'Error: National ID: %s is previously assigned to other student: %s (%s). Please change it to a different National ID.',
                                    $nationalId,
                                    $nationalIdExists->full_name,
                                    $nationalIdExists->studentnumber
                                );
                            } elseif (!empty($student->student_national_id) && $student->student_national_id != $nationalId) {
                                $resultsToHtmlTable[$studentNumber]['status'] = sprintf(
                                    'Skipped: %s (%s) has existing National ID: %s which is different from the one you are trying to update: %s',
                                    $student->full_name,
                                    $student->studentnumber,
                                    $student->student_national_id,
                                    $nationalId
                                );
                            } elseif ($nationalIdExists) {
                                $resultsToHtmlTable[$studentNumber]['status'] = sprintf(
                                    'Error: National ID: %s is previously assigned to other student: %s (%s). Please change it to a different National ID.',
                                    $nationalId,
                                    $nationalIdExists->full_name,
                                    $nationalIdExists->studentnumber
                                );
                            } elseif (strlen($nationalId) < 8) {
                                $resultsToHtmlTable[$studentNumber]['status'] = 'Error: National ID Length cannot be less than 8 characters.';
                            } else {
                                $resultsToHtmlTable[$studentNumber]['status'] = 'Unknown Error: Validation Error/End';
                            }
                        } else {
                            $invalidStudentIds[$studentNumber] = $rowCount;
                            $resultsToHtmlTable[$studentNumber]['status'] = sprintf('Error: Student ID: "%s" is not found in the system, please check for spelling errors.', $studentNumber);
                        }

                        $rowCount++;
                    }
                }

                if (!empty($validStudentIds)) {
                    $this->Flash->success(__('Updated {0} Student National IDs.', count($validStudentIds)));
                } else {
                    $this->Flash->info(__('Nothing to update. Either all of {0} Students National IDs in your Excel file already exist in the system or you have errors in your uploaded Excel file.', count($resultsToHtmlTable)));
                }

                $this->set(compact('invalidStudentIds', 'errorsToCorrect', 'resultsToHtmlTable'));
            } catch (\Exception $e) {
                $this->Flash->error(__('Importing Error. Failed to read the Excel file: {0}', $e->getMessage()));
            }
        }

        $currentAcademicYear = $this->AcademicYear->current_academicyear();
        $acYearsToLook = $this->AcademicYear->academicYearInArray(
            explode('/', $currentAcademicYear)[0] - ACY_BACK_FOR_STUDENT_NATIONAL_ID_CHECK,
            explode('/', $currentAcademicYear)[0]
        );
        $admissionsYearsToLook = $this->AcademicYear->academicYearInArray(
            explode('/', $currentAcademicYear)[0] - ACY_BACK_FOR_ALL,
            explode('/', $currentAcademicYear)[0]
        );

        $acYearsToLookImploded = "'" . implode("', '", $acYearsToLook) . "'";

        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $nonGraduatedStudentCount = $studentsTable->StudentExamStatuses->find()
            ->where([
                'StudentExamStatuses.academic_year IN' => $acYearsToLook,
                'StudentExamStatuses.student_id IN' => $studentsTable->CourseRegistrations->find()
                    ->select(['student_id'])
                    ->where(['academic_year IN' => $acYearsToLook])
                    ->group(['student_id'])
            ])
            ->contain([
                'Students' => [
                    'conditions' => [
                        'Students.graduated' => 0,
                        'Students.program_id !=' => PROGRAM_REMEDIAL,
                        'OR' => [
                            'Students.student_national_id IS NOT NULL',
                            'Students.student_national_id != 0',
                            'Students.student_national_id !=' => ''
                        ]
                    ]
                ]
            ])
            ->group(['StudentExamStatuses.student_id'])
            ->count();

        $totalStudentCount = $studentsTable->CourseRegistrations->find()
            ->where(['CourseRegistrations.academic_year IN' => $acYearsToLook])
            ->contain([
                'Students' => [
                    'conditions' => [
                        'Students.graduated' => 0,
                        'Students.program_id !=' => PROGRAM_REMEDIAL,
                        'Students.academicyear IN' => $admissionsYearsToLook
                    ]
                ]
            ])
            ->group(['CourseRegistrations.student_id'])
            ->count();

        $this->set(compact('nonGraduatedStudentCount', 'totalStudentCount'));
    }

    public function massImportOneTimePasswords()
    {
        if ($this->request->is('post') && !empty($this->request->getData())) {
            $file = $this->request->getData('Student.xls');
            if ($file['type'] !== 'application/vnd.ms-excel' && $file['type'] !== 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                $this->Flash->error(__('Importing Error. Please save your Excel file as "Excel 97-2003 Workbook" or "Excel Workbook" type and import again. Current file format is: {0}', $file['type']));
                return;
            }

            try {
                $spreadsheet = IOFactory::load($file['tmp_name']);
                $sheet = $spreadsheet->getActiveSheet();
                $rows = $sheet->toArray();

                $serviceType = $this->request->getData('Student.service');
                $requiredFields = ['studentnumber', 'username', 'password'];
                $showPortal = 0;
                $showExamCenter = 0;

                if ($serviceType == 'Elearning') {
                    $requiredFields[] = 'portal';
                    $showPortal = 1;
                } elseif ($serviceType == 'ExitExam') {
                    $requiredFields = array_merge($requiredFields, ['portal', 'exam_center']);
                    $showPortal = 1;
                    $showExamCenter = 1;
                } elseif ($serviceType != 'Office365') {
                    $this->Flash->error(__('Importing Error. Invalid service type selected.'));
                    return;
                }

                if (empty($rows)) {
                    $this->Flash->error(__('Importing Error. The Excel file you uploaded is empty.'));
                    return;
                }

                if (empty($rows[0])) {
                    $this->Flash->error(__('Importing Error. Please insert your field names ({0}) at the first row of your Excel file.', implode(', ', $requiredFields)));
                    return;
                }

                $nonExistingFields = array_diff($requiredFields, $rows[0]);
                if (!empty($nonExistingFields)) {
                    $this->Flash->error(__('Importing Error. {0} is/are required in the Excel file at the first row.', implode(', ', $nonExistingFields)));
                    return;
                }

                $fieldsNameImportTable = $rows[0];
                $uploadMaps = [];
                $nonValidRows = [];

                for ($i = 1; $i < count($rows); $i++) {
                    $rowData = [];
                    foreach ($fieldsNameImportTable as $j => $fieldName) {
                        if (in_array($fieldName, ['studentnumber', 'username', 'password', 'portal', 'exam_center']) && empty(trim($rows[$i][$j] ?? ''))) {
                            $nonValidRows[] = sprintf('Please enter a valid %s at row number %d', $fieldName, $i + 1);
                            continue 2;
                        }
                        $rowData[$fieldName] = trim($rows[$i][$j] ?? '');
                    }

                    if (isset($rowData['studentnumber']) && !empty($rowData['studentnumber']) && !isset($uploadMaps[$rowData['studentnumber']])) {
                        $uploadMaps[$rowData['studentnumber']] = [
                            'studentnumber' => $rowData['studentnumber'],
                            'username' => $rowData['username'] ?? '',
                            'password' => $rowData['password'] ?? '',
                            'portal' => ($serviceType == 'Elearning' || $serviceType == 'ExitExam') ? ($rowData['portal'] ?? null) : null,
                            'exam_center' => $serviceType == 'ExitExam' ? ($rowData['exam_center'] ?? null) : null
                        ];
                    } elseif (isset($rowData['studentnumber']) && !empty($rowData['studentnumber'])) {
                        $nonValidRows[] = sprintf('Duplicate Student ID at %s row number %d', $rowData['studentnumber'], $i + 1);
                    }
                }

                $invalidStudentIds = [];
                $errorsToCorrect = [];
                $resultsToHtmlTable = [];
                $validStudentIds = [];
                $savedRecords = 0;
                $updatedRecords = 0;
                $errorInSavingRecords = 0;
                $studentsTable = TableRegistry::getTableLocator()->get('Students');
                $otpsTable = $studentsTable->Otps;

                if (!empty($uploadMaps)) {
                    $rowCount = 1;
                    foreach ($uploadMaps as $studentNumber => $vv) {
                        $student = $studentsTable->find()
                            ->select(['id', 'full_name', 'accepted_student_id', 'user_id', 'graduated', 'studentnumber', 'student_national_id'])
                            ->where(['Students.studentnumber' => $studentNumber])
                            ->first();

                        $resultsToHtmlTable[$studentNumber] = [
                            'studentnumber' => $studentNumber,
                            'username' => $vv['username'],
                            'password' => $vv['password'],
                            'portal' => $vv['portal'] ?? '',
                            'exam_center' => $vv['exam_center'] ?? ''
                        ];

                        if ($student && !empty($vv['username']) && !empty($vv['password'])) {
                            $otpExists = $otpsTable->find()
                                ->where([
                                    'Otps.studentnumber' => $studentNumber,
                                    'Otps.username' => $vv['username'],
                                    'Otps.service' => $serviceType
                                ])
                                ->first();

                            if ($otpExists) {
                                if ($otpExists->password == $vv['password']) {
                                    $resultsToHtmlTable[$studentNumber]['status'] = sprintf(
                                        'Skipped: There is an existing account for %s (%s) with the same password for %s.',
                                        $student->full_name,
                                        $student->studentnumber,
                                        $serviceType
                                    );
                                } else {
                                    $otpExists->password = $vv['password'];
                                    $otpExists->modified = date('Y-m-d H:i:s');
                                    if ($otpsTable->save($otpExists)) {
                                        $resultsToHtmlTable[$studentNumber]['status'] = sprintf('Updated new password for %s', $serviceType);
                                        $updatedRecords++;
                                    } else {
                                        $resultsToHtmlTable[$studentNumber]['status'] = sprintf(
                                            'Database Error: unable to save new password for %s (%s) for %s.',
                                            $student->full_name,
                                            $student->studentnumber,
                                            $serviceType
                                        );
                                        $errorInSavingRecords++;
                                    }

                                    if (!empty($vv['exam_center']) && $otpExists->exam_center != $vv['exam_center']) {
                                        $otpExists->exam_center = $vv['exam_center'];
                                        $otpExists->modified = date('Y-m-d H:i:s');
                                        if ($otpsTable->save($otpExists)) {
                                            $resultsToHtmlTable[$studentNumber]['status'] = sprintf('Updated Exam Center for %s', $serviceType);
                                            $updatedRecords++;
                                        } else {
                                            $resultsToHtmlTable[$studentNumber]['status'] = sprintf(
                                                'Database Error: unable to save exam center for %s (%s) for %s.',
                                                $student->full_name,
                                                $student->studentnumber,
                                                $serviceType
                                            );
                                            $errorInSavingRecords++;
                                        }
                                    }
                                }
                            } else {
                                if (strlen($vv['username']) < 4) {
                                    $resultsToHtmlTable[$studentNumber]['status'] = sprintf(
                                        'Username Error: Username for %s (%s) is not valid.',
                                        $student->full_name,
                                        $student->studentnumber
                                    );
                                } elseif (strlen($vv['password']) < 8) {
                                    $resultsToHtmlTable[$studentNumber]['status'] = sprintf(
                                        'Password Error: Password for %s (%s) is too short.',
                                        $student->full_name,
                                        $student->studentnumber
                                    );
                                } elseif (empty($vv['portal']) && ($serviceType == 'Elearning' || $serviceType == 'ExitExam')) {
                                    $resultsToHtmlTable[$studentNumber]['status'] = sprintf(
                                        'Portal Error: You need to specify %s portal to use for %s (%s).',
                                        $serviceType == 'Elearning' ? 'E-Learning' : 'Exit Exam',
                                        $student->full_name,
                                        $student->studentnumber
                                    );
                                } elseif (empty($vv['exam_center']) && $serviceType == 'ExitExam') {
                                    $resultsToHtmlTable[$studentNumber]['status'] = sprintf(
                                        'Exam Center Error: You need to specify Exam Center for %s (%s).',
                                        $student->full_name,
                                        $student->studentnumber
                                    );
                                } else {
                                    $newOtpEntry = [
                                        'student_id' => $student->id,
                                        'studentnumber' => $studentNumber,
                                        'username' => $vv['username'],
                                        'password' => $vv['password'],
                                        'service' => $serviceType,
                                        'portal' => $vv['portal'],
                                        'exam_center' => $vv['exam_center'],
                                        'active' => 1,
                                        'created' => date('Y-m-d H:i:s'),
                                        'modified' => date('Y-m-d H:i:s')
                                    ];

                                    $otpEntity = $otpsTable->newEntity($newOtpEntry);
                                    if ($otpsTable->save($otpEntity, ['validate' => 'first'])) {
                                        $validStudentIds[$studentNumber] = $rowCount;
                                        $resultsToHtmlTable[$studentNumber]['status'] = sprintf('Added %s OTP', $serviceType);
                                        $savedRecords++;
                                    } else {
                                        $resultsToHtmlTable[$studentNumber]['status'] = sprintf(
                                            'Database Error: unable to save new OTP for %s (%s) for %s. Please try again.',
                                            $student->full_name,
                                            $student->studentnumber,
                                            $serviceType
                                        );
                                        $errorInSavingRecords++;
                                    }
                                }
                            }
                        } else {
                            $invalidStudentIds[$studentNumber] = $rowCount;
                            $resultsToHtmlTable[$studentNumber]['status'] = sprintf(
                                'Error: %s: "%s" is not valid or not found in the system, please check for spelling errors.',
                                empty($vv['username']) ? 'Username' : (empty($vv['password']) ? 'Password' : 'Student ID'),
                                empty($vv['username']) ? 'Username' : (empty($vv['password']) ? 'Password' : $studentNumber)
                            );
                        }

                        $rowCount++;
                    }
                }

                if (!empty($validStudentIds) && $errorInSavingRecords == 0) {
                    $this->Flash->success(__('Imported %d %s OTP Passwords%s.', $savedRecords, $serviceType, $updatedRecords > 0 ? " and updated $updatedRecords" : ''));
                } elseif ($savedRecords > 0 || $updatedRecords > 0) {
                    $this->Flash->success(__(
                        '%s %s OTP Passwords%s.',
                        ($savedRecords > 0 ? "Imported $savedRecords" : '') . ($updatedRecords > 0 ? ($savedRecords > 0 ? ' and updated ' : 'Updated ') . $updatedRecords : ''),
                        $serviceType,
                        $errorInSavingRecords > 0 ? " with failed $errorInSavingRecords updates" : ''
                    ));
                } else {
                    $this->Flash->info(__('Nothing to update. Either all of %d students %s OTP passwords already exist in the system or you have errors in your uploaded Excel file.', count($resultsToHtmlTable), $serviceType));
                }

                $this->set(compact('invalidStudentIds', 'errorsToCorrect', 'resultsToHtmlTable', 'showPortal', 'showExamCenter'));
            } catch (\Exception $e) {
                $this->Flash->error(__('Importing Error. Failed to read the Excel file: %s', $e->getMessage()));
            }
        }
    }

    public function activateDeactivateProfile($parameters)
    {


        if (!empty($parameters)) {
            $studentsTable = TableRegistry::getTableLocator()->get('Students');
            $student = $studentsTable->find()
                ->where(['Students.id' => $parameters])
                ->contain(['Users'])
                ->first();

            if ($student && !empty($student->user->id)) {
                $usersTable = TableRegistry::getTableLocator()->get('Users');
                $user = $usersTable->get($student->user->id);
                $user->active = !$user->active;
                if ($usersTable->save($user)) {
                    $this->Flash->success(__($user->active ? 'The student profile has been activated.' : 'The student profile has been deactivated.'));
                } else {
                    $this->Flash->error(__('Failed to update the student profile status. Please try again.'));
                }
            } else {
                $this->Flash->warning(__('Username/password is not issued to the student until now and no account is found associated with the student. Thus, there is no need to activate/deactivate the account.'));
            }

            return $this->redirect(['action' => 'student_academic_profile', $student->id]);
        }
    }

    public function idCardPrint()
    {
        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $acceptedStudentsTable = TableRegistry::getTableLocator()->get('AcceptedStudents');

        if ($this->request->is('post') && $this->request->getData('getacceptedstudent')) {
            $options = [];
            $limit = $this->request->getData('Search.limit', 100);

            if (!empty($this->request->getData('Search.academicyear'))) {
                $options['conditions']['AcceptedStudents.academic_year'] = $this->request->getData('Search.academic_year');
            }

            if (!empty($this->request->getData('Search.department_id'))) {
                $collegeId = explode('~', $this->request->getData('Search.department_id'));
                if (count($collegeId) > 1) {
                    $options['conditions']['AcceptedStudents.college_id'] = $collegeId[1];
                } else {
                    $options['conditions']['AcceptedStudents.department_id'] = $collegeId[0];
                }
            }

            if (!empty($this->request->getData('Search.name'))) {
                $options['conditions']['AcceptedStudents.first_name LIKE'] = $this->request->getData('Search.name') . '%';
            }

            if (!empty($this->request->getData('Search.program_type_id'))) {
                $options['conditions']['AcceptedStudents.program_type_id'] = $this->request->getData('Search.program_type_id');
            }

            if (!empty($this->request->getData('Search.program_id'))) {
                $options['conditions']['AcceptedStudents.program_id'] = $this->request->getData('Search.program_id');
            }

            if (!empty($options)) {
                $this->paginate = [
                    'limit' => $limit,
                    'maxLimit' => $limit,
                    'conditions' => $options['conditions']
                ];

                $acceptedStudents = $this->paginate($acceptedStudentsTable);
                if (empty($acceptedStudents)) {
                    $this->Flash->info(__('No result found with the given criteria.'));
                }

                $this->set(compact('acceptedStudents'));
            }
        }

        if ($this->request->is('post') && $this->request->getData('printIDCard')) {
            $studentsList = [];

            if (!empty($this->request->getData('AcceptedStudent.approve'))) {
                foreach ($this->request->getData('AcceptedStudent.approve') as $key => $value) {
                    if ($value == 1) {
                        $universitiesTable = TableRegistry::getTableLocator()->get('Universities');
                        $university = $universitiesTable->getAcceptedStudentUniversity($key);

                        $studentData = $acceptedStudentsTable->find()
                            ->where(['AcceptedStudents.id' => $key])
                            ->contain([
                                'Students' => ['Attachments'],
                                'Colleges',
                                'Departments',
                                'Programs',
                                'ProgramTypes'
                            ])
                            ->first();

                        $studentsList[$key] = array_merge($studentData->toArray(), ['University' => $university]);
                    }
                }
            }

            if (empty($studentsList)) {
                $this->Flash->info(__('No student is found with the given criteria to print ID card.'));
            } else {
                $this->set(compact('studentsList'));
                $this->response = $this->response->withType('application/pdf');
                $this->viewBuilder()->setLayout('pdf/default');
                $this->render('id_card_print_pdf');
                return;
            }
        }

        $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
        if ($this->role_id == ROLE_SYSADMIN) {
            $departmentIds = $departmentsTable->find('list', ['keyField' => 'id', 'valueField' => 'id'])->toArray();
            $departments = $departmentsTable->allDepartmentsByCollege2(1, $departmentIds, $this->college_ids, 1);
        } elseif (!empty($this->department_ids) || !empty($this->college_ids)) {
            $departments = $departmentsTable->allDepartmentsByCollege2(1, $this->department_ids, $this->college_ids, 1);
        } elseif (!empty($this->department_id)) {
            $departments = $departmentsTable->allDepartmentsByCollege2(1, $this->department_id, $this->college_id, 1);
        } else {
            $departments = [];
        }

        $this->set(compact('departments'));
    }

    public function cardPrintingReport()
    {
        if ($this->request->is('post') && ($this->request->getData('getReport') || $this->request->getData('getReportExcel'))) {
            $studentsTable = TableRegistry::getTableLocator()->get('Students');
            $reportType = $this->request->getData('Student.report_type');

            if (in_array($reportType, ['IDPrintingCount', 'NOTPrinttedIDCount'])) {
                if ($reportType == 'NOTPrinttedIDCount') {
                    $this->request->getData('Student.printed_count', 0);
                    $headerLabel = $this->_label(
                        'Not Printed ID Card Printing Statistics for',
                        $this->request->getData('Student.academic_year'),
                        $this->request->getData('Student.program_type_id'),
                        $this->request->getData('Student.program_id'),
                        $this->request->getData('Student.department_id'),
                        $this->request->getData('Student.gender')
                    );
                } else {
                    $headerLabel = $this->_label(
                        'ID Card Printing Statistics for',
                        $this->request->getData('Student.academic_year'),
                        $this->request->getData('Student.program_type_id'),
                        $this->request->getData('Student.program_id'),
                        $this->request->getData('Student.department_id'),
                        $this->request->getData('Student.gender')
                    );
                }

                $distributionIDPrintingCount = $studentsTable->getIDPrintCount($this->request->getData('Student'));
                $years = $this->_years($this->request->getData('Student.department_id'));

                $this->set(compact('distributionIDPrintingCount', 'years', 'headerLabel'));

                if ($reportType == 'IDPrintingCount' && $this->request->getData('getReportExcel')) {
                    $this->viewBuilder()->setLayout(false);
                    $filename = 'ID Card Printing Statistics -' . date('Ymd H:i:s');
                    $this->set(compact('distributionIDPrintingCount', 'years', 'headerLabel', 'filename'));
                    $this->render('/Elements/reports/xls/id_printing_stats_xls');
                    return;
                }
            } elseif ($reportType == 'IDNotIssuedStudentList') {
                $this->request->getData('Student.printed_count', 0);
                $headerLabel = $this->_label(
                    'ID Card Not Issued List',
                    $this->request->getData('Student.academic_year'),
                    $this->request->getData('Student.program_type_id'),
                    $this->request->getData('Student.program_id'),
                    $this->request->getData('Student.department_id'),
                    $this->request->getData('Student.gender')
                );

                $idNotPrintedStudentList = $studentsTable->getIDPrintCount($this->request->getData('Student'), 'list');
                $years = $this->_years($this->request->getData('Student.department_id'));

                $this->set(compact('idNotPrintedStudentList', 'years', 'headerLabel'));

                if ($this->request->getData('getReportExcel')) {
                    $this->viewBuilder()->setLayout(false);
                    $filename = 'ID Card Not Issued List -' . date('Ymd H:i:s');
                    $this->set(compact('idNotPrintedStudentList', 'years', 'headerLabel', 'filename'));
                    $this->render('/Elements/reports/xls/id_not_issued_student_list_xls');
                    return;
                }
            }
        }

        $reportTypeOptions = [
            'Statistics' => [
                'IDPrintingCount' => 'ID Print Count',
                'NOTPrinttedIDCount' => 'Not Printed ID Count'
            ],
            'List' => [
                'IDNotIssuedStudentList' => 'ID Card Not Issued Student List'
            ]
        ];

        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $programs = $studentsTable->Programs->find('list')->toArray();
        $programTypes = $studentsTable->ProgramTypes->find('list')->toArray();
        $departmentsTable = TableRegistry::getTableLocator()->get('Departments');

        if ($this->role_id == ROLE_SYSADMIN) {
            $departmentIds = $departmentsTable->find('list', ['keyField' => 'id', 'valueField' => 'id'])->toArray();
            $departments = $departmentsTable->allDepartmentsByCollege2(1, $departmentIds, $this->college_ids);
        } elseif (!empty($this->department_ids) || !empty($this->college_ids)) {
            $departments = $departmentsTable->allDepartmentsByCollege2(1, $this->department_ids, $this->college_ids);
        } elseif (!empty($this->department_id)) {
            $departments = $departmentsTable->allDepartmentsByCollege2(1, $this->department_id, $this->college_id);
        } else {
            $departments = [];
        }

        $yearLevels = $studentsTable->Sections->YearLevels->distinct_year_level();
        $programs = [0 => 'All Programs'] + $programs;
        $programTypes = [0 => 'All Program Types'] + $programTypes;
        $departments = [0 => 'All University Students'] + $departments;
        $yearLevels = [0 => 'All Year Level'] + $yearLevels;

        $defaultDepartmentId = null;
        $defaultProgramId = null;
        $defaultProgramTypeId = null;
        $defaultYearLevelId = null;
        $defaultRegionId = null;
        $graphType = ['bar' => 'Bar Chart', 'pie' => 'Pie Chart', 'line' => 'Line Chart'];

        $this->set(compact(
            'departments',
            'graphType',
            'defaultRegionId',
            'programTypes',
            'programs',
            'defaultProgramTypeId',
            'defaultProgramId',
            'defaultDepartmentId',
            'reportTypeOptions',
            'defaultYearLevelId',
            'yearLevels'
        ));
    }

    private function _years($collegeIds)
    {
        $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
        $yearLevelsTable = TableRegistry::getTableLocator()->get('YearLevels');

        $collegeId = explode('~', $collegeIds);
        if (count($collegeId) > 1) {
            $years = $yearLevelsTable->find('list', [
                'keyField' => 'name',
                'valueField' => 'name'
            ])
                ->where([
                    'YearLevels.department_id IN' => $departmentsTable->find()
                        ->select(['id'])
                        ->where(['Departments.college_id' => $collegeId[1]])
                ])
                ->toArray();
        } elseif (!empty($collegeIds)) {
            $years = $yearLevelsTable->find('list', [
                'keyField' => 'name',
                'valueField' => 'name'
            ])
                ->where(['YearLevels.department_id' => $collegeIds])
                ->toArray();
        } else {
            $years = $yearLevelsTable->find('list', [
                'keyField' => 'name',
                'valueField' => 'name'
            ])
                ->toArray();
        }

        return $years;
    }

    private function _label($prefix, $academicYear, $programTypeId, $programId, $departmentId, $gender)
    {
        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $programs = $studentsTable->Programs->find('list')->toArray();
        $programTypes = $studentsTable->ProgramTypes->find('list')->toArray();

        $label = sprintf('%s %s of ', $prefix, $academicYear);
        $label .= $programTypeId == 0 ? 'all program types ' : $programTypes[$programTypeId];
        $label .= $programId == 0 ? 'undergraduate/graduate ' : 'in ' . $programs[$programId];

        $name = '';
        $collegeId = explode('~', $departmentId);
        if (count($collegeId) > 1) {
            $college = $studentsTable->Colleges->find()
                ->where(['Colleges.id' => $collegeId[1]])
                ->first();
            $name .= ' ' . $college->name;
        } elseif (!empty($departmentId)) {
            $department = $studentsTable->Departments->find()
                ->where(['Departments.id' => $departmentId])
                ->first();
            $name .= ' ' . $department->name;
        } elseif ($departmentId == 0) {
            $name .= 'for all departments';
        }

        $label .= $name;
        return $label;
    }

    public function printRecord()
    {
        if ($this->Session->check('students')) {
            $displayFieldStudent = ['Display' => $this->Session->read('display_field_student')];
            $students = $this->Session->read('students');

            if (!empty($students)) {
                $universitiesTable = TableRegistry::getTableLocator()->get('Universities');
                $university = $universitiesTable->getStudentUniversity($students[0]['Student']['id']);
                $collegesTable = TableRegistry::getTableLocator()->get('Colleges');
                $colleges = $collegesTable->find()
                    ->where(['Colleges.id' => $students[0]['Student']['college_id']])
                    ->first();
                $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
                $departments = $departmentsTable->find()
                    ->where(['Departments.id' => $students[0]['Student']['department_id']])
                    ->first();

                $this->set(compact('students', 'displayFieldStudent', 'university', 'departments', 'colleges'));
                $this->response = $this->response->withType('application/pdf');
                $this->viewBuilder()->setLayout('pdf/default');
                $this->render('print_students_list_pdf');
                return;
            }
        }

        $this->Flash->error(__('Couldn\'t read students data, please refresh your page.'));
        return $this->redirect(['action' => 'index']);
    }


    public function ajaxCheckEcardnumber()
    {
        $this->viewBuilder()->setLayout('ajax');

        $value = 'Invalid';
        $studentsTable = TableRegistry::getTableLocator()->get('Students');

        if ($this->request->is('post') && !empty($this->request->getData('Student.ecardnumber'))) {
            $exists = $studentsTable->find()
                ->where(['Students.ecardnumber' => $this->request->getData('Student.ecardnumber')])
                ->first();

            if (!$exists) {
                $value = 'Valid';
            }
        }

        $this->set(compact('value'));
    }

    public function pushStudentsCafeEntry()
    {
        $studentsTable = TableRegistry::getTableLocator()->get('Students');

        if ($this->request->is('post') && $this->request->getData('getStudent')) {
            $options = [];
            $limit = $this->request->getData('Search.limit', 100);

            if (!empty($this->request->getData('Search.academic_year'))) {
                $options['conditions']['Students.admissionyear'] = $this->AcademicYear->get_academicYearBegainingDate($this->request->getData('Search.academic_year'));
            }

            if (!empty($this->request->getData('Search.currentAcademicYear')) && !empty($this->request->getData('Search.semester'))) {
                $cafe = $this->request->getData('Search.cafe', 0);
                $options['conditions'][] = [
                    'Students.id IN' => $studentsTable->CourseRegistrations->find()
                        ->select(['student_id'])
                        ->where([
                            'CourseRegistrations.academic_year' => $this->request->getData('Search.currentAcademicYear'),
                            'CourseRegistrations.semester' => $this->request->getData('Search.semester'),
                            'CourseRegistrations.cafeteria_consumer' => $cafe
                        ]),
                    'Students.ecardnumber IS NOT NULL'
                ];
            }

            if (!empty($this->request->getData('Search.department_id'))) {
                $collegeId = explode('~', $this->request->getData('Search.department_id'));
                if (count($collegeId) > 1) {
                    $options['conditions']['Students.college_id'] = $collegeId[1];
                } else {
                    $options['conditions']['Students.department_id'] = $collegeId[0];
                }
            }

            if (!empty($this->request->getData('Search.name'))) {
                $options['conditions']['Students.first_name LIKE'] = $this->request->getData('Search.name') . '%';
            }

            if (!empty($this->request->getData('Search.program_type_id'))) {
                $options['conditions']['Students.program_type_id'] = $this->request->getData('Search.program_type_id');
            }

            if (!empty($this->request->getData('Search.program_id'))) {
                $options['conditions']['Students.program_id'] = $this->request->getData('Search.program_id');
            }

            if (!empty($options)) {
                $this->paginate = [
                    'limit' => $limit,
                    'maxLimit' => $limit,
                    'conditions' => $options['conditions']
                ];

                $students = $this->paginate($studentsTable);
                if (empty($students)) {
                    $this->Flash->error(__('No result found.'));
                }
                $this->set(compact('students'));
            }
        }

        if ($this->request->is('post') && $this->request->getData('pushStudentsToCafeGate')) {
            $studentsList = [];
            $connection = ConnectionManager::get('mssql'); // Assumes MSSQL datasource configured in config/app.php

            foreach ($this->request->getData('Student.approve', []) as $key => $value) {
                if ($value == 1) {
                    $studentsList[] = $key;
                    $studentInfo = $studentsTable->find()
                        ->where(['Students.id' => $key])
                        ->contain(['Colleges'])
                        ->first();

                    $mealHallAssigned = $studentsTable->MealHallAssignments->find()
                        ->where([
                            'MealHallAssignments.student_id' => $key,
                            'MealHallAssignments.academic_year' => $this->request->getData('Search.currentAcademicYear')
                        ])
                        ->first();

                    $stmt = $connection->execute("SELECT TOP 1 SLN_Employee FROM dbo.MSTR_Employee WHERE Employee_Code = :code", [
                        'code' => $studentInfo->studentnumber
                    ]);
                    $studentResult = $stmt->fetch(PDO::FETCH_ASSOC);

                    if (!empty($studentResult['SLN_Employee'])) {
                        $stmt = $connection->execute("SELECT TOP 1 SLN_Employee FROM ACS_Cards_Info WHERE SLN_Employee = :sln", [
                            'sln' => $studentResult['SLN_Employee']
                        ]);
                        $cardResult = $stmt->fetch(PDO::FETCH_ASSOC);

                        $accessLevel4Cafe = $this->request->getData('Search.allow') == 1
                            ? ($mealHallAssigned->meal_hall_id ?? 0)
                            : 0;

                        if (!empty($cardResult['SLN_Employee'])) {
                            $connection->execute("UPDATE ACS_Cards_Info SET Access_Level4 = :access WHERE SLN_Employee = :sln", [
                                'access' => $accessLevel4Cafe,
                                'sln' => $cardResult['SLN_Employee']
                            ]);
                        } else {
                            $slnEmployee = $studentResult['SLN_Employee'];
                            $cardNumber = $studentInfo->ecardnumber;
                            $facilityID = $studentInfo->college->campus_id;
                            $accessLevel1CommonGate = 13;
                            $accessLevel2AllStudentGate = 9;
                            $accessLevel3AllLibGate = 5;
                            $accessLevel5 = 0;
                            $accessLevel6 = 0;
                            $accessLevel7 = 0;
                            $accessLevel8 = 0;

                            $connection->execute(
                                "INSERT INTO ACS_Cards_Info (SLN_Employee, Card_Number, Facility_ID, Access_Level1, Access_Level2, Access_Level3, Access_Level4, Access_Level5, Access_Level6, Access_Level7, Access_Level8) VALUES (:sln, :card, :facility, :al1, :al2, :al3, :al4, :al5, :al6, :al7, :al8)",
                                [
                                    'sln' => $slnEmployee,
                                    'card' => $cardNumber,
                                    'facility' => $facilityID,
                                    'al1' => $accessLevel1CommonGate,
                                    'al2' => $accessLevel2AllStudentGate,
                                    'al3' => $accessLevel3AllLibGate,
                                    'al4' => $accessLevel4Cafe,
                                    'al5' => $accessLevel5,
                                    'al6' => $accessLevel6,
                                    'al7' => $accessLevel7,
                                    'al8' => $accessLevel8
                                ]
                            );
                        }
                    }
                }
            }

            if (empty($studentsList)) {
                $this->Flash->info(__('Please select the students you would like to allow/deny cafe gate.'));
            } else {
                $this->Flash->success(__('The selected students have been allowed/denied cafe gate and the update has been propagated to devices.'));
            }
        }

        $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
        if ($this->role_id == ROLE_SYSADMIN) {
            $departmentIds = $departmentsTable->find('list', ['keyField' => 'id', 'valueField' => 'id'])->toArray();
            $departments = $departmentsTable->allDepartmentsByCollege2(1, $departmentIds, $this->college_ids);
        } elseif (!empty($this->department_ids) || !empty($this->college_ids)) {
            $departments = $departmentsTable->allDepartmentsByCollege2(1, $this->department_ids, $this->college_ids);
        } elseif (!empty($this->department_id)) {
            $departments = $departmentsTable->allDepartmentsByCollege2(1, $this->department_id, $this->college_id);
        } else {
            $departments = [];
        }

        $this->set(compact('departments'));
    }

    public function change($id = null)
    {
        $studentsTable = TableRegistry::getTableLocator()->get('Students');

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();
            $student = $studentsTable->find()
                ->where(['Students.id' => $this->student_id])
                ->contain(['Contacts'])
                ->first();

            if ($student) {
                $student->ecardnumber = $data['Student']['ecardnumber'] ?? $student->ecardnumber;
                $student->phone_mobile = $data['Student']['phone_mobile'] ?? $student->phone_mobile;

                if ($studentsTable->save($student)) {
                    $this->Flash->success(__('The ecardnumber and mobile phone number were updated successfully.'));
                    return $this->redirect('/');
                } else {
                    $this->Flash->error(__('Your data could not be saved. Please, try again.'));
                }
            }
        }

        $studentData = $studentsTable->find()
            ->where(['Students.id' => $this->student_id])
            ->contain(['Contacts', 'Attachments'])
            ->first();

        $this->request->withData($studentData ? $studentData->toArray() : []);
    }

    public function deleteStudentFromGraduateListForCorrection($studentId)
    {
        if ($studentId) {
            $studentsTable = TableRegistry::getTableLocator()->get('Students');
            $student = $studentsTable->get($studentId);
            $student->graduated = 0;

            if ($studentsTable->save($student)) {
                $graduateList = $studentsTable->GraduateLists->find()
                    ->select(['id'])
                    ->where(['GraduateLists.student_id' => $studentId])
                    ->first();

                $senateList = $studentsTable->SenateLists->find()
                    ->select(['id'])
                    ->where(['SenateLists.student_id' => $studentId])
                    ->first();

                if ($graduateList && $senateList) {
                    $studentsTable->GraduateLists->delete($graduateList);
                    $studentsTable->SenateLists->delete($senateList);
                    $this->Flash->success(__('The student is now deleted from Senate and Graduation Lists.'));
                }
            }

            return $this->redirect(['action' => 'student_academic_profile', $studentId]);
        }
    }

    public function updateKohaDb()
    {
        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $acceptedStudentsTable = TableRegistry::getTableLocator()->get('AcceptedStudents');

        if ($this->request->is('post') && $this->request->getData('updateKohaDB')) {
            $status = $studentsTable->extendKohaBorrowerExpireDate($this->request->getData('AcceptedStudent.approve', []));
            if ($status) {
                $this->Flash->success(__('You have successfully updated the book borrower database.'));
            }
        }

        if ($this->request->is('post') && $this->request->getData('getacceptedstudent')) {
            $conditions = [];
            if (!empty($this->request->getData('Search.college_id'))) {
                $conditions['AcceptedStudents.college_id'] = $this->request->getData('Search.college_id');
            }
            if (!empty($this->request->getData('Search.name'))) {
                $conditions['AcceptedStudents.first_name LIKE'] = $this->request->getData('Search.name') . '%';
            }
            if (!empty($this->request->getData('Search.academic_year'))) {
                $conditions['AcceptedStudents.academic_year LIKE'] = $this->request->getData('Search.academic_year') . '%';
            }
            if (!empty($this->request->getData('Search.program_id'))) {
                $conditions['AcceptedStudents.program_id'] = $this->request->getData('Search.program_id');
            }
            if (!empty($this->request->getData('Search.program_type_id'))) {
                $conditions['AcceptedStudents.program_type_id'] = $this->request->getData('Search.program_type_id');
            }

            if (!empty($conditions)) {
                $limit = $this->request->getData('Search.limit', 1800);
                $acceptedStudentIds = $acceptedStudentsTable->find('list', [
                    'keyField' => 'id',
                    'valueField' => 'id',
                    'conditions' => $conditions,
                    'limit' => $limit
                ])->toArray();

                $students = TableRegistry::getTableLocator()->get('StudentExamStatuses')
                    ->getMostRecentStudentStatusForKoha($acceptedStudentIds, 1);

                if (!empty($students)) {
                    $acceptedStudents = $students;
                    $this->set(compact('acceptedStudents'));
                } else {
                    $this->Flash->info(__('No data is found with your search criteria that needs update, either all students have been updated or they are not qualified for borrower extension.'));
                }
            }
        }

        $colleges = $studentsTable->Colleges->find('list')->toArray();
        $departments = $studentsTable->Departments->find('list')->toArray();
        $programs = $studentsTable->Programs->find('list')->toArray();
        $programTypes = $studentsTable->ProgramTypes->find('list')->toArray();

        $this->set(compact('programs', 'programTypes', 'colleges', 'departments'));
    }

    public function updateLmsDb()
    {
        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $connection = ConnectionManager::get('lms'); // Assumes LMS datasource configured in config/app.php

        if ($this->request->is('post') && $this->request->getData('deleteLMSDB')) {
            $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
            $departmentIds = $departmentsTable->find('list', [
                'keyField' => 'id',
                'valueField' => 'id',
                'conditions' => ['Departments.college_id' => $this->request->getData('Search.college_id')]
            ])->toArray();

            $publishedCoursesTable = TableRegistry::getTableLocator()->get('PublishedCourses');
            $publishedCourseListIds = $publishedCoursesTable->find('list', [
                'keyField' => 'id',
                'valueField' => 'id',
                'conditions' => [
                    'PublishedCourses.semester' => $this->request->getData('Search.semester'),
                    'PublishedCourses.academic_year' => $this->request->getData('Search.academic_year'),
                    'PublishedCourses.department_id IN' => $departmentIds,
                    'PublishedCourses.program_id' => $this->request->getData('Search.program_id'),
                    'PublishedCourses.program_type_id' => $this->request->getData('Search.program_type_id')
                ]
            ])->toArray();

            $count = 0;
            if (!empty($publishedCourseListIds)) {
                $connection->execute('DELETE FROM enrollment WHERE course_id IN (:ids)', ['ids' => implode(',', $publishedCourseListIds)]);
                $connection->execute('DELETE FROM courses WHERE courseid IN (:ids)', ['ids' => implode(',', $publishedCourseListIds)]);
                $count = count($publishedCourseListIds);
            }

            $this->Flash->success(__($count > 0 ? 'You have successfully deleted %d courses from LMS system.' : 'There were no courses to delete from LMS system.', $count));
        }

        if ($this->request->is('post') && $this->request->getData('updateLMSDB')) {
            $departmentIds = $studentsTable->Departments->find('list', [
                'keyField' => 'id',
                'valueField' => 'id',
                'conditions' => ['Departments.college_id' => $this->request->getData('Search.college_id')]
            ])->toArray();

            $publishedCoursesTable = TableRegistry::getTableLocator()->get('PublishedCourses');
            $publishedCourseList = $publishedCoursesTable->find()
                ->where([
                    'PublishedCourses.semester' => $this->request->getData('Search.semester'),
                    'PublishedCourses.academic_year' => $this->request->getData('Search.academic_year'),
                    'PublishedCourses.department_id IN' => $departmentIds,
                    'PublishedCourses.program_id' => $this->request->getData('Search.program_id'),
                    'PublishedCourses.program_type_id' => $this->request->getData('Search.program_type_id')
                ])
                ->contain([
                    'Courses',
                    'CourseInstructorAssignments' => [
                        'Staff' => [
                            'Users',
                            'Departments',
                            'Colleges',
                            'Cities',
                            'Countries'
                        ]
                    ]
                ])
                ->toArray();

            $count = 0;
            foreach ($publishedCourseList as $pv) {
                if (!empty($pv->id)) {
                    $count++;
                    $stmt = $connection->execute('SELECT COUNT(*) AS count, courseid FROM courses WHERE courseid = :id', ['id' => $pv->id]);
                    $courseExists = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

                    if ($courseExists == 0) {
                        $fullname = sprintf('%s %s %s', $pv->course->course_title, $pv->academic_year, $pv->semester);
                        $shortname = $pv->id;
                        $categoryId = $this->request->getData('Search.college_id');

                        $connection->execute(
                            'INSERT INTO courses (fullname, shortname, courseid, categoryid, ac_year, semester) VALUES (:fullname, :shortname, :courseid, :categoryid, :ac_year, :semester)',
                            [
                                'fullname' => $fullname,
                                'shortname' => $shortname,
                                'courseid' => $pv->id,
                                'categoryid' => $categoryId,
                                'ac_year' => $pv->academic_year,
                                'semester' => $pv->semester
                            ]
                        );
                    }

                    foreach ($pv->course_instructor_assignments as $civ) {
                        if ($civ->isprimary && !empty($civ->staff->user->username)) {
                            $stmt = $connection->execute('SELECT COUNT(*) AS count, username FROM users WHERE username = :username', ['username' => $civ->staff->user->username]);
                            $userExists = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

                            if ($userExists == 0) {
                                $connection->execute(
                                    'INSERT INTO users (username, password, firstname, middlename, lastname, email, city, country, idnumber, institution, department, mobile, phone, amharicfirstname, amhariclastname, address) VALUES (:username, :password, :firstname, :middlename, :lastname, :email, :city, :country, :idnumber, :institution, :department, :mobile, :phone, :amharicfirstname, :amhariclastname, :address)',
                                    [
                                        'username' => strtolower($civ->staff->user->email),
                                        'password' => $civ->staff->user->password,
                                        'firstname' => $civ->staff->first_name,
                                        'middlename' => $civ->staff->middle_name,
                                        'lastname' => $civ->staff->last_name,
                                        'email' => $civ->staff->email,
                                        'city' => $civ->staff->city->name ?? '',
                                        'country' => $civ->staff->country->name ?? '',
                                        'idnumber' => strtolower($civ->staff->user->email),
                                        'institution' => $civ->staff->college->name,
                                        'department' => $civ->staff->department->name,
                                        'mobile' => $civ->staff->phone_mobile,
                                        'phone' => $civ->staff->phone_office,
                                        'amharicfirstname' => $civ->staff->first_name,
                                        'amhariclastname' => $civ->staff->last_name,
                                        'address' => $civ->staff->address
                                    ]
                                );
                            }

                            $stmt = $connection->execute(
                                'SELECT COUNT(*) AS count, course_id FROM enrollment WHERE course_id = :course_id AND id_number = :id_number AND role_name = :role',
                                [
                                    'course_id' => $civ->published_course_id,
                                    'id_number' => $civ->staff->user->username,
                                    'role' => 'editingteacher'
                                ]
                            );
                            $enrollExists = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

                            if ($enrollExists == 0) {
                                $connection->execute(
                                    'INSERT INTO enrollment (course_id, id_number, role_name, ac_year, semester) VALUES (:course_id, :id_number, :role, :ac_year, :semester)',
                                    [
                                        'course_id' => $civ->published_course_id,
                                        'id_number' => strtolower($civ->staff->user->email),
                                        'role' => 'editingteacher',
                                        'ac_year' => $civ->academic_year,
                                        'semester' => $civ->semester
                                    ]
                                );
                            }
                        }
                    }

                    $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
                    $registeredStudentList = $courseRegistrationsTable->find()
                        ->where([
                            'CourseRegistrations.published_course_id' => $pv->id,
                            'CourseRegistrations.id NOT IN' => $courseRegistrationsTable->CourseDrops->find()->select(['course_registration_id'])
                        ])
                        ->contain([
                            'Students' => ['Users', 'Departments', 'Colleges', 'Cities', 'Countries']
                        ])
                        ->toArray();

                    foreach ($registeredStudentList as $regv) {
                        $stmt = $connection->execute('SELECT COUNT(*) AS count, idnumber FROM users WHERE idnumber = :idnumber', ['idnumber' => $regv->student->studentnumber]);
                        $userExists = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

                        if ($userExists == 0) {
                            $email = !empty($regv->student->email)
                                ? strtolower($regv->student->email)
                                : strtolower(str_replace('/', '-', $regv->student->studentnumber)) . INSTITUTIONAL_EMAIL_SUFFIX;

                            $connection->execute(
                                'INSERT INTO users (username, password, firstname, middlename, lastname, email, city, country, idnumber, institution, department, mobile, phone, amharicfirstname, amhariclastname, address) VALUES (:username, :password, :firstname, :middlename, :lastname, :email, :city, :country, :idnumber, :institution, :department, :mobile, :phone, :amharicfirstname, :amhariclastname, :address)',
                                [
                                    'username' => strtolower(str_replace('/', '.', $regv->student->user->username)),
                                    'password' => $regv->student->user->password,
                                    'firstname' => $regv->student->first_name,
                                    'middlename' => $regv->student->middle_name,
                                    'lastname' => $regv->student->last_name,
                                    'email' => $email,
                                    'city' => $regv->student->city->name ?? '',
                                    'country' => $regv->student->country->name ?? '',
                                    'idnumber' => $regv->student->studentnumber,
                                    'institution' => $regv->student->college->name,
                                    'department' => $regv->student->department->name,
                                    'mobile' => $regv->student->phone_mobile,
                                    'phone' => $regv->student->phone_home,
                                    'amharicfirstname' => $regv->student->amharic_first_name,
                                    'amhariclastname' => $regv->student->amharic_last_name,
                                    'address' => $regv->student->address1
                                ]
                            );
                        }

                        $stmt = $connection->execute(
                            'SELECT COUNT(*) AS count, course_id FROM enrollment WHERE course_id = :course_id AND id_number = :id_number AND role_name = :role',
                            [
                                'course_id' => $regv->published_course_id,
                                'id_number' => $regv->student->user->username,
                                'role' => 'student'
                            ]
                        );
                        $enrollExists = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

                        if ($enrollExists == 0) {
                            $connection->execute(
                                'INSERT INTO enrollment (course_id, id_number, role_name, ac_year, semester) VALUES (:course_id, :id_number, :role, :ac_year, :semester)',
                                [
                                    'course_id' => $regv->published_course_id,
                                    'id_number' => $regv->student->studentnumber,
                                    'role' => 'student',
                                    'ac_year' => $regv->academic_year,
                                    'semester' => $regv->semester
                                ]
                            );
                        }
                    }

                    $courseAddsTable = TableRegistry::getTableLocator()->get('CourseAdds');
                    $courseAddedStudentList = $courseAddsTable->find()
                        ->where(['CourseAdds.published_course_id' => $pv->id])
                        ->contain([
                            'Students' => ['Users', 'Departments', 'Colleges', 'Cities', 'Countries']
                        ])
                        ->toArray();

                    foreach ($courseAddedStudentList as $addv) {
                        $stmt = $connection->execute('SELECT COUNT(*) AS count, idnumber FROM users WHERE idnumber = :idnumber', ['idnumber' => $addv->student->studentnumber]);
                        $userExists = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

                        if ($userExists == 0) {
                            $email = !empty($addv->student->email)
                                ? strtolower($addv->student->email)
                                : strtolower(str_replace('/', '-', $addv->student->studentnumber)) . INSTITUTIONAL_EMAIL_SUFFIX;

                            $connection->execute(
                                'INSERT INTO users (username, password, firstname, middlename, lastname, email, city, country, idnumber, institution, department, mobile, phone, amharicfirstname, amhariclastname, address) VALUES (:username, :password, :firstname, :middlename, :lastname, :email, :city, :country, :idnumber, :institution, :department, :mobile, :phone, :amharicfirstname, :amhariclastname, :address)',
                                [
                                    'username' => strtolower(str_replace('/', '.', $addv->student->user->username)),
                                    'password' => $addv->student->user->password,
                                    'firstname' => $addv->student->first_name,
                                    'middlename' => $addv->student->middle_name,
                                    'lastname' => $addv->student->last_name,
                                    'email' => $email,
                                    'city' => $addv->student->city->name ?? '',
                                    'country' => $addv->student->country->name ?? '',
                                    'idnumber' => $addv->student->studentnumber,
                                    'institution' => $addv->student->college->name,
                                    'department' => $addv->student->department->name,
                                    'mobile' => $addv->student->phone_mobile,
                                    'phone' => $addv->student->phone_home,
                                    'amharicfirstname' => $addv->student->amharic_first_name,
                                    'amhariclastname' => $addv->student->amharic_last_name,
                                    'address' => $addv->student->address1
                                ]
                            );
                        }

                        $stmt = $connection->execute(
                            'SELECT COUNT(*) AS count, course_id FROM enrollment WHERE course_id = :course_id AND id_number = :id_number AND role_name = :role',
                            [
                                'course_id' => $addv->published_course_id,
                                'id_number' => $addv->student->user->username,
                                'role' => 'student'
                            ]
                        );
                        $enrollExists = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

                        if ($enrollExists == 0) {
                            $connection->execute(
                                'INSERT INTO enrollment (course_id, id_number, role_name, ac_year, semester) VALUES (:course_id, :id_number, :role, :ac_year, :semester)',
                                [
                                    'course_id' => $addv->published_course_id,
                                    'id_number' => $addv->student->studentnumber,
                                    'role' => 'student',
                                    'ac_year' => $addv->academic_year,
                                    'semester' => $addv->semester
                                ]
                            );
                        }
                    }
                }
            }

            $this->Flash->success(__($count > 0 ? 'You have successfully updated %d courses from SMIS to LMS system.' : 'There is no course to synchronize from SMIS to LMS system.', $count));
        }

        $colleges = $studentsTable->Colleges->find('list')->toArray();
        $departments = $studentsTable->Departments->find('list')->toArray();
        $programs = $studentsTable->Programs->find('list')->toArray();
        $programTypes = $studentsTable->ProgramTypes->find('list')->toArray();

        $this->set(compact('programs', 'programTypes', 'colleges', 'departments'));
    }

    private function _formatEthiopianPhoneNumber($number)
    {
        // Remove all non-digit characters
        $number = preg_replace('/\D/', '', $number);

        // Handle numbers with country code +251
        if (preg_match('/^251(9|7)\d{8}$/', $number)) {
            return '+251' . substr($number, 3);
        }

        // Handle numbers with leading 0
        if (preg_match('/^0(9|7)\d{8}$/', $number)) {
            return '+251' . substr($number, 1);
        }

        // Handle numbers without country code
        if (preg_match('/^(9|7)\d{8}$/', $number)) {
            return '+251' . $number;
        }

        return '';
    }
}
