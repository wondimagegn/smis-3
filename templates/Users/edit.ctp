<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-edit" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= 'Edit user: ' . $this->data['Staff'][0]['full_name'] . '  (' .  $this->data['User']['username'] . ')'; ?></span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">

                <?= $this->Form->create('User', array('data-abide')); ?>
        
                <?php 
                //debug($editingUser); 
                //debug($ownAccountOfEditingUser);
                //debug($this->data);
                ?>

                <hr style="margin-top: -15px;">
                <br>

                <div class="row">
                    <div class="large-3 columns">
                        <?= $this->Form->input('Staff.0.id'); ?>
                        <?= $this->Form->input('Staff.0.title_id', array('style' => 'width:50%;', 'id' => 'StaffTitle', 'label' => 'Title: ', 'empty' => '[ Select Title ]', 'disabled' => ($this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN  || (($ownAccountOfEditingUser && $this->Session->read('Auth.User')['role_id'] == ROLE_INSTRUCTOR) || ($this->data['User']['role_id'] != $this->Session->read('Auth.User')['role_id'] && !$ownAccountOfEditingUser && ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT || $this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE))) ?  false : true ))); ?>
                        <?= (!($this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN  || (($ownAccountOfEditingUser && $this->Session->read('Auth.User')['role_id'] == ROLE_INSTRUCTOR) || ($this->data['User']['role_id'] != $this->Session->read('Auth.User')['role_id'] && !$ownAccountOfEditingUser && ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT || $this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE)))) ? $this->Form->hidden('Staff.0.title_id') : ''); ?>
                    </div>
                    <div class="large-3 columns">
                        <?= $this->Form->input('Staff.0.education', array('style' => 'width:100%;', 'id' => 'Education', 'label' => 'Education: ', 'required', 'disabled' => ($this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN  || ($this->Session->read('Auth.User')['is_admin'] == 1 && $this->data['User']['role_id'] != $this->Session->read('Auth.User')['role_id'] &&  !$ownAccountOfEditingUser && isset($editingUser['Staff']) && (($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT && $editingUser['Staff'][0]['department_id'] == $this->request->data['Staff'][0]['department_id']) || ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE && $editingUser['Staff'][0]['college_id'] == $this->request->data['Staff'][0]['college_id']))) ?  false : true ))); ?>
                        <?= (!($this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN  || ($this->Session->read('Auth.User')['is_admin'] == 1 && $this->data['User']['role_id'] != $this->Session->read('Auth.User')['role_id'] && !$ownAccountOfEditingUser && isset($editingUser['Staff']) && (($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT && $editingUser['Staff'][0]['department_id'] == $this->request->data['Staff'][0]['department_id']) || ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE && $editingUser['Staff'][0]['college_id'] == $this->request->data['Staff'][0]['college_id'])))) ? $this->Form->hidden('Staff.0.education') : ''); ?>
                    </div>
                    <div class="large-3 columns">
                        <?= $this->Form->input('Staff.0.position_id', array('style' => 'width:100%;', 'id' => 'Position', 'label' => 'Position: ', 'required', 'empty' => '[ Select Position ]', 'disabled' => ($this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN  || ($this->Session->read('Auth.User')['is_admin'] == 1 && $this->data['User']['role_id'] != $this->Session->read('Auth.User')['role_id'] && !$ownAccountOfEditingUser && isset($editingUser['Staff']) && (($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT && $editingUser['Staff'][0]['department_id'] == $this->request->data['Staff'][0]['department_id']) || ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE && $editingUser['Staff'][0]['college_id'] == $this->request->data['Staff'][0]['college_id']))) ?  false : true ))); ?>
                        <?= (!($this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN  || ($this->Session->read('Auth.User')['is_admin'] == 1 && $this->data['User']['role_id'] != $this->Session->read('Auth.User')['role_id'] && !$ownAccountOfEditingUser && isset($editingUser['Staff']) && (($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT && $editingUser['Staff'][0]['department_id'] == $this->request->data['Staff'][0]['department_id']) || ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE && $editingUser['Staff'][0]['college_id'] == $this->request->data['Staff'][0]['college_id'])))) ? $this->Form->hidden('Staff.0.position_id') : ''); ?>
                    </div>
                    <div class="large-3 columns">
                        <?= $this->Form->input('Staff.0.servicewing', array('style' => 'width:100%;', 'id' => 'serviceWing', 'label' => 'Sevice Wing: ', 'required', 'empty' => '[ Select Service Wing ]', 'disabled' => ($this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN  || ($this->Session->read('Auth.User')['is_admin'] == 1 && $this->data['User']['role_id'] != $this->Session->read('Auth.User')['role_id'] && !$ownAccountOfEditingUser && isset($editingUser['Staff']) && (($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT && $editingUser['Staff'][0]['department_id'] == $this->request->data['Staff'][0]['department_id']) || ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE && $editingUser['Staff'][0]['college_id'] == $this->request->data['Staff'][0]['college_id']))) ?  false : true ))); ?>
                        <?= (!($this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN  || ($this->Session->read('Auth.User')['is_admin'] == 1 && $this->data['User']['role_id'] != $this->Session->read('Auth.User')['role_id'] && !$ownAccountOfEditingUser && isset($editingUser['Staff']) && (($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT && $editingUser['Staff'][0]['department_id'] == $this->request->data['Staff'][0]['department_id']) || ($this->Session->read('Auth.User')['role_id'] == ROLE_COLLEGE && $editingUser['Staff'][0]['college_id'] == $this->request->data['Staff'][0]['college_id'])))) ? $this->Form->hidden('Staff.0.servicewing') : ''); ?>
                    </div>
                </div>

                <div class="row">
                    <div class="large-4 columns">
                        <?= $this->Form->input('Staff.0.first_name', array('style' => 'width:100%;', 'label' => 'First Name: ', 'readOnly' => 'true')); ?>
                    </div>
                    <div class="large-4 columns">
                        <?= $this->Form->input('Staff.0.middle_name', array('style' => 'width:100%;', 'label' => 'Middle Name: ', 'readOnly' => 'true')); ?>
                    </div>
                    <div class="large-4 columns">
                        <?= $this->Form->input('Staff.0.last_name', array('style' => 'width:100%;', 'label' => 'Last Name: ', 'readOnly' => 'true')); ?>
                    </div>
                </div>

                <div class="row">
                     <div class="large-4 columns">
                        <div style="padding-left: 3%;">
                            <?php $options = array('male' => ' &nbsp;Male', 'female' => ' &nbsp;Female'); ?>
                            <?= '<h6 class="fs13 text-gray">Sex: </h6>' . $this->Form->input('Staff.0.gender', array('options' => $options, 'type' => 'radio', 'div' => false, 'legend' => false, 'separator' => '   '/* , 'label' => false */)); ?>
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

                <div class="row">
                    <div class="large-4 columns">
                        <?php //echo $this->Form->input('active', array('label' => 'Active/Deactive', 'type' => 'checkbox', 'checked' => (!isset($this->request->data['User']['active']) || $this->request->data['User']['active'] == 1 ? 'checked' : false))); ?>
                        <?= $this->Form->input('role_id', array('style' => 'width:100%;', 'label' => 'Role: ', 'disabled', 'empty' => '[ Select Role ]')); ?>
                        <?= $this->Form->hidden('role_id'); ?>
                    </div>
                    <div class="large-4 columns">
                        <?= $this->Form->hidden('Staff.0.user_id'); ?>
                        <?= $this->Form->hidden('User.id'); ?>
                        <?= $this->Form->input('username', array('style' => 'width:100%;', 'label' => 'Username: ', 'readOnly' => 'true')); ?>
                    </div>
                    <div class="large-4 columns">
                        <div class="email-field">
                            <?= $this->Form->input('Staff.0.email', array('style' => 'width:100%;', 'type' => 'email', 'required', 'readOnly' => ($this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN  || ($ownAccountOfEditingUser /* && $this->Session->read('Auth.User')['role_id'] == ROLE_INSTRUCTOR */) ?  false : true ), 'label' => 'Email: <small></small></label><small class="error" style="width:100%;; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;"> Email address is required and it must be a valid one.</small>')); ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="large-4 columns">
                        <?= $this->Form->input('Staff.0.phone_mobile', array('style' => 'width:100%;', /* 'type' => 'tel', 'id' => 'phonemobile', */ 'id' => 'ethiopainMobile', 'required', 'readOnly' => ($this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN  || ($ownAccountOfEditingUser/*  && $this->Session->read('Auth.User')['role_id'] == ROLE_INSTRUCTOR */) ?  false : true ), 'label' => 'Mobile Phone: <small></small></label><small class="error" style="width:100%;; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;"> Mobile Phone number is required.</small>')); ?>
                    </div>
                    <div class="large-4 columns">
                        <?= $this->Form->input('Staff.0.phone_office', array('style' => 'width:100%;', 'type' => 'tel', 'label' => 'Phone Office: ', 'readOnly' => ($this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN  || ($ownAccountOfEditingUser/*  && $this->Session->read('Auth.User')['role_id'] == ROLE_INSTRUCTOR */) ?  false : true ),  'id' => 'phoneoffice')); ?>
                    </div>
                    <div class="large-4 columns">
                        <?= $this->Form->input('Staff.0.address', array('style' => 'width:100%;', 'label' => 'Address: ', 'readOnly' => ($this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN  || ($ownAccountOfEditingUser /* && $this->Session->read('Auth.User')['role_id'] == ROLE_INSTRUCTOR */) ?  false : true ), 'maxlength'=>'20')); ?>
                    </div>
                </div>
                <hr>

                <?= $this->Form->end(array('label' => __('Update User Account'), 'id' => 'SubmitID', 'class' => 'tiny radius button bg-blue')); ?>

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
			$("#department_id_" + id).empty().append('<option value="">[ Select College First ]</option>');
		}
	}

    var form_being_submitted = false;

	$('#SubmitID').click(function() {
		var isValid = true;
		var username = $('#username').val(); 
		var roleID = $('#RoleID').val(); 
		
		/* if (username == '') { 
			$('#username').focus();
			isValid = false;
			return false;
		}

		if (roleID == 0 || roleID == '') { 
			$('#RoleID').focus();
			isValid = false;
			return false;
		} */

        const regex = /^\+251\d{9}$/;
		var phoneNumber = $('#ethiopainMobile').val();
		//alert(phoneNumber);
		
		// to prevent invalid exixting phone numbers are trimmed by data-abide
		if (phoneNumber === '') {
			alert('Mobile phone is required');
			return false;
		} else {
			if (regex.test(phoneNumber)) {
				//alert("Valid Ethiopian phone number.");
			} else {
				alert("Invalid phone number. please include country code of Ethiopia and avoid spaces. and use +251911111111 pattern.");
				return false;
			}
		}
		
		if (form_being_submitted) {
			alert("Updating User Account, please wait a moment...");
			$('#SubmitID').attr('disabled', true);
			return false;
		}

		if (!form_being_submitted && isValid) {
			$('#SubmitID').val('Updating User Account...');
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