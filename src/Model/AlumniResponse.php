<?php
App::uses('AppModel', 'Model');

class AlumniResponse extends AppModel {
	public $validate = array(	
		
	);

	public $belongsTo = array(
		'Alumnus' => array(
			'className' => 'Alumnus',
			'foreignKey' => 'alumni_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'SurveyQuestion' => array(
			'className' => 'SurveyQuestion',
			'foreignKey' => 'survey_question_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'SurveyQuestionAnswer' => array(
			'className' => 'SurveyQuestionAnswer',
			'foreignKey' => 'survey_question_answer_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	function completedRoundOneQuestionner($alumni_id){
		$surveyQuestions=$this->SurveyQuestion->find('all',
		array('contain'=>array('SurveyQuestionAnswer')));
		debug($surveyQuestions);
		if(!empty($surveyQuestions)){
				foreach($surveyQuestions as $k=>$v){
					
					$response=$this->find('count',
					array('conditions'=>array('AlumniResponse.alumni_id'=>$alumni_id,
					'AlumniResponse.survey_question_id'=>$v['SurveyQuestion']['id'])));
					if(empty($response)){
						return false;
					}
				}
				return true;
		}
		return false;
	}
}
