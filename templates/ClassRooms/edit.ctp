<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
              
<div class="classRooms form">
<?php echo $this->Form->create('ClassRoom');?>
	<fieldset>
 		<legend><?php echo __('Edit Class Room'); ?></legend>
	<?php
		echo $this->Form->input('id');
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
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $this->Form->value('ClassRoom.id')), null, sprintf(__('Are you sure you want to delete # %s?'), $this->Form->value('ClassRoom.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Class Rooms'), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Class Room Blocks'), array('controller' => 'class_room_blocks', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Class Room Block'), array('controller' => 'class_room_blocks', 'action' => 'add')); ?> </li>
	</ul>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
