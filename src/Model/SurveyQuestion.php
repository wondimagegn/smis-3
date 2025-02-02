<?php
App::uses('AppModel', 'Model');

class SurveyQuestion extends AppModel {


	public $validate = array(
		'question_english' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide the survey question',
				
			),
		),
	);

	public $hasMany = array(
		'AlumniResponse' => array(
			'className' => 'AlumniResponse',
			'foreignKey' => 'survey_question_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'SurveyQuestionAnswer' => array(
			'className' => 'SurveyQuestionAnswer',
			'foreignKey' => 'survey_question_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);
	
    function unsetdata($data=array()) {
		if(!empty($data)) {
		
		    if(($data['SurveyQuestion']['answer_required_yn']==0 && $data['SurveyQuestion']['allow_multiple_answers']==0)){
				unset($data['SurveyQuestionAnswer']);
			} else if($data['SurveyQuestion']['answer_required_yn']==1){
			
			}
				
			return $data;
		}
		return $data;
	}

}
