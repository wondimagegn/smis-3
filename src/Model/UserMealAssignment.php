<?php
class UserMealAssignment extends AppModel {
	var $name = 'UserMealAssignment';
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'MealHall' => array(
			'className' => 'MealHall',
			'foreignKey' => 'meal_hall_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
	
	function checkDuplicationAssignment ($data=null) {
	       // validation against duplication of meal hall assignment
	     
	       foreach ($data['UserMealAssignment'] as $id=>$value) {
	                    
	                    $meal=$this->MealHall->field('name',array('id'=>$value['meal_hall_id']));
	                    $check=$this->find('count',array('conditions'=>array('UserMealAssignment.user_id'=>$value['user_id'],'UserMealAssignment.meal_hall_id'=>$value['meal_hall_id'])));
	                    if ($check > 0) {
	                            $this->invalidate('error','The selected user has already assigned '.$meal.' meal halls previously.');
	                        return false; 
	                               
	                   }
	     }
	     return true;
	}
	
	function mealHallAssignmentOrganizedByCampus ($user_id=null) {
          $alreadyAssignedHalls=$this->find('all',array(
           'contain'=>array('User'=>array('id','full_name'),'MealHall'=>array('Campus'))));  
         
	      $organizedMeallHallAssignmentCampus=array();
	      $count=0;
	      foreach ($alreadyAssignedHalls as $i=>$v) {
	            $organizedMeallHallAssignmentCampus[$v['MealHall']['Campus']['name']][$count]['User']=$v['User'];
	            $organizedMeallHallAssignmentCampus[$v['MealHall']['Campus']['name']][$count]['MealHall']=$v['MealHall'];
	            $organizedMeallHallAssignmentCampus[$v['MealHall']['Campus']['name']][$count]['UserMealAssignment']=$v['UserMealAssignment'];
	            $count++;
	          
	      }
	      return $organizedMeallHallAssignmentCampus;
	}
	
	//function that return the meal hall ids assigned for a given ticker
	function assigned_meal_hall ($user_id=null)  {
 		$meal_hall_ids = $this->find('list',array('fields'=>array('UserMealAssignment.meal_hall_id','UserMealAssignment.meal_hall_id'), 'conditions'=>array('UserMealAssignment.user_id'=>$user_id)));
 		return $meal_hall_ids;
	} 
}
