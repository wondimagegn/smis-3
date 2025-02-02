<?php
class InstructorNumberOfExamConstraintsController extends AppController {

	var $name = 'InstructorNumberOfExamConstraints';
	var $components =array('AcademicYear');
	
	var $menuOptions = array(
		'exclude' => array('*'),
		'controllerButton' => false,
	);
	function beforeFilter(){
        parent::beforeFilter();
         //$this->Auth->allow(array('*'));
        $this->Auth->allow('get_instructor_number_of_exam_constraints_details');  
    }
	function beforeRender() {

        $acyear_array_data = $this->AcademicYear->acyear_array();
        //To diplay current academic year as default in drop down list
        $defaultacademicyear=$this->AcademicYear->current_academicyear();

        $this->set(compact('acyear_array_data','defaultacademicyear'));
        unset($this->request->data['User']['password']);
	}
	
	function index() {
		$this->InstructorNumberOfExamConstraint->recursive = 0;
		$this->set('instructorNumberOfExamConstraints', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid instructor number of exam constraint'));
			return $this->redirect(array('action' => 'index'));
		}
		$this->set('instructorNumberOfExamConstraint', $this->InstructorNumberOfExamConstraint->read(null, $id));
	}

	function add() {
		$from_delete = $this->Session->read('from_delete');
		if($from_delete !=1){
			if($this->Session->read('selected_academicyear')){
				$this->Session->delete('selected_academicyear');
			} 
			if($this->Session->read('selected_semester')){
				$this->Session->delete('selected_semester');
			} 
			if($this->Session->read('selected_department')){
				$this->Session->delete('selected_department');
			} 
			if($this->Session->read('selected_instructor_id')){
				$this->Session->delete('selected_instructor_id');
			} 
			if($this->Session->read('selected_instructor')){
				$this->Session->delete('selected_instructor');
			} 
		}
	
		$departments = $this->InstructorNumberOfExamConstraint->Staff->Department->find('list',array('conditions'=>array('Department.college_id'=>$this->college_id)));
		$yearLevels = $this->InstructorNumberOfExamConstraint->get_maximum_year_levels_of_college($this->college_id);
		//Set id=10000 for other colleges invigilators
		$departments[10000] = 'Other Colleges';
		$this->set(compact('departments','yearLevels'));
		if(!empty($this->request->data) && isset($this->request->data['search'])) {
			$everythingfine=false;
			switch($this->request->data) {
			        case empty($this->request->data['InstructorNumberOfExamConstraint']['academicyear']) :
			         $this->Session->setFlash('<span></span> '.__('Please select the academic year of the exam period that you want to add instructor number of exam constraints.'),'default',array('class'=>'error-box error-message'));  
			         break; 
			         case empty($this->request->data['InstructorNumberOfExamConstraint']['department_id']) :
			         $this->Session->setFlash('<span></span> '.__('Please select the department of the exam period that you want to add instructor number of exam constraints. '),'default',array('class'=>'error-box error-message'));  
			         break;
			        case empty($this->request->data['InstructorNumberOfExamConstraint']['semester']) :
			         $this->Session->setFlash('<span></span> '.__('Please select the semester of the exam period that you want to add instructor enumber of exam constraints. '),'default',array('class'=>'error-box error-message'));  
			         break; 					 
			         default:
			         $everythingfine=true;             
			}	
			if ($everythingfine) {
				$selected_academicyear =$this->request->data['InstructorNumberOfExamConstraint']['academicyear'];
			    $selected_department =$this->request->data['InstructorNumberOfExamConstraint']['department_id'];
				$selected_semester = $this->request->data['InstructorNumberOfExamConstraint']['semester'];

				$instructors_list =$this->_get_instructors_list($selected_department,$selected_academicyear,$selected_semester);

				if (empty($instructors_list)) {
			         $this->Session->setFlash('<span></span> '.__('There is no instructor to add  instructor number of exam constraints in the selected criteria.'),'default',array('class'=>'info-box info-message'));     
			    } else {
					$this->set(compact('instructors_list','selected_academicyear', 'selected_department', 'selected_semester'));
				}
			}
		} 
		if (!empty($this->request->data) && isset($this->request->data['submit'])) {
			$selected_academicyear =$this->request->data['InstructorNumberOfExamConstraint']['academicyear'];
			$selected_semester = $this->request->data['InstructorNumberOfExamConstraint']['semester'];
			$selected_department = $this->request->data['InstructorNumberOfExamConstraint']['department_id'];
			$selected_year_level = $this->request->data['InstructorNumberOfExamConstraint']['year_level_id'];
			$selected_max_number_of_exam = $this->request->data['InstructorNumberOfExamConstraint']['max_number_of_exam'];

			$selected_instructor = $this->request->data['InstructorNumberOfExamConstraint']['staff_id'];
			$explode_staff_type = explode("~",$selected_instructor);
			$selected_instructor_id = $explode_staff_type[1];
			if(!empty($selected_max_number_of_exam)){
				$year_level_count = 0;
				if(!empty($this->request->data['InstructorNumberOfExamConstraint']['year_level_id'])) {
					$year_level_count = count($selected_year_level);
				}
				if(!empty($year_level_count)) {
					$is_already_recorded = $this->InstructorNumberOfExamConstraint->alreadyRecorded($this->college_id,$selected_academicyear, $selected_semester, $selected_year_level, $selected_instructor_id);

					if($is_already_recorded == false){
						$instructor_number_of_exams = array();
						$index = 0;
						foreach($selected_year_level as $ylk=>$ylv){
							if(strcasecmp($explode_staff_type[0],"S")==0){
								$instructor_number_of_exams['InstructorNumberOfExamConstraint'][$index]['staff_id'] = $selected_instructor_id;
							} else {
								$instructor_number_of_exams['InstructorNumberOfExamConstraint'][$index]['staff_for_exam_id'] = $selected_instructor_id;
							}
							$instructor_number_of_exams['InstructorNumberOfExamConstraint'][$index]['college_id'] = $this->college_id;
							$instructor_number_of_exams['InstructorNumberOfExamConstraint'][$index]['academic_year'] = $selected_academicyear;
							$instructor_number_of_exams['InstructorNumberOfExamConstraint'][$index]['semester'] = $selected_semester;
							$this->request->data['InstructorNumberOfExamConstraints']['year_level_id'] = $selected_year_level;
							$instructor_number_of_exams['InstructorNumberOfExamConstraint'][$index]['year_level_id'] = $ylv;
							$instructor_number_of_exams['InstructorNumberOfExamConstraint'][$index]['max_number_of_exam'] = $selected_max_number_of_exam;
							
							$index = $index + 1;
						}
						if($this->InstructorNumberOfExamConstraint->saveAll($instructor_number_of_exams['InstructorNumberOfExamConstraint'], array('validate'=>'first'))){
							$is_saved = true;
						}
						$this->InstructorNumberOfExamConstraint->create();
						if ($is_saved == true) {
							$this->Session->setFlash('<span></span>'.__('The instructor number of exam constraint has been saved.'),'default',array('class'=>'success-box success-message'));
					//$this->redirect(array('action' => 'index'));
						} else {
							$this->Session->setFlash('<span></span>'.__('The instructor number of exam constraint could not be saved. Please, try again.'),'default',array('class'=>'error-box error-message'));
						}
				} else {
					$error=$this->InstructorNumberOfExamConstraint->invalidFields();
		             if(isset($error['already_recorded_instructor_number_of_exam'])){
						$this->Session->setFlash('<span></span>'.__($error['already_recorded_instructor_number_of_exam'][0].', please exclude this year level from you selection or delete the record of this year level.'),'default',array('class'=>'error-box error-message'));
					}
				}
			} else {
				$this->Session->setFlash('<span></span>'.__('Please select year level.'),'default',array('class'=>'error-box error-message'));
			}
		} else {
			$this->Session->setFlash('<span></span>'.__('Please provide maximum number of exam.'),'default',array('class'=>'error-box error-message'));
		}

			$this->request->data['submit'] = true;
			$instructors_list =$this->_get_instructors_list($selected_department,$selected_academicyear,$selected_semester);
			$instructorNumberOfExamConstraints = $this->_get_already_recorded_instructor_number_of_exam_constraints($selected_instructor_id,$selected_academicyear,$selected_semester,$selected_department);

			$this->set(compact('selected_instructor','selected_department', 'selected_academicyear', 'selected_semester', 'instructors_list','instructorNumberOfExamConstraints','selected_instructor_id'));
		}
		if($this->Session->read('from_delete')){
			if($this->Session->read('selected_academicyear')){
				$selected_academicyear = $this->Session->read('selected_academicyear');
			} 
			if($this->Session->read('selected_semester')){
				$selected_semester = $this->Session->read('selected_semester');
			} 
			if($this->Session->read('selected_department')){
				$selected_department = $this->Session->read('selected_department');
			} 
			if($this->Session->read('selected_instructor_id')){
				$selected_instructor_id = $this->Session->read('selected_instructor_id');
			} 
			if($this->Session->read('selected_instructor')){
				$selected_instructor = $this->Session->read('selected_instructor');
			} 
			
			$this->request->data['submit'] = true;
			$instructors_list =$this->_get_instructors_list($selected_department,$selected_academicyear,$selected_semester);
			$instructorNumberOfExamConstraints = $this->_get_already_recorded_instructor_number_of_exam_constraints($selected_instructor_id,$selected_academicyear,$selected_semester,$selected_department);
			
			$this->set(compact('selected_instructor','selected_department', 'selected_academicyear', 'selected_semester', 'instructors_list','instructorNumberOfExamConstraints','selected_instructor_id'));
			$this->Session->read('from_delete');
		}
	}

	function edit($id = null) {
		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__('Invalid instructor number of exam constraint'));
			return $this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->InstructorNumberOfExamConstraint->save($this->request->data)) {
				$this->Session->setFlash(__('The instructor number of exam constraint has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The instructor number of exam constraint could not be saved. Please, try again.'));
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->InstructorNumberOfExamConstraint->read(null, $id);
		}
		$staffs = $this->InstructorNumberOfExamConstraint->Staff->find('list');
		$staffForExams = $this->InstructorNumberOfExamConstraint->StaffForExam->find('list');
		$this->set(compact('staffs', 'staffForExams'));
	}

	function delete($id = null,$from=null) {
		if (!$id) {
			$this->Session->setFlash('<span></span> '.__('Invalid id for instructor number of exam constraint.'), 'default',array('class'=>'error-box error-message')); 
			if(empty($from)){
				return $this->redirect(array('action'=>'index'));
			} else {
				return $this->redirect(array('action'=>'add'));
			}
		}
		$from_delete = 1;
		$this->Session->write('from_delete',$from_delete);
		$deleted_instructor_number_of_exam_constraint_data = $this->InstructorNumberOfExamConstraint->find('first',array('conditions'=>array('InstructorNumberOfExamConstraint.id'=>$id),'recursive'=>-1));
		$this->Session->write('selected_academicyear',$deleted_instructor_number_of_exam_constraint_data['InstructorNumberOfExamConstraint']['academic_year']);
			$this->Session->write('selected_semester',$deleted_instructor_number_of_exam_constraint_data['InstructorNumberOfExamConstraint']['semester']);
			if(!empty($deleted_instructor_number_of_exam_constraint_data['InstructorNumberOfExamConstraint']['staff_id'])){
				$selected_instructor_id = $deleted_instructor_number_of_exam_constraint_data['InstructorNumberOfExamConstraint']['staff_id'];
				$this->Session->write('selected_instructor_id',$selected_instructor_id);
				$selected_instructor = 'S'.'~'.$selected_instructor_id;
				$this->Session->write('selected_instructor',$selected_instructor);
				$selected_department = $this->InstructorNumberOfExamConstraint->Staff->field('Staff.department_id',array('Staff.id'=>$selected_instructor_id));
				$this->Session->write('selected_department',$selected_department);
			} else if(!empty($deleted_instructor_number_of_exam_constraint_data['InstructorNumberOfExamConstraint']['staff_for_exam_id'])){
				$selected_instructor_id = $deleted_instructor_number_of_exam_constraint_data['InstructorNumberOfExamConstraint']['staff_for_exam_id'];
				$this->Session->write('selected_instructor_id',$selected_instructor_id);
				$selected_instructor = 'SFE'.'~'.$selected_instructor_id;
				$this->Session->write('selected_instructor',$selected_instructor);
				$selected_department = 10000;
				$this->Session->write('selected_department',$selected_department);
			}
		if ($this->InstructorNumberOfExamConstraint->delete($id)) {
			$this->Session->setFlash('<span></span> '.__('Instructor number of exam constraint deleted'), 'default',array('class'=>'success-box success-message'));  
				if(empty($from)){
					return $this->redirect(array('action'=>'index'));
				} else {
					return $this->redirect(array('action'=>'add'));
				}
		}
		$this->Session->setFlash('<span></span> '.__('Instructor number of exam constraint was not deleted.'), 'default',array('class'=>'error-box error-message')); 
			if(empty($from)){
				return $this->redirect(array('action'=>'index'));
			} else {
				return $this->redirect(array('action'=>'add'));
			}
	}
	function _get_instructors_list($department_id=null,$academicyear=null,$semester=null){
	 	if($department_id == 10000){
			$instructors_list = $this->InstructorNumberOfExamConstraint->StaffForExam->find('all',array('conditions'=>array('StaffForExam.college_id'=>$this->college_id,'StaffForExam.academic_year'=>$academicyear, 'StaffForExam.semester'=>$semester),'contain'=>array('Staff'=>array('fields'=>array('Staff.id','Staff.full_name'),'Title'=>array('fields'=>array('title')),'Position'=>array('fields'=>array('position')),'College'=>array('fields'=>array('College.id','College.name'))))));
		} else {
			$instructors_list = $this->InstructorNumberOfExamConstraint->Staff->find('all',array('conditions'=>array('Staff.college_id'=>$this->college_id,'Staff.department_id'=>$department_id, 'Staff.active'=>1),'fields'=>array('Staff.id','Staff.full_name'),'contain'=>array('Title'=>array('fields'=>array('title')),'Position'=>array('fields'=>array('position')))));
		}
		return $instructors_list;
	 }
	 
	 function get_instructor_number_of_exam_constraints_details($data=null){
	 	$this->layout = 'ajax';
		$explode_data = explode("~",$data);
		if(!empty($explode_data[0])) {
			$staff_type = $explode_data[0];
			$instructor_id = $explode_data[1];
			$academicyear = $explode_data[2];
			$academicyear = str_replace('-','/',$academicyear);
			$semester = $explode_data[3];
			$department_id = $explode_data[4];
			$yearLevels = $this->InstructorNumberOfExamConstraint->get_maximum_year_levels_of_college($this->college_id);
			$instructorNumberOfExamConstraints = $this->_get_already_recorded_instructor_number_of_exam_constraints($instructor_id,$academicyear,$semester,$department_id);
			$this->set(compact('instructor_id','yearLevels','instructorNumberOfExamConstraints'));
		}
	 }
	 
	 function _get_already_recorded_instructor_number_of_exam_constraints($instructor_id=null,$academicyear=null,$semester=null,$department_id=null){
	 	if($department_id ==10000){
	 		$instructorNumberOfExamConstraints = $this->InstructorNumberOfExamConstraint->find('all',array('conditions'=>array('InstructorNumberOfExamConstraint.academic_year'=>$academicyear, 'InstructorNumberOfExamConstraint.semester'=>$semester, 'InstructorNumberOfExamConstraint.staff_for_exam_id'=>$instructor_id),'contain'=>array('StaffForExam'=>array('Staff'=>array('fields'=>array('Staff.id','Staff.full_name'),'Title'=>array('fields'=>array('Title.title')),'Position'=>array('fields'=>array('Position.position')))))));
	 	} else {
	 		$instructorNumberOfExamConstraints = $this->InstructorNumberOfExamConstraint->find('all',array('conditions'=>array('InstructorNumberOfExamConstraint.academic_year'=>$academicyear, 'InstructorNumberOfExamConstraint.semester'=>$semester,'InstructorNumberOfExamConstraint.staff_id'=>$instructor_id),'contain'=>array('Staff'=>array('fields'=>array('Staff.id','Staff.full_name'),'Title'=>array('fields'=>array('Title.title')),'Position'=>array('fields'=>array('Position.position'))))));
	 	}
	 	return $instructorNumberOfExamConstraints;
		
	 }
}
