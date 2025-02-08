<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="disciplines form">
<?php echo $this->Form->create('Discipline');?>
<?php 
    if (!isset($studentIDs)) {

?>
<table cellpadding="0" cellspacing="0">
<?php 	
        echo '<tr><td class="smallheading">Add  Discipline Case </td></tr>';
      
		echo '<tr><td class="font">'.$this->Form->input('Search.studentID',array('label' => 'Student ID')).'</td></tr>';
       echo '<tr><td>'. $this->Form->Submit('Continue',array('name'=>'continue','div'=>false)).'</td></tr>';
?>
</table>
<?php 
}
?>
<?php 
        if (isset($studentIDs)) {
?>
	<div class="smallheading"><?php echo __('Add  Discipline Detail'); ?></div>
	<table>
	<tr>
	    <td colspan=2>
	     <?php  echo $this->element('student_basic'); ?>
	    </td>
	</tr>
	    <tr>
	     <td colspan="2">
	        <table>
	           <tr>
	               <td class="fs16">
	                 <?php echo __('Provide the displine case for  the selected student.'); 
	                 ?>
	               </td>
	           </tr>
	            <?php
		            echo $this->Form->hidden('Discipline.student_id',array('value'=>$student_section_exam_status['StudentBasicInfo']['id']));
		            echo '<tr>';
		                echo '<td>'.$this->Form->input('Discipline.discipline_taken_date',array('label' =>'Discipline Date',  'minYear' => Configure::read('Calendar.applicationStartYear'), 'maxYear' => date('Y'), 'orderYear' => 'desc')).'</td>';
		               // echo '<td>'.$this->Form->input('taken_date',array('label' =>'Discipline Date')).'</td>';
		               
		            echo '</tr>'; 
		            echo '<tr>';
		                echo '<td>'.$this->Form->input('Discipline.title').'</td>';
		              
		
		               
		            echo '</tr>';
		            echo '<tr>';
		               echo '<td>'.$this->Form->input('Discipline.description').'</td>';
		              
		            echo '</tr>'; 
		          
		            
		            //
		         
	        ?>
	        </table>
	    
	    </td>
	 
	    </tr>
	 <tr><td> <?php echo $this->Form->Submit('Save',array('name'=>'saveDisplinceCase','div'=>false)); 
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
