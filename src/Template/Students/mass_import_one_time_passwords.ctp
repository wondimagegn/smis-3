
<div class="box">
    <div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-download" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Import One Time Password for Students'); ?></span>
		</div>
	</div>
    <div class="box-body pad-forty">
        <div class="row">
            <div class="large-12 columns" style="margin-top: -55px;">
                <div class="row">
                    <hr>
                    <blockquote>
                        <h6 class="text-red"><i class="fa fa-info"></i> &nbsp; Be-aware:</h6>
                        <span style="text-align:justify;" class="fs14 text-gray">Before importing the excel keep non requred fields empty and <b class="text-black"><i>make sure that the first row of your excel file is filled with <br>
                            <ul>
                                <li>studentnumber, username and password for Office 365</li>
                                <li>studentnumber, username, password and portal for Elearning</li>
                                <li>studentnumber, username, password, portal and exam_center for Exit Exam</li>
                            </ul>
                            fields and you saved your excel file with Excel 97-2003 Format.</i></b> <a href="<?= OTP_IMPORT_TEMPLATE_FILE; ?>">Download Import Template here</a> with the required fields and sample pre-populated data.
                            <br> <br>
                            <span class="rejected fs14">NB: Please do not mix different service types in one csv. Please use different CSV files to import OTP for Office 365, Elearning and Exit Exam service types, the above template file is just to show you the required fields in each service type.</span>
                        </span> 
                    </blockquote>
                    <hr>
                    <?php

                    $otpServicesOptions = Configure::read('otp_services_option');
                    if ($this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN && $this->Session->read('Auth.User')['is_admin'] == 1) {
                        // Super Admin allow to upload all service types 
                    } else if ($this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN && $this->Session->read('Auth.User')['is_admin'] != 1) {
                        unset($otpServicesOptions['Elearning']);
                        unset($otpServicesOptions['ExitExam']);
                    } else if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] == 1) {
                        unset($otpServicesOptions['Elearning']);
                        unset($otpServicesOptions['Office365']);
                    } else if ($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR && $this->Session->read('Auth.User')['is_admin'] != 1) {
                        unset($otpServicesOptions['Elearning']);
                        unset($otpServicesOptions['ExitExam']);
                    } else if ($this->Session->read('Auth.User')['role_id'] == ROLE_GENERAL) {
                        unset($otpServicesOptions['Elearning']);
                        unset($otpServicesOptions['ExitExam']);
                    } else {
                        // prevent any access for other roles.
                        $otpServicesOptions = array();
                    }

                    if (isset($results_to_html_table) && !empty($results_to_html_table)) { ?>
                        <br>
                        <h6 class="fs16 text-gray">Import results:</h6>
                        <table cellpadding="0" cellspacing="0" class="table">
                            <thead>
                                <tr>
                                    <th class="vcenter">Student ID</th>
                                    <th class="vcenter">Username</th>
                                    <th class="vcenter">Password</th>
                                    <?= (isset($showPortal) && $showPortal == 1 ? '<th class="vcenter">Portal</th>' : ''); ?>
                                    <?= (isset($showExamCenter) && $showExamCenter == 1 ? '<th class="vcenter">Exam Center</th>' : ''); ?>
                                    <th class="vcenter" style="width: 40%;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($results_to_html_table as $key => $result) { ?>
                                    <tr>
                                        <td class="vcenter"><?= $result['studentnumber'] ?></td>
                                        <td class="vcenter"><?= $result['username'] ?></td>
                                        <td class="vcenter"><?= $result['password'] ?></td>
                                        <?= (isset($showPortal) && $showPortal == 1 ? '<td class="vcenter">' . $result['portal']. '</td>' : ''); ?>
                                        <?= (isset($showExamCenter) && $showExamCenter == 1 ? '<td class="vcenter">'. $result['exam_center'] . '</td>' : ''); ?>
                                        <td class="vcenter"><?= $result['status'] ?></td>
                                    </tr>
                                    <?php
                                } ?>
                            </tbody>
                        </table>
                        <hr>
                        <?php
                    } ?>
                </div>
            </div>

            <fieldset style="padding-top: 0px; padding-bottom: 0px;">
                <?= $this->Form->create('Student', array('controller' => 'students', 'action' => 'mass_import_one_time_passwords', 'type' => 'file')); ?>
                <div class="large-6 columns" style="margin-top: 30px;">
                    <label>
                        <strong>Student List: </strong>
                        <?= $this->Form->file('File', array('label' => 'Excel', 'name' => 'data[Student][xls]', 'required')); ?>
                    </label>
                    <span id="fileError" style="color:red;" class="fs14"></span>
                    <br>
                </div>
                <div class="large-4 columns">
                    <br>
                    <?= $this->Form->input('service', array('label' => 'Service: ', 'id' => 'serviceType', 'required', 'style' => 'width: 90%;', 'type' => 'select', 'options' => $otpServicesOptions)); ?>
                </div>
                <div class="large-2 columns">

                </div>
                <hr />
                <?= $this->Form->submit('Upload OTP', array('id' => 'uploadBtn', 'class' => 'tiny radius button bg-blue')); ?>
                <?= $this->Form->end(); ?>
            </fieldset>
        </div>
    </div>
</div>

<script>

    var form_being_submitted = false;
    var serviceType = $("#serviceType option:selected").text();

    $('#uploadBtn').click(function(e) {
        var isValid = true;

        serviceType = $("#serviceType option:selected").text(); 

        var fileInput = $('#StudentFile');
        var filePath = fileInput.val();
        var allowedExtensions = /(\.xls)$/i;

        if (!filePath) {
            $('#fileError').text('Excel 2007 format(.xls) file is required.');
            e.preventDefault();
            isValid = false;
            return false;
        } else if (!allowedExtensions.exec(filePath)) {
            $('#fileError').text('Invalid file type. Only Excel 2007 format(.xls) file is allowed.');
            fileInput.val('');
            e.preventDefault();
            isValid = false;
            return false;
        } else {
            $('#fileError').text('');
            isValid = true;
        }

        if (form_being_submitted) {
            alert('Importing OTP Passwords for ' + serviceType + ', please wait a moment..');
            $('#uploadBtn').attr('disabled', true);
            isValid = false;
            return false;
        }

        if (!form_being_submitted && isValid) {
            $('#uploadBtn').val('Importing ' + serviceType + ' OTP Passwords...');
            form_being_submitted = true;
            return true;
        } else {
            return false;
        }
    });

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>