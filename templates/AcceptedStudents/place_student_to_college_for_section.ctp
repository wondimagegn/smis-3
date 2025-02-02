<?php echo $this->Form->create('AcceptedStudent', array('action' => 'place_student_to_college_for_section'));?> 
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            	
<div>
<?php if(!isset($show_list_generated) || empty($acceptedStudents)) { ?>
<div class="smallheading"><?php echo __('Place Student To College for Section Management')?></div>
<?php if(!isset($show_list_generated) || empty($acceptedStudents)) { ?>
<table cellpadding="0" cellspacing="0"><tr> 
	<?php 
			echo '<td>'.$this->Form->input('AcceptedStudent.academicyear',array('id'=>'academicyear',
                'label' => 'Academic Year','type'=>'select','options'=>$acyear_array_data,
                'empty'=>"--Select Academic Year--",'selected'=>isset($selectedsacdemicyear)?$selectedsacdemicyear:'')).'</td>';
            echo '<td>'. $this->Form->input('AcceptedStudent.college_id',array('empty'=>"--Select College--")).'</td></tr>';
            echo '<tr><td>'. $this->Form->input('AcceptedStudent.program_id',array('empty'=>"--Select Program--")).'</td>'; 
            echo '<td>'. $this->Form->input('AcceptedStudent.program_type_id',array('empty'=>"--Select Program Type--")).'</td></tr>'; 
            ?>
	<tr><td><?php echo $this->Form->submit('Place To College of Their Assigned Campus',array('name'=>'search','div'=>'false','class'=>'tiny radius button bg-blue')); ?> </td>	
	<td><?php echo $this->Form->submit('Back MoE Assigned College',array('name'=>'backtomoe','div'=>'false','class'=>'tiny radius button bg-blue')); ?> </td>	
		
</tr>

</table>
<?php } ?>
<?php 
}

if(!empty($acceptedStudents)){
?>

<?php 
} else if(empty($acceptedStudents) && !($isbeforesearch)){
    echo "<div class='info-box info-message'> <span></span> No Accepted students without college assignment in these selected criteria</div>";
}
?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
<?php  
echo $this->Form->end();
?>
