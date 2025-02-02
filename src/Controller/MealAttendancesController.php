<?php
class MealAttendancesController extends AppController {

	var $name = 'MealAttendances';
	var $components =array('AcademicYear');
	var $helpers = array('Media.Media');
	
	var $menuOptions = array(
		'parent' => 'mealService',
		'exclude'=>array('index'),
		'alias' => array(
                    'meal_attendance_report' =>'Meal Hall Attendances Report',
					'add' =>'Record Students Meal Attendance'
		)
	);
	
	function beforeFilter() {
        parent::beforeFilter();
         //$this->Auth->allow(array('*'));
         //$this->Auth->allow();  
    }
    
    function beforeRender() {

        $acyear_array_data = $this->AcademicYear->acyear_array();
        //To diplay current academic year as default in drop down list
        $defaultacademicyear=$this->AcademicYear->current_academicyear();

        $this->set(compact('acyear_array_data','defaultacademicyear'));
        unset($this->request->data['User']['password']);
	}
	
	function index() {
		$this->MealAttendance->recursive = 0;
		$this->set('mealAttendances', $this->paginate());
		return $this->redirect(array('action' => 'meal_attendance_report'));
	}

	/*function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid meal attendance'));
			return $this->redirect(array('action' => 'index'));
		}
		$this->set('mealAttendance', $this->MealAttendance->read(null, $id));
	}*/

	function add() {
		$user_id = $this->Auth->user('id');
		$meal_hall_ids = ClassRegistry::init('UserMealAssignment')->assigned_meal_hall($user_id);
		//debug("=>".date('Y-m-d h:i:s').$meal_hall_ids);
		if(!empty($meal_hall_ids)){
			$mealHalls = ClassRegistry::init('MealHall')->get_formatted_mealhall($meal_hall_ids);
			$this->Session->write('mealHalls',$mealHalls);
			$this->set(compact('mealHalls'));
			if(isset($this->request->data['continue'])){
				$meal_hall_id = $this->request->data['MealAttendance']['meal_hall_id'];
				if(!empty($meal_hall_id)){
					$current_time = date("H:i:s");
					//Auto detect meal type
					$auto_detected_meal_type = $this->MealAttendance->auto_detect_current_meal_type($current_time);
					$mealTypes = $this->MealAttendance->MealType->find('list');
					$this->set(compact('auto_detected_meal_type','mealTypes'));
				} else {
					$this->Session->setFlash('<span></span>'.__('Please select meal hall.'), 'default',array('class'=>'error-box er-message'));
				}
			}
			debug($this->request->data);
			if(!empty($this->request->data) && (isset($this->request->data['submit']) || isset($this->request->data['MealAttendance']['studentnumber']))){
				$mealHalls = $this->Session->read('mealHalls');
				$meal_hall_id = $this->request->data['MealAttendance']['meal_hall_id'];
				$mealTypes = $this->MealAttendance->MealType->find('list');
				$current_time = date("H:i:s");
				//Auto detect meal type
				$auto_detected_meal_type = $this->MealAttendance->auto_detect_current_meal_type($current_time);
				$this->set(compact('mealHalls','mealTypes','auto_detected_meal_type'));
				$studentnumber = $this->request->data['MealAttendance']['studentnumber'];
				$current_academicyear = $this->AcademicYear->current_academicyear();
				if(!empty($studentnumber)){
					$student_id = null;
					$accepted_student_id = null;
					$student_id = $this->MealAttendance->Student->field('Student.id',array('Student.studentnumber'=>$studentnumber));
					$accepted_student_id = $this->MealAttendance->Student->field('Student.accepted_student_id',array('Student.studentnumber'=>$studentnumber));

					if(!empty($student_id)){
						$students = $this->MealAttendance->Student->find('first',array('fields'=>array('Student.full_name','Student.studentnumber','Student.gender'),'conditions'=>array('Student.id'=>$student_id),'contain'=>array('Attachment')));
				
						//Check the student is assigned to this meal hall
						$is_assigned_this_meal_hall = null;
						$is_assigned_this_meal_hall = $this->MealAttendance->Student->MealHallAssignment->find('count',array('conditions'=>array('MealHallAssignment.meal_hall_id'=>$meal_hall_id, 'MealHallAssignment.academic_year'=>$current_academicyear,'OR'=>array('MealHallAssignment.student_id'=>$student_id, 'MealHallAssignment.accepted_student_id'=>$accepted_student_id))));

						//if(!empty($is_assigned_this_meal_hall)){

							//DONE:Check student status (status should be promoted or null, not dropout, not withdrawn, not cleared)  and registered for semester course and  the student whether fills cost sharing form or pay for meal service, or allowed by exception.	
							$cafeteria_cost_sharing = $this->MealAttendance->Student->
							CostShare->field('CostShare.cafeteria_fee',
							array('CostShare.academic_year'=>
							$current_academicyear, 'CostShare.student_id'=>$student_id));
                                
                            $exception_allowed=ClassRegistry::
                            init('ExceptionMealAssignment')->isInException($student_id,$meal_hall_id);
                             
							if($this->MealAttendance->Student->StudentExamStatus->elegibleForService(
							$student_id,$current_academicyear) 
							&& ((((!empty($cafeteria_cost_sharing) || ClassRegistry::
                            init('GeneralSetting')->allowMealWithoutCostsharing($student_id)) &&  
							$exception_allowed==3 && !empty($is_assigned_this_meal_hall)) 
							|| ($exception_allowed==1)
							 )
						    )
						   ){
							 	
									$current_date = date("Y-m-d");

									$is_already_served = 0;
									$is_already_served = $this->MealAttendance->find('count',array('conditions'=>array('MealAttendance.meal_type_id'=>$this->request->data['MealAttendance']['meal_type_id'], 'MealAttendance.student_id'=>$student_id,'MealAttendance.created LIKE'=>$current_date.'%')));

									if($is_already_served ==0){
										$attendance['meal_type_id'] = $this->request->data['MealAttendance']['meal_type_id'];
										$attendance['student_id'] = $student_id;
										$this->MealAttendance->create();
										if($this->MealAttendance->save($attendance)){
											$this->Session->setFlash('<span></span>'.__('The student is elegible to be serve this meal.'),'default',array('class'=>'success-box success-message'));
										} else {
											$this->Session->setFlash('<span></span>'.__('The meal attendance could not be saved. Please try again.'),'default',array('class'=>'error-box error-message'));
										}
									} else {
										$this->Session->setFlash('<span></span>'.__('The student is already served this meal. So he/she is not eligible to get addtional meal.'),'default',array('class'=>'error-box error-message'));
									}
								
							} else {
								//message
								   $error=$this->MealAttendance->Student->StudentExamStatus->invalidFields();
			               
			                       if(isset($error['error'])){
			                        $this->Session->setFlash(__('<span></span>'.$error['error'][0]),
			                        'default',array('class'=>'error-box error-message'));
			                       } else {
			                         
			                          if (empty($is_assigned_this_meal_hall)) {
							            $this->Session->setFlash('<span></span>'.__('The student is not assigned in this meal hall. So he/she is not eligible to get meal service in this meal hall.'),'default',array('class'=>'error-box error-message'));	     
								     } else {
								     
			                            if ((empty($cafeteria_cost_sharing) &&  $exception_allowed==3)) {
								          $this->Session->setFlash('<span></span>'.__('The student didn\'t fill cafeteria cost sharing fee. So he/she is not eligible to get meal service in this meal hall.'),'default',array('class'=>'error-box error-message'));
								        }
								        
								        if ($exception_allowed == 2) {
								          $this->Session->setFlash('<span></span>'.__('The student  is not allowed to get meal service. He/she is denied the meal service by exception.'),'default',array('class'=>'error-box error-message'));
								        
								       }
								   
								   }
								   
								}
							}
				
						/*} else {
							$this->Session->setFlash('<span></span>'.__('The student is not assigned in this meal hall. So he/she is not eligible to get meal service in this meal hall.'),'default',array('class'=>'error-box error-message'));
						}
						*/
						$this->set(compact('students'));
					} else {
						$this->Session->setFlash('<span></span>'.__('The student is not yet admitted. So he/she is not eligible to get meal service in this meal hall.'),'default',array('class'=>'error-box error-message'));
					}
				} else {
					$this->Session->setFlash('<span></span>'.__('Please provide student ID.'),'default',array('class'=>'info-box info-message'));
				}
			}
			//}
		} else {
			$this->Session->setFlash('<span></span>'.__('Currently there is no meal hall assigned to you. Please contact meal hall administrator.'),'default',array('class'=>'error-box er-message'));
		}
	}

	/*function edit($id = null) {
		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__('Invalid meal attendance'));
			return $this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->MealAttendance->save($this->request->data)) {
				$this->Session->setFlash(__('The meal attendance has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The meal attendance could not be saved. Please, try again.'));
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->MealAttendance->read(null, $id);
		}
		$mealTypes = $this->MealAttendance->MealType->find('list');
		$students = $this->MealAttendance->Student->find('list');
		$acceptedStudents = $this->MealAttendance->AcceptedStudent->find('list');
		$this->set(compact('mealTypes', 'students', 'acceptedStudents'));
	}*/

	/*function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for meal attendance'));
			return $this->redirect(array('action'=>'index'));
		}
		if ($this->MealAttendance->delete($id)) {
			$this->Session->setFlash(__('Meal attendance deleted'));
			return $this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Meal attendance was not deleted'));
		return $this->redirect(array('action' => 'index'));
	}*/
	
	function meal_attendance_report(){
		$mealHalls = ClassRegistry::init('MealHall')->find('list');
		$mealHallsView = $this->MealAttendance->Student->MealHallAssignment->get_formatted_mealhall_for_view();
		$current_date = date("Y-m-d");
		$selected_date = null;
		if(!empty($this->request->data['Search']['date'])){
			$selected_date = $this->request->data['Search']['date'];
			$selected_formatted_date = $selected_date['year'].'-'.$selected_date['month'].'-'.$selected_date['day'];
			$this->set(compact('selected_date','selected_formatted_date'));
		}
		
		if(!empty($selected_date)){
			$selected_academic_year = $this->AcademicYear->get_academicyear($this->request->data['Search']['date']['month'],$this->request->data['Search']['date']['year']);
		} else {
			$selected_academic_year = $this->AcademicYear->get_academicyear(date("m"),date("Y"));
		}
		$meal_hall_attendances_details = array();
		if(!empty($this->request->data)){
			$selected_meal_hall_id = $this->request->data['Search']['mealHalls'];
			//show meal attendances for the selected meal hall and date
			if(!empty($selected_meal_hall_id)){
				$meal_hall_attendances_details[$selected_meal_hall_id]['served'] = $this->MealAttendance->get_mealhall_attendance($selected_meal_hall_id,$selected_academic_year,$selected_formatted_date);
				$meal_hall_attendances_details[$selected_meal_hall_id]['total_assigned'] = $this->MealAttendance->Student->MealHallAssignment->get_mealhall_assigned_students_count($selected_academic_year,$selected_meal_hall_id);
				$meal_hall_attendances_details[$selected_meal_hall_id]['meal_hall_name'] = $mealHalls[$selected_meal_hall_id];
				$meal_hall_attendances_details[$selected_meal_hall_id]['campus'] = $this->MealAttendance->get_meal_hall_campus($selected_meal_hall_id);
			//show meal attendances for the selected date of all meal hall
			} else {
				foreach($mealHalls as $mhk=>$mhv){
					$meal_hall_attendances_details[$mhk]['served'] = $this->MealAttendance->get_mealhall_attendance($mhk,$selected_academic_year,$selected_formatted_date);
					$meal_hall_attendances_details[$mhk]['total_assigned'] = $this->MealAttendance->Student->MealHallAssignment->get_mealhall_assigned_students_count($selected_academic_year,$mhk);
					$meal_hall_attendances_details[$mhk]['meal_hall_name'] = $mealHalls[$mhk];
					$meal_hall_attendances_details[$mhk]['campus'] = $this->MealAttendance->get_meal_hall_campus($mhk);
				}
			}
		//Before hit search button, show current_date meal attendances for all meal hall
		} else {
			foreach($mealHalls as $mhk=>$mhv){
				$meal_hall_attendances_details[$mhk]['served'] = $this->MealAttendance->get_mealhall_attendance($mhk,$selected_academic_year,$current_date);
				$meal_hall_attendances_details[$mhk]['total_assigned'] = $this->MealAttendance->Student->MealHallAssignment->get_mealhall_assigned_students_count($selected_academic_year,$mhk);
				$meal_hall_attendances_details[$mhk]['meal_hall_name'] = $mealHalls[$mhk];
				$meal_hall_attendances_details[$mhk]['campus'] = $this->MealAttendance->get_meal_hall_campus($mhk);
			}
		}
		$this->set(compact('mealHallsView','current_date','meal_hall_attendances_details'));
	}
}
