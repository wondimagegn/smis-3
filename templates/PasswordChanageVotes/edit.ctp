<?php ?>
<div class="box">
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
             
		<div class="passwordChanageVotes form">
		<?php echo $this->Form->create('PasswordChanageVote');?>
			<fieldset>
				<legend><?php echo __('Edit Password Chanage Vote'); ?></legend>
			<?php
				echo $this->Form->input('id');
				echo $this->Form->input('user_id');
				echo $this->Form->input('role_id');
				echo $this->Form->input('is_voted');
				echo $this->Form->input('chanage_password_request_date');
				echo $this->Form->input('done');
			?>
			</fieldset>
		<?php echo $this->Form->end(array('label'=>__('Submit'),'class'=>'tiny radius button bg-blue'));?>
		</div>
	  </div> <!-- end of columns 12 -->
	</div> <!-- end of row --->
      </div> <!-- end of box-body -->
</div><!-- end of box -->
