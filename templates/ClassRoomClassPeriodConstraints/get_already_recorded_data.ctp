<?php
	if(isset($already_recorded_class_room_class_period_constraints)) {
		//if(!empty($already_recorded_class_room_class_period_constraints)){
		?><div class="smallheading">Already Recorded Class Room Class Period Constraints</div>
		<table style='border: #CCC solid 1px'>
		<tr><th style='border-right: #CCC solid 1px'>No.</th><th style='border-right: #CCC solid 1px'>Room
			</th><th style='border-right: #CCC solid 1px'>Block</th><th style='border-right: #CCC solid 1px'>Campus</th><th style='border-right: #CCC solid 1px'>Academic Year</th><th style='border-right: #CCC solid 1px'>Semester</th><th style='border-right: #CCC solid 1px'>Week Day</th><th style='border-right: #CCC solid 1px'>Period</th><th style='border-right: #CCC solid 1px'>Option</th><th style='border-right: #CCC solid 1px'>Action</th></tr>
		<?php
		$count = 1;
		foreach($already_recorded_class_room_class_period_constraints as $arck=>$arcv){
		
			$week_day_name = null;
			switch($arcv['ClassPeriod']['week_day']){
				case 1: $week_day_name ="Sunday"; break;
				case 2: $week_day_name ="Monday"; break;
				case 3: $week_day_name ="Tuesday"; break;
				case 4: $week_day_name ="Wednesday"; break;
				case 5: $week_day_name ="Thursday"; break;
				case 6: $week_day_name ="Friday"; break;
				case 7: $week_day_name ="Saturday"; break;
				default : $week_day_name =null;
			}
			$option = null;
			if($arcv['ClassRoomClassPeriodConstraint']['active'] == 0){
				$option= "Free";
			} else {
				$option ="Occupied";
			}
		
			echo "<tr><td style='border-right: #CCC solid 1px'>".$count++. "</td><td style='border-right: #CCC solid 1px'>".
				$arcv['ClassRoom']['room_code']."</td><td style='border-right: #CCC solid 1px'>".
				$arcv['ClassRoom']['ClassRoomBlock']['block_code']."</td><td style='border-right: #CCC solid 1px'>".
				$arcv['ClassRoom']['ClassRoomBlock']['Campus']['name']."<td style='border-right: #CCC solid 1px'>".
				$arcv['ClassRoomClassPeriodConstraint']['academic_year']."</td><td style='border-right: #CCC solid 1px'>".
				$arcv['ClassRoomClassPeriodConstraint']['semester']."</td><td style='border-right: #CCC solid 1px'>".
				$week_day_name.'('.$arcv['ClassPeriod']['week_day'].')'."<td style='border-right: #CCC solid 1px'>".
				$this->Format->humanize_hour($arcv['ClassPeriod']['PeriodSetting']['hour'])."</td><td style='border-right: #CCC solid 1px'>".
				$option."</td><td style='border-right: #CCC solid 1px'>".
			$this->Html->link(__('Delete'), array('action' => 'delete', $arcv['ClassRoomClassPeriodConstraint']['id'],"fromadd"),null, sprintf(__('Are you sure you want to delete?'), $arcv['ClassRoomClassPeriodConstraint']['id'],"fromadd")).
			"</td></tr>";
		}
		?></table><?php
	//}
	}
?>
