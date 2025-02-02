<?php echo $this->Form->create('InstructorEvalutionQuestion'); ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
	     <h6 class="box-title">
			<?php echo __('Update Instructor Evalution Question'); ?>
	     </h6>
	  </div>
	  <div class="large-12 columns">
              	<?php
              	echo $this->Form->input('id');
      		    echo $this->Form->input('question',array('label'=>'Question In English'));	
      		    echo $this->Form->input('question_amharic',
      		     array('label'=>'Question In Amharic'));	
 
               echo $this->Form->input('type',array('options'=>
                array('objective'=>'objective','open-ended'=>'open-ended'))); 
                echo $this->Form->input('for',array('options'=>
                array('student'=>'Student','colleague'=>'Colleague','dep-head'=>'Department Head'))); 
                
                echo $this->Form->input('active');	

      		    ?>
	 
<?php 
        echo "<tr><td>".$this->Form->end(array('label'=>__('Update'),'class'=>'tiny radius button bg-blue')).'</td></tr>';
        echo "</tbody></table>";
?>
	  </div>
       </div>
    </div>
</div>
