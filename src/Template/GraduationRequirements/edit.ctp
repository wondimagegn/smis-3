<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="graduationRequirements form">
<?php
echo $this->Form->create('GraduationRequirement');
echo $this->Form->input('id');
?>
<div class="smallheading"><?php echo __('Edit Graduation Requirement'); ?></div>
<table>
	<tr>
		<td style="width:15%">CGPA:</td>
		<td style="width:85%"><?php echo $this->Form->input('cgpa', array('label' => false)); ?></td>
	</tr>
	<tr>
		<td>Program:</td>
		<td><?php echo $this->Form->input('program_id', array('label' => false)); ?></td>
	</tr>
	<tr>
		<td>Admission Year From (Application of the rule starting):</td>
		<td><?php echo $this->Form->input('academic_year', array('options' => $acyear_array_data, 'label' => false)); ?></td>
	</tr>
</table>
<?php echo $this->Form->end(array('label'=>__('Update'),
'class'=>'tiny radius button bg-blue'));?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
