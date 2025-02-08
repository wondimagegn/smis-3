<div class="graduationStatuses form">
<?php
echo $this->Form->create('GraduationStatus');
echo $this->Form->input('id');
?>
<div class="smallheading"><?php echo __('Edit Graduation Status'); ?></div>
<table class="fs12">
	<tr>
		<td style="width:15%">Program</td>
		<td style="width:85%"><?php echo $this->Form->input('program_id', array('label' => false, 'style' => 'width:250px')); ?></td>
	</tr>
	<tr>
		<td>CGPA</td>
		<td><?php echo $this->Form->input('cgpa', array('label' => false)); ?></td>
	</tr>
	<tr>
		<td>Status</td>
		<td><?php echo $this->Form->input('status', array('label' => false)); ?></td>
	</tr>
	<tr>
		<td>Academic Year</td>
		<td><?php echo $this->Form->year('academic_year', Configure::read('Calendar.universityEstablishement'), date('Y')+1, (isset($this->request->data['GraduationStatus']['academic_year']) ? $this->request->data['GraduationStatus']['academic_year'] : date('Y')), array('empty' => false, 'label' => false, 'div' => false, 'style' => 'width:100px', 'class' => 'fs14')); ?></td>
	</tr>
	<tr>
		<td>Applicable for Current Student</td>
		<td><?php echo $this->Form->input('applicable_for_current_student', array('label' => false)); ?></td>
	</tr>
</table>
<?php echo $this->Form->end(__('Update Graduation Status'));?>
</div>
