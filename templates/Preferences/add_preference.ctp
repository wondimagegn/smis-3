<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="preferences form">
<?php if(isset($departmentcount) && $departmentcount>0) { ?>
<?php echo $this->Form->create('Preference');?>

 		<div class="smallheading"><?php echo __('Add Department Preferences'); ?></div>
	<?php
		
		echo '<table><tbody>';
		
		echo '<tr><td class="font"> College: '.$collegename.'</td></tr>';
		echo '<tr><td class="font"> Name: '.$studentname.'</td></tr>';
		echo '<tr><td class="font"> Number: '.$studentnumber.'</td></tr>';
		echo '<tr><td class="font"> Academic Year: '.$acyear.'</td></tr>';
		if(isset($departmentcount)){
		    for($i=1;$i<=$departmentcount;$i++) {
		        echo $this->Form->hidden('Preference.'.$i.'.accepted_student_id');
		        echo $this->Form->hidden('Preference.'.$i.'.academicyear',array('value'=>$acyear));
			    echo '<tr><td>'.$this->Form->input('Preference.'.$i.'.department_id',array('label'=>'Preference '.$i,'empty'=>'--select department--','value'=>(!empty($this->request->data)?$this->request->data['Preference'][$i]['department_id']:''))).'</td></tr>';
	        echo $this->Form->hidden('Preference.'.$i.'.user_id',array('value'=>$user_id));
		    echo $this->Form->hidden('Preference.'.$i.'.accepted_student_id',array('value'=>$accepted_student_id));
		    echo $this->Form->hidden('Preference.'.$i.'.college_id',array('value'=>$college_id));
		    echo $this->Form->hidden('Preference.'.$i.'.preferences_order',array('value'=>$i));
			
		    }
		} else {
		    
		}
		echo '</table></tbody>';
	
	?>
<?php echo $this->Form->end(array('label'=>__('Submit'),
'class'=>'tiny radius button bg-blue'));?>
<?php } ?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
