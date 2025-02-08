<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="examExcludedDateAndSessions form">
<?php echo $this->Form->create('ExamExcludedDateAndSession');?>
	<fieldset>
		<legend><?php echo __('Edit Exam Excluded Date And Session'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('exam_period_id');
		echo $this->Form->input('excluded_date');
		echo $this->Form->input('session');
	?>
	</fieldset>
<?php echo $this->Form->end(array('label'=>__('Submit'),'class'=>'tiny radius button bg-blue'));?>
</div>

	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
