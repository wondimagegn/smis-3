<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="applicablePayments form">
<?php echo $this->Form->create('Payment');?>
<?php 
    if (!isset($studentIDs)) {

?>
<table cellpadding="0" cellspacing="0">
<?php 	
        echo '<tr><td class="smallheading">Add  Payment For Student</td></tr>';
        echo '<tr><td >'.$this->Form->input('academic_year',
		                array('options'=>$acyear_array_data,'style'=>'width:100px',
		                'selected'=>$defaultacademicyear)).'</td></tr>';
		                
		                 echo '<tr><td >'.$this->Form->input('semester',
		                array('options'=>array('I'=>'I','II'=>'II',
            'III'=>'III'))).'</td></tr>';
		echo '<tr><td class="font">'.$this->Form->input('studentID',array('label' => 'Student ID')).'</td></tr>';
       echo '<tr><td>'. $this->Form->Submit('Continue',array('name'=>'continue','class'=>'tiny radius button bg-blue','div'=>false)).'</td></tr>';
?>
</table>
<?php 
}
?>
<?php 
        if (isset($studentIDs)) {
?>
	<div class="smallheading"><?php echo __('Add  Payment'); ?></div>
	<table>
	<tr>
	    <td colspan=2>
	     <?php  echo $this->element('student_basic'); ?>
	    </td>
	</tr>
	    <tr>
	     <td>
	        <table>
	           <tr>
	               <td class="fs16">
	                 <?php echo __('Those checked are payment Applicable to the selected student for <strong> '.$applicable_payments['ApplicablePayment']['academic_year'].' academic year and semester '.$applicable_payments['ApplicablePayment']['semester'].' </strong> .'); 
	                    
	                 ?>
	               </td>
	           </tr>
	            <?php
		            echo $this->Form->hidden('student_id',array('value'=>$student_section_exam_status['StudentBasicInfo']['id']));
		             echo $this->Form->hidden('academic_year',array('value'=>$applicable_payments['ApplicablePayment']['academic_year']));
		              echo $this->Form->hidden('semester',array('value'=>$applicable_payments['ApplicablePayment']['semester']));
		           
		            echo '<tr>';
		                echo '<td>'.$this->Form->input('tutition_fee',
		                array('checked'=>($applicable_payments['ApplicablePayment']['tutition_fee'] == 1 ? 'checked' : false),'disabled'=>true)).'</td>';
		                if($applicable_payments['ApplicablePayment']['tutition_fee']==1) {
		                   echo $this->Form->hidden('tutition_fee',array('value'=>1)); 
		                }
		               
		               
		            echo '</tr>';
		            echo '<tr>';
		                 echo '<td>'.$this->Form->input('meal',
		                 array('checked'=>($applicable_payments['ApplicablePayment']['meal'] == 1 ? 'checked' : false),'disabled'=>true)).'</td>';
		                 if($applicable_payments['ApplicablePayment']['meal']==1) {
		                   echo $this->Form->hidden('meal',array('value'=>1)); 
		                }
		              
		            echo '</tr>'; 
		             echo '<tr>';
		                 echo '<td>'.$this->Form->input('accomodation',array('checked'=>($applicable_payments['ApplicablePayment']['accomodation'] == 1 ? 'checked' : false),'disabled'=>true)).'</td>';
		                 if($applicable_payments['ApplicablePayment']['accomodation']==1) {
		                   echo $this->Form->hidden('accomodation',array('value'=>1)); 
		                }
		                 
		            echo '</tr>'; 
		            
		              echo '<tr>';
		                 echo '<td>'.$this->Form->input('health',
		                 array('checked'=>($applicable_payments['ApplicablePayment']['health'] == 1 ? 'checked' : false),'disabled'=>true)).'</td>';
		              if($applicable_payments['ApplicablePayment']['health']==1) {
		                   echo $this->Form->hidden('health',array('value'=>1)); 
		                }
		            echo '</tr>'; 
		             
		            
		            //
		         
	        ?>
	        </table>
	    
	    </td>
	    <td style='width:55%'>
	      <?php 
	        echo '<table>';
	           echo '<tr>';
		                echo '<td >'.$this->Form->input('payment_date',
		                array('type'=>'date',
		                'minYear'=>date('Y-m-d')-10,'maxYear'=>date('Y-m-d'))).'</td>';
		            
		            echo '</tr>';
		           /* echo '<tr>';
		                echo '<td >'.$this->Form->input('academic_year',
		                array('options'=>$acyear_array_data,'style'=>'width:100px')).'</td>';
		              
		            echo '</tr>';
		          
		           echo '<tr>';
		                echo '<td >'.$this->Form->input('semester',
		                array('options'=>array('I'=>'I','II'=>'II',
            'III'=>'III'))).'</td>';
		              
		            echo '</tr>';
		            */
		            
		            echo '<tr>';
		                echo '<td >'.$this->Form->input('reference_number').'</td>';
		              
		            echo '</tr>';
		             echo '<tr>';
		                echo '<td >'.$this->Form->input('fee_amount').'</td>';
		              
		            echo '</tr>';
	        echo '</table>';
	      
	      ?>
	    
	    </td>
	    
	    </tr>
	 <tr><td> <?php echo $this->Form->Submit('Save',array('name'=>'saveApplicablePayment','class'=>'tiny radius button bg-blue','div'=>false)); 
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
