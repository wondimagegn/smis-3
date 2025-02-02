<div class="box">
    <div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-download" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Bulk Import Exit Exam Results from Excel(xls)') ?></span>
		</div>
	</div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">

                <?= $this->Form->create('ExitExam', array('controller' => 'exitExam', 'action' => 'import_exit_exam_results', 'type' => 'file', 'onSubmit' => 'return checkForm(this);')); ?>

                <div style="overflow-x:auto;">
                    <table cellpadding="0" cellspacing="0" class="table">
                        <thead>
                            <tr>
                                <td colspan=4>
                                    <br>
									<blockquote>
										<h6 class="text-red"><i class="fa fa-info"></i> &nbsp; Be-aware:</h6>
										<span style="text-align:justify;" class="fs14 text-gray">Before importing the excel, <b class="text-black" style="text-decoration: underline;"><i>make sure that the value of studentnumber, result and exam_date fields as listed in the first row of your excel file.</i></b> 
                                        <br>
                                        <a href="<?= EXIT_EXAM_IMPORT_TEMPLATE_FILE; ?>">Download Import Template here</a> that shows the required fields and sample pre populated data.</span> 
									</blockquote>
                                </td>
                            </tr>
                        
                            <?php
                            $yFrom = date('Y') - 1;
                            $yTo = date('Y');
                            $default_exam_date = date('Y-m-d');

                            if (isset($invalid_rows)) { ?>
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
                                <?php
                            } ?>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="background-color: white;">
                                    <table cellpadding="0" cellspacing="0" class="table">
                                        <tbody>
                                            <tr>
                                                <td style="background-color: white;">
                                                    <div class="large-4 columns">
                                                        <?= $this->Form->input('exam_date', array('style' => 'width:30%;', 'label' => 'Exam Date: ', 'minYear' => $yFrom, 'maxYear' => $yTo, 'default' => $default_exam_date)); ?>
                                                    </div>
                                                    <div class="large-5 columns">
                                                        <br>
                                                        <div style="padding-top: 10px;">
                                                            <?= $this->Form->file('File'); ?>
                                                        </div>
                                                    </div>
                                                    <div class="large-3 columns">
                                                        <br>
                                                        <?= $this->Form->submit('Upload', array('id' => 'uploadBtn', 'class' => 'tiny radius button bg-blue')); ?>
                                                    </div>
                                                </td>
                                            </tr>
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

	var form_being_submitted = false; 

	var checkForm = function(form) {
	
		if (form_being_submitted) {
			alert("Importing Exit Exam Results, please wait a moment...");
			form.uploadBtn.disabled = true;
			return false;
		}

		form.uploadBtn.value = 'Importing Exit Exam Results...';
		form_being_submitted = true;
		return true; 
	};

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>