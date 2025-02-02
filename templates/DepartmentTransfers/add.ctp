<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="departmentTransfers form">
<?php echo $this->Form->create('DepartmentTransfer');?>
	<fieldset>
		<legend><?php echo __('Add Department Transfer'); ?></legend>
	<?php
		echo $this->Form->input('department_id');
		echo $this->Form->input('student_id');
		echo $this->Form->input('minute_number');
		echo $this->Form->input('transfer_request_date');
		echo $this->Form->input('receiver_department_approval');
		echo $this->Form->input('receiver_department_approval_date');
		echo $this->Form->input('receiver_department_remark');
		echo $this->Form->input('sender_college_approval');
		echo $this->Form->input('sender_college_approval_date');
		echo $this->Form->input('sender_college_remark');
		echo $this->Form->input('receiver_college_approval');
		echo $this->Form->input('receiver_college_approval_date');
		echo $this->Form->input('receiver_college_remark');
	?>
	</fieldset>
<?php echo $this->Form->end(array('label'=>__('Submit'),
'class'=>'tiny radius button bg-blue'));?>
</div>


	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
