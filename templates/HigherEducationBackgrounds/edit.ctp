<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="higherEducationBackgrounds form">
<?php echo $this->Form->create('HigherEducationBackground');?>
	<fieldset>
		<legend><?php echo __('Edit Higher Education Background'); ?></legend>
	<?php
echo $this->Form->input('id');
		echo $this->Form->input('name');
		echo $this->Form->input('field_of_study');
		echo $this->Form->input('diploma_awarded');
		echo $this->Form->input('date_graduated');
		echo $this->Form->input('cgpa_at_graduation');
		echo $this->Form->input('city');
	?>
	</fieldset>
<?php echo $this->Form->end(array('label'=>__('Submit'),
'class'=>'tiny radius button bg-blue'));?>
</div>

	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
