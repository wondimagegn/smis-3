
<div class="box">
    <div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-download" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Mass Import Student National IDs'); ?></span>
		</div>
	</div>
    <div class="box-body pad-forty">
        <div class="row">
            <div class="large-12 columns" style="margin-top: -55px;">
                <div class="row">
                    <hr>
                    <blockquote>
                        <h6 class="text-red"><i class="fa fa-info"></i> &nbsp; Be-aware:</h6>
                        <span style="text-align:justify;" class="fs14 text-gray">Before importing the excel, <b class="text-black"><i>make sure that the first row of your excel file is filled with studentnumber and student_national_id fields and you saved your excel file with Excel 97-2003 Format.</i></b> 
                        <br>
                        you can <a href="<?= NATIONAL_ID_IMPORT_TEMPLATE_FILE; ?>">Download Import Template here</a> with the required fields and sample pre-populated data.</span> 
                    </blockquote>
                    <hr>
                    <?php
                    /* if (isset($invalid_rows) && !empty($invalidStudentIds)) { ?>
                        <table cellpadding="0" cellspacing="0" class="table">
                            <tr>
                                <td colspan=4  style="background-color: white;">
                                    <div class="error-box error-message" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"> 
                                        <!-- <span style='margin-right: 15px;'></span> Correct the following and try again! <br> -->
                                        <ol style="color:red">
                                            <?php
                                            foreach ($invalid_rows as $k => $v) { ?>
                                                <li><?= $v; ?></li>
                                                <?php
                                            } ?>
                                        </ol>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <?php
                    }  */

                    if (isset($results_to_html_table) && !empty($results_to_html_table)) { ?>
                        <br>
                        <h6 class="fs16 text-gray">Import results:</h6>
                        <table cellpadding="0" cellspacing="0" class="table">
                            <thead>
                                <tr>
                                    <th class="vcenter">Student ID</th>
                                    <th class="vcenter">National ID</th>
                                    <th class="vcenter">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($results_to_html_table as $key => $result) { ?>
                                    <tr>
                                        <td class="vcenter"><?= $result['studentnumber'] ?></td>
                                        <td class="vcenter"><?= $result['student_national_id'] ?></td>
                                        <td class="vcenter" style="width: 70%;"><?= $result['status'] ?></td>
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

            <fieldset>
                <div class="large-6 columns" style="margin-top: 30px;">
                    <?= $this->Form->create('Student', array('controller' => 'students', 'action' => 'mass_import_student_national_id', 'type' => 'file',  'onSubmit' => 'return checkForm(this);')); ?>
                    <label>
                        <strong>Student List : </strong>
                        <?= $this->Form->file('File', array('label' => 'Excel', 'name' => 'data[Student][xls]', 'required')); ?>
                    </label>
                    <br />
                    <br />

                    <?= $this->Form->submit('Upload', array('id' => 'uploadBtn', 'class' => 'tiny radius button bg-blue')); ?>
                    <?= $this->Form->end(); ?>
                </div>
                <div class="large-6 columns" style="margin-top: 30px;">
                    <div class="your-account">
                        <div class="row">
                            <div class="medium-3 columns">
                                <div class="circlestat" data-dimension="90" data-text="<?= number_format((($nonGraduatedStudentCount / $totalStudentCount) * 100), 2, '.', '') . '' . '%'; ?>" data-width="8" data-fontsize="16" data-percent="<?= ((($nonGraduatedStudentCount * 100) / $totalStudentCount)); ?>" data-fgcolor="#222" data-border="5" data-bgcolor="#D5DAE6" data-fill="#FFF"></div>
                            </div>
                            <div class="medium-9 columns ">
                                <div style="margin:0 10px;padding:0 0 0 20px" class="summary-border-left">
                                    <?php 
                                    if ((($nonGraduatedStudentCount / $totalStudentCount) * 100) != 100) { ?>
                                        <h4>Student National IDs import isn't complete!</h4>
                                        <?php
                                    } else { ?>
                                        <h4>Student National IDs import complete!</h4>
                                        <?php
                                    } ?>
                                </div>
                            </div>
                        </div>
                    </div> 
                </div>
            </fieldset>

        </div>
    </div>
</div>

<script>
    var form_being_submitted = false; 

	var checkForm = function(form) {
	
		if (form_being_submitted) {
			alert("Importing National IDs, please wait a moment...");
			form.uploadBtn.disabled = true;
			return false;
		}

		form.uploadBtn.value = 'Importing National IDs...';
		form_being_submitted = true;
		return true; 
	};

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>