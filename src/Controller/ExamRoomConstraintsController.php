<?php
class ExamRoomConstraintsController extends AppController {

	var $name = 'ExamRoomConstraints';
	var $components =array('AcademicYear');
	
	var $menuOptions = array(
		'exclude' => array('*'),
		'controllerButton' => false,
	);
	function beforeFilter(){
        parent::beforeFilter();
         //$this->Auth->allow(array('*'));
        $this->Auth->allow('get_class_rooms','get_exam_period_and_already_recorded_data');  
    }
	function beforeRender() {

        $acyear_array_data = $this->AcademicYear->acyear_array();
        //To diplay current academic year as default in drop down list
        $defaultacademicyear=$this->AcademicYear->current_academicyear();

        $this->set(compact('acyear_array_data','defaultacademicyear'));
        unset($this->request->data['User']['password']);
	}


	function index() {
		$this->ExamRoomConstraint->recursive = 0;
		$this->set('examRoomConstraints', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid exam room constraint'));
			return $this->redirect(array('action' => 'index'));
		}
		$this->set('examRoomConstraint', $this->ExamRoomConstraint->read(null, $id));
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
			if($this->Session->read('selected_class_room_block')){
				$this->Session->delete('selected_class_room_block');
			} 
			if($this->Session->read('selected_class_room')){
				$this->Session->delete('selected_class_room');
			}

		}
		if (!empty($this->request->data) && isset($this->request->data['submit'])) {

			$selected_academicyear =$this->request->data['ExamRoomConstraint']['academicyear'];
			$this->Session->write('selected_academicyear',$selected_academicyear);
			$selected_semester = $this->request->data['ExamRoomConstraint']['semester'];
			$this->Session->write('selected_semester',$selected_semester);
			$selected_class_room = $this->request->data['ExamRoomConstraint']['class_room_id'];
			$this->Session->write('selected_class_room',$selected_class_room);
			$selected_class_room_block = $this->request->data['ExamRoomConstraint']['class_room_blocks'];
			$this->Session->write('selected_class_room_block',$selected_class_room_block);
			
			$date_array = $this->_get_exam_periods($this->request->data['ExamRoomConstraint']['academicyear'], $this->request->data['ExamRoomConstraint']['semester']);
						
			$selected_sessions_array = array();
			if(!empty($this->request->data['ExamRoomConstraint']['Selected'])){
				foreach($this->request->data['ExamRoomConstraint']['Selected'] as $ercsk=>$ercsv){
					if($ercsv != '0'){
						$explode_data = explode("-",$ercsv);
						$selected_sessions_array[$date_array[$explode_data[0]]][] = $explode_data[1];
					}
				}			
			}
			$this->request->data['ExamRoomConstraints']['class_room_id'] = $selected_class_room;
			$this->request->data['ExamRoomConstraints']['academic_year'] = $selected_academicyear;
			$this->request->data['ExamRoomConstraints']['semester'] = $selected_semester;
			$count_selected_sessions= count($selected_sessions_array);
			if($count_selected_sessions !=0){
				$issave = false;
				foreach($selected_sessions_array as $date_key =>$date_value){
					$this->request->data['ExamRoomConstraints']['exam_date'] = $date_key;
					//active=0 => the class room occupied
					$this->request->data['ExamRoomConstraints']['active'] = 0;
					foreach($date_value as $session_key => $session_value){
						$this->request->data['ExamRoomConstraints']['session'] = $session_value;
						$this->ExamRoomConstraint->create();
						if ($this->ExamRoomConstraint->save($this->request->data['ExamRoomConstraints'])) {
							$issave = true;
						}
					}
				}
				if ($issave == true) {
					$this->Session->setFlash('<span></span>'.__('The exam room constraint has been saved.'),'default',array('class'=>'success-box success-message'));
					//$this->redirect(array('action' => 'index'));
				} else {
					$this->Session->setFlash('<span></span>'.__('The exam room constraint could not be saved. Please, try again.'),'default',array('class'=>'error-box error-message'));
				}
			} else {
				$this->Session->setFlash('<span></span>'.__('Please check at least 1 session.'),'default',array('class'=>'error-box error-message'));
			}

		}
		$classRoomBlocks = $this->ExamRoomConstraint->ClassRoom->ClassRoomBlock->find('all',array('fields'=>array('ClassRoomBlock.id','ClassRoomBlock.block_code'),'conditions'=>array('ClassRoomBlock.college_id'=>$this->college_id), 'contain'=>array('Campus'=>array('fields'=>array('Campus.name')))));
		//format class room blocks by campus
		$formatted_class_room_blocks = array();
		if(!empty($classRoomBlocks)){
			foreach($classRoomBlocks as $classRoomBlock){
				$formatted_class_room_blocks[$classRoomBlock['Campus']['name']][$classRoomBlock['ClassRoomBlock']['id']] = $classRoomBlock['ClassRoomBlock']['block_code'];
			}
		}
		if($this->Session->read('selected_academicyear')){
			$selected_academicyear = $this->Session->read('selected_academicyear');
		} else {
			$selected_academicyear = $this->request->data['ExamRoomConstraint']['academicyear'];
		}
		if($this->Session->read('selected_semester')){
			$selected_semester = $this->Session->read('selected_semester');
		} else {
			$selected_semester = $this->request->data['ExamRoomConstraint']['semester'];
		}
		if($this->Session->read('selected_class_room_block')){
			$selected_class_room_block = $this->Session->read('selected_class_room_block');
		}   else {
			$selected_class_room_block = $this->request->data['ExamRoomConstraint']['class_room_blocks'];
		}
		if($this->Session->read('selected_class_room')){
			$selected_class_room = $this->Session->read('selected_class_room');
		} else {
			//$selected_class_room = $this->request->data['ExamRoomConstraint']['class_room_id'];
		}
		 if(!empty($selected_class_room_block)){
				$classRooms = $this->ExamRoomConstraint->ClassRoom->find('list', array('fields'=>array('ClassRoom.room_code'),'conditions'=>array('ClassRoom.class_room_block_id'=>$selected_class_room_block,'ClassRoom.available_for_exam'=>1)));
		} else {
			$classRooms = null;
		}

		$data_array=null;
		$already_recorded_exam_room_constraints_by_date = null;

		if(!empty($selected_class_room) &&!empty($selected_academicyear) && !empty($selected_semester)){
			$date_array = $this->_get_exam_periods($selected_academicyear, $selected_semester);
			$already_recorded_exam_room_constraints_by_date = $this->ExamRoomConstraint-> get_already_recorded_exam_room_constraint($selected_class_room);
		}

		$this->set(compact('formatted_class_room_blocks','classRooms', 'selected_academicyear', 'selected_semester','selected_class_room_block','selected_class_room','date_array', 'already_recorded_exam_room_constraints_by_date'));
		//If it come from delete
		if($this->Session->read('from_delete')){
			$this->Session->delete('from_delete');
		}
	}

	function edit($id = null) {
		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash(__('Invalid exam room constraint'));
			return $this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->ExamRoomConstraint->save($this->request->data)) {
				$this->Session->setFlash(__('The exam room constraint has been saved'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The exam room constraint could not be saved. Please, try again.'));
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->ExamRoomConstraint->read(null, $id);
		}
		$classRooms = $this->ExamRoomConstraint->ClassRoom->find('list');
		$this->set(compact('classRooms'));
	}

	function delete($id = null,$from=null) {
		if (!$id) {
			$this->Session->setFlash('<span></span> '.__('Invalid id for exam room constraint.'), 'default',array('class'=>'error-box error-message')); 
			if(empty($from)){
				return $this->redirect(array('action'=>'index'));
			} else {
				return $this->redirect(array('action'=>'add'));
			}
		}
		$from_delete = 1;
		$this->Session->write('from_delete',$from_delete);
		$deleted_exam_room_constraint_data = $this->ExamRoomConstraint->find('first',array('conditions'=>array('ExamRoomConstraint.id'=>$id)));
			$this->Session->write('selected_academicyear',$deleted_exam_room_constraint_data['ExamRoomConstraint']['academic_year']);
			$this->Session->write('selected_semester',$deleted_exam_room_constraint_data['ExamRoomConstraint']['semester']);
			$this->Session->write('selected_class_room',$deleted_exam_room_constraint_data['ExamRoomConstraint']['class_room_id']);

			$class_room_block_id = $this->ExamRoomConstraint->ClassRoom->field('ClassRoom.class_room_block_id',array('ClassRoom.id'=>$deleted_exam_room_constraint_data['ExamRoomConstraint']['class_room_id']));
			$this->Session->write('selected_class_room_block',$class_room_block_id);
		if ($this->ExamRoomConstraint->delete($id)) {
			$this->Session->setFlash('<span></span> '.__('Exam room constraint deleted'), 'default',array('class'=>'success-box success-message'));  
				if(empty($from)){
					return $this->redirect(array('action'=>'index'));
				} else {
					return $this->redirect(array('action'=>'add'));
				}
		}
		$this->Session->setFlash('<span></span> '.__('Exam room constraint was not deleted.'), 'default',array('class'=>'error-box error-message')); 
			if(empty($from)){
				return $this->redirect(array('action'=>'index'));
			} else {
				return $this->redirect(array('action'=>'add'));
			}
	}
	
	function get_class_rooms($class_room_block_id = null){
		if(!empty($class_room_block_id)){

			$this->layout ='ajax';
			$classRooms = $this->ExamRoomConstraint->ClassRoom->find('list', array('fields'=>array('ClassRoom.room_code'),'conditions'=>array('ClassRoom.class_room_block_id'=>$class_room_block_id, 'ClassRoom.available_for_exam'=>1)));
			$this->set(compact('classRooms'));
		}
	}
	
	function get_exam_period_and_already_recorded_data($data=null){
		
		$this->layout ='ajax';
		$explode_data = explode("~",$data);
		$class_room_id = $explode_data[0];
		$academicyear = $explode_data[1];
		$academicyear = str_replace('-','/',$academicyear);
		$semester = $explode_data[2];

		if(!empty($class_room_id) && !empty($academicyear) && !empty($semester)){
			$date_array = $this->_get_exam_periods($academicyear, $semester);
			$already_recorded_exam_room_constraints_by_date = $this->ExamRoomConstraint-> get_already_recorded_exam_room_constraint($class_room_id);
			$this->set(compact('date_array','already_recorded_exam_room_constraints_by_date'));
		}
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
