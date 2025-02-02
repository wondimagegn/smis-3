<?php
class PeriodSetting extends AppModel {
	var $name = 'PeriodSetting';
	var $displayField = 'period';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'College' => array(
			'className' => 'College',
			'foreignKey' => 'college_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	var $hasMany = array(
		'ClassPeriod' => array(
			'className' => 'ClassPeriod',
			'foreignKey' => 'period_setting_id',
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
?>