<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="courseSplitSections form">
<?php echo $this->Form->create('CourseSplitSection');?>
	<fieldset>
		<legend><?php echo __('Edit Course Split Section'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('section_split_for_published_course_id');
		echo $this->Form->input('section_name');
	?>
	</fieldset>
<?php echo $this->Form->end(array('label'=>__('Submit'),
'class'=>'tiny radius button bg-blue'));?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
