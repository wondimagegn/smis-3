<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-edit" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= isset($this->request->data['Staff']['id']) && !empty($this->request->data['Staff']['id']) && isset($this->request->data['Staff']['full_name']) ? 'Update Staff Profile: ' . $this->request->data['Staff']['full_name'] : __('Maintain Staff Profile'); ?></span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">

				<?= $this->Form->create('Staff', array('type' => 'file', 'data-abide', /* 'novalidate' => true, */ 'enctype' => 'multipart/form-data')); ?>
				<?= $this->Form->hidden('id'); ?>
        
                <hr style="margin-top: -15px;">
                <br>

                <div class="row">
                    <div class="large-2 columns">
                        <?= $this->Form->input('title_id', array('style' => 'width:100%', 'label' => 'Title: ', 'empty' => '[ Select Title ]')); ?>
                    </div>
					<div class="large-4 columns">
                        <?= $this->Form->input('position_id', array('style' => 'width:100%;', 'label' => 'Position: ', 'required', 'empty' => '[ Select Position ]')); ?>
                    </div>
                    <div class="large-3 columns">
                        <?= $this->Form->input('education', array('style' => 'width:100%;', 'empty' => '[ Select Education ]', 'required', 'label' => 'Education: ')); ?>
                    </div>
					<div class="large-3 columns">
                        <?= $this->Form->input('servicewing', array('style' => 'width:100%;', 'label' => 'Service Wing: ', 'required',  'empty' => '[ Select Service Wing ]')); ?>
                    </div>
                </div>

                <div class="row">
                    <div class="large-4 columns">
                        <?= $this->Form->input('first_name', array('style' => 'width:100%;', 'label' => 'First Name: ', 'required')); ?>
                    </div>
                    <div class="large-4 columns">
                        <?= $this->Form->input('middle_name', array('style' => 'width:100%;', 'label' => 'Middle Name: ', 'required')); ?>
                    </div>
                    <div class="large-4 columns">
                        <?= $this->Form->input('last_name', array('style' => 'width:100%;', 'label' => 'Last Name: ', 'required')); ?>
                    </div>
                </div>

				<hr>

                <div class="row">
                     <div class="large-4 columns">
                        <div style="padding-left: 3%;">
                            <?php $options = array('male' => ' &nbsp;Male', 'female' => ' &nbsp;Female'); ?>
                            <?= '<h6 class="fs13 text-gray">Sex: </h6>' . $this->Form->input('gender', array('options' => $options, 'type' => 'radio', 'legend' => false, 'div' => false, 'separator' => '   ' /* , 'label' => false */)); ?>
                        </div>
                    </div>
                    <div class="large-4 columns">
                        <?php
                        $from = date('Y') - Configure::read('Calendar.birthdayInPast'); 
                        $to = (date('Y') - 20) + Configure::read('Calendar.birthdayAhead');  
						// assuming an employee is 20 years old when joining 
                        $format = Configure::read('Calendar.dateFormat'); ?>
                        <?= $this->Form->input('birthdate', array('style' => 'width:30%','label' => 'Birth Date: ', 'dateFormat' => $format, 'minYear' => $from, 'maxYear' => $to)); ?>
                    </div>
                    <div class="large-4 columns">
                        <?= $this->Form->input('staffid', array('style' => 'width:100%;', 'type' => 'text', 'id' => 'staffid', 'label' => 'Staff ID: ')); ?>
                    </div>
                </div>
				<hr>

                <div class="row">
                    <div class="large-4 columns">
                        <?= $this->Form->input('country_id', array('style' => 'width:100%;', 'label' => 'Country: ', 'default' => COUNTRY_ID_OF_ETHIOPIA,  'empty' => '[ Select Country ]')); ?>
                    </div>
                    <div class="large-4 columns">
                        <div class="email-field">
                            <?= $this->Form->input('email', array('style' => 'width:100%;', 'type' => 'email', 'required', 'label' => 'Email: <small></small></label><small class="error" style="width:100%;; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;"> Email address is required and it must be a valid one.</small>')); ?>
                        </div>
                    </div>
                    <div class="large-4 columns">
                        <div class="alt-email-field">
                            <?= $this->Form->input('alternative_email', array('style' => 'width:100%;', 'type' => 'email',  'label' => 'Alternative Email: <small></small></label><small class="error" style="width:100%;; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">Alternative Email address must be a valid one.</small>')); ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="large-3 columns">
                        <?= $this->Form->input('phone_mobile', array('style' => 'width:100%;', 'type' => 'tel', 'id' => 'phonemobile', 'required', 'label' => 'Mobile Phone: <small></small></label><small class="error" style="width:100%;; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">Mobile Phone number is required.</small>')); ?>
                    </div>
                    <div class="large-3 columns">
                        <?= $this->Form->input('phone_office', array('style' => 'width:100%;', 'type' => 'tel', 'label' => 'Phone Office: ', 'id' => 'phoneoffice')); ?>
                    </div>
					<div class="large-3 columns">
                        <?= $this->Form->input('phone_home', array('style' => 'width:100%;', 'type' => 'tel', 'label' => 'Phone Home: ', 'id' => 'etPhone1')); ?>
                    </div>
                    <div class="large-3 columns">
                        <?= $this->Form->input('address', array('style' => 'width:100%;', 'label' => 'Address: ', 'maxlength' => '50')); ?>
                    </div>
                </div>

				<hr>
				<div class="row">
					<div class="large-4 columns">
						<?php
						if (!empty($this->data['Attachment'][0]['basename'])) {
							if ($this->Media->file($this->data['Attachment'][0]['dirname'] . DS . $this->data['Attachment'][0]['basename'])) { ?>
								<?= $this->Media->embed($this->Media->file($this->data['Attachment'][0]['dirname'] . DS . $this->data['Attachment'][0]['basename']), array('width' => '144', 'class' => 'profile-picture')); ?>
								<?php
								$action_controller_id = 'edit~staffs~' . $this->data['Attachment'][0]['foreign_key']; ?>
								<?= $this->Html->link(__('Delete Profile Picture', true), array('controller' => 'attachments', 'action' => 'delete', $this->data['Attachment'][0]['id'], $action_controller_id), null, sprintf(__('Are you sure you want to delete profile picture which is uploaded on %s ?'), $studentDetail['Attachment'][0]['modified'] )); ?></td>
								<?php
							} else { ?>
								<span class="rejected">Could't load profile Picture, Directory/File inaccessasible</span> <br><br>
								<img src="/img/noimage.jpg" width="144" class="profile-picture">
								<?php
							}
						} else { ?>
							<!-- <img src="/img/noimage.jpg" width="144" class="profile-picture"> -->
							<?= (ALLOW_STAFFS_TO_UPLOAD_PROFILE_PICTURE || $this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN ? $this->Form->input('Attachment.0.file', array('type' => 'file', 'label' => 'Uploaad Profile Picture', 'accept' => '.jpg, .jpeg, .png')) : ''); ?>
							<?php
						} ?>
					</div>
					<div class="large-2 columns">
						&nbsp;
					</div>
					<div class="large-6 columns">
						<div class="row">
							<br>
							<?= $this->Form->input('college_id', array('style' => 'width:95%;', 'label' => 'College: ', 'id' => 'college_id_1', 'empty' => '[ Select College ]', 'onchange' => 'getDepartmentList(1)')); ?>
						</div>
						<div class="row">
							<br>
							<?= $this->Form->input('department_id', array('style' => 'width:95%;', 'label' => 'Department: ', 'id' => 'department_id_1', 'empty' => '[ Select Department ]')); ?>
						</div>
					</div>
                </div>

                <hr>
                <?= $this->Form->end(array('label' => __('Submit'), 'id' => 'saveIt', 'class' => 'tiny radius button bg-blue')); ?>
            </div>
        </div>
    </div>
</div>

<script type='text/javascript'>
	function getDepartmentList(id) {
		var cid = $("#college_id_" + id).val();
		if (cid != '' && cid != 0) {
			$("#department_id_" + id).attr('disabled', true);
			$("#department_id_" + id).empty();
			//var formUrl = '/departments/get_department_combo/' + cid;
			var formUrl = '/departments/get_department_combo/' + cid + '/0/1';
			$.ajax({
				type: 'get',
				url: formUrl,
				data: cid,
				success: function (data, textStatus, xhr) {
					$("#department_id_" + id).attr('disabled', false);
					$("#department_id_" + id).empty();
					$("#department_id_" + id).append(data);
				},
				error: function (xhr, textStatus, error) {
					alert(textStatus);
				}
			});
			return false;
		} else {
			$("#department_id_" + id).empty().append('<option value="">[ Select College First ]</option>');
		}
	}

	var form_being_submitted = false;

	$('#saveIt').click(function() {
		var allFilled = true;
		var isValid = true;

		var radios = document.querySelectorAll('input[type="radio"]');
		var checkedOne = Array.prototype.slice.call(radios).some(x => x.checked);

		// Check all fields with required attribute
		$('form input[required], form select[required], form textarea[required]').each(function() {
			if ($(this).val() === '') {
				allFilled = false;
				isValid = false;
				$(this).css('border', '1px solid red'); // Optionally highlight the empty field
			} else {
				$(this).css('border', ''); // Remove highlight if filled
			}
		});

		if (!allFilled) {
			event.preventDefault(); // Prevent form submission if not all required fields are filled
			alert('Please fill all required fields.');
			isValid = false;
			return false;
		}

		if (!checkedOne) {
            alert('Please select sex');
			isValid = false;
			return false;
		}

		if (form_being_submitted) {
			alert("Submitting your form , please wait a moment...");
			$('#saveIt').attr('disabled', true);
			isValid = false;
			return false;
		}

		if (!form_being_submitted && allFilled && isValid) {
			$('#saveIt').val('Submitting ...');
			form_being_submitted = true;
			isValid = true;
			return true;
		} else {
			isValid = false;
			return false;
		}

	});

	$('form input[required], form select[required], form textarea[required]').on('input change keyup', function() {
		if ($(this).val() !== '') {
			$(this).css('border', '');
		}
	});

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>