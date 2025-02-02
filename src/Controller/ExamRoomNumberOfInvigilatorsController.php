<?php
class ExamRoomNumberOfInvigilatorsController extends AppController {

	var $name = 'ExamRoomNumberOfInvigilators';
	var $components =array('AcademicYear');
	var $menuOptions = array(
             'parent' => 'examSchedule',
             'exclude' => array('get_class_rooms','get_already_recorded_exam_room_number_of_invigilator', 'index'),
             'alias' => array(
                    'index' =>'View Exam Room Number of Invigilator',
					'add'=>'Add Exam Room Number of Invigilator'
            )
	);
	public $paginate=array();
	function beforeFilter(){
        parent::beforeFilter();
         //$this->Auth->allow(array('*'));
        $this->Auth->allow('get_class_rooms','get_already_recorded_exam_room_number_of_invigilator');  
    }
	function beforeRender() {

        $acyear_array_data = $this->AcademicYear->acyear_array();
        //To diplay current academic year as default in drop down list
        $defaultacademicyear=$this->AcademicYear->current_academicyear();

        $this->set(compact('acyear_array_data','defaultacademicyear'));
        unset($this->request->data['User']['password']);
	}
	public function index() {
		$options=array();
		$this->ExamRoomNumberOfInvigilator->recursive = 0;

		if(!empty($this->request->data['search'])){
		    if(!empty($this->request->data['ExamRoomNumberOfInvigilator']['academicyear'])){
		    	$options['conditions']['ExamRoomNumberOfInvigilator.academic_year']=$this->request->data['ExamRoomNumberOfInvigilator']['academicyear'];
		    }
		    if(!empty($this->request->data['ExamRoomNumberOfInvigilator']['semester'])){
		    	$options['conditions']['ExamRoomNumberOfInvigilator.semester']=$this->request->data['ExamRoomNumberOfInvigilator']['semester'];
		    }
		    if(!empty($this->request->data['ExamRoomNumberOfInvigilator']['class_room_id'])){
		    	$options['conditions']['ExamRoomNumberOfInvigilator.class_room_id']=$this->request->data['ExamRoomNumberOfInvigilator']['class_room_id'];
		    }
			
		}
        if(isset($options['conditions']) && !empty($options['conditions'])){
        	$this->paginate['conditions'] =$options['conditions'];
        }
		
		
		$classRoomBlocks = $this->ExamRoomNumberOfInvigilator->ClassRoom->ClassRoomBlock->find('all',array('fields'=>array('ClassRoomBlock.id','ClassRoomBlock.block_code'),'conditions'=>array('ClassRoomBlock.college_id'=>$this->college_id), 'contain'=>array('Campus'=>array('fields'=>array('Campus.name')))));
		//format class room blocks by campus
		$formatted_class_room_blocks = array();
		if(!empty($classRoomBlocks)){
			foreach($classRoomBlocks as $classRoomBlock){
				$formatted_class_room_blocks[$classRoomBlock['Campus']['name']][$classRoomBlock['ClassRoomBlock']['id']] = $classRoomBlock['ClassRoomBlock']['block_code'];
			}
		}
        if(!empty($this->request->data['ExamRoomNumberOfInvigilator']['class_room_blocks'])){
        	$selected_class_room_block=$this->request->data['ExamRoomNumberOfInvigilator']['class_room_blocks'];
        }
		if(!empty($selected_class_room_block)){

				$classRooms = $this->ExamRoomNumberOfInvigilator->ClassRoom->find('list', array('fields'=>array('ClassRoom.room_code'),'conditions'=>array('ClassRoom.class_room_block_id'=>$selected_class_room_block,'ClassRoom.available_for_exam'=>1)));
		} else {
			$classRooms = null;
		}

		if(!empty($this->request->data['ExamRoomNumberOfInvigilator']['class_room_id'])){
			$selected_class_room=$this->request->data['ExamRoomNumberOfInvigilator']['class_room_id'];
		}
		if(!empty($this->request->data['ExamRoomNumberOfInvigilator']['class_room_blocks'])){
			$selected_class_room_block=$this->request->data['ExamRoomNumberOfInvigilator']['class_room_blocks'];
		}
		$this->Paginator->settings=$this->paginate;
		debug($this->request->data);
		if(isset($this->request->data['search']) && !empty($this->request->data['search'])){
			$examRoomNumberOfInvigilators=$this->Paginator->paginate('ExamRoomNumberOfInvigilator');
			debug($examRoomNumberOfInvigilators);
		} else {
			$examRoomNumberOfInvigilators=$this->Paginator->paginate('ExamRoomNumberOfInvigilator');
		}
		

		$this->set('examRoomNumberOfInvigilators', $examRoomNumberOfInvigilators);

		$this->set(compact('formatted_class_room_blocks','classRooms','selected_class_room','selected_class_room_block'));

	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid exam room number of invigilator'));
			return $this->redirect(array('action' => 'index'));
		}
		$this->set('examRoomNumberOfInvigilator', $this->ExamRoomNumberOfInvigilator->read(null, $id));
	}

	function add() {
		$from_delete = $this->Session->read('from_delete');
		$from_edit = $this->Session->read('from_edit');
		if($from_delete !=1 && $from_edit !=1){
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
			$selected_academicyear =$this->request->data['ExamRoomNumberOfInvigilator']['academicyear'];
			$this->Session->write('selected_academicyear',$selected_academicyear);
			$selected_semester = $this->request->data['ExamRoomNumberOfInvigilator']['semester'];
			$this->Session->write('selected_semester',$selected_semester);
			$selected_class_room = $this->request->data['ExamRoomNumberOfInvigilator']['class_room_id'];
			$this->Session->write('selected_class_room',$selected_class_room);
			$selected_class_room_block = $this->request->data['ExamRoomNumberOfInvigilator']['class_room_blocks'];
			$this->Session->write('selected_class_room_block',$selected_class_room_block);
			
			$is_already_recorded = $this->ExamRoomNumberOfInvigilator->find('count',array('conditions'=>array('ExamRoomNumberOfInvigilator.class_room_id'=>$selected_class_room, 'ExamRoomNumberOfInvigilator.academic_year'=>$selected_academicyear, 'ExamRoomNumberOfInvigilator.semester' =>$selected_semester)));
			if($is_already_recorded ==0){
				$number_of_invigilator = $this->request->data['ExamRoomNumberOfInvigilator']['number_of_invigilator'];
				if(!empty($number_of_invigilator)){
					$this->request->data['ExamRoomNumberOfInvigilators']['number_of_invigilator'] = $number_of_invigilator;
					$this->request->data['ExamRoomNumberOfInvigilators']['class_room_id'] = $selected_class_room;
					$this->request->data['ExamRoomNumberOfInvigilators']['academic_year'] = $selected_academicyear;
					$this->request->data['ExamRoomNumberOfInvigilators']['semester'] = $selected_semester;
					$this->ExamRoomNumberOfInvigilator->create();
					if ($this->ExamRoomNumberOfInvigilator->save($this->request->data['ExamRoomNumberOfInvigilators'])) {
						$this->Session->setFlash('<span></span>'.__('The exam room number of invigilator has been saved.'),'default',array('class'=>'success-box success-message'));
						//$this->redirect(array('action' => 'index'));
					} else {
						$this->Session->setFlash('<span></span>'.__('The exam room number of invigilator could not be saved. Please, try again.'),'default',array('class'=>'error-box error-message'));
					}
				} else {
					$this->Session->setFlash('<span></span>'.__('Please provide number of invigilator.'),'default',array('class'=>'error-box error-message'));
				}
			} else {
				$this->Session->setFlash('<span></span>'.__('You already have this record.'),'default',array('class'=>'error-box error-message'));
			}
		}
		$classRoomBlocks = $this->ExamRoomNumberOfInvigilator->ClassRoom->ClassRoomBlock->find('all',array('fields'=>array('ClassRoomBlock.id','ClassRoomBlock.block_code'),'conditions'=>array('ClassRoomBlock.college_id'=>$this->college_id), 'contain'=>array('Campus'=>array('fields'=>array('Campus.name')))));
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
			$selected_academicyear = $this->request->data['ExamRoomNumberOfInvigilator']['academicyear'];
		}
		if($this->Session->read('selected_semester')){
			$selected_semester = $this->Session->read('selected_semester');
		} else {
			$selected_semester = $this->request->data['ExamRoomNumberOfInvigilator']['semester'];
		}
		if($this->Session->read('selected_class_room_block')){
			$selected_class_room_block = $this->Session->read('selected_class_room_block');
		}   else {
			$selected_class_room_block = $this->request->data['ExamRoomNumberOfInvigilator']['class_room_blocks'];
		}
		if($this->Session->read('selected_class_room')){
			$selected_class_room = $this->Session->read('selected_class_room');
		} else {
			//$selected_class_room = $this->request->data['ExamRoomConstraint']['class_room_id'];
		}
		 if(!empty($selected_class_room_block)){
				$classRooms = $this->ExamRoomNumberOfInvigilator->ClassRoom->find('list', array('fields'=>array('ClassRoom.room_code'),'conditions'=>array('ClassRoom.class_room_block_id'=>$selected_class_room_block,'ClassRoom.available_for_exam'=>1)));
		} else {
			$classRooms = null;
		}

		//$data_array=null;
		$already_recorded_exam_room_number_of_invigilators = null;

		if(!empty($selected_class_room) &&!empty($selected_academicyear) && !empty($selected_semester)){
				$already_recorded_exam_room_number_of_invigilators = $this->_get_already_record($selected_class_room,$selected_academicyear,$selected_semester);
		}

		$this->set(compact('formatted_class_room_blocks','classRooms', 'selected_academicyear', 'selected_semester','selected_class_room_block','selected_class_room', 'already_recorded_exam_room_number_of_invigilators'));
		//If it come from delete
		if($this->Session->read('from_delete')){
			$this->Session->delete('from_delete');
		} else if($this->Session->read('from_edit')){
			$this->Session->delete('from_edit');
		}
		
	}

	function edit($id = null) {
		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash('<span></span> '.__('Invalid exam room number of invigilator.'),'default',array('class'=>'error-box error-message')); 
			return $this->redirect(array('action'=>'add'));
		}
		$from_edit = 1;
		$this->Session->write('from_edit',$from_edit);
		if (!empty($this->request->data)) {
			$selected_academicyear =$this->request->data['ExamRoomNumberOfInvigilator']['academic_year'];
			$this->Session->write('selected_academicyear',$selected_academicyear);
			$selected_semester = $this->request->data['ExamRoomNumberOfInvigilator']['semester'];
			$this->Session->write('selected_semester',$selected_semester);
			if ($this->ExamRoomNumberOfInvigilator->save($this->request->data)) {
				$this->Session->setFlash('<span></span> '.__('The exam room number of invigilator has been saved'),'default',array('class'=>'success-box success-message'));  
				return $this->redirect(array('action'=>'add'));
			} else {
				$this->Session->setFlash('<span></span> '.__('The exam room number of invigilator could not be saved. Please, try again.'),'default',array('class'=>'success-box success-message'));  
			}
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->ExamRoomNumberOfInvigilator->read(null, $id);
		}
		$classRooms = $this->ExamRoomNumberOfInvigilator->ClassRoom->find('list');
		$this->set(compact('classRooms'));
	}

	function delete($id = null,$from=null) {
		if (!$id) {
			$this->Session->setFlash('<span></span> '.__('Invalid id for exam room number of invigilator.'),'default',array('class'=>'error-box error-message')); 
			if(empty($from)){
				return $this->redirect(array('action'=>'index'));
			} else {
				return $this->redirect(array('action'=>'add'));
			}
		}
		$from_delete = 1;
		$this->Session->write('from_delete',$from_delete);
		if ($this->ExamRoomNumberOfInvigilator->delete($id)) {
			$this->Session->setFlash('<span></span> '.__('Exam room number of invigilator deleted'),'default',array('class'=>'success-box success-message'));  
				if(empty($from)){
					return $this->redirect(array('action'=>'index'));
				} else {
					return $this->redirect(array('action'=>'add'));
				}
		}
		$this->Session->setFlash('<span></span> '.__('Exam room number of invigilator was not deleted.'),'default',array('class'=>'error-box error-message')); 
			if(empty($from)){
				return $this->redirect(array('action'=>'index'));
			} else {
				return $this->redirect(array('action'=>'add'));
			}
	}
	function get_class_rooms($class_room_block_id = null){
		if(!empty($class_room_block_id)){

			$this->layout ='ajax';
			$classRooms = $this->ExamRoomNumberOfInvigilator->ClassRoom->find('list', array('fields'=>array('ClassRoom.room_code'),'conditions'=>array('ClassRoom.class_room_block_id'=>$class_room_block_id, 'ClassRoom.available_for_exam'=>1)));
			$this->set(compact('classRooms'));
		}
	}
	
	function get_already_recorded_exam_room_number_of_invigilator($data=null){
		$this->layout = 'ajax';
		$explode_data = explode("~",$data);
		$class_room_id = $explode_data[0];
		$academicyear = $explode_data[1];
		$academicyear = str_replace('-','/',$academicyear);
		$semester = $explode_data[2];
		$this->Session->write('selected_academicyear',$academicyear);
		$this->Session->write('selected_semester',$semester);
		if(!empty($class_room_id)){
			$this->Session->write('selected_class_room',$class_room_id);
			$selected_class_room_block = $this->ExamRoomNumberOfInvigilator->ClassRoom->field('ClassRoom.class_room_block_id',array('ClassRoom.id'=>$class_room_id));
			$this->Session->write('selected_class_room_block',$selected_class_room_block);
			$already_recorded_exam_room_number_of_invigilators = $this->_get_already_record($class_room_id,$academicyear,$semester);
			$this->set(compact('class_room_id','already_recorded_exam_room_number_of_invigilators'));
		}
	}
	
	function _get_already_record($class_room_id=null,$academicyear=null,$semester){
			$already_recorded_exam_room_number_of_invigilators = $this->ExamRoomNumberOfInvigilator->find('all',array('conditions'=>array('ExamRoomNumberOfInvigilator.class_room_id'=>$class_room_id, 'ExamRoomNumberOfInvigilator.academic_year'=>$academicyear, 'ExamRoomNumberOfInvigilator.semester'=>$semester),'contain'=>array('ClassRoom'=>array('fields'=>array('ClassRoom.id','ClassRoom.room_code', 'ClassRoom.class_room_block_id'), 'ClassRoomBlock'=>array('fields'=>array('ClassRoomBlock.id','ClassRoomBlock.block_code'), 'Campus'=>array('fields'=>array('Campus.name')))))));
			
		return $already_recorded_exam_room_number_of_invigilators;
	}
}
