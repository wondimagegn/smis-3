<?php
if(isset($publishedcourse_data)){
		echo '<table><tr><td colspan="3" class="font"> Select Course Number of Session</td></tr><tr>';
		if($publishedcourse_data[0]['Course']['lecture_hours'] >0){
			$session_array=array();
			for($i=1;$i<=$publishedcourse_data[0]['Course']['lecture_hours'];$i++){
				$session_array[$i] =$i;
			}
			echo '<td>'.$this->Form->input('lecture_number_of_session',array('type'=>'select',
				'options'=>$session_array)).'</td>';
		}
		if($publishedcourse_data[0]['Course']['tutorial_hours'] >0){
			$session_array=array();
			for($i=1;$i<=$publishedcourse_data[0]['Course']['tutorial_hours'];$i++){
				$session_array[$i] =$i;
			}
			echo '<td>'.$this->Form->input('tutorial_number_of_session',array('type'=>'select',
				'options'=>$session_array)).'</td>';
		}
		if($publishedcourse_data[0]['Course']['laboratory_hours'] >0){
			$session_array=array();
			for($i=1;$i<=$publishedcourse_data[0]['Course']['laboratory_hours'];$i++){
				$session_array[$i] =$i;
			}
			echo '<td>'.$this->Form->input('lab_number_of_session',array('type'=>'select',
			'options'=>$session_array)).'</td>';
		}
		echo '</tr></table>';
}
?>