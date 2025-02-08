<?php 
echo $this->Form->create('OfficialTranscriptRequest',array('action'=>'search','novalidate' => true,
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
            	<h3><?php __('Official Transcript Request List'); ?></h3>
				<?php
				$yFrom = Configure::read('Calendar.officialTranscriptStartYear');
				$yTo = date('Y');
				?>

				<table cellspacing="0" cellpadding="0" class="fs13">
					
					<tr>
						<td>Tracking Number:</td>
						<td><?php echo $this->Form->input('trackingnumber',array('label'=>false)); ?></td>
		
						<td>Name:</td>
						<td>
							<?php echo $this->Form->input('name',array('label'=>false)); ?>
						 </td>
					</tr>
					<tr>
						<td>Request From:</td>
						<td><?php 
						echo $this->Form->input('request_from', array('label' => false, 'type' => 'date', 'minYear' => $yFrom, 'maxYear' => $yTo,'style'=>'width:70px'));
						?></td>
						<td>Request To:</td>
						<td><?php 
						echo $this->Form->input('request_to', array('label' => false, 'type' => 'date', 
						'minYear' => $yFrom, 'maxYear' => $yTo,'style'=>'width:70px'));
						?></td>
					</tr>
	
					<tr>
						<td colspan="6">
						<?php echo $this->Form->submit(__('List Requests', true), array('name' => 'listOfficialTranscriptRequest',
						'class'=>'tiny radius button bg-blue', 
						'div' => false)); ?>
						</td>
					</tr>
				</table>
<?php if(isset($officialTranscriptRequests) && !empty($officialTranscriptRequests)) { ?>
	<table cellpadding="0" cellspacing="0">
	<thead>
	<tr>
			<th style="width:2%">N<u>o</u></th>
			<th><?php echo $this->Paginator->sort('trackingnumber','Tracking Number');?></th>
			<th><?php echo "Detail";?></th>
			<th><?php echo "Target Institution";?></th>
			<th><?php echo $this->Paginator->sort('request_processed','Request Status'); ?></th>
			<th><?php echo $this->Paginator->sort('created','Request Date'); ?></th>
			
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php 
	$count = 1;
	foreach ($officialTranscriptRequests as $officialTranscriptRequest): ?>
	<tr>
		<td><?php echo h($count); ?>&nbsp;</td>
		<td><?php echo h($officialTranscriptRequest['OfficialTranscriptRequest']['trackingnumber']); ?>&nbsp;</td>
		<td>
		<?php 
		
		echo  "<div> Name:".$officialTranscriptRequest['OfficialTranscriptRequest']['full_name'].'</div>'; 
		echo  "<div> ID Number:".$officialTranscriptRequest['OfficialTranscriptRequest']['studentnumber'].'</div>'; 
		
		echo  "<div> Mobile:".$officialTranscriptRequest['OfficialTranscriptRequest']['mobile_phone'].'</div>'; 
		echo  "<div> Email:".$officialTranscriptRequest['OfficialTranscriptRequest']['email'].'</div>'; 
		echo  "<div> Requested Degree:".$officialTranscriptRequest['OfficialTranscriptRequest']['degreetype'].'</div>'; 
		echo  "<div> Attended:".$officialTranscriptRequest['OfficialTranscriptRequest']['admissiontype'].'</div>'; 
	
		
		?>
		
		</td>
		<td>
		 <?php 
		
		echo  "<div> Institution Name:".$officialTranscriptRequest['OfficialTranscriptRequest']['institution_name'].'</div>'; 
		echo  "<div> Institution Address:".$officialTranscriptRequest['OfficialTranscriptRequest']['institution_address'].'</div>'; 
		echo  "<div> Institution Country:".$officialTranscriptRequest['OfficialTranscriptRequest']['recipent_country'].'</div>'; 
		
		?>
		
		</td>
		
		
		<td><?php 
		if($officialTranscriptRequest['OfficialTranscriptRequest']['request_processed']==0){
		  echo "Request On Process";
		} else if ($officialTranscriptRequest['OfficialTranscriptRequest']['request_processed']==1){
		    echo "Request Passed First Check";
		} else if ($officialTranscriptRequest['OfficialTranscriptRequest']['request_processed']==2) { 
		   echo "Request Cancelled";
		} else if($officialTranscriptRequest['OfficialTranscriptRequest']['request_processed']==3) {
			echo "Document Sent";
			
		}
		?></td>
		<td><?php 
		echo date("F j, Y, g:i a",strtotime($officialTranscriptRequest['OfficialTranscriptRequest']['created'])); 
		
		 ?>&nbsp;</td>
	
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $officialTranscriptRequest['OfficialTranscriptRequest']['id'])); ?>
			<?php echo $this->Html->link(__('Update Status'), array('controller'=>'OfficialRequestStatuses','action' => 'add', $officialTranscriptRequest['OfficialTranscriptRequest']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $officialTranscriptRequest['OfficialTranscriptRequest']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $officialTranscriptRequest['OfficialTranscriptRequest']['id']))); ?>
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
