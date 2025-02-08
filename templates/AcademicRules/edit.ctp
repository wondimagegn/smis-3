<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
	     <h6 class="box-title">
		<?php echo __('Add Academic Rule'); ?>
	     </h6>
	  </div>
	  <div class="large-12 columns">
		
<div class="academicRules form">
<?php echo $this->Form->create('AcademicRule');?>
<div style="padding-bottom:20px;"></div>
	<fieldset>
		<legend class="smallheading"><?php echo __('Edit Academic Rule'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name');
		echo $this->Form->input('from');
		echo $this->Form->input('to');
		echo $this->Form->input('AcademicStand');
	?>
	</fieldset>
<?php echo $this->Form->end(array('label'=>__('Submit'),
'class'=>'tiny radius button bg-blue'));?>
</div>

	  </div>
	</div>
      </div>
</div>
