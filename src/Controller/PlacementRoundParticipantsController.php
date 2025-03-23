<?php

namespace App\Controller;

use App\Controller\AppController;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
class PlacementRoundParticipantsController extends AppController
{

    public $name = 'PlacementRoundParticipants';

    public $menuOptions = array(
        'parent' => 'placement',
        'alias' => array(
            'index' => 'List Placement Participants',
            'add' => 'Add Placement Participant',

        ),
        'exclude' => array(
            'get_participant_unit',
            'get_selected_participant_unit'
        ),

    );

    public function initialize()
    {

        parent::initialize();
        $this->loadComponent('AcademicYear');
        $this->loadComponent('EthiopicDateTime');
        $this->loadComponent('Paginator');
    }

    public function beforeRender(Event $event)
    {

        parent::beforeRender($event);
        //$acyear_array_data = $this->AcademicYear->acyear_array();
        $acyear_array_data = $this->AcademicYear->academicYearInArray(date('Y') - 2, date('Y') - 1);
        $this->set(compact('acyear_array_data'));
    }

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
        /* $this->Auth->Allow(
            'edit',
            'add',
            'index',
            'get_participant_unit',
            'get_selected_participant_unit'
        ); */
    }

    public function index()
    {

        //debug($this->request->data);

        if (isset($this->request->data['search']) && !empty($this->request->data)) {
            debug($this->request->data);
            $options = array(
                'conditions' => array(
                    'PlacementRoundParticipant.academic_year' => $this->request->data['PlacementRoundParticipant']['academic_year'],
                    'PlacementRoundParticipant.placement_round' => $this->request->data['PlacementRoundParticipant']['placement_round'],
                    'PlacementRoundParticipant.applied_for' => $this->request->data['PlacementRoundParticipant']['applied_for'],
                    'PlacementRoundParticipant.program_id' => $this->request->data['PlacementRoundParticipant']['program_id'],
                    'PlacementRoundParticipant.program_type_id' => $this->request->data['PlacementRoundParticipant']['program_type_id'],
                ),
                'recursive' => -1
            );
            $placementRoundParticipants = $this->PlacementRoundParticipant->find('all', $options);
            $this->set(compact('placementRoundParticipants'));
        }


        $types = array('College' => 'College', 'Department' => 'Department', 'Specialization' => 'Specialization');
        $fieldSetups = 'type,foreign_key,name,edit';

        $availableAcademicYears = ClassRegistry::init('PlacementRoundParticipant')->find('list', array(
            'fields' => array('PlacementRoundParticipant.academic_year', 'PlacementRoundParticipant.academic_year'),
            'group' => array('PlacementRoundParticipant.academic_year'),
            'order' => array('PlacementRoundParticipant.academic_year DESC')
        ));

        if (empty($availableAcademicYears)) {
            $currACY = $this->AcademicYear->current_academicyear();
            $availableAcademicYears[$currACY] = $currACY;
        }

        $availablePrograms = ClassRegistry::init('PlacementRoundParticipant')->find('list', array(
            'fields' => array('PlacementRoundParticipant.program_id', 'PlacementRoundParticipant.program_id'),
            'group' => array('PlacementRoundParticipant.program_id')
        ));

        $availableProgramTypes = ClassRegistry::init('PlacementRoundParticipant')->find('list', array(
            'fields' => array('PlacementRoundParticipant.program_type_id', 'PlacementRoundParticipant.program_type_id'),
            'group' => array('PlacementRoundParticipant.program_type_id')
        ));

        if (!empty($availablePrograms)) {
            $programs = ClassRegistry::init('Program')->find(
                'list',
                array('conditions' => array('Program.id' => $availablePrograms))
            );
        } else {
            $programs_available_for_placement_preference = Configure::read(
                'programs_available_for_placement_preference'
            );
            $programs = ClassRegistry::init('Program')->find(
                'list',
                array('conditions' => array('Program.id' => $programs_available_for_placement_preference))
            );
        }

        if (!empty($availableProgramTypes)) {
            $programTypes = ClassRegistry::init('ProgramType')->find(
                'list',
                array('conditions' => array('ProgramType.id' => $availableProgramTypes))
            );
        } else {
            $program_types_available_for_placement_preference = Configure::read(
                'program_types_available_for_placement_preference'
            );
            $programTypes = ClassRegistry::init('ProgramType')->find(
                'list',
                array('conditions' => array('ProgramType.id' => $program_types_available_for_placement_preference))
            );
        }

        //$allUnits = ClassRegistry::init('Department')->find('list', array('conditions' => array('Department.active' => 1)));

        $currentUnits = $allUnits = array();

        $colleges = ClassRegistry::init('College')->find('list', array('conditions' => array('College.active' => 1)));
        $departments = ClassRegistry::init('Department')->find(
            'list',
            array('conditions' => array('Department.active' => 1))
        );

        if ($this->role_id == ROLE_COLLEGE) {
            $allUnits = ClassRegistry::init('Department')->allUnits(null, null, 1);
            $currentUnits = ClassRegistry::init('Department')->allUnits($this->role_id, $this->college_id);
        } elseif ($this->role_id == ROLE_DEPARTMENT) {
            $allUnits = ClassRegistry::init('Department')->allUnits(null, null, 1);
            $currentUnits = ClassRegistry::init('Department')->allUnits($this->role_id, $this->department_id);
        } else {
            if ($this->role_id == ROLE_REGISTRAR || $this->role_id == ROLE_SYSADMIN) {
                $allUnits = ClassRegistry::init('Department')->allUnits($this->role_id, null);
                $currentUnits = $allUnits;
            }
        }

        //debug($this->request->data['PlacementRoundParticipant']);

        if (isset($this->request->data['PlacementRoundParticipant']) && !empty($this->request->data['PlacementRoundParticipant']['applied_for'])) {
            $appliedForList = ClassRegistry::init('PlacementPreference')->get_defined_list_of_applied_for(
                $this->request->data['PlacementRoundParticipant']
            );
            $latestACYRoundAppliedFor = ClassRegistry::init(
                'PlacementRoundParticipant'
            )->latest_defined_academic_year_and_round($this->request->data['PlacementRoundParticipant']['applied_for']);
        } else {
            $latestACYRoundAppliedFor = ClassRegistry::init(
                'PlacementRoundParticipant'
            )->latest_defined_academic_year_and_round();
            if (!empty($availableAcademicYears)) {
                $appliedForList = ClassRegistry::init('PlacementPreference')->get_defined_list_of_applied_for(
                    null,
                    $availableAcademicYears
                );
            } else {
                $appliedForList = ClassRegistry::init('PlacementPreference')->get_defined_list_of_applied_for(
                    null,
                    $latestACYRoundAppliedFor['academic_year']
                );
            }
        }
        //debug($appliedForList);

        //debug($latestACYRoundAppliedFor);

        $this->set(
            compact(
                'colleges',
                'types',
                'allUnits',
                'departments',
                'colleges',
                'fieldSetups',
                'programs',
                'programTypes',
                'appliedForList',
                'latestACYRoundAppliedFor'
            )
        );
    }

    public function view($id = null)
    {

        if (!$this->PlacementRoundParticipant->exists($id)) {
            throw new NotFoundException(__('Invalid placement round participant'));
        }
        $options = array('conditions' => array('PlacementRoundParticipant.' . $this->PlacementRoundParticipant->primaryKey => $id));
        $this->set('placementRoundParticipant', $this->PlacementRoundParticipant->find('first', $options));
    }

    public function add()
    {

        if ($this->request->is('post')) {
            if (isset($this->request->data['PlacementRoundParticipant']) && !empty($this->request->data['PlacementRoundParticipant'])) {
                $reformated = $this->PlacementRoundParticipant->reformat($this->request->data);
                // check duplication
                $checkDuplication = $this->PlacementRoundParticipant->isDuplicated($this->request->data);
                $placementRoundChecker = classRegistry::init(
                    'PlacementParticipatingStudent'
                )->isCurrentPlacementRoundDefined($this->request->data);

                if ($placementRoundChecker == 0 && $reformated != false && $checkDuplication == false) {
                    $groupIdentifier = $reformated['PlacementRoundParticipant'][1]['group_identifier'];

                    $this->PlacementRoundParticipant->create();

                    if ($this->PlacementRoundParticipant->saveAll(
                        $reformated['PlacementRoundParticipant'],
                        array('validate' => 'first')
                    )) {
                        $this->Flash->success(
                            'The placement round participant has been saved, and please fill their quota capacity for each unit.'
                        );
                        //redirect to quota management where we can fill the quota for the involved units
                        //groupIdentifier
                        return $this->redirect(
                            array('controller' => 'placement_settings', 'action' => 'quota', $groupIdentifier)
                        );
                    } else {
                        $this->Flash->error('The placement round participant could not be saved. Please, try again.');
                    }
                } else {
                    $error = $this->PlacementRoundParticipant->invalidFields();
                    $rlabel = classRegistry::init('PlacementRoundParticipant')->roundLabel($placementRoundChecker);

                    if (isset($error['foreign_key'])) {
                        $this->Flash->error($error['foreign_key'][0]);
                    } else {
                        if (isset($checkDuplication)) {
                            $this->Flash->error(
                                'Unable to create placement round participants since they have been created earlier.'
                            );
                            return $this->redirect(
                                array('controller' => 'placement_settings', 'action' => 'quota', $checkDuplication)
                            );
                        } else {
                            if ($placementRoundChecker) {
                                $this->Flash->error(
                                    'Unable to create  ' . $rlabel . ' round placement since the placement round has took place, and students were assigned. Please change the round, and try again.'
                                );
                            }
                        }
                    }
                }
            }
        }

        $colleges = classRegistry::init('College')->find('list', array('conditions' => array('College.active' => 1)));
        $departments = classRegistry::init('Department')->find(
            'list',
            array(
                'conditions' => array('Department.active' => 1),
                'order' => array('Department.college_id' => 'ASC', 'Department.name' => 'ASC')
            )
        );
        $types = array('College' => 'College', 'Department' => 'Department', 'Specialization' => 'Specialization');
        // $programs = classRegistry::init('Program')->find('list');
        // $programTypes = classRegistry::init('ProgramType')->find('list');

        $programs_available_for_placement_preference = Configure::read('programs_available_for_placement_preference');
        $program_types_available_for_placement_preference = Configure::read(
            'program_types_available_for_placement_preference'
        );

        $programs = classRegistry::init('Program')->find(
            'list',
            array('conditions' => array('Program.id' => $programs_available_for_placement_preference))
        );
        $programTypes = classRegistry::init('ProgramType')->find(
            'list',
            array('conditions' => array('ProgramType.id' => $program_types_available_for_placement_preference))
        );

        $fieldSetups = 'type,foreign_key,name,edit';

        if ($this->role_id == ROLE_COLLEGE) {
            $allUnits = classRegistry::init('Department')->allUnits(null, null, 1);
            $currentUnits = classRegistry::init('Department')->allUnits($this->role_id, $this->college_id);
        } elseif ($this->role_id == ROLE_DEPARTMENT) {
            $allUnits = classRegistry::init('Department')->allUnits(null, null, 1);
            $currentUnits = classRegistry::init('Department')->allUnits($this->role_id, $this->department_id);
        } else {
            if ($this->role_id == ROLE_REGISTRAR) {
                $allUnits = classRegistry::init('Department')->allUnits($this->role_id, null);
                $currentUnits = $allUnits;
            }
        }
        //$allUnits = classRegistry::init('Department')->allUnits();
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

    public function edit($group_identifier = null)
    {

        if ($this->request->is('post')) {
            if (isset($this->request->data['PlacementRoundParticipant']) && !empty($this->request->data['PlacementRoundParticipant']) && isset($this->request->data['saveIt'])) {
                debug($this->request->data);

                $reformated = $this->PlacementRoundParticipant->reformat($this->request->data);
                // check duplication

                $checkDuplication = $this->PlacementRoundParticipant->isDuplicated($this->request->data, $edit = 1);
                $placementRoundChecker = classRegistry::init(
                    'PlacementParticipatingStudent'
                )->isCurrentPlacementRoundDefined($this->request->data);

                debug($checkDuplication);

                if ($placementRoundChecker == 0 && $reformated != false && $checkDuplication == false) {
                    $groupIdentifier = $reformated['PlacementRoundParticipant'][1]['group_identifier'];

                    debug($reformated);
                    //$this->PlacementRoundParticipant->create();

                    if ($this->PlacementRoundParticipant->saveAll(
                        $reformated['PlacementRoundParticipant'],
                        array('validate' => 'first')
                    )) {
                        $this->Flash->success(
                            'The placement round participant has been saved, and please fill their quota capacity for each unit.'
                        );
                        //redirect to quota management where we can fill the quota for the involved units
                        //groupIdentifier
                        return $this->redirect(
                            array('controller' => 'placement_settings', 'action' => 'quota', $groupIdentifier)
                        );
                    } else {
                        $this->Flash->error('The placement round participant could not be saved. Please, try again.');
                    }
                } else {
                    $error = $this->PlacementRoundParticipant->invalidFields();
                    $rlabel = classRegistry::init('PlacementRoundParticipant')->roundLabel($placementRoundChecker);

                    if (isset($error['foreign_key'])) {
                        $this->Flash->error($error['foreign_key'][0]);
                    } else {
                        if (isset($checkDuplication)) {
                            $this->Flash->error(
                                'Unable to create placement round participants since they have been created earlier.'
                            );
                            return $this->redirect(
                                array('controller' => 'placement_settings', 'action' => 'quota', $checkDuplication)
                            );
                        } else {
                            if ($placementRoundChecker) {
                                $this->Flash->error(
                                    'Unable to create  ' . $rlabel . ' round placement since the placement round has took place, and students were assigned. Please change the round, and try again.'
                                );
                            }
                        }
                    }
                }
            }
        }

        if (isset($group_identifier) && !empty($group_identifier)) {
            $options = array(
                'conditions' => array('PlacementRoundParticipant.group_identifier' => $group_identifier),
                'recursive' => -1
            );

            $placementRoundParticipants = $this->request->data = $this->PlacementRoundParticipant->find(
                'all',
                $options
            );
            //debug($placementRoundParticipants);


            if (!empty($placementRoundParticipants)) {
                $applied_for = $placementRoundParticipants[0]['PlacementRoundParticipant']['applied_for'];
                $academic_year = $placementRoundParticipants[0]['PlacementRoundParticipant']['academic_year'];
                $placementRound = $placementRoundParticipants[0]['PlacementRoundParticipant']['placement_round'];

                debug($applied_for);

                $participantIDs = $this->PlacementRoundParticipant->get_placement_participant_ids_by_group_identifier(
                    $group_identifier
                );
                debug($participantIDs);

                $isThereAnyPreferenceFilledByStudents = ClassRegistry::init('PlacementPreference')->find('count', array(
                    'conditions' => array(
                        'PlacementPreference.round' => $placementRound,
                        'PlacementPreference.academic_year LIKE ' => $academic_year . '%',
                        'PlacementPreference.placement_round_participant_id' => $participantIDs
                    )
                ));
                debug($isThereAnyPreferenceFilledByStudents);

                if ($isThereAnyPreferenceFilledByStudents) {
                    $this->Flash->error(
                        'There are placement preferences recorded by students using these placement round participants, you can not edit placement round participants at this time.'
                    );
                    //$this->redirect(array('action' => 'index'));
                }

                $data = array();
                $i = 1;

                if (!empty($this->request->data)) {
                    foreach ($this->request->data as $k => $prp) {
                        //debug($prp);
                        if (/* !is_null($dev['PlacementPreference']['placement_round_participant_id']) && ($prp['PlacementPreference']['preference_order']) == $i */ 1) {
                            $data['PlacementRoundParticipant'][$i] = $prp['PlacementRoundParticipant'];
                            $i++;
                        }
                    }
                }

                debug($data);

                $this->request->data = $data;

                $this->set(compact('isThereAnyPreferenceFilledByStudents'));
            }


            $colleges = classRegistry::init('College')->find('list', array('conditions' => array('College.active' => 1))
            );
            $departments = classRegistry::init('Department')->find(
                'list',
                array(
                    'conditions' => array('Department.active' => 1),
                    'order' => array('Department.college_id' => 'ASC', 'Department.name' => 'ASC')
                )
            );
            $types = array('College' => 'College', 'Department' => 'Department', 'Specialization' => 'Specialization');
            // $programs = classRegistry::init('Program')->find('list');
            // $programTypes = classRegistry::init('ProgramType')->find('list');

            $programs_available_for_placement_preference = Configure::read(
                'programs_available_for_placement_preference'
            );
            $program_types_available_for_placement_preference = Configure::read(
                'program_types_available_for_placement_preference'
            );

            $programs = classRegistry::init('Program')->find(
                'list',
                array('conditions' => array('Program.id' => $programs_available_for_placement_preference))
            );
            $programTypes = classRegistry::init('ProgramType')->find(
                'list',
                array('conditions' => array('ProgramType.id' => $program_types_available_for_placement_preference))
            );

            $fieldSetups = 'type,foreign_key,name,edit';

            if ($this->role_id == ROLE_COLLEGE) {
                $allUnits = classRegistry::init('Department')->allUnits(null, null, 1);
                $currentUnits = classRegistry::init('Department')->allUnits($this->role_id, $this->college_id);
            } elseif ($this->role_id == ROLE_DEPARTMENT) {
                $allUnits = classRegistry::init('Department')->allUnits(null, null, 1);
                $currentUnits = classRegistry::init('Department')->allUnits($this->role_id, $this->department_id);
            } else {
                if ($this->role_id == ROLE_REGISTRAR) {
                    $allUnits = classRegistry::init('Department')->allUnits($this->role_id, null);
                    $currentUnits = $allUnits;
                }
            }
            //$allUnits = classRegistry::init('Department')->allUnits();

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
    }

    /* public function edit($id = null)
    {
        if (!$this->PlacementRoundParticipant->exists($id)) {
            throw new NotFoundException(__('Invalid placement round participant'));
        }

        if ($this->request->is(array('post', 'put'))) {
            if ($this->PlacementRoundParticipant->save($this->request->data)) {
                $this->Flash->success(__('The placement round participant has been saved.'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Flash->error(__('The placement round participant could not be saved. Please, try again.'));
            }
        } else {
            $options = array('conditions' => array('PlacementRoundParticipant.' . $this->PlacementRoundParticipant->primaryKey => $id));
            $this->request->data = $this->PlacementRoundParticipant->find('first', $options);
        }
    } */

    public function delete($id = null)
    {

        $this->PlacementRoundParticipant->id = $id;
        if (!$this->PlacementRoundParticipant->exists()) {
            throw new NotFoundException(__('Invalid placement round participant'));
        }
        $this->request->allowMethod('post', 'delete');

        // check is needed if PlacementRoundParticipant id is used  in `placement_entrance_exam_result_entries`, `placement_participating_students` and `placement_preferences` tables before delete

        if ($this->PlacementRoundParticipant->canItBeDeleted($id)) {
            if ($this->PlacementRoundParticipant->delete()) {
                $this->Flash->success('The placement round participant has been deleted.');
            } else {
                $this->Flash->error('The placement round participant could not be deleted. Please, try again.');
            }
        }
        return $this->redirect(array('action' => 'index'));
    }

    public function get_participant_unit($type = "", $appliedFor = "", $appliedForValue = "")
    {

        $this->layout = 'ajax';
        $units = array();

        if ($type == "College") {
            $units = classRegistry::init('College')->find('list', array('conditions' => array('College.active' => 1)));
        } else {
            if ($type == "Department") {
                $units = classRegistry::init('Department')->find(
                    'list',
                    array('conditions' => array('Department.active' => 1))
                );
            } else {
                if ($type == "Specialization") {
                    $units = classRegistry::init('Specialization')->find('list');
                } else {
                    if ($appliedFor == "d") {
                        $units = classRegistry::init('Specialization')->find(
                            'list',
                            array('conditions' => array('Specialization.department_id' => $appliedForValue))
                        );
                    } else {
                        if ($appliedFor == "c") {
                            $units = classRegistry::init('Department')->find(
                                'list',
                                array(
                                    'conditions' => array(
                                        'Department.college_id' => $appliedForValue,
                                        'Department.active' => 1
                                    )
                                )
                            );
                            debug($units);
                        }
                    }
                }
            }
        }
        $this->set(compact('units'));
    }

    public function get_selected_participant_unit($model = null)
    {

        $this->layout = 'ajax';
        if (isset($model) && !empty($model)) {
            $units = $this->PlacementRoundParticipant->get_participating_unit_name($this->request->data["$model"]);
        } else {
            $units = $this->PlacementRoundParticipant->get_selected_participating_unit_name($this->request->data);
        }
        $this->set(compact('units'));
	}
}
