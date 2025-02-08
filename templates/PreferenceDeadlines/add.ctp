<?php 
?>
<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="preferenceDeadlines form">

   <table>
   <tbody>
   <tr><td>
   <?php echo $this->Form->create('PreferenceDeadline');?>
		<div class="smallheading"><?php echo __('Add Preference Deadline'); ?></div>
	</td>
	</tr>
	<?php
		echo '<tr><td>'.$this->Form->input('deadline').'</td></tr>';
		echo $this->Form->hidden('user_id',array('value'=>$user_id));
		echo $this->Form->hidden('college_id',array('value'=>$college_id));
		
		echo '<tr><td>'.$this->Form->input('academicyear',array('id'=>'academicyear',
            'label' => 'Academic Year','type'=>'select','options'=>$acyear_array_data,
            'empty'=>"--Select Academic Year--",'selected'=>isset($selected)?$selected:'')).'</td>
            </tr>';
	?>
     <tr>
<?php echo '<td>'.$this->Form->end(array('label'=>__('Submit'),'class'=>'tiny radius button bg-blue')).'</td>';?>
    </tr>
    </tbody></table>
</div>

	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
