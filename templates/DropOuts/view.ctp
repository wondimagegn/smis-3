<div class="equivalentCourses view">
<h2><?php echo __('Equivalent Course');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
	
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Course For Substitued'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $equivalentCourse['CourseForSubstitued']['course_title'] ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php echo __('Course Be Substitued'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $equivalentCourse['CourseBeSubstitued']['course_title']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
