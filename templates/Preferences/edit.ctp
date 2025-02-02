<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="preferences form">
<?php echo $this->Form->create('Preference');?>
	<fieldset>
 		<legend><?php echo __('Edit Preference'); ?></legend>
	<?php
	    echo '<table><tbody>';
		echo '<tr><td>'.$this->Form->input('accepted_student_id').'</td></tr>';
		echo '<tr><td>'.$this->Form->input('academicyear',array('id'=>'academicyear',
            'label' => 'Academic Year','value'=>$current_acyear,'readonly' => 'readonly')).'</td></tr>';
        echo '<tr><td>'.$this->Form->hidden('user_id',array('value'=>$user_id)).'</td></tr>';
        
        for($i=1;$i<=$departmentcount;$i++) {
		    echo '<tr><td>'.$this->Form->hidden('Preference.'.$i.'.accepted_student_id').'</td></tr>';
			echo '<tr><td>'.$this->Form->input('Preference.'.$i.'.department_id',array('label'=>'Preference '.$i)).'</td></tr>';
			echo '<tr><td>'.$this->Form->hidden('Preference.'.$i.'.preferences_order',array('value'=>$i)).'</td></tr>';
			// Model.$id.field
		}
		echo '</table></tbody>';
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>


	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
