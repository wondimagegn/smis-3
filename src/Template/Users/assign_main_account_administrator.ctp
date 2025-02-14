<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-user-add-outline" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Main Account Administrator Assignment'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<?= $this->Form->create('User'); ?>
			<div class="large-12 columns">
				<div style="margin-top: -30px;"><hr></div>
				<blockquote>
					<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
					<p style="text-align:justify;"> <span class="fs14 text-black">This tool will enable you to assign main account administrator. Note that: </span>
						<ol class="fs14 text-black">
							<li> Only one administrator is allowed for the given office and please make sure that you cancel the already existing administrator before you make an assignment. </li>
							<li> If to-be-assigned-account role is other than the one specified on the combo box, please use change role tool to change account role to the one which is specified on the combo box. </li>
							<li> If you didn't get users for the office or role you want, it is because there is no recorded staff with the role you just selected. Please use add user tool to record office staff who is going to be an administrator. </li>
							<li> Only active accounts are displayed in the user combo box and you need to activate if there is deactivated user account that you want to make an assignment for him/her. </li>
						</ol>
					</p> 
				</blockquote>
				<hr>

				<div class="row">
					<div class="large-6 columns">
						<?= $this->Form->input('role_id', array('label' => 'Role: ', 'style' => 'width:400px', 'onchange' => 'getUsersBasedOnRole(this)', 'id' => 'RoleID', 'type' => 'select', 'default' => $role_id, 'options' => $roles)); ?>
					</div>
				</div>
				<div class="row">
					<div class="large-6 columns">
						<?= $this->Form->input('user_id', array('label' => 'User: ', 'style' => 'width:400px', 'id' => 'UserID', 'onchange' => 'toggleSubmitButtonActive()', 'options' => $users)); ?>
					</div>
				</div>
				<div class="row">
					<div class="large-12 columns">
						<hr>
						<?= $this->Form->Submit('Assign Administrator', array('id' => 'SubmitID', 'disabled', 'class' => 'tiny radius button bg-blue')); ?>
					</div>
				</div>
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
		window.location.replace("/users/assign_main_account_administrator/" + obj.value);
	}

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