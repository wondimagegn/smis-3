<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="preferences form">
<h3>Add Student Department Placement Preference</h3>

<table><tbody><tr><td width="10%">
<?php 
echo $this->Form->create('Preference');
echo '<table><tbody><tr><td><div class="smallheading">Academic Year</div>'.$this->Form->input('Search.academicyear',array('id'=>'academic_year',
            'label' => false,'type'=>'select','options'=>$acyear_array_data,
            'empty'=>"--Select Academic Year--",
'selected'=>isset($selectedAcademicYear)?$selectedAcademicYear:'')).'</td></tr>';
echo '<tr><td>'.$this->Form->Submit(__('Continue'),array('div'=>false,'name'=>'searchacademicyear','class'=>'tiny radius button bg-blue')).'</td></tr></tbody></table></td>';     
 ?>
<td width="80%">
<?php

if(!empty($departments) && !empty($accepted_student_id)){	
		echo '<table><tbody>';
		
		echo '<tr><td class="font">Student Name: '.$accepted_student_detail['AcceptedStudent']['full_name'].'</td></tr>';
		echo '<tr><td class="font">Student Number: '.$accepted_student_detail['AcceptedStudent']['studentnumber'].'</td></tr>';
		echo '<tr><td>'.$this->Form->hidden('college_id',array('value'=>$college_id)).'</td></tr>';
		echo '<tr><td>'.$this->Form->input('academicyear',array('id'=>'academicyear',
            'label' => 'Academic Year','value'=>$accepted_student_detail['AcceptedStudent']['academicyear'],'readonly' => 'readonly')).'</td></tr>';
        echo $this->Form->hidden('Preference.accepted_student_id',array('value'=>$accepted_student_detail['AcceptedStudent']['id']));
		
		for($i=1;$i<=$departmentcount;$i++) {

		    echo '<tr><td>'.$this->Form->hidden('Preference.'.$i.'.accepted_student_id',array('value'=>$accepted_student_detail['AcceptedStudent']['id'])).'</td></tr>';
			echo '<tr><td>'.$this->Form->input('Preference.'.$i.'.department_id',array('label'=>'Preference '.$i,'empty'=>'--select department--',
'selected'=>isset($this->request->data['Preference'][$i]['department_id'])? $this->request->data['Preference'][$i]['department_id']:'')).'</td></tr>';
			echo '<tr><td>'.$this->Form->hidden('Preference.'.$i.'.preferences_order',array('value'=>$i)).'</td></tr>';
			
		}
		
		echo '<tr><td>'.$this->Form->Submit(__('Submit Preference'),array('div'=>false,'name'=>'submitpreference',
'class'=>'tiny radius button bg-blue')).'</td></tr>';
		echo '</table></tbody>';
}
?>
</td>
</tr>

<?php if (!empty($acceptedStudents)) {?>

<tr><td colspan=2>
<?php 
echo '<div class="info-box info-message"><span></span>'.__('List of students of '.$selectedAcademicYear.' academic year who doesnt feed their preferences to the system. Please complete their preference. Otherwise the system will assign the students to the deparment least prefered').'</div>';
?>
<table><tbody>
	<tr>
	        <th>S.N<u>o</u></th>
            <th><?php echo __('Full Name');?></th>
			<th><?php echo __('Sex');?></th>
			<th><?php echo __('Student Number');?></th>
			
			
			<th><?php echo ('Placement Type');?></th>
			<th><?php echo ('Actions');?></th>
			
	</tr>
	<?php
	$i = 0;
    $start=1;
	foreach ($acceptedStudents as $acceptedStudent):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
         <td><?php echo $start++; ?>&nbsp;</td>
        <td><?php echo $acceptedStudent['AcceptedStudent']['full_name']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['AcceptedStudent']['sex']; ?>&nbsp;</td>
		<td><?php echo $acceptedStudent['AcceptedStudent']['studentnumber']; ?>&nbsp;</td>
			
		<td><?php echo $acceptedStudent['AcceptedStudent']['placementtype']; ?>&nbsp;</td>
		<td>
		<?php echo $this->Html->link(__('Add Preference'), array('controller'=>'preferences','action' => 'add', $acceptedStudent['AcceptedStudent']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</tbody>
	</table></td></tr>
<?php } ?> 
</tbody></table> 
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
