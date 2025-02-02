<?php
App::uses('AppModel', 'Model');

class SurveyQuestionAnswer extends AppModel {
	
	public $validate = array(
		'answer_english' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Please provide answers',
			
			),
		),
	);

	public $belongsTo = array(
		'SurveyQuestion' => array(
			'className' => 'SurveyQuestion',
			'foreignKey' => 'survey_question_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	public $hasMany = array(
		'AlumniResponse' => array(
			'className' => 'AlumniResponse',
			'foreignKey' => 'survey_question_answer_id',
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
	
	
}
