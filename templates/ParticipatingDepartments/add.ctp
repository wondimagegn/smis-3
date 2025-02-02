<?php echo $this->Html->script('jquery-1.6.2.min'); ?>  
<?php echo $this->Html->script('jquery-department_placement');?>
<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<h3>Select Departments that will Participate in the Auto Student Placement to Department</h3>
<div class="participatingDepartments form">
<?php echo $this->Form->create('ParticipatingDepartment');?>
	<!--<fieldset> -->
<table><tbody><tr><td>		
<table>
<tbody>
<tr>
<td class='headerfont'><?php echo __(''); ?></td>
</tr>
<tr>
<td>

<?php
		
		echo $this->Form->input('academic_year',array('id'=>'academic_year',
            'label' => 'Academic Year','type'=>'select','options'=>$acyear_array_data,
            'empty'=>"--Select Academic Year--",'selected'=>isset($academic_year)?$academic_year:''));
        // echo $this->Form->input('academic_year',array('value'=>$academic_year,'readonly'=>'readonly'));
?>

</td>
<td></td>
</tr>
<tr>
<td>
<table>

<tr><td class="font"><?php echo "The total number admitted students to $college_name in ".$academic_year." academic year are ".number_format($totalstudents, 0, '.', ',')."."; ?></td></tr>
<tr><td class="font"><?php echo "<strong>Please select departments from your ".$college_name." that will participate in the auto student placement to department.</strong>";?> </td></tr>
<tr><td class="font">&nbsp;</td></tr>

<tr><td><?php echo '<strong>'.$college_name.' Departments</strong>'.$this->Form->input('department_id', 
array('type' => 'select', 'multiple' => 'checkbox','div'=>'input select', 'label' => false));?>
</td>

</td>
</tr>

</table></td>
<td>

<table>
<tr><td class="font"><?php echo __('Others college/institute departments you want to participate in the student auto placement');
?></td></tr>
<tr>
<td>
<?php
		
		echo $this->Form->input('other_college_department', array('id'=>'other_college'));
?>
</td>
</tr>

		
<tr>

<td>
<div id="extra">

<?php 
//echo $this->Form->input('college_id', array('id'=>'college_id','empty' => '-- Select --')); 
?>
<table>
<?php 

if(!empty($otherdep)){
    foreach($otherdep as $key=>$value){
        
        echo '<tr><td class="font">'.$key.'</td></tr>';
        foreach($value as $k=>$v){
                echo '<tr><td><input type="checkbox" name="data[ParticipatingDepartment][department_id][]" value='.$k.' id="ParticipatingDepartmentDepartmentId'.$k.'">';

                echo '<label style="width:auto">'.$v.'</label></td></tr>';
        }
    }
}

?>
</table>
<div id="otherscollegestudnetcount">
</div>
<div id="department_id">


</div>
</td>
</div>
</tr>
<tr><td>

</td></tr></table></td>

</td>
</tr>
<tr>
<td><?php echo $this->Form->end(array('label'=>__('Submit'),'class'=>'tiny radius button bg-blue'));?></td>
</tr>

</tbody>
</table>
</td>
</tr></tbody></table>
</div>


	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
