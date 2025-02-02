<?php
class TranscriptFooter extends AppModel {
	var $name = 'TranscriptFooter';
	var $validate = array(
		'acadamic_year' => array(
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
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'Program' => array(
			'className' => 'Program',
			'foreignKey' => 'program_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	function getStudentTranscriptFooter($student_id = null) {
		$student_detail = ClassRegistry::init('Student')->find('first',
			array(
				'conditions' =>
				array(
					'Student.id' => $student_id
				),
				'contain' =>
				array(
					'GraduateList'
				)
			)
		);
		$options = array();
		if(isset($student_detail['GraduateList']) && $student_detail['GraduateList']['id'] != "") {
			$options['conditions']['OR'][0] = array('TranscriptFooter.academic_year <= '.substr($student_detail['Student']['admissionyear'], 0, 4));
			$options['conditions']['OR'][1] = 
			array(
				'TranscriptFooter.applicable_for_current_student' => 1,
				'TranscriptFooter.academic_year <= '.substr($student_detail['GraduateList']['graduate_date'], 0, 4)
			);
		}
		else {
			$options['conditions'] = array('TranscriptFooter.academic_year <= '.substr($student_detail['Student']['admissionyear'], 0, 4));
		}
		$options['order'] = array('TranscriptFooter.academic_year DESC');
		
		$transcript_footer_detail = $this->find('first', $options);
		if(isset($transcript_footer_detail['TranscriptFooter']))
			return $transcript_footer_detail['TranscriptFooter'];
		else
			return array();
	}

}
