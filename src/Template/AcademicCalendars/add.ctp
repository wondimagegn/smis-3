<?php

use Cake\Core\Configure;
use Cake\Utility\Inflector;

?>
<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-calendar" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Define/Setup Academic Calendar'); ?></span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <?= $this->Form->create('AcademicCalendar', array('onSubmit' => 'return checkForm(this);')); ?>
                <div style="margin-top: -30px;">
					<hr>
                    <fieldset style="padding-bottom: 15px;padding-top: 15px;">
                        <!-- <legend>&nbsp;&nbsp; Search / Filter &nbsp;&nbsp;</legend> -->
                        <div class="row">
                            <div class="large-2 columns">
                                <?= $this->Form->input(
                                    'academic_year',
                                    array(
                                        'id' => 'academicYear',
                                        'label' => 'Academic Year: ',
                                        'style' => 'width:90%',
                                        'type' => 'select',
                                        'options' => $acyearArrayData,
                                        'required',
                                        /* 'empty' => "[ Select ]",  */
                                        'default' => isset($defaultacademicyear) ? $defaultacademicyear : ''
                                    )
                                ); ?>
                            </div>
							<div class="large-2 columns">
                                <?= $this->Form->input('semester', array('id' => 'semester', 'label' => 'Semester: ', 'style' => 'width:90%', 'options' => Configure::read('semesters'), 'required', 'empty' => '[ Select ]')); ?>
                            </div>
                            <div class="large-3 columns">
                                <?= $this->Form->input('program_id',  array('id' => 'programType', 'onchange' => 'getDepartmentsOnProgramChange()', 'label' => 'Program: ', 'style' => 'width:90%', 'required', 'empty' => '[ Select ]')); ?>
                            </div>
                            <div class="large-3 columns">
                                <?= $this->Form->input('program_type_id', array('id' => 'programTypeId', 'onchange' => 'getDepartmentsOnProgramChange()', 'label' => 'Program Type: ', 'style' => 'width:90%', 'required', 'empty' => '[ Select ]')); ?>
                            </div>
                            <div class="large-2 columns">
								<h6 id="validation-message_non_selected" class="text-red fs14"></h6>
                                <?= $this->Form->input('year_level_id', array('type' => 'select', 'id' => 'yearLevels', 'multiple' => 'checkbox')); ?>
                            </div>
                        </div>
                    </fieldset>
                </div>

				<h6 id="validation-message_non_selected_department" class="text-red fs14"></h6>

                <!-- AJAX LOAD DATA -->

				<div id="ExclusionInclusion">

                </div>

                <!-- END AJAX LOAD DATA -->

				<?= $this->Form->end(); ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function getDepartmentsOnProgramChange() {
        let academicyearValue = document.getElementById("academicYear").value;
        let semesterValue = document.getElementById("semester").value;
        let programTypeValue = document.getElementById("programType").value;
        let programTypeIdValue = document.getElementById("programTypeId").value;

        $("#ExclusionInclusion").empty().append('<p>Loading ...</p>');
        let formUrl = '/academic-calendars/get-departments-that-have-the-selected-program';

        $.ajax({
            type: 'POST',
            url: formUrl,
            data: $('form').serialize(),
            success: function (response) {
                $("#ExclusionInclusion").empty().append(response);
                $("#AppliedFor").prop('disabled', false);
            },
            error: function (xhr, textStatus) {
                alert("Error: " + textStatus);
            }
        });
    }

    function refreshDiv() {
        getDepartmentsOnProgramChange();
    }

    $("input[type='checkbox']").change(getDepartmentsOnProgramChange);

    let formBeingSubmitted = false;

    function checkForm(form) {
        let yearLevelCheckboxes = document.getElementsByName('data[AcademicCalendar][year_level_id][]');
        let checkedYearLevel = Array.from(yearLevelCheckboxes).some(x => x.checked);

        let departmentCheckboxes = document.getElementsByName('data[AcademicCalendar][department_id][]');
        let checkedDepartment = Array.from(departmentCheckboxes).some(x => x.checked);

        if (!checkedYearLevel) {
            alert('At least one year level must be selected.');
            document.getElementById('validation-message_non_selected').textContent = 'Select Year Level';
            return false;
        }

        if (!checkedDepartment) {
            alert('At least one department must be selected.');
            document.getElementById('validation-message_non_selected_department').textContent = 'Select at least one department';
            return false;
        }

        if (formBeingSubmitted) {
            alert("Setting Academic Calendar, please wait a moment...");
            form.setCalendar.disabled = true;
            return false;
        }

        form.setCalendar.value = 'Setting Academic Calendar...';
        formBeingSubmitted = true;
        return true;
    }
</script>
