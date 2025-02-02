<?php echo $this->Form->create('OfficialRequestStatus'); ?>
<div class="box">
     <div class="box-header bg-transparent">
  	  <h6 class="box-title">
	    <?php echo __('Edit Official Request Status'); ?>
	   </h6>
     </div>
     <div class="box-body">
       <div class="row">
	        <div class="large-12 columns">
				<div class="row">
					<div class="large-12 columns">
					  	
					  	<div class="row">
	  				 <div class="large-4 columns">
	  				 <?php echo __('Trackingnumber:').$this->request->data['OfficialTranscriptRequest']['trackingnumber']; ?>
		
	  				 </div>
	  				 <div class="large-4 columns">
	  				 <?php echo __('Full Name:').$this->request->data['OfficialTranscriptRequest']['full_name']; ?>
		
	  				 </div>
	  				 <div class="large-4 columns">
	  				 <?php echo __('ID.Number:').$this->request->data['OfficialTranscriptRequest']['studentnumber']; ?>
		
	  				 </div>
	  			</div>
	  			
					</div>
					<div class="large-12 columns">
					  <?php 
					   echo $this->Form->input('id');
					 
					  echo $this->Form->input('status');
					  ?>
					</div>
				</div>
	  		</div>
	  		 <div class="large-12 columns">
	  		  <?php 
	  		  echo $this->Form->input('remark');
	  		  ?>
		     </div>
	  		 <div class="large-12 columns">
	
<?php echo $this->Form->end(array('label'=>'Submit','class'=>'tiny radius button bg-blue'));
?>
	  		</div>
	    </div>
	</div>
</div>
