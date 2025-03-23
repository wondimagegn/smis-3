<?php ?>
<div class="box">
     <div class="box-header bg-transparent">
  	 <h2 class="box-title"> <?php echo __('View Students');?></h2>
     </div>
     <div class="box-body">
         <div class="row">
		    <div class="large-12 columns">

<div class="smallheading">
<?php echo $this->Form->create('Student',array('action'=>'name_list'));?>
<?php  __('Name List View'); ?></div>

<table cellspacing="0" cellpadding="0" class="fs13">
	<tr>
		<td style="width:11%">Program:</td>
		<td style="width:25%"><?php echo $this->Form->input('program_id', array('id' => 'Program', 'class' => 'fs13', 'label' => false, 'type' => 'select', 'options' => $programs, 'default' => $default_program_id)); ?></td>
		<td style="width:11%">Program Type:</td>
		<td style="width:53%"><?php echo $this->Form->input('program_type_id', array('id' => 'ProgramType', 'class' => 'fs13', 'label' => false, 'type' => 'select', 'options' => $program_types, 'default' => $default_program_type_id)); ?></td>
	</tr>
	
	<tr>
		<td>Admission Year:</td>
		<td style="width:20%"><?php echo $this->Form->input('admission_year', array('id' => 'AcadamicYear', 'label' => false, 'class' => 'fs14', 'style' => 'width:125px', 'type' => 'select', 'options' => $acyear_array_data, 'default' => (isset($academic_year_selected) ? $academic_year_selected : $defaultacademicyear))); ?></td>
                 
                 <td>Department:</td>
		<td><?php echo $this->Form->input('department_id', array('id' => 'ProgramType', 'class' => 'fs13', 'label' => false, 'type' => 'select', 'options' => $departments, 'default' => $default_department_id)); ?></td>
	
	</tr>

      <tr>
		
	 <td>Name:</td>
	<td><?php echo $this->Form->input('name', array('id' => 'name', 'class' => 'fs14', 'label' => false)); ?></td>

                <td>Student ID:</td>
		<td><?php echo $this->Form->input('studentnumber', array('id' => 'name', 'class' => 'fs14', 'label' => false)); ?></td>

	</tr>
	
	<tr>
		<td colspan="3">
		<?php echo $this->Form->submit(__('List Students', true), array('name' => 'listStudentsForNameChange', 'div' => false,'class'=>'tiny radius button bg-blue')); ?>
		</td>

		<td colspan="3">
        
		<?php 

          if (!empty($students_for_name_list)) { 
                     
echo $this->Form->submit(__('View PDF', true), array('name' =>'viewPDF', 'div' => false,
'class'=>'tiny radius button bg-blue')); 
			}

?>
		</td>


	</tr>
</table>

<?php 
  if (!empty($students_for_name_list)) { 

?>
<div class="smallheading"><?php __('Students');?></div>
	<table cellpadding="0" cellspacing="0">
	<tr>
	        <th>S.N<u>o</u></th>
			<th><?php echo $this->Paginator->sort('full_name');?></th>
<th><?php echo $this->Paginator->sort('full_am_name');?></th>
			<th><?php echo $this->Paginator->sort('gender');?></th>
			<th><?php echo $this->Paginator->sort('studentnumber');?></th>
			
			<th><?php echo $this->Paginator->sort('admissionyear');?></th>

			<th><?php echo $this->Paginator->sort('Program');?></th>
			<th><?php echo $this->Paginator->sort('Program Type');?></th>
			
			<th><?php echo $this->Paginator->sort('College');?></th>
						<th><?php echo $this->Paginator->sort('Department','department_id');?></th>
			<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
    $start = $this->Paginator->counter('%start%');
	foreach ($students_for_name_list as $student):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $start++; ?>&nbsp;</td>
		<td><?php echo $student['Student']['full_name']; ?>&nbsp;</td>
		<td><?php echo $student['Student']['full_am_name']; ?>&nbsp;</td>
		<td><?php echo $student['Student']['gender']; ?>&nbsp;</td>
		<td><?php echo $student['Student']['studentnumber']; ?>&nbsp;</td>
		<td><?php echo $this->Format->short_date($student['Student']['admissionyear']); ?>&nbsp;</td>
		<td><?php echo $student['Program']['name']; ?>&nbsp;</td>
		<td><?php echo $student['ProgramType']['name']; ?>&nbsp;</td>
		<td><?php echo $student['College']['name']; ?>&nbsp;</td>
		
		<td><?php echo $student['Department']['name']; ?>&nbsp;</td>
		<td class="actions">
			
			<?php 

            echo $this->Html->link(
    'View',
    '#',
   array('class'=>'jsview','data-animation'=>"fade",
'data-reveal-id'=>'myModal','data-reveal-ajax'=>"/students/get_modal_box/".$student['Student']['id'])
);

			
			  if ($role_id == ROLE_REGISTRAR ) {
			   echo $this->Html->link(__('Correct Name', true), array('action' => 'correct_name', $student['Student']['id']));
			  }
			 ?>
			
		</td>
	</tr>
<?php 

endforeach;

?>
   
	</table>
	
<p>
	
	<?php echo $this->Paginator->counter(array('format' => __('Page
{:page} of {:pages}, showing {:current} records out of {:count}
total, starting on record {:start}, ending on {:end}'))); ?>
	
	</p>

	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('previous', true), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>
	
<?php 
}
echo $this->Form->end();
?>

</div>
</div>
</div>
</div>
