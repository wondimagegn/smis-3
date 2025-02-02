<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div class="classPeriodCourseConstraints form">
<?php echo $this->Form->create('ClassPeriodCourseConstraint');?>
	<fieldset>
 		<legend><?php echo __('Edit Class Period Course Constraint'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('published_course_id');
		echo $this->Form->input('week_day');
		echo $this->Form->input('period');
		echo $this->Form->input('type');
		echo $this->Form->input('active');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit'));?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $this->Form->value('ClassPeriodCourseConstraint.id')), null, sprintf(__('Are you sure you want to delete # %s?'), $this->Form->value('ClassPeriodCourseConstraint.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Class Period Course Constraints'), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Published Courses'), array('controller' => 'published_courses', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Published Course'), array('controller' => 'published_courses', 'action' => 'add')); ?> </li>
	</ul>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
