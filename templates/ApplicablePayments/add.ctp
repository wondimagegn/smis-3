<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="applicablePayments form">
<?php echo $this->Form->create('ApplicablePayment');?>
<?php 
    if (!isset($studentIDs)) {

?>
<table cellpadding="0" cellspacing="0">
<?php 	
        echo '<tr><td class="smallheading">Add Applicable Payment For Student</td></tr>';
        
		echo '<tr><td class="font">'.$this->Form->input('studentID',array('label' => 'Student ID/Number')).'</td></tr>';
       echo '<tr><td>'. $this->Form->Submit('Continue',array('name'=>'continue','div'=>false,'class'=>'tiny radius button bg-blue')).'</td></tr>';
?>
</table>
<?php 
}
?>
<?php 
        if (isset($studentIDs)) {
?>
	<div class="smallheading"><?php echo __('Add Applicable Payment'); ?></div>
	<table>
	    <tr>
	     <td>
	        <table>
	           <tr>
	           <td class="fs16">
	             <?php echo __('Check those payment which is applicable for the selected student.'); 
	                
	             ?>
	           </td>
	           </tr`>
	            <?php
		            echo $this->Form->hidden('student_id',array('value'=>$student_section_exam_status['StudentBasicInfo']['id']));
		          
		            echo '<tr>';
		                echo '<td>'.$this->Form->input('tutition_fee').'</td>';
		               
		            echo '</tr>';
		            echo '<tr>';
		                 echo '<td>'.$this->Form->input('meal').'</td>';
		              
		            echo '</tr>'; 
		             echo '<tr>';
		                 echo '<td>'.$this->Form->input('accomodation').'</td>';
		            echo '</tr>'; 
		            
		              echo '<tr>';
		                 echo '<td>'.$this->Form->input('health').'</td>';
		            echo '</tr>'; 
		              echo '<tr>';
		                echo '<td >'.$this->Form->input('academic_year',
		                array('options'=>$acyear_array_data,'selected'=>isset($this->request->data['ApplicablePayment']['academic_year']) ? $this->request->data['ApplicablePayment']['academic_year'] : $defaultacademicyear,'style'=>'width:100px')).'</td>';
		              
		            echo '</tr>';
		          
		           echo '<tr>';
		                echo '<td >'.$this->Form->input('semester',
		                array('options'=>array('I'=>'I','II'=>'II',
            'III'=>'III'))).'</td>';
		              
		            echo '</tr>'; 
		              
		            echo '<tr>';
		                echo '<td>'.$this->Form->input('sponsor_type').'</td>';
		              
		            echo '</tr>';
		            
		             echo '<tr>';
		                echo '<td >'.$this->Form->input('sponsor_name').'</td>';
		              
		            echo '</tr>';
		             echo '<tr>';
		                echo '<td >'.$this->Form->input('sponsor_address').'</td>';
		              
		            echo '</tr>';
		         
	        ?>
	        </table>
	    
	    </td>
	    <td><?php 
	       echo $this->element('student_basic');
	      
	    ?></td>
	    
	    </tr>
	 <tr><td> <?php echo $this->Form->Submit('Save',array('name'=>'saveApplicablePayment','div'=>false,'class'=>'tiny radius button bg-blue')); 
	 ?>
	 </td></tr>
	</table>
	
	
    <?php //echo $this->Form->end(__('Submit'));?>
<?php 
}
?>
</div>

	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
