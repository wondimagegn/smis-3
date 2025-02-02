<?php
class ExamPeriodsController extends AppController {

	var $name = 'ExamPeriods';
	var $components =array('AcademicYear');
	var $menuOptions = array(
             'parent' => 'examSchedule',
             'exclude' => array(),
             'alias' => array(
                    'index' =>'List Final Exams Periods',
					'add' =>'Add Final Exams Periods'
            )
	);
	public function beforeFilter(){
        parent::beforeFilter();
         //$this->Auth->allow(array('*'));
        // $this->Auth->allow('get_year_level');  
    }
	public function beforeRender() {

        $acyear_array_data = $this->AcademicYear->acyear_array();
        //To diplay current academic year as default in drop down list
        $defaultacademicyear=$this->AcademicYear->current_academicyear();

        $this->set(compact('acyear_array_data','defaultacademicyear'));
        unset($this->request->data['User']['password']);
	}
	public function index() {
		//$this->ExamPeriod->recursive = 0;
		$programs = $this->ExamPeriod->Program->find('list');
		$programTypes = $this->ExamPeriod->ProgramType->find('list');
		$yearLevels = $this->ExamPeriod->get_maximum_year_levels_of_college($this->college_id);
		$this->set(compact('programs', 'programTypes', 'yearLevels'));
		$options= array();
		$options[] = array('ExamPeriod.college_id'=>$this->college_id);
		if(isset($this->request->data['ExamPeriod']['academic_year']) && !empty($this->request->data['ExamPeriod']['academic_year'])){
			$options[] = array('ExamPeriod.academic_year'=>$this->request->data['ExamPeriod']['academic_year']);
		}
		if(isset($this->request->data['ExamPeriod']['semester']) && !empty($this->request->data['ExamPeriod']['semester'])){
			$options[] = array('ExamPeriod.semester'=>$this->request->data['ExamPeriod']['semester']);
		}
		if(isset($this->request->data['ExamPeriod']['program_id']) && !empty($this->request->data['ExamPeriod']['program_id'])){
			$options[] = array('ExamPeriod.program_id'=>$this->request->data['ExamPeriod']['program_id']);
		}
		if(isset($this->request->data['ExamPeriod']['program_type_id']) && !empty($this->request->data['ExamPeriod']['program_type_id'])){
			$options[] = array('ExamPeriod.program_type_id'=>$this->request->data['ExamPeriod']['program_type_id']);
		}
		if(isset($this->request->data['ExamPeriod']['year_level_id']) && !empty($this->request->data['ExamPeriod']['year_level_id'])){
			$options[] = array('ExamPeriod.year_level_id'=>$this->request->data['ExamPeriod']['year_level_id']);
		}
		if(isset($this->request->data['ExamPeriod']['start_date']) && !empty($this->request->data['ExamPeriod']['start_date'])){
			$options[] = array('ExamPeriod.start_date >=' => ($this->request->data['ExamPeriod']['start_date']['year'].'-'.$this->request->data['ExamPeriod']['start_date']['month'].'-'.$this->request->data['ExamPeriod']['start_date']['day']));
		}
		if(isset($this->request->data['ExamPeriod']['end_date']) && !empty($this->request->data['ExamPeriod']['end_date'])){
			$options[] = array('ExamPeriod.end_date <=' => ($this->request->data['ExamPeriod']['end_date']['year'].'-'.$this->request->data['ExamPeriod']['end_date']['month'].'-'.$this->request->data['ExamPeriod']['end_date']['day']));
		}
		$this->paginate = array('conditions'=>$options,'contain'=>array('College'=>array('fields'=>array('College.name')),'Program'=>array('fields'=>array('Program.name')),'ProgramType'=>array('fields'=>array('ProgramType.name')),'YearLevel'=>array('fields'=>array('YearLevel.name'))));

		$examPeriods = $this->paginate();

		if(empty($examPeriods)) {
			$this->Session->setFlash('<span></span> '.__(' There is no exam period in the selected criteria.'),'default',array('class'=>'info-box info-message')); 
		}
		
		$this->set(compact('examPeriods'));

	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid exam period'));
			return $this->redirect(array('action' => 'index'));
		}
		$this->set('examPeriod', $this->ExamPeriod->read(null, $id));
	}

	function add() {
		if (!empty($this->request->data)) {
			//debug($this->request->data);
			$everythingfine=false;
			$program_type_count = 0;
			$year_level_count = 0;
			if(!empty($this->request->data['ExamPeriod']['program_type_id'])){
				$program_type_count = count($this->request->data['ExamPeriod']['program_type_id']);
			}
			if(!empty($this->request->data['ExamPeriod']['year_level_id'])) {
				$year_level_count = count($this->request->data['ExamPeriod']['year_level_id']);
			}
			switch($this->request->data) {
				case empty($this->request->data['ExamPeriod']['academic_year']) :
			         $this->Session->setFlash('<span></span> '.__('Please provide final exam period academic year, it is required.'),'default',array('class'=>'error-box error-message'));  
			         break; 
				case empty($this->request->data['ExamPeriod']['semester']) :
			         $this->Session->setFlash('<span></span> '.__('Please provide final exam period semester, it is required.'),'default',array('class'=>'error-box error-message'));  
			         break; 
			    case empty($this->request->data['ExamPeriod']['program_id']) :
			         $this->Session->setFlash('<span></span> '.__('Please provide final exam period program, it is required.'),'default',array('class'=>'error-box error-message'));  
			         break; 
			    case empty($program_type_count) :
			         $this->Session->setFlash('<span></span> '.__('Please select at least one program type.'),'default',array('class'=>'error-box error-message'));  
			         break;  
			    case empty($year_level_count) :
			         $this->Session->setFlash('<span></span> '.__('Please select at least one year level.'),'default',array('class'=>'error-box error-message'));  
			         break; 
			         			   
			         default:
			         $everythingfine=true;          
				}

			if ($everythingfine) {
				$is_saved = false;
				if ($this->ExamPeriod->alreadyRecorded($this->request->data)) {
					foreach($this->request->data['ExamPeriod']['program_type_id'] as $ptk=>$ptv){
						$exam_periods = array();
						$index = 0;
						foreach($this->request->data['ExamPeriod']['year_level_id'] as $ylk=>$ylv){
							$exam_periods['ExamPeriod'][$index]['college_id'] = $this->request->data['ExamPeriod']['college_id'];
							$exam_periods['ExamPeriod'][$index]['program_id'] = $this->request->data['ExamPeriod']['program_id'];
							$exam_periods['ExamPeriod'][$index]['program_type_id'] = $ptv;
							$exam_periods['ExamPeriod'][$index]['academic_year'] = $this->request->data['ExamPeriod']['academic_year'];
							$exam_periods['ExamPeriod'][$index]['semester'] = $this->request->data['ExamPeriod']['semester'];
							$exam_periods['ExamPeriod'][$index]['year_level_id'] = $ylv;
							$exam_periods['ExamPeriod'][$index]['default_number_of_invigilator_per_exam'] = $this->request->data['ExamPeriod']['default_number_of_invigilator_per_exam'];
							$exam_periods['ExamPeriod'][$index]['start_date'] = $this->request->data['ExamPeriod']['start_date'];
							$exam_periods['ExamPeriod'][$index]['end_date'] = $this->request->data['ExamPeriod']['end_date'];
							$index = $index + 1;
						}
						if($this->ExamPeriod->saveAll($exam_periods['ExamPeriod'], array('validate'=>'first'))){
							$is_saved = true;
						} else {
							$error=$this->ExamPeriod->invalidFields();
							debug($error);
						}
					}
						if ($is_saved == true) {
							$this->Session->setFlash('<span></span>'.__('The exam period has been saved'),'default',array('class'=>'success-box success-message'));
							return $this->redirect(array('action' => 'index'));
						} else {
							$this->Session->setFlash('<span></span>'.__('The exam period could not be saved. Please, try again.'),'default',array('class'=>'error-box error-message'));
						}
			
				} else {
					 $error=$this->ExamPeriod->invalidFields();
					 $string='';
					 foreach ($error['already_recorded_exam_perid'] as $key => $value) {
					 	$string.=' '.$value.'<br/>';
					 }
		             if(isset($error['already_recorded_exam_perid'])){
						$this->Session->setFlash('<span></span>'.$string.', First you have to delete or edit this exam period in.',
							"session_flash_link", array(
							"class"=>'error-box error-message',
							"link_text" => " this page",
							"link_url" => array(
							"controller" => "examPeriods",
							"action" => "index",
							"admin" => false
							)
							));
					}
				}
			}
		}
		$programs = $this->ExamPeriod->Program->find('list');
		$programTypes = $this->ExamPeriod->ProgramType->find('list');
		$yearLevels = $this->ExamPeriod->get_maximum_year_levels_of_college($this->college_id);
		//$yearLevels = $this->ExamPeriod->YearLevel->find('list');
		$this->set(compact('programs', 'programTypes', 'yearLevels'));
	}

	function edit($id = null) {
		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash('<span></span>'.__('Invalid exam period'),'default',array('class'=>'error-box error-message'));
			return $this->redirect(array('action' => 'index'));
		}
		$areyou_eligible_to_delete = $this->ExamPeriod->beforeDeleteCheckEligibility($id,$this->college_id);
		if($areyou_eligible_to_delete == true){ 
			//ToDo: Before Editing exam period first check whether there is any exam schedule on this period or not. If there is exam schedule deny change to this exam period. promote to delete exam schedule before change of this exam period.
		
			if (!empty($this->request->data)) {
			 //before editing check whether there is exam excluded date and sessions data on out side edited exam period dates. if so, deny edition of this exam period and promote the user to delete exam excluded date and sessions data on out side edited exam period dates.
		
			$start_date = $this->request->data['ExamPeriod']['start_date']['year'].'-'.$this->request->data['ExamPeriod']['start_date']['month'].'-'.$this->request->data['ExamPeriod']['start_date']['day'];
			$end_date = $this->request->data['ExamPeriod']['end_date']['year'].'-'.$this->request->data['ExamPeriod']['end_date']['month'].'-'.$this->request->data['ExamPeriod']['end_date']['day'];
		
			$isAny_ExamExcludedDateAndSessions_out_side_edited_ExamPeriod = $this->ExamPeriod->ExamExcludedDateAndSession->count_ExamExcludedDateAndSessions_out_side_edited_ExamPeriod($this->request->data['ExamPeriod']['id'],$start_date,$end_date);
			if(empty($isAny_ExamExcludedDateAndSessions_out_side_edited_ExamPeriod)){
		
				if ($this->ExamPeriod->save($this->request->data)) {
					$this->Session->setFlash('<span></span>'.__('The exam period has been edited'),'default',array('class'=>'success-box success-message'));
					return $this->redirect(array('action' => 'index'));
				} else {
					$this->Session->setFlash('<span></span>'.__('The exam period could not be saved. Please, try again.'),array('class'=>'error-box error-message'));
				} 
			} else {
				$this->Session->setFlash('<span></span>'.__('Exam period was not edited because there is one or more exam excluded date and session record in previous exam period dates which is out of the range of edited exam period dates. Thus, first delete those exam excluded date and sessions records.'),'default',array('class'=>'error-box error-message'));
			}
			}
		} else {
			$this->Session->setFlash('<span></span> '.__('You are not eligible to delete this exam period.'),'default',array('class'=>'error-box error-message')); 
			return $this->redirect(array('action'=>'index'));
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->ExamPeriod->read(null, $id);
		}
		$colleges = $this->ExamPeriod->College->find('list');
		$programs = $this->ExamPeriod->Program->find('list');
		$programTypes = $this->ExamPeriod->ProgramType->find('list');
		$yearLevels = $this->ExamPeriod->YearLevel->find('list');
		$this->set(compact('colleges', 'programs', 'programTypes', 'yearLevels'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('<span></span>'.__('Invalid id for exam period'),'default',array('class'=>'error-box error-message'));
			return $this->redirect(array('action'=>'index'));
		}
		$areyou_eligible_to_delete = $this->ExamPeriod->beforeDeleteCheckEligibility($id,$this->college_id);
		if($areyou_eligible_to_delete == true){
			//ToDo: Before deleting exam period first check whether there is any exam schedule on this period or not. If there is exam schedule deny deletion of this exam period. promote to delete exam schedule before deletion of this exam period.
		
			//Done:before delete check whether there is exam excluded date and sessions data on this exam period. if so, deny deletion of this exam period and promote the user to delete exam excluded date and sessions data on this exam periods.
			$isAny_ExamExcludedDateAndSessions_in_this_ExamPeriod = $this->ExamPeriod->ExamExcludedDateAndSession->count_ExamExcludedDateAndSessions_in_this_ExamPeriod($id);
			if(empty($isAny_ExamExcludedDateAndSessions_in_this_ExamPeriod)){
				if ($this->ExamPeriod->delete($id)) {
					$this->Session->setFlash('<span></span>'.__('Exam period deleted'),'default',array('class'=>'success-box success-message'));
					return $this->redirect(array('action'=>'index'));
				}
				$this->Session->setFlash('<span></span>'.__('Exam period was not deleted'),'default',array('class'=>'error-box error-message'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('<span></span>'.__('Exam period was not deleted because there is one or more exam excluded date and session record in this exam period dates.First Delete Exam excluded date and sessions records that lie on this Exam period dates.'),'default',array('class'=>'error-box error-message'));
				return $this->redirect(array('action' => 'index'));
			}
		} else {
			$this->Session->setFlash('<span></span> '.__('You are not eligible to delete this exam period.'),'default',array('class'=>'error-box error-message')); 
			return $this->redirect(array('action'=>'index'));
		}
	}
}
