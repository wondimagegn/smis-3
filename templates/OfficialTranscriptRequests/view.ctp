<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
       		<div class="large-12 columns">
       		 <h2><?php echo ' Request Details of tracking number:'.$officialTranscriptRequest['OfficialTranscriptRequest']['trackingnumber']; ?></h2>
       		</div>
	  		<div class="large-12 columns">
	  			<div class="row">
	  				 <div class="large-4 columns">
	  				 <?php echo __('Trackingnumber:').$officialTranscriptRequest['OfficialTranscriptRequest']['trackingnumber']; ?>
		
	  				 </div>
	  				 <div class="large-4 columns">
	  				 <?php echo __('Full Name:').$officialTranscriptRequest['OfficialTranscriptRequest']['full_name']; ?>
		
	  				 </div>
	  				 <div class="large-4 columns">
	  				 <?php echo __('ID.Number:').$officialTranscriptRequest['OfficialTranscriptRequest']['studentnumber']; ?>
		
	  				 </div>
	  			</div>
	  			<div class="row">
	  				 <div class="large-4 columns">
	  				 <?php echo __('Email:').$officialTranscriptRequest['OfficialTranscriptRequest']['email']; ?>
		
	  				 </div>
	  				 <div class="large-4 columns">
	  				 <?php echo __('Mobile Phone:').$officialTranscriptRequest['OfficialTranscriptRequest']['mobile_phone']; ?>
		
	  				 </div>
					<div class="large-4 columns">
	  				 <?php echo __('Admission Type:').$officialTranscriptRequest['OfficialTranscriptRequest']['admissiontype']; ?>
		
	  				 </div>	  				 
	  				 
	  			</div>
	  			
	  			<div class="row">
	  				 <div class="large-4 columns">
	  				 <?php echo __('Degree Type:').$officialTranscriptRequest['OfficialTranscriptRequest']['degreetype']; ?>
		
	  				 </div>
	  				 <div class="large-4 columns">
	  				 <?php echo __('Institution Name:').$officialTranscriptRequest['OfficialTranscriptRequest']['institution_name']; ?>
		
	  				 </div>
	  				 <div class="large-4 columns">
	  				 <?php echo __('Institution Address:').$officialTranscriptRequest['OfficialTranscriptRequest']['institution_address']; ?>
		
	  				 </div>
	  				  
	  			</div>
	  			
	  			<div class="row">
	  				 <div class="large-12 columns">
	  				 <?php echo __('Institution Country:').$officialTranscriptRequest['OfficialTranscriptRequest']['recipent_country']; ?>
	  				 </div>
	  			</div>
	  			<div class="row">
	  				 <div class="large-12 columns">
	  				  
<div class="related">
	<h3><?php echo __('Related Official Request Statuses'); ?></h3>
	<?php if (!empty($officialTranscriptRequest['OfficialRequestStatus'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('S.No'); ?></th>
		<th><?php echo __('Status'); ?></th>
		<th><?php echo __('Remark'); ?></th>
		<th><?php echo __('Created'); ?></th>
		
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($officialTranscriptRequest['OfficialRequestStatus'] as $officialRequestStatus): ?>
		<tr>
			<td><?php echo ++$count; ?></td>
			
			<td><?php echo $statuses[$officialRequestStatus['status']]; ?></td>
			<td><?php echo $officialRequestStatus['remark']; ?></td>
			<td><?php
			echo date("F j, Y, g:i a",strtotime($officialRequestStatus['created'])); 
			 ?></td>
			
			<td class="actions">
				
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'official_request_statuses', 'action' => 'edit', $officialRequestStatus['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'official_request_statuses', 'action' => 'delete', $officialRequestStatus['id']), array('confirm' => __('Are you sure you want to delete # %s?', $officialRequestStatus['id']))); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>
</div>
	  				 </div>
	  			</div>
	  			
			</div> <!-- end of columns 12 -->
	 	</div> <!-- end of row --->
     </div> <!-- end of box-body -->
</div><!-- end of box -->


