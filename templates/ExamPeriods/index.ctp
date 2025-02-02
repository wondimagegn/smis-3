<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
            
<div class="examPeriods index">
<?php echo $this->Form->create('ExamPeriod');?>
	<div class="smallheading"><?php echo __('List of Exam Periods');?></div>
	<div class="font"><?php echo 'Colege/Institute: '.$college_name ?></div>
	<div class="font"><?php echo __('Optional search parameters');?> </div>
<table cellpadding="0" cellspacing="0">
	<?php 
		echo '<tr><td class="font"> Academic Year</td>';
		echo '<td>'.$this->Form->input('academic_year',array('label'=>false,'type'=>'select','options'=>$acyear_array_data,'empty'=>"All", 'style'=>'width:150PX')).'</td>';
        echo '<td class="font"> Semester</td>';
		echo '<td >'.$this->Form->input('semester',array('label'=>false,'options'=>array('I'=>'I','II'=>'II','III'=>'III'), 'empty'=>'All', 'style'=>'width:150PX')).'</td>'; 
		echo '<td class="font"> Program</td>';
        echo '<td>'. $this->Form->input('program_id',array('label' => false,'empty'=>"All", 'style'=>'width:150PX')).'</td></tr>'; 
        echo '<tr><td class="font"> Program Type</td>';
        echo '<td>'. $this->Form->input('program_type_id',array('label' => false,'style'=>'width:150PX','empty'=>"All")).'</td>'; 
        echo '<td class="font"> Year Level</td>';
		echo '<td>'. $this->Form->input('year_level_id',array('label' => false, 'style'=>'width:150PX', 'empty'=>"All")).'</td></tr>';
		echo '<tr><td class="font"> Start Date</td>';
		echo '<td colspan="2">'.$this->Form->input('start_date', array('label' => false, 'style'=>'width:80PX','type'=>'date','dateFormat' => 'MDY', 'minYear' => Configure::read('Calendar.applicationStartYear'),'maxYear' => date('Y'), 'orderYear' => 'asc','default'=>false)).'</td>';
		echo '<td class="font"> End Date</td>';
		echo '<td colspan="2">'.$this->Form->input('end_date', array('label' => false, 'style'=>'width:80PX','type'=>'date','dateFormat' => 'MDY', 'minYear' => Configure::read('Calendar.applicationStartYear'),'orderYear' => 'asc')).'</td></tr>';

        echo '<tr><td colspan="6">'.$this->Form->end(array('label'=>__('Search'),
'class'=>'tiny radius button bg-blue')).'</td></tr>'; 
	?> 
</table>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo ('S.N<u>o</u>');?></th>
			<th><?php echo $this->Paginator->sort('college_id');?></th>
			<th><?php echo $this->Paginator->sort('program_id');?></th>
			<th><?php echo $this->Paginator->sort('program_type_id');?></th>
			<th><?php echo $this->Paginator->sort('academic_year');?></th>
			<th><?php echo $this->Paginator->sort('semester');?></th>
			<th><?php echo $this->Paginator->sort('year_level_id');?></th>
			<th><?php echo $this->Paginator->sort('start_date');?></th>
			<th><?php echo $this->Paginator->sort('end_date');?></th>
			<th><?php echo $this->Paginator->sort('default_number_of_invigilator_per_exam');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	$count = $this->Paginator->counter('%start%');
	foreach ($examPeriods as $examPeriod):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $count++; ?>&nbsp;</td>
		<td><?php echo $examPeriod['College']['name']; ?>&nbsp;</td>
		<td><?php echo $examPeriod['Program']['name']; ?>&nbsp;</td>
		<td><?php echo $examPeriod['ProgramType']['name']; ?>&nbsp;</td>
		<td><?php echo $examPeriod['ExamPeriod']['academic_year']; ?>&nbsp;</td>
		<td><?php echo $examPeriod['ExamPeriod']['semester']; ?>&nbsp;</td>
		<td><?php echo $examPeriod['YearLevel']['name']; ?>&nbsp;</td>
		<td><?php echo $this->Format->short_date($examPeriod['ExamPeriod']['start_date']); ?>&nbsp;</td>
		<td><?php echo $this->Format->short_date($examPeriod['ExamPeriod']['end_date']); ?>&nbsp;</td>
		<td><?php echo $examPeriod['ExamPeriod']['default_number_of_invigilator_per_exam']; ?>&nbsp;</td>
		<td class="actions">
			<!--- <?php echo $this->Html->link(__('View'), array('action' => 'view', $examPeriod['ExamPeriod']['id'])); ?> --->
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $examPeriod['ExamPeriod']['id'])); ?>
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $examPeriod['ExamPeriod']['id']), null, sprintf(__('Are you sure you want to delete?'), $examPeriod['ExamPeriod']['id'])); ?>
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
</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
