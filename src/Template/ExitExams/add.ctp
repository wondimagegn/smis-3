<div class="box">
    <div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-plus" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Record Exit Exam Result of a Student'); ?></span>
		</div>
	</div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns" style="margin-top: -15px;">
                <?= $this->Form->create('ExitExam', array('onSubmit' => 'return checkForm(this);')); ?>
                <?php
                if ($role_id != ROLE_STUDENT && !isset($studentIDs)) { ?>
                    <fieldset style="padding-bottom: 5px;">
                        <legend>&nbsp;&nbsp; Student Number / ID &nbsp;&nbsp;</legend>
                        <div class="row">
                            <div class="large-4 columns">
                                <?= $this->Form->input('Search.studentID', array('label' => false, 'placeholder' => 'Type Student ID...', 'required', 'maxlength' => 25)); ?>
                            </div>
                        </div>
                    </fieldset>
                    <?= $this->Form->Submit('Search', array('name' => 'continue', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
                    <?php
                }

                if (isset($studentIDs)) {
                    $yFrom = date('Y') - 1;
                    $yTo = date('Y'); ?>

                    <div class="large-8 columns">
                        <?= $this->element('student_basic'); ?>
                    </div>
                    <div class="large-4 columns">
                        <table cellpadding="0" cellspacing="0" class="table">
                            <tr>
                                <td>
                                    <?= (isset($exit_exam_id) && is_numeric($exit_exam_id) && $exit_exam_id > 0 ? $this->Form->hidden('ExitExam.id', array('value' => $exit_exam_id)) : (isset($this->request->data['ExitExam']['id']) && !empty($this->request->data['ExitExam']['id']) && $this->request->data['ExitExam']['id'] > 0 ? $this->Form->hidden('ExitExam.id', array('value' => $this->request->data['ExitExam']['id'])) : '')); ?>
                                    <?= (isset($this->request->data['ExitExam']['student_id']) && !empty($this->request->data['ExitExam']['student_id']) && $this->request->data['ExitExam']['student_id'] > 0 ? $this->Form->hidden('ExitExam.student_id',  array('value' => $this->request->data['ExitExam']['student_id'])) : (isset($student_section_exam_status['StudentBasicInfo']['id']) && !empty($student_section_exam_status['StudentBasicInfo']['id']) && $student_section_exam_status['StudentBasicInfo']['id'] > 0 ? $this->Form->hidden('ExitExam.student_id',  array('value' => $student_section_exam_status['StudentBasicInfo']['id'])) : '')); ?>
                                    <div class="large-12 columns">
                                        <?= $this->Form->input('ExitExam.course_id', array('style' => 'width:95%;', 'label' => 'Course: ', 'options' => $courses)); ?>
                                    </div>
                                    <div class="large-12 columns">
                                        <?= $this->Form->input('ExitExam.type', array('style' => 'width:95%;', 'label' => 'Exam Type: ', 'options' => $exit_exam_types, 'type' => 'select')); ?>
                                    </div>
                                    <div class="large-12 columns">
                                        <?= $this->Form->input('ExitExam.exam_date', array('style' => 'width:30%;', 'label' => 'Exam Date: ', 'minYear' => $yFrom, 'maxYear' => $yTo, 'default' => $default_exam_date)); ?>
                                    </div>
                                    <div class="large-12 columns">
                                        <?php
                                        if (isset($exit_exam_id) && is_numeric($exit_exam_id) && $exit_exam_id > 0) { ?>
                                            <?= $this->Form->input('ExitExam.result', array('style' => 'width:30%;', 'label' => 'Result:', 'min' => 0, 'max' => 100, 'step' => 'any', 'disabled', 'value' => $latest_exit_exam_result)); ?>
                                            <?= $this->Form->hidden('ExitExam.result',  array('value' => $latest_exit_exam_result)); ?>
                                            <?php
                                        } else { ?>
                                            <?= $this->Form->input('ExitExam.result', array('style' => 'width:30%;', 'label' => 'Result:', 'min' => 0, 'max' => 100, 'step' => 'any')); ?>
                                            <?php
                                        } ?>
                                    </div>
                                    <hr>
                                    <div class="large-12 columns">
                                        <?php
                                        if (isset($student_section_exam_status['StudentBasicInfo']) && $student_section_exam_status['StudentBasicInfo']['graduated'] == 0) { ?>
                                            <?= $this->Form->Submit('Save', array('name' => 'saveExitExam', 'id' => 'SubmitID', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
                                            <?php
                                        } ?>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <?php
                } ?>
                <?= $this->Form->end(); ?>
            </div>
        </div>
    </div>
</div>

<script>
	var form_being_submitted = false; 

	var checkForm = function(form) {
	
		if (form_being_submitted) {
			alert("Saving Exit Exam Result, please wait a moment...");
			form.SubmitID.disabled = true;
			return false;
		}

		form.SubmitID.value = 'Saving Exit Exam Result...';
		form_being_submitted = true;
		return true; /* submit form */
	};

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>