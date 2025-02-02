<?php
class InstructorExamExcludeDateConstraintsController extends AppController {

	var $name = 'InstructorExamExcludeDateConstraints';
	var $components =array('AcademicYear');
	
	var $menuOptions = array(
		'exclude' => array('*'),
		'controllerButton' => false,
	);
	function beforeFilter(){
        parent::beforeFilter();
         //$this->Auth->allow(array('*'));
         $this->Auth->allow('get_instructor_exam_exclude_date_constraints_details');  
    }
	function beforeRender() {

        $acyear_array_data = $this->AcademicYear->acyear_array();
        //To diplay current academic year as default in drop down list
        $defaultacademicyear=$this->AcademicYear->current_academicyear();

        $this->set(compact('acyear_array_data','defaultacademicyear'));
        unset($this->request->data['User']['password']);
	}
	function index() {
		$this->InstructorExamExcludeDateConstraint->recursive = 0;
		$this->set('instructorExamExcludeDateConstraints', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid instructor exam exclude date constraint'));
			return $this->redirect(array('action' => 'index'));
		}
		$this->set('instructorExamExcludeDateConstraint', $this->InstructorExamExcludeDateConstraint->read(null, $id));
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
	
		$departments = $this->InstructorExamExcludeDateConstraint->Staff->Department->find('list',array('conditions'=>array('Department.college_id'=>$this->college_id)));
		//Set id=10000 for other colleges invigilators
		$departments[10000] = 'Other Colleges';
		$this->set(compact('departments'));
		if(!empty($this->request->data) && isset($this->request->data['search'])) {
			$everythingfine=false;
			switch($this->request->data) {
			        case empty($this->request->data['InstructorExamExcludeDateConstraint']['academicyear']) :
			         $this->Session->setFlash('<span></span> '.__('Please select the academic year of the exam period that you want to add instructor exam exclude date constraints.'),'default',array('class'=>'error-box error-message'));  
			         break; 
			         case empty($this->request->data['InstructorExamExcludeDateConstraint']['department_id']) :
			         $this->Session->setFlash('<span></span> '.__('Please select the department of the exam period that you want to add instructor exam exclude date constraints. '),'default',array('class'=>'error-box error-message'));  
			         break;
			        case empty($this->request->data['InstructorExamExcludeDateConstraint']['semester']) :
			         $this->Session->setFlash('<span></span> '.__('Please select the semester of the exam period that you want to add instructor exam exclude date constraints. '),'default',array('class'=>'error-box error-message'));  
			         break; 					 
			         default:
			         $everythingfine=true;             
			}	
			if ($everythingfine) {
				$selected_academicyear =$this->request->data['InstructorExamExcludeDateConstraint']['academicyear'];
			    $selected_department =$this->request->data['InstructorExamExcludeDateConstraint']['department_id'];
				$selected_semester = $this->request->data['InstructorExamExcludeDateConstraint']['semester'];

				$instructors_list =$this->_get_instructors_list($selected_department,$selected_academicyear,$selected_semester);

				if (empty($instructors_list)) {
			         $this->Session->setFlash('<span></span> '.__('There is no instructor to add  instructor exam exclude date constraints in the selected criteria.'),'default',array('class'=>'info-box info-message'));     
			    } else {
					$this->set(compact('instructors_list','selected_academicyear', 'selected_department', 'selected_semester'));
				}
			}
		} 
		if (!empty($this->request->data) && isset($this->request->data['submit'])) {

			$selected_academicyear =$this->request->data['InstructorExamExcludeDateConstraint']['academicyear'];
			$selected_semester = $this->request->data['InstructorExamExcludeDateConstraint']['semester'];
			$selected_department = $this->request->data['InstructorExamExcludeDateConstraint']['department_id'];

			$date_array = $this->_get_exam_periods($selected_academicyear, $selected_semester);

			$selected_sessions_array = array();
			if(!empty($this->request->data['InstructorExamExcludeDateConstraint']['Selected'])){
				foreach($this->request->data['InstructorExamExcludeDateConstraint']['Selected'] as $ieedcsk=>$ieedcsv){
					if($ieedcsv != '0'){
						$explode_data = explode("-",$ieedcsv);
						$selected_sessions_array[$date_array[$explode_data[0]]][] = $explode_data[1];
					}
				}			
			}
			$selected_instructor = $this->request->data['InstructorExamExcludeDateConstraint']['staff_id'];
			$explode_staff_type = explode("~",$selected_instructor);
			
			$selected_instructor_id = $explode_staff_type[1];
			if(strcasecmp($explode_staff_type[0],"s")==0){
				$this->request->data['InstructorExamExcludeDateConstraints']['staff_id'] = $selected_instructor_id;
			} else {
				$this->request->data['InstructorExamExcludeDateConstraints']['staff_for_exam_id'] = $selected_instructor_id;
			}
			$this->request->data['InstructorExamExcludeDateConstraints']['academic_year'] = $selected_academicyear;
			$this->request->data['InstructorExamExcludeDateConstraints']['semester'] = $selected_semester;

			$count_selected_sessions= count($selected_sessions_array);
			if($count_selected_sessions !=0){
				$issave = false;
				foreach($selected_sessions_array as $date_key =>$date_value){
					$this->request->data['InstructorExamExcludeDateConstraints']['exam_date'] = $date_key;
					foreach($date_value as $session_key => $session_value){
						$this->request->data['InstructorExamExcludeDateConstraints']['session'] = $session_value;
						$this->InstructorExamExcludeDateConstraint->create();
						if ($this->InstructorExamExcludeDateConstraint->save($this->request->data['InstructorExamExcludeDateConstraints'])) {
							$issave = true;
						}
					}
				}
				if ($issave == true) {
					$this->Session->setFlash('<span></span>'.__('The instructor exam exclude date constraint has been saved.'),'default',array('class'=>'success-box success-message'));
					//$this->redirect(array('action' => 'index'));
				} else {
					$this->Session->setFlash('<span></span>'.__('The instructor exam exclude date constraint could not be saved. Please, try again.'),'default',array('class'=>'error-box error-message'));
				}
			} else {
				$this->Session->setFlash('<span></span>'.__('Please check at least 1 session.'),'default',array('class'=>'error-box error-message'));
			}

			$this->request->data['submit'] = true;
			$instructors_list =$this->_get_instructors_list($selected_department,$selected_academicyear,$selected_semester);
			$already_recorded_instructor_exam_excluded_date_constraints	= $this->InstructorExamExcludeDateConstraint->get_already_recorded_instructor_exam_excluded_date_constraint($selected_instructor_id);

			$this->set(compact('date_array','selected_instructor','selected_department', 'selected_academicyear','selected_semester','already_recorded_instructor_exam_excluded_date_constraints', 'instructors_list'));
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
			$date_array = $this->_get_exam_periods($selected_academicyear, $selected_semester);
			$instructors_list =$this->_get_instructors_list($selected_department,$selected_academicyear,$selected_semester);
			$already_recorded_instructor_exam_excluded_date_constraints	= $this->InstructorExamExcludeDateConstraint->get_already_recorded_instructor_exam_excluded_date_constraint($selected_instructor_id);

			$this->set(compact('date_array','selected_instructor','selected_department', 'selected_academicyear','selected_semester','already_recorded_instructor_exam_excluded_date_constraints','instructors_list'));
			$this->Session->delete('from_delete');
		}
	}

	function edit($id = null) {
		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__('Invalid instructor exam exclude date constraint'));
			return $this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->InstructorExamExcludeDateConstraint->save($this->request->data)) {
				$this->Session->setFlash(__('The instructor exam exclude date constraint has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The instructor exam exclude date constraint could not be saved. Please, try again.'));
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->InstructorExamExcludeDateConstraint->read(null, $id);
		}
		$staffs = $this->InstructorExamExcludeDateConstraint->Staff->find('list');
		$staffForExams = $this->InstructorExamExcludeDateConstraint->StaffForExam->find('list');
		$this->set(compact('staffs', 'staffForExams'));
	}

	function delete($id = null,$from=null) {
		if (!$id) {
			$this->Session->setFlash('<span></span> '.__('Invalid id for instructor exam exclude date constraint.'), 'default',array('class'=>'error-box error-message')); 
			if(empty($from)){
				return $this->redirect(array('action'=>'index'));
			} else {
				return $this->redirect(array('action'=>'add'));
			}
		}
		$from_delete = 1;
		$this->Session->write('from_delete',$from_delete);
		$deleted_instructor_exam_exclude_date_constraint_data = $this->InstructorExamExcludeDateConstraint->find('first',array('conditions'=>array('InstructorExamExcludeDateConstraint.id'=>$id),'recursive'=>-1));
		$this->Session->write('selected_academicyear',$deleted_instructor_exam_exclude_date_constraint_data['InstructorExamExcludeDateConstraint']['academic_year']);
			$this->Session->write('selected_semester',$deleted_instructor_exam_exclude_date_constraint_data['InstructorExamExcludeDateConstraint']['semester']);
			if(!empty($deleted_instructor_exam_exclude_date_constraint_data['InstructorExamExcludeDateConstraint']['staff_id'])){
				$selected_instructor_id = $deleted_instructor_exam_exclude_date_constraint_data['InstructorExamExcludeDateConstraint']['staff_id'];
				$this->Session->write('selected_instructor_id',$selected_instructor_id);
				$selected_instructor = 'S'.'~'.$selected_instructor_id;
				$this->Session->write('selected_instructor',$selected_instructor);
				$selected_department = $this->InstructorExamExcludeDateConstraint->Staff->field('Staff.department_id',array('Staff.id'=>$selected_instructor_id));
				$this->Session->write('selected_department',$selected_department);
			} else if(!empty($deleted_instructor_exam_exclude_date_constraint_data['InstructorExamExcludeDateConstraint']['staff_for_exam_id'])){
				$selected_instructor_id = $deleted_instructor_exam_exclude_date_constraint_data['InstructorExamExcludeDateConstraint']['staff_for_exam_id'];
				$this->Session->write('selected_instructor_id',$selected_instructor_id);
				$selected_instructor = 'SFE'.'~'.$selected_instructor_id;
				$this->Session->write('selected_instructor',$selected_instructor);
				$selected_department = 10000;
				$this->Session->write('selected_department',$selected_department);
			}

		if ($this->InstructorExamExcludeDateConstraint->delete($id)) {
			$this->Session->setFlash('<span></span> '.__('Instructor exam exclude date constraint deleted'), 'default',array('class'=>'success-box success-message'));  
				if(empty($from)){
					return $this->redirect(array('action'=>'index'));
				} else {
					return $this->redirect(array('action'=>'add'));
				}
		}
		$this->Session->setFlash('<span></span> '.__('Instructor exam exclude date constraint was not deleted.'), 'default',array('class'=>'error-box error-message')); 
			if(empty($from)){
				return $this->redirect(array('action'=>'index'));
			} else {
				return $this->redirect(array('action'=>'add'));
			}
	}
	
	 function _get_instructors_list($department_id=null,$academicyear=null,$semester=null){
	 	if($department_id == 10000){
			$instructors_list = $this->InstructorExamExcludeDateConstraint->StaffForExam->find('all',array('conditions'=>array('StaffForExam.college_id'=>$this->college_id,'StaffForExam.academic_year'=>$academicyear, 'StaffForExam.semester'=>$semester),'contain'=>array('Staff'=>array('fields'=>array('Staff.id','Staff.full_name'),'Title'=>array('fields'=>array('title')),'Position'=>array('fields'=>array('position')),'College'=>array('fields'=>array('College.id','College.name'))))));
		} else {
			$instructors_list = $this->InstructorExamExcludeDateConstraint->Staff->find('all',array('conditions'=>array('Staff.college_id'=>$this->college_id,'Staff.department_id'=>$department_id, 'Staff.active'=>1),'fields'=>array('Staff.id','Staff.full_name'),'contain'=>array('Title'=>array('fields'=>array('title')),'Position'=>array('fields'=>array('position')))));
		}
		return $instructors_list;
	 }
	/*
	function get_instructors($data=null){
		$explode_data = explode("~",$data);
		$department_id = $explode_data[0];
		$academicyear = $explode_data[1];
		$academicyear = str_replace('-','/',$academicyear);
		$semester = $explode_data[2];
		if(!empty($department_id)){
			$instructors_list =$this->_get_instructors_list($department_id,$academicyear,$semester);
			$this->set(compact('instructors_list'));
		}
	} */
	
	function get_instructor_exam_exclude_date_constraints_details($data=null){
		$this->layout = 'ajax';
		$explode_data = explode("~",$data);
		$staff_type = $explode_data[0];
		$instructor_id = $explode_data[1];
		$academicyear = $explode_data[2];
		$academicyear = str_replace('-','/',$academicyear);
		$semester = $explode_data[3];

		$date_array = $this->_get_exam_periods($academicyear,$semester);

		if(!empty($instructor_id)){
			$already_recorded_instructor_exam_excluded_date_constraints	= $this->InstructorExamExcludeDateConstraint->get_already_recorded_instructor_exam_excluded_date_constraint($instructor_id);
		}
		$this->set(compact('date_array','already_recorded_instructor_exam_excluded_date_constraints'));
	}
	function _get_exam_periods($academicyear=null, $semester=null){
		if(!empty($academicyear) && !empty($semester)){
			$examPeriods = ClassRegistry::init('ExamPeriod')->find('all',array('fields'=>array('ExamPeriod.start_date','ExamPeriod.end_date'),'conditions'=>array('ExamPeriod.academic_year'=>$academicyear,'ExamPeriod.college_id'=>$this->college_id,'ExamPeriod.semester'=>$semester),'recursive'=>-1));
			$date_array = array();
			if(!empty($examPeriods)){
				$start_date = $examPeriods[0]['ExamPeriod']['start_date'];
				$end_date =	$examPeriods[0]['ExamPeriod']['end_date'];
				//$count_examPeriods = count($examPeriods);
				foreach($examPeriods as $examPeriod){
					if($start_date > $examPeriod['ExamPeriod']['start_date']){
						$start_date = $examPeriod['ExamPeriod']['start_date'];
					}
					if($end_date < $examPeriod['ExamPeriod']['end_date']){
						$end_date = $examPeriod['ExamPeriod']['end_date'];
					}
				}
				$start_date = strtotime($start_date); // Convert date to a UNIX timestamp
				$end_date = strtotime($end_date); // Convert date to a UNIX timestamp
				// Loop from the start date to end date and output all dates inbetween
				for($i=$start_date; $i<=$end_date; $i+=86400) {
					$date_array[] = date("Y-m-d", $i);
				}
			}
		return $date_array;
		}
	}
}
