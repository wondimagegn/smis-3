<div class="mealAttendances form">
<?php echo $this->Form->create('MealAttendance');?>
<div class="smallheading"><?php echo 'Meal Hall Attendances Summary Report'; ?></div>
	<table cellpadding="0" cellspacing="0">
	<?php 
        echo '<tr><td class="font"> MealHall</td>'; 
        echo '<td class="font">'.$this->Form->input('Search.mealHalls',array('label' => false, 'id'=>'mealHalls_id','options'=>$mealHallsView ,'style'=>'width:150px','empty'=>"All")).'</td>';
        echo '<td class="font"> Date</td>'; 
        echo '<td class="font">'.$this->Form->input('Search.date',array('label' => false,'type'=>'date', 'selected'=>isset($selected_date)?$selected_date:$current_date)).'</td></tr>';
        		
		echo '<tr><td colspan="4">'.$this->Form->end('Search').'</td></tr>'; 
	?>
	</table>
	<table cellpadding="0" cellspacing="0" style="border: #CCC solid 1px">
	<?php 
		if(!empty($meal_hall_attendances_details)){
			echo '<tr><th style="border-right: #CCC solid 1px">S.N<u>o</u></th>';
			echo '<th style="border-right: #CCC solid 1px">Meal Hall</th>';
			echo '<th style="border-right: #CCC solid 1px">Campus</th>';
			echo '<th style="border-right: #CCC solid 1px">Date</th>';
			echo '<th style="border-right: #CCC solid 1px">Break Fast</th>';
			echo '<th style="border-right: #CCC solid 1px">Lunch</th>';
			echo '<th style="border-right: #CCC solid 1px">Dinner</th></tr>';
			
			$count =1;
			foreach($meal_hall_attendances_details as $meal_hall_attendance){
				echo '<tr><td style="border-right: #CCC solid 1px">'.$count++.'</td>';
				echo '<td style="border-right: #CCC solid 1px">'.$meal_hall_attendance['meal_hall_name'].'</td>';
				echo '<td style="border-right: #CCC solid 1px">'.$meal_hall_attendance['campus'].'</td>';
				echo '<td style="border-right: #CCC solid 1px">'.(isset($selected_formatted_date)?$this->Format->short_date(date($selected_formatted_date)):$this->Format->short_date(date("Y-m-d"))).'</td>';
				echo '<td style="border-right: #CCC solid 1px">'.
				$meal_hall_attendance['served']['Break Fast'].' Out of '.
				$meal_hall_attendance['total_assigned'].'</td>';
				echo '<td style="border-right: #CCC solid 1px">'.$meal_hall_attendance['served']['Lunch'].' Out of '.$meal_hall_attendance['total_assigned'].'</td>';
				echo '<td style="border-right: #CCC solid 1px">'.$meal_hall_attendance['served']['Dinner'].' Out of '.$meal_hall_attendance['total_assigned'].'</td></tr>';
			}
		}
	?>
	</table>
	
</div>
