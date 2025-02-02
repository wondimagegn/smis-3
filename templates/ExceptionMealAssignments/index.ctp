<?php echo $this->Form->create('ExceptionMealAssignment');?>
<div class="exceptionMealAssignments index">
   <p class="fs16">Please search the student you want view who are in the exception list.</p>
<table class="fs13 small_padding">
	<tr> 
		<td style="width:20%">First Letter of Name:</td>
		<td style="width:30%"><?php echo $this->Form->input('Search.name',array('label'=>false)); ?></td>
		<td style="width:20%">Student Number/ID:</td>
		<td style="width:30%"><?php echo $this->Form->input('Search.studentnumber',array('label'=>false)); 7
		?></td>
	</tr>
	<tr> 
		<td style="width:20%">Meal Hall:</td>
		<td style="width:30%"><?php echo $this->Form->input('Search.meal_hall_id',array('label'=>false,'empty'=>' ')); ?></td>
		<td style="width:20%">&nbsp;</td>
		<td style="width:30%">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="4"><?php echo $this->Form->Submit('Search',
		array('div'=>false,'name'=>'continue')); ?></td>	
	</tr>
</table>
<?php if (isset($exceptionMealAssignments) && !empty($exceptionMealAssignments)) { ?>
	<h2><?php echo __('Exception Meal Assignments');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th>S.N<u>o</u></th>
			<th><?php echo $this->Paginator->sort('student_id');?></th>
			<th><?php echo $this->Paginator->sort('meal_hall_id');?></th>
			<th><?php echo $this->Paginator->sort('accept_deny');?></th>
			<th><?php echo $this->Paginator->sort('start_date');?></th>
			<th><?php echo $this->Paginator->sort('end_date');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	$start = $this->Paginator->counter('%start%');
	foreach ($exceptionMealAssignments as $exceptionMealAssignment):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $start++; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($exceptionMealAssignment['Student']['full_name'], array('controller' => 'students', 'action' => 'view', $exceptionMealAssignment['Student']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($exceptionMealAssignment['MealHall']['name'], array('controller' => 'meal_halls', 'action' => 'view', $exceptionMealAssignment['MealHall']['id'])); ?>
		</td>
		<td><?php 
		    if ($exceptionMealAssignment['ExceptionMealAssignment']['accept_deny'] == 1) {
		        echo 'Allowed';
		    } else if ($exceptionMealAssignment['ExceptionMealAssignment']['accept_deny']==-1) {
		        echo 'Denied';
		    }
		    
		    ?>&nbsp;</td>
		<td><?php echo $exceptionMealAssignment['ExceptionMealAssignment']['start_date']; ?>&nbsp;</td>
		<td><?php echo $exceptionMealAssignment['ExceptionMealAssignment']['end_date']; ?>&nbsp;</td>
	
		<td class="actions">
			
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $exceptionMealAssignment['ExceptionMealAssignment']['id']), null, sprintf(__('Are you sure you want to delete  %s is from exception meal ?'), $exceptionMealAssignment['Student']['full_name'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%')
	));
	?>	</p>

	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('previous'), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next') . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>
<?php } ?>
</div>
