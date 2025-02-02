<?php
class InstructorClassPeriodCourseConstraintsController extends AppController {

	var $name = 'InstructorClassPeriodCourseConstraints';
	var $components =array('AcademicYear');
	
	var $menuOptions = array(
		//'parent' => 'courseConstraint',
		'exclude' => array('*'),
		'controllerButton' => false,
	);
	function beforeFilter(){
        parent::beforeFilter();
         //$this->Auth->allow(array('*'));
        $this->Auth->allow('get_year_level','get_periods');  
    }
	function beforeRender() {

        $acyear_array_data = $this->AcademicYear->acyear_array();
        //To diplay current academic year as default in drop down list
        $defaultacademicyear=$this->AcademicYear->current_academicyear();

        $this->set(compact('acyear_array_data','defaultacademicyear'));
        unset($this->request->data['User']['password']);
	}
	function index() {
		//$this->InstructorClassPeriodCourseConstraint->recursive = 0;
		$this->paginate = array('conditions'=>array('InstructorClassPeriodCourseConstraint.college_id'=>$this->college_id), 'contain'=>array('Staff'=>array('fields'=>array('Staff.id', 'Staff.first_name','Staff.middle_name', 'Staff.last_name'), 'Title'=>array('fields'=>array('Title.title')),'Position'=>array('fields'=>array('Position.position'))),'ClassPeriod'=>array('fields'=>array('ClassPeriod.week_day','ClassPeriod.id','ClassPeriod.period_setting_id'),'PeriodSetting'=>array('fields'=>array('PeriodSetting.period','PeriodSetting.hour')))));
		$this->set('instructorClassPeriodCourseConstraints', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid instructor class period course constraint'));
			return $this->redirect(array('action' => 'index'));
		}
		$this->set('instructorClassPeriodCourseConstraint', $this->InstructorClassPeriodCourseConstraint->read(null, $id));
	}

	function add() {
		$from_delete = $this->Session->read('from_delete');
		if($from_delete !=1){
			if($this->Session->read('selected_academicyear')){
				$this->Session->delete('selected_academicyear');
			}
			if($this->Session->read('selected_program')){
				$this->Session->delete('selected_program');
			} 
			if($this->Session->read('selected_program_type')){
				$this->Session->delete('selected_program_type');
			} 
			if($this->Session->read('selected_semester')){
				$this->Session->delete('selected_semester');
			} 
			if($this->Session->read('selected_year_level')){
				$this->Session->delete('selected_year_level');
			} 
			if($this->Session->read('selected_department')){
				$this->Session->delete('selected_department');
			} 
		}
		$programs = $this->InstructorClassPeriodCourseConstraint->College->PublishedCourse->Program->find('list');
        $programTypes = $this->InstructorClassPeriodCourseConstraint->College->PublishedCourse->ProgramType->find('list');
		$departments = $this->InstructorClassPeriodCourseConstraint->College->Department->find('list',array('conditions'=>array('Department.college_id'=>$this->college_id)));
		$yearLevels = null;
		$yearLevels = $this->InstructorClassPeriodCourseConstraint->College->PublishedCourse->YearLevel->find('list',array('conditions'=> array('YearLevel.department_id'=>$this->department_id)));
        $this->set(compact('programs','programTypes','departments','yearLevels'));
        if(!empty($this->request->data) && isset($this->request->data['search'])) {
        	if($this->Session->read('instructors')){
				$this->Session->delete('instructors');
			}
			if($this->Session->read('selected_week_day')){
				 $this->Session->delete('selected_week_day');
			}
        	$everythingfine=false;
			switch($this->request->data) {
		        case empty($this->request->data['InstructorClassPeriodCourseConstraint']['academicyear']) :
		        	 $this->Session->setFlash('<span></span> '.__('Please select the academic year that you want to add instructor class period course constraints. '),'default',array('class'=>'error-box error-message'));  
		         break; 
		        case empty($this->request->data['InstructorClassPeriodCourseConstraint']['program_id']) :
		         	$this->Session->setFlash('<span></span> '.__('Please select the academic year that you want to add instructor class period course constraints. '),'default',array('class'=>'error-box error-message'));  
		         break;    
		        case empty($this->request->data['InstructorClassPeriodCourseConstraint']['program_type_id']) :
		         	$this->Session->setFlash('<span></span> '.__('Please select the academic year that you want to add instructor class period course constraints. '),'default',array('class'=>'error-box error-message'));  
		         break;
		        case empty($this->request->data['InstructorClassPeriodCourseConstraint']['semester']) :
		         	$this->Session->setFlash('<span></span> '.__('Please select the academic year that you want to add instructor class period course constraints. '),'default',array('class'=>'error-box error-message'));  
		         break; 					 
		         default:
		         $everythingfine=true;               
			}
			if ($everythingfine) {
				$selected_academicyear =$this->request->data['InstructorClassPeriodCourseConstraint']['academicyear'];
				$this->Session->write('selected_academicyear',$selected_academicyear);
			    $selected_program =$this->request->data['InstructorClassPeriodCourseConstraint']['program_id'];
				$this->Session->write('selected_program',$selected_program);
				$selected_program_type = $this->request->data['InstructorClassPeriodCourseConstraint']['program_type_id'];
				$this->Session->write('selected_program_type',$selected_program_type);
				$selected_semester = $this->request->data['InstructorClassPeriodCourseConstraint']['semester'];
				$this->Session->write('selected_semester',$selected_semester);
				//$program_type_id=$this->AcademicYear->equivalent_program_type($selected_program_type);
				
				if($this->role_id == ROLE_COLLEGE){
					$selected_year_level =$this->request->data['InstructorClassPeriodCourseConstraint']['year_level_id'];
					$this->Session->write('selected_year_level',$selected_year_level);
					if(empty($selected_year_level) || ($selected_year_level =="All")) {
						$selected_year_level ='%';
					}
					$selected_department = $this->request->data['InstructorClassPeriodCourseConstraint']['department_id'];
					$this->Session->write('selected_department',$selected_department);
					if(!empty($selected_department)){
				
						$yearLevels = $this->InstructorClassPeriodCourseConstraint->College->PublishedCourse->YearLevel->find('list',array('conditions'=>array('YearLevel.department_id'=>$selected_department)));
						$conditions=array('PublishedCourse.academic_year'=>$selected_academicyear,
							'PublishedCourse.department_id'=>$selected_department,'PublishedCourse.program_id'=>
							$selected_program,'PublishedCourse.program_type_id'=>$selected_program_type,
							'PublishedCourse.year_level_id LIKE'=>$selected_year_level, 'PublishedCourse.semester'=>$selected_semester,'PublishedCourse.drop'=>0);				
				
					} else {
						$conditions=array('PublishedCourse.academic_year'=>$selected_academicyear,
							'PublishedCourse.college_id'=>$this->college_id, 
							'PublishedCourse.program_id'=>$selected_program,
							'PublishedCourse.program_type_id'=>$selected_program_type,
							'PublishedCourse.semester'=>$selected_semester,
							'PublishedCourse.drop'=>0,
							"OR"=>array("PublishedCourse.department_id is null",
							"PublishedCourse.department_id"=>array(0,'')));
					}
				}

		$publishedCourses_id_array = $this->InstructorClassPeriodCourseConstraint->College->PublishedCourse->find('list',array('fields'=>array('PublishedCourse.id'),'conditions'=>$conditions));
		
		$instructors = $this->InstructorClassPeriodCourseConstraint->Staff->CourseInstructorAssignment->find('all',array('conditions'=>array('CourseInstructorAssignment.published_course_id'=>$publishedCourses_id_array),'fields'=>array('DISTINCT CourseInstructorAssignment.staff_id'),'contain'=>array('Staff'=>array('fields'=>array('Staff.id', 'Staff.first_name','Staff.middle_name', 'Staff.last_name'), 'Title'=>array('fields'=>array('Title.title')),'Position'=>array('fields'=>array('Position.position'))))));

				if (empty($instructors)) {
			         $this->Session->setFlash('<span></span> '.__('There is no Instructor to add class period constraints in the selected criteria.'),'default',array('class'=>'info-box info-message'));     
			    } else {
					$week_days =$this->InstructorClassPeriodCourseConstraint->ClassPeriod->find('all',array('fields'=>array('DISTINCT ClassPeriod.week_day'),'conditions'=>array('ClassPeriod.college_id'=> $this->college_id, 'ClassPeriod.program_id'=>$selected_program,'ClassPeriod.program_type_id'=>$selected_program_type
					),'recursive'=>-1));

					$this->Session->write('instructors',$instructors);
					$this->Session->write('yearLevels',$yearLevels);
					$this->Session->write('week_days',$week_days);
					$this->set(compact('instructors','yearLevels','week_days')); 
				}
			}
        }
        if(!empty($this->request->data) && isset($this->request->data['submit'])) {
			if(!empty($this->request->data['InstructorClassPeriodCourseConstraint']['instructor'])){
				if(!empty($this->request->data['InstructorClassPeriodCourseConstraint']['week_day'])){
					$selected_week_day = $this->request->data['InstructorClassPeriodCourseConstraint']['week_day'];
					$this->Session->write('selected_week_day',$selected_week_day);
					$selected_class_period_id_array = array();
					if(!empty($this->request->data['InstructorClassPeriodCourseConstraint']['Selected'])){
						foreach($this->request->data['InstructorClassPeriodCourseConstraint']['Selected'] as $icpcck=>$icpccv){
							if($icpccv !=0){
								$selected_class_period_id_array[$icpccv] = $icpccv;
							}
						}
						if(count($selected_class_period_id_array) !=0){
							$isalready_record_this_constraints =0;
							$isalready_record_this_constraints = $this->InstructorClassPeriodCourseConstraint->find('count',array('conditions'=>array('InstructorClassPeriodCourseConstraint.staff_id'=>$this->request->data['InstructorClassPeriodCourseConstraint']['instructor'], 'InstructorClassPeriodCourseConstraint.class_period_id'=>$selected_class_period_id_array,'InstructorClassPeriodCourseConstraint.college_id'=>$this->college_id,'InstructorClassPeriodCourseConstraint.academic_year'=>$this->request->data['InstructorClassPeriodCourseConstraint']['academicyear'],'InstructorClassPeriodCourseConstraint.semester'=>$this->request->data['InstructorClassPeriodCourseConstraint']['semester'])));
							if($isalready_record_this_constraints == 0){
								$this->request->data['InstructorClassPeriodCourseConstraints']['staff_id']=
									$this->request->data['InstructorClassPeriodCourseConstraint']['instructor'];
								$this->request->data['InstructorClassPeriodCourseConstraints']['active']=
									$this->request->data['InstructorClassPeriodCourseConstraint']['active'];
								$this->request->data['InstructorClassPeriodCourseConstraints']['college_id'] = 
									$this->college_id;
								$this->request->data['InstructorClassPeriodCourseConstraints']['academic_year']=
									$this->request->data['InstructorClassPeriodCourseConstraint']['academicyear'];
								$this->request->data['InstructorClassPeriodCourseConstraints']['semester']=
									$this->request->data['InstructorClassPeriodCourseConstraint']['semester'];
								foreach($selected_class_period_id_array as $class_period_id){
									$this->request->data['InstructorClassPeriodCourseConstraints']['class_period_id']= $class_period_id;
									$this->InstructorClassPeriodCourseConstraint->create();
									$this->InstructorClassPeriodCourseConstraint->save($this->request->data['InstructorClassPeriodCourseConstraints']);
								}
								$this->Session->setFlash('<span></span>'.__('The instructor class period course constraint has been saved'),'default',array('class'=>'success-box success-message'));
							} else {
								$this->Session->setFlash('<span></span>'.__('The Instructor class period constraint for the one or more seclected period is already recored, Please exclude already recored periods.'),'default',array('class'=>'error-box error-message'));
							}
							
						} else {
							$this->Session->setFlash('<span></span>'.__(' Please select at least 1 period'),'default',array('class'=>'error-box error-message'));
						}
					} else {
						$this->Session->setFlash('<span></span> Please set class period first in ', 
							"session_flash_link", array(
							"class"=>'info-box info-message',
							"link_text" => " this page",
							"link_url" => array(
							"controller" => "classPeriods",
							"action" => "add",
							"admin" => false
							)
							));
					}
				} else {
					$this->Session->setFlash('<span></span>'.__(' Please select week day'),'default',
								array('class'=>'error-box error-message'));
				}
			} else {
				$this->Session->setFlash('<span></span>'.__(' Please select instructor'),'default',
							array('class'=>'error-box error-message'));
			}
			
			$instructors = $this->Session->read('instructors');
			$yearLevels = $this->Session->read('yearLevels');
			$week_days = $this->Session->read('week_days');
			$this->set(compact('instructors','yearLevels','week_days')); 
		}
		$instructors = $this->Session->read('instructors');
		$yearLevels = $this->Session->read('yearLevels');
		$week_days = $this->Session->read('week_days');
		$selected_week_day = null;
		if($this->Session->read('selected_week_day')){
			$selected_week_day = $this->Session->read('selected_week_day');
		}
		if($this->Session->read('selected_academicyear')){
			$selected_academicyear = $this->Session->read('selected_academicyear');
		} else {
			$selected_academicyear = $this->request->data['InstructorClassPeriodCourseConstraint']['academicyear'];
			$this->Session->write('selected_academicyear',$selected_academicyear);
		}
		if($this->Session->read('selected_program')){
			$selected_program = $this->Session->read('selected_program');
		} else {
			$selected_program = $this->request->data['InstructorClassPeriodCourseConstraint']['program_id'];
			$this->Session->write('selected_program',$selected_program);
		}
		if($this->Session->read('selected_program_type')){
			$selected_program_type = $this->Session->read('selected_program_type');
		} else {
			$selected_program_type = $this->request->data['InstructorClassPeriodCourseConstraint']['program_type_id'];
			$this->Session->write('selected_program_type',$selected_program_type);
		}
		if($this->Session->read('selected_semester')){
			$selected_semester = $this->Session->read('selected_semester');
		} else {
			$selected_semester = $this->request->data['InstructorClassPeriodCourseConstraint']['semester'];
			$this->Session->write('selected_semester',$selected_semester);
		}
		$fromadd_periods = $this->_get_periods_data($selected_week_day,$selected_program,$selected_program_type);
		if($this->role_id == ROLE_COLLEGE){
			if($this->Session->read('selected_year_level')){
				$selected_year_level =$this->Session->read('selected_year_level');
			} else {
				$selected_year_level = $this->request->data['InstructorClassPeriodCourseConstraint']['year_level_id'];
				$this->Session->write('selected_year_level',$selected_year_level);
			}
			if(empty($selected_year_level) || ($selected_year_level =="All")) {
				$selected_year_level ='%';
			}
			if($this->Session->read('selected_department')){
				$selected_department = $this->Session->read('selected_department');
			} else {
				$selected_department = $this->request->data['InstructorClassPeriodCourseConstraint']['department_id'];
				$this->Session->write('selected_department',$selected_department);
			}
		} 
		$conditions=array('InstructorClassPeriodCourseConstraint.academic_year'=>$selected_academicyear, 'InstructorClassPeriodCourseConstraint.college_id'=>$this->college_id, 'InstructorClassPeriodCourseConstraint.semester'=>$selected_semester);
		
		$instructorClassPeriodCourseConstraints = $this->InstructorClassPeriodCourseConstraint->find('all',array('conditions'=>$conditions, 'contain'=>array(
			'Staff'=>array('fields'=>array('Staff.id', 'Staff.first_name','Staff.middle_name', 'Staff.last_name'), 'Title'=>array('fields'=>array('Title.title')),'Position'=>array('fields'=>array('Position.position'))),'ClassPeriod'=>array('fields'=>array('ClassPeriod.week_day','ClassPeriod.id','ClassPeriod.period_setting_id'),'PeriodSetting'=>array('fields'=>array('PeriodSetting.period','PeriodSetting.hour'))))));

		$this->set(compact('yearLevels','instructorClassPeriodCourseConstraints',
			'selected_academicyear','selected_program','selected_program_type','selected_semester',
			'selected_department','selected_year_level','fromadd_periods','selected_week_day'));
		if($this->Session->read('from_delete')){

			$fromadd_periods = $this->_get_periods_data($selected_week_day,$selected_program, $selected_program_type);
			$this->set(compact('instructors','week_days','fromadd_periods','selected_week_day'));
			$this->Session->delete('from_delete');
		}

	}

	function edit($id = null) {
		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__('Invalid instructor class period course constraint'));
			return $this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->InstructorClassPeriodCourseConstraint->save($this->request->data)) {
				$this->Session->setFlash(__('The instructor class period course constraint has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The instructor class period course constraint could not be saved. Please, try again.'));
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->InstructorClassPeriodCourseConstraint->read(null, $id);
		}
		$staffs = $this->InstructorClassPeriodCourseConstraint->Staff->find('list');
		$classPeriods = $this->InstructorClassPeriodCourseConstraint->ClassPeriod->find('list');
		$colleges = $this->InstructorClassPeriodCourseConstraint->College->find('list');
		$this->set(compact('staffs', 'classPeriods', 'colleges'));
	}

	function delete($id = null,$from=null) {
		if (!$id) {
			$this->Session->setFlash('<span></span> '.__('Invalid id for instructor class period course constraint.'),'default',array('class'=>'error-box error-message')); 
			if(empty($from)){
				return $this->redirect(array('action'=>'index'));
			} else {
				return $this->redirect(array('action'=>'add'));
			}
		}

		$from_delete = 1;
		$this->Session->write('from_delete',$from_delete);
		$areyou_eligible_to_delete = $this->InstructorClassPeriodCourseConstraint->beforeDeleteCheckEligibility($id,$this->college_id);
		if($areyou_eligible_to_delete == true){
			if ($this->InstructorClassPeriodCourseConstraint->delete($id)) {
				$this->Session->setFlash('<span></span> '.__('Instructor class period course constraint deleted'),'default',array('class'=>'success-box success-message'));  
				if(empty($from)){
					return $this->redirect(array('action'=>'index'));
				} else {
					return $this->redirect(array('action'=>'add'));
				}
			}
			$this->Session->setFlash('<span></span> '.__('Instructor class period course constraint was not deleted.'),'default',array('class'=>'error-box error-message')); 
			if(empty($from)){
				return $this->redirect(array('action'=>'index'));
			} else {
				return $this->redirect(array('action'=>'add'));
			}
		} else {
			$this->Session->setFlash('<span></span> '.__('You are not eligible to delete this Instructor Class period course constraint.'),'default',array('class'=>'error-box error-message')); 
			if(empty($from)){
				return $this->redirect(array('action'=>'index'));
			} else {
				return $this->redirect(array('action'=>'add'));
			}
		}
	}
	function get_year_level($department_id=null){
		if(!empty($department_id)){
			$this->layout = 'ajax';
			$yearLevels = $this->InstructorClassPeriodCourseConstraint->College->PublishedCourse->YearLevel->find('list',array('conditions'=>array('YearLevel.department_id'=>$department_id)));
			$this->set(compact('yearLevels'));	
		}
	}
	function get_periods($week_id=null){
		$selected_program = $this->Session->read('selected_program');
		$selected_program_type = $this->Session->read('selected_program_type');
		if(!empty($week_id)){
			$this->layout = 'ajax';
			$periods = $this->_get_periods_data($week_id,$selected_program,$selected_program_type);

			$this->set(compact('periods'));	
		}
	}
	function _get_periods_data($week_id=null,$program_id=null,$program_type_id=null){
		if($week_id){
			$periods_data = $this->InstructorClassPeriodCourseConstraint->ClassPeriod->find('all',array('fields'=>array('ClassPeriod.id','ClassPeriod.period_setting_id','ClassPeriod.week_day'), 'conditions'=>array('ClassPeriod.week_day'=>$week_id,'ClassPeriod.program_id'=>$program_id, 'ClassPeriod.program_type_id'=>$program_type_id,'ClassPeriod.college_id'=>$this->college_id),'contain'=>array('PeriodSetting'=>array('fields'=>	array('PeriodSetting.id','PeriodSetting.period','PeriodSetting.hour')))));
			return $periods_data;
		}
	}
}
