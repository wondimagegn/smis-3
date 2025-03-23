<?php

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Event\Event;

class PlacementDeadlinesController extends AppController
{

    public $name = 'PlacementDeadlines';
    public $menuOptions = array(
        'parent' => 'placement',
        'alias' => array(
            'add' => 'Add Placement Deadline',
            'index' => 'List Placement Deadlines'
        ),
        'exclude' => array(
            'get_participant_unit',
            'view',
            'edit'
        ),
    );

    public function initialize()
    {

        parent::initialize();
        $this->loadComponent('AcademicYear');
        $this->loadComponent('Paginator'); // Ensure Paginator is loaded


    }

    public function beforeRender(Event $event)
    {

        parent::beforeRender($event);
        //$acyear_array_data = $this->AcademicYear->acyear_array();
        $availableAcademicYears = ClassRegistry::init('PlacementRoundParticipant')->find('list', array(
            'fields' => array('PlacementRoundParticipant.academic_year', 'PlacementRoundParticipant.academic_year'),
            'group' => array('PlacementRoundParticipant.academic_year'),
            'order' => array('PlacementRoundParticipant.academic_year' => 'DESC')
        ));

        $defaultacademicyear = $current_academicyear = $this->AcademicYear->currentAcademicYear();

        if (empty($availableAcademicYears)) {
            $acyear_array_data = $this->AcademicYear->academicYearInArray(
                ((explode('/', $current_academicyear)[0]) - 2),
                (explode('/', $current_academicyear)[0])
            );
        } else {
            $acyear_array_data = $availableAcademicYears;
        }

        //debug($acyear_array_data);

        $this->set(compact('acyear_array_data', 'defaultacademicyear'));
        //$this->set('defaultacademicyear', $defaultacademicyear);
    }

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
        // $this->Auth->Allow('add', 'index', 'get_participant_unit', 'view', 'edit');
    }

    public function index()
    {

        //$this->PlacementDeadline->recursive = 0;
        $this->Paginator->settings = array(
            'order' => array(
                'PlacementDeadline.academic_year' => 'DESC',
                'PlacementDeadline.placement_round' => 'DESC',
                'PlacementDeadline.modified' => 'DESC'
            ),
            'recursive' => 0
        );

        if ($this->role_id == ROLE_COLLEGE) {
            $allUnit = ClassRegistry::init('Department')->allUnits(null, null, 1);
            $currentUnits = ClassRegistry::init('Department')->allUnits($this->role_id, $this->college_id);
        } elseif ($this->role_id == ROLE_DEPARTMENT) {
            $allUnit = ClassRegistry::init('Department')->allUnits(null, null, 1);
            $currentUnits = ClassRegistry::init('Department')->allUnits($this->role_id, $this->department_id);
        } else {
            if ($this->role_id == ROLE_REGISTRAR) {
                $allUnit = ClassRegistry::init('Department')->allUnits($this->role_id, null);
                $currentUnits = $allUnit;
            }
        }

        $allUnits = array();
        if (!empty($allUnit)) {
            foreach ($allUnit as $ak => $v) {
                foreach ($v as $vvk => $vvv) {
                    $allUnits[$vvk] = $vvv;
                }
            }
        }

        $this->set(compact('allUnits'));
        $this->set('placementDeadlines', $this->Paginator->paginate());
    }

    public function view($id = null)
    {

        if (!$this->PlacementDeadline->exists($id)) {
            throw new NotFoundException(__('Invalid placement deadline'));
        }

        $options = array('conditions' => array('PlacementDeadline.' . $this->PlacementDeadline->primaryKey => $id));
        $this->set('placementDeadline', $this->PlacementDeadline->find('first', $options));
        $colleges = ClassRegistry::init('College')->find('list', array('conditions' => array('College.active' => 1)));
        $departments = ClassRegistry::init('Department')->find(
            'list',
            array('conditions' => array('Department.active' => 1))
        );

        $types = array(
            'College' => 'College',
            'Department' => 'Department',
            'Specialization' => 'Specialization'
        );

        // $programs = ClassRegistry::init('Program')->find('list');
        // $programTypes = ClassRegistry::init('ProgramType')->find('list');

        $programs_available_for_placement_preference = Configure::read('programs_available_for_placement_preference');
        $program_types_available_for_placement_preference = Configure::read(
            'program_types_available_for_placement_preference'
        );

        $programs = ClassRegistry::init('Program')->find(
            'list',
            array('conditions' => array('Program.id' => $programs_available_for_placement_preference))
        );
        $programTypes = ClassRegistry::init('ProgramType')->find(
            'list',
            array('conditions' => array('ProgramType.id' => $program_types_available_for_placement_preference))
        );

        $allUnit = ClassRegistry::init('Department')->allUnits();
        $allUnits = array();

        foreach ($allUnit as $ak => $v) {
            foreach ($v as $vvk => $vvv) {
                $allUnits[$vvk] = $vvv;
            }
        }

        $this->set(
            compact(
                'colleges',
                'types',
                'allUnits',
                'departments',
                'colleges',
                'fieldSetups',
                'programs',
                'programTypes'
            )
        );
    }

    public function add()
    {

        $colleges = ClassRegistry::init('College')->find('list', array('conditions' => array('College.active' => 1)));
        $departments = ClassRegistry::init('Department')->find(
            'list',
            array('conditions' => array('Department.active' => 1))
        );

        if ($this->request->is('post')) {
            if (isset($this->request->data['PlacementDeadline']) && !empty($this->request->data['PlacementDeadline'])) {
                // check duplication
                $findDefinedParticipants = ClassRegistry::init('PlacementRoundParticipant')->isPossibleToDefineDeadline(
                    $this->request->data
                );

                if (isset($findDefinedParticipants) && !empty($findDefinedParticipants)) {
                    if (!$this->PlacementDeadline->isDuplicated($this->request->data)) {
                        $this->request->data['PlacementDeadline']['group_identifier'] = $findDefinedParticipants;
                        $this->PlacementDeadline->create();
                        if ($this->PlacementDeadline->save($this->request->data)) {
                            $this->Flash->success(
                                __('The placement deadline has been saved.'),
                                'default',
                                array('class' => 'success-message success-box')
                            );
                            return $this->redirect(array('action' => 'index'));
                        } else {
                            $this->Flash->error(__('The placement deadline could not be saved. Please, try again.'));
                        }
                    } else {
                        $this->Flash->error(
                            __(
                                'The there is aleady defined deadline for ' . (count(
                                    explode('c~', $this->request->data['PlacementDeadline']['applied_for'])
                                ) > 1 ? $colleges[explode(
                                    'c~',
                                    $this->request->data['PlacementDeadline']['applied_for']
                                )[1]] : $departments[explode(
                                    'd~',
                                    $this->request->data['PlacementDeadline']['applied_for']
                                )[1]]) . ' in ' . $this->request->data['PlacementDeadline']['academic_year'] . ' for round ' . $this->request->data['PlacementDeadline']['placement_round'] . '. Please chnage it or try again.'
                            )
                        );
                    }
                } else {
                    if ($findDefinedParticipants == false) {
                        $this->Flash->error(
                            __(
                                'Unable to create placement deadline due participant is not defined, please define participants first to set a deadline.'
                            )
                        );
                        return $this->redirect(array('controller' => 'PlacementRoundParticipants', 'action' => 'add'));
                    }
                }
            }
        }

        $types = array(
            'College' => 'College',
            'Department' => 'Department',
            'Specialization' => 'Specialization'
        );
        // $programs = ClassRegistry::init('Program')->find('list');
        // $programTypes = ClassRegistry::init('ProgramType')->find('list');

        $programs_available_for_placement_preference = Configure::read('programs_available_for_placement_preference');
        $program_types_available_for_placement_preference = Configure::read(
            'program_types_available_for_placement_preference'
        );

        $programs = ClassRegistry::init('Program')->find(
            'list',
            array('conditions' => array('Program.id' => $programs_available_for_placement_preference))
        );
        $programTypes = ClassRegistry::init('ProgramType')->find(
            'list',
            array('conditions' => array('ProgramType.id' => $program_types_available_for_placement_preference))
        );


        $fieldSetups = 'type,foreign_key,name,edit';
        //$allUnits = ClassRegistry::init('Department')->allUnits();
        if ($this->role_id == ROLE_COLLEGE) {
            $allUnits = ClassRegistry::init('Department')->allUnits(null, null, 1);
            $currentUnits = ClassRegistry::init('Department')->allUnits($this->role_id, $this->college_id);
        } elseif ($this->role_id == ROLE_DEPARTMENT) {
            $allUnits = ClassRegistry::init('Department')->allUnits(null, null, 1);
            $currentUnits = ClassRegistry::init('Department')->allUnits($this->role_id, $this->department_id);
        } else {
            if ($this->role_id == ROLE_REGISTRAR) {
                $allUnits = ClassRegistry::init('Department')->allUnits($this->role_id, null);
                $currentUnits = $allUnits;
            }
        }

        $this->set(
            compact(
                'colleges',
                'types',
                'allUnits',
                'departments',
                'colleges',
                'fieldSetups',
                'programs',
                'programTypes'
            )
        );
    }

    public function edit($id = null)
    {

        $colleges = ClassRegistry::init('College')->find('list', array('conditions' => array('College.active' => 1)));
        $departments = ClassRegistry::init('Department')->find(
            'list',
            array('conditions' => array('Department.active' => 1))
        );

        if (!$this->PlacementDeadline->exists($id)) {
            throw new NotFoundException(__('Invalid placement deadline'));
        }

        if ($this->request->is(array('post', 'put'))) {
            if (!$this->PlacementDeadline->isDuplicated($this->request->data)) {
                if ($this->PlacementDeadline->save($this->request->data)) {
                    $this->Flash->success(__('The placement deadline has been updated.'));
                    return $this->redirect(array('action' => 'index'));
                } else {
                    $this->Flash->error(__('The placement deadline could not be saved. Please, try again.'));
                }
            } else {
                $this->Flash->error(
                    __(
                        'The there is aleady defined deadline for ' . (count(
                            explode('c~', $this->request->data['PlacementDeadline']['applied_for'])
                        ) > 1 ? $colleges[explode(
                            'c~',
                            $this->request->data['PlacementDeadline']['applied_for']
                        )[1]] : $departments[explode(
                            'd~',
                            $this->request->data['PlacementDeadline']['applied_for']
                        )[1]]) . ' in ' . $this->request->data['PlacementDeadline']['academic_year'] . ' for round ' . $this->request->data['PlacementDeadline']['placement_round'] . '. Please chnage it or try again.'
                    )
                );
            }
        } else {
            $options = array('conditions' => array('PlacementDeadline.' . $this->PlacementDeadline->primaryKey => $id));
            $this->request->data = $this->PlacementDeadline->find('first', $options);
        }

        $types = array(
            'College' => 'College',
            'Department' => 'Department',
            'Specialization' => 'Specialization'
        );
        // $programs = ClassRegistry::init('Program')->find('list');
        // $programTypes = ClassRegistry::init('ProgramType')->find('list');

        $programs_available_for_placement_preference = Configure::read('programs_available_for_placement_preference');
        $program_types_available_for_placement_preference = Configure::read(
            'program_types_available_for_placement_preference'
        );

        $programs = ClassRegistry::init('Program')->find(
            'list',
            array('conditions' => array('Program.id' => $programs_available_for_placement_preference))
        );
        $programTypes = ClassRegistry::init('ProgramType')->find(
            'list',
            array('conditions' => array('ProgramType.id' => $program_types_available_for_placement_preference))
        );

        $fieldSetups = 'type,foreign_key,name,edit';
        //$allUnits = ClassRegistry::init('Department')->allUnits();

        if ($this->role_id == ROLE_COLLEGE) {
            $allUnits = ClassRegistry::init('Department')->allUnits(null, null, 1);
            $currentUnits = ClassRegistry::init('Department')->allUnits($this->role_id, $this->college_id);
        } elseif ($this->role_id == ROLE_DEPARTMENT) {
            $allUnits = ClassRegistry::init('Department')->allUnits(null, null, 1);
            $currentUnits = ClassRegistry::init('Department')->allUnits($this->role_id, $this->department_id);
        } else {
            if ($this->role_id == ROLE_REGISTRAR) {
                $allUnits = ClassRegistry::init('Department')->allUnits($this->role_id, null);
                $currentUnits = $allUnits;
            }
        }

        $this->set(
            compact(
                'colleges',
                'types',
                'allUnits',
                'departments',
                'colleges',
                'fieldSetups',
                'programs',
                'programTypes'
            )
        );
    }

    public function delete($id = null)
    {

        $this->PlacementDeadline->id = $id;
        if (!$this->PlacementDeadline->exists()) {
            throw new NotFoundException(__('Invalid placement deadline'));
        }
        //$this->request->allowMethod('post', 'delete');
        if ($this->PlacementDeadline->delete()) {
            $this->Flash->success(__('The placement deadline has been deleted.'));
        } else {
            $this->Flash->error(__('The placement deadline could not be deleted. Please, try again.'));
        }
        return $this->redirect(array('action' => 'index'));
	}
}
