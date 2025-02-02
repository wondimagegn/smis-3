<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="dismissals form">
<?php echo $this->Form->create('Dismissal');?>
	<fieldset>
		<legend><?php echo __('Add Dismissal'); ?></legend>
	<?php
		echo $this->Form->input('student_id');
		echo $this->Form->input('reason');
		echo $this->Form->input('request_date');
		echo $this->Form->input('acceptance_date');
		echo $this->Form->input('for_good');
		echo $this->Form->input('dismisal_date');
	?>
	</fieldset>
<?php echo $this->Form->end(array('label'=>__('Submit'),
'class'=>'tiny radius button bg-blue'));?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
