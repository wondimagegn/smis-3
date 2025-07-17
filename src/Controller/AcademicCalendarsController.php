<?php
namespace App\Controller;


use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
class AcademicCalendarsController extends AppController
{

    public $name = 'AcademicCalendars';
    public $paginate = [];
    public $menuOptions = [
        'parent' => 'dashboard',
        'exclude' => [
            'autoSaveExtension',
            'getDepartmentsThatHaveTheSelectedProgram'
        ],
        'alias' => [
            'index' => 'View All Academic Calendars',
            'add' => 'Set Academic Calendar',
            'extending_calendar' => 'Extend Academic Calendar',
        ]
    ];

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('AcademicYear');
        $this->loadComponent('Paginator'); // Ensure Paginator is loaded

        $this->viewBuilder()->setHelpers(['DatePicker']);
    }

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->Auth->allow([
            'autoSaveExtension',
            'extendingCalendar',
            'getDepartmentsThatHaveTheSelectedProgram'
        ]);

    }

    public function beforeRender(Event $event)
    {
        parent::beforeRender($event);

        $acyearArrayData = $this->AcademicYear->academicYearInArray(date('Y') - 10, date('Y'));
        $defaultAcademicYear = $this->AcademicYear->currentAcademicYear();

        if (!empty($acyearArrayData)) {
            foreach ($acyearArrayData as $key => $value) {
                if ($value == $defaultAcademicYear) {
                    $defaultAcademicYear = $key;
                    break;
                }
            }
        }

        $this->set(compact('acyearArrayData', 'defaultAcademicYear'));
        unset($this->request->getData()['User']['password']);
    }
    public function index()
    {

        $this->loadModel('AcademicCalendars');
        $currentAcyAndSemester = $this->AcademicYear->currentAcyAndSemester();
        $this->initSearchCalendar();

        $paginationConfig = [
            'contain' => ['Programs', 'ProgramTypes','ExtendingAcademicCalendars.Departments',
                'ExtendingAcademicCalendars.Programs',
                'ExtendingAcademicCalendars.ProgramTypes'],
            'order' => [
                'created' => 'DESC',
                'academic_year' => 'DESC',
                'semester' => 'DESC',
                'program_id' => 'ASC',
                'program_type_id' => 'ASC'
            ],
            'limit' => 50
        ];
        $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
        $yearLevelsTable = TableRegistry::getTableLocator()->get('YearLevels');


        $options = [];
        $requestData = $this->request->getData();

        if ($this->request->is('post') && isset($requestData['viewAcademicCalendar'])) {
            $this->initClearSessionFilters();
            $this->initSearchCalendar();
        }

        foreach (['program_id', 'program_type_id', 'academic_year', 'semester'] as $filter) {
            if (!empty($requestData['Search'][$filter])) {
                $options[$filter] = $requestData['Search'][$filter];
            }
        }

        if (!empty($requestData['Search']['department_id'])) {
            $options['department_id LIKE'] = '%"' . $requestData['Search']['department_id'] . '"%';
        }
        if (!empty($requestData['Search']['year_level_id'])) {
            $options['year_level_id LIKE'] = '%"' . $requestData['Search']['year_level_id'] . '"%';
        }

        if (empty($options)) {
            $options['academic_year'] = $currentAcyAndSemester['academic_year'];
            if ($this->request->getSession()->read('Auth.User.role_id') !== Configure::read('ROLE_REGISTRAR')) {
                $options['semester'] = $currentAcyAndSemester['semester'];
            }
        }

        $this->paginate = $paginationConfig;
        $query = $this->AcademicCalendars->find()
            ->contain([
                'Programs',
                'ProgramTypes',
                'ExtendingAcademicCalendars.Departments',
                'ExtendingAcademicCalendars.Programs',
                'ExtendingAcademicCalendars.ProgramTypes'
            ])
            ->where($options); // Apply conditions

        $academicCalendars = $this->paginate($query);

        if (empty($academicCalendars)) {
            $this->Flash->info('There is no academic calendar defined in the system with the given criteria.');
        }

        foreach ($academicCalendars as $calendar) {
            $calendar->department_id = unserialize($calendar->department_id);
            // Filter only numeric values to prevent conversion errors
            $numericDepartmentIds = array_filter( $calendar->department_id, function ($id) {
                return is_numeric($id); // Ensure we only pass integers to the query
            });

            if (!empty($numericDepartmentIds)) {

                $calendar->department_name = implode(', ',
                    $departmentsTable->find('list',[
                        'keyField' => 'id',
                        'valueField' => 'name'
                    ])->where(['id IN' => $numericDepartmentIds])->toArray()
                );
            } else {
                $calendar->department_name = 'N/A';
            }
            $calendar->year_level_id = unserialize($calendar->year_level_id);
            $yearLevelIds = array_filter( $calendar->year_level_id, function ($id) {
                return is_numeric($id); // Ensure we only pass integers to the query
            });
            if (!empty($yearLevelIds)) {
                $calendar->year_level_name = implode(', ',
                    $yearLevelsTable->find('list',[
                        'keyField' => 'id',
                        'valueField' => 'name'
                    ])->where(['id IN' => $yearLevelIds])->toArray()
                );
            } else {
                $calendar->year_level_name = 'N/A';
            }

        }

        $programs = $this->AcademicCalendars->Programs->find('list', ['conditions' => ['active' => 1]])->toArray();
        $programTypes = $this->AcademicCalendars->ProgramTypes->find('list', ['conditions' => ['active' => 1]])->toArray();
        $departments = $colleges = [];

        if ($this->request->getSession()->read('Auth.User.role_id') !==
            Configure::read('ROLE_REGISTRAR')) {
            if (in_array($this->request->getSession()->read('Auth.User.role_id'),
                [Configure::read('ROLE_DEPARTMENT'), Configure::read('ROLE_STUDENT')])) {
                $departments = $departmentsTable->find('list',
                    ['conditions' => ['id' => $this->department_id, 'active' => 1]])->toArray();
            } elseif ($this->request->getSession()->read('Auth.User.role_id') == Configure::read('ROLE_COLLEGE')) {
                $departments =$departmentsTable->find('list', ['conditions' => ['college_id' => $this->college_id, 'active' => 1]])->toArray();
            }
        }

        $departments =$departmentsTable->find('list', ['conditions' => ['college_id' => 1,
            'active' => 1]])->toArray();


        $this->set(compact('departments', 'colleges', 'programs', 'programTypes', 'academicCalendars'));
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Flash->error(__('Invalid academic calendar ID'));
            return $this->redirect(['action' => 'index']);
        }

        $academicCalendar = $this->AcademicCalendar->get($id, ['contain' => ['Departments', 'Colleges', 'YearLevels']]);
        $academicCalendar['college_id'] = unserialize($academicCalendar['college_id']);
        $academicCalendar['department_id'] = unserialize($academicCalendar['department_id']);
        $academicCalendar['year_level_id'] = unserialize($academicCalendar['year_level_id']);

        $this->set(compact('academicCalendar'));
    }

    public function add()
    {
        if ($this->request->is('post')) {
            $academicCalendar = $this->AcademicCalendar->newEntity($this->request->getData());

            if (!empty($this->request->getData('academic_year')) && !empty($this->request->getData('semester'))) {
                if (!empty($this->request->getData('year_level_id')) && !empty(
                    $this->request->getData(
                        'department_id'
                    )
                    )) {
                    if ($this->AcademicCalendar->checkDuplicateEntry($this->request->getData())) {
                        $academicCalendar->department_id = serialize($this->request->getData('department_id'));
                        $academicCalendar->year_level_id = serialize($this->request->getData('year_level_id'));

                        if ($this->AcademicCalendar->save($academicCalendar)) {
                            $this->Flash->success(__('The Academic Calendar has been saved.'));
                            return $this->redirect(['action' => 'index']);
                        }
                        $this->Flash->error(__('The Academic Calendar could not be saved. Please, try again.'));
                    } else {
                        $this->Flash->error(__('Duplicate entry detected. Please check the existing records.'));
                    }
                } else {
                    $this->Flash->error(__('Please select year level and department.'));
                }
            } else {
                $this->Flash->error(__('Please provide academic year and semester.'));
            }
        }

        $yearLevels = $this->AcademicCalendar->YearLevel->find('list');
        $programs = $this->AcademicCalendar->Program->find('list', ['conditions' => ['Program.active' => 1]]);
        $programTypes = $this->AcademicCalendar->ProgramType->find('list', ['conditions' => ['ProgramType.active' => 1]]
        );

        unset($programTypes[PROGRAM_TYPE_PART_TIME], $programTypes[PROGRAM_TYPE_ADVANCE_STANDING]);

        $this->set(compact('programs', 'programTypes', 'yearLevels'));
    }

    public function edit($id = null)
    {

        $academicCalendar = $this->AcademicCalendar->get($id);

        if ($this->request->is(['post', 'put'])) {
            $academicCalendar = $this->AcademicCalendar->patchEntity($academicCalendar, $this->request->getData());
            $academicCalendar->department_id = serialize($this->request->getData('department_id'));
            $academicCalendar->year_level_id = serialize($this->request->getData('year_level_id'));

            if ($this->AcademicCalendar->checkDuplicateEntry($this->request->getData())) {
                if ($this->AcademicCalendar->save($academicCalendar)) {
                    $this->Flash->success(__('The Academic Calendar has been updated.'));
                    return $this->redirect(['action' => 'index']);
                }
                $this->Flash->error(__('The Academic Calendar could not be saved. Please, try again.'));
            } else {
                $this->Flash->error(__('Duplicate entry detected. Please check the existing records.'));
            }
        }

        $yearLevels = $this->AcademicCalendar->YearLevel->find('list');
        $programs = $this->AcademicCalendar->Program->find('list', ['conditions' => ['Program.active' => 1]]);
        $programTypes = $this->AcademicCalendar->ProgramType->find('list', ['conditions' => ['ProgramType.active' => 1]]
        );
        unset($programTypes[PROGRAM_TYPE_PART_TIME], $programTypes[PROGRAM_TYPE_ADVANCE_STANDING]);

        $this->set(compact('academicCalendar', 'programs', 'programTypes', 'yearLevels'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $academicCalendar = $this->AcademicCalendar->get($id);

        if ($this->AcademicCalendar->delete($academicCalendar)) {
            $this->Flash->success(__('Academic calendar deleted.'));
        } else {
            $this->Flash->error(__('Academic calendar could not be deleted.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function extendingCalendar()
    {

        $academicCalendars = [];

        if ($this->request->is('post')) {
            if ($this->request->getData('searchbutton')) {
                $options = [];

                foreach (['program_id', 'program_type_id', 'academic_year', 'semester'] as $field) {
                    if (!empty($this->request->getData('Search.' . $field))) {
                        $options['AcademicCalendar.' . $field] = $this->request->getData('Search.' . $field);
                    }
                }

                $xacademicCalendars = $this->AcademicCalendar->find()
                    ->where($options)
                    ->contain(['Program', 'ProgramType'])
                    ->all();

                foreach ($xacademicCalendars as $acV) {
                    $departments = unserialize($acV->department_id);
                    $list = [];

                    if (!empty($departments)) {
                        foreach ($departments as $deptv) {
                            $list[] = $this->AcademicCalendar->Department->find()
                                ->select(['name'])
                                ->where(['id' => $deptv])
                                ->first()
                                ->name;
                        }
                    }

                    $listStr = implode(' ', $list);
                    $academicCalendars[$listStr][$acV->id] = $acV->full_year . ' ' . $acV->program->name . ' ' . $acV->program_type->name;
                }
            }

            if ($this->request->getData('extend')) {
                $saveAllExtension = [];

                foreach ($this->request->getData('ExtendingAcademicCalendar.department_id') as $dpv) {
                    foreach ($this->request->getData('ExtendingAcademicCalendar.year_level_id') as $ylv) {
                        $saveAllExtension[] = [
                            'academic_calendar_id' => $this->request->getData(
                                'ExtendingAcademicCalendar.academic_calendar_id'
                            ),
                            'department_id' => $dpv,
                            'year_level_id' => $ylv,
                            'program_id' => $this->request->getData('Search.program_id'),
                            'program_type_id' => $this->request->getData('Search.program_type_id'),
                            'activity_type' => $this->request->getData('ExtendingAcademicCalendar.activity_type'),
                            'days' => $this->request->getData('ExtendingAcademicCalendar.days')
                        ];
                    }
                }

                if (!empty($saveAllExtension)) {
                    if ($this->AcademicCalendar->ExtendingAcademicCalendar->saveMany($saveAllExtension)) {
                        $this->Flash->success(__('The Academic Calendar Extension has been updated.'));
                        return $this->redirect(['action' => 'index']);
                    }
                    $this->Flash->error(__('The Academic Calendar Extension could not be saved. Please, try again.'));
                }
            }
        }

        $departments = $this->AcademicCalendar->Department->find('list', ['conditions' => ['Department.active' => 1]]);
        $programs = $this->AcademicCalendar->Program->find('list');
        $programTypes = $this->AcademicCalendar->ProgramType->find('list');

        $activity_types = [
            'registration' => 'Registration',
            'add' => 'Add',
            'drop' => 'Drop',
            'grade_submission' => 'Grade Submission',
            'fx_grade_submission' => 'Fx Grade Submission',
            'graduation_date' => 'Graduation Day',
            'senate_meeting' => 'University Senate Meeting'
        ];

        $this->set(compact('departments', 'programs', 'programTypes', 'activity_types', 'academicCalendars'));
    }

    public function autoSaveExtension()
    {

        $this->autoRender = false;
        $userRoleId = $this->request->getSession()->read('Auth.User.role_id');
        $userParentRole = $this->request->getSession()->read('Auth.User.Role.parent_id');

        if ($userRoleId == ROLE_REGISTRAR || $userParentRole == ROLE_REGISTRAR) {
            if ($this->request->is('ajax') && !empty($this->request->getData('ExtendingAcademicCalendar'))) {
                $saveData = [];

                foreach ($this->request->getData('ExtendingAcademicCalendar') as $ev) {
                    $saveData[] = $ev;
                }

                if (!empty($saveData)) {
                    if ($this->AcademicCalendar->ExtendingAcademicCalendar->saveMany($saveData)) {
                        return $this->response->withType('application/json')
                            ->withStringBody(
                                json_encode(
                                    [
                                        'status' => 'success',
                                        'message' => 'Academic Calendar Extension saved successfully.'
                                    ]
                                )
                            );
                    }
                }

                return $this->response->withType('application/json')
                    ->withStringBody(
                        json_encode(['status' => 'error', 'message' => 'Failed to save Academic Calendar Extension.'])
                    );
            }
        }
    }

    public function getDepartmentsThatHaveTheSelectedProgram()
    {

        $this->request->allowMethod(['ajax']);

        $colleges = $this->AcademicCalendar->Colleges->find('list', ['conditions' => ['active' => 1]])->toArray();
        $departments = $this->AcademicCalendar->Departments->find('list', ['conditions' => ['active' => 1]])->toArray();

        $collegeDepartment = [];
        $preFreshmanRemedialCollegeIds = array_merge(
            Configure::read('preengineering_college_ids') ?? [],
            Configure::read('social_stream_college_ids') ?? [],
            Configure::read('natural_stream_college_ids') ?? []
        );

        $programId = $this->request->getData('AcademicCalendar.program_id');
        $programTypeId = $this->request->getData('AcademicCalendar.program_type_id');

        if (!empty($colleges) && !empty($programId)) {
            $curriculumTable = $this->fetchTable('Curriculum');
            $departmentsThatHaveSelectedProgramCurriculum = $curriculumTable->find('list', [
                'keyField' => 'department_id',
                'valueField' => 'department_id',
                'conditions' => ['program_id' => $programId, 'active' => 1],
                'group' => 'department_id'
            ])->toArray();

            if (defined(
                    'CHECK_STUDY_PROGRAMS_FOR_ACADEMIC_CALENDAR_DEFINITION'
                ) && CHECK_STUDY_PROGRAMS_FOR_ACADEMIC_CALENDAR_DEFINITION) {
                if (!empty($programTypeId) && $programTypeId != PROGRAM_TYPE_REGULAR) {
                    $programModalityIds = $this->fetchTable('ProgramType')->find('list', [
                        'keyField' => 'program_modality_id',
                        'valueField' => 'program_modality_id',
                        'conditions' => ['id' => $programTypeId]
                    ])->toArray();

                    if (!empty($programModalityIds)) {
                        $departmentsThatHaveSelectedProgramCurriculum = $curriculumTable->find('list', [
                            'keyField' => 'department_id',
                            'valueField' => 'department_id',
                            'conditions' => [
                                'department_id IN' => $departmentsThatHaveSelectedProgramCurriculum,
                                'program_id' => $programId,
                                'active' => 1,
                                'department_study_program_id IN (SELECT id FROM department_study_programs WHERE department_id IN (:departments) AND program_modality_id IN (:modalities))'
                            ],
                            'bind' => [
                                'departments' => implode(',', $departmentsThatHaveSelectedProgramCurriculum),
                                'modalities' => implode(',', $programModalityIds)
                            ],
                            'group' => 'department_id'
                        ])->toArray();
                    }
                }
            }

            foreach ($colleges as $collegeId => $collegeName) {
                $departmentsList = $this->AcademicCalendar->Departments->find('list', [
                    'keyField' => 'id',
                    'valueField' => 'name',
                    'conditions' => [
                        'college_id' => $collegeId,
                        'id IN' => $departmentsThatHaveSelectedProgramCurriculum,
                        'active' => 1
                    ],
                    'order' => ['name' => 'ASC']
                ])->toArray();

                if (!empty($departmentsList)) {
                    $collegeDepartment[$collegeId] = $departmentsList;
                }

                if (!empty($this->request->getData('AcademicCalendar.year_level_id')) && in_array(
                        '1st',
                        (array)$this->request->getData('AcademicCalendar.year_level_id')
                    )
                    && in_array(
                        $this->request->getData('AcademicCalendar.program_id'),
                        [PROGRAM_UNDEGRADUATE, PROGRAM_REMEDIAL]
                    )
                    && $this->request->getData('AcademicCalendar.program_type_id') == PROGRAM_TYPE_REGULAR) {
                    if (!empty($preFreshmanRemedialCollegeIds) && in_array(
                            $collegeId,
                            $preFreshmanRemedialCollegeIds
                        )) {
                        $collegeDepartment[$collegeId]['pre_' . $collegeId] = 'Pre/Freshman';
                    }
                }
            }
        }

        return $this->response->withType('application/json')->withStringBody(
            json_encode(compact('collegeDepartment', 'colleges'))
        );
    }

    private function initSearchCalendar()
    {

        $session = $this->request->getSession();

        if (!empty($this->request->getData('Search'))) {
            $session->write('search_calendar', $this->request->getData('Search'));
        } elseif ($session->check('search_calendar')) {
            $this->request = $this->request->withData('Search', $session->read('search_calendar'));
        }
    }

    private function initClearSessionFilters()
    {

        $session = $this->request->getSession();

        if ($session->check('search_calendar')) {
            $session->delete('search_calendar');
        }

        if ($session->check('search_define_calendar')) {
            $session->delete('search_define_calendar');
        }
    }

    private function initSearchDefineCalendar()
    {

        $session = $this->request->getSession();

        if (!empty($this->request->getData('AcademicCalendar'))) {
            $session->write('search_define_calendar', $this->request->getData('AcademicCalendar'));
        } elseif ($session->check('search_define_calendar')) {
            $this->request = $this->request->withData('AcademicCalendar', $session->read('search_define_calendar'));
        }
    }


}
