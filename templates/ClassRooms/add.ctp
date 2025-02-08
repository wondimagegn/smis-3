<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="classRooms form">
<?php echo $this->Form->create('ClassRoom');?>
	<fieldset>
 		<legend><?php echo __('Add Class Room'); ?></legend>
	<?php
		echo $this->Form->input('class_room_block_id');
		echo $this->Form->input('room_code');
		echo $this->Form->input('available_for_lecture');
		echo $this->Form->input('available_for_exam');
		echo $this->Form->input('lecture_capacity');
		echo $this->Form->input('exam_capacity');
	?>
	</fieldset>
<?php echo $this->Form->end(array('label'=>__('Submit'),'class'=>'tiny radius button bg-blue'));?>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
