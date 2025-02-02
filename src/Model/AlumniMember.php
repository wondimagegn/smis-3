<?php
App::uses('AppModel', 'Model');
/**
 * AlumniMember Model
 *
 */
class AlumniMember extends AppModel {

  public $validate = array(
		'first_name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide first name.',
			),
		),
		'last_name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide last name.',
		
			),
		),
		
		'email' => array(
			'email' => array(
				'rule' => array('email'),
				'message' => 'Please enter a valid email address.',
				
			),
			
            'unique'=>array(
				 'rule'=>'isUnique',
				 'message'=>'The email address is used by someone. Please provided unique different email.',
				 
			)
		),
		/*
		'date_of_birth' => array(
			'date' => array(
				'rule' => array('date'),
				'message' => 'Please provide birth date.',
				
			),
		),
		*/
		'gradution' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide gradution.',
				
			),
		),
		'gender' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please select  gender.',
				
			),
		),
		'phone' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide phone number.',
				
			),
		),
		'institute_college' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide college.',
			),
		),
		'department' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide department.',
			),
		),
		'program' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide program.',
			),
		),
		
	);
	public $virtualFields = array(
	'full_name' => "CONCAT(AlumniMember.first_name, ' ',AlumniMember.last_name)",
    );
     public function nextTrackingNumber(){
		$nextapplicationnumber=$this->find('first',
		array('order'=>array('AlumniMember.created DESC')));
		if(isset($nextapplicationnumber) 
		&& !empty($nextapplicationnumber)){
			return $nextapplicationnumber['AlumniMember']['trackingnumber']+1;
		} 
		return 20011;
	 }
	
	 function checkUnique($data, $fieldName) {
            $valid = false;
            if(isset($fieldName) && $this->hasField($fieldName)) {
                $valid = $this->isUnique(array($fieldName => $data));
            }
            return $valid;
    }

}
