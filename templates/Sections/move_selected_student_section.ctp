
<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?=  __('Move Students from ' . $previousSectionName['Section']['name'] . ' ('. (isset($previousSectionName['YearLevel']['name']) ? $previousSectionName['YearLevel']['name'] : ($previousSectionName['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial': 'Pre/1st')). ', ' . $previousSectionName['Section']['academicyear'] . ') Section'); ?></span>
		</div>

        <a class="close-reveal-modal">&#215;</a>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
                <div class="row">
                    <div class="large-12 columns">

                        <div style="margin-top: -30px;"></div>
                        <hr>
                        <blockquote>
                            <h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
                            <span style="text-align:justify;" class="fs14 text-gray">
                                <i class="text-black">
                                    Pre Conditions for student section movement: Students that doesn't have any course registration from <?= $previousSectionName['Section']['name']; ?> section or only students that have registration in <?= $previousSectionName['Section']['name']; ?> section with <span class="rejected">all registrered course grades are submitted and all grades are pass grades or doesn't contain (NG, F, DO, I W) grades</span> will be listed here for section movement.
                                   <br><br> Destination section selection options only contain active sections that have the same curriculum, academic year, year level, program and program type to that of <?= $previousSectionName['Section']['name']; ?> section<?= ALLOW_STDENT_SECTION_MOVE_TO_NEXT_YEAR_LEVEL == 1 && $this->Session->read('Auth.User')['role_id'] != ROLE_COLLEGE ? ' and active sections that are one year up, if there are any.' : '.'; ?>
                                </i>
                            <br>
                        </blockquote>
                        <hr>

                        <?php
                        if (isset($studentsections['Student']) && !empty($studentsections['Student'])) { ?>
                            <?= $this->Form->create('Section', array('controller' => 'sections', 'action' => 'section_move_update', "method" => "POST")); ?>
                            <div class="row">
                                <div class="large-12 columns">
                                    <fieldset style="margin: 5px;">
                                        <div class="large-6 columns">
                                            <?= $this->Form->input('Selected_section_id', array('label' => 'Destination Section: ', 'id' => 'selectedSectionId', 'type' => 'select', 'options' => $sections, 'empty' => "[ Select Destination Section ]")); ?>
                                            <?= $this->Form->hidden('previous_section_id', array('value' => $previous_section_id)); ?>
                                        </div>
                                        <div class="large-6 columns">
                                            <br>
                                            <?= $this->Form->Submit(__('Move Selected'), array('div' => false, 'id' => 'SubmitID', 'class' => 'tiny radius button bg-blue', 'name' => 'attach')); ?>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>

                            <br>
                            <h6 id="validation-message_non_selected" class="text-red fs14"></h6>
                            <br>

                            <div style="overflow-x:auto;">
                                <table  cellpadding="0" cellspacing="0" class="table">
                                    <thead>
                                        <tr><th colspan=5><h6 class="fs14 text-gray"><?=  __('List of eligible students to moved from ' . $previousSectionName['Section']['name'] . ' (' . $previousSectionName['Program']['name'] . ', ' . $previousSectionName['ProgramType']['name'] . ' - ' . (!empty($previousSectionName['Department']['name']) ? $previousSectionName['Department']['name'] : $previousSectionName['College']['name']) . ')'); ?></h6></th></tr>
                                        <tr>
                                            <th class="center" style="width: 5%;">#</th>
                                            <th class="center" style="width: 5%;"><?= '' . $this->Form->checkbox("SelectAll", array('id' => 'select-all', 'checked' => '')); ?></th>
                                            <th class="vcenter" style="width: 30%;">Student Name</th>
                                            <th class="center" style="width: 10%;">Sex</th>
                                            <th class="vcenter">Student ID</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $count = 1;
                                        foreach ($studentsections['Student'] as $student) {
                                            if ($student['StudentsSection']['archive'] == 0) { ?>
                                                <tr>
                                                    <td class="center"><?= $count; ?></td>
                                                    <td class="center">
                                                        <div style="margin-left: 15%;"><?= $this->Form->checkbox('Section.' . $count . '.selected_id', array('class' => 'checkbox1')); ?></div>
                                                        <?= $this->Form->hidden('Section.' . $count . '.student_id', array('value' => $student['id'])); ?>
                                                    </td>
                                                    <td class="vcenter"><?= $student['full_name']; ?></td>
                                                    <td class="center"><?= (strcasecmp(trim($student['gender']), 'male') == 0 ? 'M' :(strcasecmp(trim($student['gender']), 'female') == 0 ? 'F' : $student['gender'])); ?></td>
                                                    <td class="vcenter"><?= $student['studentnumber']; ?></td>
                                                </tr>
                                                <?php
                                                $count++;
                                            }
                                        } ?>
                                    </tbody>
                                </table>
                            </div>
                            <br>
                            <?= $this->Form->end(); ?>
                            <?php
                        } else { ?>
                            <div class="large-12 columns">
                            <div id="flashMessage" class="info-box info-message" style="font-family: 'Times New Roman', Times, serif;"><span style="margin-right: 15px;"></span><?= empty($previousSectionName['StudentsSection']) ?  $previousSectionName['Section']['name'] . ' section is empty' : 'No eligible student is found to move from ' . $previousSectionName['Section']['name'] . ' section. All students assigned in this section are registered for published course(s) and grade is not fully submitted'; ?>.</div>
                            </div>
                            <?php
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- <a class="close-reveal-modal">&#215;</a> -->

<script type="text/javascript">

	var form_being_submitted = false;

    var sectionName = "<?= $previousSectionName['Section']['name'];?>";

    const validationMessageNonSelected = document.getElementById('validation-message_non_selected');

	$('#SubmitID').click(function() {
		
		var checkboxes = document.querySelectorAll('input[type="checkbox"]');
		var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);

        var selectedDropDownValue = $('#selectedSectionId').val();
        var selectedDropDownText = $('#selectedSectionId option:selected').text();

        var selectedDropDownTextExploded = selectedDropDownText.split(")  (");

        if (selectedDropDownTextExploded[0] != '') {
            selectedDropDownText = selectedDropDownTextExploded[0] + ')';
        }


        if (selectedDropDownValue == '') {
            alert('Select the destination section to move students from ' + sectionName + ' section.');
			validationMessageNonSelected.innerHTML = 'Select the destination section to move students from ' + sectionName + ' section.';
            $('#selectedSectionId').focus();
			return false;
        }

		//alert(checkedOne);
		if (!checkedOne) {
            alert('At least one student must be selected to move from ' + sectionName + ' to ' + selectedDropDownText + ' section.');
			validationMessageNonSelected.innerHTML = 'At least one student must be selected to move from ' + sectionName + ' to ' + selectedDropDownText + ' section.';
			return false;
		}

		if (form_being_submitted) {
			alert('Moving selected students move from ' + sectionName + ' to ' + selectedDropDownText + ' section. please wait a moment...');
			$('#SubmitID').attr('disabled', true);
			return false;
		}

		var confirmm = confirm('Are you sure you want to the selected students from ' + sectionName + ' to ' + selectedDropDownText + ' section?');

		if (confirmm) {
			$('#SubmitID').val('Moving to ' + selectedDropDownText + '...');
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