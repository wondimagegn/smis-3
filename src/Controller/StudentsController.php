<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class StudentsController extends AppController
{

    public $name = 'Students';
    public $conn;
    public $config = array();

    public $menuOptions = array(
        'parent' => 'placement',
        'exclude' => array(
            'add',
            'search',
            'searchProfile',
            'nameChange',
            'correctName',
            'profileNotBuildList',
            'getCourseRegisteredAndAdd',
            'getPossibleSupRegisteredAndAdd',
            'deleteStudentFromGraduateListForCorrection',
            'activateDeactivateProfile'
        ),
        'alias' => array(
            'index' => 'List Admitted Students',
            'departmentIssuePassword' => 'Issue/Reset Password',
            'freshmanIssuePassword' => 'Issue/Reset Password',
            'nameList' => 'Correct Student Name',
            'idCardPrint' => 'Print Student ID Card',
            'moveBatchStudentToDepartment' => 'Move Batch Student to Other Department',
            'admitAll' => 'Admit Accepted Students',
            'massImportOneTimePasswords' => 'Import One Time Password'
        )
    );

    public $paginate = [];

    public function initialize()
    {

        parent::initialize();
        $this->loadComponent('AcademicYear');
        $this->loadComponent('EthiopicDateTime');
        $this->loadComponent('Paginator'); // Ensure Paginator is loaded
      //  $this->viewBuilder()->setHelpers(['DatePicker', 'Media.Media', 'Xls']);
    }

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
        $this->Auth->allow(
            'ajaxGetDepartment',
            'change',
            'getRegions',
            'getCities',
            'ajaxUpdate',
            'ajaxCheckEcardnumber',
            'getCourseRegisteredAndAdd',
            'getPossibleSupRegisteredAndAdd',
            'autoYearlevelUpdate',
            'studentLists',
            'search',
            'searchProfile',
            'getModalBox',
            'printRecord',
            'getCountries',
            'getZones',
            'getWoredas'
        );
    }


    public function beforeRender(Event $event)
    {

        parent::beforeRender($event);
        // Get current academic year
        $current_academicyear = $defaultacademicyear = $this->AcademicYear->currentAcademicYear();
        // Generate academic year arrays
        $acyear_array_data = $this->AcademicYear->academicYearInArray(
            APPLICATION_START_YEAR,
            explode('/', $current_academicyear)[0]
        );
        $acYearMinuSeparated = $this->AcademicYear->acYearMinuSeparated(
            APPLICATION_START_YEAR,
            explode('/', $current_academicyear)[0] + 1
        );

        $defaultacademicyearMinusSeparted = str_replace('/', '-', $defaultacademicyear);

        // Fetch Programs
        $programs = $this->Students->Programs->find('list', [
            'conditions' => [
                'Programs.id IN' => $this->program_ids,
                'Programs.active' => 1
            ]
        ])->toArray();

        // Fetch Program Types
        $program_types = $programTypes = $this->Students->ProgramTypes->find('list', [
            'conditions' => [
                'ProgramTypes.id IN' => $this->program_type_ids,
                'ProgramTypes.active' => 1
            ]
        ])->toArray();

        // Fetch Year Levels
        $yearLevels = $this->year_levels;

        if ($this->role_id == ROLE_DEPARTMENT) {
            $YearLevelTable = TableRegistry::getTableLocator()->get('YearLevels');

            $yearLevels = $YearLevelTable->find('list', [
                'conditions' => [
                    'YearLevels.department_id IN' => $this->department_ids,
                    'YearLevels.name IN' => $this->year_levels
                ]
            ])->toArray();
        }

        // Set view variables
        $this->set(compact(
            'acyear_array_data',
            'defaultacademicyear',
            'acYearMinuSeparated',
            'program_types',
            'programTypes',
            'defaultacademicyearMinusSeparted',
            'programs',
            'yearLevels'
        ));

        // Unset password before rendering view
        $data = $this->request->getData();
        if (!empty($data['User']['password'])) {
            unset($data['User']['password']);
            $this->request = $this->request->withParsedBody($data);
        }
    }
    public function __init_search()
    {
        $request = $this->getRequest();
        $session = $this->getRequest()->getSession();

        $searchData = $request->getData('Search');

        if (!empty($searchData)) {
            $searchSession = $searchData;

            if ($request->getData('getacceptedstudent') !== null || isset($searchData['getacceptedstudent'])) {
                $searchSession['getacceptedstudent'] = $request->getData('getacceptedstudent') ?? $searchData['getacceptedstudent'];
            }

            $session->write('search_data', $searchSession);
        } elseif ($session->check('search_data')) {
            $savedSearchData = $session->read('search_data');
            $this->request = $this->request->withData('Search', $savedSearchData);

            if (isset($savedSearchData['getacceptedstudent'])) {
                $this->request = $this->request->withData('getacceptedstudent', $savedSearchData['getacceptedstudent']);
                unset($savedSearchData['getacceptedstudent']);
            }
        }
    }

    public function __init_search_index()
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        $searchData = $request->getData('Search');

        if (!empty($searchData)) {
            $session->write('search_data_index', $searchData);
        } elseif ($session->check('search_data_index')) {
            $savedData = $session->read('search_data_index');
            $this->request = $this->request->withData('Search', $savedData);
        }

        // debug($this->request->getData());
    }


    public function __init_clear_session_filters()
    {
        $session = $this->getRequest()->getSession();

        $keys = [
            'search_data',
            'search_data_student',
            'search_data_index'
        ];

        foreach ($keys as $key) {
            if ($session->check($key)) {
                $session->delete($key);
            }
        }
    }


    public function __init_search_student()
    {
        $session = $this->getRequest()->getSession();
        $data = $this->getRequest()->getData();

        if (!empty($data['Student'])) {
            $session->write('search_data_student', $data['Student']);
        } elseif ($session->check('search_data_student')) {
            $this->request = $this->request->withData('Student', $session->read('search_data_student'));
        }

        if (!empty($data['Display'])) {
            $session->delete('display_field_student');
            $session->write('display_field_student', $data['Display']);
        }
    }


    // Generic search for returned items
    public function search()
    {
        $this->__init_search_student();

        $url = ['action' => 'index'];

        $data = $this->getRequest()->getData();
        unset($data['Display']);

        if (!empty($data)) {
            foreach ($data as $k => $v) {
                if (!empty($v)) {
                    foreach ($v as $kk => $vv) {
                        if (is_array($vv) && !empty($vv)) {
                            foreach ($vv as $kkk => $vvv) {
                                $url["$k.$kk.$kkk"] = str_replace('/', '-', trim($vvv));
                            }
                        } else {
                            $url["$k.$kk"] = str_replace('/', '-', trim($vv));
                        }
                    }
                }
            }
        }

        return $this->redirect($url);
    }


    public function searchProfile()
    {
        $this->__init_search_student();

        $url = ['action' => 'profile_not_build_list'];

        $data = $this->getRequest()->getData();
        unset($data['Display']);

        if (!empty($data)) {
            foreach ($data as $k => $v) {
                if (!empty($v)) {
                    foreach ($v as $kk => $vv) {
                        if (is_array($vv) && !empty($vv)) {
                            foreach ($vv as $kkk => $vvv) {
                                $url["$k.$kk.$kkk"] = str_replace('/', '-', trim($vvv));
                            }
                        } else {
                            $url["$k.$kk"] = str_replace('/', '-', trim($vv));
                        }
                    }
                }
            }
        }

        return $this->redirect($url);
    }

    public function index($data = null)
    {
        $session = $this->getRequest()->getSession();
        $queryParams = $this->getRequest()->getQuery();

        $limit = 100;
        $name = '';
        $page = null;
        $selected_academic_year = '';
        $options = [];

        // Handle passed args via query string
        if (!empty($queryParams)) {
            if (!empty($queryParams['Search.limit'])) {
                $limit = $queryParams['Search.limit'];
            }

            if (!empty($queryParams['Search.name'])) {
                $name = str_replace('-', '/', trim($queryParams['Search.name']));
            }

            $searchFields = [
                'department_id', 'college_id', 'academicyear', 'gender',
                'program_id', 'program_type_id', 'status', 'page', 'sort', 'direction'
            ];

            foreach ($searchFields as $field) {
                if (isset($queryParams["Search.$field"])) {
                    $this->request = $this->request->withData("Search.$field", $queryParams["Search.$field"]);
                }
            }

            $this->__init_search_index();
        }

        // If method param passed from search()
        if (!empty($data['Search'])) {
            $this->request = $this->request->withData('Search', $data['Search']);
            $this->__init_search_index();
        }

        // Clear previous search if "search" clicked
        if ($this->getRequest()->getData('search')) {
            $this->__init_clear_session_filters();
            $this->__init_search_index();
        }

        // Start processing filters and build query options
        $formData = $this->getRequest()->getData();

        if (!empty($formData)) {
            $user = $this->Authentication->getIdentity();
            $role_id = $user->role_id ?? null;

            // Example filter by role
            if ($role_id == ROLE_DEPARTMENT) {
                $departments = $this->Students->Departments->find('list', [
                    'conditions' => ['id' => $this->department_id, 'active' => 1]
                ]);
                $options['conditions']['Students.department_id'] = $this->department_id;
            }

            // Add more filter conditions like above...

            // Program filter
            if (!empty($formData['Search']['program_id'])) {
                $options['conditions']['Students.program_id'] = $formData['Search']['program_id'];
            }

            // Name search
            if (!empty($name)) {
                $options['conditions'][] = [
                    'OR' => [
                        'Students.first_name LIKE' => "%$name%",
                        'Students.middle_name LIKE' => "%$name%",
                        'Students.last_name LIKE' => "%$name%",
                        'Students.studentnumber LIKE' => "$name%"
                    ]
                ];
            }
        }

        // Add default conditions if nothing selected
        if (empty($options['conditions'])) {
            $options['conditions']['Students.id IS NOT'] = null;
            $options['conditions']['Students.graduated'] = 0;
        }

        // Pagination
        $this->paginate = [
            'conditions' => $options['conditions'],
            'contain' => ['Departments', 'Colleges', 'Programs', 'ProgramTypes'],
            'order' => [
                'Students.admissionyear' => 'DESC',
                'Students.created' => 'DESC'
            ],
            'limit' => $limit
        ];

        try {
            $students = $this->paginate($this->Students);
            $session->write('students', $students);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->Flash->info(__('No student is found with the given search criteria.'));
            return $this->redirect(['action' => 'index']);
        }

        $this->set(compact('students', 'limit', 'name'));
    }


    public function view($student_id = null)
    {
        $session = $this->getRequest()->getSession();
        $user = $this->Authentication->getIdentity();
        $role_id = $user->role_id ?? null;
        $current_student_id = $user->student_id ?? $student_id;

        if (!$current_student_id) {
            $this->Flash->error(__('Invalid student'));
            return $this->redirect(['action' => 'index']);
        }

        // Check student existence
        $checkStudent = $this->Students->exists(['Students.id' => $current_student_id]);

        if (!$checkStudent) {
            $this->Flash->info(__('Student ID not found.'));
            return $this->redirect(['action' => 'index']);
        }

        // Get full student detail
        $studentDetail = $this->Students->get($current_student_id, [
            'contain' => [
                'Users', 'AcceptedStudents', 'Programs', 'ProgramTypes',
                'Contacts', 'Countries', 'Regions', 'Zones', 'Woredas', 'Cities',
                'Departments', 'Colleges', 'EslceResults', 'EheeceResults',
                'Attachments', 'HigherEducationBackgrounds', 'HighSchoolEducationBackgrounds'
            ]
        ]);

        // Optionally prepare data for form prefilling
        if ($this->getRequest()->is('get')) {
            $this->request = $this->request->withData('Student', $studentDetail->toArray());
        }

        // Fix gender value based on AcceptedStudent or Student
        if (isset($studentDetail->accepted_student->sex)) {
            $studentDetail->gender = strtolower(trim($studentDetail->accepted_student->sex));
        }

        // Form dropdowns
        $regions = $this->Students->Regions->find('list')->toArray();
        $countries = $this->Students->Countries->find('list')->toArray();
        $cities = $this->Students->Cities->find('list')->toArray();
        $zones = $this->Students->Zones->find('list')->toArray();
        $woredas = $this->Students->Woredas->find('list')->toArray();

        $colleges = $this->Students->Colleges->find('list', [
            'conditions' => ['Colleges.id' => $studentDetail->college_id]
        ])->toArray();

        $departments = [];
        if (!empty($studentDetail->department_id)) {
            $departments = $this->Students->Departments->find('list', [
                'conditions' => ['Departments.id' => $studentDetail->department_id]
            ])->toArray();
        }

        $contacts = $this->Students->Contacts->find('list', [
            'conditions' => ['Contacts.student_id' => $current_student_id]
        ])->toArray();

        $users = $this->Students->Users->find('list', [
            'conditions' => ['Users.username' => $studentDetail->studentnumber]
        ])->toArray();

        $programs = $this->Students->Programs->find('list', [
            'conditions' => ['Programs.id' => $studentDetail->program_id]
        ])->toArray();

        $programTypes = $this->Students->ProgramTypes->find('list', [
            'conditions' => ['ProgramTypes.id' => $studentDetail->program_type_id]
        ])->toArray();

        $this->set(compact(
            'studentDetail', 'contacts', 'users', 'colleges', 'departments',
            'programs', 'programTypes', 'regions', 'countries', 'cities', 'zones', 'woredas'
        ));
    }

    public function edit($id = null)
    {
        if (!$id) {
            $this->Flash->error(__('Invalid Student ID'));
            return $this->redirect(['action' => 'index']);
        }

        $student = $this->Students->get($id, [
            'contain' => [
                'Users', 'AcceptedStudents', 'Programs', 'ProgramTypes',
                'Contacts', 'Departments', 'Colleges', 'Countries',
                'Regions', 'Cities', 'Zones', 'Woredas',
                'EslceResults', 'EheeceResults',
                'Attachments', 'HigherEducationBackgrounds', 'HighSchoolEducationBackgrounds'
            ]
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $student = $this->Students->patchEntity($student, $this->request->getData(), [
                'associated' => [
                    'Users', 'Contacts', 'Attachments', 'EslceResults',
                    'EheeceResults', 'HigherEducationBackgrounds', 'HighSchoolEducationBackgrounds'
                ]
            ]);

            if ($this->Students->save($student)) {
                $this->Flash->success(__('Student profile has been updated.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The student profile could not be saved. Please try again.'));
        }

        // Setup dropdown lists, regions, and conditionally-loaded lists here
        // Will handle this in next step
        $this->set(compact('student'));
    }



    // function which will allow the registrar to admit all students then update


    public function admitAll()
    {
        $acceptedStudentsTable = TableRegistry::getTableLocator()->get('AcceptedStudents');
        $studentsTable = $this->Students;

        $data = $this->getRequest()->getData();
        $lastSuccessMessage = '';

        if (!empty($data) && !empty($data['admit'])) {
            $searchSession = $data['Search'];
            $this->getRequest()->getSession()->write('search_data', $searchSession);

            $approveList = $data['AcceptedStudent']['approve'] ?? [];
            $selectedIds = array_keys(array_filter($approveList));

            if (empty($selectedIds)) {
                $this->Flash->error(__('Please select at least one student to admit.'));
                return;
            }

            $admitEntities = [];

            foreach ($selectedIds as $id) {
                $accepted = $acceptedStudentsTable->get($id, ['contain' => []]);

                // Check for duplication
                $exists = $studentsTable->exists([
                    'OR' => [
                        'accepted_student_id' => $accepted->id,
                        'studentnumber' => $accepted->studentnumber
                    ]
                ]);

                if (!$exists) {
                    $student = $studentsTable->newEntity([
                        'first_name' => $accepted->first_name,
                        'middle_name' => $accepted->middle_name,
                        'last_name' => $accepted->last_name,
                        'user_id' => $accepted->user_id,
                        'accepted_student_id' => $accepted->id,
                        'gender' => $accepted->sex,
                        'studentnumber' => $accepted->studentnumber,
                        'country_id' => TableRegistry::getTableLocator()
                            ->get('Regions')->get($accepted->region_id)->country_id,
                        'region_id' => $accepted->region_id,
                        'program_id' => $accepted->program_id,
                        'college_id' => $accepted->college_id,
                        'original_college_id' => $accepted->college_id,
                        'department_id' => $accepted->department_id,
                        'program_type_id' => $accepted->program_type_id,
                        'curriculum_id' => $accepted->curriculum_id,
                        'high_school' => $accepted->high_school,
                        'moeadmissionnumber' => $accepted->moeadmissionnumber,
                        'benefit_group' => $accepted->benefit_group,
                        'academicyear' => $accepted->academicyear,
                        'admissionyear' => $accepted->created
                    ]);
                    $admitEntities[] = $student;
                }
            }

            if ($studentsTable->saveMany($admitEntities)) {
                $this->Flash->success(__('All selected students have been admitted successfully.'));
                return $this->redirect(['action' => 'admitAll']);
            } else {
                $this->Flash->error(__('Could not admit the selected students. Please, try again.'));
            }
        }

        // Setup dropdown filters and related info (colleges, departments, programs...)
        $this->loadComponent('Paginator');
        $programs = $studentsTable->Programs->find('list')->where(['Programs.active' => 1]);
        $programTypes = $studentsTable->ProgramTypes->find('list')->where(['ProgramTypes.active' => 1]);

        $this->set(compact('programs', 'programTypes'));
    }


    public function admit($id = null)
    {
        $session = $this->getRequest()->getSession();
        $acceptedStudentsTable = TableRegistry::getTableLocator()->get('AcceptedStudents');
        $studentsTable = TableRegistry::getTableLocator()->get('Students');

        if ($id) {
            // Check authorization
            $conditions = ['AcceptedStudents.id' => $id];
            if (!empty($this->college_ids)) {
                $conditions['AcceptedStudents.college_id IN'] = $this->college_ids;
            } elseif (!empty($this->department_ids)) {
                $conditions['AcceptedStudents.department_id IN'] = $this->department_ids;
            }

            $eligibilityCount = $acceptedStudentsTable->find()->where($conditions)->count();

            if ($eligibilityCount == 0) {
                $this->Flash->error(__('You are not eligible to admit the student.'));
                return $this->redirect(['action' => 'index']);
            }

            $studentRecord = $acceptedStudentsTable->find()
                ->select(['studentnumber'])
                ->where(['AcceptedStudents.id' => $id])
                ->first();

            if (empty($studentRecord->studentnumber)) {
                $this->Flash->error(__('Please generate a student number before admission.'));
                return $this->redirect(['controller' => 'AcceptedStudents', 'action' => 'generate']);
            }

            if ($studentsTable->exists(['accepted_student_id' => $id])) {
                $this->Flash->warning(__('This student has already been admitted.'));
                return $this->redirect(['controller' => 'Students', 'action' => 'admit']);
            }
        }

        if ($this->request->is('post') && $this->request->getData('admit')) {
            $data = $this->request->getData();
            $data = $studentsTable->unsetEmpty($data); // If you implemented this helper

            $data['User']['role_id'] = ROLE_STUDENT;
            $data['User']['username'] = $data['Student']['studentnumber'];

            // Cleanup if optional models are not filled
            if (empty($data['HigherEducationBackground'][0]['name'])) {
                unset($data['HigherEducationBackground']);
            }

            if ($studentsTable->saveAll($data, ['validate' => 'first'])) {
                $this->Flash->success(__('Student has been admitted successfully.'));
                return $this->redirect(['action' => 'admit']);
            }

            $this->Flash->error(__('Failed to admit student. Please try again.'));
        }

        if ($this->request->is(['post', 'put']) && $this->request->getData('getacceptedstudent')) {
            // Load eligible students (pagination)
            $query = $acceptedStudentsTable->find()
                ->where([
                    'AcceptedStudents.id NOT IN' => $studentsTable->find()
                        ->select(['accepted_student_id'])
                        ->where(['accepted_student_id IS NOT' => null])
                ])
                ->contain(['Program', 'ProgramType', 'College', 'Department']);

            $this->paginate = [
                'limit' => 50,
                'order' => ['AcceptedStudents.created' => 'DESC']
            ];
            $this->set('acceptedStudents', $this->paginate($query));
        }

        // Populate colleges and departments dropdowns
        $colleges = $this->Students->Colleges->find('list', ['conditions' => ['Colleges.active' => 1]]);
        $departments = $this->Students->Departments->find('list', ['conditions' => ['Departments.active' => 1]]);

        // Load dropdown data
        $regions = $this->Students->Regions->find('list', ['conditions' => ['Regions.active' => 1]]);
        $zones = $this->Students->Zones->find('list');
        $woredas = $this->Students->Woredas->find('list');
        $countries = $this->Students->Countries->find('list');
        $cities = $this->Students->Cities->find('list');

        $this->set(compact(
            'id',
            'regions',
            'zones',
            'woredas',
            'countries',
            'cities',
            'colleges',
            'departments'
        ));
    }


    public function getCountries($region_id = null)
    {
        $this->viewBuilder()->setLayout('ajax');
        $countries = [];

        $this->loadModel('Regions');
        $this->loadModel('Countries');

        // Determine the region or country ID source
        if (!empty($region_id)) {
            $countryIds = $this->Regions->find('list', [
                'conditions' => ['Regions.id' => $region_id],
                'keyField' => 'country_id',
                'valueField' => 'country_id'
            ])->toArray();
        } elseif (!empty($this->request->getData('Student.region_id'))) {
            $countryIds = $this->Regions->find('list', [
                'conditions' => ['Regions.id' => $this->request->getData('Student.region_id')],
                'keyField' => 'country_id',
                'valueField' => 'country_id'
            ])->toArray();
        } elseif (!empty($this->request->getData('Student.country_id'))) {
            $countryIds = [$this->request->getData('Student.country_id')];
        } else {
            $countryIds = null;
        }

        if (!empty($countryIds)) {
            $countries = $this->Countries->find('list', [
                'conditions' => ['Countries.id IN' => $countryIds],
                'keyField' => 'id',
                'valueField' => 'name'
            ])->toArray();
        } else {
            $countries = $this->Countries->find('list', [
                'keyField' => 'id',
                'valueField' => 'name'
            ])->toArray();
        }

        $this->set(compact('countries'));
        $this->set('_serialize', ['countries']); // Optional: for JSON responses
    }
    public function getRegions($country_id = null)
    {
        $this->viewBuilder()->setLayout('ajax');
        $this->loadModel('Regions');

        if ($country_id) {
            $regions = $this->Regions->find('list', [
                'conditions' => ['Regions.country_id' => $country_id],
                'keyField' => 'id',
                'valueField' => 'name'
            ])->toArray();
        } else {
            $studentCountryId = $this->request->getData('Student.country_id');
            $regions = $this->Regions->find('list', [
                'conditions' => ['Regions.country_id' => $studentCountryId],
                'keyField' => 'id',
                'valueField' => 'name'
            ])->toArray();
        }

        $this->set(compact('regions'));
        $this->set('_serialize', ['regions']); // Optional for JSON API response
    }


    public function getZones($regionId = null)
    {
        $this->request->allowMethod(['get', 'ajax']);
        $this->viewBuilder()->setLayout('ajax');

        $regionId = $regionId ?? $this->request->getData('region_id');

        $zones = $this->Students->Zones->find('list', [
            'conditions' => ['Zones.region_id' => $regionId],
            'keyField' => 'id',
            'valueField' => 'name'
        ])->toArray();

        $this->set(compact('zones'));
        $this->set('_serialize', ['zones']);
    }


    public function getWoredas($zoneId = null)
    {
        $this->request->allowMethod(['get', 'ajax']);
        $this->viewBuilder()->setLayout('ajax');

        // If not passed via URL, try to get from POST data
        if (!$zoneId) {
            $zoneId = $this->request->getData('Student.zone_id');
        }

        $woredas = $this->Students->Woredas->find('list', [
            'conditions' => ['Woredas.zone_id' => $zoneId],
            'keyField' => 'id',
            'valueField' => 'name'
        ])->toArray();

        $this->set(compact('woredas'));
    }


    public function getCities($regionId = null)
    {
        $this->request->allowMethod(['get', 'ajax']);
        $this->viewBuilder()->setLayout('ajax');

        if (!$regionId) {
            $regionId = $this->request->getData('Student.region_id');
        }

        $cities = $this->Students->Cities->find('list', [
            'conditions' => ['Cities.region_id' => $regionId],
            'keyField' => 'id',
            'valueField' => 'name'
        ])->toArray();

        $this->set(compact('cities'));
    }


    public function ajaxGetDepartment()
    {
        $this->request->allowMethod(['post', 'ajax']);
        $this->viewBuilder()->setLayout('ajax');

        $collegeId = $this->request->getData('Staff.0.college_id');

        $departments = $this->Students->Departments->find('list', [
            'conditions' => ['Departments.college_id' => $collegeId],
            'keyField' => 'id',
            'valueField' => 'name'
        ])->toArray();

        $this->set(compact('departments'));
    }



    public function profile($student_id = null)
    {

        $check_student_admitted = $this->Student->find(
            'count',
            array('conditions' => array('Student.id' => (isset($this->student_id) || $this->role_id == ROLE_STUDENT ? $this->student_id : (isset($student_id) ? $student_id : 0))))
        );

        if ($check_student_admitted == 0) {
            $this->Flash->info('You profile will be available after registrar finishes the admission data entry.');
            $this->redirect('/dashboard/index');
        } else {
            if ($this->Session->read('Auth.User')['role_id'] == ROLE_STUDENT) {
                $require_update = false;
                $require_update_fields = array();
                $rupdt_key = 0;

                $studentDetail = $this->Student->find('first', array(
                    'conditions' => array(
                        'Student.id' => (isset($this->student_id) || $this->role_id == ROLE_STUDENT ? $this->student_id : (isset($student_id) ? $student_id : 0))
                    ),
                    'contain' => array(
                        'User',
                        'AcceptedStudent',
                        'Program',
                        'ProgramType',
                        'Contact',
                        'Department',
                        'College',
                        'EslceResult',
                        'EheeceResult',
                        'Attachment',
                        'HigherEducationBackground',
                        'HighSchoolEducationBackground',
                        'Country',
                        'Region',
                        'City',
                        'Zone',
                        'Woreda'
                    )
                ));

                $student_admission_year = ((int)(isset($studentDetail['AcceptedStudent']['academicyear']) && !empty($studentDetail['AcceptedStudent']['academicyear']) ? (explode(
                    '/',
                    $studentDetail['AcceptedStudent']['academicyear']
                )[0]) : (isset($studentDetail['Student']['academicyear']) && !empty($studentDetail['Student']['academicyear']) ? (explode(
                    '/',
                    $studentDetail['Student']['academicyear']
                )[0]) : (explode('/', $this->AcademicYear->current_academicyear())[0]))));


                if ($this->Auth->user('id') != $studentDetail['Student']['user_id']) {
                    $this->Flash->error(__('There is a conflictiong session please login again.'));
                    $this->Session->destroy();
                    $this->redirect($this->Auth->logout());
                }

                //debug($studentDetail);

                if (!empty($this->request->data) && isset($this->request->data['updateStudentDetail'])) {
                    unset($this->request->data['User']);

                    if (isset($this->request->data['AcceptedStudent'])) {
                        unset($this->request->data['AcceptedStudent']);
                    }

                    if (isset($this->request->data['College'])) {
                        unset($this->request->data['College']);
                    }

                    //unset($this->request->data['Student']['gender']);

                    if (strcasecmp(trim($studentDetail['AcceptedStudent']['sex']), "female") == 0 || strcasecmp(
                            trim($studentDetail['AcceptedStudent']['sex']),
                            "f"
                        ) == 0) {
                        $this->request->data['Student']['gender'] = 'Female';
                    } else {
                        if (strcasecmp(trim($studentDetail['AcceptedStudent']['sex']), "male") == 0 || strcasecmp(
                                trim($studentDetail['AcceptedStudent']['sex']),
                                "m"
                            ) == 0) {
                            $this->request->data['Student']['gender'] = 'Male';
                        } else {
                            $this->request->data['Student']['gender'] = (ucfirst(
                                strtolower(trim($studentDetail['AcceptedStudent']['sex']))
                            ));
                        }
                    }

                    if (!empty($this->request->data['Student']['email'])) {
                        $this->request->data['User']['email'] = trim($this->request->data['Student']['email']);
                        if ($this->role_id == ROLE_STUDENT && $this->Auth->user(
                                'id'
                            ) == $studentDetail['Student']['user_id']) {
                            $this->request->data['User']['id'] = $this->Auth->user('id');
                        } else {
                            if ($studentDetail['Student']['user_id']) {
                                $this->request->data['User']['id'] = $studentDetail['Student']['user_id'];
                            } else {
                                $student_user_id = $this->Student->User->field(
                                    'User.id',
                                    array(
                                        'User.username LIKE ' => $studentDetail['Student']['studentnumber'],
                                        'User.role_id' => ROLE_STUDENT
                                    )
                                );

                                if (!empty($student_user_id)) {
                                    $this->request->data['User']['id'] = $student_user_id;
                                }
                            }
                        }
                    }

                    // $student_user_id = $this->Student->User->field('User.id', array('User.username LIKE ' => $studentDetail['Student']['studentnumber'], 'User.role_id' => ROLE_STUDENT));
                    // debug($student_user_id);

                    if (isset($this->request->data['updateStudentDetail'])) {
                        if (!empty($this->request->data['Student']['phone_mobile']) && !empty($this->request->data['Student']['email'])) {
                            $this->request->data = $this->Student->unset_empty($this->request->data);


                            if (empty($this->request->data['Student']['city_id'])) {
                                unset($this->request->data['Student']['city_id']);
                            }

                            if (isset($this->request->data['Attachment']) && (empty($this->request->data['Attachment'][0]['file']['name']) || $this->request->data['Attachment'][0]['file']['error'])) {
                                unset($this->request->data['Attachment']);
                            }

                            if (isset($this->request->data['HighSchoolEducationBackground']) && (empty($this->request->data['HighSchoolEducationBackground'][0]['name']) || empty($this->request->data['HighSchoolEducationBackground'][0]['town']) || empty($this->request->data['HighSchoolEducationBackground'][0]['region_id']))) {
                                unset($this->request->data['HighSchoolEducationBackground']);
                            }

                            if (isset($this->request->data['HigherEducationBackground']) && (empty($this->request->data['HigherEducationBackground'][0]['name']) || empty($this->request->data['HigherEducationBackground'][0]['field_of_study']) || empty($this->request->data['HigherEducationBackground'][0]['diploma_awarded']) || empty($this->request->data['HigherEducationBackground'][0]['cgpa_at_graduation']))) {
                                unset($this->request->data['HigherEducationBackground']);
                            }

                            if (isset($this->request->data['EheeceResult']) && (empty($this->request->data['EheeceResult'][0]['subject']) || empty($this->request->data['EheeceResult'][0]['mark']) /* || empty($this->request->data['EheeceResult'][0]['exam_year']) */)) {
                                unset($this->request->data['EheeceResult']);
                            }

                            if (isset($this->request->data['EslceResult']) && (empty($this->request->data['EslceResult'][0]['subject']) || empty($this->request->data['EslceResult'][0]['grade']) || empty($this->request->data['EslceResult'][0]['exam_year']))) {
                                unset($this->request->data['EslceResult']);
                            }

                            unset($this->request->data['updateStudentDetail']);

                            debug($this->request->data);

                            if ($this->Student->saveAll($this->request->data, array('validate' => 'first'))) {
                                $this->Flash->success(__('Your Profile has been updated.'));
                                return $this->redirect(array('controller' => 'dashboard', 'action' => 'index'));
                                //return $this->redirect(array('action' => 'profile'));
                            } else {
                                $this->Flash->error(__('Your student profile could not be saved. Please, try again.'));
                            }
                        } else {
                            if (empty($this->request->data['Student']['phone_mobile']) && empty($this->request->data['Student']['email'])) {
                                $this->Flash->error(
                                    __(
                                        'Please provide your mobile phone number and personal email address. You can use ' . (strtolower(
                                                str_replace('/', '.', $studentDetail['Student']['studentnumber'])
                                            ) . INSTITUTIONAL_EMAIL_SUFFIX) . ' if you don\'t have personal email address like Gmail, yahoo, hotmail etc..'
                                    )
                                );
                            } else {
                                if (empty($this->request->data['Student']['phone_mobile'])) {
                                    $this->Flash->error(__('Please provide your mobile phone number.'));
                                } else {
                                    $this->Flash->error(
                                        __(
                                            'Please provide your personal email address. You can use ' . (strtolower(
                                                    str_replace('/', '.', $studentDetail['Student']['studentnumber'])
                                                ) . INSTITUTIONAL_EMAIL_SUFFIX) . ' if you don\'t have personal email address like Gmail, yahoo, hotmail etc..'
                                        )
                                    );
                                }
                            }
                        }
                    }
                }

                if (empty($this->request->data)) {
                    $this->request->data = $this->Student->find('first', array(
                        'conditions' => array(
                            'Student.id' => (isset($this->student_id) || $this->role_id == ROLE_STUDENT ? $this->student_id : (isset($student_id) ? $student_id : 0))
                        ),
                        'contain' => array(
                            'User',
                            'AcceptedStudent',
                            'Program',
                            'ProgramType',
                            'Department',
                            'College',
                            'Contact',
                            'EslceResult',
                            'EheeceResult',
                            'Attachment',
                            'HigherEducationBackground',
                            'HighSchoolEducationBackground',
                            'Country',
                            'Region',
                            'City',
                            'Zone',
                            'Woreda'
                        )
                    ));
                }

                $this->request->data['Student']['gender'] = (isset($this->request->data['AcceptedStudent']['sex']) ? (ucfirst(
                    strtolower(trim($this->request->data['AcceptedStudent']['sex']))
                )) : (ucfirst(strtolower(trim($this->request->data['Student']['gender'])))));

                if (strcasecmp(trim($studentDetail['AcceptedStudent']['sex']), "female") == 0 || strcasecmp(
                        trim($studentDetail['AcceptedStudent']['sex']),
                        "f"
                    ) == 0) {
                    $this->request->data['Student']['gender'] = 'Female';
                } else {
                    if (strcasecmp(trim($studentDetail['AcceptedStudent']['sex']), "male") == 0 || strcasecmp(
                            trim($studentDetail['AcceptedStudent']['sex']),
                            "m"
                        ) == 0) {
                        $this->request->data['Student']['gender'] = 'Male';
                    } else {
                        $this->request->data['Student']['gender'] = (ucfirst(
                            strtolower(trim($studentDetail['AcceptedStudent']['sex']))
                        ));
                    }
                }

                if (isset($this->request->data['EheeceResult'][0]['exam_year']) && !empty($this->request->data['EheeceResult'][0]['exam_year']) && !$this->AcademicYear->isValidDateWithinYearRange(
                        $this->request->data['EheeceResult'][0]['exam_year'],
                        ($student_admission_year - 10),
                        $student_admission_year
                    )) {
                    $require_update = true;
                    $require_update_fields[$rupdt_key]['field'] = 'EHEECE Exam Taken Date';
                    $require_update_fields[$rupdt_key]['previous_value'] = $this->request->data['EheeceResult'][0]['exam_year'];
                    $this->request->data['EheeceResult'][0]['exam_year'] = $student_admission_year . '-' . '07-01';
                    $require_update_fields[$rupdt_key]['auto_corrected_value'] = $this->request->data['EheeceResult'][0]['exam_year'];
                    $require_update_fields[$rupdt_key]['reason'] = 'EHEECE Exam Taken Date is not valid date.';

                    if (((int)explode(
                            '-',
                            $studentDetail['EheeceResult'][0]['exam_year']
                        )[0]) > $student_admission_year) {
                        $require_update_fields[$rupdt_key]['reason'] = 'EHEECE Exam Taken Date can\'t be behind Student Admission Year.';
                    }

                    $rupdt_key++;
                } else {
                    if (empty($studentDetail['EheeceResult'])) {
                        $this->request->data['EheeceResult'][0]['exam_year'] = $student_admission_year . '-' . '07-01';
                    }
                }


                $maximum_estimated_graduation_year_limit = $student_admission_year;

                if ($studentDetail['Student']['program_id'] == PROGRAM_UNDEGRADUATE || $studentDetail['Student']['program_id'] == PROGRAM_PhD) {
                    $maximum_estimated_graduation_year_limit = $student_admission_year + 6;
                } else {
                    if ($studentDetail['Student']['program_id'] == PROGRAM_POST_GRADUATE) {
                        if ($studentDetail['Student']['program_type_id'] == PROGRAM_TYPE_REGULAR) {
                            $maximum_estimated_graduation_year_limit = $student_admission_year + 3;
                        } else {
                            $maximum_estimated_graduation_year_limit = $student_admission_year + 6;
                        }
                    } else {
                        // Remedial and PGDT
                        $maximum_estimated_graduation_year_limit = $student_admission_year;
                    }
                }


                if (!empty($studentDetail['Student']['curriculum_id']) && $studentDetail['Student']['curriculum_id'] > 0) {
                    $get_curriculum_year_level_count = $this->Student->Curriculum->Course->find(
                        'count',
                        array(
                            'conditions' => array('Course.curriculum_id' => $studentDetail['Student']['curriculum_id']),
                            'group' => array('Course.year_level_id')
                        )
                    );

                    if ($studentDetail['Student']['program_id'] == PROGRAM_UNDEGRADUATE || $studentDetail['Student']['program_type_id'] != PROGRAM_TYPE_REGULAR) {
                        if (!empty($get_curriculum_year_level_count)) {
                            $maximum_estimated_graduation_year_limit = $student_admission_year + ($get_curriculum_year_level_count * 2);
                        }
                    }

                    //debug($get_curriculum_year_level_count);

                    if (isset($this->request->data['Student']['estimated_grad_date']) && !empty($this->request->data['Student']['estimated_grad_date']) && !$this->AcademicYear->isValidDateWithinYearRange(
                            $this->request->data['Student']['estimated_grad_date'],
                            $student_admission_year,
                            ($student_admission_year + ($get_curriculum_year_level_count * 2))
                        )) {
                        $require_update = true;
                        $require_update_fields[$rupdt_key]['field'] = 'Estimated Graduation Date';
                        $require_update_fields[$rupdt_key]['previous_value'] = $this->request->data['Student']['estimated_grad_date'];
                        $this->request->data['Student']['estimated_grad_date'] = ($student_admission_year + $get_curriculum_year_level_count) . '-08-01';
                        $require_update_fields[$rupdt_key]['auto_corrected_value'] = $this->request->data['Student']['estimated_grad_date'];
                        $require_update_fields[$rupdt_key]['reason'] = 'Estimated Graduation Date is not valid date.';

                        if (((int)explode(
                                '-',
                                $studentDetail['Student']['estimated_grad_date']
                            )[0]) > ($student_admission_year + ($get_curriculum_year_level_count * 2))) {
                            $require_update_fields[$rupdt_key]['reason'] = 'Estimated Graduation Date can\'t be behind ' . ($student_admission_year + ($get_curriculum_year_level_count * 2)) . ' G.C. (Double of student\'s attached curriculum year levels, ' . $get_curriculum_year_level_count . ' X 2 years)';
                        } else {
                            if (((int)explode(
                                    '-',
                                    $studentDetail['Student']['estimated_grad_date']
                                )[0]) < ($student_admission_year + $get_curriculum_year_level_count)) {
                                $require_update_fields[$rupdt_key]['reason'] = 'Estimated Graduation Date can\'t be before ' . ($student_admission_year + $get_curriculum_year_level_count) . ' G.C.';
                            }
                        }

                        $rupdt_key++;
                    } else {
                        if (empty($studentDetail['Student']['estimated_grad_date'])) {
                            $this->request->data['Student']['estimated_grad_date'] = ($student_admission_year + $get_curriculum_year_level_count) . '-08-01';
                        }
                    }
                } else {
                    if (empty($studentDetail['Student']['estimated_grad_date']) || is_null(
                            $studentDetail['Student']['estimated_grad_date']
                        )) {
                        $this->request->data['Student']['estimated_grad_date'] = $maximum_estimated_graduation_year_limit . '-08-01';
                    }
                }

                //debug($this->request->data);

                //$foriegn_students_region_ids = Configure::read('foriegn_students_region_ids');

                $foriegn_students_region_ids = $this->Student->Region->find(
                    'list',
                    array(
                        'conditions' => array('Region.country_id <> ' => COUNTRY_ID_OF_ETHIOPIA),
                        'fields' => array('Region.id', 'Region.id')
                    )
                );

                debug($foriegn_students_region_ids);

                $regions = array();
                $zones = array();
                $woredas = array();
                $cities = array();

                $foriegn_student = 0;

                $country_id_of_region = COUNTRY_ID_OF_ETHIOPIA;

                $region_id_of_student = '';

                if (!empty($studentDetail['AcceptedStudent']['region_id']) || !empty($studentDetail['Student']['region_id'])) {
                    $region_id_of_student = (!empty($studentDetail['AcceptedStudent']['region_id']) ? $studentDetail['AcceptedStudent']['region_id'] : $studentDetail['Student']['region_id']);

                    $country_id_of_region = $this->Student->Region->field(
                        'country_id',
                        array('Region.id' => $region_id_of_student)
                    );

                    $countries = $this->Student->Country->find(
                        'list',
                        array('conditions' => array('Country.id' => $country_id_of_region))
                    );

                    $regions = $this->Student->Region->find('list', array(
                        'conditions' => array(
                            'Region.id' => $region_id_of_student,
                            'Region.country_id' => $country_id_of_region
                        )
                    ));

                    $zones = $this->Student->Zone->find(
                        'list',
                        array('conditions' => array('Zone.region_id' => $region_id_of_student))
                    );

                    $city_zone_ids = $this->Student->City->find('list', array(
                        'conditions' => array(
                            'City.region_id' => $region_id_of_student
                        ),
                        'fields' => array('City.zone_id', 'City.zone_id')
                    ));

                    $woredas = $this->Student->Woreda->find('list', array(
                        'conditions' => array(
                            'Woreda.zone_id' => (!empty($zones) ? array_keys(
                                $zones
                            ) : (!empty($city_zone_ids) ? $city_zone_ids : null)),
                        )
                    ));

                    $cities = $this->Student->City->find('list', array(
                        'conditions' => array(
                            'OR' => array(
                                'City.id' => $studentDetail['Student']['city_id'],
                                'City.zone_id' => (!empty($zones) ? array_keys(
                                    $zones
                                ) : (!empty($studentDetail['AcceptedStudent']['zone_id']) ? $studentDetail['AcceptedStudent']['zone_id'] : $studentDetail['Student']['zone_id'])),
                                'City.region_id' => $region_id_of_student,
                            )
                        )
                    ));
                } else {
                    $countries = $this->Student->Country->find('list');
                    $regions = $this->Student->Region->find('list', array('conditions' => array('Region.active' => 1)));
                    $zones = $this->Student->Zone->find('list', array('conditions' => array('Zone.active' => 1)));
                    $woredas = $this->Student->Woreda->find('list', array('conditions' => array('Woreda.active' => 1)));
                    $cities = $this->Student->City->find('list', array('conditions' => array('City.active' => 1)));
                }

                if (empty($regions)) {
                    $regions = $this->Student->Region->find(
                        'list',
                        array('conditions' => array('Region.country_id' => $country_id_of_region))
                    );
                }

                if (empty($zones)) {
                    $zones = $this->Student->Zone->find('list');
                }

                if (empty($woredas)) {
                    $woredas = $this->Student->Woreda->find('list');
                }

                if (empty($cities)) {
                    if (!empty($region_id_of_student)) {
                        $cities = $this->Student->City->find(
                            'list',
                            array('conditions' => array('City.region_id' => $region_id_of_student))
                        );
                    } else {
                        if (!empty($regions)) {
                            $cities = $this->Student->City->find(
                                'list',
                                array('conditions' => array('City.region_id' => array_keys($regions)))
                            );
                        } else {
                            $cities = $this->Student->City->find('list');
                        }
                    }
                }

                if (!empty($foriegn_students_region_ids) && ((isset($studentDetail['AcceptedStudent']['region_id']) && !empty($studentDetail['AcceptedStudent']['region_id']) && in_array(
                                $studentDetail['AcceptedStudent']['region_id'],
                                $foriegn_students_region_ids
                            )) || (isset($studentDetail['Student']['region_id']) && !empty($studentDetail['Student']['region_id']) && in_array(
                                $studentDetail['Student']['region_id'],
                                $foriegn_students_region_ids
                            )))) {
                    $foriegn_student = 1;
                }

                $colleges = $this->Student->College->find(
                    'list',
                    array('conditions' => array('College.id' => $studentDetail['Student']['college_id']))
                );

                if (!empty($studentDetail['Student']['department_id']) && is_numeric(
                        $studentDetail['Student']['department_id']
                    ) && $studentDetail['Student']['department_id'] > 0) {
                    $departments = $this->Student->Department->find(
                        'list',
                        array('conditions' => array('Department.id' => $studentDetail['Student']['department_id']))
                    );
                } else {
                    //$departments = $this->Student->Department->find('list');
                    $departments = array();
                }


                $regionsAll = $this->Student->Region->find(
                    'list',
                    array('conditions' => array('Region.active' => 1, 'Region.country_id' => $country_id_of_region))
                );
                $zonesAll = $this->Student->Zone->find('list', array('conditions' => array('Zone.active' => 1)));
                $woredasAll = $this->Student->Woreda->find('list', array('conditions' => array('Woreda.active' => 1)));
                $citiesAll = $this->Student->City->find('list', array('conditions' => array('City.active' => 1)));

                if (isset($this->request->data['Contact'][0]['region_id']) && !empty($this->request->data['Contact'][0]['region_id'])) {
                    $citiesAll = $this->Student->City->find(
                        'list',
                        array(
                            'conditions' => array(
                                'City.region_id' => $this->request->data['Contact'][0]['region_id'],
                                'City.active' => 1
                            )
                        )
                    );
                }

                $contacts = $this->Student->Contact->find(
                    'list',
                    array('conditions' => array('Contact.student_id' => $this->student_id))
                );
                $users = $this->Student->User->find(
                    'list',
                    array('conditions' => array('User.username' => $studentDetail['Student']['studentnumber']))
                );
                $programs = $this->Student->Program->find(
                    'list',
                    array('conditions' => array('Program.id' => $studentDetail['Student']['program_id']))
                );
                $programTypes = $this->Student->ProgramType->find(
                    'list',
                    array('conditions' => array('ProgramType.id' => $studentDetail['Student']['program_type_id']))
                );

                $studentDetail['Student']['country_id'] = $country_id_of_region;

                $this->set(
                    compact(
                        'studentDetail',
                        'contacts',
                        'users',
                        'colleges',
                        'departments',
                        'programs',
                        'programTypes',
                        'countries',
                        'regions',
                        'zones',
                        'woredas',
                        'cities',
                        'regionsAll',
                        'zonesAll',
                        'woredasAll',
                        'citiesAll',
                        'foriegn_student',
                        'require_update',
                        'require_update_fields',
                        'student_admission_year',
                        'maximum_estimated_graduation_year_limit'
                    )
                );
            } else {
                if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
                    $this->Flash->info('You can edit student profile on this page.');
                    $this->redirect('/edit', $student_id);
                } else {
                    $this->Flash->warning('You are not allowed to edit or view any student profile.');
                    $this->redirect('/');
                }
            }
        }
    }

    public function moveBatchStudentToDepartment()
    {

        if (!empty($this->request->data) && !empty($this->request->data['moveSelectedSection'])) {
            $selectedSections = array();
            $done = 0;
            $targetDepartmentDetail = $this->Student->Department->find(
                'first',
                array(
                    'conditions' => array('Department.id' => $this->request->data['AcceptedStudent']['target_department_id']),
                    'recursive' => -1
                )
            );

            $sourceDepartmentDetail = $this->Student->Department->find(
                'first',
                array(
                    'conditions' => array('Department.id' => $this->request->data['AcceptedStudent']['department_id']),
                    'recursive' => -1
                )
            );


            foreach ($this->request->data['AcceptedStudent']['selected_section'] as $k => $secId) {
                if ($secId) {
                    //$selectedSections[$secId]=$secId;
                    $secDetail = ClassRegistry::init('Section')->find(
                        'first',
                        array('conditions' => array('Section.id' => $secId), 'contain' => array('YearLevel'))
                    );
                    $yearLevelLists = ClassRegistry::init('YearLevel')->find(
                        'all',
                        array(
                            'conditions' => array(
                                'YearLevel.department_id' => $secDetail['Section']['department_id'],
                                'YearLevel.id !=' => $secDetail['Section']['year_level_id']
                            ),
                            'recursive' => -1
                        )
                    );

                    $studentListsInTheSection = ClassRegistry::init('StudentsSection')->find(
                        'list',
                        array(
                            'conditions' => array('StudentsSection.section_id' => $secId),
                            'fields' => array('student_id', 'student_id')
                        )
                    );
                    $acceptedStudentsList = $this->Student->find(
                        'list',
                        array(
                            'conditions' => array('Student.id' => $studentListsInTheSection),
                            'fields' => array('accepted_student_id', 'accepted_student_id')
                        )
                    );
                    $curriculums = $this->Student->find('all', array(
                        'conditions' => array('Student.id' => $studentListsInTheSection),
                        'group' => array('Student.curriculum_id'),
                        'recursive' => -1,
                        'fields' => array(
                            'Student.curriculum_id',
                            'count(Student.curriculum_id) as total',

                        )
                    ));
                    $batchCurriculumC = 0;
                    $batchCurriculum = 0;
                    foreach ($curriculums as $ck => $cv) {
                        if ($cv[0]['total'] > $batchCurriculumC) {
                            $batchCurriculumC = $cv[0]['total'];
                            $batchCurriculum = $cv['Student']['curriculum_id'];
                        }
                    }

                    if (isset($studentListsInTheSection) && !empty($studentListsInTheSection)) {
                        $sectionLists = array();
                        $sectionLists[] = $secDetail;
                        $sectAcademicYear = $secDetail['Section']['academicyear'];
                        foreach ($yearLevelLists as $yk => $yv) {
                            $nextAcademicYear = ClassRegistry::init('StudentExamStatus')->getNextSemster(
                                $sectAcademicYear
                            );
                            $secDetailIn = ClassRegistry::init('Section')->find(
                                'first',
                                array(
                                    'conditions' => array(
                                        'Section.year_level_id' => $yv['YearLevel']['id'],
                                        'Section.department_id' => $secDetail['Section']['department_id'],
                                        'Section.program_id' => $secDetail['Section']['program_id'],
                                        'Section.program_type_id' => $secDetail['Section']['program_type_id'],
                                        'Section.academicyear' => $nextAcademicYear['academic_year'],
                                        'Section.id in (select section_id from students_sections where student_id in (' . implode(
                                            ', ',
                                            $studentListsInTheSection
                                        ) . '))'
                                    ),
                                    'contain' => array('YearLevel')
                                )
                            );
                            if (isset($secDetailIn) && !empty($secDetailIn)) {
                                $sectionLists[] = $secDetailIn;
                                $sectAcademicYear = $secDetailIn['Section']['academicyear'];
                            }
                        }
                        //update each data accordingly
                        foreach ($sectionLists as $sk => $sv) {
                            $targetSectionYearLevel = ClassRegistry::init('YearLevel')->find(
                                'first',
                                array(
                                    'conditions' => array(
                                        'YearLevel.name' => $sv['YearLevel']['name'],
                                        'YearLevel.department_id' => $this->request->data['AcceptedStudent']['target_department_id']
                                    ),
                                    'recursive' => -1
                                )
                            );
                            $countSectionStudent = ClassRegistry::init('StudentsSection')->find(
                                'count',
                                array('conditions' => array('StudentsSection.section_id' => $sv['Section']['id']))
                            );

                            if ($countSectionStudent > 0 && isset($targetSectionYearLevel) && !empty($targetSectionYearLevel)) {
                                //update section
                                ClassRegistry::init('Section')->updateAll(array(
                                    'Section.department_id' => $targetSectionYearLevel['YearLevel']['department_id'],
                                    'Section.year_level_id' => $targetSectionYearLevel['YearLevel']['id']
                                ), array('Section.id' => $sv['Section']['id']));
                                // update published courses
                                ClassRegistry::init('PublishedCourse')->updateAll(array(
                                    'PublishedCourse.department_id' => $targetSectionYearLevel['YearLevel']['department_id'],
                                    'PublishedCourse.year_level_id' => $targetSectionYearLevel['YearLevel']['id']
                                ), array(
                                    'PublishedCourse.section_id' => $sv['Section']['id'],
                                    'PublishedCourse.year_level_id' => $sv['Section']['year_level_id']
                                ));
                                // update registration
                                ClassRegistry::init('CourseRegistration')->updateAll(
                                    array('CourseRegistration.year_level_id' => $targetSectionYearLevel['YearLevel']['id']),
                                    array(
                                        'CourseRegistration.section_id' => $sv['Section']['id'],
                                        'CourseRegistration.year_level_id' => $sv['Section']['year_level_id']
                                    )
                                );

                                // update course adds
                                ClassRegistry::init('CourseAdd')->updateAll(
                                    array('CourseAdd.year_level_id' => $targetSectionYearLevel['YearLevel']['id']),
                                    array(
                                        'CourseAdd.published_course_id in (select id from published_courses where section_id=' . $sv['Section']['id'] . ' and year_level_id=' . $sv['Section']['year_level_id'] . ')',
                                        'CourseAdd.year_level_id' => $sv['Section']['year_level_id']
                                    )
                                );
                                //update curriculums if the number of students attached only those in this batch
                                if ($batchCurriculum) {
                                    ClassRegistry::init('Curriculum')->updateAll(
                                        array('Curriculum.department_id' => $targetDepartmentDetail['Department']['id']),
                                        array(
                                            'Curriculum.id' => $batchCurriculum,
                                            'Curriculum.department_id' => $sourceDepartmentDetail['Department']['id']
                                        )
                                    );
                                    ClassRegistry::init('Course')->updateAll(array(
                                        'Course.department_id' => $targetDepartmentDetail['Department']['id'],
                                        'Course.year_level_id' => $targetSectionYearLevel['YearLevel']['id']

                                    ), array(
                                        'Course.curriculum_id' => $batchCurriculum,
                                        'Course.year_level_id' => $sv['Section']['year_level_id']
                                    ));
                                }


                                $done++;
                            }
                        }
                    }
                    //update admitted student, and accepted student
                    if ($done) {
                        //targetDepartmentDetail sourceDepartmentDetail
                        ClassRegistry::init('AcceptedStudent')->updateAll(array(
                            'AcceptedStudent.department_id' => $targetDepartmentDetail['Department']['id'],
                            'AcceptedStudent.college_id' => $targetDepartmentDetail['Department']['college_id']
                        ), array(
                            'AcceptedStudent.id' => $acceptedStudentsList,
                            'AcceptedStudent.department_id' => $sourceDepartmentDetail['Department']['id']
                        ));

                        // sourceDepartmentDetail
                        ClassRegistry::init('Student')->updateAll(array(
                            'Student.department_id' => $targetDepartmentDetail['Department']['id'],
                            'Student.college_id' => $targetDepartmentDetail['Department']['college_id']
                        ), array(
                            'Student.id' => $studentListsInTheSection,
                            'Student.department_id' => $sourceDepartmentDetail['Department']['id']
                        ));
                    }
                }
            }

            if ($done) {
                $this->Session->setFlash(
                    '<span></span> ' . __(
                        'The selected section students has successfully moved from ' . $sourceDepartmentDetail['Department']['name'] . ' department to ' . $targetDepartmentDetail['Department']['name'] . ' department.'
                    ),
                    'default',
                    array('class' => 'success-box success-message')
                );
            } else {
                $this->Session->setFlash(
                    '<span></span> ' . __('No section is selected to move the students to the target department. '),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
        }
        if (!empty($this->request->data) && !empty($this->request->data['getacceptedstudent'])) {
            // do validation
            $everythingfine = false;
            switch ($this->request->data) {
                case empty($this->request->data['AcceptedStudent']['academicyear']):
                    $this->Session->setFlash(
                        '<span></span> ' . __('Please select the academic year of the batch admitted.'),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['AcceptedStudent']['department_id']):
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'Please select the current student department you want to transfer to target department. '
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['AcceptedStudent']['target_department_id']):
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'Please select the target student department you want to transfer the batch. '
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['AcceptedStudent']['program_id']):
                    $this->Session->setFlash(
                        '<span></span> ' . __('Please select the program you want to  transfer. '),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;
                case empty($this->request->data['AcceptedStudent']['program_type_id']):
                    $this->Session->setFlash(
                        '<span></span> ' . __('Please select the program type you want to transfer. '),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;


                case $this->request->data['AcceptedStudent']['department_id'] == $this->request->data['AcceptedStudent']['target_department_id']:
                    $this->Session->setFlash(
                        '<span></span> ' . __(
                            'You have selected the same department for moving, please select a different target department. '
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    break;


                default:
                    $everythingfine = true;
            }

            if ($everythingfine) {
                $acceptedStudent = $this->Student->AcceptedStudent->find(
                    'list',
                    array(
                        'conditions' => array(
                            'AcceptedStudent.department_id' => $this->request->data['AcceptedStudent']['department_id'],
                            'AcceptedStudent.program_type_id' => $this->request->data['AcceptedStudent']['program_type_id'],
                            'AcceptedStudent.program_id' => $this->request->data['AcceptedStudent']['program_id'],
                            'AcceptedStudent.academicyear' => $this->request->data['AcceptedStudent']['academicyear']
                        ),
                        'recursive' => -1,
                        'field' => array('id', 'id')
                    )
                );
                $admittedStudent = $this->Student->find(
                    'list',
                    array(
                        'conditions' => array(
                            'Student.accepted_student_id' => $acceptedStudent,

                            'Student.id not in (select student_id from course_exemptions)',
                        ),
                        'recursive' => -1
                    )
                );
                $gradutingCount = $this->Student->SenateList->find(
                    'count',
                    array(
                        'conditions' => array('SenateList.student_id' => $admittedStudent),
                        'fields' => array('Student.id', 'Student.id')
                    )
                );

                $gradutingCount = $this->Student->SenateList->find(
                    'count',
                    array('conditions' => array('SenateList.student_id' => $admittedStudent))
                );

                if ($gradutingCount == 0 && isset($acceptedStudent) && !empty($acceptedStudent)) {
                    $yearLevelId = ClassRegistry::init('YearLevel')->find(
                        'first',
                        array(
                            'conditions' => array(
                                'YearLevel.department_id' => $this->request->data['AcceptedStudent']['department_id'],
                                'YearLevel.name' => '1st'
                            ),
                            'recursive' => -1
                        )
                    );

                    $sectionLists = ClassRegistry::init('Section')->find('all', array(
                        'conditions' => array(

                            'Section.department_id' => $this->request->data['AcceptedStudent']['department_id'],
                            'Section.year_level_id' => $yearLevelId['YearLevel']['id'],

                            'Section.program_id' => $this->request->data['AcceptedStudent']['program_id'],
                            'Section.academicyear' => $this->request->data['AcceptedStudent']['academicyear'],

                            'Section.program_type_id' => $this->request->data['AcceptedStudent']['program_type_id'],

                            'Section.id in (select section_id from students_sections where student_id in (' . implode(
                                ',',
                                $admittedStudent
                            ) . '))'
                        ),
                        'contain' => array('YearLevel'),
                        'order' => array('Section.academicyear asc')
                    ));

                    $this->set(compact('sectionLists'));
                } else {
                    if ($gradutingCount > 0) {
                        $this->Session->setFlash(
                            '<span></span> ' . __(
                                'Some students have graduated in selected section so not possible to move to other department. '
                            ),
                            'default',
                            array('class' => 'error-box error-message')
                        );
                    }
                }
            }
        }
        $acyear_list = $this->AcademicYear->academicYearInArray(date('Y') - 7, date('Y') - 1);
        $colleges = $this->Student->College->find('list');
        $departments = $this->Student->Department->find('list');
        $programs = $this->Student->Program->find('list');
        $programTypes = $this->Student->ProgramType->find('list');
        $this->set(compact('colleges', 'departments', 'programs', 'programTypes', 'acyear_list'));
    }

    public function ajaxUpdate()
    {

        //Step 1. Update the value in the database
        $value = $this->request->data['update_value']; //new value to save
        $field = $this->request->data['element_id'];
        $this->Student->id = $this->student_id;

        if (!$this->Student->saveField($field, $value, true)) { // Update the field
            $this->set('error', true);
        }

        $student = $this->Student->read(null, $this->student_id);

        //Step 2. Get the display value for the field if the field is a foreign key
        // See if field to be updated is a foreign key and set the display value
        if (substr($field, -3) == '_id') {
            // Chop off the "_id"
            $new_field = substr($field, 0, strlen($field) - 3);

            // Camelize the result to get the Model name
            $model_name = Inflector::camelize($new_field);

            // See if the model has a display name other than default "name";
            if (!empty($this->$model_name->display_field)) {
                $display_field = $this->$model_name->display_field;
            } else {
                $display_field = 'name';
            }

            // Get the display value for the id
            $value = $this->$model_name->field($display_field, array('id' => $value));
        }

        //Step 3. Set the view variable and render the view.
        $this->set('value', $value);
        $this->beforeRender();
        $this->layout = 'ajax';
    }

    public function getCourseRegisteredAndAdd($student_id = "")
    {

        $this->layout = "ajax";
        $published_courses = array();

        if ($student_id != "") {
            $published_courses = $this->Student->getStudentRegisteredAndAddCourses($student_id);
        }

        $this->set(compact('published_courses'));
    }

    public function getPossibleSupRegisteredAndAdd($student_id = "")
    {

        $this->layout = "ajax";
        $published_courses = array();

        if ($student_id != "") {
            $published_courses = $this->Student->getPossibleStudentRegisteredAndAddCoursesForSup($student_id);
        }

        $this->set(compact('published_courses'));
    }


    /// Web services to access students from warehouse system

    public function studentLists($student_id = null)
    {

        $this->Student->bindModel(
            array('hasMany' => array('StudentsSection' => array('conditions' => array('StudentsSection.archive' => 0))))
        );
        if ($student_id) {
            $students = $this->Student->find('all', array(
                'conditions' => array(
                    'Student.id' => $student_id,
                    'Student.id NOT IN (select  student_id from graduate_lists)'
                ),
                'fields' => array('id', 'studentnumber', 'full_name', 'department_id'),
                'contain' => array('StudentsSection')
            ));
        } else {
            $students = $this->Student->find(
                'all',
                array(
                    'conditions' => array('Student.id NOT IN (select  student_id from graduate_lists)'),
                    'fields' => array('id', 'studentnumber', 'full_name', 'department_id'),
                    'contain' => array('StudentsSection')
                )
            );
        }


        $sections = $this->Student->Section->find(
            'all',
            array(

                'conditions' => array(

                    'Section.archive' => 0
                ),

                'contain' => array('Program' => array('id', 'name'), 'ProgramType' => array('id', 'name'))
            )
        );


        $colleges = $this->Student->College->find('all', array(
            'fields' => array('College.id', 'College.name'),
            'contain' => array()
        ));
        $departments = $this->Student->Department->find('all', array(
            'fields' => array('Department.id', 'Department.name', 'Department.college_id'),
            'contain' => array()
        ));
        $this->set(compact('students', 'sections', 'colleges', 'departments'));
    }

    public function manageStudentMedicalCardNumber()
    {

        if (isset($this->request->data['search'])) {
            $studentnumber = $this->request->data['Student']['studentnumber'];
            if (!empty($studentnumber)) {
                $students = $this->Student->get_student_details_for_health($studentnumber);
                if (empty($students)) {
                    $this->Session->setFlash(
                        '<span></span>' . __(
                            'There is not student in this ID. Please provide correct student id (format example. Reg/453/88).'
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                } else {
                    $this->set(compact('students'));
                }
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __('Please provide student ID (format example. Reg/453/88).'),
                    'default',
                    array('class' => 'info-box info-message')
                );
            }
        }

        if (isset($this->request->data['submit'])) {
            $this->Student->id = $this->request->data['Student']['id'];
            if ($this->Student->saveField('card_number', $this->request->data['Student']['card_number'], true)) {
                $this->Session->setFlash(
                    '<span></span>' . __(' The card number has been saved.'),
                    'default',
                    array('class' => 'success-box success-message')
                );
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __('The card number could not be saved. Please, try again.'),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
            $students = $this->Student->get_student_details_for_health(
                $this->request->data['Student']['studentnumber']
            );
            $this->set(compact('students'));
        }
    }

    public function studentAcademicProfile($student_id = null)
    {

        $academicYR = $this->AcademicYear->currentAcademicYear();
        $isTheStudentDismissed = 0;
        $isTheStudentReadmitted = 0;
        $moodleUserDetails = array();

        if ($this->role_id == ROLE_STUDENT) {

            $StudentsTable = TableRegistry::getTableLocator()->get('Students');
            $student_section_exam_status = $StudentsTable->getStudentSection($this->student_id,
                null, null);


            $otps = array();

            if (SHOW_OTP_TAB_ON_STUDENT_ACADEMIC_PROFILE_FOR_STUDENTS == 1) {

                $OtpsTable = TableRegistry::getTableLocator()->get('Otps');

                $otps = $OtpsTable->find()
                    ->where([
                        'student_id' => $this->student_id,
                        'active' => 1
                    ])
                    ->order([
                        'modified' => 'DESC',
                        'created' => 'DESC'
                    ])
                    ->all();

                if (!empty($otps)) {
                    $moodleIntegratedUser = false;

                    foreach ($otps as $key => $otp) {
                        if ($otp['Otp']['service'] == 'Elearning' && empty($otp['Otp']['portal'])) {
                            $moodleIntegratedUser = true;
                        }
                    }

                    if ($moodleIntegratedUser) {


                        $MoodleUsers = TableRegistry::getTableLocator()->get('MoodleUsers');

                        $moodleUserDetails = $MoodleUsers->find()
                            ->where([
                                'table_id' => $this->student_id,
                                'role_id' => ROLE_STUDENT
                            ])
                            ->order(['created' => 'DESC'])
                            ->first();

                    }
                }
            }

            if (isset($student_section_exam_status['Section'])) {
                if (!$student_section_exam_status['Section']['archive'] && !$student_section_exam_status['Section']['StudentsSection']['archive']) {
                    debug($student_section_exam_status['Section']['academicyear']);
                    $academicYR = $student_section_exam_status['Section']['academicyear'];
                }
            }

            if (
                isset($student_section_exam_status['StudentExamStatus']) &&
                !empty($student_section_exam_status['StudentExamStatus']) &&
                $student_section_exam_status['StudentExamStatus']['academic_status_id']
                == DISMISSED_ACADEMIC_STATUS_ID
            ) {
                $isTheStudentDismissed = 1;

                $studentExamStatusTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');
                $possibleReadmissionYears = $studentExamStatusTable->getAcademicYearRange(
                    $student_section_exam_status['StudentExamStatus']['academic_year'],
                    $this->AcademicYear->currentAcademicYear()
                );

                $readmissionTable = TableRegistry::getTableLocator()->get('Readmissions');
                $readmitted = $readmissionTable->find()
                    ->where([
                        'student_id' => $this->student_id,
                        'registrar_approval' => 1,
                        'academic_commision_approval' => 1,
                        'academic_year IN' => $possibleReadmissionYears
                    ])
                    ->order([
                        'academic_year' => 'DESC',
                        'semester' => 'DESC',
                        'modified' => 'DESC'
                    ])
                    ->first();

                if (!empty($readmitted)) {
                    $lastReadmittedAcademicYear = $readmitted->academic_year;
                    $lastReadmittedSemester = $readmitted->semester;
                    $lastReadmittedDate = $readmitted->registrar_approval_date;
                    $isTheStudentReadmitted = 1;

                    $possibleAcademicYears = $studentExamStatusTable->getAcademicYearRange(
                        $lastReadmittedAcademicYear,
                        $academicYR
                    );

                    $this->set(compact('possibleAcademicYears'));
                }
            }
            // Set dismissal and readmission flags
            $this->set('isTheStudentDismissed', $isTheStudentDismissed ?? 0);
            $this->set('isTheStudentReadmitted', $isTheStudentReadmitted ?? 0);
            // Get academic profile and sections
            $student_academic_profile = $this->Students->getStudentRegisteredAddDropCurriculumResult($this->student_id, $academicYR);
            $sectionTable = TableRegistry::getTableLocator()->get('Sections');
            $studentAttendedSections = $sectionTable->getStudentSectionHistory($this->student_id);

            $this->set(compact(
                'student_academic_profile',
                'studentAttendedSections',
                'student_section_exam_status',
                'otps',
                'moodleUserDetails',
                'academicYR'
            ));
            // Profile completion check
            $authUser = $this->getRequest()->getSession()->read('Auth.User');
            $controller = $this->getRequest()->getParam('controller');
            $action = $this->getRequest()->getParam('action');

            if (
                $authUser['role_id'] == ROLE_STUDENT &&
                !empty($authUser['id']) &&
                strcasecmp($action, 'profile') !== 0 &&
                (TableRegistry::getTableLocator()->get('StudentStatusPatterns')->isEligibleForExitExam($this->student_id)
                    || FORCE_ALL_STUDENTS_TO_FILL_BASIC_PROFILE == 1) &&
                strcasecmp($controller, 'Users') !== 0 &&
                strcasecmp($action, 'changePwd') !== 0
            ) {
                $statusPatternTable = TableRegistry::getTableLocator()->get('StudentStatusPatterns');
                if (!$statusPatternTable->completedFillingProfileInfomation($this->student_id)) {
                    $this->Flash->warning(
                        'Dear ' . $authUser['first_name'] . ', you are required to complete your basic profile before proceeding. If you encounter an error or need help, please contact the registrar officer assigned to your department.'
                    );
                    return $this->redirect(['controller' => 'Students', 'action' => 'profile']);
                }
            }
        } else {
            $check_id_is_valid = 0;
            if (!empty($student_id) && is_numeric($student_id)) {
                $student_id = trim($student_id);
                if ($this->role_id == ROLE_REGISTRAR && $this->Auth->user('is_admin') == 0) {
                    if (!empty($this->department_ids)) {
                        $check_id_is_valid = $this->Student->find('count', array(
                            'conditions' => array(
                                'Student.id' => $student_id,
                                'Student.program_type_id' => $this->program_type_ids,
                                'Student.program_id' => $this->program_ids,
                                'Student.department_id' => $this->department_ids
                            )
                        ));
                    } else {
                        if (!empty($this->college_ids)) {
                            $check_id_is_valid = $this->Student->find('count', array(
                                'conditions' => array(
                                    'Student.id' => $student_id,
                                    'Student.program_type_id' => $this->program_type_ids,
                                    'Student.program_id' => $this->program_ids,
                                    'Student.college_id' => $this->college_ids
                                )
                            ));
                        }
                    }
                } else {
                    if ($this->role_id == ROLE_DEPARTMENT) {
                        $check_id_is_valid = $this->Student->find(
                            'count',
                            array(
                                'conditions' => array(
                                    'Student.id' => $student_id,
                                    'Student.department_id' => $this->department_ids,
                                )
                            )
                        );
                    } else {
                        if ($this->role_id == ROLE_COLLEGE) {
                            $check_id_is_valid = $this->Student->find(
                                'count',
                                array(
                                    'conditions' => array(
                                        'Student.id' => $student_id,
                                        'Student.college_id' => $this->college_ids
                                    ),
                                    'recursive' => -1
                                )
                            );
                        } else {
                            if ($this->role_id == ROLE_SYSADMIN || ($this->role_id == ROLE_REGISTRAR && $this->Auth->user(
                                        'is_admin'
                                    ) == 1)) {
                                $check_id_is_valid = $this->Student->find(
                                    'count',
                                    array('conditions' => array('Student.id' => $student_id))
                                );
                            }
                        }
                    }
                }
            }

            if (isset($check_id_is_valid) && $check_id_is_valid > 0) {
                $otps = array();

                if (SHOW_OTP_TAB_ON_STUDENT_ACADEMIC_PROFILE_FOR_STUDENTS == 1) {
                    $otps = $this->Student->Otp->find('all', array(
                        'conditions' => array(
                            'Otp.student_id' => $student_id,
                            'Otp.active' => 1
                        ),
                        'contain' => array(),
                        'order' => array('Otp.modified' => 'DESC', 'Otp.created' => 'DESC')
                    ));

                    if (!empty($otps)) {
                        $moodleIntegratedUser = false;

                        foreach ($otps as $key => $otp) {
                            if ($otp['Otp']['service'] == 'Elearning' && empty($otp['Otp']['portal'])) {
                                $moodleIntegratedUser = true;
                            }
                        }

                        if ($moodleIntegratedUser) {
                            $moodleUserDetails = ClassRegistry::init('MoodleUser')->find('first', array(
                                'conditions' => array(
                                    'MoodleUser.table_id' => $student_id,
                                    'MoodleUser.role_id' => ROLE_STUDENT
                                ),
                                'contain' => array(),
                                'order' => array('MoodleUser.created' => 'DESC')
                            ));
                            debug($moodleUserDetails);
                        }
                    }
                }

                $student_section_exam_status = ClassRegistry::init('Student')->get_student_section(
                    $student_id,
                    null,
                    null
                );

                if (isset($student_section_exam_status['Section'])) {
                    if (!$student_section_exam_status['Section']['archive'] && !$student_section_exam_status['Section']['StudentsSection']['archive']) {
                        debug($student_section_exam_status['Section']['academicyear']);
                        $academicYR = $student_section_exam_status['Section']['academicyear'];
                    }
                }

                if (isset($student_section_exam_status['StudentExamStatus']) && !empty($student_section_exam_status['StudentExamStatus']) && $student_section_exam_status['StudentExamStatus']['academic_status_id'] == DISMISSED_ACADEMIC_STATUS_ID) {
                    $isTheStudentDismissed = 1;

                    $possibleReadmissionYears = ClassRegistry::init('StudentExamStatus')->getAcademicYearRange(
                        $student_section_exam_status['StudentExamStatus']['academic_year'],
                        $this->AcademicYear->currentAcademicYear()
                    );

                    $readmitted = ClassRegistry::init('Readmission')->find('first', array(
                        'conditions' => array(
                            'Readmission.student_id' => $student_id,
                            'Readmission.registrar_approval' => 1,
                            'Readmission.academic_commision_approval' => 1,
                            'Readmission.academic_year' => $possibleReadmissionYears,

                        ),
                        'order' => array(
                            'Readmission.academic_year' => 'DESC',
                            'Readmission.semester' => 'DESC',
                            'Readmission.modified' => 'DESC'
                        ),
                        'recursive' => -1,
                    ));

                    if (count($readmitted)) {
                        $lastReadmittedAcademicYear = $readmitted['Readmission']['academic_year'];
                        $lastReadmittedSemester = $readmitted['Readmission']['semester'];
                        $lastReadmittedDate = $readmitted['Readmission']['registrar_approval_date'];

                        debug($lastReadmittedAcademicYear);

                        $isTheStudentReadmitted = 1;
                        $possibleAcademicYears = ClassRegistry::init('StudentExamStatus')->getAcademicYearRange(
                            $lastReadmittedAcademicYear,
                            $academicYR
                        );
                        $this->set(compact('possibleAcademicYears'));
                    }

                    debug($isTheStudentReadmitted);
                }

                $this->set('isTheStudentDismissed', $isTheStudentDismissed);
                $this->set('isTheStudentReadmitted', $isTheStudentReadmitted);

                $isStudentEverReadmitted = ClassRegistry::init('Readmission')->find('count', array(
                    'conditions' => array(
                        'Readmission.student_id' => $student_id,
                        'Readmission.registrar_approval' => 1,
                        'Readmission.academic_commision_approval' => 1,
                    )
                ));

                $student_academic_profile = $this->Student->getStudentRegisteredAddDropCurriculumResult(
                    $student_id,
                    $academicYR
                );
                $studentAttendedSections = ClassRegistry::init('Section')->getStudentSectionHistory($student_id);

                $this->set(
                    compact(
                        'student_academic_profile',
                        'studentAttendedSections',
                        'student_section_exam_status',
                        'otps',
                        'moodleUserDetails',
                        'isStudentEverReadmitted'
                    )
                );
                $this->set('academicYR', $academicYR);
            }
        }
        echo '<pre>';
        print_r($this->request->getData());
        echo '</pre>';

        if ($this->request->is('post')  && $this->request->getData('continue')) {

            //debug($this->request->data);
            if (!empty($this->request->data['Student']['studentID'])) {
                $student_id_valid = $this->Student->find(
                    'count',
                    array(
                        'conditions' => array(
                            'Student.studentnumber' => trim(
                                $this->request->getData('Student.studentID')
                            )
                        ),
                        'recursive' => -1
                    )
                );
                if ($this->role_id == ROLE_REGISTRAR && $this->Auth->user('is_admin') == 0) {
                    if (!empty($this->department_ids)) {
                        $check_id_is_valid = $this->Student->find('count', array(
                            'conditions' => array(
                                'Student.studentnumber' => trim($this->request->data['Student']['studentID']),
                                'Student.program_type_id' => $this->program_type_ids,
                                'Student.program_id' => $this->program_ids,
                                'Student.department_id' => $this->department_ids
                            ),
                            'recursive' => -1
                        ));
                    } else {
                        if (!empty($this->college_ids)) {
                            $check_id_is_valid = $this->Student->find('count', array(
                                'conditions' => array(
                                    'Student.studentnumber' => trim($this->request->data['Student']['studentID']),
                                    'Student.program_type_id' => $this->program_type_ids,
                                    'Student.program_id' => $this->program_ids,
                                    'Student.college_id' => $this->college_ids
                                ),
                                'recursive' => -1
                            ));
                        }
                    }
                } else {
                    if ($this->role_id == ROLE_DEPARTMENT) {
                        $check_id_is_valid = $this->Student->find(
                            'count',
                            array(
                                'conditions' => array(
                                    'Student.studentnumber' => trim(
                                        $this->request->data['Student']['studentID']
                                    ),
                                    'Student.department_id' => $this->department_ids
                                ),
                                'recursive' => -1
                            )
                        );
                    } else {
                        if ($this->role_id == ROLE_COLLEGE) {
                            $check_id_is_valid = $this->Student->find(
                                'count',
                                array(
                                    'conditions' => array(
                                        'Student.studentnumber' => trim(
                                            $this->request->data['Student']['studentID']
                                        ),
                                        'Student.college_id' => $this->college_ids
                                    ),
                                    'recursive' => -1
                                )
                            );
                        } else {
                            if ($this->role_id == ROLE_SYSADMIN || ($this->role_id == ROLE_REGISTRAR && $this->Auth->user(
                                        'is_admin'
                                    ) == 1)) {
                                $check_id_is_valid = $this->Student->find(
                                    'count',
                                    array(
                                        'conditions' => array(
                                            'Student.studentnumber' => trim(
                                                $this->request->data['Student']['studentID']
                                            )
                                        ),
                                        'recursive' => -1
                                    )
                                );
                            }
                        }
                    }
                }

                $studentIDs = 1;

                if ($student_id_valid == 0) {
                    $this->Flash->warning('The provided Student ID is not valid.');
                } else {
                    if ($student_id_valid > 0 && $check_id_is_valid > 0) {
                        $everythingfine = true;

                        $student_id = $this->Student->field(
                            'id',
                            array('studentnumber' => trim($this->request->data['Student']['studentID']))
                        );

                        $otps = array();

                        if (SHOW_OTP_TAB_ON_STUDENT_ACADEMIC_PROFILE_FOR_STUDENTS == 1) {
                            $otps = $this->Student->Otp->find('all', array(
                                'conditions' => array(
                                    'Otp.student_id' => $student_id,
                                    'Otp.active' => 1
                                ),
                                'contain' => array(),
                                'order' => array('Otp.modified' => 'DESC', 'Otp.created' => 'DESC')
                            ));

                            if (!empty($otps)) {
                                $moodleIntegratedUser = false;

                                foreach ($otps as $key => $otp) {
                                    if ($otp['Otp']['service'] == 'Elearning' && empty($otp['Otp']['portal'])) {
                                        $moodleIntegratedUser = true;
                                    }
                                }

                                if ($moodleIntegratedUser) {
                                    $moodleUserDetails = ClassRegistry::init('MoodleUser')->find('first', array(
                                        'conditions' => array(
                                            'MoodleUser.table_id' => $student_id,
                                            'MoodleUser.role_id' => ROLE_STUDENT
                                        ),
                                        'contain' => array(),
                                        'order' => array('MoodleUser.created' => 'DESC')
                                    ));
                                    debug($moodleUserDetails);
                                }
                            }
                        }

                        $student_section_exam_status = ClassRegistry::init('Student')->get_student_section(
                            $student_id,
                            null,
                            null
                        );

                        if (isset($student_section_exam_status['Section'])) {
                            if (!$student_section_exam_status['Section']['archive'] && !$student_section_exam_status['Section']['StudentsSection']['archive']) {
                                debug($student_section_exam_status['Section']['academicyear']);
                                $academicYR = $student_section_exam_status['Section']['academicyear'];
                            }
                        }

                        if (isset($student_section_exam_status['StudentExamStatus']) && !empty($student_section_exam_status['StudentExamStatus']) && $student_section_exam_status['StudentExamStatus']['academic_status_id'] == DISMISSED_ACADEMIC_STATUS_ID) {
                            $isTheStudentDismissed = 1;

                            $possibleReadmissionYears = ClassRegistry::init('StudentExamStatus')->getAcademicYearRange(
                                $student_section_exam_status['StudentExamStatus']['academic_year'],
                                $this->AcademicYear->current_academicyear()
                            );

                            $readmitted = ClassRegistry::init('Readmission')->find('first', array(
                                'conditions' => array(
                                    'Readmission.student_id' => $student_id,
                                    'Readmission.registrar_approval' => 1,
                                    'Readmission.academic_commision_approval' => 1,
                                    'Readmission.academic_year' => $possibleReadmissionYears,

                                ),
                                'order' => array(
                                    'Readmission.academic_year' => 'DESC',
                                    'Readmission.semester' => 'DESC',
                                    'Readmission.modified' => 'DESC'
                                ),
                                'recursive' => -1,
                            ));

                            if (count($readmitted)) {
                                $lastReadmittedAcademicYear = $readmitted['Readmission']['academic_year'];
                                $lastReadmittedSemester = $readmitted['Readmission']['semester'];
                                $lastReadmittedDate = $readmitted['Readmission']['registrar_approval_date'];

                                debug($lastReadmittedAcademicYear);

                                $isTheStudentReadmitted = 1;
                                $possibleAcademicYears = ClassRegistry::init('StudentExamStatus')->getAcademicYearRange(
                                    $lastReadmittedAcademicYear,
                                    $academicYR
                                );
                                $this->set(compact('possibleAcademicYears'));
                            }

                            debug($isTheStudentReadmitted);
                        }

                        $this->set('isTheStudentDismissed', $isTheStudentDismissed);
                        $this->set('isTheStudentReadmitted', $isTheStudentReadmitted);

                        $isStudentEverReadmitted = ClassRegistry::init('Readmission')->find('count', array(
                            'conditions' => array(
                                'Readmission.student_id' => $student_id,
                                'Readmission.registrar_approval' => 1,
                                'Readmission.academic_commision_approval' => 1,
                            )
                        ));

                        $student_academic_profile = $this->Student->getStudentRegisteredAddDropCurriculumResult(
                            $student_id,
                            $academicYR
                        );
                        $studentAttendedSections = ClassRegistry::init('Section')->getStudentSectionHistory(
                            $student_id
                        );
                        $this->set(
                            compact(
                                'student_academic_profile',
                                'studentAttendedSections',
                                'student_section_exam_status',
                                'otps',
                                'moodleUserDetails',
                                'isStudentEverReadmitted'
                            )
                        );
                        $this->set('academicYR', $academicYR);
                    } else {
                        if ($check_id_is_valid == 0) {
                            $this->Flash->warning(
                                'You don\'t have the privilage to view the selected student\'s profile.'
                            );
                        } else {
                            $this->Flash->warning('The provided Student ID is not valid.');
                        }
                    }
                }
            } else {
                $this->Flash->error('Please provide Student ID to view Academic Profile.');
            }
        }
    }

    public function getModalBox($student_id = null)
    {
        $this->viewBuilder()->setLayout('ajax');

        $authUser = $this->Authentication->getIdentity();
        if (!$authUser) {
            throw new NotFoundException(__('Unauthorized'));
        }

        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $check_id_is_valid = 0;

        if ($authUser->role_id == ROLE_STUDENT) {
            $student_id = $authUser->table_id ?? null;
            $check_id_is_valid = $studentsTable->find()->where(['id' => $student_id])->count();
        } else {
            $check_id_is_valid = $studentsTable->find()->where(['id' => $student_id])->count();
        }

        $otps = [];
        $moodleUserDetails = [];

        if ($check_id_is_valid > 0) {
            $academicYR = $this->AcademicYear->currentAcademicYear();
            $isTheStudentDismissed = 0;
            $isTheStudentReadmitted = 0;

            if (SHOW_OTP_TAB_ON_STUDENT_ACADEMIC_PROFILE_FOR_STUDENTS == 1) {
                $otpsTable = TableRegistry::getTableLocator()->get('Otps');
                $otps = $otpsTable->find()
                    ->where(['student_id' => $student_id, 'active' => 1])
                    ->order(['modified' => 'DESC', 'created' => 'DESC'])
                    ->toArray();

                $moodleIntegratedUser = false;
                foreach ($otps as $otp) {
                    if ($otp->service === 'Elearning' && empty($otp->portal)) {
                        $moodleIntegratedUser = true;
                    }
                }

                if ($moodleIntegratedUser) {
                    $moodleUserTable = TableRegistry::getTableLocator()->get('MoodleUsers');
                    $moodleUserDetails = $moodleUserTable->find()
                        ->where(['table_id' => $student_id, 'role_id' => ROLE_STUDENT])
                        ->order(['created' => 'DESC'])
                        ->first();
                }
            }

            $studentExamStatusTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');
            $sectionTable = TableRegistry::getTableLocator()->get('Sections');

            $student_section_exam_status = $studentsTable->getStudentSection($student_id);

            if (!empty($student_section_exam_status['Section'])
                && !$student_section_exam_status['Section']['archive']
                && !$student_section_exam_status['Section']['StudentsSection']['archive']) {
                $academicYR = $student_section_exam_status['Section']['academicyear'];
            }

            if (!empty($student_section_exam_status['StudentExamStatus']) &&
                $student_section_exam_status['StudentExamStatus']['academic_status_id'] == DISMISSED_ACADEMIC_STATUS_ID) {
                $isTheStudentDismissed = 1;

                $possibleReadmissionYears = $studentExamStatusTable->getAcademicYearRange(
                    $student_section_exam_status['StudentExamStatus']['academic_year'],
                    $this->AcademicYear->currentAcademicYear()
                );

                $readmissionTable = TableRegistry::getTableLocator()->get('Readmissions');
                $readmitted = $readmissionTable->find()
                    ->where([
                        'student_id' => $student_id,
                        'registrar_approval' => 1,
                        'academic_commision_approval' => 1,
                        'academic_year IN' => $possibleReadmissionYears
                    ])
                    ->order(['academic_year' => 'DESC', 'semester' => 'DESC', 'modified' => 'DESC'])
                    ->first();

                if ($readmitted) {
                    $lastReadmittedAcademicYear = $readmitted->academic_year;
                    $lastReadmittedSemester = $readmitted->semester;
                    $lastReadmittedDate = $readmitted->registrar_approval_date;

                    $isTheStudentReadmitted = 1;

                    $possibleAcademicYears = $studentExamStatusTable->getAcademicYearRange(
                        $lastReadmittedAcademicYear,
                        $academicYR
                    );
                    $this->set(compact('possibleAcademicYears'));
                }
            }

            $student_academic_profile = $studentsTable->getStudentRegisteredAddDropCurriculumResult(
                $student_id,
                $academicYR
            );

            $studentAttendedSections = $sectionTable->getStudentSectionHistory($student_id);

            $this->set(compact(
                'studentAttendedSections',
                'student_academic_profile',
                'student_section_exam_status',
                'otps',
                'moodleUserDetails',
                'academicYR',
                'isTheStudentDismissed',
                'isTheStudentReadmitted'
            ));
        }
    }

    public function profileNotBuildList()
    {

        $limit = 100;
        $name = '';
        $page = '';

        $options = array();

        if (!empty($this->passedArgs)) {
            //debug($this->passedArgs);

            if (!empty($this->passedArgs['Search.limit'])) {
                $limit = $this->request->data['Search']['limit'] = $this->passedArgs['Search.limit'];
            }

            if (!empty($this->passedArgs['Search.name'])) {
                $name = str_replace('-', '/', trim($this->passedArgs['Search.name']));
                //$this->request->data['Search']['name']
            }

            if (!empty($this->passedArgs['Search.department_id'])) {
                $this->request->data['Search']['department_id'] = $this->passedArgs['Search.department_id'];
            }

            if (!empty($this->passedArgs['Search.college_id'])) {
                $this->request->data['Search']['college_id'] = $this->passedArgs['Search.college_id'];
            }

            if (isset($this->passedArgs['Search.academicyear'])) {
                $selected_academic_year = $this->request->data['Search']['academicyear'] = str_replace(
                    '-',
                    '/',
                    $this->passedArgs['Search.academicyear']
                );
            } else {
                $selected_academic_year = '';
            }

            if (isset($this->passedArgs['Search.gender'])) {
                $this->request->data['Search']['gender'] = $this->passedArgs['Search.gender'];
            }

            if (isset($this->passedArgs['Search.program_id'])) {
                $this->request->data['Search']['program_id'] = $this->passedArgs['Search.program_id'];
            }

            if (isset($this->passedArgs['Search.program_type_id'])) {
                $this->request->data['Search']['program_type_id'] = $this->passedArgs['Search.program_type_id'];
            }

            if (isset($this->passedArgs['Search.status'])) {
                $this->request->data['Search']['status'] = $this->passedArgs['Search.status'];
            }

            ////////////////////

            if (isset($this->passedArgs['page'])) {
                $page = $this->request->data['Search']['page'] = $this->passedArgs['page'];
            }

            if (isset($this->passedArgs['sort'])) {
                $this->request->data['Search']['sort'] = $this->passedArgs['sort'];
            }

            if (isset($this->passedArgs['direction'])) {
                $this->request->data['Search']['direction'] = $this->passedArgs['direction'];
            }

            ////////////////////

            $this->__init_search_index();
        }

        $this->__init_search_index();

        if (isset($this->request->data['search'])) {
            unset($this->passedArgs);
            $this->__init_clear_session_filters();
            $this->__init_search_index();
        }

        if (!empty($this->request->data)) {
            debug($this->request->data);

            if (!empty($page) && !isset($this->request->data['search'])) {
                $this->request->data['Search']['page'] = $page;
            }

            if (!empty($this->request->data['Search']['limit'])) {
                $limit = $this->request->data['Search']['limit'];
            }

            if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
                $departments = $this->Student->Department->find(
                    'list',
                    array('conditions' => array('Department.id' => $this->department_id, 'Department.active' => 1))
                );
                $options['conditions'][] = array('Student.department_id' => $this->department_id);
                $this->request->data['Search']['department_id'] = $this->department_id;
            } else {
                if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
                    $departments = array();

                    if (!$this->onlyPre) {
                        $departments = $this->Student->Department->find(
                            'list',
                            array(
                                'conditions' => array(
                                    'Department.college_id' => $this->college_ids,
                                    'Department.active' => 1
                                )
                            )
                        );
                    }

                    if (!empty($this->request->data['Search']['department_id'])) {
                        $options['conditions'][] = array('Student.department_id' => $this->request->data['Search']['department_id']);
                    } else {
                        $options['conditions'][] = array('Student.college_id' => $this->college_ids);
                    }

                    $this->request->data['Search']['college_id'] = $this->college_id;
                } else {
                    if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
                        if (!empty($this->department_ids)) {
                            $colleges = array();
                            $departments = $this->Student->Department->find(
                                'list',
                                array(
                                    'conditions' => array(
                                        'Department.id' => $this->department_ids,
                                        'Department.active' => 1
                                    )
                                )
                            );

                            if (!empty($this->request->data['Search']['department_id'])) {
                                $options['conditions'][] = array('Student.department_id' => $this->request->data['Search']['department_id']);
                            } else {
                                $options['conditions'][] = array("Student.department_id" => $this->department_ids);
                            }
                        } else {
                            if (!empty($this->college_ids)) {
                                $departments = array();
                                $colleges = $this->Student->College->find(
                                    'list',
                                    array(
                                        'conditions' => array(
                                            'College.id' => $this->college_ids,
                                            'College.active' => 1
                                        )
                                    )
                                );

                                if (!empty($this->request->data['Search']['college_id'])) {
                                    $options['conditions'][] = array(
                                        'Student.college_id' => $this->request->data['Search']['college_id'],
                                        'Student.department_id IS NULL'
                                    );
                                } else {
                                    $options['conditions'][] = array(
                                        "Student.college_id" => $this->college_ids,
                                        'Student.department_id IS NULL'
                                    );
                                }
                            }
                        }
                    } else {
                        if ($this->Session->read('Auth.User')['role_id'] == ROLE_STUDENT) {
                            unset($this->passedArgs);
                            unset($this->request->data);
                            return $this->redirect(array('action' => 'index'));
                        } else {
                            $departments = $this->Student->Department->find(
                                'list',
                                array('conditions' => array('Department.active' => 1))
                            );
                            $colleges = $this->Student->College->find(
                                'list',
                                array('conditions' => array('College.active' => 1))
                            );

                            if (!empty($this->request->data['Search']['department_id'])) {
                                $options['conditions'][] = array("Student.department_id" => $this->request->data['Search']['department_id']);
                            } else {
                                if (empty($this->request->data['Search']['department_id']) && !empty($this->request->data['Search']['college_id'])) {
                                    $departments = $this->Student->Department->find(
                                        'list',
                                        array(
                                            'conditions' => array(
                                                'Department.college_id' => $this->request->data['Search']['college_id'],
                                                'Department.active' => 1
                                            )
                                        )
                                    );
                                    $options['conditions'][] = array('Student.college_id' => $this->request->data['Search']['college_id']);
                                } else {
                                    if (!empty($departments) && !empty($colleges)) {
                                        $options['conditions'][] = array(
                                            'OR' => array(
                                                'Student.college_id' => $this->college_ids,
                                                'Student.department_id' => $this->department_ids
                                            )
                                        );
                                    } else {
                                        if (!empty($this->college_ids)) {
                                            $options['conditions'][] = array('Student.college_id' => $this->college_ids);
                                        } else {
                                            if (!empty($this->department_ids)) {
                                                $options['conditions'][] = array('Student.department_id' => $this->department_ids);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if (!empty($selected_academic_year)) {
                $options['conditions'][] = array('Student.academicyear' => $selected_academic_year);
            }

            if (!empty($this->request->data['Search']['program_id'])) {
                $options['conditions'][] = array('Student.program_id' => $this->request->data['Search']['program_id']);
            } else {
                if (empty($this->request->data['Search']['program_id']) && $this->Session->read(
                        'Auth.User'
                    )['role_id'] == ROLE_REGISTRAR) {
                    $options['conditions'][] = array('Student.program_id' => $this->program_ids);
                }
            }

            if (!empty($this->request->data['Search']['program_type_id'])) {
                $options['conditions'][] = array('Student.program_type_id' => $this->request->data['Search']['program_type_id']);
            } else {
                if (empty($this->request->data['Search']['program_type_id']) && $this->Session->read(
                        'Auth.User'
                    )['role_id'] == ROLE_REGISTRAR) {
                    $options['conditions'][] = array('Student.program_type_id' => $this->program_type_ids);
                }
            }

            if (isset($name) && !empty($name)) {
                $options['conditions'][] = array(
                    'OR' => array(
                        'Student.first_name LIKE ' => '%' . $name . '%',
                        'Student.middle_name LIKE ' => '%' . $name . '%',
                        'Student.last_name LIKE ' => '%' . $name . '%',
                        'Student.studentnumber LIKE' => $name . '%',
                    )
                );
            }

            if (isset($this->request->data['Search']['college_id']) && !empty($this->request->data['Search']['college_id']) && $this->Session->read(
                    'Auth.User'
                )['role_id'] != ROLE_REGISTRAR) {
                $departments = $this->Student->Department->find('list', array(
                    'conditions' => array(
                        'Department.college_id' => $this->request->data['Search']['college_id'],
                        'Department.active' => 1
                    )
                ));
            }

            if (isset($this->request->data['Search']['gender']) && !empty($this->request->data['Search']['gender'])) {
                $options['conditions'][] = array('Student.gender LIKE ' => $this->request->data['Search']['gender']);
            }

            if (!empty($this->request->data['Search']['status'])) {
                $options['conditions'][] = array('Student.graduated' => $this->request->data['Search']['status']);
            }
        } else {
            $not_build_for = date('Y-m-d ', strtotime("-" . DAYS_BACK_PROFILE . " day "));

            if ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE) {
                $departments = array();

                if (!$this->onlyPre) {
                    $departments = $this->Student->Department->find(
                        'list',
                        array(
                            'conditions' => array(
                                'Department.college_id' => $this->college_ids,
                                'Department.active' => 1
                            )
                        )
                    );
                }

                if (empty($departments)) {
                    $options['conditions'][] = array('Student.college_id' => $this->college_ids);
                } else {
                    $options['conditions'][] = array(
                        'OR' => array(
                            'Student.college_id' => $this->college_ids,
                            'Student.department_id' => $this->department_ids
                        )
                    );
                }

                $this->request->data['Search']['college_id'] = $this->college_id;
            } else {
                if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) {
                    $departments = $this->Student->Department->find(
                        'list',
                        array('conditions' => array('Department.id' => $this->department_ids))
                    );
                    $options['conditions'][] = array('Student.department_id' => $this->department_ids);

                    $this->request->data['Search']['department_id'] = $this->department_id;
                } else {
                    if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR) {
                        if (!empty($this->department_ids)) {
                            $colleges = array();
                            $departments = $this->Student->Department->find(
                                'list',
                                array(
                                    'conditions' => array(
                                        'Department.id' => $this->department_ids,
                                        'Department.active' => 1
                                    )
                                )
                            );
                            $options['conditions'][] = array(
                                'Student.department_id' => $this->department_ids,
                                'Student.program_id' => $this->program_ids,
                                'Student.program_type_id' => $this->program_type_ids
                            );
                        } else {
                            if (!empty($this->college_ids)) {
                                $departments = array();
                                $colleges = $this->Student->College->find(
                                    'list',
                                    array(
                                        'conditions' => array(
                                            'College.id' => $this->college_ids,
                                            'College.active' => 1
                                        )
                                    )
                                );
                                $options['conditions'][] = array(
                                    'Student.college_id' => $this->college_ids,
                                    'Student.department_id IS NULL',
                                    'Student.program_id' => $this->program_ids,
                                    'Student.program_type_id' => $this->program_type_ids
                                );
                            }
                        }
                    } else {
                        if ($this->Session->read('Auth.User')['role_id'] == ROLE_STUDENT) {
                            $options['conditions'][] = array('Student.id' => $this->student_id);
                        } else {
                            $departments = $this->Student->Department->find(
                                'list',
                                array('conditions' => array('Department.active' => 1))
                            );
                            $colleges = $this->Student->College->find(
                                'list',
                                array('conditions' => array('College.active' => 1))
                            );

                            if (!empty($departments) && !empty($colleges)) {
                                $options['conditions'][] = array(
                                    'OR' => array(
                                        'Student.department_id' => $this->department_ids,
                                        'Student.college_id' => $this->college_ids
                                    )
                                );
                            } else {
                                if (!empty($departments)) {
                                    $options['conditions'][] = array('Student.department_id' => $this->department_ids);
                                } else {
                                    if (!empty($colleges)) {
                                        $options['conditions'][] = array('Student.college_id' => $this->college_ids);
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if (!empty($options['conditions'])) {
                $options['conditions'][] = array('Student.id IS NOT NULL');
                $options['conditions'][] = array('Student.graduated = 0');
                $options['conditions'][] = array('Student.created >= ' => $not_build_for);
            }
        }

        //debug($options['conditions']);
        $students = array();

        if (!empty($options['conditions'])) {
            $options['conditions'][] = array('Student.id NOT IN (SELECT student_id FROM contacts)');

            $this->Paginator->settings = array(
                'conditions' => $options['conditions'],
                'contain' => array(
                    'Department' => array(
                        'fields' => array(
                            'Department.id',
                            'Department.name',
                            'Department.shortname',
                            'Department.college_id',
                            'Department.institution_code'
                        )
                    ),
                    'College' => array(
                        'fields' => array(
                            'College.id',
                            'College.name',
                            'College.shortname',
                            'College.institution_code',
                            'College.campus_id',
                        ),
                        'Campus' => array(
                            'id',
                            'name',
                            'campus_code'
                        )
                    ),
                    'Program' => array(
                        'fields' => array(
                            'Program.id',
                            'Program.name',
                            'Program.shortname',
                        )
                    ),
                    'AcceptedStudent' => array(
                        'fields' => array(
                            'AcceptedStudent.id'
                        )
                    ),
                    'ProgramType' => array(
                        'fields' => array(
                            'ProgramType.id',
                            'ProgramType.name',
                            'ProgramType.shortname',
                        )
                    ),
                    'Contact',
                    'Curriculum' => array(
                        'id',
                        'name',
                        'year_introduced',
                        'type_credit',
                        'english_degree_nomenclature',
                        'active'
                    ),
                    'Specialization' => array('id', 'name'),
                    'Region' => array('id', 'name', 'short'),
                    'Zone' => array('id', 'name', 'short'),
                    'Woreda' => array('id', 'name', 'code'),
                    'City' => array('id', 'name', 'short'),
                ),
                'order' => array(
                    'Student.admissionyear' => 'DESC',
                    'Student.department_id' => 'ASC',
                    'Student.program_type_id' => 'ASC',
                    'Student.studentnumber' => 'ASC',
                    'Student.first_name' => 'ASC',
                    'Student.middle_name' => 'ASC',
                    'Student.last_name' => 'ASC',
                    'Student.created' => 'DESC'
                ),
                'limit' => $limit,
                'maxLimit' => $limit,
                'recursive' => -1,
                'page' => $page
            );


            try {
                $students = $this->Paginator->paginate($this->modelClass);
                $this->set(compact('students'));
            } catch (NotFoundException $e) {
                if (!empty($this->request->data['Search'])) {
                    unset($this->request->data['Search']['page']);
                    unset($this->request->data['Search']['sort']);
                    unset($this->request->data['Search']['direction']);
                }
                if (!empty($this->request->data['Student'])) {
                    unset($this->request->data['Student']['page']);
                    unset($this->request->data['Student']['sort']);
                    unset($this->request->data['Student']['direction']);
                }
                unset($this->passedArgs);
                $this->__init_search_index();
                return $this->redirect(array('action' => 'profile_not_build_list'));
            } catch (Exception $e) {
                if (!empty($this->request->data['Search'])) {
                    unset($this->request->data['Search']['page']);
                    unset($this->request->data['Search']['sort']);
                    unset($this->request->data['Search']['direction']);
                }
                if (!empty($this->request->data['Student'])) {
                    unset($this->request->data['Student']['page']);
                    unset($this->request->data['Student']['sort']);
                    unset($this->request->data['Student']['direction']);
                }
                unset($this->passedArgs);
                $this->__init_search_index();
                return $this->redirect(array('action' => 'profile_not_build_list'));
            }

            if (!empty($students)) {
                if ($this->Session->check('students')) {
                    $this->Session->delete('students');
                }
                $this->Session->write('students', $students);
            }
        }

        if (empty($students) && !empty($options['conditions'])) {
            $this->Flash->info('No Student is found with the given search criteria.');
            $turn_off_search = false;
        } else {
            $turn_off_search = false;
            //debug($students[0]);
        }

        $this->set(compact('colleges', 'departments', /* 'students', */ 'turn_off_search', 'limit', 'name'));
    }



    public function nameChange($id = null)
    {

        if (!empty($this->request->data['Student']) && isset($this->request->data['searchStudentName'])) {
            $student_id = null;
            $everythingfine = true;

            if (empty($this->request->data['Student'])) {
                $this->Flash->error(__('Please provide the student number (ID) you want to change name.'));
                $everythingfine = false;
            }

            $department_id = null;
            $college_id = null;

            if (!empty($this->department_ids)) {
                $department_id = $this->department_ids;
            } else {
                if (!empty($this->department_id)) {
                    $department_id = $this->department_id;
                } else {
                    if ($this->role_id == ROLE_REGISTRAR) {
                        if (!empty($this->department_ids)) {
                            $department_id = $this->department_ids;
                        } else {
                            if (!empty($this->college_ids)) {
                                $college_id = $this->college_ids;
                            }
                        }
                    }
                }
            }

            if ($everythingfine) {
                if (!empty($department_id)) {
                    $check_id_is_valid = $this->Student->find('count', array(
                        'conditions' => array(
                            'Student.studentnumber LIKE ' => trim(
                                    $this->request->data['Student']['studentnumber']
                                ) . '%',
                            'Student.department_id' => $department_id
                        )
                    ));
                } else {
                    if (!empty($college_id)) {
                        $check_id_is_valid = $this->Student->find('count', array(
                            'conditions' => array(
                                'Student.studentnumber LIKE ' => trim(
                                        $this->request->data['Student']['studentnumber']
                                    ) . '%',
                                'Student.college_id' => $college_id,
                                'Student.department_id is null'
                            )
                        ));
                    }
                }

                if ($check_id_is_valid > 0) {
                    // do something if needed
                    $everythingfine = true;
                    $student_id = $this->Student->find('first', array(
                        'conditions' => array(
                            'Student.studentnumber LIKE ' => trim(
                                    $this->request->data['Student']['studentnumber']
                                ) . '%',
                            'Student.department_id' => $department_id
                        ),
                        'recursive' => -1
                    ));
                } else {
                    $everythingfine = false;
                    $this->Flash->error(
                        __(
                            'The provided student number is not valid or you don\'t have the privilage to change name to this student.'
                        )
                    );
                }
            }

            if ($everythingfine) {
                $test_data = $this->Student->find(
                    'first',
                    array('conditions' => array('Student.id' => $student_id['Student']['id']), 'recursive' => -1)
                );
                $this->request->data = $this->Student->StudentNameHistory->reformat($test_data);
            }
        }

        if (!empty($this->request->data) && isset($this->request->data['changeName'])) {
            $data = $this->Student->StudentNameHistory->reformat($this->request->data);

            $isThereChangeInFullName = true;

            if ($data['StudentNameHistory']['to_first_name'] === $data['StudentNameHistory']['from_first_name'] && $data['StudentNameHistory']['to_middle_name'] === $data['StudentNameHistory']['from_middle_name'] && $data['StudentNameHistory']['to_last_name'] === $data['StudentNameHistory']['from_last_name']) {
                $isThereChangeInFullName = false;
            }

            if ($isThereChangeInFullName) {
                if ($this->Student->StudentNameHistory->save($data)) {
                    $change['Student']['amharic_first_name'] = $data['StudentNameHistory']['to_amharic_first_name'];
                    $change['Student']['id'] = $data['StudentNameHistory']['student_id'];
                    $change['Student']['amharic_middle_name'] = $data['StudentNameHistory']['to_amharic_middle_name'];
                    $change['Student']['amharic_last_name'] = $data['StudentNameHistory']['to_amharic_last_name'];

                    $change['Student']['first_name'] = $data['StudentNameHistory']['to_first_name'];
                    $change['Student']['middle_name'] = $data['StudentNameHistory']['to_middle_name'];
                    $change['Student']['last_name'] = $data['StudentNameHistory']['to_last_name'];

                    if ($this->Student->save($change)) {
                        $this->Flash->success(__('Student name change name has been saved.'));
                        //save the changed name in student table
                        $this->redirect($this->referer());
                    } else {
                        $this->Flash->error(__('Student name change could not be saved.  Please, try again.'));
                        $this->Student->StudentNameHistory->delete($this->Student->StudentNameHistory->id);
                    }
                } else {
                    //debug($this->Student->StudentNameHistory->invalidFields());
                    $this->Flash->error(__('Student name change could not be saved. Please, try again.'));
                    $this->redirect($this->referer());
                }
            } else {
                $this->Flash->info(__('No change detected in previous and new student name. Nothing updated.'));
                $this->redirect($this->referer());
            }
        }

        if (empty($this->request->data) && !empty($id)) {
            $test_data = $this->Student->find(
                'first',
                array('conditions' => array('Student.id' => $id), 'contain' => array())
            );
            $this->request->data = $this->Student->StudentNameHistory->reformat($test_data);
        }
    }

    public function departmentIssuePassword($section_id = null)
    {

        $this->__issue_password($section_id, 0);
    }

    public function freshmanIssuePassword($section_id = null)
    {

        $this->__issue_password($section_id, 1);
    }

    private function __issue_password($section_id = null, $freshman_program = 0)
    {

        /*
			1. Retrieve list of sections based on the given search criteria
			2. Display list of sections
			3. Up on the selection of section, display list of students with check-box
			4. Prepare student password issue/reset in PDF for the selected students
		*/

        $programs = $this->Student->Section->Program->find('list');
        $program_types = $this->Student->Section->ProgramType->find('list');

        if ($freshman_program == 0) {
            $yearLevels = $this->Student->Section->YearLevel->find(
                'list',
                array('conditions' => array('YearLevel.department_id' => $this->department_id))
            );
        } else {
            $yearLevels[0] = "Pre/Freshman";
        }

        $reset_password_by_email = (ALLOW_STUDENTS_TO_RESET_PASSWORD_BY_EMAIL == 1 ? 1 : 0);

        $section_ac_years = $this->AcademicYear->academicYearInArray(
            (explode('/', $this->AcademicYear->current_academicyear())[0] - ACY_BACK_FOR_SECTION_ADD),
            explode('/', $this->AcademicYear->current_academicyear())[0]
        );
        debug($section_ac_years);

        $departments[0] = 0;
        //Get sections button is clicked
        if (isset($this->request->data['listSections'])) {
            $this->__init_search_student();

            $options = array();

            $options = array(
                'conditions' => array(
                    'Section.archive' => 0,
                    'Section.program_id' => $this->request->data['Student']['program_id'],
                    'Section.program_type_id' => $this->request->data['Student']['program_type_id']
                ),
                'order' => array(
                    'Section.academicyear' => 'DESC',
                    'Section.year_level_id' => 'ASC',
                    'Section.id' => 'ASC',
                    'Section.name' => 'ASC'
                ),
                'recursive' => -1
            );

            if ($freshman_program == 1) {
                $options['conditions'][] = array(
                    'Section.college_id' => $this->college_id,
                    'Section.archive' => 0,
                    'Section.department_id IS NULL',
                    'Section.academicyear' => $this->request->data['Student']['acadamic_year']
                    //'Section.year_level_id IS NULL or Section.year_level_id="" or Section.year_level_id = 0'
                );
            } else {
                $options['conditions'][] = array(
                    'Section.department_id' => $this->department_id,
                    'Section.year_level_id' => $this->request->data['Student']['year_level_id'],
                    'Section.academicyear' => $section_ac_years
                );
            }

            //$sections = $this->Student->Section->find('list', $options);

            $options['contain'] = array('YearLevel', 'Program', 'ProgramType');

            $sections_detail_all = $this->Student->Section->find('all', $options);

            if (!empty($sections_detail_all)) {
                foreach ($sections_detail_all as $seindex => $secvalue) {
                    $sections[$secvalue['Program']['name']][$secvalue['Section']['id']] = $secvalue['Section']['name'] . ' (' . (isset($secvalue['YearLevel']['name']) && !empty($secvalue['YearLevel']['name']) ? $secvalue['YearLevel']['name'] : ($secvalue['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')) . ', ' . $secvalue['Section']['academicyear'] . ')';
                }
            }

            if ($freshman_program == 1 && !empty($sections)) {
                $sections['pre'] = "All";
                asort($sections);
            }

            if (empty($sections)) {
                $this->Flash->info(__('No section is found with the given search criteria.'));
            } else {
                $sections = array('0' => '[ Select Section ]') + $sections;
            }

            $year_level_selected = $this->request->data['Student']['year_level_id'];
            $program_id = $this->request->data['Student']['program_id'];
            $program_type_id = $this->request->data['Student']['program_type_id'];
        }

        //Section is selected from the combo box
        if (isset($this->request->data['issueStudentPassword']) || (!empty($section_id) && ($section_id != 0 || strcasecmp(
                        $section_id,
                        "pre"
                    ) == 0))) {
            $this->__init_search_student();

            if (isset($this->request->data['issueStudentPassword'])) {
                $section_id = $this->request->data['Student']['section_id'];
            }

            if (!empty($section_id) && $section_id != "pre" && $section_id > 0) {
                $section_detail = $this->Student->Section->find('first', array(
                    'conditions' => array(
                        'Section.id' => $section_id
                    ),
                    'contain' => array(
                        'Program' => array('id', 'name'),
                        'ProgramType' => array('id', 'name'),
                        'YearLevel' => array('id', 'name'),
                    ),
                    'recursive' => -1
                ));

                if (ALLOW_STUDENTS_TO_RESET_PASSWORD_BY_EMAIL == 'AUTO') {
                    $general_settings = ClassRegistry::init(
                        'GeneralSetting'
                    )->getAllGeneralSettingsByStudentByProgramIdOrBySectionID(null, null, null, $section_id);

                    if (!empty($general_settings['GeneralSetting'])) {
                        //debug($general_settings['GeneralSetting']['allowStudentsToResetPasswordByEmail']);
                        $reset_password_by_email = $general_settings['GeneralSetting']['allowStudentsToResetPasswordByEmail'];
                    }
                }

                $year_level_selected = $section_detail['Section']['year_level_id'];
                $program_id = $section_detail['Section']['program_id'];
                $program_type_id = $section_detail['Section']['program_type_id'];
            }

            //Student list retrial
            if (strcasecmp($section_id, "pre") == 0) {
                $students_in_section = $this->Student->listStudentByAdmissionYear(
                    null,
                    $this->college_id,
                    $this->request->data['Student']['acadamic_year'],
                    $this->request->data['Student']['name'],
                    0
                );

                $this->request->data['Student']['section_id'] = 'pre';
            } else {
                $students_in_section = $this->Student->Section->getSectionStudents(
                    $section_id,
                    $this->request->data['Student']['name']
                );

                $this->request->data['Student']['section_id'] = $section_id;
            }

            $options = array();

            $options = array(
                'conditions' => array(
                    'Section.archive' => 0,
                    'Section.program_id' => $this->request->data['Student']['program_id'],
                    'Section.program_type_id' => $this->request->data['Student']['program_type_id'],
                ),
                'order' => array(
                    'Section.academicyear' => 'DESC',
                    'Section.year_level_id' => 'ASC',
                    'Section.id' => 'ASC',
                    'Section.name' => 'ASC'
                ),
                'recursive' => -1
            );

            if ($freshman_program == 1) {
                $options['conditions'][] = array(
                    'Section.college_id' => $this->college_id,
                    'Section.archive' => 0,
                    'Section.department_id IS NULL',
                    'Section.academicyear' => $this->request->data['Student']['acadamic_year'],
                    //'Section.year_level_id IS NULL'
                );
            } else {
                $options['conditions'][] = array(
                    'Section.department_id' => $this->department_id,
                    'Section.year_level_id' => $this->request->data['Student']['year_level_id'],
                    'Section.academicyear' => $section_ac_years
                );
            }

            //$sections = $this->Student->Section->find('list', $options);

            $options['contain'] = array('YearLevel', 'Program', 'ProgramType');

            $sections_detail_all = $this->Student->Section->find('all', $options);

            if (!empty($sections_detail_all)) {
                foreach ($sections_detail_all as $seindex => $secvalue) {
                    $sections[$secvalue['Program']['name']][$secvalue['Section']['id']] = $secvalue['Section']['name'] . ' (' . (isset($secvalue['YearLevel']['name']) && !empty($secvalue['YearLevel']['name']) ? $secvalue['YearLevel']['name'] : ($secvalue['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')) . ', ' . $secvalue['Section']['academicyear'] . ')';
                }
            }

            //Give an option to get all freshman studnet of the college
            if ($freshman_program == 1 && !empty($sections)) {
                $sections['pre'] = "All"; // most if the time, it doesn't work when the student number is too large, better to turn it off, reset by section is enough. // Added Common Password Option, It will remove this short coming, Neway
                asort($sections);
            }

            if (empty($sections)) {
                $this->Flash->info(__('There is no section with the selected search criteria.'));
            } else {
                $sections = array('0' => '[ Select Section ]') + $sections;
            }
        }

        //Issue Student Password button is clicked
        if (isset($this->request->data['issueStudentPassword'])) {
            $student_ids = array();

            if (!empty($this->request->data['Student'])) {
                foreach ($this->request->data['Student'] as $key => $student) {
                    if (is_numeric($key) && !empty($student['student_id'])) {
                        if (isset($student['gp']) && ($student['gp'] == 1 || $student['gp'] == '1')) {
                            $student_detail['student_id'] = $student['student_id'];
                            //$student_detail['flat_password'] = $this->_generatePassword(5);

                            if (empty($this->request->data['Student']['common_password']) || (isset($this->request->data['Student']['common_password']) && strlen(
                                        $this->request->data['Student']['common_password']
                                    ) < 5)) {
                                $student_detail['flat_password'] = $this->_generatePassword(5);
                            } else {
                                $student_detail['flat_password'] = $this->request->data['Student']['common_password'];
                            }

                            $student_detail['hashed_password'] = Security::hash(
                                trim($student_detail['flat_password']),
                                null,
                                true
                            );
                            $student_ids[] = $student_detail;
                        }
                    }
                }
            }

            if (empty($student_ids)) {
                $this->Flash->error(__('You are required to select at least one student.'));
            } else {
                //debug($student_ids[0]);
                //debug($student_ids);

                $student_passwords = $this->Student->getStudentPassword($student_ids);

                if (empty($student_passwords)) {
                    $this->Flash->info(
                        '<span></span>' . __(
                            'ERROR: Unable to issue password for the selected students. Please try again.'
                        )
                    );
                } else {
                    $this->set(compact('student_passwords'));

                    $this->response->type('application/pdf');
                    $this->layout = '/pdf/default';

                    if (isset($section_detail)) {
                        $section_for_file_name = $section_detail['Section']['name'] . '_' . (isset($section_detail['YearLevel']['name']) ? $section_detail['YearLevel']['name'] : '') . '_' . (str_replace(
                                '/',
                                '_',
                                $section_detail['Section']['academicyear']
                            )) . '_' . $section_detail['Program']['name'] . '_' . $section_detail['ProgramType']['name'];
                    } else {
                        $section_for_file_name = 'All_' . ($this->request->data['Student']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial_Sections_' : 'Pre_Freshman_Sections_') . '_' . (str_replace(
                                '/',
                                '_',
                                $this->request->data['Student']['acadamic_year']
                            ));
                    }

                    $this->set(compact('section_for_file_name'));

                    if ($this->request->data['Student']['single_page'] == "yes") {
                        $this->render('mass_password_issue_single_page_pdf');
                    } else {
                        $this->set(compact('reset_password_by_email'));
                        $this->render('issue_password_pdf');
                    }

                    return;
                }
            }
        }

        $this->set(
            compact(
                'programs',
                'program_types',
                'departments',
                'yearLevels',
                'year_level_selected',
                'semester_selected',
                'program_id',
                'program_type_id',
                'section_id',
                'sections',
                'students_in_section'
            )
        );

        $this->render('issue_password_list');
    }

    public function _generatePassword($length = '')
    {

        $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $max = strlen($str);
        $length = @round($length);

        if (empty($length)) {
            $length = rand(8, 12);
        }

        $password = '';

        for ($i = 0; $i < $length; $i++) {
            $password .= $str[rand(0, $max - 1)];
        }

        return $password;
    }

    public function autoYearlevelUpdate()
    {

        $studentssections = $this->Student->find('all', array(
            'conditions' => array(
                //'Student.id NOT IN (select student_id from graduate_lists)'
                'Student.graduated' => 0
            ),
            'contain' => array(
                'CourseRegistration' => array(
                    //'order' => array('CourseRegistration.created DESC'), // Backdated grade entry affects created field and it will not be accurate
                    'order' => array(
                        'CourseRegistration.academic_year' => 'DESC',
                        'CourseRegistration.semester' => 'DESC',
                        'CourseRegistration.id' => 'DESC'
                    ),
                    'limit' => 1
                )
            ),
            'fields' => array(
                'Student.id',
                'Student.studentnumber',
                'Student.full_name',
                'Student.department_id',
                'Student.program_id'
            )
        ));

        $count = 0;
        $studentList = array();

        if (!empty($studentssections) && count($studentssections) > 0) {
            foreach ($studentssections as $key => $student) {
                $studentList['Student'][$count]['id'] = $student['Student']['id'];

                if (is_null(
                        $student['Student']['department_id']
                    ) && (empty($student['CourseRegistration'][0]['year_level_id']) || empty($student['CourseRegistration']))) {
                    $studentList['Student'][$count]['yearLevel'] = 'Pre/1st';
                } else {
                    if (empty($student['CourseRegistration']) || empty($student['CourseRegistration'][0]['year_level_id'])) {
                        $studentList['Student'][$count]['yearLevel'] = '1st';
                    } else {
                        // find the year level
                        $yearLevel = ClassRegistry::init('YearLevel')->field(
                            'YearLevel.name',
                            array('YearLevel.id' => $student['CourseRegistration'][0]['year_level_id'])
                        );

                        if (!empty($yearLevel)) {
                            $studentList['Student'][$count]['yearLevel'] = $yearLevel;
                        }

                        debug($yearLevel);
                    }
                }

                $count++;
            }
        }

        if (!empty($studentList['Student'])) {
            //saveAll
            if ($this->Student->saveAll($studentList['Student'], array('validate' => false))) {
            }
        }
    }

    public function nameList()
    {

        $this->paginate = array('contain' => array('Department', 'Curriculum', 'ProgramType', 'Program', 'College'));

        if ((isset($this->request->data['Student']) && isset($this->request->data['viewPDF']))) {
            $search_session = $this->Session->read('search_data');
            debug($search_session);
            $this->request->data['Student'] = $search_session;
        }

        if (isset($this->passedArgs)) {
            if (isset($this->passedArgs['page'])) {
                $this->__init_search_name();
                $this->request->data['Student']['page'] = $this->passedArgs['page'];
                $this->__init_search_name();
            }
        }

        if ((isset($this->request->data['Student']) && isset($this->request->data['listStudentsForNameChange']))) {
            $this->__init_search_name();
        }

        // filter by department or college
        if (isset($this->request->data['Student']['department_id']) && !empty($this->request->data['Student']['department_id'])) {
            $department_id = $this->request->data['Student']['department_id'];
            $college_id = explode('~', $department_id);

            if (count($college_id) > 1) {
                $this->paginate['conditions'][]['Student.college_id'] = $college_id[1];
            } else {
                $this->paginate['conditions'][]['Student.department_id'] = $department_id;
            }
        }

        if (isset($this->request->data['Student']['program_id']) && !empty($this->request->data['Student']['program_id'])) {
            $this->paginate['conditions'][]['Student.program_id'] = $this->request->data['Student']['program_id'];
        }

        if (isset($this->request->data['Student']['program_type_id']) && !empty($this->request->data['Student']['program_type_id'])) {
            $this->paginate['conditions'][]['Student.program_type_id'] = $this->request->data['Student']['program_type_id'];
        }

        if (isset($this->request->data['Student']['studentnumber']) && !empty($this->request->data['Student']['studentnumber'])) {
            unset($this->paginate);
            $this->paginate['conditions'][]['Student.studentnumber'] = $this->request->data['Student']['studentnumber'];
        }

        if (isset($this->request->data['Student']['admission_year']) && !empty($this->request->data['Student']['admission_year'])) {
            debug($this->request->data['Student']['admission_year']);
            $this->paginate['conditions'][]['Student.admissionyear'] = $this->AcademicYear->getAcademicYearBegainingDate(
                $this->request->data['Student']['admission_year'],
                'I'
            );
        }

        if (isset($this->request->data['Student']['name']) && !empty($this->request->data['Student']['name'])) {
            unset($this->paginate);
            $this->paginate['conditions'][]['Student.first_name LIKE '] = trim(
                    $this->request->data['Student']['name']
                ) . '%';
        }

        if (isset($this->request->data['Student']['page']) && !empty($this->request->data['Student']['page'])) {
            $this->paginate['page'] = $this->request->data['Student']['page'];
        }

        $this->Paginator->settings = $this->paginate;

        if (isset($this->request->data) && !empty($this->Paginator->settings['conditions'])) {
            $students_for_name_list = $senateLists = $this->Paginator->paginate('Student');
        } else {
            $students_for_name_list = array();
        }

        if (empty($students_for_name_list) && isset($this->request->data) && !empty($this->request->data)) {
            $this->Flash->info(__('There is no student in the system based with the given criteria.'));
        }

        //debug($students_for_name_list);

        $programs = $this->Student->Program->find('list');
        $program_types = $this->Student->ProgramType->find('list');
        $departments = $this->Student->Department->allDepartmentsByCollege2(
            1,
            $this->department_ids,
            $this->college_ids
        );

        $programs = array(0 => 'All Programs') + $programs;
        $program_types = array(0 => 'All Program Types') + $program_types;
        $departments = array(0 => 'All University Students') + $departments;

        $default_department_id = null;
        $default_program_id = null;
        $default_program_type_id = null;

        if ((isset($this->request->data['Student']) && isset($this->request->data['viewPDF']))) {
            debug($students_for_name_list);

            if (!empty($students_for_name_list)) {
                foreach ($students_for_name_list as $k => $v) {
                    $g_d_obj = new DateTime($v['Student']['admissionyear']);
                    $admission_year = explode('-', $v['Student']['admissionyear']);
                    $e_g_year = $this->EthiopicDateTime->GetEthiopicYear(
                        $g_d_obj->format('j'),
                        $g_d_obj->format('n'),
                        $g_d_obj->format('Y')
                    );
                    $g_academic_year = $this->AcademicYear->get_academicyear($admission_year[1], $admission_year[0]);
                    $students_for_name_list_pdf[$v['Department']['name'] . '~' . $v['Program']['name'] . '~' . $v['ProgramType']['name'] . '~' . $g_academic_year . '(' . $e_g_year . 'E.C)'][] = $v;
                }

                $this->set(compact('students_for_name_list_pdf', 'defaultacademicyear'));
                $this->response->type('application/pdf');
                $this->layout = 'pdf';
                $this->render('name_list_pdf');
            } else {
                $this->Flash->info(__('EMPTY DATA: Unable to generate PDF.'));
            }
        }

        $this->set(
            compact(
                'programs',
                'program_types',
                'departments',
                'students_for_name_list',
                'default_department_id',
                'default_program_id',
                'default_program_type_id',
                'senateLists'
            )
        );
    }

    public function __init_search_name()
    {

        // We create a search_data session variable when we fill any criteria  in the search form.
        if (!empty($this->request->data['Student'])) {
            $this->Session->write('search_data', $this->request->data['Student']);
        } else {
            if ($this->Session->check('search_data')) {
                $this->request->data['Student'] = $this->Session->read('search_data');
            }
        }
    }

    public function correctName($id)
    {

        if (!$id) {
            $this->Flash->error(__('Invalid ID'));
            $this->redirect($this->referer());
        }

        $check_elegibility_to_edit = 0;

        if (!empty($this->college_ids)) {
            $check_elegibility_to_edit = $this->Student->find('count', array(
                'conditions' => array(
                    'Student.college_id' => $this->college_ids,
                    'Student.id' => $id,
                    'Student.program_id' => $this->program_ids,
                    'Student.program_type_id' => $this->program_type_ids,
                )
            ));
        } else {
            if ($this->department_ids) {
                $check_elegibility_to_edit = $this->Student->find('count', array(
                    'conditions' => array(
                        'Student.department_id' => $this->department_ids,
                        'Student.id' => $id,
                        'Student.program_id' => $this->program_ids,
                        'Student.program_type_id' => $this->program_type_ids,
                    )
                ));
            }
        }


        if ($check_elegibility_to_edit == 0) {
            $this->Flash->error(
                __(
                    'You are not elgibile to correct the student name. This happens when you are trying to edit students name which you are not assigned to edit.'
                )
            );
            //$this->redirect(array('action' => 'name_list'));
        }

        if (!empty($this->request->data) && $this->request->data['correctName']) {
            if ($this->Student->save($this->request->data)) {
                $this->Flash->success(__('The student name has been updated.'));
                $this->redirect($this->referer());
                //$this->redirect(array('action' => 'index'));
            } else {
                $this->Flash->error(
                    __(
                        'The student name could not be saved. Please check other required fields are updated in studnet profile and try again.'
                    )
                );
            }

            $this->redirect($this->referer());
        }

        $studentDetail = $this->Student->find(
            'first',
            array(
                'conditions' => array('Student.id' => $id),
                /* 'contain' => array('StudentNameHistory'), */
                'recursive' => -1
            )
        );

        //debug($studentDetail);

        if (empty($this->request->data)) {
            $this->request->data = $this->Student->find(
                'first',
                array(
                    'conditions' => array('Student.id' => $id),
                    /* 'contain' => array('StudentNameHistory'), */
                    'recursive' => -1
                )
            ); //$this->Student->read(null, $id);
        }

        $this->set(compact('studentDetail'));
    }

    public function __auto_registration_update($publishedcourse_id)
    {

        $latest_academic_year = $this->AcademicYear->current_academicyear();

        $publishedCourseDetail = ClassRegistry::init('PublishedCourse')->find(
            'first',
            array('conditions' => array('PublishedCourse.id' => $publishedcourse_id), 'recursive' => -1)
        );

        $studentssections = ClassRegistry::init('StudentsSection')->find(
            'all',
            array(
                'conditions' => array('StudentsSection.section_id' => $publishedCourseDetail['PublishedCourse']['section_id']),
                'recursive' => -1
            )
        );

        $count = 0;
        $studentList = array();

        if (!empty($studentssections) && count($studentssections) > 0) {
            foreach ($studentssections as $k => $v) {
                // registered
                $registered = ClassRegistry::init('CourseRegistration')->find(
                    'first',
                    array(
                        'conditions' => array(
                            'CourseRegistration.published_course_id' => $publishedcourse_id,
                            'CourseRegistration.student_id' => $v['StudentsSection']['student_id']
                        ),
                        'recursive' => -1
                    )
                );

                //print_r($registered);
                if (empty($registered)) {
                    // does that student dismissed ?
                    $passed_or_failed = $this->Student->StudentExamStatus->getStudentLastExamStatus(
                        $v['StudentsSection']['student_id'],
                        $latest_academic_year
                    );

                    if ($passed_or_failed == 1 || $passed_or_failed == 3) {
                        $studentList['CourseRegistration'][$count]['year_level_id'] = $publishedCourseDetail['PublishedCourse']['year_level_id'];
                        $studentList['CourseRegistration'][$count]['section_id'] = $publishedCourseDetail['PublishedCourse']['section_id'];
                        $studentList['CourseRegistration'][$count]['semester'] = $publishedCourseDetail['PublishedCourse']['semester'];
                        $studentList['CourseRegistration'][$count]['academic_year'] = $publishedCourseDetail['PublishedCourse']['academic_year'];
                        $studentList['CourseRegistration'][$count]['student_id'] = $v['StudentsSection']['student_id'];
                        $studentList['CourseRegistration'][$count]['published_course_id'] = $publishedCourseDetail['PublishedCourse']['id'];

                        $studentList['CourseRegistration'][$count]['created'] = $publishedCourseDetail['PublishedCourse']['created'];
                        $studentList['CourseRegistration'][$count]['modified'] = $publishedCourseDetail['PublishedCourse']['modified'];

                        $count++;
                    }
                }

                //$count++;
                //print_r($count);
            }
        }

        if (!empty($studentList['CourseRegistration'])) {
            //saveAll
            if (ClassRegistry::init('CourseRegistration')->saveAll(
                $studentList['CourseRegistration'],
                array('validate' => false)
            )) {
            }
        }
    }

    public function scanProfilePicture()
    {

        debug($this->request->data);
        if (isset($this->request->data['Synchronize']) && !empty($this->request->data['Synchronize'])) {


            $path = WWW_ROOT . "media/transfer/img/";
            $allImages = $this->__getNewestFN($path);
            $count = 0;

            if (!empty($allImages)) {
                foreach ($allImages as $image) {
                    //check if student is there
                    $attachmentModel = array();

                    $imageFileName = explode(WWW_ROOT . 'media/transfer/img/', $image);

                    $studentnumberWithImage = str_replace('-', '/', $imageFileName[1]);
                    $studentnumber = explode('.jpg', $studentnumberWithImage);

                    $student_number_exist = $this->Student->find(
                        'first',
                        array('conditions' => array('Student.studentnumber' => $studentnumber[0]))
                    );
                    $filename = $imageFileName[1];

                    if (!empty($student_number_exist)) {
                        $isUploadedAlready = ClassRegistry::init('Photo')->find('first', array(
                            'conditions' => array(
                                'Photo.model' => 'Student',
                                'Photo.foreign_key' => $student_number_exist['Student']['id'],
                                'Photo.group' => 'profile'
                            )
                        ));

                        if (!empty($isUploadedAlready)) {
                            $attachmentModel['Photo']['id'] = $isUploadedAlready['Photo']['id'];
                        }

                        $attachmentModel['Photo']['model'] = 'Student';
                        $attachmentModel['Photo']['foreign_key'] = $student_number_exist['Student']['id'];
                        $attachmentModel['Photo']['dirname'] = 'img';
                        $attachmentModel['Photo']['basename'] = $filename;
                        $attachmentModel['Photo']['checksum'] = md5($filename);
                        $attachmentModel['Photo']['group'] = 'profile';

                        if (!empty($attachmentModel['Photo'])) {
                            if (empty($attachmentModel['Photo']['id'])) {
                                ClassRegistry::init('Photo')->create();
                            }

                            if (ClassRegistry::init('Photo')->save($attachmentModel)) {
                                $count++;
                            }
                        }
                    }
                }
            }

            if ($count) {
                $this->Flash->success(
                    __(
                        'The dropped profile pictures of students has been completed by synchronizing ' . $count . ' file(s).'
                    )
                );
            }
        }
    }

    private function __getNewestFN($path)
    {

        // store all .inf names in array

        $files = glob($path . '*.{jpg}', GLOB_BRACE);
        usort($files, array($this, "_filemtime_compare"));

        return $files;

    }

    private function _filemtime_compare($a, $b)
    {

        return filemtime($a) - filemtime($b);
    }

    public function massImportProfilePicture()
    {

        if (!empty($this->request->data)) {
            //check the file type before doing the fucken manipulations.
            if (strcasecmp($this->request->data['Student']['xls']['type'], 'application/vnd.ms-excel')) {
                $this->Flash->error(
                    __(
                        'Importing Error. Please  save your excel file as "Excel 97-2003 Workbook" type and import again. Current file format is: ' . $this->request->data['Student']['xls']['type']
                    )
                );
                return;
            }

            $data = new Spreadsheet_Excel_Reader();
            // Set output Encoding.
            $data->setOutputEncoding('CP1251');
            $data->read($this->request->data['Student']['xls']['tmp_name']);
            $headings = array();
            $xls_data = array();
            $non_existing_field = array();
            $required_fields = array('studentnumber', 'photonumber');

            if (empty($data->sheets[0]['cells'])) {
                $this->Flash->error(__('Importing Error. The excel file you uploaded is empty.'));
                return;
            }

            if (empty($data->sheets[0]['cells'][1])) {
                $this->Flash->error(
                    __(
                        'Importing Error. Please insert your filed name (studentnumber,photonumber)  at first row of your excel file.'
                    )
                );
                return;
            }

            for ($k = 0; $k < count($required_fields); $k++) {
                if (in_array($required_fields[$k], $data->sheets[0]['cells'][1]) === false) {
                    $non_existing_field[] = $required_fields[$k];
                }
            }

            if (count($non_existing_field) > 0) {
                $field_list = "";
                foreach ($non_existing_field as $k => $v) {
                    $field_list .= ($v . ", ");
                }
                $field_list = substr($field_list, 0, (strlen($field_list) - 2));
                $this->Flash->error(
                    __(
                        'Importing Error. ' . $field_list . ' is/are required in the excel file you imported at first row.'
                    )
                );
                return;
            } else {
                $fields_name_import_table = $data->sheets[0]['cells'][1];
                $formatUploadedPicsPath = array();
                $uploadMaps = array();

                for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
                    $row_data = array();
                    for ($j = 1; $j <= count($fields_name_import_table); $j++) {
                        if ($fields_name_import_table[$j] == "studentnumber" && $data->sheets[0]['cells'][$i][$j] == "") {
                            $non_valide_rows[] = "Please enter a valid student number on row number " . $i;
                            continue;
                        } else {
                            if ($fields_name_import_table[$j] == "studentnumber") {
                                $row_data['studentnumber'] = $data->sheets[0]['cells'][$i][$j];
                            }

                            if ($fields_name_import_table[$j] == "photonumber") {
                                $row_data['photonumber'] = $data->sheets[0]['cells'][$i][$j];
                            }
                        }
                    }
                    $uploadMaps[$row_data['studentnumber']] = $row_data['photonumber'];
                }

                $invalidStudentIds = array();
                $validStudentIds = array();

                if (!empty($uploadMaps)) {
                    $rowCount = 1;
                    $attachmentModel = array();
                    foreach ($uploadMaps as $kk => $vv) {
                        //check if the student id exists

                        $student_number_exist = $this->Student->find(
                            'first',
                            array('conditions' => array('Student.studentnumber' => $kk), 'recursive' => -1)
                        );
                        debug($student_number_exist);


                        if ($student_number_exist) {
                            $uploadAndSavePicture = array();
                            foreach ($this->request->data['Student']['File'] as $fk => $fv) {
                                $attachmentModel = array();
                                if (stristr($fv['name'], $vv) !== false) {
                                    $ext = substr(strtolower(strrchr($fv['name'], '.')), 1); //get the extension

                                    $filenameNew = str_replace('/', '-', $kk) . '.' . $ext;

                                    $arr_ext = array('jpg', 'jpeg', 'png'); //set allowed extensions

                                    //only process if the extension is valid
                                    if (in_array($ext, $arr_ext)) {
                                        if (move_uploaded_file(
                                            $fv['tmp_name'],
                                            WWW_ROOT . "/media/transfer/img/" . $filenameNew
                                        )) {
                                            $attachment = ClassRegistry::init('Photo')->find('first', array(
                                                'conditions' => array(
                                                    'foreign_key' => $student_number_exist['Student']['id'],
                                                    'model' => "Student"
                                                ),
                                                'fields' => array(
                                                    'id',
                                                    'model',
                                                    'dirname',
                                                    'basename',
                                                    'checksum',
                                                    'group'
                                                ),
                                                'recursive' => -1,
                                            ));

                                            if (!empty($attachment)) {
                                                $attachmentModel['Photo']['id'] = $attachment['Photo']['id'];
                                            }

                                            // do size validation and extension in here

                                            $attachmentModel['Photo']['model'] = 'Student';
                                            $attachmentModel['Photo']['foreign_key'] = $student_number_exist['Student']['id'];
                                            $attachmentModel['Photo']['dirname'] = 'img';
                                            $attachmentModel['Photo']['basename'] = $filenameNew;
                                            $attachmentModel['Photo']['checksum'] = md5($filenameNew);
                                            $attachmentModel['Photo']['group'] = 'profile';

                                            if (!empty($attachmentModel['Photo'])) {
                                                if (empty($attachmentModel['Photo']['id'])) {
                                                    ClassRegistry::init('Photo')->create();
                                                }
                                                if (ClassRegistry::init('Photo')->save($attachmentModel)) {
                                                    $validStudentIds[$kk] = $rowCount;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            $invalidStudentIds[$kk] = $rowCount;
                        }

                        $rowCount++;
                    }
                }

                if (!empty($validStudentIds)) {
                    debug($invalidStudentIds);
                    $this->Flash->success('Uploaded ' . count($validStudentIds) . ' profile pictures.');
                }
            }
        }

        $profilePictureUploaded = ClassRegistry::init('Attachment')->find('count', array(
            'conditions' => array(
                'group' => 'profile',
                'model' => "Student",
                'foreign_key in (select id from students )'
            ),
            'recursive' => -1
        ));

        $totalStudentCount = $this->Student->find(
            'count',
            array('conditions' => array('Student.graduated = 0'), 'recursive' => -1)
        );

        $this->set(compact('profilePictureUploaded', 'totalStudentCount'));
    }

    public function massImportStudentNationalId()
    {

        if (!empty($this->request->data)) {
            debug($this->request->data);

            //check the file type before doing the fucken manipulations.
            if (strcasecmp($this->request->data['Student']['xls']['type'], 'application/vnd.ms-excel')) {
                $this->Flash->error(
                    __(
                        'Importing Error. Please  save your excel file as "Excel 97-2003 Workbook" type and import again. Current file format is: ' . $this->request->data['Student']['xls']['type']
                    )
                );
                return;
            }

            $data = new Spreadsheet_Excel_Reader();
            // Set output Encoding.
            $data->setOutputEncoding('CP1251');
            $data->read($this->request->data['Student']['xls']['tmp_name']);
            $headings = array();
            $xls_data = array();
            $non_existing_field = array();
            $required_fields = array('studentnumber', 'student_national_id');

            if (empty($data->sheets[0]['cells'])) {
                $this->Flash->error(__('Importing Error. The excel file you uploaded is empty.'));
                return;
            }

            if (empty($data->sheets[0]['cells'][1])) {
                $this->Flash->error(
                    __(
                        'Importing Error. Please insert your fieled name (studentnumber,student_national_id) at first row of your excel file.'
                    )
                );
                return;
            }

            for ($k = 0; $k < count($required_fields); $k++) {
                if (in_array($required_fields[$k], $data->sheets[0]['cells'][1]) === false) {
                    $non_existing_field[] = $required_fields[$k];
                }
            }

            if (count($non_existing_field) > 0) {
                $field_list = "";
                foreach ($non_existing_field as $k => $v) {
                    $field_list .= ($v . ", ");
                }
                $field_list = substr($field_list, 0, (strlen($field_list) - 2));
                $this->Flash->error(
                    __(
                        'Importing Error. ' . $field_list . ' is/are required in the excel file you imported at first row.'
                    )
                );
                return;
            } else {
                $fields_name_import_table = $data->sheets[0]['cells'][1];
                $uploadMaps = array();

                for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
                    $row_data = array();
                    for ($j = 1; $j <= count($fields_name_import_table); $j++) {
                        if ($fields_name_import_table[$j] == "studentnumber" && trim(
                                $data->sheets[0]['cells'][$i][$j]
                            ) == "") {
                            $non_valide_rows[] = "Please enter a valid student number on row number " . $i;
                            continue;
                        } else {
                            if ($fields_name_import_table[$j] == "student_national_id" && trim(
                                    $data->sheets[0]['cells'][$i][$j]
                                ) == "") {
                                $non_valide_rows[] = "Please enter a valid Student National ID at row number " . $i;
                                continue;
                            } else {
                                if ($fields_name_import_table[$j] == "studentnumber") {
                                    $row_data['studentnumber'] = trim($data->sheets[0]['cells'][$i][$j]);
                                }

                                if ($fields_name_import_table[$j] == "student_national_id") {
                                    $row_data['student_national_id'] = trim($data->sheets[0]['cells'][$i][$j]);
                                }
                            }
                        }
                    }

                    $uploadMaps[$row_data['studentnumber']] = $row_data['student_national_id'];
                }

                $invalidStudentIds = array();
                $errors_to_correct = array();
                $results_to_html_table = array();
                $validStudentIds = array();

                if (!empty($uploadMaps)) {
                    $rowCount = 1;

                    foreach ($uploadMaps as $kk => $vv) {
                        //check if the student id exists

                        $student_number_exist = $this->Student->find(
                            'first',
                            array(
                                'conditions' => array('Student.studentnumber' => $kk),
                                'fields' => array(
                                    'id',
                                    'full_name',
                                    'accepted_student_id',
                                    'user_id',
                                    'graduated',
                                    'studentnumber',
                                    'student_national_id'
                                ),
                                'recursive' => -1
                            )
                        );
                        //debug($student_number_exist);

                        $results_to_html_table[$kk]['studentnumber'] = $kk;
                        $results_to_html_table[$kk]['student_national_id'] = $vv;

                        if (!empty($student_number_exist)) {
                            $national_id_exists = $this->Student->find(
                                'first',
                                array(
                                    'conditions' => array('Student.student_national_id' => $vv),
                                    'fields' => array(
                                        'id',
                                        'full_name',
                                        'accepted_student_id',
                                        'user_id',
                                        'graduated',
                                        'studentnumber',
                                        'student_national_id'
                                    ),
                                    'recursive' => -1
                                )
                            );
                            //debug($national_id_exists);
                            //debug(strlen($vv) > 7);

                            if ((is_null(
                                        $student_number_exist['Student']['student_national_id']
                                    ) || empty($student_number_exist['Student']['student_national_id'])) && (strlen(
                                        $vv
                                    ) > 7) && empty($national_id_exists)) {
                                $this->Student->id = $student_number_exist['Student']['id'];

                                if ($this->Student->saveField('student_national_id', $vv)) {
                                    $validStudentIds[$kk] = $rowCount;
                                    $results_to_html_table[$kk]['status'] = 'Updated';
                                } else {
                                    $results_to_html_table[$kk]['status'] = 'Database Error: unable to save National ID. Please try again.';
                                }
                            } else {
                                if (!empty($student_number_exist['Student']['student_national_id']) && $student_number_exist['Student']['student_national_id'] == $vv) {
                                    // same national id existing in DB, same as in the excel row
                                    //$errors_to_correct[$kk] = 'National ID: '.  $vv . ' at row # ' . $rowCount . ' is already mapped previusly to the same student ' . $student_number_exist['Student']['full_name']. ' (' . $kk . '), Update skipped.';
                                    $results_to_html_table[$kk]['status'] = 'Skipped: Existing Student ID to National ID Combination';
                                } else {
                                    if (!empty($student_number_exist['Student']['student_national_id']) && $student_number_exist['Student']['student_national_id'] != $vv && !empty($national_id_exists['Student']['studentnumber']) && $national_id_exists['Student']['student_national_id'] == $vv) {
                                        // national id already used for someone
                                        //$errors_to_correct[$kk] = 'National ID: '.  $vv . ' at row # ' . $rowCount . ' is already mapped to other student ' . $national_id_exists['Student']['full_name']. ' (' . $national_id_exists['Student']['studentnumber'] . '). Change the national ID or remove it the student from the excel.';
                                        $results_to_html_table[$kk]['status'] = 'Error: National ID: ' . $vv . ' is previously assigend to other student: ' . $national_id_exists['Student']['full_name'] . ' (' . $national_id_exists['Student']['studentnumber'] . '). Please change it to different National ID.';
                                    } else {
                                        if (!empty($student_number_exist['Student']['student_national_id']) && $student_number_exist['Student']['student_national_id'] != $vv) {
                                            // student have previous national id recorded which is diffrent to the one to be updated
                                            $results_to_html_table[$kk]['status'] = 'Skipped: ' . $student_number_exist['Student']['full_name'] . ' (' . $student_number_exist['Student']['studentnumber'] . ') have existing National ID: ' . $student_number_exist['Student']['student_national_id'] . ' which is different from the one you are tying to update: ' . $vv;
                                        } else {
                                            if (!empty($national_id_exists)) {
                                                $results_to_html_table[$kk]['status'] = 'Error: National ID: ' . $vv . ' is previously assigend to other student: ' . $national_id_exists['Student']['full_name'] . ' (' . $national_id_exists['Student']['studentnumber'] . '). Please change it to different National ID.';
                                            } else {
                                                if (strlen($vv) < 8) {
                                                    $results_to_html_table[$kk]['status'] = 'Error: National ID Length can not be less than 8 characters.';
                                                } else {
                                                    $results_to_html_table[$kk]['status'] = 'Unknown Error: Validation Error/End';
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            $invalidStudentIds[$kk] = $rowCount;
                            $results_to_html_table[$kk]['status'] = 'Error: Student ID: "' . $kk . '" is not found in the system, Please check for spelling errors.';
                        }

                        $rowCount++;
                    }
                }

                if (!empty($validStudentIds)) {
                    $this->Flash->success('Updated ' . count($validStudentIds) . ' Student National IDs.');
                } else {
                    $this->Flash->info(
                        'Nothing to update. Either all of ' . count(
                            $results_to_html_table
                        ) . ' Students National IDs in your Excel file already exists in the system or you have errors in your uploaded Excel File.'
                    );
                }

                $this->set(compact('invalidStudentIds', 'errors_to_correct', 'results_to_html_table'));
            }
        }

        $current_academicyear = $this->AcademicYear->current_academicyear();
        $ac_years_to_look = $this->AcademicYear->academicYearInArray(
            (explode('/', $current_academicyear)[0] - ACY_BACK_FOR_STUDENT_NATIONAL_ID_CHECK),
            explode('/', $current_academicyear)[0]
        );

        $admissions_years_to_look = $this->AcademicYear->academicYearInArray(
            (explode('/', $current_academicyear)[0] - ACY_BACK_FOR_ALL),
            explode('/', $current_academicyear)[0]
        );
        //debug($ac_years_to_look);

        $ac_years_to_look_imploded = "'" . implode("', '", $ac_years_to_look) . "'";


        $nonGraduatedStudentCount = $this->Student->StudentExamStatus->find('count', array(
            'conditions' => array(
                'StudentExamStatus.academic_year' => $ac_years_to_look,
                //'StudentExamStatus.academic_status_id !=' => DISMISSED_ACADEMIC_STATUS_ID,
            ),
            'contain' => array(
                'Student' => array(
                    'conditions' => array(
                        'Student.graduated' => 0,
                        'Student.program_id !=' => PROGRAM_REMEDIAL,
                        'OR' => array(
                            'Student.student_national_id IS NOT NULL',
                            'Student.student_national_id != 0',
                            'Student.student_national_id != ""'
                        ),
                        'Student.id IN (select student_id from course_registrations where academic_year IN (' . $ac_years_to_look_imploded . ') GROUP BY student_id ) '
                    )
                )
            ),
            'group' => array('StudentExamStatus.student_id'),
            'recursive' => -1
        ));

        debug($nonGraduatedStudentCount);


        $totalStudentCount = $this->Student->CourseRegistration->find('count', array(
            'conditions' => array(
                'CourseRegistration.academic_year' => $ac_years_to_look,
            ),
            'contain' => array(
                'Student' => array(
                    'conditions' => array(
                        'graduated' => 0,
                        'Student.program_id !=' => PROGRAM_REMEDIAL,
                        'Student.academicyear' => $admissions_years_to_look,
                    )
                )
            ),
            'group' => array('CourseRegistration.student_id'),
            'recursive' => -1
        ));

        debug($totalStudentCount);

        $this->set(compact('nonGraduatedStudentCount', 'totalStudentCount'));
    }

    public function massImportOneTimePasswords()
    {

        if (!empty($this->request->data)) {
            debug($this->request->data);

            //check the file type before doing the fucken manipulations.
            if (strcasecmp($this->request->data['Student']['xls']['type'], 'application/vnd.ms-excel')) {
                $this->Flash->error(
                    __(
                        'Importing Error. Please  save your excel file as "Excel 97-2003 Workbook" type and import again. Current file format is: ' . $this->request->data['Student']['xls']['type']
                    )
                );
                return;
            }

            $data = new Spreadsheet_Excel_Reader();
            // Set output Encoding.
            $data->setOutputEncoding('CP1251');
            $data->read($this->request->data['Student']['xls']['tmp_name']);
            $headings = array();
            $xls_data = array();
            $non_existing_field = array();
            $savedRecords = 0;
            $updatedRecords = 0;
            $errorInSavingRecords = 0;
            $showPortal = 0;
            $showExamCenter = 0;

            $service_type = '';
            $required_fields = array('studentnumber', 'username', 'password');

            if ($this->request->data['Student']['service'] == 'Office365') {
                $service_type = 'Office365';
            } else {
                if ($this->request->data['Student']['service'] == 'Elearning') {
                    $service_type = 'Elearning';
                    $required_fields = array('studentnumber', 'username', 'password', 'portal');
                    $showPortal = 1;
                } else {
                    if ($this->request->data['Student']['service'] == 'ExitExam') {
                        $service_type = 'ExitExam';
                        $required_fields = array('studentnumber', 'username', 'password', 'portal', 'exam_center');
                        $showPortal = 1;
                        $showExamCenter = 1;
                    } else {
                        return;
                    }
                }
            }

            if (empty($service_type)) {
                $this->Flash->error(__('Importing Error. Please select service type.'));
                return;
            }

            if (empty($data->sheets[0]['cells'])) {
                $this->Flash->error(__('Importing Error. The excel file you uploaded is empty.'));
                return;
            }

            if (empty($data->sheets[0]['cells'][1])) {
                if ($service_type == 'Office365') {
                    $this->Flash->error(
                        __(
                            'Importing Error. Please insert your fieled name (studentnumber,username, password) at first row of your excel file.'
                        )
                    );
                } else {
                    if ($service_type == 'Elearning') {
                        $this->Flash->error(
                            __(
                                'Importing Error. Please insert your fieled name (studentnumber,username, password, portal) at first row of your excel file.'
                            )
                        );
                    } else {
                        if ($service_type == 'ExitExam') {
                            $this->Flash->error(
                                __(
                                    'Importing Error. Please insert your fieled name (studentnumber,username, password, portal, exam_center) at first row of your excel file.'
                                )
                            );
                        }
                    }
                }
                return;
            }

            for ($k = 0; $k < count($required_fields); $k++) {
                if (in_array($required_fields[$k], $data->sheets[0]['cells'][1]) === false) {
                    $non_existing_field[] = $required_fields[$k];
                }
            }

            if (count($non_existing_field) > 0) {
                $field_list = "";
                foreach ($non_existing_field as $k => $v) {
                    $field_list .= ($v . ", ");
                }
                $field_list = substr($field_list, 0, (strlen($field_list) - 2));
                $this->Flash->error(
                    __(
                        'Importing Error. ' . $field_list . ' is/are required in the excel file you imported at first row.'
                    )
                );
                return;
            } else {
                $fields_name_import_table = $data->sheets[0]['cells'][1];
                $uploadMaps = array();

                for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) {
                    $row_data = array();
                    $non_valid_rows = array();

                    for ($j = 1; $j <= count($fields_name_import_table); $j++) {
                        if ($fields_name_import_table[$j] == "studentnumber" && isset($data->sheets[0]['cells'][$i][$j]) && trim(
                                $data->sheets[0]['cells'][$i][$j]
                            ) == "") {
                            $non_valid_rows[] = "Please enter a valid student number at row number " . $i;
                            continue;
                        }

                        if ($fields_name_import_table[$j] == "username" && isset($data->sheets[0]['cells'][$i][$j]) && trim(
                                $data->sheets[0]['cells'][$i][$j]
                            ) == "") {
                            $non_valid_rows[] = "Please enter a valid username at row number " . $i;
                            continue;
                        }

                        if ($fields_name_import_table[$j] == "password" && isset($data->sheets[0]['cells'][$i][$j]) && trim(
                                $data->sheets[0]['cells'][$i][$j]
                            ) == "") {
                            $non_valid_rows[] = "Please enter a valid password at row number " . $i;
                            continue;
                        }

                        if (($service_type == 'Elearning' || $service_type == 'ExitExam') && ($fields_name_import_table[$j] == "portal" && (isset($data->sheets[0]['cells'][$i][$j]) && trim(
                                        $data->sheets[0]['cells'][$i][$j]
                                    ) == ""))) {
                            $non_valid_rows[] = "Please enter a valid portal at row number " . $i;
                            continue;
                        }

                        if ($service_type == 'ExitExam' && $fields_name_import_table[$j] == "exam_center" && isset($data->sheets[0]['cells'][$i][$j]) && trim(
                                $data->sheets[0]['cells'][$i][$j]
                            ) == "") {
                            $non_valid_rows[] = "Please enter a exam center at row number " . $i;
                            continue;
                        }

                        if ($fields_name_import_table[$j] == "studentnumber" && isset($data->sheets[0]['cells'][$i][$j]) && trim(
                                $data->sheets[0]['cells'][$i][$j]
                            ) != "") {
                            $row_data['studentnumber'] = trim($data->sheets[0]['cells'][$i][$j]);
                        }

                        if (isset($row_data['studentnumber']) && !empty($row_data['studentnumber']) && !in_array(
                                $row_data['studentnumber'],
                                array_keys($uploadMaps)
                            )) {
                            if ($fields_name_import_table[$j] == "username" && isset($data->sheets[0]['cells'][$i][$j]) && trim(
                                    $data->sheets[0]['cells'][$i][$j]
                                ) != "") {
                                $row_data['username'] = trim($data->sheets[0]['cells'][$i][$j]);
                            }

                            if ($fields_name_import_table[$j] == "password" && isset($data->sheets[0]['cells'][$i][$j]) && trim(
                                    $data->sheets[0]['cells'][$i][$j]
                                ) != "") {
                                $row_data['password'] = $data->sheets[0]['cells'][$i][$j];
                            }

                            if (($service_type == 'Elearning' || $service_type == 'ExitExam') && $fields_name_import_table[$j] == "portal" && isset($data->sheets[0]['cells'][$i][$j]) && trim(
                                    $data->sheets[0]['cells'][$i][$j]
                                ) != "") {
                                $row_data['portal'] = trim($data->sheets[0]['cells'][$i][$j]);
                            }

                            if ($service_type == 'ExitExam' && $fields_name_import_table[$j] == "exam_center") {
                                $row_data['exam_center'] = trim($data->sheets[0]['cells'][$i][$j]);
                            }
                        } else {
                            if (isset($row_data['studentnumber']) && !empty($row_data['studentnumber'])) {
                                $non_valid_rows[] = 'Duplicate Student ID at  ' . $row_data['studentnumber'] . ' row number ' . $i;
                            }
                        }
                    }

                    debug($non_valid_rows);

                    if (empty($non_valid_rows) && isset($row_data['studentnumber']) && !empty($row_data['studentnumber'])) {
                        $uploadMaps[$row_data['studentnumber']] = array(
                            'studentnumber' => $row_data['studentnumber'],
                            'username' => (isset($row_data['username']) && !empty($row_data['username']) ? $row_data['username'] : ''),
                            'password' => (isset($row_data['password']) && !empty($row_data['password']) ? $row_data['password'] : ''),
                            'portal' => (isset($row_data['portal']) && !empty($row_data['portal']) && ($service_type == 'Elearning' || $service_type == 'ExitExam') ? $row_data['portal'] : null),
                            'exam_center' => (isset($row_data['exam_center']) && !empty($row_data['exam_center']) && $service_type == 'ExitExam' ? $row_data['exam_center'] : null),
                        );
                    }
                }

                $invalidStudentIds = array();
                $errors_to_correct = array();
                $results_to_html_table = array();
                $validStudentIds = array();

                //debug($uploadMaps);

                if (!empty($uploadMaps)) {
                    $rowCount = 1;

                    foreach ($uploadMaps as $kk => $vv) {
                        //check if the student id exists

                        $student_number_exist = $this->Student->find(
                            'first',
                            array(
                                'conditions' => array('Student.studentnumber' => $kk),
                                'fields' => array(
                                    'id',
                                    'full_name',
                                    'accepted_student_id',
                                    'user_id',
                                    'graduated',
                                    'studentnumber',
                                    'student_national_id'
                                ),
                                'recursive' => -1
                            )
                        );
                        //debug($student_number_exist);

                        $results_to_html_table[$kk]['studentnumber'] = $kk;
                        $results_to_html_table[$kk]['username'] = $vv['username'];
                        $results_to_html_table[$kk]['password'] = $vv['password'];

                        if (!empty($vv['portal'])) {
                            $results_to_html_table[$kk]['portal'] = $vv['portal'];
                        } else {
                            $results_to_html_table[$kk]['portal'] = '';
                        }

                        if (!empty($vv['exam_center'])) {
                            $results_to_html_table[$kk]['exam_center'] = $vv['exam_center'];
                        } else {
                            $results_to_html_table[$kk]['exam_center'] = '';
                        }

                        if (!empty($student_number_exist) && !empty($vv['username']) && !empty($vv['password'])) {
                            $otp_exists = $this->Student->Otp->find(
                                'first',
                                array(
                                    'conditions' => array(
                                        'Otp.studentnumber' => $kk,
                                        'Otp.username' => $vv['username'],
                                        'Otp.service' => $service_type
                                    ),
                                    'recursive' => -1
                                )
                            );
                            //debug($national_id_exists);
                            //debug(strlen($vv) > 7);

                            if (!empty($otp_exists)/*  || (!empty($otp_exists) && $service_type == 'Elearning' && isset($vv['portal']) && !empty($vv['portal']) && $vv['portal'] == $otp_exists['Otp']['portal']) */) {
                                if ($otp_exists['Otp']['password'] == $vv['password']) {
                                    $results_to_html_table[$kk]['status'] = 'Skipped: There is existing account for ' . $student_number_exist['Student']['full_name'] . ' (' . $student_number_exist['Student']['studentnumber'] . ') with the same password for ' . $service_type . '.';
                                } else {
                                    $this->Student->Otp->id = $otp_exists['Otp']['id'];
                                    if ($this->Student->Otp->saveField('password', $vv['password'])) {
                                        $this->Student->Otp->saveField('modified', date('Y-m-d H:i:s'));
                                        //$validStudentIds[$kk] = $rowCount;
                                        $results_to_html_table[$kk]['status'] = 'Updated new password for ' . $service_type . '';
                                        $updatedRecords++;
                                    } else {
                                        $results_to_html_table[$kk]['status'] = 'Database Error: unable to save new password for ' . $student_number_exist['Student']['full_name'] . ' (' . $student_number_exist['Student']['studentnumber'] . ') for ' . $service_type . '.';
                                        $errorInSavingRecords++;
                                    }

                                    if (!empty($vv['exam_center']) && $otp_exists['Otp']['exam_center'] != $vv['exam_center']) {
                                        if ($this->Student->Otp->saveField('exam_center', $vv['exam_center'])) {
                                            $this->Student->Otp->saveField('modified', date('Y-m-d H:i:s'));
                                            //$validStudentIds[$kk] = $rowCount;
                                            $results_to_html_table[$kk]['status'] = ' Updated Exam Center ' . $service_type . '';
                                            $updatedRecords++;
                                        } else {
                                            $results_to_html_table[$kk]['status'] = 'Database Error: unable to save exam center for ' . $student_number_exist['Student']['full_name'] . ' (' . $student_number_exist['Student']['studentnumber'] . ') for ' . $service_type . '.';
                                            $errorInSavingRecords++;
                                        }
                                    }
                                }
                            } else {
                                if (strlen($vv['username']) < 4) {
                                    $results_to_html_table[$kk]['status'] = 'Username Error: Username for ' . $student_number_exist['Student']['full_name'] . ' (' . $student_number_exist['Student']['studentnumber'] . ') is not valid.';
                                } else {
                                    if (strlen($vv['password']) < 8) {
                                        $results_to_html_table[$kk]['status'] = 'Password Error: password for ' . $student_number_exist['Student']['full_name'] . ' (' . $student_number_exist['Student']['studentnumber'] . ') is too short.';
                                    } else {
                                        if (empty($vv['portal']) && ($service_type == 'Elearning' || $service_type == 'ExitExam')) {
                                            $results_to_html_table[$kk]['status'] = 'Portal Error: you need to specify ' . ($service_type == 'Elearning' ? 'E-Learning' : 'Exit Exam') . ' portal to use for ' . $student_number_exist['Student']['full_name'] . ' (' . $student_number_exist['Student']['studentnumber'] . ').';
                                        } else {
                                            if (empty($vv['portal']) && ($service_type == 'Elearning' || $service_type == 'ExitExam')) {
                                                $results_to_html_table[$kk]['status'] = 'Exam Center Error: you need to specify Exam Center for ' . $student_number_exist['Student']['full_name'] . ' (' . $student_number_exist['Student']['studentnumber'] . ').';
                                            } else {
                                                $new_otp_entry = array();
                                                $new_otp_entry['student_id'] = $student_number_exist['Student']['id'];
                                                $new_otp_entry['studentnumber'] = $kk;
                                                $new_otp_entry['username'] = $vv['username'];
                                                $new_otp_entry['password'] = $vv['password'];
                                                $new_otp_entry['service'] = $service_type;
                                                $new_otp_entry['portal'] = (!empty($vv['portal']) ? $vv['portal'] : null);
                                                $new_otp_entry['exam_center'] = (!empty($vv['exam_center']) ? $vv['exam_center'] : null);
                                                $new_otp_entry['active'] = 1;
                                                $new_otp_entry['created'] = date('Y-m-d H:i:s');
                                                $new_otp_entry['modified'] = date('Y-m-d H:i:s');

                                                if ($this->Student->Otp->saveAll(
                                                    $new_otp_entry,
                                                    array('validate' => 'first')
                                                )) {
                                                    $validStudentIds[$kk] = $rowCount;
                                                    $results_to_html_table[$kk]['status'] = 'Added ' . $service_type . ' OTP';
                                                    $savedRecords++;
                                                } else {
                                                    $results_to_html_table[$kk]['status'] = 'Database Error: unable to save new OTP for ' . $student_number_exist['Student']['full_name'] . ' (' . $student_number_exist['Student']['studentnumber'] . ') for ' . $service_type . ' . Please try again.';
                                                    $errorInSavingRecords++;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            if (empty($vv['username'])) {
                                $invalidStudentIds[$kk] = $rowCount;
                                $results_to_html_table[$kk]['status'] = 'Username Error: Please provide a username for ' . (isset($student_number_exist['Student']['full_name']) ? $student_number_exist['Student']['full_name'] . ' (' . $student_number_exist['Student']['studentnumber'] . ')' : '"' . $kk . '"') . '.';
                            } else {
                                if (empty($vv['password'])) {
                                    $invalidStudentIds[$kk] = $rowCount;
                                    $results_to_html_table[$kk]['status'] = 'Password Error: Please provide a valid password for ' . (isset($student_number_exist['Student']['full_name']) ? $student_number_exist['Student']['full_name'] . ' (' . $student_number_exist['Student']['studentnumber'] . ')' : '"' . $kk . '"') . '.';
                                } else {
                                    $invalidStudentIds[$kk] = $rowCount;
                                    $results_to_html_table[$kk]['status'] = 'Error: Student ID: "' . $kk . '" is not found in the system, Please check for spelling errors.';
                                }
                            }
                        }

                        $rowCount++;
                    }
                }

                if (!empty($validStudentIds) && $errorInSavingRecords == 0) {
                    $this->Flash->success(
                        'Imported ' . $savedRecords . ($updatedRecords > 0 ? ' and updated ' . $updatedRecords : '') . ' ' . $service_type . ' OTP Passwords.'
                    );
                } else {
                    if ($savedRecords > 0 || $updatedRecords > 0) {
                        $this->Flash->success(
                            ($savedRecords > 0 ? ('Imported ' . $savedRecords . ($updatedRecords > 0 ? ' and updated ' . $updatedRecords : '')) : ($updatedRecords > 0 ? 'Updated ' . $updatedRecords : '')) . ' ' . $service_type . ' OTP Passwords ' . ($errorInSavingRecords != 0 ? ' with failed ' . $errorInSavingRecords . ' updates.' : '.')
                        );
                    } else {
                        $this->Flash->info(
                            'Nothing to update. Either all of ' . (isset($invalidStudentIds) && count(
                                $invalidStudentIds
                            ) > 0 ? count($invalidStudentIds) : (count($results_to_html_table) > 0 ? count(
                                $results_to_html_table
                            ) : ($data->sheets[0]['numRows'] - 1))) . ' students ' . $service_type . ' OTP password already exists in the system or you have errors in your uploaded Excel File.'
                        );
                    }
                }

                $this->set(
                    compact(
                        'invalidStudentIds',
                        'errors_to_correct',
                        'results_to_html_table',
                        'showPortal',
                        'showExamCenter'
                    )
                );
            }
        }
    }


    public function activateDeactivateProfile($parameters)
    {

        if (!empty($parameters)) {
            $student = $this->Student->find(
                'first',
                array('conditions' => array('Student.id' => $parameters), 'contain' => array('User'))
            );

            if (!empty($student) && !empty($student['User']['id'])) {
                $this->Student->User->id = $student['User']['id'];
                if ($student['User']['active'] == true) {
                    $this->Student->User->saveField('active', false);
                    $this->Flash->success(__('The student profile has been deactivated.'));
                } elseif ($student['User']['active'] == false) {
                    $this->Student->User->saveField('active', true);
                    $this->Flash->success(__('The student profile has been activated.'));
                }
            } else {
                $this->Flash->warning(
                    __(
                        'Username/password is not issued to the student until now and no account is found associated to the studunt. Thus, there is no need to activate/deactivate account.'
                    )
                );
            }

            $this->redirect(
                array('controller' => 'students', 'action' => 'student_academic_profile', $student['Student']['id'])
            );
        }
    }

    public function idcardPrint()
    {

        if (!empty($this->request->data) && !empty($this->request->data['getacceptedstudent'])) {
            $options = array();
            $limit = 100;

            if (!empty($this->request->data['Search']['academicyear'])) {
                $options['conditions']['AcceptedStudent.academicyear'] = $this->request->data['Search']['academicyear'];
            }

            if (!empty($this->request->data['Search']['department_id'])) {
                $college_id = explode('~', $this->request->data['Search']['department_id']);

                if (count($college_id) > 1) {
                    $options['conditions']['AcceptedStudent.college_id'] = $college_id[1];
                } else {
                    $options['conditions']['AcceptedStudent.department_id'] = $college_id;
                }
            }

            if (!empty($this->request->data['Search']['name'])) {
                $options['conditions']['AcceptedStudent.first_name LIKE '] = $this->request->data['Search']['name'] . '%';
            }

            if (!empty($this->request->data['Search']['program_type_id'])) {
                $options['conditions']['AcceptedStudent.program_type_id'] = $this->request->data['Search']['program_type_id'];
            }

            if (!empty($this->request->data['Search']['program_id'])) {
                $options['conditions']['AcceptedStudent.program_id'] = $this->request->data['Search']['program_id'];
            }

            if (!empty($this->request->data['Search']['limit'])) {
                $limit = $this->request->data['Search']['limit'];
            }


            if (!empty($options)) {
                $this->paginate = array(
                    'limit' => $limit,
                    'maxLimit' => $limit
                );

                $this->paginate['conditions'] = $options['conditions'];
                $this->Paginator->settings = $this->paginate;

                debug($this->Paginator->settings);
                $acceptedStudents = $this->Paginator->paginate('AcceptedStudent');

                if (empty($acceptedStudents)) {
                    $this->Flash->info(__('No result found with the given criteria.'));
                }

                $this->set(compact('acceptedStudents'));
            }
        }

        if (!empty($this->request->data) && !empty($this->request->data['printIDCard'])) {
            $studentsList = array();

            if (!empty($this->request->data['AcceptedStudent']['approve'])) {
                foreach ($this->request->data['AcceptedStudent']['approve'] as $key => $value) {
                    if ($value == 1) {
                        $university['University'] = ClassRegistry::init('University')->getAcceptedStudentUnivrsity(
                            $key
                        );

                        $studentsList[$key] = array_merge(
                            $this->Student->AcceptedStudent->find('first', array(
                                    'conditions' => array('AcceptedStudent.id' => $key),
                                    'contain' => array(
                                        'Student' => array('Attachment'),
                                        'College',
                                        'Department',
                                        'Program',
                                        'ProgramType'
                                    )
                                )
                            ),
                            $university
                        );
                    }
                }
            }

            if (empty($studentsList)) {
                $this->Flash->info(__('No student is found with the given criteria to print ID card '));
            } else {
                $this->set(compact('studentsList'));
                $this->response->type('application/pdf');
                $this->layout = '/pdf/default';
                $this->render('id_card_print_pdf');
                return;
            }
        }

        if ($this->role_id == ROLE_SYSADMIN) {
            $department_ids = ClassRegistry::init('Department')->find(
                'list',
                array('fields' => array('Department.id', 'Department.id'))
            );
            $departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(
                1,
                $department_ids,
                $this->college_ids,
                1
            );
        } else {
            if (!empty($this->department_ids) || !empty($this->college_ids)) {
                $departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(
                    1,
                    $this->department_ids,
                    $this->college_ids,
                    1
                );
            } else {
                if (!empty($this->department_id)) {
                    $departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(
                        1,
                        $this->department_id,
                        $this->college_id,
                        1
                    );
                } else {
                    $departments = array();
                }
            }
        }

        $this->set(compact('departments'));
    }

    public function cardPrintingReport()
    {

        if (
            isset($this->request->data['getReport'])
            || isset($this->request->data['getReportExcel'])
        ) {
            if ($this->request->data['Student']['report_type'] == 'IDPrintingCount' || $this->request->data['Student']['report_type'] == 'NOTPrinttedIDCount') {
                if ($this->request->data['Student']['report_type'] == 'NOTPrinttedIDCount') {
                    $this->request->data['Student']['printed_count'] = 0;
                    $headerLabel = $this->__label(
                        'Not Printed ID Card Printing Statistics  for ',
                        $this->request->data['Student']['acadamic_year'],
                        $this->request->data['Student']['program_type_id'],
                        $this->request->data['Student']['program_id'],
                        $this->request->data['Student']['department_id'],
                        $this->request->data['Student']['gender']
                    );
                } else {
                    $headerLabel = $this->__label(
                        'ID Card Printing Statistics  for ',
                        $this->request->data['Student']['acadamic_year'],
                        $this->request->data['Student']['program_type_id'],
                        $this->request->data['Student']['program_id'],
                        $this->request->data['Student']['department_id'],
                        $this->request->data['Student']['gender']
                    );
                }

                $distributionIDPrintingCount = $this->Student->getIDPrintCount($this->request->data['Student']);
                $years = $this->__years($this->request->data['Student']['department_id']);


                $this->set(
                    compact(
                        'distributionIDPrintingCount',
                        'years',
                        'headerLabel'
                    )
                );

                if ($this->request->data['Student']['report_type'] == 'IDPrintingCount' && isset($this->request->data['getReportExcel'])) {
                    $this->autoLayout = false;
                    $filename = 'ID Card Printing Statistics -' . date('Ymd H:i:s');

                    $this->set(
                        compact(
                            'distributionIDPrintingCount',
                            'years',
                            'headerLabel',
                            'filename'
                        )
                    );

                    $this->render('/Elements/reports/xls/id_printing_stats_xls');
                    return;
                }
            } else {
                if ($this->request->data['Student']['report_type'] == 'IDNotIssuedStudentList') {
                    $this->request->data['Student']['printed_count'] = 0;
                    $headerLabel = $this->__label(
                        'ID Card Not Issued List ',
                        $this->request->data['Student']['acadamic_year'],
                        $this->request->data['Student']['program_type_id'],
                        $this->request->data['Student']['program_id'],
                        $this->request->data['Student']['department_id'],
                        $this->request->data['Student']['gender']
                    );

                    $idNotPrintedStudentList = $this->Student->getIDPrintCount($this->request->data['Student'], 'list');
                    $years = $this->__years($this->request->data['Student']['department_id']);

                    $this->set(
                        compact(
                            'idNotPrintedStudentList',
                            'years',
                            'headerLabel'
                        )
                    );

                    if ($this->request->data['Student']['report_type'] == 'IDNotIssuedStudentList' && isset($this->request->data['getReportExcel'])) {
                        $this->autoLayout = false;
                        $filename = 'ID Card Not Issued List -' . date('Ymd H:i:s');

                        $this->set(
                            compact(
                                'idNotPrintedStudentList',
                                'years',
                                'headerLabel',
                                'filename'
                            )
                        );

                        $this->render('/Elements/reports/xls/id_not_issued_student_list_xls');
                        return;
                    }
                }
            }
        }
        $report_type_options = array(
            'Statistics' => array(
                'IDPrintingCount' => 'ID Print Count',
                'NOTPrinttedIDCount' => 'Not Printed ID Count',

            ),
            'List' => array(
                'IDNotIssuedStudentList' => 'ID Card Not Issued Student List',
                //'profileNotCompleted' => 'Profile Not Completed Student List',
            ),


        );
        $programs = $this->Student->Program->find('list');
        $program_types = $this->Student->ProgramType->find('list');

        //debug($academicStatuses);
        if ($this->role_id == ROLE_SYSADMIN) {
            $department_ids = $this->Student->Department->find(
                'list',
                array('fields' => array('Department.id', 'Department.id'))
            );
            $departments = $this->Student->Department->allDepartmentsByCollege2(1, $department_ids, $this->college_ids);
        } else {
            if (!empty($this->department_ids) || !empty($this->college_ids)) {
                $departments = $this->Student->Department->allDepartmentsByCollege2(
                    1,
                    $this->department_ids,
                    $this->college_ids
                );
            } else {
                if (!empty($this->department_id)) {
                    $departments = $this->Student->Department->allDepartmentsByCollege2(
                        1,
                        $this->department_id,
                        $this->college_id
                    );
                } else {
                    $departments = array();
                }
            }
        }

        $yearLevels = $this->Student->Section->YearLevel->distinct_year_level();
        $programs = array(0 => 'All Programs') + $programs;
        $program_types = array(0 => 'All Program Types') + $program_types;
        $departments = array(0 => 'All University Students') + $departments;
        $yearLevels = array(0 => 'All Year Level') + $yearLevels;

        $default_department_id = null;
        $default_program_id = null;
        $default_program_type_id = null;
        $default_year_level_id = null;
        $default_year_level_id = null;
        $default_region_id = null;
        $graph_type = array('bar' => 'Bar Chart', 'pie' => 'Pie Chart', 'line' => 'Line Chart');

        $this->set(
            compact(
                'departments',
                'academicStatuses',
                'graph_type',
                'default_region_id',
                'program_types',
                'programs',
                'default_program_type_id',
                'graph_type',
                'student_lists',
                'default_program_id',
                'default_department_id',
                'report_type_options',
                'default_year_level_id',
                'yearLevels'
            )
        );
    }

    private function __years($college_idds)
    {

        $college_id = explode('~', $college_idds);

        if (count($college_id) > 1) {
            $years = $this->Student->Section->YearLevel->find('list', array(
                'conditions' => array(
                    'YearLevel.department_id in (select id from departments where college_id=' . $college_id[1] . ' )'
                ),
                'fields' => array('YearLevel.name', 'YearLevel.name')
            ));
        } else {
            if (!empty($college_idds)) {
                $years = $this->Student->Section->YearLevel->find('list', array(
                    'conditions' => array(
                        'YearLevel.department_id' => $college_idds
                    ),
                    'fields' => array('YearLevel.name', 'YearLevel.name')
                ));
            } else {
                $years = $this->Student->Section->YearLevel->find(
                    'list',
                    array('fields' => array('YearLevel.name', 'YearLevel.name'))
                );
            }
        }
        return $years;
    }

    private function __label($prefix, $acadamic_year, $program_type_id, $program_id, $department_id, $gender)
    {

        $programs = $this->Student->Program->find('list');
        $programTypes = $this->Student->ProgramType->find('list');

        $label = '';
        $name = '';
        $label .= $prefix . ' ' . $acadamic_year . ' of ';

        if ($program_type_id == 0) {
            $label .= 'all program types ';
        } else {
            $label .= $programTypes[$program_type_id];
        }

        if ($program_id == 0) {
            $label .= 'undergraduate/graduate ';
        } else {
            $label .= 'in ' . $programs[$program_id];
            debug($program_id);
        }

        if ($gender == "all") {
            //$label.=' both gender';
        }

        $college_id = explode('~', $department_id);
        if (count($college_id) > 1) {
            $namee = $this->Student->College->find(
                'first',
                array('conditions' => array('College.id' => $college_id[1]), 'recursive' => -1)
            );
            $name .= ' ' . $namee['College']['name'];
        } else {
            if (!empty($department_id)) {
                $namee = $this->Student->Department->find(
                    'first',
                    array('conditions' => array('Department.id' => $department_id), 'recursive' => -1)
                );
                $name .= ' ' . $namee['Department']['name'];
            } else {
                if ($department_id == 0) {
                    $name .= 'for all department';
                }
            }
        }
        $label .= $name;
        return $label;
    }

    public function printRecord()
    {

        if ($this->Session->check('students')) {
            $display_field_student['Display'] = $this->Session->read('display_field_student');
            $students = $this->Session->read('students');

            if (!empty($students)) {
                $university['University'] = ClassRegistry::init('University')->getStudentUnivrsity(
                    $students[0]['Student']['id']
                );
                $colleges = $this->Student->College->find(
                    'first',
                    array(
                        'conditions' => array('College.id' => $students[0]['Student']['college_id']),
                        'recursive' => -1
                    )
                );
                $departments = $this->Student->Department->find(
                    'first',
                    array(
                        'conditions' => array('Department.id' => $students[0]['Student']['department_id']),
                        'recursive' => -1
                    )
                );
                $this->set(compact('students', 'display_field_student', 'university', 'departments', 'colleges'));
                $this->response->type('application/pdf');
                $this->layout = '/pdf/default';
                $this->render('print_students_list_pdf');
                return;
            }
        } else {
            $this->Flash->error(__('Could\'t read students data, Please refresh your page.'));
            $this->redirect(array('controller' => 'students', 'action' => 'index'));
        }
    }

    public function ajaxCheckEcardnumber()
    {

        $this->layout = 'ajax';
        $value = 'Invalid';

        if (!empty($this->data)) {
            if (!empty($this->data['Student']['ecardnumber'])) {
                $u = $this->Student->find(
                    'first',
                    array('conditions' => array('Student.ecardnumber' => $this->data['Student']['ecardnumber']))
                );
                if (empty($u)) {
                    $value = 'Valid';
                }
            }
        }
        $this->set(compact('value'));
    }

    public function pushStudentsCafeEntry()
    {

        $this->_mssql();
        if (!empty($this->request->data) && !empty($this->request->data['getStudent'])) {
            $options = array();
            $limit = 100;
            if (!empty($this->request->data['Search']['academicyear'])) {
                $options['conditions']['Student.admissionyear'] = $this->AcademicYear->get_academicYearBegainingDate(
                    $this->request->data['Search']['academicyear']
                );
            }
            if (
                !empty($this->request->data['Search']['currentAcademicYear'])
                && !empty($this->request->data['Search']['currentAcademicYear'])
            ) {
                $cafe = $this->request->data['Search']['cafe'];

                $options['conditions'][] = 'Student.id in (select student_id from course_registrations where academic_year="' . $this->request->data['Search']['currentAcademicYear'] . '" and semester="' . $this->request->data['Search']['semester'] . '" and cafeteria_consumer=' . $cafe . ') and Student.ecardnumber is not null';
            }
            if (!empty($this->request->data['Search']['department_id'])) {
                $college_id = explode('~', $this->request->data['Search']['department_id']);
                if (count($college_id) > 1) {
                    $options['conditions']['Student.college_id'] = $college_id[1];
                } else {
                    $options['conditions']['Student.department_id'] = $college_id;
                }
            }

            if (!empty($this->request->data['Search']['name'])) {
                $options['conditions']['Student.first_name LIKE '] = $this->request->data['Search']['name'] . '%';
            }

            if (!empty($this->request->data['Search']['program_type_id'])) {
                $options['conditions']['Student.program_type_id'] = $this->request->data['Search']['program_type_id'];
            }
            if (!empty($this->request->data['Search']['program_id'])) {
                $options['conditions']['Student.program_id'] = $this->request->data['Search']['program_id'];
            }
            if (!empty($this->request->data['Search']['limit'])) {
                $limit = $this->request->data['Search']['limit'];
            }


            if (!empty($options)) {
                $this->paginate = array(
                    'limit' => $limit,
                    'maxLimit' => $limit
                );
                $this->paginate['conditions'] = $options['conditions'];
                $this->Paginator->settings = $this->paginate;

                $students = $this->Paginator->paginate('Student');
                if (empty($students)) {
                    $this->Session->setFlash(
                        '<span></span>' . __('No result found.'),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                }
                $this->set(compact('students'));
            }
        }

        if (!empty($this->request->data) && !empty($this->request->data['pushStudentsToCafeGate'])) {
            $studentsList = array();

            foreach ($this->request->data['Student']['approve'] as $key => $value) {
                if ($value == 1) {
                    $studentsList = 1;
                    $studentInfo = $this->Student->find(
                        'first',
                        array('conditions' => array('Student.id' => $key), 'contain' => array('College'))
                    );
                    $mealHallAssigned = $this->Student->MealHallAssignment->find(
                        'first',
                        array(
                            'conditions' => array(
                                'MealHallAssignment.student_id' => $key,
                                'MealHallAssignment.academic_year' => $this->request->data['Search']['currentAcademicYear']
                            ),
                            'recursive' => -1
                        )
                    );

                    $studentQuery = "SELECT TOP(1) SLN_Employee FROM dbo.MSTR_Employee AS S WHERE Employee_Code='" . $studentInfo['Student']['studentnumber'] . "'";
                    //[Access_Level4]
                    $resultSetReturn = $this->_mssql($studentQuery);

                    while ($row = mssql_fetch_assoc($resultSetReturn)) {
                        $studentResult[0][0]['SLN_Employee'] = $row['SLN_Employee'];
                    }
                    mssql_free_result($resultSetReturn);
                    if (!empty($studentResult[0][0]['SLN_Employee'])) {
                        // does the student exist ?

                        $cardSQL = "SELECT TOP(1) SLN_Employee FROM ACS_Cards_Info AS S WHERE SLN_Employee='" . $studentResult[0][0]['SLN_Employee'] . "'";

                        //	$cardResult = $db->query($cardSQL);
                        $cardResultSet = $this->_mssql($cardSQL);
                        while ($row = mssql_fetch_assoc($resultSetReturn)) {
                            $cardResult[0][0]['SLN_Employee'] = $row['SLN_Employee'];
                        }
                        mssql_free_result($cardResultSet);
                        if ($this->request->data['Search']['allow'] == 1) {
                            $accessLevel4Cafe = isset($mealHallAssigned['MealHallAssignment']['meal_hall_id']) ? $mealHallAssigned['MealHallAssignment']['meal_hall_id'] : 0;
                        } else {
                            $accessLevel4Cafe = 0;
                        }


                        if (!empty($cardResult[0][0]['SLN_Employee'])) {
                            $cafeAccessSQL = "UPDATE ACS_Cards_Info
SET Access_Level4 = " . $accessLevel4Cafe . " WHERE SLN_Employee=" . $cardResult[0][0]['SLN_Employee'] . "";
                        } else {
                            // do inseration to ess db
                            $slnEmployee = $studentResult[0][0]['SLN_Employee'];
                            $cardNumber = $studentInfo['Student']['ecardnumber'];
                            $facilityID = $studentInfo['College']['campus_id'];
                            $accessLevel1CommonGate = 13;
                            $accessLevel2AllStudentGate = 9;
                            $accessLevel3AllLibGate = 5;

                            $accessLevel5 = 0;
                            $accessLevel6 = 0;
                            $accessLevel7 = 0;
                            $accessLevel8 = 0;

                            $cafeAccessSQL = "INSERT INTO ACS_Cards_Info(SLN_Employee,Card_Number,Facility_ID,Access_Level1,Access_Level2,Access_Level3,Access_Level4,Access_Level5,Access_Level6,Access_Level7,Access_Level8) VALUES ('$slnEmployee','$cardNumber','$facilityID','$accessLevel1CommonGate','$accessLevel2AllStudentGate','$accessLevel3AllLibGate','$accessLevel4Cafe','$accessLevel5','$accessLevel6','$accessLevel7','$accessLevel8')";
                        }
                        $cafeQuery = $this->_mssql($cafeAccessSQL);
                        debug($cafeQuery);
                    }
                }
            }

            if (empty($studentsList)) {
                $this->Session->setFlash(
                    '<span></span>' . __('Please select the students you would like to allow/deny cafe gate.'),
                    'default',
                    array('class' => 'info-box info-message')
                );
            } else {
                $this->Session->setFlash(
                    '<span></span>' . __(
                        'The selected students has been allowed/denied cafe gate and update has been propagated to devices.'
                    ),
                    'default',
                    array('class' => 'success-box success-message')
                );
            }
            mssql_close($this->conn);
        }


        if ($this->role_id == ROLE_SYSADMIN) {
            $department_ids = ClassRegistry::init('Department')->find('list', array(
                'fields' =>
                    array('Department.id', 'Department.id')
            ));

            $departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(
                1,
                $department_ids,
                $this->college_ids
            );
        } else {
            if (
                !empty($this->department_ids) ||
                !empty($this->college_ids)
            ) {
                $departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(
                    1,
                    $this->department_ids,
                    $this->college_ids
                );
            } else {
                if (!empty($this->department_id)) {
                    $departments = ClassRegistry::init('Department')->allDepartmentsByCollege2(
                        1,
                        $this->department_id,
                        $this->college_id
                    );
                } else {
                    $departments = array();
                }
            }
        }
        $this->set(compact('departments'));
    }

    public function change($id = null)
    {

        if (!empty($this->request->data)) {
            $data = $this->request->data;
            $this->request->data = $this->Student->find('first', array(
                'conditions' => array('Student.id' => $this->student_id),
                'contain' => array('Contact')
            ));
            $this->request->data['Student']['ecardnumber'] = $data['Student']['ecardnumber'];
            $this->request->data['Student']['phone_mobile'] = $data['Student']['phone_mobile'];
            //$this->request->data['Contact'][0]['phone_mobile']=$data['Contact'][0]['phone_mobile'];

        }


        if (!empty($this->request->data) && $this->Student->save($this->request->data)) {
            $this->Session->setFlash(
                '<span></span>' . __('The ecardnumber and mobile phone number was updated successfully'),
                'default',
                array('class' => 'success-box success-message')
            );
            $this->redirect('/');
        } else {
            if (!empty($this->request->data)) {
                $this->Session->setFlash(
                    '<span></span>' . __('Your data could not be saved.Please, try again.', true),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
        }
        $this->request->data = $this->Student->find('first', array(
            'conditions' => array('Student.id' => $this->student_id),
            'contain' => array(
                'Contact',
                'Attachment'
            )
        ));
    }


    public function deleteStudentFromGraduateListForCorrection($student_id)
    {

        if ($student_id) {
            $this->Student->id = $student_id;

            if ($this->Student->saveField('graduated', 0)) {
                /* $deleteromGraduateList = "DELETE FROM `graduate_lists` WHERE  student_id = $student_id";
				$dgl = $this->Student->GraduateList->query($deleteromGraduateList);

				$deleteromSenateList = "DELETE FROM `senate_lists` WHERE  student_id = $student_id";
				$dsl = $this->Student->SenateList->query($deleteromSenateList); */


                $graduateListID = $this->Student->GraduateList->field(
                    'GraduateList.id',
                    array('GraduateList.student_id' => $student_id)
                );
                debug($graduateListID);

                $senateListID = $this->Student->SenateList->field(
                    'SenateList.id',
                    array('SenateList.student_id' => $student_id)
                );
                debug($senateListID);

                if ($this->Student->GraduateList->delete($graduateListID) && $this->Student->SenateList->delete(
                        $senateListID
                    )) {
                    $this->Flash->success('The student is now deleted from Senate and Graduation Lists');
                }
            }

            $this->redirect(array('controller' => 'students', 'action' => 'student_academic_profile', $student_id));
        }
    }

    public function updateKohaDb()
    {

        if (!empty($this->request->data) && !empty($this->request->data['updateKohaDB'])) {
            $status = $this->Student->extendKohaBorrowerExpireDate($this->request->data['AcceptedStudent']['approve']);

            if ($status) {
                $this->Session->setFlash(
                    __('<span></span>You have successfully update book borrower database.'),
                    'default',
                    array('class' => 'success-box success-message')
                );
            }
        }
        debug($this->request->data);
        if (!empty($this->request->data) && !empty($this->request->data['getacceptedstudent'])) {
            if (!empty($this->request->data['Search']['college_id'])) {
                $conditions['AcceptedStudent.college_id'] = $this->request->data['Search']['college_id'];
            }
            if (!empty($this->request->data['Search']['name'])) {
                $conditions['AcceptedStudent.first_name like '] = $this->request->data['Search']['name'] . '%';
            }
            if (!empty($this->request->data['Search']['academicyear'])) {
                $conditions['AcceptedStudent.academicyear like '] = $this->request->data['Search']['academicyear'] . '%';
            }
            if (!empty($this->request->data['Search']['program_id'])) {
                $conditions['AcceptedStudent.program_id'] = $this->request->data['Search']['program_id'];
            }
            if (!empty($this->request->data['Search']['program_type_id'])) {
                $conditions['AcceptedStudent.program_type_id'] = $this->request->data['Search']['program_type_id'];
            }
            if (!empty($conditions)) {
                if (isset($this->request->data['Search']['limit'])) {
                    $limit = $this->request->data['AcceptedStudent']['limit'];
                } else {
                    $limit = 1800;
                }
                $acceptedStudentIds = $this->Student->AcceptedStudent->find('list', array(
                    'conditions' => $conditions,
                    'limit' => $limit,
                    'maxLimit' => $limit,
                    'fields' => array(
                        'AcceptedStudent.id',
                        'AcceptedStudent.id'
                    )
                ));

                $students = ClassRegistry::init('StudentExamStatus')->getMostRecentStudentStatusForKoha(
                    $acceptedStudentIds,
                    1
                );


                if (!empty($students)) {
                    $acceptedStudents = $students;
                    $this->set(compact('acceptedStudents'));
                } else {
                    $this->Session->setFlash(
                        __(
                            '<span></span>No data is found with your search criteria that needs update, either all students has been updated or they are not qualified for borrower extension.'
                        ),
                        'default',
                        array('class' => 'info-box info-message')
                    );
                }
            }
            //debug($conditions);

        }
        // display the right department and college based on the privilage of registrar users
        $colleges = $this->Student->College->find('list');
        $departments = $this->Student->Department->find('list');
        $this->set(compact('colleges', 'departments'));

        $programs = $this->Student->Program->find('list');
        //  $programTypes =$this->Student->ProgramType->find('list');
        $this->set(
            compact(
                'programs',
                'programTypes',
                'colleges',
                'departments'
            )
        );
    }

    public function updateLmsDb()
    {

        if (!empty($this->request->data) && !empty($this->request->data['deleteLMSDB'])) {
            $department_ids = $this->Student->Department->find('list', array(
                'conditions' => array('Department.college_id' => $this->request->data['Search']['college_id']),
                'fields' => array('Department.id', 'Department.id')
            ));

            $db = ConnectionManager::getDataSource('lms');
            // find published courses and update the courses table
            $publishedCourseListIds = ClassRegistry::init('PublishedCourse')->find(
                'list',
                array(
                    'conditions' => array(
                        'PublishedCourse.semester' => $this->request->data['Search']['semester'],
                        'PublishedCourse.academic_year' => $this->request->data['Search']['academicyear'],
                        'PublishedCourse.department_id' => $department_ids,
                        'PublishedCourse.program_id' => $this->request->data['Search']['program_id'],
                        'PublishedCourse.program_type_id' => $this->request->data['Search']['program_type_id'],
                    ),
                    'fields' => array(
                        'PublishedCourse.id',
                        'PublishedCourse.id'

                    )
                )
            );
            $count = 0;
            if (isset($publishedCourseListIds) && !empty($publishedCourseListIds)) {
                $deleteCourses = "DELETE FROM `enrollment` WHERE  course_id in (" . implode(
                        ",",
                        $publishedCourseListIds
                    ) . ")";
                $d = $db->query($deleteCourses);
                $count = count($publishedCourseListIds);
            }
            //DELETE FROM `courses` WHERE `courses`.`id` = 2

            if (isset($publishedCourseListIds) && !empty($publishedCourseListIds)) {
                $deleteCourses = "DELETE FROM `courses` WHERE  courseid in (" . implode(
                        ",",
                        $publishedCourseListIds
                    ) . ")";
                $d = $db->query($deleteCourses);
            }

            if ($count > 0) {
                $this->Session->setFlash(
                    __('<span></span>You have successfully deleted ' . $count . ' courses from  LMS system .'),
                    'default',
                    array('class' => 'success-box success-message')
                );
            }
        }

        if (!empty($this->request->data) && !empty($this->request->data['updateLMSDB'])) {
            debug($this->request->data);
            $department_ids = $this->Student->Department->find('list', array(
                'conditions' => array('Department.college_id' => $this->request->data['Search']['college_id']),
                'fields' => array('Department.id', 'Department.id')
            ));
            debug($department_ids);
            $db = ConnectionManager::getDataSource('lms');
            // find published courses and update the courses table
            $publishedCourseList = ClassRegistry::init('PublishedCourse')->find(
                'all',
                array(
                    'conditions' => array(
                        'PublishedCourse.semester' => $this->request->data['Search']['semester'],
                        'PublishedCourse.academic_year' => $this->request->data['Search']['academicyear'],
                        'PublishedCourse.department_id' => $department_ids,
                        'PublishedCourse.program_id' => $this->request->data['Search']['program_id'],
                        'PublishedCourse.program_type_id' => $this->request->data['Search']['program_type_id'],
                    ),
                    'contain' => array(
                        'Course',
                        'CourseInstructorAssignment' => array(
                            'Staff' => array(
                                'User',
                                'Department',
                                'College',
                                'City',
                                'Country'
                            )
                        ),
                    )
                )
            );
            debug($publishedCourseList);
            $count = 0;
            foreach ($publishedCourseList as $pk => $pv) {
                //feed course list
                if (isset($pv['PublishedCourse']['id']) && !empty($pv['PublishedCourse']['id'])) {
                    $count++;
                    // course inseration
                    $sqlCourseRecorded = "SELECT count(*),courseid FROM  courses as course
							where courseid=" . $pv['PublishedCourse']['id'] . "";
                    $resultCourseRecorded = $db->query($sqlCourseRecorded);
                    if ($resultCourseRecorded[0][0]['count(*)'] == 0) {
                        //create the course if not existed.
                        $fullname = $pv['Course']['course_title'] . ' ' . $pv['PublishedCourse']['academic_year'] . ' ' . $pv['PublishedCourse']['semester'];
                        $shortname = $pv['PublishedCourse']['id'];
                        $pid = $pv['PublishedCourse']['id'];
                        $categoryid = $this->request->data['Search']['college_id'];
                        $ac_year = $pv['PublishedCourse']['academic_year'];
                        $semester = $pv['PublishedCourse']['semester'];

                        $createCourseSql = "INSERT INTO  `courses` (`id`,
							   `fullname`,`shortname`,`courseid`,`categoryid`,
							   `ac_year`,`semester`) VALUES (NULL,
							   \"$fullname\",\"$shortname\",\"$pid\",\"$categoryid\",
							   \"$ac_year\",\"$semester\")";
                        $resultinsert = $db->query($createCourseSql);
                    } else {
                        // nothing to do for now but we need to update course detail required
                    }
                }
                //instructor enrollement done
                if (
                    isset($pv['CourseInstructorAssignment']) &&
                    !empty($pv['CourseInstructorAssignment'])
                ) {
                    //check if the instructor is primary and enroll it

                    foreach ($pv['CourseInstructorAssignment'] as $cia => $civ) {
                        //is that primary instructor
                        debug($civ);
                        if (
                            $civ['isprimary'] && isset($civ['Staff']['User']['username'])
                            && !empty($civ['Staff']['User']['username'])
                        ) {
                            $sqlUserTeacher = "SELECT count(*),username FROM  users as user
							where username='" . $civ['Staff']['User']['username'] . "'";
                            $resultUserTeacher = $db->query($sqlUserTeacher);

                            if ($resultUserTeacher[0][0]['count(*)'] == 0) {
                                // create user

                                $username = strtolower($civ['Staff']['User']['email']);
                                $password = $civ['Staff']['User']['password'];
                                $firstname = $civ['Staff']['first_name'];
                                $middlename = $civ['Staff']['middle_name'];
                                $lastname = $civ['Staff']['last_name'];
                                $email = $civ['Staff']['email'];
                                $city = $civ['Staff']['City']['name'];
                                $country = $civ['Staff']['Country']['name'];
                                $institution = $civ['Staff']['College']['name'];
                                $department = $civ['Staff']['Department']['name'];
                                $mobile = $civ['Staff']['phone_mobile'];
                                $phone = $civ['Staff']['phone_office'];
                                $amharicfirstname = $civ['Staff']['first_name'];
                                $amhariclastname = $civ['Staff']['last_name'];

                                $address = $civ['Staff']['address'];
                                if (
                                    isset($firstname) && !empty($firstname)
                                    && isset($middlename) && !empty($middlename)
                                    && isset($username) && !empty($username)
                                ) {
                                    $createUsersSql = "INSERT INTO  `users` (`id`,`username`,`password`,
							   `firstname`,`middlename`,`lastname`,`email`,`city`,`country`
							   ,`idnumber`,`institution`,`department`,
							   `mobile`,`phone`,
							   `amharicfirstname`,
							   `amhariclastname`,

							   `address`) VALUES (NULL,
							   \"$username\",\"$password\",
							   \"$firstname\",\"$middlename\",\"$lastname\",\"$email\",
							   \"$city\",\"$country\",\"$username\",
							\"$institution\",\"$department\",\"$mobile\",\"$phone\",
							\"$amharicfirstname\",
							\"$amhariclastname\",

							\"$address\"
							)";
                                    $resultinsert = $db->query($createUsersSql);
                                }
                            }
                            debug($civ);
                            //Is s/he already enrolled
                            $courseId = $civ['published_course_id'];
                            $idNumber = strtolower($civ['Staff']['User']['email']);
                            $role_name = 'editingteacher';
                            $ac_year = $civ['academic_year'];
                            $semester = $civ['semester'];
                            debug($courseId);
                            $sqlEnrollTeacher = "SELECT count(*),
							course_id FROM  enrollment as enroll
							where course_id=" . $civ['published_course_id'] . "
							and id_number='" . $civ['Staff']['User']['username'] . "'
							and role_name='editingteacher'";
                            $resultEnrollTeacher = $db->query($sqlEnrollTeacher);
                            //debug($resultEnrollTeacher);
                            $insertToEnrollementTeacher = "INSERT INTO  `enrollment` (`id`,`course_id`,`id_number`,`role_name`,`ac_year`,`semester`) VALUES (NULL,
							\"$courseId\",\"$idNumber\",
							\"$role_name\",\"$ac_year\",\"$semester\")";

                            debug($resultEnrollTeacher);

                            //$resultinsert = $db->query($insertToEnrollementTeacher);

                            if ($resultEnrollTeacher[0][0]['count(*)'] == 0) {
                                // never enrolled for the course as teacher
                                $resultTeachers = $db->query($insertToEnrollementTeacher);
                            } else {
                                //update the new instructor
                            }
                        }
                    }
                }


                //student enrollement

                $registeredStudentList = ClassRegistry::init('CourseRegistration')->find(
                    'all',
                    array(
                        'conditions' => array(
                            'CourseRegistration.published_course_id' =>
                                $pv['PublishedCourse']['id'],
                            'CourseRegistration.id not in (select course_registration_id from course_drops)'
                        ),
                        'contain' => array('Student' => array('User', 'Department', 'College', 'City', 'Country'))
                    )
                );
                if (
                    isset($registeredStudentList) &&
                    !empty($registeredStudentList)
                ) {
                    foreach ($registeredStudentList as $regk => $regv) {
                        $sqlUserStudent = "SELECT count(*),idnumber FROM  users as user
							where idnumber='" . $regv['Student']['studentnumber'] . "'";
                        $resultUserStudent = $db->query($sqlUserStudent);

                        if ($resultUserStudent[0][0]['count(*)'] == 0) {
                            // create user

                            $username = strtolower(str_replace('/', '.', $regv['Student']['User']['username']));
                            $password = $regv['Student']['User']['password'];
                            $firstname = $regv['Student']['first_name'];
                            $lastname = $regv['Student']['last_name'];
                            if (isset($regv['Student']['email']) && !empty($regv['Student']['email'])) {
                                $email = strtolower($regv['Student']['User']['email']);
                            } else {
                                $userId = strtolower(str_replace('/', '-', $regv['Student']['studentnumber']));
                                $email = $userId . INSTITUTIONAL_EMAIL_SUFFIX;
                            }
                            $studentnumber = $regv['Student']['studentnumber'];
                            //$email = $regv['Student']['email'];
                            $city = $regv['Student']['City']['name'];
                            $country = $regv['Student']['Country']['name'];
                            $institution = $regv['Student']['College']['name'];
                            $department = $regv['Student']['Department']['name'];
                            $mobile = $regv['Student']['phone_mobile'];
                            $phone = $regv['Student']['phone_home'];
                            $amharicfirstname = $regv['Student']['amharic_first_name'];
                            $amhariclastname = $regv['Student']['amharic_last_name'];
                            $middlename = $regv['Student']['middle_name'];
                            $address = $regv['Student']['address1'];
                            if (
                                isset($firstname) && !empty($firstname)
                                && isset($middlename) && !empty($middlename)
                            ) {
                                $createUsersStudentSql = "INSERT INTO  `users` (`id`,`username`,`password`,
							   `firstname`,`middlename`,`lastname`,`email`,`city`,`country`
							   ,`idnumber`,`institution`,`department`,
							   `mobile`,`phone`,
							   `amharicfirstname`,
							   `amhariclastname`,

							   `address`) VALUES (NULL,
							   \"$username\",\"$password\",
							   \"$firstname\",\"$middlename\",\"$lastname\",\"$email\",
							   \"$city\",\"$country\",\"$studentnumber\",
							\"$institution\",\"$department\",\"$mobile\",\"$phone\",
							\"$amharicfirstname\",
							\"$amhariclastname\",
							\"$address\"
							)";
                                $resultinsert = $db->query($createUsersStudentSql);
                            }
                        }

                        //Is s/he already enrolled
                        $courseId = $regv['CourseRegistration']['published_course_id'];
                        $ac_year = $regv['CourseRegistration']['academic_year'];
                        $semester = $regv['CourseRegistration']['semester'];
                        $idNumber = $regv['Student']['studentnumber'];
                        $role_name = 'student';
                        $sqlEnrollStudent = "SELECT count(*),
							course_id FROM  enrollment as enroll
							where course_id=" . $regv['CourseRegistration']['published_course_id'] . "
							and id_number='" . $regv['Student']['User']['username'] . "'
							and role_name='student'";
                        $resultEnrollStudent = $db->query($sqlEnrollStudent);

                        if ($resultEnrollStudent[0][0]['count(*)'] == 0) {
                            // never enrolled for the course as teacher
                            $insertToEnrollementStudent = "INSERT INTO  `enrollment` (`id`,`course_id`,`id_number`,`role_name`,`ac_year`,`semester`) VALUES (NULL,
								\"$courseId\",\"$idNumber\",
								\"$role_name\",\"$ac_year\",\"$semester\")";

                            $resultinsertS = $db->query($insertToEnrollementStudent);
                        } else {
                            //update the new student enrollement
                        }
                    }
                }
                $courseAddedStudentList = ClassRegistry::init('CourseAdd')->find(
                    'all',
                    array(
                        'conditions' => array(
                            'CourseAdd.published_course_id' =>
                                $pv['PublishedCourse']['id']
                        ),
                        'contain' => array('Student' => array('User', 'Department', 'College', 'City', 'Country'))
                    )
                );

                if (
                    isset($courseAddedStudentList) &&
                    !empty($courseAddedStudentList)
                ) {
                    foreach ($courseAddedStudentList as $addk => $addv) {
                        $sqlUserStudent = "SELECT count(*),idnumber FROM  users as user
							where idnumber='" . $addv['Student']['studentnumber'] . "'";
                        $resultUserStudent = $db->query($sqlUserStudent);

                        if ($resultUserStudent[0][0]['count(*)'] == 0) {
                            // create user

                            $username = strtolower(str_replace('/', '.', $addv['Student']['User']['username']));
                            $password = $addv['Student']['User']['password'];
                            $firstname = $addv['Student']['first_name'];
                            $lastname = $addv['Student']['last_name'];
                            if (isset($addv['Student']['email']) && !empty($addv['Student']['email'])) {
                                $email = strtolower($addv['Student']['User']['email']);
                            } else {
                                $userId = strtolower(str_replace('/', '-', $addv['Student']['studentnumber']));
                                $email = $userId . INSTITUTIONAL_EMAIL_SUFFIX;
                            }
                            $studentnumber = $addv['Student']['studentnumber'];
                            //$email = $regv['Student']['email'];
                            if (
                                isset($addv['Student']['City']['name'])
                                && !empty($addv['Student']['City']['name'])
                            ) {
                                $city = $addv['Student']['City']['name'];
                            } else {
                                $city = "";
                            }

                            if (
                                isset($addv['Student']['Country']['name'])
                                && !empty($addv['Student']['Country']['name'])
                            ) {
                                $country = $addv['Student']['Country']['name'];
                            } else {
                                $country = "";
                            }


                            $institution = $addv['Student']['College']['name'];


                            $department = $addv['Student']['Department']['name'];
                            $mobile = $addv['Student']['phone_mobile'];
                            $phone = $addv['Student']['phone_home'];
                            $amharicfirstname = $addv['Student']['amharic_first_name'];
                            $amhariclastname = $addv['Student']['amharic_last_name'];
                            $middlename = $addv['Student']['amharic_middle_name'];
                            $address = $addv['Student']['address1'];
                            if (
                                isset($firstname) && !empty($firstname)
                                && isset($middlename) && !empty($middlename)
                                && isset($username) && !empty($username)
                            ) {
                                $createUsersStudentSql = "INSERT INTO  `users` (`id`,`username`,`password`,
							   `firstname`,`middlename`,`lastname`,`email`,`city`,`country`
							   ,`idnumber`,`institution`,`department`,
							   `mobile`,`phone`,
							   `amharicfirstname`,
							   `amhariclastname`,

							   `address`) VALUES (NULL,
							   \"$username\",\"$password\",
							   \"$firstname\",\"$middlename\",\"$lastname\",\"$email\",
							   \"$city\",\"$country\",\"$studentnumber\",
							\"$institution\",\"$department\",\"$mobile\",\"$phone\",
							\"$amharicfirstname\",
							\"$amhariclastname\",
							\"$address\"
							)";
                                $resultinsert = $db->query($createUsersStudentSql);
                            }
                        }

                        //Is s/he already enrolled
                        $courseId = $addv['CourseAdd']['published_course_id'];
                        $ac_year = $addv['CourseAdd']['academic_year'];
                        $semester = $addv['CourseAdd']['semester'];
                        $idNumber = $addv['Student']['studentnumber'];
                        $role_name = 'student';
                        $sqlEnrollStudent = "SELECT count(*),
							course_id FROM  enrollment as enroll
							where course_id=" . $addv['CourseAdd']['published_course_id'] . "
							and id_number='" . $addv['Student']['User']['username'] . "'
							and role_name='student'";
                        $resultEnrollStudent = $db->query($sqlEnrollStudent);

                        if ($resultEnrollStudent[0][0]['count(*)'] == 0) {
                            // never enrolled for the course as teacher
                            $insertToEnrollementStudent = "INSERT INTO  `enrollment` (`id`,`course_id`,`id_number`,`role_name`,`ac_year`,`semester`) VALUES (NULL,
								\"$courseId\",\"$idNumber\",
								\"$role_name\",\"$ac_year\",\"$semester\")";

                            $resultinsertS = $db->query($insertToEnrollementStudent);
                        } else {
                            //update the new student enrollement
                        }
                    }
                }
            }
            if ($count > 0) {
                $this->Session->setFlash(
                    __('<span></span>You have successfully update ' . $count . ' courses from SMIS to  LMS system .'),
                    'default',
                    array('class' => 'success-box success-message')
                );
            } else {
                $this->Session->setFlash(
                    __('<span></span>There is no course to synchronize  from SMIS to  LMS system .'),
                    'default',
                    array('class' => 'info-box info-message')
                );
            }
        }


        // display the right department and college based on the privilage of registrar users
        $colleges = $this->Student->College->find('list');
        $departments = $this->Student->Department->find('list');
        $this->set(compact('colleges', 'departments'));

        $programs = $this->Student->Program->find('list');
        //  $programTypes =$this->Student->ProgramType->find('list');
        $this->set(
            compact(
                'programs',
                'programTypes',
                'colleges',
                'departments'
            )
        );
    }

    private function _mssql($query)
    {

        //connect to the database
        $this->conn = mssql_connect($this->config['host'], $this->config['login'], $this->config['password']);
        $selectDB = mssql_select_db($this->config['database'], $this->conn);
        $result = mssql_query($query);
        return $result;
    }

    private function _config()
    {

        $this->config['host'] = '10.144.5.210';
        $this->config['login'] = 'sa';
        $this->config['password'] = 'admin@123';
        $this->config['database'] = 'ESS';
    }
}
