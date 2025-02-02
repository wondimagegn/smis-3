<?php
class DormitoryBlock extends AppModel {
	var $name = 'DormitoryBlock';
	//var $displayField = 'block_name';
	var $validate = array(
		'block_name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Block name should not be empty, Please provide valid Block name.',
				'allowEmpty' => false,
				'required' => false,
				'last' => true, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'type' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Type should not be empty, Please select type.',
				'allowEmpty' => false,
				'required' => false,
				'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'Campus' => array(
			'className' => 'Campus',
			'foreignKey' => 'campus_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	var $hasMany = array(
		'Dormitory' => array(
			'className' => 'Dormitory',
			'foreignKey' => 'dormitory_block_id',
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
	function get_floor_data($number=null){
		$floor_data = array();
		if($number >=1){
			$floor_data[1] = "Ground Floor";
			for($i=2;$i<=$number;$i++){
				if($i==2){
					$floor_data[$i] = ($i-1)."st Floor";
				} else if($i==3){
					$floor_data[$i] = ($i-1)."nd Floor";
				} else if($i==4){
					$floor_data[$i] = ($i-1)."rd Floor";
				} else {
					$floor_data[$i] = ($i-1)."th Floor";
				}
			}
		return $floor_data;
		}
	}
   function send_dormitory_block_data(){
   	return $this->data;
   }
   
   function getDormitoryBlock () {
        
       $dormitoryBlocks = $this->find('all',array('contain'=>array('Campus')));
      
       $reformateBlocks=array();
       
       foreach ($dormitoryBlocks as $in=>$name) {
          $reformateBlocks[$name['Campus']['name']][$name['DormitoryBlock']['id']]=$name['DormitoryBlock']['block_name'].'-'.
          $name['DormitoryBlock']['type'];
       }
       return $reformateBlocks;
   }
}
