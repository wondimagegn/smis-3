<?php
class Prerequisite extends AppModel {
	var $name = 'Prerequisite';
	//The Associations below have been created with all possible keys, those that are not needed can be removed
    /*var $validate = array(
		
		'prerequisite_course_code' => array(
				'rule'=>'isCourseExist',
				'message'=> 'The prerequiste you provided is non exist, please provide a valid and existing course code. If the course has no prerequiste, please leave blank or write none.'
		)		
	);
	*/
	var $belongsTo = array(
		'Course' => array(
			'className' => 'Course',
			'foreignKey' => 'course_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'PrerequisiteCourse' => array(
			'className' => 'Course',
			'foreignKey' => 'prerequisite_course_id',
			'conditions' => '',
			'fields' => array('PrerequisiteCourse.credit','PrerequisiteCourse.course_code',
			'PrerequisiteCourse.course_title'),
			'order' => ''
		)
	);
	
	function isCourseExist() {
			// if the course has no prerequist return true
			if (!isset($this->data['Prerequisite']['prerequisite_course_id']) || strcasecmp(
				$this->data['Prerequisite']['prerequisite_course_id'],'none')==0 || 
				empty($this->data['Prerequisite']['prerequisite_course_id'])) {
			  return true;
			
			}
			
			// check user has enter a valid course code
			$is_course_code_exist= $this->Course->find('count',array('conditions'=>array('Course.course_code'=>
			$this->data['Prerequisite']['prerequisite_course_id'])));
			if($is_course_code_exist>0){
				return true;
			}
			
			return false;
	}
	function prerequisiteCourseCodeUnique($data=null) {
	   // check user has selected unique prerequisite course code 
		//$is_prerequisite_course_code_exist=null;
		$pre_count =0;
		$is_unique =1;
		//$coming_form=array();
		if (!empty($data['Prerequisite'])) { 
			$pre_count = count($data['Prerequisite']);
			$data['Prerequisite'] = array_values($data['Prerequisite']);
			for($i=0;$i<($pre_count - 1); $i++) {
				for($j=$i + 1;$j< $pre_count;$j++) {
					if(strcasecmp ($data['Prerequisite'][$i]['prerequisite_course_id'],$data['Prerequisite'][$j]['prerequisite_course_id']) == 0) {
						$is_unique =0;
						break 2;
					}
				}
			}
			
		}
		if($is_unique ==0) {
			$this->invalidate('prerequisite','The prerequisite course  you selected have duplicated course id. Please select a unique prerequisite course, or delete one of the duplicated prerequisite.');
					return false;
		} else {
		return true;
		}
	}
	
	function deletePrerequisiteList ($course_id=null,$data=null) {
	        $dontdeleteids=array();
	        $deleteids=array();
	        $deleteids=$this->find('list',
            array('conditions'=>array('Prerequisite.course_id'=>$course_id),
            'fields'=>'id'));
            if (!empty($data['Prerequisite'])) {
	            foreach ($data['Prerequisite'] as $in=>$va) {
	                  if (!empty($va['id'])) {
	                        if (in_array($va['id'],$deleteids)) {
	                            $dontdeleteids[]=$va['id'];
	                        }
          
	                  } 
	            }
	        
	        }
	        if (!empty($dontdeleteids)) {
	            foreach ($deleteids as $in=>&$va) {
	                    if (in_array($va,$dontdeleteids)) {
	                        unset($deleteids[$in]);
	                    }
	            }
	        }
	       
          
            if (!empty($deleteids)) {
                $this->deleteAll(array(
                'Prerequisite.id'=>$deleteids), false);
            }
           
            
	}
	
}
?>
