<?php echo $this->Form->create('ProgramTypeTransfer');?>
<script type='text/javascript'>
//Sub cat combo
function updateDepartmentCollege(id) {
           
            //serialize form data
            var formData = $("#college_id_"+id).val();
			$("#college_id_"+id).attr('disabled', true);
			$("#department_id_"+id).attr('disabled', true);
			
			
			//get form action
            var formUrl = '/departments/get_department_combo/'+formData;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: formData,
                success: function(data,textStatus,xhr){
						$("#department_id_"+id).attr('disabled', false);
						$("#college_id_"+id).attr('disabled', false);
						$("#department_id_"+id).empty();
						$("#department_id_"+id).append('<option></option>');
						$("#department_id_"+id).append(data);
						
					},
                error: function(xhr,textStatus,error){
                        alert(textStatus);
                }
			});
			
			return false;
      
}
</script>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
<div>
<table class="fs13 small_padding" style="margin-bottom:0px">
    <tr><td class="smallheading" colspan="4">View Program Transfers </td></tr>
	
	
	<tr>
		<td style="width:13%"> College:</td>
		<td style="width:37%"><?php echo $this->Form->input('college_id',array('label'=>false,
		'options'=>$colleges,'empty'=>' ',
		'style'=>'width:250px','id'=>'college_id_1','onchange'=>'updateDepartmentCollege(1)')); ?></td>
		<td style="width:13%">Department:</td>
		<td style="width:37%"><?php echo $this->Form->input('department_id',array('label'=>false,
		'empty'=>' ','options'=>array(),'style'=>'width:250px','id'=>'department_id_1')); ?></td>
	</tr>
   <tr>
		<td style="width:13%"> Student Number:</td>
		<td style="width:37%"><?php echo $this->Form->input('studentnumber',array('label'=>false)); ?></td>
		
	</tr>
   
	<tr>
			<td colspan="4"><?php echo $this->Form->submit(__('View Program Transfer'), array('name' => 'viewProgramTransfer', 'id' => 'viewProgramTransfer', 'div' => false)); ?></td>
	</tr>
	</table>
</div>

<div class="programTypeTransfers index">
	<h2><?php echo __('Program Type Transfers');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th>S.N<u>o</u></th>
			<th><?php echo $this->Paginator->sort('student_id');?></th>
			<th><?php echo $this->Paginator->sort('From Program Type','program_type_id');?></th>
			<th><?php echo $this->Paginator->sort('To Program Type','program_type_id');?></th>
			<th><?php echo $this->Paginator->sort('transfer_date');?></th>
			<th><?php echo $this->Paginator->sort('Academic Year','academic_year');?></th>
			<th><?php echo $this->Paginator->sort('Semester','semester');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	$start=$this->Paginator->counter('%start%');
	foreach ($programTypeTransfers as $programTypeTransfer):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $start++; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($programTypeTransfer['Student']['full_name'], array('controller' => 'students', 'action' => 'student_academic_profile', $programTypeTransfer['Student']['id'])); ?>
		</td>
		<td>
			<?php echo $programTypes[$programTypeTransfer['Student']['base_program_type_id']]; ?>
		</td>
		
		<td>
			<?php echo $this->Html->link($programTypeTransfer['ProgramType']['name'], array('controller' => 'program_types', 'action' => 'view', $programTypeTransfer['ProgramType']['id'])); ?>
		</td>
		<td><?php echo $programTypeTransfer['ProgramTypeTransfer']['transfer_date']; ?>&nbsp;</td>
		<td><?php echo $programTypeTransfer['ProgramTypeTransfer']['academic_year']; ?>&nbsp;</td>
		<td><?php echo $programTypeTransfer['ProgramTypeTransfer']['semester']; ?>&nbsp;</td>
		<td class="actions">
			
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $programTypeTransfer['ProgramTypeTransfer']['id']), null, sprintf(__('Are you sure you want to delete # %s?'), $programTypeTransfer['ProgramType']['name'])); ?>
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
