<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-user-delete-outline" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Cancel Main Account Administrator'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<div style="margin-top: -30px;"><hr></div>
				<blockquote>
					<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
					<p style="text-align: justify;">
						<span class="fs14 text-black">This tool will enable you to cancel main account administrator of a given office so that you can assign another administrator.</span>
						<ol class="fs14" >
							<li> For security reason, any privilage which was assigned to the administrator by its username will be removed and reassignment of privilage is expected to be done again, if necessary. </li>
							<li> If the account to be cancelled is of Department role and the user is instructor, please don't forget to change the user role from Department to Instructor so that the user can get basic privilages like submiting grade, taking attendance etc. </li>
							<li> If you didn't get administrator name for the office or role you want, that is because there is no assigned administrator for the selected unit. Please use the administrator assignment tool to assign staff as office administrator. </li>
						</ol>
					</p>
				</blockquote>
				<hr>
			</div>

			<?= $this->Form->create('User'); ?>
			<div class="large-12 columns">
				<?= $this->Form->input('role_id', array('label' => 'Role <small></small>', 'style' => 'width:385px', 'onchange' => 'getUsersBasedOnRole(this)', 'id' => 'RoleID', 'type' => 'select', 'default' => $role_id, 'options' => $roles, 'required')); ?>
			</div>
			<div class="large-12 columns">
				<?= $this->Form->input('user_id', array('label' => 'User <small></small>', 'style' => 'width:385px', 'id' => 'UserID', 'onchange' => 'toggleSubmitButtonActive()', 'options' => $users, 'required')); ?>
			</div>
			<div class="large-12 columns">
				<hr>
				<?= $this->Form->Submit('Cancel Administrator', array('id' => 'SubmitID', 'disabled', 'class' => 'tiny radius button bg-blue')); ?>
			</div>
			
			<?= $this->Form->end(); ?>
		</div>
	</div>
</div>

<script type="text/javascript">
	function getUsersBasedOnRole(obj) {
		$("#RoleID").attr('disabled', true);
		$("#UserID").attr('disabled', true);
		$("#SubmitID").attr('disabled', true);
		window.location.replace("/users/cancel_main_account_administrator/" + obj.value);
	}

	$(function() {
		$("#UserID").customselect();
	});

	function toggleSubmitButtonActive() {
		if ($("#UserID").val != 0 || $("#UserID").val != '') {
			$("#SubmitID").attr('disabled', false);
		}
	}

	// prevent possible form resubmission of a form 
	// and disable default JS form resubmit warning  dialog  caused by pressing browser back button or reload or refresh button

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>