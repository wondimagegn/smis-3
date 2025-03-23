<div class="box">
    <div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-download-outline" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Import New Students to '. Configure::read('ApplicationShortName').'') ?></span>
		</div>
	</div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">

                <?= $this->Form->create('AcceptedStudent', array('controller' => 'acceptedStudents', 'action' => 'import_newly_students', 'type' => 'file', 'onSubmit' => 'return checkForm(this);')); ?>

                <div style="overflow-x:auto;">
                    <table cellpadding="0" cellspacing="0" class="table">
                        <thead>
                            <tr>
                                <td colspan=4>
                                    <br>
									<blockquote>
										<h6 class="text-red"><i class="fa fa-info"></i> &nbsp; Be-aware:</h6>
										<span style="text-align:justify;" class="fs14 text-gray">Before importing the excel, <b class="text-black" style="text-decoration: underline;"><i>make sure that the value of college, region, program, program types, and department(if it exists or needed) fields as listed below.</i></b> 
                                        If you think there is a missing college, region, program type, and program name, department, please contact the system administrator to add them to the system. <br>
                                        <a href="<?= (INCLUDE_STUDENT_NUMBER_IN_IMPORT_TEMPLATE_FILE == 1 ? STUDENT_IMPORT_TEMPLATE_FILE : STUDENT_IMPORT_TEMPLATE_FILE_WITHOUT_STUDENT_NUMBER); ?>">Download Import Template here</a> that shows the required fields and sample pre populated data that is compatible with the system database.</span> 
									</blockquote>
                                </td>
                            </tr>
                        

                            <?php
                            if (isset($non_valide_rows)) { ?>
                                <tr>
                                    <td colspan=4  style="background-color: white;">
                                        <div class="error-box error-message"> 
                                            <!-- <span style='margin-right: 15px;'></span> Correct the following and try again! <br> -->
                                            <ol style="color:red">
                                                <?php
                                                foreach ($non_valide_rows as $k => $v) { ?>
                                                    <li><?= $v; ?></li>
                                                    <?php
                                                } ?>
                                            </ol>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            } ?>
                        </thead>
                        <tbody>
                            
                            <tr>
                                <td style="background-color: white;">
                                    <table cellpadding="0" cellspacing="0" class="table">
                                        <tbody>
                                            <tr>
                                                <th>Import Accepted Students</th>
                                            </tr>
                                            <tr>
                                                <td style="background-color: white;"><?= $this->Form->input('AcceptedStudent.academicyear', array('id' => 'academicyear', 'label' => 'Academic Year: ', 'type' => 'select', 'options' => $acyear_array_data, 'empty' => "[ Select Academic Year ]", 'default' => isset($this->request->data['AcceptedStudent']['academicyear']) && !empty($this->request->data['AcceptedStudent']['academicyear']) ?  $this->request->data['AcceptedStudent']['academicyear'] : '')); ?></td>
                                            </tr>
                                            <tr>
                                                <td style="background-color: white;"><?= $this->Form->file('File'); ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <br> 
                                    <?= $this->Form->submit('Upload', array('id' => 'uploadBtn', 'class' => 'tiny radius button bg-blue')); ?>
                                </td>
                                <td style="background-color: white;">
                                    <table cellpadding="0" cellspacing="0" class="table">
                                        <tbody>
                                            <tr>
                                                <th>Colleges / Institutes / Schools</th>
                                                <?php
                                                foreach ($departments_organized_by_college as $college => $department) { ?>
                                                    <tr>
                                                        <td><h6 class="fs13"><?= $college; ?></h6></td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <table cellpadding="0" cellspacing="0" class="table">
                                                                <?php
                                                                foreach ($department as $k => $dep) { ?>
                                                                   <tr>
                                                                        <td><?= $dep; ?></td>
                                                                    </tr>
                                                                    <?php
                                                                } ?>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                } ?>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                                <td style="background-color: white;">
                                    <table cellpadding="0" cellspacing="0" class="table">
                                        <tbody>
                                            <tr>
                                                <th>Programs</th>
                                            </tr>
                                            <?php
                                            foreach ($programs as $ck => $cv) { ?>
                                                <tr>
                                                    <td><?= $cv; ?></td>
                                                </tr>
                                                <?php
                                            } ?>
                                        </tbody>
                                    </table>
                                    <br>
                                    <table cellpadding="0" cellspacing="0" class="table">
                                        <tbody>
                                            <tr>
                                                <th>Program Types</th>
                                            </tr>
                                            <?php
                                            foreach ($programTypes as $ck => $cv) { ?>
                                               <tr>
                                                    <td><?= $cv; ?></td>
                                                </tr>
                                                <?php
                                            } ?>
                                        </tbody>
                                    </table>
                                </td>
                                <td style="background-color: white;">
                                    <table cellpadding="0" cellspacing="0" class="table">
                                        <tbody>
                                            <tr>
                                                <th>Regions</th>
                                            </tr>
                                            <?php
                                            foreach ($regions as $ck => $cv) { ?>
                                                <tr>
                                                    <td><?= $cv; ?></td>
                                                </tr>
                                                <?php
                                            } ?>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <br>

                <?= $this->Form->end(); ?>

            </div>
        </div>
    </div>
</div>

<script>

	var form_being_submitted = false; /* global variable */

	var checkForm = function(form) {
	
		if (form_being_submitted) {
			alert("Uploading Students, please wait a moment...");
			form.uploadBtn.disabled = true;
			return false;
		}

		form.uploadBtn.value = 'Uploading Students...';
		form_being_submitted = true;
		return true; /* submit form */
	};

	// prevent possible form resubmission of a form 
	// and disable default JS form resubmit warning  dialog  caused by pressing browser back button or reload or refresh button

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>