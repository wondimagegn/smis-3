<?php
App::uses('ConnectionManager', 'Model');

class Koha extends AppModel {
/*
    public $useTable = 'borrowers';
    public $primaryKey = 'borrowernumber';
    public $useDbConfig = 'koha';
	*/
	 // var $useDbConfig = 'koha';
	public function synckoha($college_id=0){
		 $db1 = ConnectionManager::getDataSource('default');
		if(isset($college_id)
		 && !empty($college_id)){
		$colleges=ClassRegistry::init('College')->find('all',
		array('conditions'=>array('College.id'=>$college_id),'recursive'=>-1));
		} else {
		 $colleges=ClassRegistry::init('College')->find('all',
		 array('recursive'=>-1));
		}
		debug($colleges);
	/*
		$result=$this->find('all',
		array('limit'=>5));
		echo '<pre>';
		debug($result);
		echo '</pre>';
		*/
		 $db = ConnectionManager::getDataSource('koha'); #Remote Database 1
      $sql   = "SELECT * FROM  borrowers as Borrower limit 3";
     // $query = $conn1->prepare($sql);
     // $query->execute();
      $result = $db->query($sql); #Here is the result
      echo '<pre>';
		print_r($result);
		echo '</pre>';
	}
} 
