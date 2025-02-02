<?php
class Note extends AppModel {
	var $name = 'Note';
	var $displayField = 'title';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'College' => array(
			'className' => 'College',
			'foreignKey' => 'college_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Department' => array(
			'className' => 'Department',
			'foreignKey' => 'department_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	function getRecentNote(){
	     $start = date('Y-m-d');
         $end = date('Y-m-d', strtotime('+7 day'));
	     $conditions = array('Note.start_date <=' => $end, 'Note.end_date >=' => $start);
	     $getRecentNote = $this->find('all', array(
            'conditions' => $conditions,
            'order' => 'title DESC',
            'limit' => 2)
        );
        return $getRecentNote;
	}
}
