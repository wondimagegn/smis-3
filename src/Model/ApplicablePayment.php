<?php
class ApplicablePayment extends AppModel {
	var $name = 'ApplicablePayment';
	var $validate = array(
		
		'sponsor_type' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide sponsor type',
				'allowEmpty' => false,
				'required' => true,
			),
		),
	);
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'Student' => array(
			'className' => 'Student',
			'foreignKey' => 'student_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
   
   function duplication ($data=null) {
            if (empty($data['ApplicablePayment']['sponsor_type'])) {
                    return 0;
            }
            $count=$this->find('count',array('conditions'=>
              array('ApplicablePayment.student_id'=>$data['ApplicablePayment']['student_id'],
              'ApplicablePayment.semester'=>$data['ApplicablePayment']['semester'],
              'ApplicablePayment.academic_year'=>$data['ApplicablePayment']['academic_year'])));
            debug($count);
            return $count;
     }

}
