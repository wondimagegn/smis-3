<?php
class UserDormAssignment extends AppModel {
	var $name = 'UserDormAssignment';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'DormitoryBlock' => array(
			'className' => 'DormitoryBlock',
			'foreignKey' => 'dormitory_block_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	function checkDuplicationAssignment ($data=null) {
	       // validation against duplication of return item list.
	       foreach ($data['UserDormAssignment'] as $id=>$value) {
	                    
	                    $dorm=$this->DormitoryBlock->field('block_name',array('id'=>$value['dormitory_block_id']));
	                    $check=$this->find('count',array('conditions'=>array('UserDormAssignment.user_id'=>$value['user_id'],'UserDormAssignment.dormitory_block_id'=>$value['dormitory_block_id'])));
	                    if ($check > 0) {
	                            $this->invalidate('error','The selected user has already assigned '.$dorm.' dormitory blocks previously.');
	                        return false; 
	                               
	                   }
	     }
	     return true;
	}
	
	function dormitoryBlocksAssignmentOrganizedByCampus ($user_id=null) {
          $alreadyAssignedBlocks=$this->find('all',array(
           'contain'=>array('User'=>array('id','full_name'),'DormitoryBlock'=>array('Campus'))));   
        
	      $organizedBlockAssignmentCampus=array();
	      $count=0;
	      foreach ($alreadyAssignedBlocks as $i=>$v) {
	            $organizedBlockAssignmentCampus[$v['DormitoryBlock']['Campus']['name']][$count]['User']=$v['User'];
	            $organizedBlockAssignmentCampus[$v['DormitoryBlock']['Campus']['name']][$count]['DormitoryBlock']=$v['DormitoryBlock'];
	            $organizedBlockAssignmentCampus[$v['DormitoryBlock']['Campus']['name']][$count]['UserDormAssignment']=$v['UserDormAssignment'];
	            //UserDormAssignment
	           $count++; 
	      }
	      return $organizedBlockAssignmentCampus;
	} 
}
