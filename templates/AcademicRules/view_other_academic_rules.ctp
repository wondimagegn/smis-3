<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
	     <h6 class="box-title">
		<?php echo __('Other Academic Rule'); ?>
	     </h6>
	  </div>
	  <div class="large-12 columns">
             <div class="academicRules index">
	<h2><?php echo __('Other Academic Rules');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo 'S.No';?></th>
			<th><?php echo 'Department';?></th>
			<th><?php echo 'Program';?></th>
			<th><?php echo 'Program Type';?></th>
			<th><?php echo 'Year Level';?></th>
			<th><?php echo 'Curriculum'; ?></th>
			<th><?php echo 'Course Category/Module';?></th>
			<th><?php echo 'Academic Status';?></th>
			<th><?php echo 'Grade';?></th>
		    <th><?php echo 'Number of Courses'; ?></th>
		    <th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	
	foreach ($otherAcademicRules as $academicRule):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
		
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $i; ?>&nbsp;</td>
		<td><?php echo $academicRule['OtherAcademicRule']['department_id'];?></td>
		<td><?php echo $academicRule['OtherAcademicRule']['program_id'];?></td>
		<td><?php  echo $academicRule['OtherAcademicRule']['program_type_id'];?></td>
		<td><?php echo $academicRule['OtherAcademicRule']['year_level_id'];?></td>
		<td><?php echo $academicRule['OtherAcademicRule']['curriculum_id']; ?></td>
		<td><?php echo $academicRule['OtherAcademicRule']['course_category_id'];?></td>
		<td><?php echo $academicRule['OtherAcademicRule']['academic_status_id'];?></td>
		<td><?php echo  $academicRule['OtherAcademicRule']['grade'];?></th>
		<td><?php echo  $academicRule['OtherAcademicRule']['number_courses']; ?></td>
		
		<td class="actions">
			
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit_other_academic_rules', $academicRule['OtherAcademicRule']['id'])); ?>
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete_other_ar', $academicRule['OtherAcademicRule']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $academicRule['OtherAcademicRule']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
