<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="highSchoolEducationBackgrounds form">
<?php echo $this->Form->create('HighSchoolEducationBackground');?>
	<fieldset>
		<legend><?php echo __('Edit High School Education Background'); ?></legend>
	<?php
		echo $this->Form->input('name');
		echo $this->Form->input('town');
		echo $this->Form->input('zone');
		echo $this->Form->input('region');
		echo $this->Form->input('school_level');
	?>
	</fieldset>
<?php echo $this->Form->end(array('label'=>__('Submit'),
'class'=>'tiny radius button bg-blue'));?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
