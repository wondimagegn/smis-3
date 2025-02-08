<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="sectionSplitForExams form">
<?php echo $this->Form->create('SectionSplitForExam');?>
	<fieldset>
 		<legend><?php echo __('Edit Section Split For Exam'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('section_id');
		echo $this->Form->input('published_course_id');
	?>
	</fieldset>
<?php echo $this->Form->end(array('label'=>__('Submit'),'class'=>'tiny radius button bg-blue'));?>
</div>

	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
