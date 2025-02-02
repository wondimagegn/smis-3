<?php
App::uses('AppModel', 'Model');
/**
 * Alumnus Model
 *
 * @property Student $Student
 */
class Alumnus extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		
		'full_name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide your full name',
				
			),
		),
		'father_name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide your father name',
			),
		),
		'region' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide your region',
				
			),
		),
		'woreda' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide your region',
			),
		),
	
		'housenumber' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide your house number',
				
			),
		),
		'email' => array(
			'email' => array(
				'rule' => array('email'),
				'message' => 'Please enter a valid email address.',
				'allowEmpty' => false,
				'required' => true,
				
			),
            'unique'=>array(
				 'rule'=>'isUnique',
				 
				 'message'=>'The email address is used by someone. Please provided unique different email.',
				 
			)
		),
		'mobile' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please enter a valid mobile.',
				
			),
		),
		
	
		'sex' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please select your gender.',
				
			),
		),
		'placeofbirthregion' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
					'message' => 'Please provide place of birth region.',
				
			),
		),
		
		'fieldofstudy' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
					'message' => 'Please provide field of study.',
				
			),
		),
		'age' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Please provide your current age.',
				
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
		'Student' => array(
			'className' => 'Student',
			'foreignKey' => 'student_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	var $hasMany = array(
		'AlumniResponse' => array(
			'className' => 'AlumniResponse',
			'foreignKey' => 'alumni_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
	);
	
	public function formatResponse($data){
	    $formattedData=array();
	    $formattedData['Alumnus']=$data['Alumnus'];
	    $count=0;
		foreach($data['AlumniResponse'] as $k=>$v){
		        if(isset($v['answer']['mother']) && !empty($v['answer']['mother']) && isset($v['answer']['father']) && 
		        !empty($v['answer']['father'])){
		        	$formattedData['AlumniResponse'][$count]['survey_question_id']=$v['survey_question_id'];
		        	$formattedData['AlumniResponse'][$count]['mother']=1;
		        	$formattedData['AlumniResponse'][$count]['survey_question_answer_id']=$v['answer']['mother'];
		        	
		        	$count++;
		        	$formattedData['AlumniResponse'][$count]['survey_question_id']=$v['survey_question_id'];
		        	$formattedData['AlumniResponse'][$count]['father']=1;
		        	$formattedData['AlumniResponse'][$count]['survey_question_answer_id']=$v['answer']['father'];
		        	
		        } else {
		          	 if(empty($v['answer'])){
		          	 $formattedData['AlumniResponse'][$count]['survey_question_id']=$v['survey_question_id'];
		        	
		        	$formattedData['AlumniResponse'][$count]['specifiy']=$v['specifiy'];
		          	 } else if(is_array($v['answer'])) {
		               foreach($v['answer'] as $ak=>$av){
		              	 	if($av==1){
		               	 $formattedData['AlumniResponse'][$count]['survey_question_id']=$v['survey_question_id'];
		        	
		        	$formattedData['AlumniResponse'][$count]['specifiy']=$v['specifiy'];
		        	$formattedData['AlumniResponse'][$count]['survey_question_answer_id']=$ak;
		        			$count++;
		        			}
		        	
		               }
		             } else if(!empty($v['answer']) && !is_array($v['answer'])){
		             		 $formattedData['AlumniResponse'][$count]['survey_question_id']=$v['survey_question_id'];
		        	
		        	$formattedData['AlumniResponse'][$count]['specifiy']=$v['specifiy'];
		        	$formattedData['AlumniResponse'][$count]['survey_question_answer_id']=$v['answer'];
		             }
		      }
		      $count++;
		}
		
		return $formattedData;
		
	}
	
	function completedRoundOneQuestionner($student_id){
		$surveyQuestions=ClassRegistry::init('SurveyQuestion')->find('all',
		array('contain'=>array('SurveyQuestionAnswer')));
		$alumni_id=$this->find('first',array('conditions'=>array('Alumnus.student_id'=>$student_id),'recursive'=>-1));
		if(empty($alumni_id['Alumnus']['student_id'])){
			return false;
		} else {
			return true;
		}
		
		if(!empty($surveyQuestions)){
		
				foreach($surveyQuestions as $k=>$v){
					
					$response=ClassRegistry::init('AlumniResponse')->find('count',
					array('conditions'=>array('AlumniResponse.alumni_id'=>$alumni_id['Alumnus']['id'],
					'AlumniResponse.survey_question_id'=>$v['SurveyQuestion']['id'])));
					if(empty($response)){
						debug($v['SurveyQuestion']['id']);
						return false;
					}
				}
				return true;
		}
		return false;
	}
	public function getSelectedAlumniSurvey($student_ids){
		$alumniresponse=$this->find('all',array('conditions'=>array('Alumnus.student_id'=>$student_ids),'contain'=>array('AlumniResponse'=>array('SurveyQuestion','SurveyQuestionAnswer'))));
		return $alumniresponse;
		
	}
	public function getCompletedSurvey($student_ids){
		$alumniresponse=$this->find('all',array('conditions'=>array('Alumnus.student_id'=>$student_ids),'contain'=>array('AlumniResponse'=>array('SurveyQuestion','SurveyQuestionAnswer'))));
		$student=array();
		foreach($alumniresponse as $k=>$v){
			foreach($v['AlumniResponse'] as $alk=>$alv){
			
				if($alv['mother']==1){
				  $student[$v['Alumnus']['full_name'].'~'.$v['Alumnus']['student_id']][$alv['survey_question_id']]['mother']=$alv;
				} else if ($alv['father']==1){
				 $student[$v['Alumnus']['full_name'].'~'.$v['Alumnus']['student_id']][$alv['survey_question_id']]['father']=$alv;
				} else {
						if($alv['SurveyQuestion']['allow_multiple_answers']==1){
						    $student[$v['Alumnus']['full_name'].'~'.$v['Alumnus']['student_id']][$alv['survey_question_id']]['answer'][]=$alv;
						    
						} else if ($alv['SurveyQuestion']['answer_required_yn']==1 && !empty($alv['survey_question_answer_id'])){
						   $student[$v['Alumnus']['full_name'].'~'.$v['Alumnus']['student_id']][$alv['survey_question_id']]['answer']=$alv;
						} else if(empty($alv['survey_question_answer_id']) && !empty($alv['specifiy'])){
					     $student[$v['Alumnus']['full_name'].'~'.$v['Alumnus']['student_id']][$alv['survey_question_id']]['answer']=$alv['specifiy'];					
						}
				}
				
			}
	   }
	   return $student;
	
	}
	
	public function checkIfStudentGradutingClass($student_id){
	    $studentCurriculum=ClassRegistry::init('Student')->find('first',array('conditions'=>array('Student.id'=>$student_id),
	    'contain'=>array('Curriculum')));
	    
	    $allRegistration=ClassRegistry::init('CourseRegistration')->find('all',array('conditions'=>array('CourseRegistration.student_id'=>$student_id),
	'contain'=>array('PublishedCourse'=>array('Course'))));
		$sumRegistered=0;
		$graduatingCourseTaken=0;
		foreach($allRegistration as $k=>$v){
		      $sumRegistered+=$v['PublishedCourse']['Course']['credit'];
		      if($v['PublishedCourse']['Course']['thesis']){
		      	$graduatingCourseTaken=1;
		         break;
		      }
		}
		
		$exemptionMaximum=$this->query(
		"SELECT SUM(course_taken_credit) as sumex 
		FROM  course_exemptions
		WHERE student_id =".$student_id."
		order by SUM(course_taken_credit)  
		DESC limit 1
		");
		
		if(($sumRegistered+$exemptionMaximum[0][0]['sumex'])>=$studentCurriculum['Curriculum']['minimum_credit_points'] ){
		   return true;
		} else if ($graduatingCourseTaken==1){
			return true;
		}
		return false;
		
	}
	
}
