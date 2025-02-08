<div class="applicablePayments form">
<?php echo $this->Form->create('ExceptionMealAssignment');?>

<p class="fs16">Please search the student you want to put into exception.</p>
<table class="fs13 small_padding">
	<tr> 
		<td style="width:20%">First Letter of Name:</td>
		<td style="width:30%"><?php echo $this->Form->input('Search.name',array('label'=>false)); ?></td>
		<td style="width:20%">Student Number/ID:</td>
		<td style="width:30%"><?php echo $this->Form->input('Search.studentnumber',array('label'=>false)); 7
		?></td>
	</tr>
	<tr> 
		<td style="width:20%">Meal Hall:</td>
		<td style="width:30%"><?php echo $this->Form->input('Search.meal_hall_id',array('label'=>false,'empty'=>' ')); ?></td>
		<td style="width:20%">&nbsp;</td>
		<td style="width:30%">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="4"><?php echo $this->Form->Submit('Search',
		array('div'=>false,'name'=>'continue')); ?></td>	
	</tr>
</table>
<?php 
    if (!empty($studentslist)) {
?>
	<div class="smallheading"><?php echo __('List of students in the selected criteria.');?></div>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th>S.N<u>o</u></th>
			<th><?php echo 'Student';?></th>
			<th><?php echo 'User Meal Hall';?></th>
			<th><?php echo 'Allow/Deny';?></th>
			<th><?php echo 'Start Date';?></th>
			<th><?php echo 'End Date';?></th>
			<th><?php echo 'Meal Hall';?></th>
		
			<th><?php echo 'Remark';?></th>
	</tr>
	<?php
	  $options=array('1'=>'Allow','-1'=>'Deny');
  // $attributes=array('legend'=>false);
   $attributes=array('legend'=>false,'label'=>false,'separator'=>'<br/>');
	$i = 0;
	$start = 0;
	$serino=1;
	foreach ($studentslist as $student):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $serino++; ?>&nbsp;</td>
		
		
		<td>
			<?php echo $this->Html->link($student['Student']['full_name'], array('controller' => 'students', 'action' => 'view', $student['Student']['id'])); ?>
			
			
		<?php 
		  
		     
		    echo $this->Form->hidden('ExceptionMealAssignment.'.$start.'.student_id',array('label'=>false,'div'=>false,'value'=>$student['Student']['id'])); 
		 
		?>
		</td>
		<td>
		  <?php 
		      if (isset($student['MealHallAssignment']['meal_hall_id']) && !empty($student['MealHallAssignment']['meal_hall_id'])) {
		           echo $mealHalls[$student['MealHallAssignment']['meal_hall_id']];
		      } else {
		          echo '---';
		      }
		      
		  ?>
		</td>
		<td><?php 
		      
		      echo $this->Form->radio('ExceptionMealAssignment.'.$start.'.accept_deny',$options,$attributes)
		     
		    ?>
	&nbsp;</td>
		
		<td>
		
		<?php 
		    
		   echo $this->Form->input('ExceptionMealAssignment.'.$start.'.start_date',array('label'=>false
		 
		 )); 
		?>
		
		&nbsp;</td>
		
		<td>
		<?php 
		    
		   echo $this->Form->input('ExceptionMealAssignment.'.$start.'.end_date',array('label'=>false
		 
		 )); 
		?>
		</td>
		<td>
		<?php 
		    
		   echo $this->Form->input('ExceptionMealAssignment.'.$start.'.meal_hall_id',array('label'=>false
		 
		 )); 
		?>
		</td>
		
		<td>
		<?php 
		  echo $this->Form->input('ExceptionMealAssignment.'.$start.'.remark',array('label'=>false
		 
		 )); ?>
		</td>
	</tr>
	<?php  $start++; ?>
<?php endforeach; ?>
	</table>
	<?php 
	
	     echo $this->Form->submit('Put selected to exception',array('name'=>'saveException','div'=>'false')); 
	    
	    ?>
<?php } ?>
</div>
