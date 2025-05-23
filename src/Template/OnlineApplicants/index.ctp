<?php 
echo $this->Form->create('OnlineApplicant',array('action'=>'search','novalidate' => true,
'method'=>'get'));

?>
<script>
function toggleView(obj) {
	if($('#c'+obj.id).css("display") == 'none')
		$('#i'+obj.id).attr("src", '/img/minus2.gif');
	else
		$('#i'+obj.id).attr("src", '/img/plus2.gif');
	$('#c'+obj.id).toggle("slow");
}
</script>
<div class="box">
     <div class="box-body">
       <div class="row">
	    <div class="large-12 columns">
            	<h3><?php __('Online Applicant Request List'); ?></h3>
				<?php
				$yFrom = Configure::read('Calendar.officialTranscriptStartYear');
				$yTo = date('Y');
				?>

				<table cellspacing="0" cellpadding="0" class="fs13">
					
					<tr>
						<td>Application Number:</td>
						<td><?php echo $this->Form->input('applicationnumber',array('label'=>false)); ?></td>
		
						<td>Name:</td>
						<td>
							<?php echo $this->Form->input('name',array('label'=>false)); ?>
						 </td>
					</tr>
					<tr>
						<td>Applied From:</td>
						<td><?php 
						echo $this->Form->input('applied_from', array('label' => false, 'type' => 'date', 'minYear' => $yFrom, 'maxYear' => $yTo,'style'=>'width:70px'));
						?></td>
						<td>Applied To:</td>
						<td><?php 
						echo $this->Form->input('applied_to', array('label' => false, 'type' => 'date', 
						'minYear' => $yFrom, 'maxYear' => $yTo,'style'=>'width:70px'));
						?></td>
					</tr>
	
					<tr>
						<td colspan="6">
						<?php echo $this->Form->submit(__('List Requests', true), array('name' => 'listOnlineApplicant',
						'class'=>'tiny radius button bg-blue', 
						'div' => false)); ?>
						</td>
					</tr>
				</table>
<?php if(isset($onlineApplicants) && !empty($onlineApplicants)) { ?>
	<table cellpadding="0" cellspacing="0">
	<thead>
	<tr>
			<th style="width:2%">&nbsp;</th>
			<th style="width:2%">N<u>o</u></th>
			<th><?php echo $this->Paginator->sort('applicationnumber','Application Number');?></th>
			<th><?php echo $this->Paginator->sort('college_id','College');?></th>
			<th><?php echo $this->Paginator->sort('department_id','Department');?></th>
			<th><?php echo $this->Paginator->sort('program_id','Study Level'); ?></th>
			<th><?php echo $this->Paginator->sort('program_type_id','Admission Type'); ?></th>
		
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php 
	$count = 1;
	debug($onlineApplicants);
	foreach ($onlineApplicants as $onlineApplicant): ?>
	<tr>
		<td onclick="toggleView(this)" id="<?php echo $count; ?>"><?php echo $this->Html->image('plus2.gif', array('id' => 'i'.$count)); ?></td>
		
		<td><?php echo h($count); ?>&nbsp;</td>
		<td><?php echo h($onlineApplicant['OnlineApplicant']['applicationnumber']); ?>&nbsp;</td>
		<td><?php echo h($onlineApplicant['College']['name']); ?>&nbsp;</td>
		<td><?php echo h($onlineApplicant['Department']['name']); ?>&nbsp;</td>
		<td><?php echo h($onlineApplicant['Program']['name']); ?>&nbsp;</td>
		<td><?php echo h($onlineApplicant['ProgramType']['name']); ?>&nbsp;</td>
		
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $onlineApplicant['OnlineApplicant']['id'])); ?>
			<?php echo $this->Html->link(__('Update Status'), array('controller'=>'OnlineApplicantStatuses','action' => 'add', $onlineApplicant['OnlineApplicant']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $onlineApplicant['OnlineApplicant']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $onlineApplicant['OnlineApplicant']['id']))); ?>
		</td>
	</tr>
	
	<tr id="c<?php echo $count; ?>" style="display:none">
	  <td colspan="7">
	    <table>
	       <tr>
	       	<td>Applicant Name</td><td><?php echo $onlineApplicant['OnlineApplicant']['full_name']; ?></td>
	       </tr>
	       
	       <tr>
	       	<td>Mobile</td><td><?php echo $onlineApplicant['OnlineApplicant']['mobile_phone']; ?></td>
	       </tr>
	       <tr>
	       	<td>Email</td><td><?php echo $onlineApplicant['OnlineApplicant']['email']; ?></td>
	       </tr>
	       <tr>
	       	<td>Undergraduate University Attended</td><td><?php echo $onlineApplicant['OnlineApplicant']['undergraduate_university_name']; ?></td>
	       </tr>
	       <tr>
	       	<td>Undergraduate University CGPA</td><td><?php echo $onlineApplicant['OnlineApplicant']['undergraduate_university_cgpa']; ?></td>
	       </tr>
	       
	        <tr>
	       	<td>Entrance Result</td><td><?php echo $onlineApplicant['OnlineApplicant']['entrance_result']; ?></td>
	       </tr>
	        <tr>
	       	<td>Document  Approved</td><td><?php echo $onlineApplicant['OnlineApplicant']['document_submitted']==1 ? 'Accepted': $onlineApplicant['OnlineApplicant']['document_submitted']==-1 ? 'Rejected':'Pending'; ?></td>
	       </tr>
	       <tr>
	       	<td>Application Status</td><td><?php echo $onlineApplicant['OnlineApplicant']['application_status']==1 ? 'Accepted': $onlineApplicant['OnlineApplicant']['application_status']==-1 ? 'Rejected':'Pending'; ?></td>
	       </tr>
		
		</table>
		<?php 
		//debug($onlineApplicant);
		if(isset($onlineApplicant['OnlineApplicantStatus']) 
		&& !empty($onlineApplicant['OnlineApplicantStatus'])){ ?>
				<table>
						<tr><th>Status</th> <th>Remark</th> <th>Date</th></tr>
						<?php foreach($onlineApplicant['OnlineApplicantStatus'] as $st=>$stv){ 
						
						?>
						 <tr> <td><?php echo $stv['status'];?></td> <td><?php echo $stv['remark'];?></td>
						 <td><?php echo date("F j,Y,g:i a",strtotime($stv['created'])); ?></td></tr>
						<?php } ?>
				</table>
		
		<?php } ?>
	</td>
		
	
	</tr>
	
<?php
	$count++;
 endforeach; ?>
	</tbody>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
		'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>
	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
<?php } ?>
</div> <!-- end of columns 12 -->
	</div> <!-- end of row -->
   </div> <!-- end of box-body -->
</div><!-- end of box -->

<?php $this->Form->end();
?>

