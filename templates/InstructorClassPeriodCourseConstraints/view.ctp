<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="instructorClassPeriodCourseConstraints view">
<h2><?php echo __('Instructor Class Period Course Constraint');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $instructorClassPeriodCourseConstraint['InstructorClassPeriodCourseConstraint']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Staff'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($instructorClassPeriodCourseConstraint['Staff']['id'], array('controller' => 'staffs', 'action' => 'view', $instructorClassPeriodCourseConstraint['Staff']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Class Period'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($instructorClassPeriodCourseConstraint['ClassPeriod']['week_day'], array('controller' => 'class_periods', 'action' => 'view', $instructorClassPeriodCourseConstraint['ClassPeriod']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('College'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($instructorClassPeriodCourseConstraint['College']['name'], array('controller' => 'colleges', 'action' => 'view', $instructorClassPeriodCourseConstraint['College']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Academic Year'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $instructorClassPeriodCourseConstraint['InstructorClassPeriodCourseConstraint']['academic_year']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Semester'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $instructorClassPeriodCourseConstraint['InstructorClassPeriodCourseConstraint']['semester']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Active'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $instructorClassPeriodCourseConstraint['InstructorClassPeriodCourseConstraint']['active']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
