<?php
class NumberProcess extends AppModel {
	var $name = 'NumberProcess';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	function recoredAsRunning ($user_id = null, $initiated_by = null) {
	        $check_record_is_existed=$this->find('count',
	        array('conditions'=>array('NumberProcess.user_id'=>$user_id)));
	        if ($check_record_is_existed>0) {
	        
	        } else {
	            $data['NumberProcess']['user_id'] = $user_id;
	            $data['NumberProcess']['initiated_by'] = $initiated_by;
	            $this->save($data);
	        }
	}
	
	function jobDoneDelete($user_id=null) {
	    $processRunning=$this->find('first',array('conditions'=>
	    array('NumberProcess.user_id'=>$user_id),'recursive'=>-1));
	   
	    $this->delete($processRunning['NumberProcess']['id']);
	    
	}
}
