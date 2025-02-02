<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="graduationStatuses form">
<?php echo $this->Form->create('GraduationStatus');?>
<div class="smallheading"><?php echo __('Add Graduation Status'); ?></div>
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
		<td><?php 

echo $this->Form->year('academic_year', Configure::read('Calendar.universityEstablishement'), date('Y')+1, array('empty' => false, 'label' => false, 'div' => false, 'style' => 'width:100px', 'class' => 'fs14',
'value'=> (isset($this->request->data['GraduationStatus']['academic_year']) ? $this->request->data['GraduationStatus']['academic_year'] : date('Y'))));
 
//echo $this->Form->year('academic_year', 2000, date('Y'));
?></td>
	</tr>
	<tr>
		<td>Applicable for Current Student</td>
		<td><?php echo $this->Form->input('applicable_for_current_student', array('label' => false)); ?></td>
	</tr>
</table>
<?php echo $this->Form->end(array('label'=>__('Record Graduation Status'),'class'=>'tiny radius button bg-blue'));?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
