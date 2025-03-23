<?php

namespace App\Controller;

use Cake\Event\Event;

class ReservedPlacesController extends AppController
{

    public $name = 'ReservedPlaces';
    public $menuOptions = array(
        'parent' => 'placement',
        'exclude' => array('get_summeries', 'index'),
        'alias' => array(
            //'index'=>'List Reserved Place',
            'add' => 'Add/Edit Reserved Place For Department'
        )
    );

    public $paginate = [];

    public function initialize()
    {

        parent::initialize();
        $this->loadComponent('AcademicYear');
        $this->loadComponent('Paginator'); // Ensure Paginator is loaded

    }

    public function beforeFilter(Event $event)
    {

        parent::beforeFilter($event);
        $this->Auth->allow('get_summeries');
    }


    public function beforeRender(Event $event)
    {

        parent::beforeRender($event);
        $acyear_array_data = $this->AcademicYear->acyearArray();
        //To diplay current academic year as default in drop down list
        $selected = $this->AcademicYear->currentAcademicYear();
        foreach ($acyear_array_data as $k => $v) {
            if ($v == $selected) {
                $selected = $k;
                break;
            }
        }
        $this->set(compact('acyear_array_data', 'selected'));
    }

    public function index()
    {

        /*$this->ReservedPlace->recursive = 0;

        $departments = $this->_department_reformat($this->college_id);
        $this->set(compact('departments'));
        if (!empty($this->request->data)) {

            $ssdepartment = isset($this->request->data['ReservedPlace']['department_id'])?
            $this->request->data['ReservedPlace']['department_id']:null;
            $ssacdemicyear = isset($this->request->data['ReservedPlace']['academicyear'])?$this->request->data['ReservedPlace']['academicyear']:$this->AcademicYear->current_academicyear.'%';
            if($ssdepartment){
                $conditions = array(
                    "ReservedPlace.academicyear LIKE" => "$ssacdemicyear%",
                    "ReservedPlace.participating_department_id LIKE" => "$ssdepartment%"
                );
                $reservedPlaces=$this->_participating_department_name($this->college_id,$conditions);
                if(!empty($reservedPlaces)){
                    $this->set('reservedPlaces',
                    $this->_participating_department_name($this->college_id,$conditions));
                } else {
                   $this->Session->setFlash(__('<span></span>There is no reserved place within the search criteria you provided.'),'default',array('class'=>'error-box error-message'));
                }
            } else {
                $conditions = array(
                    "ReservedPlace.academicyear LIKE" => "$ssacdemicyear%"
                );
                $this->set('reservedPlaces',
                $this->_participating_department_name($this->college_id,$conditions));
            }

        }
        */
        return $this->redirect(array('action' => 'add'));
    }

    public function view($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(
                __('Invalid reserved place'),
                'default',
                array('class' => 'error-box error-message')
            );
            return $this->redirect(array('action' => 'index'));
        }
        $this->set('reservedPlace', $this->ReservedPlace->read(null, $id));
    }

    public function _resultsCategory($college_id = null, $academicyear = null)
    {

        $placementsResultsCriterias = $this->ReservedPlace->PlacementsResultsCriteria->find(
            'all',
            array(
                'conditions' => array(
                    "PlacementsResultsCriteria.admissionyear LIKE" => $academicyear . '%',
                    'PlacementsResultsCriteria.college_id' => $college_id
                ),
                'order' => array('PlacementsResultsCriteria.result_to desc')
            )
        );

        $placementcriteria = array();
        foreach ($placementsResultsCriterias as $key => $value) {
            $placementcriteria[$value['PlacementsResultsCriteria']['id']] = $value['PlacementsResultsCriteria']['name'] . '(' .
                $value['PlacementsResultsCriteria']['result_from'] . '-' . $value['PlacementsResultsCriteria']['result_to'] . ')';
        }
        return $placementcriteria;
    }

    /* it was an add to add reserved place for each result category.
	function add() {
	   if(empty($this->request->data)){
	        //debug($this->request->data);
	        //check the academic year is coming from form
	        $selectedAcademicYear=null;
	        if(isset($this->request->data['ReservedPlace']['academicyear'])&&
	        !empty($this->request->data['ReservedPlace']['academicyear'])){
	                 $selectedAcademicYear = $this->request->data['AcceptedStudent']['academicyear'];
	        } else {
	               $selectedAcademicYear= $this->AcademicYear->current_academicyear();
	        }
	        $checkResultCategoryIsRecorded=$this->ReservedPlace->
	        PlacementsResultsCriteria->find('count',array('conditions'=>array("PlacementsResultsCriteria.admissionyear LIKE" =>
	        $selectedAcademicYear.'%',
	        'PlacementsResultsCriteria.college_id'=>$this->college_id)));

	        if(!$checkResultCategoryIsRecorded){
	            $this->Session->setFlash(__('Please add result criteria for the given academic year.'));
	            $this->redirect(array('controller'=>'placementsResultsCriterias','action'=>'add'));

	        }
	        $checkParticipatingDepartment = $this->ReservedPlace->ParticipatingDepartment->find('count',
		    array('conditions'=>array('ParticipatingDepartment.college_id'=>$this->college_id,
		    'ParticipatingDepartment.academic_year LIKE '=>$selectedAcademicYear.'%')));
		    if(!$checkParticipatingDepartment){
	            $this->Session->setFlash(__('Please add placement participating department for
	            given academic year.', true),'ajax');
	            $this->redirect(array('controller'=>'participatingDepartments','action'=>'add'));

	        }
        }
		$placementsResultsCriterias=$this->_resultsCategory($this->college_id);

		if (!empty($this->request->data)) {
			    $this->ReservedPlace->create();

			    $description = $this->request->data['ReservedPlace']['description'];
			    $acadamicyear =$this->request->data['ReservedPlace']['academicyear'];
			    $placement_result_category=$this->request->data['ReservedPlace']['placements_results_criteria_id'];

			    unset($this->request->data['ReservedPlace']['description']);
			    unset($this->request->data['ReservedPlace']['academicyear']);
			    unset($this->request->data['ReservedPlace']['placements_results_criteria_id']);
			    foreach( $this->request->data['ReservedPlace']  as &$value) {

					    $value['placements_results_criteria_id']=$placement_result_category;
					    $value['description']=$description;
					    $value['academicyear']=$acadamicyear;
					    $value['college_id']=$this->college_id;

			    }
			  $this->set($this->request->data);

			  $this->set('defaultPlacementResultsCriterias',$placement_result_category);
			  $this->set('PlacementResultsCriteriasName',$placementsResultsCriterias[$placement_result_category]);
			  $this->set('total_students_in_given_category',$this->ReservedPlace->find_total_number_accepted_student_in_given_category
			  ($placement_result_category,$this->college_id,$acadamicyear));
			  $this->set('total_students_college_academicyear',$this->ReservedPlace->total_accepted_students_unsigned_to_department($this->college_id,$this->AcademicYear->current_academicyear()));
			  if($this->ReservedPlace->validates()){
			    //check the available  number of students for the given category
			    //debug($this->request->data['ReservedPlace']);
			    if($this->ReservedPlace->checkCategoryReservedPlaceIsWithinRanage($this->request->data['ReservedPlace'])){
			        //check if reserved place is already recorded for the college


			     if($this->ReservedPlace->checkGivenCategoryReserved($placement_result_category,
			     $this->college_id,$acadamicyear)){
			        if ($this->ReservedPlace->saveAll($this->request->data['ReservedPlace'],
			    array('validate'=>'first'))) {
				        $this->Session->setFlash(__('The reserved place has been saved'));
				        //$this->redirect(array('action' => 'index'));
			        } else {
				        $this->Session->setFlash(__('The reserved place could not be saved. Please, try again.'));
			        }
			       } else {
			          $this->Session->setFlash(__('The reserved place has been already recorded for the academic year, please edit if you want to modify resereved place.'));
			          $this->redirect(array('action' => 'index'));
			       }
			      } else {

			         $this->Session->setFlash(__('The sum of number of reserved place for each department should be equal to the total number of students in the selected category. Please, try again.'));

			      }

			    }

		    }

		 //check the academic year is coming from form
	     $selectedAcademicYear=null;
	     if(isset($this->request->data['ReservedPlace']['academicyear'])&&
	        !empty($this->request->data['ReservedPlace']['academicyear'])){
	                 $selectedAcademicYear = $this->request->data['AcceptedStudent']['academicyear'];
	     } else {
	               $selectedAcademicYear= $this->AcademicYear->current_academicyear();
	     }
		$departments = $this->ReservedPlace->ParticipatingDepartment->find('all',
		array('conditions'=>array('ParticipatingDepartment.college_id'=>$this->college_id,
		'ParticipatingDepartment.academic_year LIKE '=>$selectedAcademicYear.'%')));
		$colleges = $this->ReservedPlace->College->find('list');

		$this->set(compact('departments', 'colleges', 'placementsResultsCriterias'));
	}
	*/
    public function add($academicyear_set = null)
    {

        if (!empty($academicyear_set)) {
            $this->request->data['ReservedPlace']['academicyear'] = str_replace('-', '/', $academicyear_set);
        }
        if (!empty($this->request->data)) {
            debug($this->request->data);
            if (!empty($this->request->data['ReservedPlace']['academicyear'])) {
                // is auto placement run
                $check_auto_placement_already_run_not_allow_adding_or_edit = ClassRegistry::init(
                    'AcceptedStudent'
                )->find(
                    'count',
                    array(
                        'conditions' => array(
                            'AcceptedStudent.academicyear' => $this->request->data['ReservedPlace']['academicyear'],
                            'AcceptedStudent.college_id' => $this->college_id,
                            'AcceptedStudent.placementtype' =>
                                AUTO_PLACEMENT,

                            'AcceptedStudent.program_type_id' => PROGRAM_TYPE_REGULAR,
                            'AcceptedStudent.program_id' => PROGRAM_UNDEGRADUATE
                        )
                    )
                );
                $this->set(
                    compact(
                        'check_auto_placement_already_run_not_allow_adding_or_edit',
                        'selectedAcademicYear'
                    )
                );
                $selectedAcademicYear = $this->request->data['ReservedPlace']['academicyear'];
                //check auto has already run
                $this->loadModel('PlacementLock');
                $auto_run = $this->PlacementLock->find('count', array(
                    'conditions' => array(
                        'PlacementLock.college_id' => $this->college_id,
                        'PlacementLock.academic_year' => $selectedAcademicYear,
                        'PlacementLock.process_start' => 1
                    )
                ));
                if ($auto_run) {
                    $this->Session->setFlash(
                        __(
                            'The auto placement is up and running you can not modify the reserved places for department right now, please come back after you cancelled the auto placement.'
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    $this->redirect(array('action' => 'add'));
                }
                $result_type = null;
                $is_preparatory = $this->ReservedPlace->
                PlacementsResultsCriteria->find('count', array(
                    'conditions' => array(
                        'PlacementsResultsCriteria.admissionyear' =>
                            $selectedAcademicYear,
                        'PlacementsResultsCriteria.college_id' => $this->college_id,
                        'PlacementsResultsCriteria.prepartory_result' => 1
                    )
                ));
                if ($is_preparatory > 0) {
                    $result_type = 'EHEECE_total_results';
                } else {
                    $result_type = 'freshman_result';
                }
                $checkResultCategoryIsRecorded = $this->ReservedPlace->
                PlacementsResultsCriteria->find(
                    'count',
                    array(
                        'conditions' => array(
                            "PlacementsResultsCriteria.admissionyear LIKE" =>
                                $selectedAcademicYear . '%',
                            'PlacementsResultsCriteria.college_id' => $this->college_id
                        )
                    )
                );

                if (!$checkResultCategoryIsRecorded) {
                    $this->Session->setFlash(
                        __(
                            '<span></span>Please first add result criteria for the selected ' . $selectedAcademicYear . ' academic year and then you can reserve places for each department.'
                        ),
                        'default',
                        array('class' => 'info-box info-message')
                    );
                    $this->redirect(array('controller' => 'placementsResultsCriterias', 'action' => 'add'));
                }

                //check grade ranage recorded  against students grade range availabilty.
                $listPlacementResultCriteria = $this->ReservedPlace->PlacementsResultsCriteria->find(
                    'all',
                    array(
                        'recursive' => -1,
                        'conditions' => array(
                            "PlacementsResultsCriteria.admissionyear LIKE" =>
                                $selectedAcademicYear . '%',
                            'PlacementsResultsCriteria.college_id' => $this->college_id
                        )
                    )
                );
                //debug($listPlacementResultCriteria);
                $numberOfAcceptedStudents = 0;
                $leftOverStudents = array();
                $tmpList = array();
                foreach ($listPlacementResultCriteria as $key => $resultCriteria) {
                    $tmpCnt = ClassRegistry::init('AcceptedStudent')->find(
                        'list',
                        array(
                            'recursive' => -1,
                            'conditions' =>
                                array(
                                    'AcceptedStudent.' . $result_type . ' >=' => $resultCriteria['PlacementsResultsCriteria']['result_from'],
                                    'AcceptedStudent.' . $result_type . ' <=' => $resultCriteria['PlacementsResultsCriteria']['result_to'],
                                    'AcceptedStudent.academicyear LIKE' => $selectedAcademicYear . '%',
                                    'AcceptedStudent.college_id' => $this->college_id,
                                    'OR' => array(
                                        'AcceptedStudent.department_id is NULL',
                                        'AcceptedStudent.department_id ' => array(0, '')
                                    ),
                                    'AcceptedStudent.program_type_id' => PROGRAM_TYPE_REGULAR,
                                    'AcceptedStudent.program_id' => PROGRAM_UNDEGRADUATE
                                ),
                            'fields' => array('AcceptedStudent.id', 'AcceptedStudent.id')
                        )
                    );
                    $numberOfAcceptedStudents += count($tmpCnt);
                    $tmpList = array_merge($tmpList, $tmpCnt);
                }
                $studentsNotIncludedInTheGradeRange = $this->ReservedPlace->total_accepted_students_unsigned_to_department(
                        $this->college_id,
                        $selectedAcademicYear
                    ) - $numberOfAcceptedStudents;
                if ($studentsNotIncludedInTheGradeRange > 0) {
                    debug($tmpList);
                    $leftoverStudent = ClassRegistry::init('AcceptedStudent')->find(
                        'all',
                        array(
                            'recursive' => -1,
                            'conditions' =>
                                array(
                                    'AcceptedStudent.id not ' => $tmpList,
                                    'AcceptedStudent.academicyear LIKE' => $selectedAcademicYear . '%',
                                    'AcceptedStudent.college_id' => $this->college_id,
                                    'AcceptedStudent.program_type_id' => PROGRAM_TYPE_REGULAR,
                                    'AcceptedStudent.program_id' => PROGRAM_UNDEGRADUATE,
                                    'OR' => array(
                                        'AcceptedStudent.department_id is NULL',
                                        'AcceptedStudent.department_id' => array(0, '')
                                    )
                                )
                        )
                    );


                    $list = '';
                    $list .= '<ul>';
                    foreach ($leftoverStudent as $k => $v) {
                        $list .= '<li>' . $v['AcceptedStudent']['full_name'] . '(' . $v['AcceptedStudent']['studentnumber'] . ') reason ' . $result_type . ' is ' . $v['AcceptedStudent'][$result_type] . '</li>';
                    }
                    $list .= '</ul>';

                    $this->Session->setFlash(
                        __(
                            '<span></span> There are ' . $studentsNotIncludedInTheGradeRange . ' students who are not included in the defined grade range. Please first finalize your grade range setting before reserving a place for each result category.' . $list . '
',
                            true
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    $this->redirect(array('controller' => 'placementsResultsCriterias', 'action' => 'add'));
                }


                $checkParticipatingDepartment = $this->ReservedPlace->ParticipatingDepartment->find(
                    'count',
                    array(
                        'conditions' => array(
                            'ParticipatingDepartment.college_id' => $this->college_id,
                            'ParticipatingDepartment.academic_year LIKE ' => $selectedAcademicYear . '%'
                        )
                    )
                );

                //force the user to add participating department
                if (!$checkParticipatingDepartment) {
                    $this->Session->setFlash(
                        __(
                            '<span></span>Please add placement participating department for  given academic year.',
                            true
                        ),
                        'default',
                        array('class' => 'info-box info-message')
                    );
                    $this->redirect(array('controller' => 'participatingDepartments', 'action' => 'add'));
                }
                // for each department
                $department_capacity_summery = $this->ReservedPlace->ParticipatingDepartment->find(
                    'all',
                    array(
                        'conditions' => array(
                            'ParticipatingDepartment.college_id' => $this->college_id,
                            'ParticipatingDepartment.academic_year LIKE ' => $selectedAcademicYear . '%'
                        )
                    )
                );
                if (!empty($department_capacity_summery)) {
                    $dept_capacity_summery = array();
                    foreach ($department_capacity_summery as $dck => $dcv) {
                        $dept_capacity_summery[$dcv['Department']['name']]['Q'] = $dcv['ParticipatingDepartment']['female'] + $dcv['ParticipatingDepartment']['regions'] +
                            $dcv['ParticipatingDepartment']['disability'];
                        $dept_capacity_summery[$dcv['Department']['name']]['R'] = $dcv['ParticipatingDepartment']['number'] - ($dcv['ParticipatingDepartment']['female'] + $dcv['ParticipatingDepartment']['regions'] + $dcv['ParticipatingDepartment']['disability']);
                    }
                    $this->set(compact('dept_capacity_summery'));
                }
                //force the user to add quota

                $quotaParticipatingDepartment = $this->ReservedPlace->ParticipatingDepartment->find(
                    'count',
                    array(
                        'conditions' => array(
                            'ParticipatingDepartment.college_id' => $this->college_id,
                            'ParticipatingDepartment.academic_year LIKE ' => $selectedAcademicYear . '%',
                            'ParticipatingDepartment.number' => 0
                        )
                    )
                );
                //force the user to add quota
                if ($quotaParticipatingDepartment > 0) {
                    $this->Session->setFlash(
                        __(
                            '<span></span> Please add participating
		            department capacity. Otherwise you can not reserve place.',
                            true
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                    $this->redirect(array('controller' => 'participatingDepartments', 'action' => 'add_quota'));
                }


                $placementsResultsCriterias = $this->_resultsCategory(
                    $this->college_id,
                    $selectedAcademicYear
                );
                //debug($placementsResultsCriterias);

                $summeryresultcategorystudent = array();
                foreach ($placementsResultsCriterias as $k => $v) {
                    $summeryresultcategorystudent[$v] = $this->ReservedPlace->find_total_number_accepted_student_in_given_category
                    (
                        $k,
                        $this->college_id,
                        $selectedAcademicYear
                    );
                }
                $this->set(
                    compact('summeryresultcategorystudent', 'selectedAcademicYear', 'placementsResultsCriterias')
                );

                $this->set('selectedAcademicyear', $selectedAcademicYear);
                $this->set(
                    'total_students_college_academicyear',
                    $this->ReservedPlace->total_accepted_students_unsigned_to_department(
                        $this->college_id,
                        $selectedAcademicYear
                    )
                );


                $departments = $this->ReservedPlace->ParticipatingDepartment->find(
                    'all',
                    array(
                        'conditions' => array(
                            'ParticipatingDepartment.college_id' => $this->college_id,
                            'ParticipatingDepartment.academic_year LIKE ' => $selectedAcademicYear . '%'
                        )
                    )
                );
                $placementsResultsCriterias = $this->ReservedPlace->PlacementsResultsCriteria->find(
                    'all',
                    array(
                        'conditions' => array(
                            "PlacementsResultsCriteria.admissionyear LIKE" => $selectedAcademicYear . '%',
                            'PlacementsResultsCriteria.college_id' => $this->college_id
                        ),
                        'order' => array('PlacementsResultsCriteria.result_to desc')
                    )
                );
                $reservedplacess = $this->ReservedPlace->find('all', array(
                    'conditions' => array(
                        'ReservedPlace.college_id' => $this->college_id,
                        'ReservedPlace.academicyear LIKE' => $selectedAcademicYear . '%'
                    )
                ));
                //debug($reservedplacess);
                $this->set(compact('reservedplacess'));

                $preference_count = array();

                foreach ($placementsResultsCriterias as $pk => $pv) {
                    foreach ($departments as $k => $v) {
                        for ($i = 1; $i <= count($departments); $i++) {
                            $preference_count[$v['ParticipatingDepartment']['department_id']][$pv['PlacementsResultsCriteria']['id']]['pref'][$i] =
                                ClassRegistry::init('Preference')->find('count', array(
                                    'conditions' => array(
                                        'Preference.academicyear LIKE' => $selectedAcademicYear . '%',
                                        'Preference.college_id' => $this->college_id,
                                        'Preference.department_id' => $v['ParticipatingDepartment']['department_id'],
                                        'Preference.preferences_order' => $i,
                                        'AcceptedStudent.' . $result_type . ' >=' => $pv['PlacementsResultsCriteria']['result_from'],
                                        'AcceptedStudent.' . $result_type . ' <=' => $pv['PlacementsResultsCriteria']['result_to']
                                    )
                                ));
                            $preference_count[$v['ParticipatingDepartment']['department_id']][$pv['PlacementsResultsCriteria']['id']]['female'][$i] =
                                ClassRegistry::init('Preference')->find('count', array(
                                    'conditions' => array(
                                        'Preference.academicyear LIKE' => $selectedAcademicYear . '%',
                                        'Preference.college_id' => $this->college_id,
                                        'Preference.department_id' => $v['ParticipatingDepartment']['department_id'],
                                        'Preference.preferences_order' => $i,
                                        'AcceptedStudent.' . $result_type . ' >=' => $pv['PlacementsResultsCriteria']['result_from'],
                                        'AcceptedStudent.sex' => 'female',

                                        'AcceptedStudent.program_type_id' => PROGRAM_TYPE_REGULAR,
                                        'AcceptedStudent.program_id' => PROGRAM_UNDEGRADUATE,
                                        'AcceptedStudent.' . $result_type . ' <=' => $pv['PlacementsResultsCriteria']['result_to']
                                    )
                                ));
                        }
                    }
                }

                $this->set(compact('preference_count'));
                if (!empty($departments)) {
                    $this->set(compact('departments'));
                } else {
                    $this->Session->setFlash(
                        __(
                            '<span></span>Before reserving a place for each department
	               , add participating departments.',
                            true
                        ),
                        'default',
                        array('class' => 'info-box info-message')
                    );
                    $this->redirect(array(
                        'controller' => 'participatingDepartments',
                        'action' => 'add'
                    ));
                }


                if (isset($this->request->data['reservedplaces']) &&
                    !empty($this->request->data['reservedplaces'])) {
                    $placementsResultsCriterias = $this->
                    _resultsCategory($this->college_id, $selectedAcademicYear);
                    $this->set($this->request->data);
                    if ($this->ReservedPlace->validates()) {
                        //business logic validation
                        //$this->ReservedPlace->isAlreadyRecorded($this->college_id,
                        //   $selectedAcademicYear) &&
                        if (
                            $this->ReservedPlace->
                            checkCategoryReservedPlaceIsWithinRanage(
                                $this->request->data['ReservedPlace'],
                                $placementsResultsCriterias,
                                $this->college_id
                            )) {
                            $acadamic_year_for_redirect = $this->request->data['ReservedPlace']['academicyear'];
                            unset($this->request->data['ReservedPlace']['academicyear']);
                            if ($this->ReservedPlace->saveAll($this->request->data['ReservedPlace'])) {
                                $this->Session->setFlash(
                                    __(
                                        '<span></span>The reserved place for each result category of participating department  has been saved',
                                        true
                                    ),
                                    'default',
                                    array('class' => 'success-box success-message')
                                );
                                $this->redirect(
                                    array('action' => 'add', str_replace('/', '-', $acadamic_year_for_redirect))
                                );
                            } else {
                                $this->Session->setFlash(
                                    __('<span></span>Please fill the input fields'),
                                    'default',
                                    array('class' => 'error-box error-message')
                                );
                            }
                        } else {
                            $error = $this->ReservedPlace->invalidFields();
                            if (isset($error['duplicate'])) {
                                $this->Session->setFlash(
                                    __('<span></span>' . $error['duplicate'][0]),
                                    'default',
                                    array('class' => 'error-box error-message')
                                );
                            } elseif (isset($error['resultcategory'])) {
                                $this->Session->setFlash(
                                    __('<span></span>' . $error['resultcategory'][0]),
                                    'default',
                                    array('class' => 'error-box error-message')
                                );
                            } else {
                                $this->Session->setFlash(
                                    __('<span></span>Validation Error:Please fill the input fields'),
                                    'default',
                                    array('class' => 'error-box error-message')
                                );
                                $this->redirect(
                                    array('action' => 'add', str_replace('/', '-', $acadamic_year_for_redirect))
                                );
                            }
                        }
                    }
                }
                if (isset($this->request->data['deleteReservedplaces']) &&
                    !empty($this->request->data['deleteReservedplaces'])) {
                    $acadamic_year_for_redirect = $this->request->data['ReservedPlace']['academicyear'];
                    $reservedIds = array();
                    foreach ($this->request->data['ReservedPlace'] as $k => $v) {
                        if (isset($v['id']) && !empty($v['id'])) {
                            $reservedIds[] = $v['id'];
                        }
                    }

                    if (!empty($reservedIds)) {
                        if ($this->ReservedPlace->deleteAll(array('ReservedPlace.id' => $reservedIds), false)) {
                            $this->Session->setFlash(
                                '<span></span>' . __('You have deleted the data successfully.'),
                                'default',
                                array('class' => 'success-box success-message')
                            );
                            $this->redirect(array('action' => 'add', str_replace('/', '-', $acadamic_year_for_redirect))
                            );
                        }
                    }
                }

                $this->set('selected_academic_year', true);
            } else {
                $this->Session->setFlash(
                    __(
                        '<span></span>To set reserved place, you need to select academic year. Please, select academic year.'
                    ),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
        }
    }

    public function edit($id = null)
    {

        if (!$id && empty($this->request->data)) {
            $this->Session->setFlash(
                __('<span></span>Invalid reserved place'),
                'default',
                array('class' => 'error-box error-message')
            );
            return $this->redirect(array('action' => 'index'));
        }

        if (!empty($this->request->data)) {
            $this->set($this->request->data);
            if ($this->ReservedPlace->validates()) {
                if ($this->ReservedPlace->find_student_quota_given_participating_department(
                    $this->request->data['ReservedPlace']['number'],
                    $this->request->data['ReservedPlace']['participating_department_id'],
                    $this->college_id,
                    $this->request->data['ReservedPlace']['academicyear'],
                    $this->request->data['ReservedPlace']['placements_results_criteria_id']
                )) {
                    if ($this->ReservedPlace->save($this->request->data)) {
                        $this->Session->setFlash(__('<span></span>The reserved place has been saved'));
                        $this->redirect(array('action' => 'index'));
                    } else {
                        $this->Session->setFlash(
                            __('<span></span>The reserved place could not be saved. Please, try again.'),
                            'default',
                            array('class' => 'error-box error-message')
                        );
                    }
                } else {
                    $this->Session->setFlash(
                        __(
                            '<span></span>The reserved place of the department should be less than or equal to the sum of participation department reserved place. Please adjust the number, try again.'
                        ),
                        'default',
                        array('class' => 'error-box error-message')
                    );
                }
            } else {
                $this->Session->setFlash(
                    __('<span></span>Validation Error. Please, try again.'),
                    'default',
                    array('class' => 'error-box error-message')
                );
            }
        }
        if (empty($this->request->data)) {
            $this->request->data = $this->ReservedPlace->read(null, $id);
            $departments = $this->ReservedPlace->ParticipatingDepartment->Department->find(
                'list',
                array('conditions' => array('Department.college_id' => $this->college_id))
            );
            $departmentname = $departments[$this->request->data['ReservedPlace']['participating_department_id']];
            $othersreservedquota = $this->ReservedPlace->find(
                'all',
                array(
                    'conditions' => array(
                        'ReservedPlace.college_id' =>
                            $this->request->data['ReservedPlace']['college_id'],
                        'ReservedPlace.academicyear'
                        => $this->request->data['ReservedPlace']['academicyear'],
                        'ReservedPlace.placements_results_criteria_id' => $this->request->data['ReservedPlace']['placements_results_criteria_id']
                    )
                )
            );

            foreach ($othersreservedquota as &$value) {
                foreach ($departments as $k => $v) {
                    if ($value['ReservedPlace']['participating_department_id'] ==
                        $k) {
                        $value['ReservedPlace']['department_name'] = $v;
                    }
                }
            }

            $this->set(compact('departmentname', 'othersreservedquota'));
        }

        $placementsResultsCriterias = $placementsResultsCriterias = $this->_resultsCategory($this->college_id);

        $colleges = $this->ReservedPlace->College->find(
            'list',
            array('conditions' => array('College.id' => $this->college_id))
        );


        $this->set(compact('placementsResultsCriterias'));
    }

    public function delete($id = null)
    {

        if (!$id) {
            $this->Session->setFlash(
                __('<span></span>Invalid id for reserved place'),
                'default',
                array('class' => 'error-box error-message')
            );
            return $this->redirect(array('action' => 'index'));
        }
        if ($this->ReservedPlace->delete($id)) {
            $this->Session->setFlash(
                __('<span></span>Reserved place deleted'),
                'default',
                array('class' => 'success-box success-message')
            );
            return $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(
            __('<span></span>Reserved place was not deleted'),
            'default',
            array('class' => 'error-box error-message')
        );
        return $this->redirect(array('action' => 'index'));
    }

    public function _preference_summery($result_criteria_id = null)
    {

        //find placement restul criteria category given acadamic year
        $this->ReservedPlace->PlacementsResultsCriteria->recursive = -1;
        $result_criteria_data = $this->ReservedPlace->PlacementsResultsCriteria->find(
            'first',
            array('conditions' => array('id' => $result_criteria_id))
        );

        /**
         * Find the total number of  accepted students for the given
         * result category with given  college and academic year
         */
        if (!empty($result_criteria_data['PlacementsResultsCriteria'])) {
            $result_category_count = array();
            if ($result_criteria_data['PlacementsResultsCriteria']['prepartory_result']) {
                $result_category_count[$result_criteria_data['PlacementsResultsCriteria']['name']] = $this->ReservedPlace->College->AcceptedStudent->find(
                    'count',
                    array(
                        'conditions' => array(
                            "AcceptedStudent.academicyear LIKE" => $this->AcademicYear->current_academicyear() . '%',
                            "AcceptedStudent.college_id" => $this->college_id,
                            "AcceptedStudent.EHEECE_total_results >="
                            => $result_criteria_data['PlacementsResultsCriteria']['result_from'],
                            "AcceptedStudent.EHEECE_total_results <=" => $result_criteria_data['PlacementsResultsCriteria']['result_to']
                        )
                    )
                );
            } else {
                $result_category_count[$result_criteria_data['PlacementsResultsCriteria']['name']] = $this->
                ReservedPlace->College->AcceptedStudent->find(
                    'count',
                    array(
                        'conditions' => array(
                            "AcceptedStudent.academicyear LIKE" => $this->AcademicYear->current_academicyear() . '%',
                            "AcceptedStudent.college_id" => $this->college_id,
                            "AcceptedStudent.freshman_result >="
                            => $result_criteria_data['PlacementsResultsCriteria']['result_from'],
                            "AcceptedStudent.freshman_result <=" => $result_criteria_data['PlacementsResultsCriteria']['result_to']
                        )
                    )
                );
            }
            return $result_category_count;
        }
        return null;
    }

    /*
	function get_summeries($result_criteria_id=null) {
	          $this->layout='ajax';

	          $result_category_count=$this->_preference_summery($result_criteria_id);

	          if(!empty($result_category_count)){

		        $total_accepted_student_college=$this->ReservedPlace->total_accepted_students_unsigned_to_department($this->college_id,$this->AcademicYear->current_academicyear());

	            $this->set(compact('result_category_count','total_accepted_student_college'));

	         }

	}
	*/
    public function get_summeries($selectedAcademicYear = null, $suffix = null)
    {

        $this->layout = 'ajax';

        $selectedAcademicYear = $selectedAcademicYear . '/' . $suffix;

        $placementsResultsCriterias = $this->_resultsCategory(
            $this->college_id,
            $selectedAcademicYear
        );
        $reservedmatrix = $this->ReservedPlace->find('all', array(
            'conditions' =>
                array(
                    'ReservedPlace.college_id' => $this->college_id,
                    'ReservedPlace.academicyear LIKE ' => $selectedAcademicYear . '%'
                )
        ));

        $summeryresultcategorystudent = array();
        foreach ($placementsResultsCriterias as $k => $v) {
            $summeryresultcategorystudent[$v] = $this->ReservedPlace->find_total_number_accepted_student_in_given_category
            (
                $k,
                $this->college_id,
                $selectedAcademicYear
            );
        }
        $generalsummery = $this->ReservedPlace->ParticipatingDepartment->find(
            'all',
            array(
                'conditions' => array(
                    'ParticipatingDepartment.college_id' => $this->college_id,
                    'ParticipatingDepartment.academic_year LIKE ' => $selectedAcademicYear . '%'
                )
            )
        );
        $reservedmodifiedmatrix = array();
        foreach ($reservedmatrix as &$value) {
            if (!empty($value['ReservedPlace']['participating_department_id'])) {
                foreach ($generalsummery as $k => $v) {
                    if ($v['ParticipatingDepartment']['department_id'] == $value['ReservedPlace']['participating_department_id']) {
                        $value['ReservedPlace']['department_name'] = $v['Department']['name'];
                        break 1;
                    }
                }
                $reservedmodifiedmatrix[$value['ReservedPlace']['department_name']][$value['PlacementsResultsCriteria']['result_category']][$value['ReservedPlace']['number']] = $value['ReservedPlace']['number'];
            }
        }
        /*$this->set('college_name',$this->ReservedPlace->College->field('College.name',
        array('College.id'=>$this->college_id)));
         */
        $this->set('acceptedStudents', $this->_getListOfAcceptedStudents($selectedAcademicYear, $this->college_id));
        $this->set(
            compact(
                'generalsummery',
                'reservedmatrix',
                'reservedmodifiedmatrix'
            )
        );
        $this->set(compact('summeryresultcategorystudent', 'selectedAcademicYear'));
        debug(
            $this->ReservedPlace->total_accepted_students_unsigned_to_department(
                $this->college_id,
                $selectedAcademicYear
            )
        );
        $this->set(
            'total_students_college_academicyear',
            $this->ReservedPlace->total_accepted_students_unsigned_to_department(
                $this->college_id,
                $selectedAcademicYear
            )
        );
    }

    /*reformat departments which is found via find all to find list*/
    public function _department_reformat($college_id = null)
    {

        $department_name_list = array();
        $departments = $this->ReservedPlace->ParticipatingDepartment->find(
            'all',
            array(
                'conditions' => array(
                    'ParticipatingDepartment.college_id' => $college_id,
                    'ParticipatingDepartment.academic_year LIKE ' => $this->AcademicYear->current_academicyear() . '%'
                )
            )
        );

        foreach ($departments as $key => $value) {
            $department_name_list[$value['Department']['id']] = $value['Department']['name'];
        }
        return $department_name_list;
    }

    public function _getListOfAcceptedStudents($academicyear = null, $college_id = null)
    {

        $this->loadModel('AcceptedStudent');
        //NULL tricky is solved using IS NULL as full conditional statmennt
        // array('Model.fieldname IS NULL') not array('Model.fieldname '=>'IS NULL') or
        // array('Model.fieldname '=>array(0,'',NULL,null))
        $isPrepartory = ClassRegistry::init('PlacementsResultsCriteria')->isPrepartoryResult(
            $academicyear,
            $college_id
        );
        $options =
            array(
                'conditions' =>
                    array(
                        "OR" =>
                            array(
                                'AcceptedStudent.department_id IS NULL',
                                'AcceptedStudent.department_id ' => array('', 0)
                            ),
                        "AcceptedStudent.academicyear LIKE" => $academicyear . '%',
                        "AcceptedStudent.college_id" => $college_id,
                        "AcceptedStudent.Placement_Approved_By_Department is null",
                        "OR" => array(
                            "AcceptedStudent.placementtype IS NULL",
                            "AcceptedStudent.placementtype" => CANCELLED_PLACEMENT
                        )
                    )
            );
        if ($isPrepartory == 0) {
            $options['conditions'][] = 'AcceptedStudent.freshman_result IS NOT NULL';
        }
        $acceptedStudents = $this->AcceptedStudent->find('all', $options);


        $preference_not_completed = array();
        foreach ($acceptedStudents as $k => $value) {
            $count = count($value['Preference']);
            if (!$count) {
                $preference_not_completed[] = $value;
            }
        }

        return $preference_not_completed;
    }

    /**
     *Method to get the department of the participating department
     *
     * @return array
     */
    public function _participating_department_name($college_id = null, $conditions = null)
    {

        $participating_departments = $this->ReservedPlace->ParticipatingDepartment->find(
            'all',
            array(
                'conditions' => array(
                    'ParticipatingDepartment.college_id' => $college_id,
                    'ParticipatingDepartment.academic_year LIKE ' => $this->AcademicYear->current_academicyear() . '%'
                )
            )
        );
        $reservedPlaces = $this->paginate('ReservedPlace', $conditions);

        foreach ($reservedPlaces as $key => &$data) {
            if ($data['ReservedPlace']['participating_department_id']) {
                foreach ($participating_departments as $v) {
                    if ($v['Department']['id'] == $data['ReservedPlace']['participating_department_id']) {
                        $data['ReservedPlace']['participating_department_name'] = $v['Department']['name'];
                        break;
                    }
                }
            }
        }
        return $reservedPlaces;
    }
}
