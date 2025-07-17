<?php
namespace App\Controller;

use Cake\ORM\TableRegistry;
use Cake\I18n\Time;
use Cake\Http\Exception\NotFoundException;
use Cake\Datasource\Exception\RecordNotFoundException;

class PlacementPreferencesController extends AppController
{
    protected $menuOptions = [
        'parent' => 'placement',
        'exclude' => [
            'edit_preference',
            'auto_fill_preference',
            'get_selected_participant',
            'get_selected_student',
            'auto_save_result',
        ],
        'alias' => [
            'index' => 'List Preference',
            'add' => 'Add Preference on Behalf of Student',
            'record_preference' => 'Record Your Preference',
            'view_result_of_placement' => 'View Your Placement Result'
        ],
    ];

    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Auth');
        $this->loadComponent('Flash');
        $this->loadComponent('Paginator');
        $this->loadComponent('RequestHandler');
        $this->loadComponent('AcademicYear'); // Custom component
    }

    public function beforeRender(\Cake\Event\EventInterface $event)
    {
        parent::beforeRender($event);

        $placementRoundParticipantsTable = TableRegistry::getTableLocator()->get('PlacementRoundParticipants');
        $availableAcademicYears = $placementRoundParticipantsTable->find('list')
            ->select(['academic_year' => 'PlacementRoundParticipants.academic_year'])
            ->group(['PlacementRoundParticipants.academic_year'])
            ->order(['PlacementRoundParticipants.academic_year' => 'DESC'])
            ->disableHydration()
            ->toArray();

        $defaultacademicyear = $current_academicyear = $this->AcademicYear->currentAcademicYear();

        if (empty($availableAcademicYears)) {
            $acyear_array_data[$current_academicyear] = $current_academicyear;
        } else {
            $acyear_array_data = $availableAcademicYears;
        }

        $this->set(compact('acyear_array_data', 'defaultacademicyear'));
    }

    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->Auth->allow(['getStudentPreference']);
    }

    private function initSearchPreferences()
    {
        $session = $this->request->getSession();
        $searchData = $this->request->getData('PlacementPreference');

        if (!empty($searchData)) {
            $session->write('search_preferences', $searchData);
            $this->request = $this->request->withData('PlacementPreference', $searchData);
        } elseif ($session->check('search_preferences')) {
            $search_session = $session->read('search_preferences');
            $this->request = $this->request->withData('PlacementPreference', $search_session);
        }
    }

    public function index(?string $academic_year = null, ?string $suffix = null)
    {
        $acYear = $selectedAcy = $this->AcademicYear->currentAcademicYear();
        $selectedRound = 1;
        $selectedCurrentUnit = '';
        $selectedProgID = 1;
        $selectedProgTypeID = 1;
        $selectedLimit = '';
        $preferenceOrderListCount = 0;
        $preferenceOrderList = [];
        $selected_preference_order = 1;
        $options = [];
        $page = 1;
        $sort = '';
        $direction = '';
        $participatingUnits = 0;
        $placementDeadlinesTable= TableRegistry::getTableLocator()->get('PlacementDeadlines');

        $this->initSearchPreferences();

        $params = $this->request->getParam('pass', []);
        if (isset($params['page'])) {
            $page = $this->request->getData('PlacementPreference.page', $params['page']);
        }
        if (isset($params['sort'])) {
            $sort = $this->request->getData('PlacementPreference.sort', $params['sort']);
        }
        if (isset($params['direction'])) {
            $direction = $this->request->getData('PlacementPreference.direction', $params['direction']);
        }

        if ($this->request->getData('PlacementPreference.academic_year')) {
            $selectedAcy = $options['conditions']['PlacementPreferences.academic_year'] = $this->request->getData('PlacementPreference.academic_year');
        }

        if ($this->request->getData('PlacementPreference.limit')) {
            $selectedLimit = $options['limit'] = $this->request->getData('PlacementPreference.limit');
        }

        if ($this->request->getData('PlacementPreference.preference_order')) {
            $selected_preference_order = $options['conditions']['PlacementPreferences.preference_order'] = $this->request->getData('PlacementPreference.preference_order');
        }

        if ($this->request->getData('PlacementPreference.round')) {
            $selectedRound = $options['conditions']['PlacementPreferences.round'] = $this->request->getData('PlacementPreference.round');
        }

        if ($this->request->getData('PlacementPreference.placement_round_participant_id')) {
            $options['conditions']['PlacementPreferences.placement_round_participant_id'] = $this->request->getData('PlacementPreference.placement_round_participant_id');
        }

        if ($this->request->getData('PlacementPreference.program_id')) {
            $selectedProgID = $options['conditions']['Students.program_id'] = $this->request->getData('PlacementPreference.program_id');
        }

        if ($this->request->getData('PlacementPreference.program_type_id')) {
            $selectedProgTypeID = $options['conditions']['Students.program_type_id'] = $this->request->getData('PlacementPreference.program_type_id');
        }

        if ($this->role_id == ROLE_STUDENT) {
            $studentsTable = TableRegistry::getTableLocator()->get('Students');
            $acceptedStudentID = $studentsTable->find()
                ->select(['accepted_student_id'])
                ->where(['Students.id' => $this->student_id])
                ->disableHydration()
                ->first()['accepted_student_id'] ?? null;

            $acceptedStudentsTable = TableRegistry::getTableLocator()->get('AcceptedStudents');
            $acceptedStudentdetail = $acceptedStudentsTable->find()
                ->where(['AcceptedStudents.id' => $acceptedStudentID])
                ->contain(['Students', 'PlacementEntranceExamResultEntries'])
                ->disableHydration()
                ->first() ?? [];

            $applied_for = '';
            if (empty($acceptedStudentdetail['AcceptedStudent']['specialization_id']) && empty($acceptedStudentdetail['AcceptedStudent']['department_id'])) {
                $applied_for = 'c~' . ($acceptedStudentdetail['AcceptedStudent']['college_id'] ?? '');
            } elseif (empty($acceptedStudentdetail['AcceptedStudent']['department_id'])) {
                $applied_for = 'c~' . ($acceptedStudentdetail['AcceptedStudent']['college_id'] ?? '');
            } elseif (!empty($acceptedStudentdetail['AcceptedStudent']['college_id']) && !empty($acceptedStudentdetail['AcceptedStudent']['department_id']) && empty($acceptedStudentdetail['AcceptedStudent']['specialization_id'])) {
                $applied_for = 'd~' . ($acceptedStudentdetail['AcceptedStudent']['department_id'] ?? '');
            }

            $lastStudentSection = $this->last_section;
            $deosTheStudentHaveAnySectionAssignment = false;
            $freshman = false;

            $placementRoundParticipantsTable = TableRegistry::getTableLocator()->get('PlacementRoundParticipants');
            $latestACY = $placementRoundParticipantsTable->latest_defined_academic_year_and_round($applied_for);

            $roundLebel = '';
            if (!empty($latestACY['round'])) {
                $roundLebel = ($latestACY['round'] == 1 ? '1st' : ($latestACY['round'] == 2 ? '2nd' : '3rd'));
            }

            if (!empty($lastStudentSection) && is_null($acceptedStudentdetail['Student']['department_id'] ?? null)) {
                if (!$lastStudentSection['archive']) {
                    $acYear = $lastStudentSection['academicyear'];
                }
                $deosTheStudentHaveAnySectionAssignment = true;
                $freshman = true;
            } elseif (!empty($latestACY)) {
                $selectedAcy = $acYear = $latestACY['academic_year'];
                $selectedRound = $latestACY['round'];
                $selectedCurrentUnit = $latestACY['applied_for'];
            }

            $placementDeadlinesTable = TableRegistry::getTableLocator()->get('PlacementDeadlines');
            $preference_deadline = $placementDeadlinesTable->find()
                ->where([
                    'PlacementDeadlines.program_id' => $acceptedStudentdetail['AcceptedStudent']['program_id'] ?? null,
                    'PlacementDeadlines.applied_for' => $applied_for,
                    'PlacementDeadlines.program_type_id IN' => \Cake\Core\Configure::read('program_types_available_for_placement_preference'),
                    'PlacementDeadlines.academic_year LIKE' => $acYear . '%'
                ])
                ->order(['PlacementDeadlines.academic_year' => 'DESC', 'PlacementDeadlines.placement_round' => 'DESC'])
                ->disableHydration()
                ->first() ?? [];

            if (empty($preference_deadline)) {
                $studentExamStatusesTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');
                $currentAcademicYear = $studentExamStatusesTable->getPreviousSemester($this->AcademicYear->currentAcademicYear());
                $acYear = $currentAcademicYear['academic_year'] ?? $acYear;

                $preference_deadline = $placementDeadlinesTable->find()
                    ->where([
                        'PlacementDeadlines.program_id' => $acceptedStudentdetail['AcceptedStudent']['program_id'] ?? null,
                        'PlacementDeadlines.applied_for' => $applied_for,
                        'PlacementDeadlines.program_type_id IN' => \Cake\Core\Configure::read('program_types_available_for_placement_preference'),
                        'PlacementDeadlines.academic_year LIKE' => $acYear . '%'
                    ])
                    ->order(['PlacementDeadlines.academic_year' => 'DESC', 'PlacementDeadlines.placement_round' => 'DESC'])
                    ->disableHydration()
                    ->first() ?? [];
            } elseif (
                !empty($preference_deadline['deadline']) &&
                in_array($acceptedStudentdetail['AcceptedStudent']['program_type_id'] ?? null, \Cake\Core\Configure::read('program_types_available_for_placement_preference')) &&
                ($acceptedStudentdetail['AcceptedStudent']['program_id'] ?? null) == PROGRAM_UNDERGRADUATE
            ) {
                $current_datetime = new Time();
                $deadline_datetime = new Time($preference_deadline['deadline']);

                $ispreferenceFilledByStudent = $this->PlacementPreferences->find()
                    ->where([
                        'PlacementPreferences.academic_year' => $acYear,
                        'PlacementPreferences.round' => $selectedRound,
                        'PlacementPreferences.student_id' => $this->student_id
                    ])
                    ->count();

                if (!$ispreferenceFilledByStudent && $current_datetime < $deadline_datetime) {
                    return $this->redirect(['action' => 'record_preference']);
                }
            }

            $options['conditions']['PlacementPreferences.student_id'] = $this->student_id;

            $participatingUnitsCount = $this->PlacementPreferences->find()
                ->where([
                    'PlacementPreferences.student_id' => $this->student_id,
                    'PlacementPreferences.academic_year LIKE' => $acYear . '%'
                ])
                ->count();

            $departments = $placementRoundParticipantsTable->participating_unit_name($acceptedStudentdetail, $acYear);

            $collegesList = TableRegistry::getTableLocator()->get('Colleges')->find('list')
                ->where(['Colleges.active' => 1])
                ->disableHydration()
                ->toArray();

            $departmentsList = TableRegistry::getTableLocator()->get('Departments')->find('list')
                ->where(['Departments.active' => 1])
                ->disableHydration()
                ->toArray();

            $placementParticipatingStudentsTable = TableRegistry::getTableLocator()->get('PlacementParticipatingStudents');

            $placementRound = $placementParticipatingStudentsTable->getNextRound($acYear, $acceptedStudentdetail['AcceptedStudent']['id'] ?? null);
            $app_for = $placementRoundParticipantsTable->appliedFor($acceptedStudentdetail, $acYear);
            $deadLineStatus = $placementDeadlinesTable->getDeadlineStatus($acceptedStudentdetail, $app_for, $placementRound, $acYear);

            $this->set(compact(
                'preference_deadline',
                'departments',
                'collegesList',
                'departmentsList',
                'acYear',
                'roundLebel',
                'freshman',
                'deadLineStatus',
                'deosTheStudentHaveAnySectionAssignment'
            ));
        } else {
            $placementRoundParticipantsTable = TableRegistry::getTableLocator()->get('PlacementRoundParticipants');
            $latestACY = $placementRoundParticipantsTable->latest_defined_academic_year_and_round($this->request->getData('PlacementPreference.applied_for'));

            if (!empty($latestACY)) {
                $selectedAcy = $acYear = $latestACY['academic_year'];
                $selectedRound = $latestACY['round'];
                $selectedCurrentUnit = $latestACY['applied_for'];

                $this->request = $this->request->withData('PlacementPreference.applied_for', $selectedCurrentUnit)
                    ->withData('PlacementPreference.academic_year', $selectedAcy)
                    ->withData('PlacementPreference.round', $selectedRound)
                    ->withData('PlacementPreference.program_id', $selectedProgID)
                    ->withData('PlacementPreference.program_type_id', $selectedProgTypeID);

                $this->initSearchPreferences();

                $preferredUnits = $placementRoundParticipantsTable->get_selected_participating_unit_name($this->request->getData());
                $participatingUnitsCount = count($preferredUnits) > 0 ? count($preferredUnits) : 0;
            }

            $programs_available_for_placement_preference = \Cake\Core\Configure::read('programs_available_for_placement_preference');
            $program_types_available_for_placement_preference = \Cake\Core\Configure::read('program_types_available_for_placement_preference');

            if ($this->request->getData('listStudentsPreference')) {
                $preference_deadline = $placementDeadlinesTable->find()
                    ->where([
                        'PlacementDeadlines.program_id' => $selectedProgID,
                        'PlacementDeadlines.applied_for' => $this->request->getData('PlacementPreference.applied_for'),
                        'PlacementDeadlines.program_type_id' => $selectedProgTypeID,
                        'PlacementDeadlines.academic_year LIKE' => $selectedAcy . '%',
                        'PlacementDeadlines.placement_round' => $selectedRound,
                        'PlacementDeadlines.deadline >' => $this->AcademicYear->getAcademicYearBegainingDate($selectedAcy, 'I')
                    ])
                    ->order(['PlacementDeadlines.academic_year' => 'DESC', 'PlacementDeadlines.placement_round' => 'DESC'])
                    ->disableHydration()
                    ->first() ?? [];
            } else {
                $preference_deadline = $placementDeadlinesTable->find()
                    ->where([
                        'PlacementDeadlines.program_id' => $selectedProgID,
                        'PlacementDeadlines.applied_for' => 'c~2',
                        'PlacementDeadlines.program_type_id' => $selectedProgTypeID,
                        'PlacementDeadlines.academic_year LIKE' => $selectedAcy . '%',
                        'PlacementDeadlines.placement_round' => $selectedRound,
                        'PlacementDeadlines.deadline >' => $this->AcademicYear->getAcademicYearBegainingDate($selectedAcy, 'I')
                    ])
                    ->order(['PlacementDeadlines.academic_year' => 'DESC', 'PlacementDeadlines.placement_round' => 'DESC'])
                    ->disableHydration()
                    ->first() ?? [];
            }
        }

        $placement_preferences = [];

        if (!empty($options['conditions'])) {
            $this->Paginator->settings = [
                'conditions' => $options['conditions'],
                'order' => empty($sort) && empty($direction) ? [
                    'PlacementPreferences.student_id' => 'ASC',
                    'PlacementPreferences.academic_year' => 'DESC',
                    'PlacementPreferences.round' => 'DESC',
                    'PlacementPreferences.preference_order' => 'ASC'
                ] : ["PlacementPreferences.{$sort}" => $direction],
                'limit' => !empty($selectedLimit) ? $selectedLimit : 100,
                'maxLimit' => !empty($selectedLimit) ? $selectedLimit : 100,
                'contain' => ['AcceptedStudents', 'Students', 'PlacementRoundParticipants']
            ];

            try {
                $placement_preferences = $this->Paginator->paginate($this->PlacementPreferences);
            } catch (NotFoundException $e) {
                return $this->redirect(['action' => 'index']);
            }
        }

        if (empty($placement_preferences) && !empty($options['conditions'])) {
            $this->Flash->info(__('No placement preference is found in a given search criteria.'));
        }

        $placementRoundParticipantsTable = TableRegistry::getTableLocator()->get('PlacementRoundParticipants');
        $preferenceOrderListCount = $placementRoundParticipantsTable->find()
            ->where([
                'PlacementRoundParticipants.academic_year' => $selectedAcy,
                'PlacementRoundParticipants.placement_round' => $selectedRound
            ])
            ->group(['PlacementRoundParticipants.placement_round', 'PlacementRoundParticipants.academic_year'])
            ->count();

        $availableAcademicYears = $placementRoundParticipantsTable->find('list')
            ->select(['academic_year' => 'PlacementRoundParticipants.academic_year'])
            ->group(['PlacementRoundParticipants.academic_year'])
            ->order(['PlacementRoundParticipants.academic_year' => 'ASC'])
            ->disableHydration()
            ->toArray();

        if (empty($availableAcademicYears)) {
            $currACY = $this->AcademicYear->currentAcademicYear();
            $availableAcademicYears[$currACY] = $currACY;
        }

        $availablePrograms = $placementRoundParticipantsTable->find('list')
            ->select(['program_id' => 'PlacementRoundParticipants.program_id'])
            ->group(['PlacementRoundParticipants.program_id'])
            ->disableHydration()
            ->toArray();

        $availableProgramTypes = $placementRoundParticipantsTable->find('list')
            ->select(['program_type_id' => 'PlacementRoundParticipants.program_type_id'])
            ->group(['PlacementRoundParticipants.program_type_id'])
            ->disableHydration()
            ->toArray();

        $programsTable = TableRegistry::getTableLocator()->get('Programs');
        if (!empty($availablePrograms)) {
            $programs = $programsTable->find('list')
                ->where(['Programs.id IN' => array_keys($availablePrograms)])
                ->disableHydration()
                ->toArray();
        } else {
            $programs_available_for_placement_preference = \Cake\Core\Configure::read('programs_available_for_placement_preference');
            $programs = $programsTable->find('list')
                ->where(['Programs.id IN' => $programs_available_for_placement_preference])
                ->disableHydration()
                ->toArray();
        }

        $programTypesTable = TableRegistry::getTableLocator()->get('ProgramTypes');
        if (!empty($availableProgramTypes)) {
            $programTypes = $programTypesTable->find('list')
                ->where(['ProgramTypes.id IN' => array_keys($availableProgramTypes)])
                ->disableHydration()
                ->toArray();
        } else {
            $program_types_available_for_placement_preference = \Cake\Core\Configure::read('program_types_available_for_placement_preference');
            $programTypes = $programTypesTable->find('list')
                ->where(['ProgramTypes.id IN' => $program_types_available_for_placement_preference])
                ->disableHydration()
                ->toArray();
        }

        if ($participatingUnitsCount != 0) {
            $preferenceOrderList = range(1, $participatingUnitsCount);
        } elseif ($preferenceOrderListCount != 0) {
            $preferenceOrderList = range(1, $preferenceOrderListCount);
        } else {
            $preferenceOrderList = range(1, 5);
        }

        if ($this->role_id != ROLE_STUDENT) {
            $appliedForList = $this->request->getData('PlacementPreference') ?
                $this->PlacementPreferences->get_defined_list_of_applied_for($this->request->getData('PlacementPreference')) :
                $this->PlacementPreferences->get_defined_list_of_applied_for(null, $availableAcademicYears ?: [$latestACY['academic_year']]);

            if ($this->request->getData('PlacementPreference.applied_for')) {
                $participatingUnits = $placementRoundParticipantsTable->find('list')
                    ->where([
                        'PlacementRoundParticipants.applied_for' => $this->request->getData('PlacementPreference.applied_for'),
                        'PlacementRoundParticipants.program_id' => $selectedProgID,
                        'PlacementRoundParticipants.program_type_id' => $selectedProgTypeID,
                        'PlacementRoundParticipants.academic_year' => $selectedAcy,
                        'PlacementRoundParticipants.placement_round' => $selectedRound
                    ])
                    ->select(['id', 'name'])
                    ->order(['PlacementRoundParticipants.name' => 'ASC'])
                    ->disableHydration()
                    ->toArray();
            } else {
                $participatingUnits = $placementRoundParticipantsTable->find('list')
                    ->where([
                        'PlacementRoundParticipants.program_id' => $selectedProgID,
                        'PlacementRoundParticipants.program_type_id' => $selectedProgTypeID,
                        'PlacementRoundParticipants.academic_year' => $latestACY['academic_year'] ?? $acYear,
                        'PlacementRoundParticipants.placement_round' => $latestACY['round'] ?? $selectedRound
                    ])
                    ->select(['id', 'name'])
                    ->order(['PlacementRoundParticipants.name' => 'ASC'])
                    ->disableHydration()
                    ->toArray();
            }

            $this->set(compact('appliedForList', 'participatingUnits'));
        }

        $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
        $allUnits = $departmentsTable->find('list')
            ->where(['Departments.active' => 1])
            ->disableHydration()
            ->toArray();

        $collegesTable = TableRegistry::getTableLocator()->get('Colleges');
        $colleges = $collegesTable->find('list')
            ->where(['Colleges.active' => 1])
            ->disableHydration()
            ->toArray();

        $departments = $departmentsTable->find('list')
            ->where(['Departments.active' => 1])
            ->disableHydration()
            ->toArray();

        if ($this->role_id == ROLE_COLLEGE) {
            $allUnits = $departmentsTable->allUnits(null, null, 1);
            $currentUnits = $departmentsTable->allUnits($this->role_id, $this->college_id);
        } elseif ($this->role_id == ROLE_DEPARTMENT) {
            $allUnits = $departmentsTable->allUnits(null, null, 1);
            $currentUnits = $departmentsTable->allUnits($this->role_id, $this->department_id);
        } elseif (in_array($this->role_id, [ROLE_REGISTRAR, ROLE_SYSADMIN])) {
            $allUnits = $departmentsTable->allUnits($this->role_id, null);
            $currentUnits = $allUnits;
        }

        $this->set(compact(
            'selectedAcy',
            'selectedRound',
            'selectedCurrentUnit',
            'selectedLimit',
            'preferenceOrderListCount',
            'preferenceOrderList',
            'currentUnits',
            'preferredUnits',
            'colleges',
            'departments',
            'programs',
            'programTypes',
            'allUnits',
            'placement_preferences',
            'appliedForList',
            'page',
            'sort',
            'direction'
        ));
    }

    public function recordPreference(?int $id = null)
    {
        $acceptedStudentsTable = TableRegistry::getTableLocator()->get('AcceptedStudents');
        $acceptedStudents = $acceptedStudentsTable->find()
            ->where(['AcceptedStudents.user_id' => $this->Auth->user('id')])
            ->contain(['Departments', 'Colleges', 'Programs', 'ProgramTypes'])
            ->disableHydration()
            ->first() ?? [];

        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $admittedStudent = $studentsTable->find()
            ->where(['Students.user_id' => $this->Auth->user('id')])
            ->contain(['AcceptedStudents', 'Departments', 'Colleges', 'Programs', 'ProgramTypes'])
            ->disableHydration()
            ->first() ?? [];

        if (!empty($acceptedStudents['AcceptedStudent']['department_id'])) {
            $placementRoundParticipantsTable = TableRegistry::getTableLocator()->get('PlacementRoundParticipants');
            $specializationDefined = $placementRoundParticipantsTable->find()
                ->where(['PlacementRoundParticipants.applied_for' => 'd~' . $acceptedStudents['AcceptedStudent']['department_id']])
                ->count();

            if ($specializationDefined == 0) {
                $this->Flash->info(__('You are not eligible for placement either you are already in the department or other specialization placement is not defined yet.'));
                return $this->redirect(['action' => 'index']);
            }
        }

        $academic_year = $this->AcademicYear->currentAcademicYear();
        $student_section_exam_status = $studentsTable->get_student_section($admittedStudent['Student']['id'] ?? null, null, null);
        $override_acyear = false;

        if (!empty($student_section_exam_status['Section']) && !$student_section_exam_status['Section']['archive']) {
            $selectedAcademicYear = $academic_year = $student_section_exam_status['Section']['academicyear'];
            $override_acyear = true;
        }

        $placementRoundParticipantsTable = TableRegistry::getTableLocator()->get('PlacementRoundParticipants');
        $departments = $placementRoundParticipantsTable->participating_unit_name($acceptedStudents, $academic_year);

        if (empty($departments)) {
            $studentExamStatusesTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');
            $x = $studentExamStatusesTable->getPreviousSemester($academic_year);
            $academic_year = $x['academic_year'] ?? $academic_year;
            $departments = $placementRoundParticipantsTable->participating_unit_name($acceptedStudents, $academic_year);
        }

        $placementParticipatingStudentsTable = TableRegistry::getTableLocator()->get('PlacementParticipatingStudents');
        $placementRound = $placementParticipatingStudentsTable->getNextRound($academic_year, $acceptedStudents['AcceptedStudent']['id'] ?? null);
        $roundlabel = $placementRoundParticipantsTable->roundLabel($placementRound);
        $applied_for = $placementRoundParticipantsTable->appliedFor($acceptedStudents, $academic_year);

        $placementDeadlinesTable = TableRegistry::getTableLocator()->get('PlacementDeadlines');
        $require_all_selected_switch = $placementRoundParticipantsTable->find()
            ->select(['require_all_selected'])
            ->where([
                'PlacementRoundParticipants.program_id IN' => \Cake\Core\Configure::read('programs_available_for_placement_preference'),
                'PlacementRoundParticipants.program_type_id IN' => \Cake\Core\Configure::read('program_types_available_for_placement_preference'),
                'PlacementRoundParticipants.applied_for' => $applied_for,
                'PlacementRoundParticipants.placement_round' => $placementRound,
                'PlacementRoundParticipants.academic_year LIKE' => $academic_year . '%'
            ])
            ->disableHydration()
            ->first()['require_all_selected'] ?? null;

        if ($this->request->getData('fillPreference')) {
            $deadLineStatus = $placementDeadlinesTable->getDeadlineStatus($acceptedStudents, $applied_for, $placementRound, $academic_year);

            $isThePlacementRun = $placementParticipatingStudentsTable->find()
                ->where([
                    'PlacementParticipatingStudents.program_id IN' => \Cake\Core\Configure::read('programs_available_for_placement_preference'),
                    'PlacementParticipatingStudents.program_type_id IN' => \Cake\Core\Configure::read('program_types_available_for_placement_preference'),
                    'PlacementParticipatingStudents.applied_for' => $applied_for,
                    'PlacementParticipatingStudents.round' => $placementRound,
                    'PlacementParticipatingStudents.academic_year LIKE' => $academic_year . '%',
                    'PlacementParticipatingStudents.placement_round_participant_id IS NOT NULL'
                ])
                ->count();

            if ($deadLineStatus == 1 && $isThePlacementRun == 0) {
                $this->set($this->request->getData());
                $validationErrors = $this->PlacementPreferences->validateMany($this->request->getData('PlacementPreference') ?? []);
                if (empty($validationErrors)) {
                    if (!$this->PlacementPreferences->isAlreadyEnteredPreference($this->request->getData('PlacementPreference.1'))) {
                        if ($this->PlacementPreferences->isAllPreferenceSelectedDifferent($this->request->getData('PlacementPreference'), $require_all_selected_switch)) {
                            if ($this->PlacementPreferences->saveMany($this->PlacementPreferences->newEntities($this->request->getData('PlacementPreference')))) {
                                $this->Flash->success(__('Your preferences are saved.'));
                                return $this->redirect(['action' => 'index']);
                            } else {
                                $this->Flash->error(__('The preferences could not be saved. Please, try again.'));
                            }
                        } else {
                            $this->Flash->error(__('Input Error: Please select different program preference for each preference order.'));
                        }
                    } else {
                        $this->Flash->error(__('You have already entered your preference. Please edit your preferences before the deadline.'));
                        return $this->redirect(['controller' => 'PlacementPreferences', 'action' => 'index']);
                    }
                } else {
                    $this->Flash->error(__('Please enter the input correctly'));
                }
            } else {
                if ($isThePlacementRun) {
                    $this->Flash->error(__('The defined placement has already run, and you can not edit your preference at this time.'));
                } else {
                    if ($deadLineStatus == 2) {
                        $this->Flash->error(__('Preference Deadline is passed. You can not record or change your preferences. Advise the registrar for more information'));
                    } else {
                        $this->Flash->info(__('Preference Deadline is not defined, please come again after announced by registrar or advise the registrar for more information.'));
                    }
                }
                return $this->redirect(['action' => 'index']);
            }
        }

        if ($id) {
            $firstRow = $this->PlacementPreferences->find()
                ->where(['PlacementPreferences.id' => $id])
                ->contain(['PlacementRoundParticipants'])
                ->disableHydration()
                ->first() ?? [];

            if (!empty($firstRow['PlacementRoundParticipant']['applied_for'])) {
                $applied_for = $firstRow['PlacementRoundParticipant']['applied_for'];
            } else {
                $applied_for = $placementRoundParticipantsTable->appliedFor($acceptedStudents, $firstRow['PlacementPreference']['academic_year']);
            }

            $academic_year = $firstRow['PlacementPreference']['academic_year'];
            $placementRound = $firstRow['PlacementPreference']['round'];

            $isThePlacementRun = $placementParticipatingStudentsTable->find()
                ->where([
                    'PlacementParticipatingStudents.program_id IN' => \Cake\Core\Configure::read('programs_available_for_placement_preference'),
                    'PlacementParticipatingStudents.program_type_id IN' => \Cake\Core\Configure::read('program_types_available_for_placement_preference'),
                    'PlacementParticipatingStudents.applied_for' => $applied_for,
                    'PlacementParticipatingStudents.round' => $firstRow['PlacementPreference']['round'],
                    'PlacementParticipatingStudents.academic_year LIKE' => $firstRow['PlacementPreference']['academic_year'] . '%',
                    'PlacementParticipatingStudents.placement_round_participant_id IS NOT NULL'
                ])
                ->count();

            if ($isThePlacementRun) {
                $this->Flash->error(__('The defined placement has already run, and you can not edit your preference at this time.'));
                return $this->redirect(['action' => 'index']);
            }

            $departments = $placementRoundParticipantsTable->get_participating_unit_for_edit($firstRow['PlacementPreference']['placement_round_participant_id']);

            $option_2 = [
                'conditions' => [
                    'PlacementPreferences.accepted_student_id' => $firstRow['PlacementPreference']['accepted_student_id'],
                    'PlacementPreferences.student_id' => $firstRow['PlacementPreference']['student_id'],
                    'PlacementPreferences.academic_year' => $firstRow['PlacementPreference']['academic_year'],
                    'PlacementPreferences.round' => $firstRow['PlacementPreference']['round']
                ],
                'order' => ['PlacementPreferences.preference_order' => 'ASC']
            ];

            $data = $this->PlacementPreferences->find()
                ->where($option_2['conditions'])
                ->order($option_2['order'])
                ->disableHydration()
                ->all()
                ->toArray();

            $formattedData = [];
            $i = 1;

            if (!empty($data)) {
                foreach ($data as $dev) {
                    if (($dev['preference_order'] ?? null) == $i) {
                        $formattedData['PlacementPreference'][$i] = $dev['PlacementPreference'];
                        $i++;
                    }
                }
            }

            $this->request = $this->request->withData($formattedData);
        } else {
            $deadLineStatus = $placementDeadlinesTable->getDeadlineStatus($acceptedStudents, $applied_for, $placementRound, $academic_year);

            if ($deadLineStatus == 2) {
                $this->Flash->error(__('The preference deadline for %s academic year %s round is passed. You can not record your preference now. Please ask the registrar for more information', $academic_year, $roundlabel));
                return $this->redirect(['action' => 'index']);
            } elseif ($deadLineStatus == 0) {
                $this->Flash->error(__('The preference deadline for %s academic year %s round is not announced yet by the registrar. Please ask the registrar for more information', $academic_year, $roundlabel));
                return $this->redirect(['action' => 'index']);
            } else {
                $options = [
                    'conditions' => [
                        'PlacementPreferences.accepted_student_id' => $acceptedStudents['AcceptedStudent']['id'] ?? null,
                        'PlacementPreferences.round' => $placementRound,
                        'PlacementPreferences.academic_year' => $academic_year
                    ]
                ];

                $firstRow = $this->PlacementPreferences->find()
                    ->where($options['conditions'])
                    ->disableHydration()
                    ->first() ?? [];

                if (!empty($firstRow)) {
                    return $this->redirect(['action' => 'record_preference', $firstRow['PlacementPreference']['id']]);
                }
            }
        }

        if ($departments) {
            $departmentcount = count($departments);
            $this->set(compact('departments', 'departmentcount'));
        } else {
            $this->Flash->info(__('There is no a placement preference setting defined by registrar for now, please come back when registrar announces to fill your preferences.'));
            return $this->redirect(['controller' => 'PlacementPreferences', 'action' => 'index']);
        }

        $collegesTable = TableRegistry::getTableLocator()->get('Colleges');
        $unitFor = $collegesTable->find()
            ->where(['Colleges.id' => $acceptedStudents['AcceptedStudent']['college_id'] ?? null])
            ->disableHydration()
            ->first() ?? [];

        $this->set(compact('roundlabel', 'unitFor', 'academic_year'));

        if (!empty($acceptedStudents)) {
            $studentname = $acceptedStudents['AcceptedStudent']['full_name'] ?? '';
            $studentnumber = $acceptedStudents['AcceptedStudent']['studentnumber'] ?? '';
            $collegename = $acceptedStudents['College']['name'] ?? '';
            $college_id = $acceptedStudents['College']['id'] ?? null;
            $accepted_student_id = $acceptedStudents['AcceptedStudent']['id'] ?? null;

            $student_id = !empty($admittedStudent) ? $admittedStudent['Student']['id'] : $this->student_id;

            if ($student_id != $this->student_id && !empty($acceptedStudents['AcceptedStudent']['id'])) {
                $student_id = $studentsTable->find()
                    ->select(['id'])
                    ->where(['Students.accepted_student_id' => $acceptedStudents['AcceptedStudent']['id']])
                    ->disableHydration()
                    ->first()['id'] ?? $student_id;
            }

            $acyear = $override_acyear ? $selectedAcademicYear : ($acceptedStudents['AcceptedStudent']['academicyear'] ?? $academic_year);

            $this->set(compact(
                'studentname',
                'studentnumber',
                'collegename',
                'college_id',
                'placementRound',
                'accepted_student_id',
                'student_id',
                'acyear',
                'acceptedStudents',
                'require_all_selected_switch'
            ));
        }
    }

    public function autoFillPreference(string $academicyear = '2019/20', string $targetUnitType = 'c', string $targetUnitValue = '', int $round = 1)
    {
        $academicyear = '2019/20';
        $acceptedStudentsTable = TableRegistry::getTableLocator()->get('AcceptedStudents');
        $placementRoundParticipantsTable = TableRegistry::getTableLocator()->get('PlacementRoundParticipants');

        if (!empty($targetUnitValue) && $targetUnitType == 'c') {
            $round = 1;
            $applied_for = 'c~' . $targetUnitValue;

            $accepted_students = $acceptedStudentsTable->find()
                ->where([
                    'AcceptedStudents.college_id' => $targetUnitValue,
                    'AcceptedStudents.academicyear LIKE' => $academicyear
                ])
                ->contain(['Students'])
                ->disableHydration()
                ->all()
                ->toArray();

            $detail_of_participating_department = $placementRoundParticipantsTable->find()
                ->where([
                    'PlacementRoundParticipants.type' => 'College',
                    'PlacementRoundParticipants.academic_year' => $academicyear,
                    'PlacementRoundParticipants.applied_for' => $applied_for,
                    'PlacementRoundParticipants.program_id' => 1,
                    'PlacementRoundParticipants.program_type_id' => 1,
                    'PlacementRoundParticipants.placement_round' => $round
                ])
                ->disableHydration()
                ->all()
                ->toArray();
        } elseif (!empty($targetUnitValue) && $targetUnitType == 'd') {
            $round = 2;
            $applied_for = 'd~' . $targetUnitValue;

            $accepted_students = $acceptedStudentsTable->find()
                ->where([
                    'AcceptedStudents.department_id' => $targetUnitValue,
                    'AcceptedStudents.academicyear LIKE' => $academicyear
                ])
                ->contain(['Students'])
                ->disableHydration()
                ->all()
                ->toArray();

            $detail_of_participating_department = $placementRoundParticipantsTable->find()
                ->where([
                    'PlacementRoundParticipants.type' => 'Department',
                    'PlacementRoundParticipants.academic_year' => $academicyear,
                    'PlacementRoundParticipants.applied_for' => $applied_for,
                    'PlacementRoundParticipants.program_id' => 1,
                    'PlacementRoundParticipants.program_type_id' => 1,
                    'PlacementRoundParticipants.placement_round' => $round
                ])
                ->disableHydration()
                ->all()
                ->toArray();
        } else {
            $accepted_students = [];
            $detail_of_participating_department = [];
        }

        $departments = [];
        foreach ($detail_of_participating_department as $participating_department) {
            $departments[] = $participating_department['id'];
        }

        $count = 0;
        $preference_selection = [];

        if (!empty($accepted_students)) {
            foreach ($accepted_students as $accepted_student) {
                $filled = $this->PlacementPreferences->find()
                    ->where([
                        'PlacementPreferences.accepted_student_id' => $accepted_student['AcceptedStudent']['id'],
                        'PlacementPreferences.student_id' => $accepted_student['Student']['id'],
                        'PlacementPreferences.academic_year' => $academicyear,
                        'PlacementPreferences.round' => $round
                    ])
                    ->count();

                if ($filled <= 0) {
                    shuffle($departments);
                    for ($i = 1; $i <= count($departments); $i++) {
                        $preference_selection[$count] = [
                            'accepted_student_id' => $accepted_student['AcceptedStudent']['id'],
                            'student_id' => $accepted_student['Student']['id'],
                            'academic_year' => $academicyear,
                            'user_id' => $accepted_student['AcceptedStudent']['user_id'],
                            'edited_by' => $accepted_student['AcceptedStudent']['user_id'],
                            'round' => $round,
                            'placement_round_participant_id' => $departments[$i - 1],
                            'preference_order' => $i
                        ];
                        $count++;
                    }
                }
            }
        }

        $this->PlacementPreferences->saveMany($this->PlacementPreferences->newEntities($preference_selection));

        return $this->redirect(['controller' => 'PlacementPreferences', 'action' => 'index']);
    }

    public function add()
    {
        $collegesTable = TableRegistry::getTableLocator()->get('Colleges');
        $colleges = $collegesTable->find('list')
            ->where(['Colleges.active' => 1])
            ->disableHydration()
            ->toArray();

        $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
        $departments = $departmentsTable->find('list')
            ->where(['Departments.active' => 1])
            ->disableHydration()
            ->toArray();

        $types = ['College' => 'College', 'Department' => 'Department', 'Specialization' => 'Specialization'];

        $placementRoundParticipantsTable = TableRegistry::getTableLocator()->get('PlacementRoundParticipants');
        $availableAcademicYears = $placementRoundParticipantsTable->find('list')
            ->select(['academic_year' => 'PlacementRoundParticipants.academic_year'])
            ->group(['PlacementRoundParticipants.academic_year'])
            ->order(['PlacementRoundParticipants.academic_year' => 'ASC'])
            ->disableHydration()
            ->toArray();

        if (empty($availableAcademicYears)) {
            $currACY = $this->AcademicYear->currentAcademicYear();
            $availableAcademicYears[$currACY] = $currACY;
        }

        $availablePrograms = $placementRoundParticipantsTable->find('list')
            ->select(['program_id' => 'PlacementRoundParticipants.program_id'])
            ->group(['PlacementRoundParticipants.program_id'])
            ->disableHydration()
            ->toArray();

        $availableProgramTypes = $placementRoundParticipantsTable->find('list')
            ->select(['program_type_id' => 'PlacementRoundParticipants.program_type_id'])
            ->group(['PlacementRoundParticipants.program_type_id'])
            ->disableHydration()
            ->toArray();

        $programsTable = TableRegistry::getTableLocator()->get('Programs');
        if (!empty($availablePrograms)) {
            $programs = $programsTable->find('list')
                ->where(['Programs.id IN' => array_keys($availablePrograms)])
                ->disableHydration()
                ->toArray();
        } else {
            $programs_available_for_placement_preference = \Cake\Core\Configure::read('programs_available_for_placement_preference');
            $programs = $programsTable->find('list')
                ->where(['Programs.id IN' => $programs_available_for_placement_preference])
                ->disableHydration()
                ->toArray();
        }

        $programTypesTable = TableRegistry::getTableLocator()->get('ProgramTypes');
        if (!empty($availableProgramTypes)) {
            $programTypes = $programTypesTable->find('list')
                ->where(['ProgramTypes.id IN' => array_keys($availableProgramTypes)])
                ->disableHydration()
                ->toArray();
        } else {
            $program_types_available_for_placement_preference = \Cake\Core\Configure::read('program_types_available_for_placement_preference');
            $programTypes = $programTypesTable->find('list')
                ->where(['ProgramTypes.id IN' => $program_types_available_for_placement_preference])
                ->disableHydration()
                ->toArray();
        }

        $latestACY = $placementRoundParticipantsTable->latest_defined_academic_year_and_round($this->request->getData('PlacementPreference.applied_for'));
        $appliedForList = $this->request->getData('PlacementPreference') ?
            $this->PlacementPreferences->get_defined_list_of_applied_for($this->request->getData('PlacementPreference')) :
            $this->PlacementPreferences->get_defined_list_of_applied_for(null, $availableAcademicYears ?: [$latestACY['academic_year']]);

        $fieldSetups = 'type, foreign_key, name, edit';

        if ($this->role_id == ROLE_COLLEGE) {
            $allUnits = $departmentsTable->allUnits(null, null, 1);
            $currentUnits = $departmentsTable->allUnits($this->role_id, $this->college_id);
        } elseif ($this->role_id == ROLE_DEPARTMENT) {
            $allUnits = $departmentsTable->allUnits(null, null, 1);
            $currentUnits = $departmentsTable->allUnits($this->role_id, $this->department_id);
        } elseif ($this->role_id == ROLE_REGISTRAR) {
            $allUnits = $departmentsTable->allUnits($this->role_id, null);
            $currentUnits = $allUnits;
        }

        $sections = [];
        $section_combo_id = null;

        $this->set(compact(
            'colleges',
            'types',
            'allUnits',
            'departments',
            'sections',
            'fieldSetups',
            'programs',
            'currentUnits',
            'section_combo_id',
            'programTypes',
            'latestACY',
            'appliedForList'
        ));
    }

    public function getSelectedParticipant()
    {
        $this->viewBuilder()->setLayout('ajax');

        $placementRoundParticipantsTable = TableRegistry::getTableLocator()->get('PlacementRoundParticipants');
        $placementRoundParticipants = $placementRoundParticipantsTable->find('list')
            ->where([
                'PlacementRoundParticipants.applied_for' => $this->request->getData('Search.applied_for'),
                'PlacementRoundParticipants.program_id' => $this->request->getData('Search.program_id'),
                'PlacementRoundParticipants.program_type_id' => $this->request->getData('Search.program_type_id'),
                'PlacementRoundParticipants.academic_year' => $this->request->getData('Search.academic_year'),
                'PlacementRoundParticipants.placement_round' => $this->request->getData('Search.placement_round')
            ])
            ->select(['id', 'name'])
            ->disableHydration()
            ->toArray();

        $preferenceOrders = [];
        $count = 1;

        foreach ($placementRoundParticipants as $id => $name) {
            $preferenceOrders[$count] = $count;
            $count++;
        }

        $this->set(compact('placementRoundParticipants', 'preferenceOrders'));
    }

    public function getSelectedStudent()
    {
        $this->viewBuilder()->setLayout('ajax');

        $placementEntranceExamResultEntriesTable = TableRegistry::getTableLocator()->get('PlacementEntranceExamResultEntries');
        $students = $placementEntranceExamResultEntriesTable->getStudentForPreferenceEntry($this->request->getData());

        $this->set(compact('students'));
    }

    public function autoSaveResult()
    {
        $this->viewBuilder()->setOption('serialize', true);
        $this->viewBuilder()->setOption('autoLayout', false);

        $exam_results = [];
        $save_is_ok = true;

        if ($this->request->getData('PlacementPreference')) {
            foreach ($this->request->getData('PlacementPreference') as $exam_result) {
                if (is_array($exam_result)) {
                    if (is_numeric($exam_result['preference_order'] ?? null)) {
                        $exam_results = $exam_result;
                        if (!is_numeric($exam_result['preference_order'])) {
                            $save_is_ok = false;
                        }
                        if ($save_is_ok) {
                            $data = [
                                'PlacementPreference' => $exam_results,
                                'academic_year' => $this->request->getData('Search.academic_year'),
                                'user_id' => $this->Auth->user('id'),
                                'edited_by' => $this->Auth->user('id'),
                                'round' => $this->request->getData('Search.placement_round')
                            ];

                            if (!empty($data['PlacementPreference']['id'])) {
                                $alreadyRecorded = $this->PlacementPreferences->find()
                                    ->where(['PlacementPreferences.id' => $data['PlacementPreference']['id']])
                                    ->disableHydration()
                                    ->first();
                            } else {
                                $alreadyRecorded = $this->PlacementPreferences->find()
                                    ->where([
                                        'PlacementPreferences.placement_round_participant_id' => $data['PlacementPreference']['placement_round_participant_id'],
                                        'PlacementPreferences.accepted_student_id' => $data['PlacementPreference']['accepted_student_id'],
                                        'PlacementPreferences.student_id' => $data['PlacementPreference']['student_id'],
                                        'PlacementPreferences.academic_year' => $data['PlacementPreference']['academic_year'],
                                        'PlacementPreferences.round' => $data['PlacementPreference']['round']
                                    ])
                                    ->disableHydration()
                                    ->first();
                            }

                            if (!empty($alreadyRecorded)) {
                                $data['PlacementPreference']['id'] = $alreadyRecorded['id'];
                            }

                            $entity = $this->PlacementPreferences->newEntity($data['PlacementPreference']);
                            $this->PlacementPreferences->save($entity);
                        }
                    } elseif (!empty($exam_result['id']) && empty($exam_result['preference_order'])) {
                        $delete = $this->PlacementPreferences->find()
                            ->where([
                                'PlacementPreferences.placement_round_participant_id' => $exam_result['placement_round_participant_id'],
                                'PlacementPreferences.id' => $exam_result['id'],
                                'PlacementPreferences.accepted_student_id' => $exam_result['accepted_student_id'],
                                'PlacementPreferences.student_id' => $exam_result['student_id']
                            ])
                            ->count();

                        if ($delete) {
                            $this->PlacementPreferences->deleteAll(['PlacementPreferences.id' => $exam_result['id']]);
                        }
                    }
                }
            }
        }
    }


    public function viewResultOfPlacement()
    {
        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $studentBasic = $studentsTable->find()
            ->where(['Students.id' => $this->student_id])
            ->contain(['AcceptedStudents'])
            ->disableHydration()
            ->first() ?? [];

        $allPreferenceEntryStudentsInterested = $this->PlacementPreferences->find()
            ->where(['PlacementPreferences.student_id' => $this->student_id])
            ->contain(['PlacementRoundParticipants', 'AcceptedStudents', 'Students'])
            ->order([
                'PlacementPreferences.academic_year' => 'DESC',
                'PlacementPreferences.round' => 'DESC',
                'PlacementPreferences.preference_order' => 'ASC'
            ])
            ->disableHydration()
            ->all()
            ->toArray();

        $all_placement_round_participant_ids = [];
        foreach ($allPreferenceEntryStudentsInterested as $key => $value) {
            if (empty($value['PlacementPreference']['placement_round_participant_id'])) {
                unset($allPreferenceEntryStudentsInterested[$key]);
            } else {
                $all_placement_round_participant_ids[$value['PlacementPreference']['placement_round_participant_id']] = $value['PlacementPreference']['placement_round_participant_id'];
            }
        }
        $allPreferenceEntryStudentsInterested = array_values($allPreferenceEntryStudentsInterested);

        $studentList = [];
        $last_placement_round = '';
        $last_placement_academic_year = '';
        $semester_to_use_for_cgpa = '';
        $freshmanResultSet = 0;
        $freshmanResultPercent = 0;
        $prepararoryResultSet = 0;
        $prepararoryResultPercent = 0;
        $entranceResultSet = 0;
        $entranceResultPercent = 0;
        $freshmanMaxResultDB = null;
        $prepMaxResultDB = null;
        $entranceMaxResultDB = null;

        $lastPreferenceoftheStudent = $this->PlacementPreferences->find()
            ->where([
                'PlacementPreferences.student_id' => $this->student_id,
                'OR' => [
                    'PlacementPreferences.preference_order IS NOT NULL',
                    'PlacementPreferences.preference_order != 0',
                    'PlacementPreferences.preference_order != ""'
                ]
            ])
            ->contain(['PlacementRoundParticipants'])
            ->order([
                'PlacementPreferences.academic_year' => 'DESC',
                'PlacementPreferences.round' => 'DESC',
                'PlacementPreferences.preference_order' => 'ASC'
            ])
            ->disableHydration()
            ->first() ?? [];

        if (!empty($lastPreferenceoftheStudent)) {
            $entrance_result_found = false;
            $last_placement_round = $lastPreferenceoftheStudent['PlacementPreference']['round'];
            $last_placement_academic_year = $lastPreferenceoftheStudent['PlacementPreference']['academic_year'];
            $semester_to_use_for_cgpa = !empty($lastPreferenceoftheStudent['PlacementRoundParticipant']['semester']) ?
                $lastPreferenceoftheStudent['PlacementRoundParticipant']['semester'] :
                ($lastPreferenceoftheStudent['PlacementPreference']['round'] == 1 ? 'I' : 'II');

            $resultType = [];
            $placementResultSettingsTable = TableRegistry::getTableLocator()->get('PlacementResultSettings');
            $placementSettings = $placementResultSettingsTable->find()
                ->where([
                    'PlacementResultSettings.applied_for' => $lastPreferenceoftheStudent['PlacementRoundParticipant']['applied_for'] ?? null,
                    'PlacementResultSettings.round' => $lastPreferenceoftheStudent['PlacementPreference']['round'],
                    'PlacementResultSettings.academic_year' => $lastPreferenceoftheStudent['PlacementPreference']['academic_year'],
                    'PlacementResultSettings.program_id' => $lastPreferenceoftheStudent['PlacementRoundParticipant']['program_id'] ?? null,
                    'PlacementResultSettings.program_type_id' => $lastPreferenceoftheStudent['PlacementRoundParticipant']['program_type_id'] ?? null
                ])
                ->disableHydration()
                ->all()
                ->toArray();

            if (!empty($placementSettings)) {
                foreach ($placementSettings as $pv) {
                    $resultType[$pv['result_type']] = $pv['percent'];
                    if ($pv['result_type'] == 'EHEECE_total_results') {
                        $prepMaxResultDB = !empty($pv['max_result']) && is_numeric($pv['max_result']) && (int)$pv['max_result'] > 0 ? (int)$pv['max_result'] : null;
                        $prepararoryResultSet = !empty($pv['percent']) && is_numeric($pv['percent']) && (int)$pv['percent'] > 0 ? 1 : 0;
                        $prepararoryResultPercent = !empty($pv['percent']) && is_numeric($pv['percent']) && (int)$pv['percent'] > 0 ? (int)$pv['percent'] : null;
                    } elseif ($pv['result_type'] == 'freshman_result') {
                        $freshmanMaxResultDB = !empty($pv['max_result']) && is_numeric($pv['max_result']) && (int)$pv['max_result'] > 0 ? (float)$pv['max_result'] : null;
                        $freshmanResultSet = !empty($pv['percent']) && is_numeric($pv['percent']) && (int)$pv['percent'] > 0 ? 1 : 0;
                        $freshmanResultPercent = !empty($pv['percent']) && is_numeric($pv['percent']) && (int)$pv['percent'] > 0 ? (int)$pv['percent'] : null;
                    } elseif ($pv['result_type'] == 'entrance_result') {
                        $entranceMaxResultDB = !empty($pv['max_result']) && is_numeric($pv['max_result']) && (int)$pv['max_result'] > 0 ? (int)$pv['max_result'] : null;
                        $entranceResultSet = !empty($pv['percent']) && is_numeric($pv['percent']) && (int)$pv['percent'] > 0 ? 1 : 0;
                        $entranceResultPercent = !empty($pv['percent']) && is_numeric($pv['percent']) && (int)$pv['percent'] > 0 ? (int)$pv['percent'] : null;
                    }
                }

                if (!empty($allPreferenceEntryStudentsInterested[0]['PlacementRoundParticipant']['group_identifier'])) {
                    $placementRoundParticipantsTable = TableRegistry::getTableLocator()->get('PlacementRoundParticipants');
                    $latestStudentPreferencePlacement_round_participants_ids = $placementRoundParticipantsTable->find('list')
                        ->where(['PlacementRoundParticipants.group_identifier' => $allPreferenceEntryStudentsInterested[0]['PlacementRoundParticipant']['group_identifier']])
                        ->select(['id'])
                        ->disableHydration()
                        ->toArray();

                    if (!empty($latestStudentPreferencePlacement_round_participants_ids)) {
                        $placementEntranceExamResultEntriesTable = TableRegistry::getTableLocator()->get('PlacementEntranceExamResultEntries');
                        $entranceResult = $placementEntranceExamResultEntriesTable->find()
                            ->where([
                                'PlacementEntranceExamResultEntries.student_id' => $this->student_id,
                                'PlacementEntranceExamResultEntries.placement_round_participant_id IN' => array_keys($latestStudentPreferencePlacement_round_participants_ids)
                            ])
                            ->select(['result', 'placement_round_participant_id'])
                            ->order([
                                'PlacementEntranceExamResultEntries.modified' => 'DESC',
                                'PlacementEntranceExamResultEntries.created' => 'DESC',
                                'PlacementEntranceExamResultEntries.result' => 'DESC'
                            ])
                            ->group([
                                'PlacementEntranceExamResultEntries.accepted_student_id',
                                'PlacementEntranceExamResultEntries.student_id',
                                'PlacementEntranceExamResultEntries.placement_round_participant_id'
                            ])
                            ->disableHydration()
                            ->first() ?? [];

                        if (!empty($entranceResult)) {
                            $entranceResultForPreference = $this->PlacementPreferences->find()
                                ->where([
                                    'PlacementPreferences.student_id' => $this->student_id,
                                    'PlacementPreferences.placement_round_participant_id' => $entranceResult['placement_round_participant_id']
                                ])
                                ->disableHydration()
                                ->first() ?? [];

                            if (!empty($entranceResultForPreference['id'])) {
                                $entrance_result_found = true;
                                if (
                                    isset($entranceResult['result']) &&
                                    is_numeric($entranceResult['result']) &&
                                    (int)$entranceResult['result'] >= 0 &&
                                    $entranceResultSet
                                ) {
                                    if (!empty($entranceMaxResultDB) && !empty($entranceResultPercent)) {
                                        $entrancePercent = $entranceResultPercent;
                                        $studentList[$entranceResultForPreference['id']]['PlacementSetting']['entrance'] = ($entranceResultPercent * (int)$entranceResult['result']) / $entranceMaxResultDB;
                                        $entrance = ($entranceResultPercent * (int)$entranceResult['result']) / $entranceMaxResultDB;
                                    } else {
                                        $studentList[$entranceResultForPreference['id']]['PlacementSetting']['entrance'] = ((int)DEFAULT_DEPARTMENT_ENTRANCE_RESULT_PERCENT_FOR_PLACEMENT * (int)$entranceResult['result']) / (int)ENTRANCEMAXIMUM;
                                        $entrance = ((int)DEFAULT_DEPARTMENT_ENTRANCE_RESULT_PERCENT_FOR_PLACEMENT * (int)$entranceResult['result']) / (int)ENTRANCEMAXIMUM;
                                    }
                                } elseif (
                                    isset($entranceResult['result']) &&
                                    is_numeric($entranceResult['result']) &&
                                    (int)$entranceResult['result'] >= 0
                                ) {
                                    $studentList[$entranceResultForPreference['id']]['PlacementSetting']['entrance'] = ((int)DEFAULT_DEPARTMENT_ENTRANCE_RESULT_PERCENT_FOR_PLACEMENT * (int)$entranceResult['result']) / (int)ENTRANCEMAXIMUM;
                                    $entrance = ((int)DEFAULT_DEPARTMENT_ENTRANCE_RESULT_PERCENT_FOR_PLACEMENT * (int)$entranceResult['result']) / (int)ENTRANCEMAXIMUM;
                                } else {
                                    $entrancePercent = $entranceResultPercent;
                                    $studentList[$entranceResultForPreference['id']]['PlacementSetting']['entrance'] = 0;
                                    $entrance = 0;
                                }
                            }
                        }
                    }
                }
            }

            if (
                is_numeric($studentBasic['AcceptedStudent']['EHEECE_total_results'] ?? null) &&
                (int)$studentBasic['AcceptedStudent']['EHEECE_total_results'] > 100
            ) {
                $firstPlacementPreferenceID = !empty($entranceResultForPreference['id']) ?
                    $entranceResultForPreference['id'] :
                    $lastPreferenceoftheStudent['PlacementPreference']['id'];

                if ($prepararoryResultSet) {
                    if (!empty($prepMaxResultDB) && !empty($prepararoryResultPercent)) {
                        $preparatoryPercent = $prepararoryResultPercent;
                        $studentList[$firstPlacementPreferenceID]['PlacementSetting']['prepartory'] = round((($prepararoryResultPercent * (int)$studentBasic['AcceptedStudent']['EHEECE_total_results']) / $prepMaxResultDB), 2);
                        $prepartory = round((($prepararoryResultPercent * (int)$studentBasic['AcceptedStudent']['EHEECE_total_results']) / $prepMaxResultDB), 2);
                    } else {
                        $studentList[$firstPlacementPreferenceID]['PlacementSetting']['prepartory'] = round((((int)DEFAULT_PREPARATORY_RESULT_PERCENT_FOR_PLACEMENT * (int)$studentBasic['AcceptedStudent']['EHEECE_total_results']) / (int)PREPARATORYMAXIMUM), 2);
                        $prepartory = round((((int)DEFAULT_PREPARATORY_RESULT_PERCENT_FOR_PLACEMENT * (int)$studentBasic['AcceptedStudent']['EHEECE_total_results']) / (int)PREPARATORYMAXIMUM), 2);
                    }
                } else {
                    $studentList[$firstPlacementPreferenceID]['PlacementSetting']['prepartory'] = round((((int)DEFAULT_PREPARATORY_RESULT_PERCENT_FOR_PLACEMENT * (int)$studentBasic['AcceptedStudent']['EHEECE_total_results']) / (int)PREPARATORYMAXIMUM), 2);
                    $prepartory = round((((int)DEFAULT_PREPARATORY_RESULT_PERCENT_FOR_PLACEMENT * (int)$studentBasic['AcceptedStudent']['EHEECE_total_results']) / (int)PREPARATORYMAXIMUM), 2);
                }
            }

            if (!empty($allPreferenceEntryStudentsInterested)) {
                foreach ($allPreferenceEntryStudentsInterested as $v) {
                    $studentExamStatusesTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');
                    $freshManresult = $studentExamStatusesTable->find()
                        ->where([
                            'StudentExamStatuses.student_id' => $this->student_id,
                            'StudentExamStatuses.academic_status_id !=' => DISMISSED_ACADEMIC_STATUS_ID,
                            'StudentExamStatuses.academic_year' => $last_placement_academic_year,
                            'StudentExamStatuses.semester' => $semester_to_use_for_cgpa
                        ])
                        ->select(['sgpa', 'cgpa'])
                        ->order([
                            'StudentExamStatuses.academic_year' => 'DESC',
                            'StudentExamStatuses.semester' => 'DESC',
                            'StudentExamStatuses.id' => 'DESC',
                            'StudentExamStatuses.created' => 'DESC'
                        ])
                        ->group(['StudentExamStatuses.student_id', 'StudentExamStatuses.semester', 'StudentExamStatuses.academic_year'])
                        ->disableHydration()
                        ->first() ?? [];

                    if (
                        !empty($freshManresult) &&
                        is_numeric($freshManresult['cgpa'] ?? null) &&
                        (float)$freshManresult['cgpa'] > (float)DEFAULT_MINIMUM_CGPA_FOR_PLACEMENT &&
                        $freshmanResultSet
                    ) {
                        if (!empty($freshmanMaxResultDB) && !empty($freshmanResultPercent)) {
                            $freshmanPercent = $freshmanResultPercent;
                            $studentList[$v['PlacementPreference']['id']]['PlacementSetting']['freshman'] = round((($freshmanResultPercent * (float)$freshManresult['cgpa']) / $freshmanMaxResultDB), 2);
                            $freshman = round((($freshmanResultPercent * (float)$freshManresult['cgpa']) / $freshmanMaxResultDB), 2);
                        } else {
                            $studentList[$v['PlacementPreference']['id']]['PlacementSetting']['freshman'] = round((((float)DEFAULT_FRESHMAN_RESULT_PERCENT_FOR_PLACEMENT * (float)$freshManresult['cgpa']) / (float)FRESHMANMAXIMUM), 2);
                            $freshman = round((((float)DEFAULT_FRESHMAN_RESULT_PERCENT_FOR_PLACEMENT * (float)$freshManresult['cgpa']) / (float)FRESHMANMAXIMUM), 2);
                        }
                    } elseif (
                        !empty($freshManresult) &&
                        is_numeric($freshManresult['cgpa'] ?? null) &&
                        (float)$freshManresult['cgpa'] > (float)DEFAULT_MINIMUM_CGPA_FOR_PLACEMENT
                    ) {
                        $studentList[$v['PlacementPreference']['id']]['PlacementSetting']['freshman'] = round((((float)DEFAULT_FRESHMAN_RESULT_PERCENT_FOR_PLACEMENT * (float)$freshManresult['cgpa']) / (float)FRESHMANMAXIMUM), 2);
                        $freshman = round((((float)DEFAULT_FRESHMAN_RESULT_PERCENT_FOR_PLACEMENT * (float)$freshManresult['cgpa']) / (float)FRESHMANMAXIMUM), 2);
                    } else {
                        $freshmanPercent = $freshmanResultPercent;
                        $studentList[$v['PlacementPreference']['id']]['PlacementSetting']['freshman'] = 0;
                        $freshman = 0;
                    }

                    if (!$entrance_result_found) {
                        $placementEntranceExamResultEntriesTable = TableRegistry::getTableLocator()->get('PlacementEntranceExamResultEntries');
                        $entranceResult = $placementEntranceExamResultEntriesTable->find()
                            ->where([
                                'PlacementEntranceExamResultEntries.student_id' => $this->student_id,
                                'PlacementEntranceExamResultEntries.placement_round_participant_id' => $v['PlacementRoundParticipant']['id'],
                                'PlacementEntranceExamResultEntries.created >' => $v['PlacementRoundParticipant']['created']
                            ])
                            ->select(['result', 'placement_round_participant_id'])
                            ->order([
                                'PlacementEntranceExamResultEntries.modified' => 'DESC',
                                'PlacementEntranceExamResultEntries.created' => 'DESC',
                                'PlacementEntranceExamResultEntries.result' => 'DESC'
                            ])
                            ->group([
                                'PlacementEntranceExamResultEntries.accepted_student_id',
                                'PlacementEntranceExamResultEntries.student_id',
                                'PlacementEntranceExamResultEntries.placement_round_participant_id'
                            ])
                            ->disableHydration()
                            ->first() ?? [];

                        if (
                            isset($entranceResult['result']) &&
                            is_numeric($entranceResult['result']) &&
                            (int)$entranceResult['result'] >= 0 &&
                            $entranceResultSet
                        ) {
                            if (!empty($entranceMaxResultDB) && !empty($entranceResultPercent)) {
                                $entrancePercent = $entranceResultPercent;
                                $studentList[$v['PlacementPreference']['id']]['PlacementSetting']['entrance'] = ($entranceResultPercent * (int)$entranceResult['result']) / $entranceMaxResultDB;
                                $entrance = ($entranceResultPercent * (int)$entranceResult['result']) / $entranceMaxResultDB;
                            } else {
                                $studentList[$v['PlacementPreference']['id']]['PlacementSetting']['entrance'] = ((int)DEFAULT_DEPARTMENT_ENTRANCE_RESULT_PERCENT_FOR_PLACEMENT * (int)$entranceResult['result']) / (int)ENTRANCEMAXIMUM;
                                $entrance = ((int)DEFAULT_DEPARTMENT_ENTRANCE_RESULT_PERCENT_FOR_PLACEMENT * (int)$entranceResult['result']) / (int)ENTRANCEMAXIMUM;
                            }
                        } elseif (
                            isset($entranceResult['result']) &&
                            is_numeric($entranceResult['result']) &&
                            (int)$entranceResult['result'] >= 0
                        ) {
                            $studentList[$v['PlacementPreference']['id']]['PlacementSetting']['entrance'] = ((int)DEFAULT_DEPARTMENT_ENTRANCE_RESULT_PERCENT_FOR_PLACEMENT * (int)$entranceResult['result']) / (int)ENTRANCEMAXIMUM;
                            $entrance = ((int)DEFAULT_DEPARTMENT_ENTRANCE_RESULT_PERCENT_FOR_PLACEMENT * (int)$entranceResult['result']) / (int)ENTRANCEMAXIMUM;
                        } else {
                            $entrancePercent = $entranceResultPercent;
                            $studentList[$v['PlacementPreference']['id']]['PlacementSetting']['entrance'] = 0;
                            $entrance = 0;
                        }
                    }

                    $placementParticipatingStudentsTable = TableRegistry::getTableLocator()->get('PlacementParticipatingStudents');
                    $assignedTo = $placementParticipatingStudentsTable->find()
                        ->where([
                            'PlacementParticipatingStudents.student_id' => $v['Student']['id'],
                            'PlacementParticipatingStudents.placement_round_participant_id' => $v['PlacementRoundParticipant']['id']
                        ])
                        ->contain(['PlacementRoundParticipants'])
                        ->disableHydration()
                        ->first() ?? [];

                    $studentList[$v['PlacementPreference']['id']]['PlacementSetting']['academic_year'] = $v['PlacementPreference']['academic_year'];
                    $studentList[$v['PlacementPreference']['id']]['PlacementSetting']['round'] = $v['PlacementPreference']['round'];
                    $studentList[$v['PlacementPreference']['id']]['PlacementSetting']['preference_order'] = $v['PlacementPreference']['preference_order'];
                    $studentList[$v['PlacementPreference']['id']]['PlacementSetting']['preference_name'] = $v['PlacementRoundParticipant']['name'];
                    $studentList[$v['PlacementPreference']['id']]['AcceptedStudent'] = $v['AcceptedStudent'];

                    if (!empty($assignedTo['PlacementRoundParticipant']['name'])) {
                        $studentList[$v['PlacementPreference']['id']]['Assigned'] = $assignedTo['PlacementRoundParticipant']['name'] . '( Prefered as - ' . $v['PlacementPreference']['preference_order'] . ')';
                        $assigned = $assignedTo['PlacementRoundParticipant']['name'] . '(' . $v['PlacementPreference']['preference_order'] . ')';
                    }
                }
            }

            if (empty($assigned)) {
                $placementParticipatingStudentsTable = TableRegistry::getTableLocator()->get('PlacementParticipatingStudents');
                $assignedTo = $placementParticipatingStudentsTable->find()
                    ->where(['PlacementParticipatingStudents.student_id' => $this->student_id])
                    ->contain(['PlacementRoundParticipants'])
                    ->order([
                        'PlacementParticipatingStudents.modified' => 'DESC',
                        'PlacementParticipatingStudents.academic_year' => 'DESC',
                        'PlacementParticipatingStudents.round' => 'DESC'
                    ])
                    ->disableHydration()
                    ->first() ?? [];

                if (!empty($assignedTo['PlacementRoundParticipant']['name'])) {
                    $assigned = $assignedTo['PlacementRoundParticipant']['name'];
                } elseif (
                    !empty($studentBasic['Student']['department_id']) &&
                    is_numeric($studentBasic['Student']['department_id']) &&
                    $studentBasic['Student']['department_id'] > 0
                ) {
                    $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
                    $assigned = $departmentsTable->find()
                            ->select(['name'])
                            ->where(['Departments.id' => $studentBasic['Student']['department_id']])
                            ->disableHydration()
                            ->first()['name'] . ' (Registrar Placed)';
                }
            }

            if (empty($freshmanMaxResultDB) || $freshmanResultSet == 0) {
                $freshmanMaxResultDB = FRESHMANMAXIMUM;
                $freshmanPercent = DEFAULT_FRESHMAN_RESULT_PERCENT_FOR_PLACEMENT;
            }

            if (empty($prepMaxResultDB) || $prepararoryResultSet == 0) {
                $prepMaxResultDB = PREPARATORYMAXIMUM;
                $preparatoryPercent = DEFAULT_PREPARATORY_RESULT_PERCENT_FOR_PLACEMENT;
            }

            if (empty($entranceMaxResultDB) || $entranceResultSet == 0) {
                $entranceMaxResultDB = ENTRANCEMAXIMUM;
                $entrancePercent = DEFAULT_DEPARTMENT_ENTRANCE_RESULT_PERCENT_FOR_PLACEMENT;
            }

            $this->set(compact(
                'freshmanMaxResultDB',
                'prepMaxResultDB',
                'entranceMaxResultDB',
                'freshmanResultSet',
                'prepararoryResultSet',
                'entranceResultSet',
                'preparatoryPercent',
                'freshmanPercent',
                'entrancePercent',
                'studentBasic',
                'studentList',
                'entrance',
                'prepartory',
                'freshman',
                'assigned'
            ));
        }
    }

    public function getStudentPreference($student_id)
    {
        $this->request->allowMethod(['post','get', 'ajax']); // Allow only AJAX and POST requests
        $this->viewBuilder()->setLayout('ajax');

        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $studentBasic = $studentsTable->find()
            ->where(['Students.id' => $student_id])
            ->contain(['AcceptedStudents'])
            ->disableHydration()
            ->first() ?? [];
        $allPreferenceEntryStudentsInterested = $this->PlacementPreferences->find()
            ->where(['PlacementPreferences.student_id' => $student_id])
            ->contain(['PlacementRoundParticipants', 'AcceptedStudents', 'Students'])
            ->order([
                'PlacementPreferences.academic_year' => 'DESC',
                'PlacementPreferences.round' => 'DESC',
                'PlacementPreferences.preference_order' => 'ASC'
            ])
            ->all()
            ->toArray();

        $all_placement_round_participant_ids = [];
        foreach ($allPreferenceEntryStudentsInterested as $key => $value) {
            if (empty($value['placement_round_participant_id'])) {
                unset($allPreferenceEntryStudentsInterested[$key]);
            } else {
                $all_placement_round_participant_ids[$value['placement_round_participant_id']] = $value['placement_round_participant_id'];
            }
        }
        $allPreferenceEntryStudentsInterested = array_values($allPreferenceEntryStudentsInterested);

        $studentList = [];
        $last_placement_round = '';
        $last_placement_academic_year = '';
        $semester_to_use_for_cgpa = '';
        $freshmanResultSet = 0;
        $freshmanResultPercent = 0;
        $prepararoryResultSet = 0;
        $prepararoryResultPercent = 0;
        $entranceResultSet = 0;
        $entranceResultPercent = 0;
        $freshmanMaxResultDB = null;
        $prepMaxResultDB = null;
        $entranceMaxResultDB = null;

        $lastPreferenceoftheStudent = $this->PlacementPreferences->find()
            ->where([
                'PlacementPreferences.student_id' => $student_id,
                'OR' => [
                    'PlacementPreferences.preference_order IS NOT NULL',
                    'PlacementPreferences.preference_order != 0',
                    'PlacementPreferences.preference_order != ""'
                ]
            ])
            ->contain(['PlacementRoundParticipants'])
            ->order([
                'PlacementPreferences.academic_year' => 'DESC',
                'PlacementPreferences.round' => 'DESC',
                'PlacementPreferences.preference_order' => 'ASC'
            ])
            ->first() ?? [];

        if (!empty($lastPreferenceoftheStudent)) {
            $entrance_result_found = false;
            $last_placement_round = $lastPreferenceoftheStudent['round'];
            $last_placement_academic_year = $lastPreferenceoftheStudent['academic_year'];
            $semester_to_use_for_cgpa = !empty($lastPreferenceoftheStudent['semester']) ?
                $lastPreferenceoftheStudent['semester'] :
                ($lastPreferenceoftheStudent['round'] == 1 ? 'I' : 'II');

            $resultType = [];
            $placementResultSettingsTable = TableRegistry::getTableLocator()->get('PlacementResultSettings');
            $placementSettings = $placementResultSettingsTable->find()
                ->where([
                    'PlacementResultSettings.applied_for' => $lastPreferenceoftheStudent['placement_round_participant']['applied_for'] ?? null,
                    'PlacementResultSettings.round' => $lastPreferenceoftheStudent['round'],
                    'PlacementResultSettings.academic_year' => $lastPreferenceoftheStudent['academic_year'],
                    'PlacementResultSettings.program_id' => $lastPreferenceoftheStudent['placement_round_participant']['program_id'] ?? null,
                    'PlacementResultSettings.program_type_id' => $lastPreferenceoftheStudent['placement_round_participant']['program_type_id'] ?? null
                ])
                ->disableHydration()
                ->all()
                ->toArray();

            if (!empty($placementSettings)) {
                foreach ($placementSettings as $pv) {
                    $resultType[$pv['result_type']] = $pv['percent'];
                    if ($pv['result_type'] == 'EHEECE_total_results') {
                        $prepMaxResultDB = !empty($pv['max_result']) && is_numeric($pv['max_result']) && (int)$pv['max_result'] > 0 ? (int)$pv['max_result'] : null;
                        $prepararoryResultSet = !empty($pv['percent']) && is_numeric($pv['percent']) && (int)$pv['percent'] > 0 ? 1 : 0;
                        $prepararoryResultPercent = !empty($pv['percent']) && is_numeric($pv['percent']) && (int)$pv['percent'] > 0 ? (int)$pv['percent'] : null;
                    } elseif ($pv['result_type'] == 'freshman_result') {
                        $freshmanMaxResultDB = !empty($pv['max_result']) && is_numeric($pv['max_result']) && (int)$pv['max_result'] > 0 ? (float)$pv['max_result'] : null;
                        $freshmanResultSet = !empty($pv['percent']) && is_numeric($pv['percent']) && (int)$pv['percent'] > 0 ? 1 : 0;
                        $freshmanResultPercent = !empty($pv['percent']) && is_numeric($pv['percent']) && (int)$pv['percent'] > 0 ? (int)$pv['percent'] : null;
                    } elseif ($pv['result_type'] == 'entrance_result') {
                        $entranceMaxResultDB = !empty($pv['max_result']) && is_numeric($pv['max_result']) && (int)$pv['max_result'] > 0 ? (int)$pv['max_result'] : null;
                        $entranceResultSet = !empty($pv['percent']) && is_numeric($pv['percent']) && (int)$pv['percent'] > 0 ? 1 : 0;
                        $entranceResultPercent = !empty($pv['percent']) && is_numeric($pv['percent']) && (int)$pv['percent'] > 0 ? (int)$pv['percent'] : null;
                    }
                }

                if (!empty($allPreferenceEntryStudentsInterested[0]['PlacementRoundParticipants']['group_identifier'])) {
                    $placementRoundParticipantsTable = TableRegistry::getTableLocator()->get('placement_round_participant');
                    $latestStudentPreferencePlacement_round_participants_ids = $placementRoundParticipantsTable->find('list')
                        ->where(['PlacementRoundParticipants.group_identifier' => $allPreferenceEntryStudentsInterested[0]['placement_round_participant']['group_identifier']])
                        ->select(['id'])
                        ->disableHydration()
                        ->toArray();

                    if (!empty($latestStudentPreferencePlacement_round_participants_ids)) {
                        $placementEntranceExamResultEntriesTable = TableRegistry::getTableLocator()->get('PlacementEntranceExamResultEntries');
                        $entranceResult = $placementEntranceExamResultEntriesTable->find()
                            ->where([
                                'PlacementEntranceExamResultEntries.student_id' => $student_id,
                                'PlacementEntranceExamResultEntries.placement_round_participant_id IN' => array_keys($latestStudentPreferencePlacement_round_participants_ids)
                            ])
                            ->select(['result', 'placement_round_participant_id'])
                            ->order([
                                'PlacementEntranceExamResultEntries.modified' => 'DESC',
                                'PlacementEntranceExamResultEntries.created' => 'DESC',
                                'PlacementEntranceExamResultEntries.result' => 'DESC'
                            ])
                            ->group([
                                'PlacementEntranceExamResultEntries.accepted_student_id',
                                'PlacementEntranceExamResultEntries.student_id',
                                'PlacementEntranceExamResultEntries.placement_round_participant_id'
                            ])
                            ->disableHydration()
                            ->first() ?? [];

                        if (!empty($entranceResult)) {
                            $entranceResultForPreference = $this->PlacementPreferences->find()
                                ->where([
                                    'PlacementPreferences.student_id' => $student_id,
                                    'PlacementPreferences.placement_round_participant_id' => $entranceResult['placement_round_participant_id']
                                ])
                                ->disableHydration()
                                ->first() ?? [];

                            if (!empty($entranceResultForPreference['id'])) {
                                $entrance_result_found = true;
                                if (
                                    isset($entranceResult['result']) &&
                                    is_numeric($entranceResult['result']) &&
                                    (int)$entranceResult['result'] >= 0 &&
                                    $entranceResultSet
                                ) {
                                    if (!empty($entranceMaxResultDB) && !empty($entranceResultPercent)) {
                                        $entrancePercent = $entranceResultPercent;
                                        $studentList[$entranceResultForPreference['id']]['PlacementSetting']['entrance'] = ($entranceResultPercent * (int)$entranceResult['result']) / $entranceMaxResultDB;
                                        $entrance = ($entranceResultPercent * (int)$entranceResult['result']) / $entranceMaxResultDB;
                                    } else {
                                        $studentList[$entranceResultForPreference['id']]['PlacementSetting']['entrance'] = ((int)DEFAULT_DEPARTMENT_ENTRANCE_RESULT_PERCENT_FOR_PLACEMENT * (int)$entranceResult['result']) / (int)ENTRANCEMAXIMUM;
                                        $entrance = ((int)DEFAULT_DEPARTMENT_ENTRANCE_RESULT_PERCENT_FOR_PLACEMENT * (int)$entranceResult['result']) / (int)ENTRANCEMAXIMUM;
                                    }
                                } elseif (
                                    isset($entranceResult['result']) &&
                                    is_numeric($entranceResult['result']) &&
                                    (int)$entranceResult['result'] >= 0
                                ) {
                                    $studentList[$entranceResultForPreference['id']]['PlacementSetting']['entrance'] = ((int)DEFAULT_DEPARTMENT_ENTRANCE_RESULT_PERCENT_FOR_PLACEMENT * (int)$entranceResult['result']) / (int)ENTRANCEMAXIMUM;
                                    $entrance = ((int)DEFAULT_DEPARTMENT_ENTRANCE_RESULT_PERCENT_FOR_PLACEMENT * (int)$entranceResult['result']) / (int)ENTRANCEMAXIMUM;
                                } else {
                                    $entrancePercent = $entranceResultPercent;
                                    $studentList[$entranceResultForPreference['id']]['PlacementSetting']['entrance'] = 0;
                                    $entrance = 0;
                                }
                            }
                        }
                    }
                }
            }

            if (
                is_numeric($studentBasic['accepted_student']['EHEECE_total_results'] ?? null) &&
                (int)$studentBasic['accepted_student']['EHEECE_total_results'] > 100
            ) {
                $firstPlacementPreferenceID = !empty($entranceResultForPreference['id']) ?
                    $entranceResultForPreference['id'] :
                    $lastPreferenceoftheStudent['id'];

                if ($prepararoryResultSet) {
                    if (!empty($prepMaxResultDB) && !empty($prepararoryResultPercent)) {
                        $preparatoryPercent = $prepararoryResultPercent;
                        $studentList[$firstPlacementPreferenceID]['PlacementSetting']['prepartory'] = round((($prepararoryResultPercent * (int)$studentBasic['accepted_student']['EHEECE_total_results']) / $prepMaxResultDB), 2);
                        $prepartory = round((($prepararoryResultPercent * (int)$studentBasic['accepted_student']['EHEECE_total_results']) / $prepMaxResultDB), 2);
                    } else {
                        $studentList[$firstPlacementPreferenceID]['PlacementSetting']['prepartory'] = round((((int)DEFAULT_PREPARATORY_RESULT_PERCENT_FOR_PLACEMENT * (int)$studentBasic['accepted_student']['EHEECE_total_results']) / (int)PREPARATORYMAXIMUM), 2);
                        $prepartory = round((((int)DEFAULT_PREPARATORY_RESULT_PERCENT_FOR_PLACEMENT * (int)$studentBasic['accepted_student']['EHEECE_total_results']) / (int)PREPARATORYMAXIMUM), 2);
                    }
                } else {
                    $studentList[$firstPlacementPreferenceID]['PlacementSetting']['prepartory'] = round((((int)DEFAULT_PREPARATORY_RESULT_PERCENT_FOR_PLACEMENT * (int)$studentBasic['accepted_student']['EHEECE_total_results']) / (int)PREPARATORYMAXIMUM), 2);
                    $prepartory = round((((int)DEFAULT_PREPARATORY_RESULT_PERCENT_FOR_PLACEMENT * (int)$studentBasic['accepted_student']['EHEECE_total_results']) / (int)PREPARATORYMAXIMUM), 2);
                }
            }

            if (!empty($allPreferenceEntryStudentsInterested)) {
                foreach ($allPreferenceEntryStudentsInterested as $v) {

                    $studentExamStatusesTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');
                    $freshManresult = $studentExamStatusesTable->find()
                        ->where([
                            'StudentExamStatuses.student_id' => $student_id,
                            'StudentExamStatuses.academic_status_id !=' => DISMISSED_ACADEMIC_STATUS_ID,
                            'StudentExamStatuses.academic_year' => $last_placement_academic_year,
                            'StudentExamStatuses.semester' => $semester_to_use_for_cgpa
                        ])
                        ->select(['sgpa', 'cgpa'])
                        ->order([
                            'StudentExamStatuses.academic_year' => 'DESC',
                            'StudentExamStatuses.semester' => 'DESC',
                            'StudentExamStatuses.id' => 'DESC',
                            'StudentExamStatuses.created' => 'DESC'
                        ])
                        ->group(['StudentExamStatuses.student_id', 'StudentExamStatuses.semester', 'StudentExamStatuses.academic_year'])
                        ->disableHydration()
                        ->first() ?? [];

                    if (
                        !empty($freshManresult) &&
                        is_numeric($freshManresult['cgpa'] ?? null) &&
                        (float)$freshManresult['cgpa'] > (float)DEFAULT_MINIMUM_CGPA_FOR_PLACEMENT &&
                        $freshmanResultSet
                    ) {
                        if (!empty($freshmanMaxResultDB) && !empty($freshmanResultPercent)) {
                            $freshmanPercent = $freshmanResultPercent;
                            $studentList[$v['id']]['PlacementSetting']['freshman'] = round((($freshmanResultPercent * (float)$freshManresult['cgpa']) / $freshmanMaxResultDB), 2);
                            $freshman = round((($freshmanResultPercent * (float)$freshManresult['cgpa']) / $freshmanMaxResultDB), 2);
                        } else {
                            $studentList[$v['id']]['PlacementSetting']['freshman'] = round((((float)DEFAULT_FRESHMAN_RESULT_PERCENT_FOR_PLACEMENT * (float)$freshManresult['cgpa']) / (float)FRESHMANMAXIMUM), 2);
                            $freshman = round((((float)DEFAULT_FRESHMAN_RESULT_PERCENT_FOR_PLACEMENT * (float)$freshManresult['cgpa']) / (float)FRESHMANMAXIMUM), 2);
                        }
                    } elseif (
                        !empty($freshManresult) &&
                        is_numeric($freshManresult['cgpa'] ?? null) &&
                        (float)$freshManresult['cgpa'] > (float)DEFAULT_MINIMUM_CGPA_FOR_PLACEMENT
                    ) {
                        $studentList[$v['id']]['PlacementSetting']['freshman'] = round((((float)DEFAULT_FRESHMAN_RESULT_PERCENT_FOR_PLACEMENT * (float)$freshManresult['cgpa']) / (float)FRESHMANMAXIMUM), 2);
                        $freshman = round((((float)DEFAULT_FRESHMAN_RESULT_PERCENT_FOR_PLACEMENT * (float)$freshManresult['cgpa']) / (float)FRESHMANMAXIMUM), 2);
                    } else {
                        $freshmanPercent = $freshmanResultPercent;
                        $studentList[$v['id']]['PlacementSetting']['freshman'] = 0;
                        $freshman = 0;
                    }

                    if (!$entrance_result_found) {
                        debug($v);
                        $placementEntranceExamResultEntriesTable = TableRegistry::getTableLocator()->get('PlacementEntranceExamResultEntries');
                        $entranceResult = $placementEntranceExamResultEntriesTable->find()
                            ->where([
                                'PlacementEntranceExamResultEntries.student_id' => $student_id,
                                'PlacementEntranceExamResultEntries.placement_round_participant_id' => $v['placement_round_participant']['id'],
                                'PlacementEntranceExamResultEntries.created >' => $v['placement_round_participant']['created']
                            ])
                            ->select(['result', 'placement_round_participant_id'])
                            ->order([
                                'PlacementEntranceExamResultEntries.modified' => 'DESC',
                                'PlacementEntranceExamResultEntries.created' => 'DESC',
                                'PlacementEntranceExamResultEntries.result' => 'DESC'
                            ])
                            ->group([
                                'PlacementEntranceExamResultEntries.accepted_student_id',
                                'PlacementEntranceExamResultEntries.student_id',
                                'PlacementEntranceExamResultEntries.placement_round_participant_id'
                            ])
                            ->disableHydration()
                            ->first() ?? [];

                        if (
                            isset($entranceResult['result']) &&
                            is_numeric($entranceResult['result']) &&
                            (int)$entranceResult['result'] >= 0 &&
                            $entranceResultSet
                        ) {
                            if (!empty($entranceMaxResultDB) && !empty($entranceResultPercent)) {
                                $entrancePercent = $entranceResultPercent;
                                $studentList[$v['id']]['PlacementSetting']['entrance'] = ($entranceResultPercent * (int)$entranceResult['result']) / $entranceMaxResultDB;
                                $entrance = ($entranceResultPercent * (int)$entranceResult['result']) / $entranceMaxResultDB;
                            } else {
                                $studentList[$v['id']]['PlacementSetting']['entrance'] = ((int)DEFAULT_DEPARTMENT_ENTRANCE_RESULT_PERCENT_FOR_PLACEMENT * (int)$entranceResult['result']) / (int)ENTRANCEMAXIMUM;
                                $entrance = ((int)DEFAULT_DEPARTMENT_ENTRANCE_RESULT_PERCENT_FOR_PLACEMENT * (int)$entranceResult['result']) / (int)ENTRANCEMAXIMUM;
                            }
                        } elseif (
                            isset($entranceResult['result']) &&
                            is_numeric($entranceResult['result']) &&
                            (int)$entranceResult['result'] >= 0
                        ) {
                            $studentList[$v['id']]['PlacementSetting']['entrance'] = ((int)DEFAULT_DEPARTMENT_ENTRANCE_RESULT_PERCENT_FOR_PLACEMENT * (int)$entranceResult['result']) / (int)ENTRANCEMAXIMUM;
                            $entrance = ((int)DEFAULT_DEPARTMENT_ENTRANCE_RESULT_PERCENT_FOR_PLACEMENT * (int)$entranceResult['result']) / (int)ENTRANCEMAXIMUM;
                        } else {
                            $entrancePercent = $entranceResultPercent;
                            $studentList[$v['id']]['PlacementSetting']['entrance'] = 0;
                            $entrance = 0;
                        }
                    }

                    $placementParticipatingStudentsTable = TableRegistry::getTableLocator()->get('PlacementParticipatingStudents');
                    $assignedTo = $placementParticipatingStudentsTable->find()
                        ->where([
                            'PlacementParticipatingStudents.student_id' => $student_id,
                            'PlacementParticipatingStudents.placement_round_participant_id' => $v['placement_round_participant']['id']
                        ])
                        ->contain(['PlacementRoundParticipants'])
                        ->disableHydration()
                        ->first() ?? [];

                    $studentList[$v['id']]['PlacementSetting']['academic_year'] = $v['academic_year'];
                    $studentList[$v['id']]['PlacementSetting']['round'] = $v['round'];
                    $studentList[$v['id']]['PlacementSetting']['preference_order'] = $v['preference_order'];
                    $studentList[$v['id']]['PlacementSetting']['preference_name'] = $v['placement_round_participant']['name'];
                    $studentList[$v['id']]['AcceptedStudent'] = $v['accepted_student'];

                    if (!empty($assignedTo['placement_round_participant']['name'])) {
                        $studentList[$v['id']]['Assigned'] = $assignedTo['placement_round_participant']['name'] . '( Prefered as - ' . $v['preference_order'] . ')';
                        $assigned = $assignedTo['placement_round_participant']['name'] . '(' . $v['preference_order'] . ')';
                    }
                }
            }

            if (empty($assigned)) {
                $placementParticipatingStudentsTable = TableRegistry::getTableLocator()->get('PlacementParticipatingStudents');
                $assignedTo = $placementParticipatingStudentsTable->find()
                    ->where(['PlacementParticipatingStudents.student_id' => $student_id])
                    ->contain(['PlacementRoundParticipants'])
                    ->order([
                        'PlacementParticipatingStudents.modified' => 'DESC',
                        'PlacementParticipatingStudents.academic_year' => 'DESC',
                        'PlacementParticipatingStudents.round' => 'DESC'
                    ])
                    ->disableHydration()
                    ->first() ?? [];

                if (!empty($assignedTo['placement_round_participant']['name'])) {
                    $assigned = $assignedTo['placement_round_participant']['name'];
                } elseif (
                    !empty($studentBasic['department_id']) &&
                    is_numeric($studentBasic['department_id']) &&
                    $studentBasic['department_id'] > 0
                ) {
                    $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
                    $assigned = $departmentsTable->find()
                            ->select(['name'])
                            ->where(['Departments.id' => $studentBasic['department_id']])
                            ->disableHydration()
                            ->first()['name'] . ' (Registrar Placed)';
                }
            }

            if (empty($freshmanMaxResultDB) || $freshmanResultSet == 0) {
                $freshmanMaxResultDB = FRESHMANMAXIMUM;
                $freshmanPercent = DEFAULT_FRESHMAN_RESULT_PERCENT_FOR_PLACEMENT;
            }

            if (empty($prepMaxResultDB) || $prepararoryResultSet == 0) {
                $prepMaxResultDB = PREPARATORYMAXIMUM;
                $preparatoryPercent = DEFAULT_PREPARATORY_RESULT_PERCENT_FOR_PLACEMENT;
            }

            if (empty($entranceMaxResultDB) || $entranceResultSet == 0) {
                $entranceMaxResultDB = ENTRANCEMAXIMUM;
                $entrancePercent = DEFAULT_DEPARTMENT_ENTRANCE_RESULT_PERCENT_FOR_PLACEMENT;
            }


            $this->set(compact(
                'freshmanMaxResultDB',
                'prepMaxResultDB',
                'entranceMaxResultDB',
                'freshmanResultSet',
                'prepararoryResultSet',
                'entranceResultSet',
                'preparatoryPercent',
                'freshmanPercent',
                'entrancePercent',
                'studentBasic',
                'studentList',
                'entrance',
                'prepartory',
                'freshman',
                'assigned'
            ));
        }


        $this->set(compact('studentBasic', 'studentList', 'entrance', 'prepartory', 'freshman', 'assigned'));
    }
}
