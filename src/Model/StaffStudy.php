<?php
App::uses('AppModel', 'Model');
/**
 * StaffStudy Model
 *
 * @property Staff $Staff
 * @property Country $Country
 */
class StaffStudy extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'education' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'leave_date' => array(
			'date' => array(
				'rule' => array('date'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'notBlank' => array(
				'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'return_date' => array(
			'date' => array(
				'rule' => array('date'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'notBlank' => array(
				'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'specialization' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'university_joined' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	// The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Staff' => array(
			'className' => 'Staff',
			'foreignKey' => 'staff_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Country' => array(
			'className' => 'Country',
			'foreignKey' => 'country_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	public $hasMany = array(
		'Attachment' => array( 
            'className' => 'Media.Attachment', 
            'foreignKey' => 'foreign_key', 
            'conditions'    => array('model' => 'StaffStudy'),
			'order' => array('Attachment.created' => 'DESC'),
            'dependent' => true, 
        ),
	);

	function preparedAttachment($data=null,$group=null){
            foreach ($data['Attachment'] as $in=>  &$dv) {
		                    
		                     if (empty($dv['file']['name']) && empty($dv['file']['type'])
		                     && empty($dv['tmp_name'])) {
		                            unset($data['Attachment'][$in]);
		                     } else {
		                         $dv['model']='StaffStudy';
                                 $dv['group']=$group;		                        
		                     }
		      }
		  return $data;
    }

    function getStaffCompletedHDPStatistics($acadamic_year=null,$department_id=null,$sex='all'){

       $graph['data']=array();
	   $graph['labels']=array();
       // list out the department 
	   if (isset($department_id) && !empty($department_id)) 
       {	
       		debug($department_id);	      
			$college_id = explode('~', $department_id);
			if(count($college_id) > 1) {
			$departments=$this->Staff->Department->find('all',
				array('conditions'=>array('Department.college_id'=>$college_id[1]
					),'contain'=>array('College','YearLevel')));
			} else {
              $departments=$this->Staff->Department->find('all',array('conditions'=>array('Department.id'=>$department_id
              	),'contain'=>array('College','YearLevel')));
			} 
	   } else {
	   	  $departments=$this->Staff->Department->find('all',array('contain'=>array('College','YearLevel')
	   	  	));
	   }
	   //debug($departments);
	    if($sex=="all"){
			 $sexList=array('male'=>'male','female'=>'female');
		} else {
			$sexList[$sex]=$sex;
		}
		App::import('Component','AcademicYear');
	    $AcademicYear= new AcademicYearComponent(new ComponentCollection);
	    $acadamicYearBegDate=$AcademicYear->get_academicYearBegainingDate($acadamic_year);
	   
	    $hdpTrainningStatistics=array();
	    $graph['series']=array('male','female');
	    $completed=array('0'=>'Not Completed','1'=>'Completed');
	    foreach ($departments as $key => $value) {
	    	 foreach($sexList as $skey => $svalue) {
	    	 	 foreach($completed as $ckey => $cvalue) {
	    	 	  $check=$this->find('all',array('conditions'=>array('StaffStudy.education'=>'HDP','StaffStudy.study_completed'=>$ckey,
	    	 	  	'StaffStudy.leave_date >= '=>
	    	 	  	$acadamicYearBegDate,
	    	 	  
	    	 	  	'StaffStudy.staff_id in (select id from staffs where gender="'.$skey.'" and department_id='.$value['Department']['id'].')'
	    	 	  	),
                    'contain'=>array('Staff')
	    	 	  ));
	    	 	  debug($check);
	    	 	 	if(!empty($check)){
	 	 			 	$hdpTrainningStatistics[$value['College']['name']][$value['Department']['name']][$ckey][$skey]=$this->find('count',array('conditions'=>array('StaffStudy.education'=>'HDP','StaffStudy.study_completed'=>$ckey)));
	    	 	 	}
	    	    }
	    	}
	    }
	    return $hdpTrainningStatistics;
    }
}
