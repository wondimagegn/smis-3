<?php echo $this->Form->create('OnlineApplicantStatus'); ?>
<div class="box">
     <div class="box-header bg-transparent">
  	  <h6 class="box-title">
	    <?php echo __('Update Online Applicant Status'); ?>
	   </h6>
     </div>
     <div class="box-body">
       <div class="row">
	        <div class="large-12 columns">
				<div class="row">
					<div class="large-6 columns">
					  <?php 
					  echo $this->Form->input('id');
					  
					  echo $this->Form->input('online_applicant_id');
					  ?>
					</div>
					<div class="large-6 columns">
					  <?php 
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
