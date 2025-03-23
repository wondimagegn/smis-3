<div class="box">
    <div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-check-outline" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Batch Regenerate Student Academic Status'); ?></span>
		</div>
	</div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <?= $this->Form->create('StudentStatusPattern'); ?>
                <div style="margin-top: -30px;">
                    <hr>
                    <div class="examGrades <?= $this->request->action; ?>">
                        <div onclick="toggleViewFullId('ListSection')">
                            <?php
                            if (!empty($sections)) {
                                echo $this->Html->image('plus2.gif', array('id' => 'ListSectionImg')); ?>
                                <span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListSectionTxt">Display Filter</span><?php
                            } else {
                                echo $this->Html->image('minus2.gif', array('id' => 'ListSectionImg')); ?>
                                <span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListSectionTxt">Hide Filter</span>
                                <?php
                            } ?>
                        </div>
                        <div id="ListSection" style="display:<?= (!empty($sections) ? 'none' : 'display'); ?>">
                            <fieldset style="padding-bottom: 5px;padding-top: 5px;">
                                <legend>&nbsp;&nbsp; Search / Filter &nbsp;&nbsp;</legend>
                                <div class="row">
                                    <div class="large-3 columns">
                                        <?= $this->Form->input('acadamic_year', array('id' => 'AcadamicYear', 'label' => 'Admission Year: ', 'class' => 'fs14', 'style' => 'width:90%;',  'options' => $acyear_array_data, 'default' => (isset($academic_year_selected) ? $academic_year_selected : $defaultacademicyear))); ?>
                                    </div>
                                    <div class="large-3 columns">
                                        <?= $this->Form->input('year_level_id', array('id' => 'YearLevelId', 'label' => 'Year Level', 'class' => 'fs14', 'style' => 'width:90%;', 'options' => $yearLevels)); ?>
                                    </div>
                                    <div class="large-3 columns">
                                        <?= $this->Form->input('program_id', array('id' => 'Program', 'class' => 'fs14', 'label' => 'Program: ', 'style' => 'width:90%;', 'options' => $programs, 'default' => (isset($program_id) ? $program_id : false))); ?>
                                    </div>
                                    <div class="large-3 columns">
                                        <?= $this->Form->input('program_type_id', array('id' => 'ProgramType', 'class' => 'fs14', 'label' => 'Program Type: ', 'style' => 'width:90%;', 'options' => $program_types, 'default' => (isset($program_type_id) ? $program_type_id : false))); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="large-3 columns">
                                        <?= $this->Form->input('status_acadamic_year', array('id' => 'AcadamicYear', 'label' => 'Status Acadamic Year: ', 'class' => 'fs14', 'style' => 'width:90%;',  'options' => $acyear_array_data, 'default' => (isset($academic_year_selected) ? $academic_year_selected : $defaultacademicyear))); ?>
                                    </div>
                                    <div class="large-3 columns">
                                        <?= $this->Form->input('semester', array('label' => 'Status Semester: ', 'class' => 'fs14', 'style' => 'width:90%;', 'options' => Configure::read('semesters'))); ?>
                                    </div>
                                    <div class="large-3 columns">
                                        <?= $this->Form->input('name', array('id' => 'name', 'class' => 'fs14', 'style' => 'width:90%;', 'label' => 'Name: ')); ?>
                                    </div>
                                    <div class="large-3 columns">
                                        &nbsp;
                                    </div>
                                </div>
                                <div class="row">
                                    <?php
                                    if ((!empty($departments[0]) && $departments[0] != 0) || !empty($department_ids) ) { ?>
                                        <div class="large-6 columns">
                                            <?= $this->Form->input('department_id', array('class' => 'fs14', 'label' => 'Department: ',  'style' => 'width:90%;', 'options' => $departments, 'default' => (isset($department_id) ? $department_id : false))); ?>
                                        </div>
                                        <?php
                                    } else if (!empty($colleges) || !empty($college_ids)) { ?>
                                        <div class="large-6 columns">
                                            <?= $this->Form->input('college_id', array('class' => 'fs14', 'label' => 'College/Institute/School: ', 'style' => 'width:90%;', 'options' => $colleges, 'default' => (isset($college_id) ? $college_id : false))); ?>
                                        </div>
                                        <?php
                                    } ?>
                                </div>
                                <hr>
                                <?= $this->Form->submit(__('List Students'), array('name' => 'listSections', 'class' => 'tiny radius button bg-blue', 'div' => false, 'id' => 'listSections')); ?>
                            </fieldset>
                            <br>

                            <?php
                            /* echo $this->Form->submit(__('Regenerate All Status From Admission Till Current'), array('name' => 'regenerateAllStatus','class'=>'tiny radius button bg-blue', 'div' => false,'id'=>'regenerateAllStatus'));
                                <br/><strong>Note:</strong> Very expensive process, not recommended to do it via interface.
                            */ ?>
                        </div>
                        <hr>


                        <?php
                        //debug($departments);
                        //debug($section_id);
                        if (!empty($sections)) { ?>
                            <table cellspacing="0" cellpadding="0" class="table">
                                <tr>
                                    <td class="center">
                                        <div class="row">
                                            <div class="large-2 columns" style="margin-top: 10px;">
                                                <br>
                                                <span style="padding-left: 25px;">Section:</span>
                                            </div>
                                            <div class="large-8 columns">
                                                <br>
                                                <?= $this->Form->input('section_id', array('class' => 'fs14', 'id' => 'Section', 'label' => false, 'style' => 'width:90%;',  'empty' => '[ Select Section ]', 'options' => $sections, 'default' => (isset($section_id) && !empty($section_id)  && $section_id != 0 ? $section_id : 0))); ?>
                                            </div>
                                            <div class="large-2 columns">
                                                &nbsp;
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <hr>
                            <?php
                        }

                        if (isset($students_in_section) && empty($students_in_section) && !empty($section_id)) { ?>
                            <div id="flashMessage" class="info-box info-message" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style="margin-right: 15px;"></span>There is no student in the selected section.</div>
                            <?php
                        } else if (isset($students_in_section) && !empty($students_in_section)) { ?>
                            <h6 class="fs14 text-gray"> Please select student/s for whom you want to regenerate academic status.</h6>
                            <br>

                            <h6 id="validation-message_non_selected" class="text-red fs14"></h6>

                            <div style="overflow-x:auto;">
                                <table cellpadding="0" cellspacing="0" class="table">
                                    <thead>
                                        <tr>
                                            <td style="width: 4%;" class="center"><?= $this->Form->input('select_all', array('type' => 'checkbox', 'id' => 'select-all', 'div' => false, 'label' => false)); ?></td>
                                            <td style="width: 3%;" class="center">#</td>
                                            <td style="width: 30%;" class="vcenter">Student Name</td>
                                            <td style="width: 10%;" class="center">Sex</td>
                                            <td style="width: 15%;" class="center">Student ID</td>
                                            <td style="width: 38%;" class="center">Last Generated</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $st_count = 0;
                                        foreach ($students_in_section as $key => $student) {
                                            ++$st_count; ?>
                                            <tr>
                                                <td class="center">
                                                    <div style="margin-left: 15%;">
                                                        <?= $this->Form->input('StudentStatusPattern.' . $st_count . '.gp', array('type' => 'checkbox', 'class' => 'checkbox1', 'label' => false, 'id' => 'StudentSelection' . $st_count)); ?>
                                                        <?= $this->Form->input('StudentStatusPattern.' . $st_count . '.student_id', array('type' => 'hidden', 'value' => $student['Student']['id'])); ?>
                                                    </div>
                                                </td>
                                                <td class="center"><?= $st_count; ?></td>
                                                <td class="vcenter"><?= $student['Student']['full_name']; ?></td>
                                                <td class="center"><?= (strcasecmp(trim($student['Student']['gender']), 'male') == 0 ? 'M' :(strcasecmp(trim($student['Student']['gender']), 'female') == 0 ? 'F' : $student['Student']['gender'])); ?></td>
                                                <td class="center"><?= $student['Student']['studentnumber']; ?></td>
                                                <td class="center"><?= (isset($student['Student']['StudentExamStatus']) && !empty($student['Student']['StudentExamStatus'][0]) ? ($this->Time->format("M j, Y h:i A", $student['Student']['StudentExamStatus'][0]['modified'], NULL, NULL)) . ' (' . $student['Student']['StudentExamStatus'][0]['academic_year'] . ' - ' . $student['Student']['StudentExamStatus'][0]['semester'] . ')' : '') ; ?></td>
                                            </tr>
                                            <?php
                                        } ?>
                                    </tbody>
                                </table>
                            </div>
                            <hr>
                            <?= $this->Form->submit(__('Regenerate Student Status'), array('name' => 'regenerateStatus', 'id' => 'regenerateStatus', 'div' => false, 'class' => 'tiny radius button bg-blue')); ?>
                            <?php
                        } ?>
                    </div>
                    <?= $this->Form->end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    //$(document).ready(function() {
        /* $('#listSections').on('click', function () {
            window.location.replace("/student_status_patterns/regenerate_academic_status");
            alert('Hello');
            //return false;

        }); */

        $("#Section").change(function() {
            var s_id = $("#Section").val();
            //if (s_id != '') {
                window.location.replace("/student_status_patterns/<?= $this->request->action; ?>/" + s_id);
            //}
        });
    //});

    //  window.location.replace("/student_status_patterns/<?= $this->request->action; ?>/"); 

    var number_of_students = <?= (isset($students_in_section) ? count($students_in_section) : 0); ?>;

    function check_uncheck(id) {
        var checked = ($('#' + id).attr("checked") == 'checked' ? true : false);
        for (i = 1; i <= number_of_students; i++) {
            $('#StudentSelection' + i).attr("checked", checked);
        }
    }

    function toggleView(obj) {
        if ($('#c' + obj.id).css("display") == 'none') {
            $('#i' + obj.id).attr("src", '/img/minus2.gif');
        } else {
            $('#i' + obj.id).attr("src",  '/img/plus2.gif');
        }
        $('#c' + obj.id).toggle("slow");
    }

    function toggleViewFullId(id) {
        if ($('#' + id).css("display") == 'none') {
            $('#' + id + 'Img').attr("src", '/img/minus2.gif');
            $('#' + id + 'Txt').empty();
            $('#' + id + 'Txt').append('Hide Filter');
        } else {
            $('#' + id + 'Img').attr("src", '/img/plus2.gif');
            $('#' + id + 'Txt').empty();
            $('#' + id + 'Txt').append('Display Filter');
        }
        $('#' + id).toggle("slow");
    }

    var form_being_submitted = false;

    const validationMessageNonSelected = document.getElementById('validation-message_non_selected');

	$('#regenerateStatus').click(function() {

        var checkboxes = document.querySelectorAll('input[type="checkbox"]');
		var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);
		var checkedCount = Array.prototype.slice.call(checkboxes).filter(x => x.checked).length;

        //alert(checkedOne);

		if (!checkedOne) {
            alert('At least one student must be selected to regenerate status.');
			validationMessageNonSelected.innerHTML = 'At least one student must be selected to regenerate status.';
			return false;
		}

		if (form_being_submitted) {
			alert("Regenerating status for the selected (" + checkedCount + ") students , please wait a moment...");
			$('#regenerateStatus').attr('disabled', true);
			return false;
		}

		$('#regenerateStatus').val('Regenerating Student Status...');
        $('#regenerateStatus').attr('disabled', false);
		form_being_submitted = true;

		//return confirm("Are you sure you want to regenerate status for the selected (" + checkedCount + ") students?");

	});
</script>