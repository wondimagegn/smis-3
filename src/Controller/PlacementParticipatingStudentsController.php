<?php

namespace App\Controller;

use Cake\Event\Event;

class PlacementParticipatingStudentsController extends AppController
{

    public $name = 'PlacementParticipatingStudents';
    public $menuOptions = array(
        'parent' => 'placement',
        'alias' => array(//'index' => 'List Placement Participant Student',
        ),
        'exclude' => array(
            'delete_ajax',
            'index'
        ),
    );

    public $paginate = [];

    public function initialize()
    {

        parent::initialize();
        $this->loadComponent('AcademicYear');
        $this->loadComponent('EthiopicDateTime');
        $this->loadComponent('Paginator'); // Ensure Paginator is loaded

    }

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
    }

    public function beforeRender(Event $event)
    {

        parent::beforeRender($event);
        ////////////////////////////// BLOCK: DONT REMOVE ANY VARIABLE /////////////////////////////////////

        $defaultacademicyear = $this->AcademicYear->currentAcademicYear();

        if (is_numeric(ACY_BACK_FOR_PLACEMENT) && ACY_BACK_FOR_PLACEMENT) {
            $acyear_array_data = $this->AcademicYear->academicYearInArray(
                ((explode('/', $defaultacademicyear)[0]) - ACY_BACK_FOR_PLACEMENT),
                (explode('/', $defaultacademicyear)[0])
            );
        } else {
            $acyear_array_data[$defaultacademicyear] = $defaultacademicyear;
        }

        $this->set(compact('acyear_array_data', 'defaultacademicyear'));
        //////////////////////////////////// END BLOCK ///////////////////////////////////////////////////
    }


    public function delete_ajax($id = null)
    {

        $this->autoRender = false;
        $this->layout = 'ajax';

        //check if placement is already run
        $placementParticpatingStu = $this->PlacementParticipatingStudent->find(
            'first',
            array('conditions' => array('PlacementParticipatingStudent.id' => $id), 'recursive' => -1)
        );

        $isPlaced = $this->PlacementParticipatingStudent->find('count', array(
            'conditions' => array(
                'PlacementParticipatingStudent.program_id' => $placementParticpatingStu['PlacementParticipatingStudent']['program_id'],
                'PlacementParticipatingStudent.program_type_id' => $placementParticpatingStu['PlacementParticipatingStudent']['program_type_id'],
                'PlacementParticipatingStudent.applied_for' => $placementParticpatingStu['PlacementParticipatingStudent']['applied_for'],
                'PlacementParticipatingStudent.round' => $placementParticpatingStu['PlacementParticipatingStudent']['round'],
                'PlacementParticipatingStudent.academic_year' => $placementParticpatingStu['PlacementParticipatingStudent']['academic_year'],
                'PlacementParticipatingStudent.placement_round_participant_id is not null'
            ),
            'recursive' => -1
        ));

        if ($isPlaced == 0) {
            $this->PlacementParticipatingStudent->id = $id;
            $this->request->allowMethod('post', 'delete');
            if ($this->PlacementParticipatingStudent->delete()) {
            }
        }
    }
}
