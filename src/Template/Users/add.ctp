<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-user-add-outline" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Create User Account for System Access'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<div style="margin-top: -30px;"><hr></div>

				<?= $this->Form->create('User', array('data-abide')); ?>

				<div class="row">
                    <div class="large-3 columns">
                        <?= $this->Form->input('Staff.0.id'); ?>
                        <?= $this->Form->input('Staff.0.title_id', array('style' => 'width:50%;', 'id' => 'StaffTitle', 'label' => 'Title: ', 'empty' => '[ Select Title ]')); ?>
                    </div>
                    <div class="large-3 columns">
                        <?= $this->Form->input('Staff.0.education', array('style' => 'width:100%;', 'id' => 'Education', 'label' => 'Education: ', 'required',  'empty' => '[ Select Education Level ]')); ?>
                    </div>
					<div class="large-3 columns">
                        <?= $this->Form->input('Staff.0.position_id', array('style' => 'width:100%;', 'id' => 'Position',  'label' => 'Position: ', 'required', 'empty' => '[ Select Position ]')); ?>
                    </div>
					<div class="large-3 columns">
                        <?= $this->Form->input('Staff.0.servicewing', array('style' => 'width:100%;', 'id' => 'serviceWing', 'label' => 'Sevice Wing: ', 'required', 'empty' => '[ Select Service Wing ]')); ?>
                    </div>
                </div>

				<div class="row">
                    <div class="large-4 columns">
                        <?= $this->Form->input('Staff.0.first_name', array('style' => 'width:100%;', 'label' => 'First Name: ', 'required')); ?>
                    </div>
                    <div class="large-4 columns">
                        <?= $this->Form->input('Staff.0.middle_name', array('style' => 'width:100%;', 'label' => 'Middle Name: ', 'required')); ?>
                    </div>
                    <div class="large-4 columns">
                        <?= $this->Form->input('Staff.0.last_name', array('style' => 'width:100%;', 'label' => 'Last Name: ', 'required')); ?>
                    </div>
                </div>

                <div class="row">
                     <div class="large-4 columns">
                        <div style="padding-left: 3%;">
                            <?php $options = array('male' => ' &nbsp;Male', 'female' => ' &nbsp;Female'); ?>
                            <?= '<h6 class="fs13 text-gray">Sex: </h6>' . $this->Form->input('Staff.0.gender', array('options' => $options, 'id' => 'gender', 'type' => 'radio', 'div' => false, 'legend' => false, 'separator' => '   '/* , 'label' => false */)); ?>
                        </div>
                    </div>
                    <div class="large-4 columns">
                        <?php
                        $from = date('Y') - Configure::read('Calendar.birthdayInPast');
                        // assuming an employee is 20 years old when joining 
                        $to = (date('Y') - 20) + Configure::read('Calendar.birthdayAhead');
                        $format = Configure::read('Calendar.dateFormat');
                        ?>
                        <?= $this->Form->input('Staff.0.birthdate', array('style' => 'width:30%','label' => 'Birth Date: ', 'dateFormat' => $format, 'minYear' => $from, 'maxYear' => $to)); ?>
                    </div>
                    <div class="large-4 columns">
                        <?= $this->Form->input('Staff.0.staffid', array('style' => 'width:100%;', 'type' => 'text', 'id' => 'staffid', 'label' => 'Staff ID: ', 'readOnly' => ($this->Session->read('Auth.User')['role_id'] != ROLE_SYSADMIN ? true : false))); ?>
                    </div>
                </div>

				<?php
				if ($this->Session->read('Auth.User')['role_id'] != ROLE_REGISTRAR) { ?>
					<div class="row">
						<div class="large-6 columns">
							<?= $this->Form->input('Staff.0.college_id', array('style' => 'width:100%;', 'label' => 'College: ', 'id' => 'college_id_1', 'empty' => '[ Select College ]', 'onchange' => 'getDepartmentList(1)', 'disabled' => ($this->Session->read('Auth.User')['role_id'] != ROLE_SYSADMIN ?  true : false))); ?>
							<?= ($this->Session->read('Auth.User')['role_id'] != ROLE_SYSADMIN ? $this->Form->hidden('Staff.0.college_id') : ''); ?>
						</div>
						<div class="large-6 columns">
							<?= $this->Form->input('Staff.0.department_id', array('style' => 'width:100%;', 'label' => 'Department: ', 'id' => 'department_id_1', 'empty' => '[ Select Department ]', 'disabled' => ($this->Session->read('Auth.User')['role_id'] != ROLE_SYSADMIN ?  true : false))); ?>
							<?= ($this->Session->read('Auth.User')['role_id'] != ROLE_SYSADMIN ? $this->Form->hidden('Staff.0.department_id') : ''); ?>
						</div>
					</div>
					<?php
				} ?>

				<div class="row">
					<div class="large-4 columns">
						<?= $this->Form->input('role_id', array('style' => 'width:100%;', 'label' => 'Role: ', 'id' => 'RoleID',  'empty' => '[ Select Role ]', 'required')); ?>
					</div>
					<div class="large-4 columns">
						<?= $this->Form->hidden('Staff.0.user_id'); ?>
						<?= $this->Form->hidden('User.id'); ?>
						<?= $this->Form->input('username', array('style' => 'width:100%;', 'label' => 'Username: ', 'id' => 'username', 'onchange' => 'toggleSubmitButtonActive()', 'required')); ?>
					</div>
					<div class="large-4 columns">
						<div class="email-field">
							<?= $this->Form->input('Staff.0.email', array('style' => 'width:100%;', 'type' => 'email', 'required', 'label' => 'Email: <small></small></label><small class="error" style="width:100%;; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;"> Email address is required and it must be a valid one.</small>')); ?>
						</div>
					</div>
				</div>

                <div class="row">
                    <div class="large-4 columns">
                        <?= $this->Form->input('Staff.0.phone_mobile', array('style' => 'width:100%;', 'type' => 'tel', 'id' => 'phonemobile', 'required', 'label' => 'Mobile Phone: <small></small></label><small class="error" style="width:100%;; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;"> Mobile Phone number is required.</small>')); ?>
                    </div>
                    <div class="large-4 columns">
                        <?= $this->Form->input('Staff.0.phone_office', array('style' => 'width:100%;', 'type' => 'tel', 'label' => 'Phone Office: ', 'id' => 'phoneoffice')); ?>
                    </div>
                    <div class="large-4 columns">
                        <?= $this->Form->input('Staff.0.address', array('style' => 'width:100%;', 'label' => 'Address: ', 'maxlength'=>'20')); ?>
                    </div>
                </div>
				<hr>
				<?= $this->Form->end(array('label' => 'Create User', 'disabled', 'id' => 'SubmitID', 'class' => 'tiny radius button bg-blue')); ?>
			</div>
		</div>
	</div>
</div>

<script type='text/javascript'>
	function getDepartmentList(id) {
		var cid = $("#college_id_" + id).val();
		//get form action
		if (cid != '' && cid != 0) {
            $("#department_id_" + id).attr('disabled', true);
		    $("#department_id_" + id).empty();
            //var formUrl = '/departments/get_department_combo/' + cid;
			var formUrl = '/departments/get_department_combo/' + cid + '/0/1';
			$.ajax({
				type: 'get',
				url: formUrl,
				data: cid,
				success: function(data, textStatus, xhr) {
					$("#department_id_" + id).attr('disabled', false);
					$("#department_id_" + id).empty();
					$("#department_id_" + id).append(data);
				},
				error: function(xhr, textStatus, error) {
					alert(textStatus);
				}
			});
			return false;
		} else {
			$("#college_id_" + id).empty().append('<option value="">[ Select College ]</option>');
			$("#department_id_" + id).empty().append('<option value="">[ Select College First ]</option>');
		}
	}

	function getDepartmentList(id) {
		var cid = $("#college_id_" + id).val();
		$("#department_id_" + id).attr('disabled', true);
		$("#department_id_" + id).empty();
		//get form action
		if (cid != '' && cid != 0) {
			var formUrl = '/departments/get_department_combo/' + cid + '/0/1';;
			$.ajax({
				type: 'get',
				url: formUrl,
				data: cid,
				success: function(data, textStatus, xhr) {
					$("#department_id_" + id).attr('disabled', false);
					$("#department_id_" + id).empty();
					$("#department_id_" + id).append(data);
				},
				error: function(xhr, textStatus, error) {
					alert(textStatus);
				}
			});
			return false;
		} else {
			$("#department_id_" + id).empty().append('<option value="">[ Select College First ]</option>');
		}
	}

	/* (function($) {
		$('#college_id').change(function() {
			$('#department_assignment').load('/users/get_department/' + $(this).val());
		});
	})(jQuery); */

	function toggleSubmitButtonActive() {
		if ($("#username").val != 0 && $("#username").val != '') {
			$("#SubmitID").attr('disabled', false);
		}
	}

	var form_being_submitted = false;

	$('#SubmitID').click(function() {
		var isValid = true;
		var username = $('#username').val(); 
		var roleID = $('#RoleID').val(); 
		var genderRadios = document.getElementsByName('data[Staff][0][gender]');
		var isGenderSelected = false;

		//var inputs = document.querySelectorAll('#UserAddForm input[required]');
		var inputs = document.querySelectorAll('#UserAddForm input[required], #UserAddForm select[required]');

		for (var i = 0; i < inputs.length; i++) {
			if (!inputs[i].value) {
				isValid = false;
				inputs[i].focus();
				return false;
				break;
			}
		}

		for (var i = 0; i < genderRadios.length; i++) {
			if (genderRadios[i].checked) {
				isGenderSelected = true;
				break;
			}
		}

		if (!isGenderSelected) {
			alert('Please select sex.');
			isValid = false;
			return false;
		}

		if (username == '') { 
			$('#username').focus();
			isValid = false;
			return false;
		}

		if (roleID == 0 || roleID == '') { 
			$('#RoleID').focus();
			isValid = false;
			return false;
		}
		
		if (form_being_submitted) {
			alert("Adding User Account, please wait a moment...");
			$('#SubmitID').attr('disabled', true);
			isValid = false;
			return false;
		}

		if (!form_being_submitted && isValid) {
			$('#SubmitID').val('Creating User Account...');
			$('#SubmitID').attr('disabled', false);
			form_being_submitted = true;
			isValid = true;
			return true;
		} else {
			isValid = false;
			return false;
		}
	});
	
	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>