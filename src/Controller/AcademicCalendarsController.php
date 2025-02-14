<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\EventInterface;
use Cake\ORM\TableRegistry;

use Cake\Core\Configure;



class AcademicCalendarsController extends AppController
{
    public $paginate = [];
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('AcademicYear');
        $this->loadComponent('Paginator'); // Ensure Paginator is loaded

        $this->viewBuilder()->setHelpers(['DatePicker']);
    }



    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        /*
        $this->Authentication->allowUnauthenticated([
            'autoSaveExtension',
            'extendingCalendar',
            'getDepartmentsThatHaveTheSelectedProgram'
        ]);
        */
    }

    public function beforeRender(EventInterface $event)
    {
        parent::beforeRender($event);

        $acyearArrayData = $this->AcademicYear->academicYearInArray(date('Y') - 10, date('Y'));
        $defaultAcademicYear = $this->AcademicYear->current_academicyear();

        foreach ($acyearArrayData as $key => $value) {
            if ($value == $defaultAcademicYear) {
                $defaultAcademicYear = $key;
                break;
            }
        }

        $this->set(compact('acyearArrayData', 'defaultAcademicYear'));
    }

    public function index()
    {

        $currentAcyAndSemester = $this->AcademicYear->currentAcyAndSemester();
        $this->_initSearchCalendar();

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
            $this->_initClearSessionFilters();
            $this->_initSearchCalendar();
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
        $academicCalendar = $this->AcademicCalendars->get($id, [
            'contain' => [ 'Programs', 'ProgramTypes', 'ExtendingAcademicCalendars'],
        ]);

        $this->set('academicCalendar', $academicCalendar);
    }

    public function add()
    {
        $academicCalendar = $this->AcademicCalendars->newEntity();
        if ($this->request->is('post')) {
            $academicCalendar = $this->AcademicCalendars->patchEntity($academicCalendar, $this->request->getData());
            if ($this->AcademicCalendars->save($academicCalendar)) {
                $this->Flash->success(__('The academic calendar has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The academic calendar could not be saved. Please, try again.'));
        }
        $programs = $this->AcademicCalendars->Programs->find('list', ['limit' => 200]);
        $programTypes = $this->AcademicCalendars->ProgramTypes->find('list', ['limit' => 200]);
        $this->set(compact('academicCalendar',
            'programs', 'programTypes'));
    }


    public function edit($id = null)
    {
        $academicCalendar = $this->AcademicCalendars->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $academicCalendar = $this->AcademicCalendars->patchEntity($academicCalendar, $this->request->getData());
            if ($this->AcademicCalendars->save($academicCalendar)) {
                $this->Flash->success(__('The academic calendar has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The academic calendar could not be saved. Please, try again.'));
        }
        $programs = $this->AcademicCalendars->Programs->find('list', ['limit' => 200]);
        $programTypes = $this->AcademicCalendars->ProgramTypes->find('list', ['limit' => 200]);
        $this->set(compact('academicCalendar', 'programs', 'programTypes'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $academicCalendar = $this->AcademicCalendars->get($id);
        if ($this->AcademicCalendars->delete($academicCalendar)) {
            $this->Flash->success(__('The academic calendar has been deleted.'));
        } else {
            $this->Flash->error(__('The academic calendar could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }


    private function _initSearchDefineCalendar()
    {
        $session = $this->getRequest()->getSession();

        if (!empty($this->request->getData('AcademicCalendar'))) {
            $session->write('search_define_calendar', $this->request->getData('AcademicCalendar'));
        } elseif ($session->check('search_define_calendar')) {
            $this->request = $this->request->withData('AcademicCalendar', $session->read('search_define_calendar'));
        }
    }

    private function _initSearchCalendar()
    {
        $session = $this->getRequest()->getSession();

        if (!empty($this->request->getData('Search'))) {
            $session->write('search_calendar', $this->request->getData('Search'));
        } elseif ($session->check('search_calendar')) {
            $this->request = $this->request->withData('Search', $session->read('search_calendar'));
        }
    }

    private function _initClearSessionFilters()
    {
        $session = $this->getRequest()->getSession();

        if ($session->check('search_calendar')) {
            $session->delete('search_calendar');
        }

        if ($session->check('search_define_calendar')) {
            $session->delete('search_define_calendar');
        }
    }
}
