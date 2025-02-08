<?php ?>

<div class="box">
     <div class="box-header bg-transparent">
     </div>
     <div class="box-body">
       <div class="row">
	  <div class="large-12 columns">
          <h6 style="text-align:center" class="box-title">
	     <?php echo __('Account Suspended'); ?>
	     </h6>
	<p class="fs14"><strong>Based on university roles and guideline</strong>, the account <strong class="rejected"><?php echo $userDetails['User']['full_name'].'('.$userDetails['User']['username'].')';?> </strong> has been suspended for some reason. As a result, this account will be active again based on decision from the university.</p>

	<p class="fs14">If you are getting this message without prior notification of the case. Then it means the account was suspened wrongly , please communicate with the department about the case. </p>
          </div>
	</div>
     </div>
</div>
