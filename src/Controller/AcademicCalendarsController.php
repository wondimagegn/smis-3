<?php
namespace App\Controller;

use App\Utility\AcademicYearTrait;
use Cake\ORM\TableRegistry;
use Cake\Http\Exception\NotFoundException;
use App\Utility\ArrayHelper;

class AcademicCalendarsController extends AppController
{
    use AcademicYearTrait;

    /**
     * Menu options – public as requested
     *
     * @var array
     */
    public $menuOptions = [
        'parent' => 'dashboard',
        'exclude' => [
            'autoSaveExtension',
            'get_departments_that_have_the_selected_program'
        ],
        'alias' => [
            'index' => 'View All Academic Calendars',
            'add' => 'Set Academic Calendar',
            'extending_calendar' => 'Extend Academic Calendar',
        ]
    ];

    /**
     * Initialize – load components and helpers
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('EthiopicDateTime'); // Custom component
    }

    /**
     * Before filter – allow unauthenticated actions
     */
    public function beforeFilter(\Cake\Event\EventInterface $event): void
    {
        parent::beforeFilter($event);
        $this->Auth->allow([
            'autoSaveExtension',
            'extending_calendar',
            'get_departments_that_have_the_selected_program'
        ]);
    }

    /**
     * Before render – set common variables
     */
    public function beforeRender(\Cake\Event\EventInterface $event): void
    {
        parent::beforeRender($event);

        $current = $this->currentAcyAndSemester();
        $defaultAcademicYear = $current['academic_year'];
        $currentSemester = $current['semester'];

        if (defined('ACY_BACK_FOR_ALL') && is_numeric(ACY_BACK_FOR_ALL) && ACY_BACK_FOR_ALL) {
            $acyearArrayData = $this->academicYearArray(
                (int)explode('/', $defaultAcademicYear)[0] - ACY_BACK_COURSE_REGISTRATION,
                (int)explode('/', $defaultAcademicYear)[0]
            );
        } else {
            $acyearArrayData[$defaultAcademicYear] = $defaultAcademicYear;
        }

        $roleId = $this->request->getSession()->read('Auth.User.role_id');
        $isAdmin = $this->request->getSession()->read('Auth.User.is_admin');
        if ($roleId == ROLE_REGISTRAR && $isAdmin == 1) {
            $minus = (defined('ACY_BACK_FOR_ALL') && is_numeric(ACY_BACK_FOR_ALL)) ? ACY_BACK_FOR_ALL : 4;
            $acyearArrayData = $this->academicYearArray(
                (int)explode('/', $defaultAcademicYear)[0] - $minus,
                (int)explode('/', $defaultAcademicYear)[0]
            );
        }

        $this->set(compact('acyearArrayData', 'defaultAcademicYear', 'currentSemester'));
    }

    /**
     * Initialize search calendar from session or request data.
     *
     * @return void
     */
    protected function _initSearchCalendar(): void
    {
        $search = $this->request->getData('Search');
        if (!empty($search)) {
            $this->request->getSession()->write('search_calendar', $search);
        } elseif ($this->request->getSession()->check('search_calendar')) {
            $saved = $this->request->getSession()->read('search_calendar');
            $this->request = $this->request->withData('Search', $saved);
        }
    }

    /**
     * Initialize define calendar from session or request data.
     *
     * @return void
     */
    protected function _initSearchDefineCalendar(): void
    {
        $search = $this->request->getData('AcademicCalendar');
        if (!empty($search)) {
            $this->request->getSession()->write('search_define_calendar', $search);
        } elseif ($this->request->getSession()->check('search_define_calendar')) {
            $saved = $this->request->getSession()->read('search_define_calendar');
            $this->request = $this->request->withData('AcademicCalendar', $saved);
        }
    }

    /**
     * Clear session filters.
     *
     * @return void
     */
    protected function _initClearSessionFilters(): void
    {
        $session = $this->request->getSession();
        if ($session->check('search_calendar')) {
            $session->delete('search_calendar');
        }
        if ($session->check('search_define_calendar')) {
            $session->delete('search_define_calendar');
        }
    }

    /**
     * Index action to list academic calendars.
     *
     * @return void
     */
    public function index()
    {
        $current = $this->currentAcyAndSemester();
        $this->_initSearchCalendar();

        $this->paginate = [
            'fields' => [
                'id',
                'academic_year',
                'semester',
                'department_id',
                'year_level_id',
                'course_registration_start_date',
                'course_registration_end_date',
                'course_add_start_date',
                'course_add_end_date',
                'course_drop_start_date',
                'course_drop_end_date',
                'grade_submission_start_date',
                'grade_submission_end_date',
                'grade_fx_submission_end_date',
                'senate_meeting_date',
                'graduation_date',
                'created'
            ],
            'contain' => [
                'ExtendingAcademicCalendars' => [
                    'Departments',
                    'Programs',
                    'ProgramTypes'
                ],
                'Programs' => ['fields' => ['id', 'name']],
                'ProgramTypes' => ['fields' => ['id', 'name']]
            ],
            'order' => [
                'AcademicCalendars.created' => 'DESC',
                'AcademicCalendars.academic_year' => 'DESC',
                'AcademicCalendars.semester' => 'DESC',
                'AcademicCalendars.program_id' => 'ASC',
                'AcademicCalendars.program_type_id' => 'ASC'
            ],
            'limit' => 50,
            'maxLimit' => 50
        ];

        $conditions = [];

        $search = $this->request->getData('Search') ?? [];

        if ($this->request->is('post') && $this->request->getData('viewAcademicCalendar')) {
            $this->_initClearSessionFilters();
            $this->_initSearchCalendar();
        }

        if (!empty($search['program_id'])) {
            $conditions['AcademicCalendars.program_id'] = $search['program_id'];
        }
        if (!empty($search['program_type_id'])) {
            $conditions['AcademicCalendars.program_type_id'] = $search['program_type_id'];
        }
        if (!empty($search['department_id'])) {
            $conditions['AcademicCalendars.department_id LIKE'] = '%s:_:"' . $search['department_id'] . '"%';
        }
        if (!empty($search['academic_year'])) {
            $conditions['AcademicCalendars.academic_year'] = $search['academic_year'];
        }
        if (!empty($search['semester'])) {
            $conditions['AcademicCalendars.semester'] = $search['semester'];
        }
        if (!empty($search['year_level_id'])) {
            $conditions['AcademicCalendars.year_level_id LIKE'] = '%s:_:"' . $search['year_level_id'] . '"%';
        }

        if (empty($conditions)) {
            $conditions['AcademicCalendars.academic_year'] = $current['academic_year'];
            $conditions['AcademicCalendars.semester'] = $current['semester'];
        }

        try {
            $academicCalendars = $this->paginate(
                $this->AcademicCalendars->find()->where($conditions)
            );

            // Post-processing for department/year names

            foreach ($academicCalendars as $cal) {
                $deptIds = $cal->department_id ? unserialize($cal->department_id) : [];
                $yearIds = $cal->year_level_id ? unserialize($cal->year_level_id) : [];
                // ==== Department names (only numeric IDs) ====
                $numericDeptIds = array_filter($deptIds, 'is_numeric');
                $cal->department_name = '';
                if (!empty($numericDeptIds)) {
                    $deptNames = $this->AcademicCalendars->Departments->find('list')
                        ->where(['Departments.id IN' => $numericDeptIds])
                        ->toArray();
                    $cal->department_name = implode(', ', $deptNames);
                }

                // ==== College names for freshman/pre-engineering (pre_5, pre_7, …) ====
                $collegeIds = [];
                foreach ($deptIds as $d) {
                    if (strpos($d, 'pre_') === 0) {
                        $parts = explode('pre_', $d);
                        if (isset($parts[1]) && is_numeric($parts[1])) {
                            $collegeIds[] = (int)$parts[1];
                        }
                    }
                }
                $cal->college_name = '';
                if (!empty($collegeIds)) {
                    $collegeNames = $this->AcademicCalendars->Colleges->find('list')
                        ->where(['Colleges.id IN' => $collegeIds])
                        ->toArray();
                    $cal->college_name = implode(', ', $collegeNames);
                }

                // Year level names (just implode – they are strings like "1st", "2nd")
                $cal->year_name = implode("\n", $yearIds);
            }
            unset($cal);

            if (empty($academicCalendars) && !empty($conditions)) {
                $this->Flash->info('There is no academic calendar defined in the system in the given criteria.');
            }
        } catch (NotFoundException $e) {
            $this->redirect(['action' => 'index']);
            return;
        }
        /*
        $programIds = [];
        debug($this->program_ids);

        if (!empty($this->program_ids)) {
            // Convert comma-separated string to array of integers
            $programIds = array_map('intval', explode(',', $this->program_ids));
            // Optional: remove invalid/zero values
            $programIds = array_filter($programIds, fn($id) => $id > 0);
        }

        $programs = $this->AcademicCalendars->Programs->find('list', [
            'conditions' => ['Programs.id IN' => $programIds, 'Programs.active' => 1]
        ])->toArray();
        debug($programIds);
        */

        $programIds = ArrayHelper::toIntArray($this->program_ids);
        debug($programIds);




        $programs = $this->AcademicCalendars->Programs->find('list')
            ->where(['Programs.active' => 1])
            ->where(!empty($programIds) ? ['Programs.id IN' => $programIds] : [])
            ->toArray();

        $programTypes = $this->AcademicCalendars->ProgramTypes->find('list', [
            'conditions' => ['ProgramTypes.id IN' => $this->program_type_ids ?? [], 'ProgramTypes.active' => 1]
        ])->toArray();

        $yearLevels = $this->year_levels;

        debug($yearLevels);
        debug( $this->year_levels);

        $this->set(compact('programs', 'programTypes', 'yearLevels'));
        $this->set('academicCalendars', $academicCalendars);
    }


    /* ==============================================================
       VIEW
       ============================================================== */
    public function view(?int $id = null): void
    {
        if (!$id) {
            $this->Flash->error('Invalid Academic Calendar ID.');
             $this->redirect(['action' => 'index']);
        }

        try {
            $academicCalendar = $this->AcademicCalendars->get($id, [
                'contain' => ['Departments', 'Colleges', 'Programs', 'ProgramTypes']
            ]);
        } catch (\Exception $e) {
            $this->Flash->error('Invalid Academic Calendar ID.');
             $this->redirect(['action' => 'index']);
        }

        // Unserialize serialized fields
        $academicCalendar->college_id = $academicCalendar->college_id ? unserialize($academicCalendar->college_id) : [];
        $academicCalendar->department_id = $academicCalendar->department_id ? unserialize($academicCalendar->department_id) : [];
        $academicCalendar->year_level_id = $academicCalendar->year_level_id ? unserialize($academicCalendar->year_level_id) : [];

        $collegeIds = [];
        foreach ($academicCalendar->department_id as $dept) {
            if (strpos($dept, 'pre_') === 0) {
                $parts = explode('pre_', $dept);
                if (isset($parts[1])) $collegeIds[] = $parts[1];
            }
        }

        $collegeDepartment = [];
        if (!empty($academicCalendar->department_id)) {
            $depts = $this->AcademicCalendars->Departments->find()
                ->select(['id', 'name', 'college_id'])
                ->where(['Departments.id IN' => $academicCalendar->department_id])
                ->contain(['Colleges'])
                ->all();

            foreach ($depts as $dept) {
                $collegeDepartment[$dept->college->name][$dept->id] = $dept->name;
            }
        }

        $this->set(compact('academicCalendar', 'collegeDepartment'));
    }
    /* ==============================================================
       ADD
       ============================================================== */
    public function add()
    {
        $academicCalendar = $this->AcademicCalendars->newEntity();

        if ($this->request->is('post')) {
            $data = $this->request->getData();

            if (
                !empty($data['academic_year']) &&
                !empty($data['semester']) &&
                !empty($data['year_level_id']) &&
                !empty($data['department_id'])
            ) {
                if ($this->AcademicCalendars->check_duplicate_entry($data)) {
                    $data['department_id'] = serialize($data['department_id']);
                    $data['year_level_id'] = serialize($data['year_level_id']);

                    $academicCalendar = $this->AcademicCalendars->patchEntity($academicCalendar, $data);

                    if ($this->AcademicCalendars->save($academicCalendar)) {
                        $this->Flash->success('The Academic Calendar has been saved.');

                        $this->_initClearSessionFilters();

                        $this->request->getSession()->write('search_calendar', [
                            'academic_year' => $data['academic_year'],
                            'semester' => $data['semester'],
                            'program_id' => $data['program_id'],
                            'program_type_id' => $data['program_type_id'],
                        ]);

                        return $this->redirect(['action' => 'index']);
                    }
                    $this->Flash->error('The Academic Calendar could not be saved. Please, try again.');
                } else {
                    $error = $this->AcademicCalendars->invalidFields();
                    if (isset($error['duplicate'])) {
                        $this->Flash->error($error['duplicate'][0] . ' Those unchecked red marked department has an academic calendar for the given criteria.');
                    }
                    $this->set('alreadyexisteddepartment', $error['departmentduplicate'] ?? []);
                    $this->set('alreadyexistedyearlevel', $error['yearlevelduplicate'] ?? []);
                }
            } else {
                $this->Flash->error('Please fill all required fields.');
            }

            // Restore arrays on error
            if (isset($data['department_id'])) {
                $data['department_id'] = is_string($data['department_id']) ? unserialize($data['department_id']) : $data['department_id'];
            }
            if (isset($data['year_level_id'])) {
                $data['year_level_id'] = is_string($data['year_level_id']) ? unserialize($data['year_level_id']) : $data['year_level_id'];
            }
            $academicCalendar = $this->AcademicCalendars->patchEntity($academicCalendar, $data);
        }

        $this->_prepareFormVariables();
        $this->set(compact('academicCalendar'));
        $this->render('edit'); // reuse edit view
    }
    /* ==============================================================
       EDIT
       ============================================================== */
    public function edit(?int $id = null): void
    {
        if (!$id) {
            $this->Flash->error('Invalid Academic Calendar.');
             $this->redirect(['action' => 'index']);
        }

        try {
            $academicCalendar = $this->AcademicCalendars->get($id);
        } catch (\Exception $e) {
            $this->Flash->error('Invalid Academic Calendar ID.');
             $this->redirect(['action' => 'index']);
        }

        if ($this->request->is(['post', 'put'])) {
            $data = $this->request->getData();

            if (!empty($data['year_level_id']) && !empty($data['department_id'])) {
                $data['department_id'] = serialize($data['department_id']);
                $data['year_level_id'] = serialize($data['year_level_id']);

                if ($this->AcademicCalendars->check_duplicate_entry($data)) {
                    $academicCalendar = $this->AcademicCalendars->patchEntity($academicCalendar, $data);

                    if ($this->AcademicCalendars->save($academicCalendar)) {
                        $this->Flash->success('The Academic Calendar has been updated.');
                        $this->_initClearSessionFilters();
                        $this->request->getSession()->write('search_calendar', [
                            'academic_year' => $data['academic_year'],
                            'semester' => $data['semester'],
                            'program_id' => $data['program_id'],
                            'program_type_id' => $data['program_type_id'],
                        ]);
                         $this->redirect(['action' => 'index']);
                    }
                    $this->Flash->error('The Academic Calendar could not be saved. Please, try again.');
                } else {
                    $error = $this->AcademicCalendars->invalidFields();
                    if (isset($error['duplicate'])) {
                        $this->Flash->error($error['duplicate'][0] . ' Those unchecked red marked department has an academic calendar for the given criteria.');
                    }
                    $this->set('alreadyexisteddepartment', $error['departmentduplicate'] ?? []);
                    $this->set('alreadyexistedyearlevel', $error['yearlevelduplicate'] ?? []);
                }
            } else {
                $this->Flash->error('Please select year level and department.');
            }

            // Restore arrays on error
            if (isset($data['department_id'])) {
                $data['department_id'] = is_string($data['department_id']) ? unserialize($data['department_id']) : $data['department_id'];
            }
            if (isset($data['year_level_id'])) {
                $data['year_level_id'] = is_string($data['year_level_id']) ? unserialize($data['year_level_id']) : $data['year_level_id'];
            }
            $academicCalendar = $this->AcademicCalendars->patchEntity($academicCalendar, $data);
        }

        // Unserialize for form display
        $academicCalendar->department_id = $academicCalendar->department_id ? unserialize($academicCalendar->department_id) : [];
        $academicCalendar->year_level_id = $academicCalendar->year_level_id ? unserialize($academicCalendar->year_level_id) : [];

        $this->_prepareFormVariables();
        $this->set(compact('academicCalendar'));
    }
    /**
     * Delete action to remove an academic calendar.
     *
     * @param int|null $id The ID of the academic calendar.
     * @return void
     */

    public function delete(?int $id = null)
    {
        if (!$id) {
            $this->Flash->error('Invalid Academic Calendar ID.');
             $this->redirect(['action' => 'index']);
        }

        $this->request->allowMethod(['post', 'delete']);

        try {
            $entity = $this->AcademicCalendars->get($id);
            if ($this->AcademicCalendars->delete($entity)) {
                $this->Flash->success('Academic calendar deleted');
            } else {
                $this->Flash->error('Academic calendar could not be deleted.');
            }
        } catch (\Exception $e) {
            $this->Flash->error('Invalid Academic Calendar ID.');
        }

        $this->redirect(['action' => 'index']);
    }

    public function extendingCalendar(): void
    {
        if ($this->request->is('post') && $this->request->getData('extend')) {
            $saveData = [];
            $count = 0;

            $ext = $this->request->getData('ExtendingAcademicCalendar');
            if (!empty($ext['department_id']) && !empty($ext['year_level_id'])) {
                foreach ($ext['department_id'] as $dept) {
                    foreach ($ext['year_level_id'] as $year) {
                        $saveData[$count] = [
                            'academic_calendar_id' => $ext['academic_calendar_id'],
                            'department_id' => $dept,
                            'year_level_id' => $year,
                            'program_id' => $this->request->getData('Search.program_id') ?? null,
                            'program_type_id' => $this->request->getData('Search.program_type_id') ?? null,
                            'activity_type' => $ext['activity_type'],
                            'days' => $ext['days']
                        ];
                        $count++;
                    }
                }
            }

            $extTable = $this->AcademicCalendars->ExtendingAcademicCalendars;
            $entities = $extTable->newEntities($saveData, ['validate' => 'first']);
            if ($extTable->saveMany($entities)) {
                $this->Flash->success('The Academic Calendar Extension has been updated.');
                $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('The Academic Calendar Extension could not be saved. Please, try again.');
            }
        }

        // Prepare data for view (same as index but without pagination)
        $this->_prepareFormVariables();
        $this->set('academicCalendars', $academicCalendars ?? []);
    }

    /**
     * Auto save extension action (AJAX).
     *
     * @return void
     */
    public function autoSaveExtension(): void
    {
        $this->viewBuilder()->setLayout('ajax');

        $roleId = $this->request->getSession()->read('Auth.User.role_id');
        if ($roleId != ROLE_REGISTRAR && $roleId != $this->request->getSession()->read('Auth.User.Role.parent_id')) {
            return;
        }

        $data = $this->request->getData('ExtendingAcademicCalendar');
        if (!empty($data)) {
            $extTable = $this->AcademicCalendars->ExtendingAcademicCalendars;
            $entities = $extTable->newEntities($data);
            $extTable->saveMany($entities);
        }
    }

    /**
     * Get departments that have the selected program (AJAX).
     *
     * @return void
     */
    public function get_departments_that_have_the_selected_program(): void
    {
        $this->viewBuilder()->setLayout('ajax');

        $collegesTable = TableRegistry::getTableLocator()->get('Colleges');
        $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
        $programTypesTable= TableRegistry::getTableLocator()->get('ProgramTypes');
        $departmentStudyProgramsTable= TableRegistry::getTableLocator()->get('DepartmentStudyPrograms');



        $colleges = $collegesTable->find('list', [
            'conditions' => ['Colleges.active' => 1],
            'keyField' => 'id',
            'valueField' => 'name'
        ])->toArray();

        $departments = $departmentsTable->find('list', [
            'conditions' => ['Departments.active' => 1],
            'keyField' => 'id',
            'valueField' => 'name'
        ])->toArray();

        $collegeDepartment = [];

        $programId = $this->request->getData('AcademicCalendar.program_id');
        $programTypeId = $this->request->getData('AcademicCalendar.program_type_id');
        $yearLevelIds = $this->request->getData('AcademicCalendar.year_level_id') ?? [];

        if (!empty($colleges) && $programId) {
            $curriculumsTable = TableRegistry::getTableLocator()->get('Curriculums');
            $departmentIds = $curriculumsTable->find('list', [
                'keyField' => 'department_id',
                'valueField' => 'department_id'
            ])
                ->where(['Curriculums.program_id' => $programId, 'Curriculums.active' => 1])
                ->group('Curriculums.department_id')
                ->toArray();

            if (CHECK_STUDY_PROGRAMS_FOR_ACADEMIC_CALENDAR_DEFINITION) {
                if ($programId && $programTypeId && $programTypeId != PROGRAM_TYPE_REGULAR) {
                    $programModalities = $programTypesTable->find('list', [
                        'keyField' => 'program_modality_id',
                        'valueField' => 'program_modality_id'
                    ])
                        ->where(['ProgramTypes.id' => $programTypeId])
                        ->toArray();

                    if (!empty($programModalities)) {
                        $curriculumsTable->find('list', [
                            'keyField' => 'department_id',
                            'valueField' => 'department_id'
                        ])
                            ->where([
                                'Curriculums.department_id IN' => $departmentIds,
                                'Curriculums.program_id' => $programId,
                                'Curriculums.department_study_program_id IN' => $departmentStudyProgramsTable->find('list', [
                                    'keyField' => 'id',
                                    'valueField' => 'id'
                                ])
                                    ->where([
                                        'DepartmentStudyPrograms.department_id IN' => $departmentIds,
                                        'DepartmentStudyPrograms.program_modality_id IN' => $programModalities
                                    ])
                                    ->toArray(),
                                'Curriculums.active' => 1
                            ])
                            ->group('Curriculums.department_id')
                            ->toArray();
                    }
                }
            }

            foreach ($colleges as $collegeId => $collegeName) {
                $depts = $departmentsTable->find('list', [
                    'keyField' => 'id',
                    'valueField' => 'name'
                ])
                    ->where([
                        'Departments.college_id' => $collegeId,
                        'Departments.id IN' => $departmentIds,
                        'Departments.active' => 1
                    ])
                    ->order(['Departments.name' => 'ASC'])
                    ->toArray();

                if (!empty($depts)) {
                    $collegeDepartment[$collegeId] = $depts;
                }

                if (($programId == PROGRAM_UNDEGRADUATE || $programId == PROGRAM_REMEDIAL) && $programTypeId == PROGRAM_TYPE_REGULAR && !empty($yearLevelIds) && in_array('1st', $yearLevelIds)) {
                    if (!empty($preFreshmanRemedialCollegeIds) && in_array($collegeId, $preFreshmanRemedialCollegeIds)) {
                        $collegeDepartment[$collegeId]['pre_' . $collegeId] = 'Pre/Freshman';
                    }
                }
            }
        }

        $error = null;
        $this->set(compact('collegeDepartment', 'colleges', 'error'));
    }

    /* ==============================================================
       Helper: prepare variables for add/edit/extending_calendar
       ============================================================== */
    protected function _prepareFormVariables(): void
    {
        $programs = $this->AcademicCalendars->Programs->find('list', [
            'conditions' => ['Programs.active' => 1]
        ])->toArray();

        $programTypes = $this->AcademicCalendars->ProgramTypes->find('list', [
            'conditions' => ['ProgramTypes.active' => 1]
        ])->toArray();

        $yearLevels = $this->year_levels ?? [];

        $this->set(compact('programs', 'programTypes', 'yearLevels'));
    }
}
