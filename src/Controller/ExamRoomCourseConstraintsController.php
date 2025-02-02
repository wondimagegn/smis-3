<?php
class ExamRoomCourseConstraintsController extends AppController {

	var $name = 'ExamRoomCourseConstraints';
	var $components =array('AcademicYear');
	
	var $menuOptions = array(
		'exclude' => array('*'),
		'controllerButton' => false,
	);
	function beforeFilter(){
        parent::beforeFilter();
         //$this->Auth->allow(array('*'));
         $this->Auth->allow('get_year_level','get_class_rooms');  
    }
	function beforeRender() {

        $acyear_array_data = $this->AcademicYear->acyear_array();
        //To diplay current academic year as default in drop down list
        $defaultacademicyear=$this->AcademicYear->current_academicyear();

        $this->set(compact('acyear_array_data','defaultacademicyear'));
        unset($this->request->data['User']['password']);
	}

	function index() {
		$this->ExamRoomCourseConstraint->recursive = 0;
		$this->set('examRoomCourseConstraints', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid exam room course constraint'));
			return $this->redirect(array('action' => 'index'));
		}
		$this->set('examRoomCourseConstraint', $this->ExamRoomCourseConstraint->read(null, $id));
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
			if($this->Session->read('selected_class_room_block')){
				$this->Session->delete('selected_class_room_block');
			} 
			if($this->Session->read('classRooms')){
				$this->Session->delete('classRooms');
			} 
			if($this->Session->read('selected_class_rooms')){
				$this->Session->delete('selected_class_rooms');
			} 
		}
		$programs = $this->ExamRoomCourseConstraint->PublishedCourse->Program->find('list');
        $programTypes = $this->ExamRoomCourseConstraint->PublishedCourse->ProgramType->find('list');
		$departments = $this->ExamRoomCourseConstraint->PublishedCourse->Department->find('list',array('conditions'=>array('Department.college_id'=>$this->college_id)));
		$yearLevels = null;
		$yearLevels = $this->ExamRoomCourseConstraint->PublishedCourse->YearLevel->find('list',array('conditions'=> array('YearLevel.department_id'=>$this->department_id)));
        $this->set(compact('programs','programTypes','departments','yearLevels'));
        if(!empty($this->request->data) && isset($this->request->data['search'])) {
        	if($this->Session->read('sections_array')){
				$this->Session->delete('sections_array');
			}

        	$everythingfine=false;
			switch($this->request->data) {
		        case empty($this->request->data['ExamRoomCourseConstraint']['academicyear']) :
		        	 $this->Session->setFlash('<span></span> '.__('Please select the academic year of the publish courses that you want to add exam room course constraints.'),'default',array('class'=>'error-box error-message'));  
		         break; 
		        case empty($this->request->data['ExamRoomCourseConstraint']['program_id']) :
		         	$this->Session->setFlash('<span></span> '.__('Please select the program of the publish courses that you want to add exam room course constraints. '),'default',array('class'=>'error-box error-message'));  
		         break;    
		        case empty($this->request->data['ExamRoomCourseConstraint']['program_type_id']) :
		         	$this->Session->setFlash('<span></span> '.__('Please select the program type of the publish courses that you want to add exam room course constraints. '),'default',array('class'=>'error-box error-message'));  
		         break;
		        case empty($this->request->data['ExamRoomCourseConstraint']['semester']) :
		         	$this->Session->setFlash('<span></span> '.__('Please select the semester of the publish courses that you want to add exam room course constraints. '),'default',array('class'=>'error-box error-message'));  
		         break; 					 
		         default:
		         $everythingfine=true;               
			}
			if ($everythingfine) {
				$selected_academicyear =$this->request->data['ExamRoomCourseConstraint']['academicyear'];
				$this->Session->write('selected_academicyear',$selected_academicyear);
			    $selected_program =$this->request->data['ExamRoomCourseConstraint']['program_id'];
				$this->Session->write('selected_program',$selected_program);
				$selected_program_type = $this->request->data['ExamRoomCourseConstraint']['program_type_id'];
				$this->Session->write('selected_program_type',$selected_program_type);
				$selected_semester = $this->request->data['ExamRoomCourseConstraint']['semester'];
				$this->Session->write('selected_semester',$selected_semester);
				//$program_type_id=$this->AcademicYear->equivalent_program_type($selected_program_type);
				
				if($this->role_id == ROLE_COLLEGE){
					$selected_year_level =$this->request->data['ExamRoomCourseConstraint']['year_level_id'];
					$this->Session->write('selected_year_level',$selected_year_level);
					if(empty($selected_year_level) || ($selected_year_level =="All")) {
						$selected_year_level ='%';
					}
					$selected_department = $this->request->data['ExamRoomCourseConstraint']['department_id'];
					$this->Session->write('selected_department',$selected_department);
					if(!empty($selected_department)){
				
						$yearLevels = $this->ExamRoomCourseConstraint->PublishedCourse->YearLevel->find('list',array('conditions'=>array('YearLevel.department_id'=>$selected_department)));
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
				$publishedcourses = $this->ExamRoomCourseConstraint->PublishedCourse->find('all',array(
					'conditions'=>$conditions,'fields'=>array('PublishedCourse.id','PublishedCourse.section_id'),
					'contain'=>array('Section'=>array('fields'=>array('Section.id','Section.name')),'Course'=>array(
					'fields'=>array('Course.id','Course.course_title','Course.course_code','Course.credit',
					'Course.lecture_hours','Course.tutorial_hours','Course.laboratory_hours')))
				));

				$sections_array = array();
				foreach($publishedcourses as $key=>$publishedcourse) {
					$sections_array[$publishedcourse['Section']['name']][$key]['course_title'] =$publishedcourse['Course']['course_title'];
					$sections_array[$publishedcourse['Section']['name']][$key]['course_id'] = $publishedcourse
						['Course']['id'];
					$sections_array[$publishedcourse['Section']['name']][$key]['course_code'] = $publishedcourse
						['Course']['course_code'];
					$sections_array[$publishedcourse['Section']['name']][$key]['credit'] = $publishedcourse
						['Course']['credit'];
					$sections_array[$publishedcourse['Section']['name']][$key]['credit_detail'] = $publishedcourse
						['Course']['lecture_hours'].' '.$publishedcourse['Course']['tutorial_hours'].' '.
						$publishedcourse['Course']['laboratory_hours'];
					$sections_array[$publishedcourse['Section']['name']][$key]['section_id'] = 
						$publishedcourse['PublishedCourse']['section_id'];
					$sections_array[$publishedcourse['Section']['name']][$key]['published_course_id'] = 
						$publishedcourse['PublishedCourse']['id'];
				}

				if (empty($sections_array)) {
			         $this->Session->setFlash('<span></span> '.__('There is no course to assign exam room constraints in the selected criteria.'),'default',array('class'=>'info-box info-message'));     
			    } else {
					/*
					$classRoomBlocks_ids = $this->ExamRoomCourseConstraint->ClassRoom->ClassRoomBlock->find('list',array('conditions'=>array('ClassRoomBlock.college_id'=>$this->college_id),'fields'=>array('ClassRoomBlock.id')));

					$classRooms = $this->ExamRoomCourseConstraint->ClassRoom->find('all', array('conditions'=>array('ClassRoom.class_room_block_id'=>$classRoomBlocks_ids, 'ClassRoom.available_for_exam'=>1), 'fields'=>array('ClassRoom.id','ClassRoom.room_code', 'ClassRoom.class_room_block_id'),'contain'=>array('ProgramProgramTypeClassRoom'=>array('Program'=>array('fields'=>array('Program.name')),'ProgramType'=>array('fields'=>array('ProgramType.name'))), 'ClassRoomBlock'=>array('fields'=>array('ClassRoomBlock.id','ClassRoomBlock.block_code'), 'Campus'=>array('fields'=>array('Campus.name'))))));
					//formatted class rooms by class room blocks and campus
					$formated_classRooms = array();
					foreach($classRooms as $crk=>$crv){
						$formated_classRooms[$crv['ClassRoomBlock']['Campus']['name'].'-Block:'.$crv['ClassRoomBlock']['block_code']][$crv['ClassRoom']['id']] = $crv['ClassRoom'];
						if(!empty($crv['ProgramProgramTypeClassRoom'])){
							foreach($crv['ProgramProgramTypeClassRoom'] as $ppk=>$ppv){
								$formated_classRooms[$crv['ClassRoomBlock']['Campus']['name'].'-Block:'.$crv['ClassRoomBlock']['block_code']][$crv['ClassRoom']['id']][$ppv['Program']['name']][]=$ppv['ProgramType']['name'];
							}
						} 
					}	
					*/
					$undergraduate_program_name = $this->ExamRoomCourseConstraint->PublishedCourse->Program->field('Program.name',array('Program.id'=>1));
					$postgraduate_program_name = $this->ExamRoomCourseConstraint->PublishedCourse->ProgramType->field('ProgramType.name',array('ProgramType.id'=>2));	
					$this->Session->write('sections_array',$sections_array);
					$this->Session->write('yearLevels',$yearLevels);
					//$this->Session->write('formated_classRooms',$formated_classRooms);
					$this->Session->write('undergraduate_program_name',$undergraduate_program_name);
					$this->Session->write('postgraduate_program_name',$postgraduate_program_name);
					$this->set(compact('sections_array','yearLevels','formated_classRooms', 'undergraduate_program_name','postgraduate_program_name'));
				}
			}
        }
        if(!empty($this->request->data) && isset($this->request->data['submit'])) {
			if(!empty($this->request->data['ExamRoomCourseConstraint']['courses'])){
				if(!empty($this->request->data['ExamRoomCourseConstraint']['class_rooms'])){
					$isalready_record_this_constraints =0;
					$isalready_record_this_constraints = $this->ExamRoomCourseConstraint->find('count',array('conditions'=>array('ExamRoomCourseConstraint.published_course_id'=>$this->request->data['ExamRoomCourseConstraint']['courses'],'ExamRoomCourseConstraint.class_room_id'=>$this->request->data['ExamRoomCourseConstraint']['class_rooms'])));
					if($isalready_record_this_constraints == 0){
						$this->request->data['ExamRoomCourseConstraints']['published_course_id']=
							$this->request->data['ExamRoomCourseConstraint']['courses'];
						$this->request->data['ExamRoomCourseConstraints']['class_room_id']= 
							$this->request->data['ExamRoomCourseConstraint']['class_rooms'];
						$this->request->data['ExamRoomCourseConstraints']['active']=
							$this->request->data['ExamRoomCourseConstraint']['active'];
						
						$this->ExamRoomCourseConstraint->create();
						if ($this->ExamRoomCourseConstraint->save($this->request->data['ExamRoomCourseConstraints'])) {
							$this->Session->setFlash('<span></span>'.__('The exam room course constraint has been saved'),'default',array('class'=>'success-box success-message'));
						} else {
							$this->Session->setFlash('<span></span>'.__('The exam room course constraint could not be saved. Please, try again.'),'default',array('class'=>'error-box error-message'));
						}
					
					} else {
						$this->Session->setFlash('<span></span>'.__('The exam room constraint for the seclected course and class room is already recored.'),'default',array('class'=>'error-box error-message'));
					}

				} else {
					$this->Session->setFlash('<span></span>'.__(' Please select exam rooms'),'default',array('class'=>'error-box error-message'));
				}
			} else {
				$this->Session->setFlash('<span></span>'.__(' Please select course'),'default',
							array('class'=>'error-box error-message'));
			}
			$selected_class_room_block = $this->request->data['ExamRoomCourseConstraint']['class_room_blocks'];
			$classRooms = $this->ExamRoomCourseConstraint->ClassRoom->find('list', array('fields'=>array('ClassRoom.room_code'),'conditions'=>array('ClassRoom.class_room_block_id'=>$selected_class_room_block,'ClassRoom.available_for_exam'=>1)));
			$selected_class_rooms = $this->request->data['ExamRoomCourseConstraint']['class_rooms'];
			$this->Session->write('classRooms',$classRooms);
			$this->Session->write('selected_class_room_block',$selected_class_room_block);
			$this->Session->write('selected_class_rooms',$selected_class_rooms);
			
			$sections_array = $this->Session->read('sections_array');
			$yearLevels = $this->Session->read('yearLevels');
			//$formated_classRooms = $this->Session->read('formated_classRooms');
			$undergraduate_program_name = $this->Session->read('undergraduate_program_name');
			$postgraduate_program_name = $this->Session->read('postgraduate_program_name');
			$this->set(compact('sections_array','yearLevels', 'undergraduate_program_name', 'postgraduate_program_name','selected_class_room_block','classRooms', 'selected_class_room_block'));
		}
		$sections_array = $this->Session->read('sections_array');
		$yearLevels = $this->Session->read('yearLevels');
		$formated_classRooms = $this->Session->read('formated_classRooms');
		$undergraduate_program_name = $this->Session->read('undergraduate_program_name');
		$postgraduate_program_name = $this->Session->read('postgraduate_program_name');
		if($this->Session->read('selected_academicyear')){
			$selected_academicyear = $this->Session->read('selected_academicyear');
		} else {
			$selected_academicyear = $this->request->data['ExamRoomCourseConstraint']['academicyear'];
			$this->Session->write('selected_academicyear',$selected_academicyear);
		}
		if($this->Session->read('selected_program')){
			$selected_program = $this->Session->read('selected_program');
		} else {
			$selected_program = $this->request->data['ExamRoomCourseConstraint']['program_id'];
			$this->Session->write('selected_program',$selected_program);
		}
		if($this->Session->read('selected_program_type')){
			$selected_program_type = $this->Session->read('selected_program_type');
		} else {
			$selected_program_type = $this->request->data['ExamRoomCourseConstraint']['program_type_id'];
			$this->Session->write('selected_program_type',$selected_program_type);
		}
		if($this->Session->read('selected_semester')){
			$selected_semester = $this->Session->read('selected_semester');
		} else {
			$selected_semester = $this->request->data['ExamRoomCourseConstraint']['semester'];
			$this->Session->write('selected_semester',$selected_semester);
		}
		if($this->role_id == ROLE_COLLEGE){
			if($this->Session->read('selected_year_level')){
				$selected_year_level =$this->Session->read('selected_year_level');
			} else {
				$selected_year_level = $this->request->data['ExamRoomCourseConstraint']['year_level_id'];
				$this->Session->write('selected_year_level',$selected_year_level);
			}
			if(empty($selected_year_level) || ($selected_year_level =="All")) {
				$selected_year_level ='%';
			}
			if($this->Session->read('selected_department')){
				$selected_department = $this->Session->read('selected_department');
			} else {
				$selected_department = $this->request->data['ExamRoomCourseConstraint']['department_id'];
				$this->Session->write('selected_department',$selected_department);
			}
			if(empty($selected_department)){
				$conditions=array('PublishedCourse.academic_year'=>$selected_academicyear, 'PublishedCourse.college_id'=>$this->college_id, 'PublishedCourse.program_id'=>$selected_program,'PublishedCourse.program_type_id'=>$selected_program_type, 'PublishedCourse.semester'=>$selected_semester,'PublishedCourse.drop'=>0,"OR"=>array("PublishedCourse.department_id is null","PublishedCourse.department_id"=>array(0,'')));
		   } else {
				$conditions=array('PublishedCourse.academic_year'=>
				$selected_academicyear,'PublishedCourse.department_id'=>$selected_department,
				'PublishedCourse.program_id'=>$selected_program,'PublishedCourse.program_type_id'=>
				$selected_program_type,'PublishedCourse.year_level_id LIKE'=>$selected_year_level,
				'PublishedCourse.semester'=>$selected_semester,'PublishedCourse.drop'=>0);
			}
		} 
		if($this->Session->read('selected_class_room_block')){
			$selected_class_room_block = $this->Session->read('selected_class_room_block');
		}   else if(isset($this->request->data['ExamRoomCourseConstraint']['class_room_blocks'])){
			$selected_class_room_block = $this->request->data['ExamRoomCourseConstraint']['class_room_blocks'];
			$this->Session->write('selected_class_room_block',$selected_class_room_block);
		}
		if($this->Session->read('selected_class_rooms')){
			$selected_class_rooms = $this->Session->read('selected_class_rooms');
		} else {
			//$selected_class_rooms = $this->request->data['ClassRoomClassPeriodConstraint']['class_room_id'];
		} 
		$classRoomBlocks = $this->ExamRoomCourseConstraint->ClassRoom->ClassRoomBlock->find('all',array('fields'=>array('ClassRoomBlock.id','ClassRoomBlock.block_code'),'conditions'=>array('ClassRoomBlock.college_id'=>$this->college_id), 'contain'=>array('Campus'=>array('fields'=>array('Campus.name')))));

		$formatted_class_room_blocks = array();
		if(!empty($classRoomBlocks)){
			foreach($classRoomBlocks as $classRoomBlock){
				$formatted_class_room_blocks[$classRoomBlock['Campus']['name']][$classRoomBlock['ClassRoomBlock']['id']] = $classRoomBlock['ClassRoomBlock']['block_code'];
			}
		}
		//debug($formatted_class_room_blocks);
		if($this->Session->read('classRooms')){
			$classRooms = $this->Session->read('classRooms');
		}  else if(!empty($selected_class_room_block)){
			$classRooms = $this->ExamRoomCourseConstraint->ClassRoom->find('list', array('fields'=>array('ClassRoom.room_code'),'conditions'=>array('ClassRoom.class_room_block_id'=>$selected_class_room_block,'ClassRoom.available_for_exam'=>1)));
		} else {
			$classRooms = null;
		}
		$publishedCourses_id_array = $this->ExamRoomCourseConstraint->PublishedCourse->find('list',array('fields'=>array('PublishedCourse.id'),'conditions'=>$conditions));

		$examRoomCourseConstraints = $this->ExamRoomCourseConstraint->find('all',array('conditions'=>array('ExamRoomCourseConstraint.published_course_id'=>$publishedCourses_id_array), 'contain'=>array('PublishedCourse'=>array('fields'=>array('PublishedCourse.id'),'Section'=>array('fields'=>array('Section.name')),'Course'=>array('fields'=>array('Course.id','Course.course_code_title'))), 'ClassRoom'=>array('fields'=>array('ClassRoom.id','ClassRoom.room_code', 'ClassRoom.class_room_block_id'),'ClassRoomBlock'=>array('fields'=>array('ClassRoomBlock.id', 'ClassRoomBlock.block_code'), 'Campus'=>array('fields'=>array('Campus.name')))))));

		$this->set(compact('examRoomCourseConstraints','yearLevels','selected_academicyear', 'selected_program', 'selected_program_type', 'selected_semester','selected_department', 'selected_year_level','formatted_class_room_blocks','classRooms','selected_class_rooms', 'selected_class_room_block'));
		if($this->Session->read('from_delete')){
			$this->set(compact('sections_array','yearLevels','formated_classRooms', 'undergraduate_program_name', 'postgraduate_program_name'));
			$this->Session->delete('from_delete');
		}
	}
	function edit($id = null) {
		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__('Invalid exam room course constraint'));
			return $this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->ExamRoomCourseConstraint->save($this->request->data)) {
				$this->Session->setFlash(__('The exam room course constraint has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The exam room course constraint could not be saved. Please, try again.'));
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->ExamRoomCourseConstraint->read(null, $id);
		}
		$classRooms = $this->ExamRoomCourseConstraint->ClassRoom->find('list');
		$publishedCourses = $this->ExamRoomCourseConstraint->PublishedCourse->find('list');
		$this->set(compact('classRooms', 'publishedCourses'));
	}

	function delete($id = null,$from=null) {
		if (!$id) {
			$this->Session->setFlash('<span></span> '.__('Invalid id for exam room course constraint.'),'default',array('class'=>'error-box error-message')); 
			if(empty($from)){
				return $this->redirect(array('action'=>'index'));
			} else {
				return $this->redirect(array('action'=>'add'));
			}
		}

		$from_delete = 1;
		$this->Session->write('from_delete',$from_delete);
		$areyou_eligible_to_delete = $this->ExamRoomCourseConstraint->beforeDeleteCheckEligibility($id,$this->college_id);
		if($areyou_eligible_to_delete == true){
			if ($this->ExamRoomCourseConstraint->delete($id)) {
				$this->Session->setFlash('<span></span> '.__('Exam room course constraint deleted'),'default',array('class'=>'success-box success-message'));  
				if(empty($from)){
					return $this->redirect(array('action'=>'index'));
				} else {
					return $this->redirect(array('action'=>'add'));
				}
			}
			$this->Session->setFlash('<span></span> '.__('Exam room course constraint was not deleted.'),'default',array('class'=>'error-box error-message')); 
			if(empty($from)){
				return $this->redirect(array('action'=>'index'));
			} else {
				return $this->redirect(array('action'=>'add'));
			}
		} else {
			$this->Session->setFlash('<span></span> '.__('You are not eligible to delete this exam room course constraint.'),'default',array('class'=>'error-box error-message')); 
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
			//debug($this->request->data);
			$yearLevels = $this->ExamRoomCourseConstraint->PublishedCourse->YearLevel->find('list',array('conditions'=>array('YearLevel.department_id'=>$department_id)));
			$this->set(compact('yearLevels'));	
		}
	}
	function get_class_rooms($class_room_block_id = null){
		if(!empty($class_room_block_id)){
			$this->layout ='ajax';
			$classRooms = $this->ExamRoomCourseConstraint->ClassRoom->find('list', array('fields'=>array('ClassRoom.room_code'),'conditions'=>array('ClassRoom.class_room_block_id'=>$class_room_block_id,'ClassRoom.available_for_exam'=>1)));
			$this->set(compact('classRooms'));
		}
	}
}
