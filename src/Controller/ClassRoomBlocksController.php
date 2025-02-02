<?php
class ClassRoomBlocksController extends AppController {

	public $name = 'ClassRoomBlocks';
	public $menuOptions = array(
		'parent' => 'schedule',
		'exclude'=>array('deleteClassRoom','class_rooms_checkboxs','delete_assign_program_program_type', 'get_modal','get_class_room_blocks', 'get_class_room_block_exam_rooms'),
		'alias' => array(
                    'index' =>'List Class Room Blocks with Rooms',
					'add' =>'Add Class Room Block/Rooms ',
					'assign_program_program_type'=>'Assign/Edit Class Rooms Program Type'
		)
	);
	public $paginate=array();
	public function beforeFilter(){
        parent::beforeFilter();
         //$this->Auth->allow(array('*'));
         $this->Auth->allow('class_rooms_checkboxs','delete_assign_program_program_type','get_modal', 'get_class_room_blocks', 'get_class_room_block_exam_rooms');
    }
    
	public function index($id=null) {
		//$this->ClassRoomBlock->recursive = 1;
		if(ROLE_COLLEGE==$this->role_id){
			$thiscollege = $this->college_id;
		} else {
		
			$thiscollege = $this->ClassRoomBlock->College->Department->field('Department.college_id',array('Department.id'=>$this->department_id));
		}	
		if($id){
			$programProgramTypes = $this->ClassRoomBlock->ClassRoom->ProgramProgramTypeClassRoom->find('all',
			array('conditions'=>array('ProgramProgramTypeClassRoom.class_room_id'=>$id),'contain'=>array('ClassRoom'=>array('fields'=>array('ClassRoom.room_code')),'Program'=>array('fields'=>array('Program.name')),'ProgramType'=>array('fields'=>array('ProgramType.name')))));

			if(!empty($programProgramTypes)){
				$this->set(compact('programProgramTypes'));
			} else{
				$classroomname = $this->ClassRoomBlock->ClassRoom->field('ClassRoom.room_code',array('ClassRoom.id'=>$id));
				$this->set(compact('classroomname'));
			}
			
		}
		$conditions = array('ClassRoomBlock.College_id'=>$thiscollege);
		
		$campuses = $this->ClassRoomBlock->Campus->find('list');
		if(!empty($this->request->data['ClassRoomBlock']['campus_id'])){
			$campus_id = $this->request->data['ClassRoomBlock']['campus_id'];
			$conditions[] = array('ClassRoomBlock.campus_id'=>$campus_id);
			$campus_classRoomBlocks = $this->_get_class_room_block($campus_id);
		} else {
			$campus_classRoomBlocks = null;
		}
		if(!empty($this->request->data['ClassRoomBlock']['class_room_block_code'])){
			$class_room_block_code = $this->request->data['ClassRoomBlock']['class_room_block_code'];
			$conditions[] = array('ClassRoomBlock.block_code'=>$class_room_block_code);
		}
		$this->set(compact('campuses','campus_classRoomBlocks'));
		
		$this->paginate = array('conditions'=>$conditions,'contain'=>array('College'=>array('fields'=>array('College.id','College.name')),
		'Campus'=>array('fields'=>array('Campus.id','Campus.name')),'ClassRoom'));
		$this->Paginator->settings=$this->paginate;
		$this->set('classRoomBlocks', $this->Paginator->paginate('ClassRoomBlock'));
	}
	
	function get_modal($id=null){
		$this->layout = 'ajax';
		if($id){
			$programProgramTypes = $this->ClassRoomBlock->ClassRoom->ProgramProgramTypeClassRoom->find('all',
			array('conditions'=>array('ProgramProgramTypeClassRoom.class_room_id'=>$id),'contain'=>array('ClassRoom'=>array('fields'=>array('ClassRoom.room_code')),'Program'=>array('fields'=>array('Program.name')),'ProgramType'=>array('fields'=>array('ProgramType.name')))));
			if(!empty($programProgramTypes)){
				$this->set(compact('programProgramTypes'));
			} else{
				$classroomname = $this->ClassRoomBlock->ClassRoom->field('ClassRoom.room_code',array('ClassRoom.id'=>$id));
				$this->set(compact('classroomname'));
			}
			
		}
	}
	
	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid class room block'));
			return $this->redirect(array('action' => 'index'));
		}
		$this->set('classRoomBlock', $this->ClassRoomBlock->read(null, $id));
	}

	function add() {
		if (!empty($this->request->data)) {
			$is_class_room_block_duplicated = 0;
			$is_class_room_block_duplicated = $this->ClassRoomBlock->find('count',
			array('conditions'=>array('ClassRoomBlock.college_id'=>$this->request->data['ClassRoomBlock']['college_id'], 'ClassRoomBlock.campus_id'=>$this->request->data['ClassRoomBlock']['campus_id'], 'ClassRoomBlock.block_code'=>$this->request->data['ClassRoomBlock']['block_code'])));
			if($is_class_room_block_duplicated == 0){
				$this->set($this->request->data);
				$this->ClassRoomBlock->create();
				if ($this->ClassRoomBlock->saveAll($this->request->data,array('validate'=>'first'))) {
					$this->Session->setFlash('<span></span>'.__('The class room block has been saved'),
					'default',array('class'=>'success-box success-message'));
					return $this->redirect(array('action' => 'index'));
				} else {
					$this->Session->setFlash('<span></span>'.__('The class room block could not be saved. Please, try again.'),'default',array('class'=>'error-box error-message'));
				}
			} else {
				$this->Session->setFlash('<span></span> The class room block available in this college and campus. Please, provide unique class room block or edit the class room block in ', 
						"session_flash_link", array(
						"class"=>'info-box info-message',
						"link_text" => " this page",
						"link_url" => array(
						"controller" => "class_room_blocks",
						"action" => "index",
						"admin" => false
						)
						));
			}
			
		}
		$colleges = $this->ClassRoomBlock->College->find('list');
		$campuses = $this->ClassRoomBlock->Campus->find('list');
		$this->set(compact('colleges', 'campuses'));
	}

	function edit($id = null) {
		if (!$id && empty($this->request->data)) {
			$this->Session->setFlash('<span></span> '.__('Invalid class room block'),'default',
			array('class'=>'error-box error-message'));
			return $this->redirect(array('action' => 'index'));
		}
		if (!empty($this->request->data)) {
			if ($this->ClassRoomBlock->saveAll($this->request->data,array('validate'=>'first'))) {
				$this->Session->setFlash('<span></span>'.__('The class room block has been saved'),
				'default',array('class'=>'success-box success-message'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('<span></span>'.__('The class room block could not be saved. Please, try again.'),'default',array('class'=>'error-box error-message'));
			}
			
		}
		if (empty($this->request->data)) {
			$this->request->data = $this->ClassRoomBlock->read(null, $id);
		}
		$colleges = $this->ClassRoomBlock->College->find('list');
		$campuses = $this->ClassRoomBlock->Campus->find('list');
		$this->set(compact('colleges', 'campuses'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('<span></span> '.__('Invalid id for class room block'),'default',array('class'=>'error-box error-message'));
			return $this->redirect(array('action'=>'index'));
		}
		//Before Deleting the class room block check whether the block have class room or not
		//If it have class rooms, deny deletion of class room block 
		$classroom_count = 0;
		$classroom_count = $this->ClassRoomBlock->ClassRoom->find('count',array('conditions'=>
		array('ClassRoom.class_room_block_id'=>$id)));
		if($classroom_count ==0){
			if ($this->ClassRoomBlock->delete($id)) {
				$this->Session->setFlash('<span></span> '.__('Class room block deleted'),'default',array('class'=>'success-box success-message'));
				return $this->redirect(array('action'=>'index'));
			}
		} else {
				$this->Session->setFlash('<span></span> '.__('The Class room block can not be delete since it have '.$classroom_count.' class rooms under the block, Please delete the class room first.'),'default',array(
				'class'=>'error-box error-message'));
				return $this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash('<span></span> '.__('Class room block was not deleted'),'default',
		array('class'=>'error-box error-message'));
		return $this->redirect(array('action' => 'index'));
	}
	function deleteClassRoom($id = null){
		if (!$id) {
			$this->Session->setFlash('<span></span>'.__('Invalid id for class room'),'default',array(
			'class'=>'error-box error-message'));
			return $this->redirect(array('action'=>'index'));
		}
		//Before delete class room check this class room is not used in any constranint or schedule
		$is_ever_used = $this->ClassRoomBlock->ClassRoom->is_this_class_room_used_in_others_related_table($id);
		if($is_ever_used == false){
			if ($this->ClassRoomBlock->ClassRoom->delete($id)) {
				$this->Session->setFlash('<span></span> '.__('Class room deleted'),'default',array(
				'class'=>'success-box success-message'));
				return $this->redirect(array('action'=>'index'));
			}
			$this->Session->setFlash('<span></span> '.__('Class room was not deleted'),'default',array(
			'class'=>'error-box error-message'));
			return $this->redirect(array('action' => 'index'));
		} else {
			$error=$this->ClassRoomBlock->ClassRoom->invalidFields();
            if(isset($error['delete_class_rom'])){
				$this->Session->setFlash('<span></span>'.__('You can not delete this class room, Since '.$error['delete_class_rom'][0]),'default',array('class'=>'error-box error-message'));
				return $this->redirect(array('action' => 'index'));
			}
		}
	}
	
	function assign_program_program_type(){
		
		if (!empty($this->request->data)) {
			if(!empty($this->request->data['ClassRoomBlock']['program_type_id'])){
				$selected_class_rooms_array = array();
				if(!empty($this->request->data['ClassRoomBlock']['Selected'])){
					foreach($this->request->data['ClassRoomBlock']['Selected'] as $scrk=>$scrv){
						if($scrv != 0){
							$selected_class_rooms_array[] = $scrv;
						}
					}
				}
				if(count($selected_class_rooms_array)>0){
					$data=array();
					$data['ProgramProgramTypeClassRoom']['program_id'] = $this->request->data['ClassRoomBlock']['program_id'];
					$data['ProgramProgramTypeClassRoom']['program_type_id'] = $this->request->data['ClassRoomBlock']['program_type_id'];
					foreach($selected_class_rooms_array as $class_room_id){
						$data['ProgramProgramTypeClassRoom']['class_room_id'] = $class_room_id;
						$this->ClassRoomBlock->ClassRoom->ProgramProgramTypeClassRoom->create();
						$this->ClassRoomBlock->ClassRoom->ProgramProgramTypeClassRoom->save($data);
					}
				$this->Session->setFlash('<span></span>'.__(' The class rooms has been assigned to selected program and program type.'),'default',array(
					'class'=>'success-box success-message'));
				//$this->redirect(array('action' => 'index'));
				} else {
					$this->Session->setFlash('<span></span>'.__(' Please select at least 1 class rooms'),'default',array('class'=>'error-box error-message'));
				}
			} else {
				$this->Session->setFlash('<span></span>'.__(' Please select program type.'),'default',array(
						'class'=>'error-box error-message'));
			}
			if($this->Session->read('program_id')){
				$this->Session->delete('program_id');
			}
			if($this->Session->read('program_type_id')){
				$this->Session->delete('program_type_id');
			}
			$merged_class_rooms_data = $this->_get_class_rooms_data($this->request->data['ClassRoomBlock']['program_id'],$this->request->data['ClassRoomBlock']['program_type_id']);
			$fromadd_organized_classRooms_blocks_data = $merged_class_rooms_data[0];
			$fromadd_already_assign_class_rooms = $merged_class_rooms_data[1];
			$seleted_program_type_id = $this->request->data['ClassRoomBlock']['program_type_id'];
			$this->set(compact('fromadd_organized_classRooms_blocks_data','fromadd_already_assign_class_rooms', 'seleted_program_type_id'));
		}
		if ($this->Session->read('program_type_id')) {
			
			$seleted_program_type_id = $this->Session->read('program_type_id');
			$merged_class_rooms_data = $this->_get_class_rooms_data($this->Session->read('program_id'), $this->Session->read('program_type_id'));
			$fromadd_organized_classRooms_blocks_data = $merged_class_rooms_data[0];
			$fromadd_already_assign_class_rooms = $merged_class_rooms_data[1];
			$this->set(compact('fromadd_organized_classRooms_blocks_data','fromadd_already_assign_class_rooms', 'seleted_program_type_id'));
			
		}
		
		$programs = $this->ClassRoomBlock->ClassRoom->ProgramProgramTypeClassRoom->Program->find('list');
		$programTypes = $this->ClassRoomBlock->ClassRoom->ProgramProgramTypeClassRoom->ProgramType->find('list');
		$classRooms = $this->ClassRoomBlock->ClassRoom->find('list',array('fields'=>array('room_code')));
		//debug($classRooms);
		$this->set(compact('programs', 'programTypes', 'classRooms'));

	}
	function class_rooms_checkboxs($data= null){
		 if($this->Session->read('program_id')){
			$this->Session->delete('program_id');
		}
		if($this->Session->read('program_type_id')){
			$this->Session->delete('program_type_id');
		}
		$this->layout ='ajax';
		//debug($data);
		$explode_data = explode("~",$data);
		$program_type_id = $explode_data[0];
		$program_id = $explode_data[1];
		//debug($program_type_id);
		//debug($program_id);
		if(!empty($program_type_id)){
			$merged_class_rooms_data = $this->_get_class_rooms_data($program_id,$program_type_id);
			$organized_classRooms_blocks_data = $merged_class_rooms_data[0];
			$already_assign_class_rooms = $merged_class_rooms_data[1];
			$this->set(compact('organized_classRooms_blocks_data','already_assign_class_rooms'));
		}
	}
	
	function delete_assign_program_program_type($id=null){
		if (!$id) {
			$this->Session->setFlash('<span></span> '.__('Invalid id for program program type class room'), 'default',array('class'=>'error-box error-message'));
			return $this->redirect(array('action'=>'assign_program_program_type'));
		}
		//used to redirect to assign_program_program_type page
		$program_id = $this->ClassRoomBlock->ClassRoom->ProgramProgramTypeClassRoom->field('ProgramProgramTypeClassRoom.program_id',array('ProgramProgramTypeClassRoom.id'=>$id));
		$program_type_id = $this->ClassRoomBlock->ClassRoom->ProgramProgramTypeClassRoom->field('ProgramProgramTypeClassRoom.program_type_id',array('ProgramProgramTypeClassRoom.id'=>$id));
		$this->Session->write('program_id',$program_id);
		$this->Session->write('program_type_id',$program_type_id);
		
		if ($this->ClassRoomBlock->ClassRoom->ProgramProgramTypeClassRoom->delete($id)) {
			$this->Session->setFlash('<span></span> '.__('Program program type class room deleted'),'default', array('class'=>'success-box success-message'));
			return $this->redirect(array('action'=>'assign_program_program_type'));
		}
		$this->Session->setFlash('<span></span> '.__('Program program type class room was not deleted'),'default',array('class'=>'error-box error-message'));
		return $this->redirect(array('action' => 'assign_program_program_type'));
	}
	
	function _get_class_rooms_data($program_id=null,$program_type_id){
		//to get this college class rooms
		$class_room_id_array=array();
		$classroomblocks = $this->ClassRoomBlock->find('all',array('fields'=>array('ClassRoomBlock.id'),'conditions'=>array('ClassRoomBlock.college_id'=>$this->college_id),'contain'=>array('ClassRoom'=>array('fields'=>array('ClassRoom.id')))));	
			foreach($classroomblocks as $classroomblock){
				foreach($classroomblock['ClassRoom'] as $classrooms){
					$class_room_id_array[$classrooms['id']] = $classrooms['id'];
				}
			}
		//already assign Class rooms
			$already_assign_class_rooms=$this->ClassRoomBlock->ClassRoom->ProgramProgramTypeClassRoom->find('all', array('conditions'=>array('ProgramProgramTypeClassRoom.class_room_id'=>$class_room_id_array, 'ProgramProgramTypeClassRoom.program_id'=>$program_id, 'ProgramProgramTypeClassRoom.program_type_id'=>$program_type_id),'contain'=>array('ClassRoom'=>array('fields'=>array('ClassRoom.id', 'ClassRoom.room_code'), 'ClassRoomBlock'=>array('fields'=>array('ClassRoomBlock.block_code'),'Campus'=>array('fields'=>array('Campus.name')))))));
			//this class rooms id are used to exclude already assign program type class rooms
			$already_assign_class_rooms_id_array = array();
			foreach($already_assign_class_rooms as $aacrk=>$aacrv){
				$already_assign_class_rooms_id_array[] = $aacrv['ClassRoom']['id'];
			}
			
			$classRooms_blocks_data = $this->ClassRoomBlock->find('all',array('fields'=>array('ClassRoomBlock.id','ClassRoomBlock.block_code'),'conditions'=>array('ClassRoomBlock.college_id'=>$this->college_id),'contain'=>array('Campus'=>array('fields'=>array('Campus.name')),'ClassRoom'=>array('fields'=>array('ClassRoom.id','ClassRoom.room_code')))));
			//debug($classRooms_blocks_data);
			$organized_classRooms_blocks_data = array();
			foreach($classRooms_blocks_data as $crbdk=>$crbdv){
				if(!empty($crbdv['ClassRoom'])){
					foreach($crbdv['ClassRoom'] as $crk =>$crv){
						//exclude already assign class rooms from available class rooms checkbox for assignment
						if(in_array($crv['id'],$already_assign_class_rooms_id_array)){
							//do nothing
						} else {
						$organized_classRooms_blocks_data[$crbdv['Campus']['name']][$crbdv['ClassRoomBlock']['block_code']][$crv['id']] = $crv['room_code'];
						}
					}
				}
			
			}
		$merged_return_get_class_rooms_data_array = array();
		$merged_return_get_class_rooms_data_array[0]=$organized_classRooms_blocks_data;
		$merged_return_get_class_rooms_data_array[1]=$already_assign_class_rooms;
		
		return $merged_return_get_class_rooms_data_array;
	}
	function get_class_room_blocks($campus_id=null){
		if(!empty($campus_id)){
			$this->layout = 'ajax';
			$campus_classRoomBlocks = $this->_get_class_room_block($campus_id);
			$this->set(compact('campus_classRoomBlocks'));
		}
	}
	
	function _get_class_room_block($campus_id=null){
		if(!empty($campus_id)){
			$campus_classRoomBlocks = $this->ClassRoomBlock->find('list',array('fields'=>array('ClassRoomBlock.block_code','ClassRoomBlock.block_code'),'conditions'=>array('ClassRoomBlock.campus_id'=>$campus_id, 'ClassRoomBlock.college_id'=>$this->college_id)));
			return $campus_classRoomBlocks;
		}
	}
	
	function get_class_room_block_exam_rooms($class_room_block_id = null) {
		$this->layout = 'ajax';
		$classRooms = array();
		if(!empty($class_room_block_id)) {
			$classRooms = $this->ClassRoomBlock->ClassRoom->find('list',
				array(
					'conditions' =>
					array(
						'ClassRoom.available_for_exam' => 1,
						'ClassRoom.exam_capacity > 0',
						'ClassRoom.class_room_block_id' => $class_room_block_id
					),
					'fields' =>
					array(
						'ClassRoom.id',
						'ClassRoom.room_code'
					)
				)
			);
		}
		$this->set(compact('classRooms'));
	}
	
}
?>
