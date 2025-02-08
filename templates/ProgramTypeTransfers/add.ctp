<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="programTypeTransfers form">
<?php echo $this->Form->create('ProgramTypeTransfer');?>
<?php 
    if (!isset($studentIDs)) {

?>
<table cellpadding="0" cellspacing="0">
<?php 	
        echo '<tr><td class="smallheading">Add Program Transfer For Student</td></tr>';
        
		echo '<tr><td class="font">'.$this->Form->input('studentID',array('label' => 'Student ID')).'</td></tr>';
       echo '<tr><td>'. $this->Form->Submit('Continue',array('name'=>'continue','class'=>'tiny radius button bg-blue','div'=>false)).'</td></tr>';
?>
</table>
<?php 
}
?>


<?php 
        if (isset($studentIDs)) {
        debug($studentIDs);
?>
	<div class="smallheading"><?php echo __('Add Program Type Transfer'); ?></div>
	<table>
	    <tr>
	     <td>
	        <table>
	           <tr>
	           <td class="fs16">
	             <?php echo __('Provide transfered program  which is applicable for the selected student.'); 
	                
	             ?>
	           </td>
	           </tr`>
	            <?php
	            if(isset($student_id) 
	            && !empty($student_id)){
	            	echo $this->Form->hidden('student_id',array('value'=>$student_id));
	            } else{
	            echo $this->Form->hidden('student_id',array('value'=>$student_section_exam_status['StudentBasicInfo']['id']));
	            }
		            
		            echo '<tr>';
		                echo '<td>'.$this->Form->input('program_type_id',
		                array('style'=>'width:250px')).'</td>';
		               
		            echo '</tr>';
		             echo '<tr>';
		                echo '<td>'.$this->Form->input('academic_year',
		                array('style'=>'width:250px','type'=>'select','options'=>$acyear_array_data,
            'empty'=>"--Select Academic Year--",'selected'=>isset($defaultacademicyear)?$defaultacademicyear:'')).'</td>';
		               
		            echo '</tr>';
		             echo '<tr>';
		                echo '<td>'.$this->Form->input('semester',
		                array('style'=>'width:250px',
		                'options'=>array('I'=>'I','II'=>'II',
            'III'=>'III'),'empty'=>'--select semester--')).'</td>';
		               
		            echo '</tr>';
		            echo '<tr>';
		                 echo '<td>'.$this->Form->input('transfer_date').'</td>';
		              
		            echo '</tr>'; 
		         
	        ?>
	        </table>
	    
	    </td>
	    <td><?php 
	       echo $this->element('student_basic');
	      
	    ?></td>
	    
	    </tr>
	 <tr><td> <?php echo $this->Form->Submit('Save',array('name'=>'saveTransfer','class'=>'tiny radius button bg-blue','div'=>false)); 
	 ?>
	 </td></tr>
	</table>
<?php 
}
?>
</div>

	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
