<div class="box">
    <div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Maintain Course Registration'); ?></span>
		</div>
	</div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">

                <?= $this->Form->create('CourseRegistration'); ?>

                <div style="margin-top: -30px;">
                    <hr>

                    <div onclick="toggleViewFullId('ListPublishedCourse')">
                        <?php
                        if (!empty($turn_off_search) || empty($students) || empty($studentss)) {
                            echo $this->Html->image('plus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
                            <span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Display Filter</span>
                            <?php
                        } else {
                            echo $this->Html->image('minus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
                            <span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Hide Filter</span>
                            <?php
                        } ?>
                    </div>

                    <div id="ListPublishedCourse" style="display:<?= (isset($hide_search) || !empty($students) || !empty($studentss) ? 'none' : 'display'); ?>">
                    
                        <fieldset style="padding-bottom: 0px;">
                            <legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend>
                            <div class="row">
                                <div class="large-3 columns">
                                    <?= $this->Form->input('Student.program_id', array('label' => 'Program: ', 'id' => 'ProgramId', 'style' => 'width: 90%;', 'required', 'onchange' => 'updateCourseListOnChangeofOtherField();updateDepartmentListOnProgramChange();')); ?>
                                </div>
                                <div class="large-3 columns">
                                    <?= $this->Form->input('Student.program_type_id', array('label' => 'Program Type: ', 'id' => 'ProgramTypeId', 'style' => 'width: 90%;', 'required', 'onchange' => 'updateCourseListOnChangeofOtherField()')); ?>
                                </div>
                                <div class="large-2 columns">
                                    <?= $this->Form->input('Student.academicyear', array('label' => 'Academic Year: ', 'type' => 'select', 'required', 'options' => $acyear_array_data, 'required', 'id' => 'AcademicYear', 'style' => 'width: 90%;', 'onchange' => 'updateCourseListOnChangeofOtherField()', 'default' => isset($this->request->data['Student']['academicyear']) ? $this->request->data['Student']['academicyear'] : (isset($defaultacademicyear) ? $defaultacademicyear : ''))); ?>
                                </div>
                                <div class="large-2 columns">
                                    <?= $this->Form->input('Student.semester', array('label' => 'Semester: ', 'id' => 'Semester', 'style' => 'width: 90%;', 'onchange' => 'required', 'updateCourseListOnChangeofOtherField()', 'options' => Configure::read('semesters'))); ?>
                                </div>
                                <?php
                                if (isset($departments) && !empty($departments)) { ?>
                                    <div class="large-2 columns">
                                        <?= $this->Form->input('Student.year_level_id', array('label' => 'Year Level: ', 'id' => 'YearLevelId', 'style' => 'width: 90%;', 'required', 'onchange' => 'updateCourseListOnChangeofOtherField()')); ?>
                                    </div>
                                    <?php
                                } else { ?>
                                    <div class="large-2 columns">
                                        &nbsp;
                                    </div>
                                    <?php
                                } ?>
                            </div>
                            <div class="row">
                                <div class="large-6 columns">
                                    <?php
                                    if (isset($departments) && !empty($departments)) {
                                        echo $this->Form->input('Student.department_id', array('label' => 'Department: ', 'id' => 'DepartmentId', 'style' => 'width: 95%;', 'required', 'onchange' => 'updateCourseListOnChangeofOtherField()'));
                                    } else if (isset($colleges) && !empty($colleges)) {
                                        echo $this->Form->input('Student.college_id', array('onchange' => 'updateCourseListOnChangeofOtherField()', 'id' => 'CollegeId', 'required', 'style' => 'width: 95%;', 'label' => 'College: '));
                                    } ?>
                                </div>
                                <div class="large-6 columns">
                                    <?= $this->Form->input('Student.section_id', array('label' => 'Section: ', 'id' => 'SectionId', 'style' => 'width: 98%;', 'options' => $sections, 'required')); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="large-3 columns">
                                    <?= $this->Form->input('Student.studentnumber', array('id' => 'studentNumber', 'style' => 'width: 95%;', 'label' => 'Student ID: ', 'div' => false, 'required'=> 'false', 'placeholder' => 'Type Optional Student ID here...', 'maxlength' => 25)); ?>
                                </div>
                            </div>
                            <hr>
                            <?= $this->Form->Submit('Search', array('div' => false, 'class' => 'tiny radius button bg-blue', 'name' => 'continue', 'id' => 'Search')); ?>
                        </fieldset>
                    </div>
                </div>
                <hr>

                <div class="smallheading">
                    <h6 class="fs15 text-gray">Maintain course registration for <span id="DeptYrAcySem"></span></h6>
                    <hr>
                </div>
                
                <div>
                    <?php
                    if (isset($published_courses) && !empty($published_courses)) {
                        echo $this->element('student_basic');
                        echo $this->element('registration/student_register');
                    } else {
                        if (isset($students) && !empty($students)) {
                            //debug($students);
                            $st_count = 0;
                            foreach ($students as $desdetail => $stuList) {
                                $sectionDetail = explode('~', $desdetail);
                                //debug($stuList[0]['Student']['program_id']);
                                ?>

                                <!-- <div class='fs16'> Program: <?php //echo $sectionDetail[0]; ?></div>
                                <div class='fs16'> Program Type: <?php //echo $sectionDetail[1]; ?></div>
                                <div class='fs16'> Academic Year: <?php //echo $academic_year; ?></div>
                                <div class='fs16'> Year Level: <?php //echo $sectionDetail[2]; ?></div>
                                <div class='fs16'> Section: <?php //echo $sectionDetail[3]; ?></div>
                                <br> -->

                                <h6 id="validation-message_non_selected" class="text-red fs14"></h6>
				                <br>

                                <div style="overflow-x:auto;">
                                    <table cellpadding=0 cellspacing=0 class="table">
                                        <thead>
                                            <tr>
                                                <td colspan=9 style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85); line-height: 1.5;">
                                                    <span style="font-size:16px;font-weight:bold; margin-top: 25px;"> Section: <?= $sectionDetail[3]; ?></span>
                                                    <br>
                                                    <span class="text-gray" style="padding-top: 13px; font-size: 13px; font-weight: bold"> 
                                                        <?= (isset($sectionDetail[0]) && !empty($sectionDetail[0]) ? $sectionDetail[0] : $program_name); ?> &nbsp;|&nbsp; <?= (isset($sectionDetail[1]) && !empty($sectionDetail[1]) ? $sectionDetail[1] : $program_type_name); ?> &nbsp;|&nbsp;
                                                        <?= (isset($department_name) && !empty($department_name['Department']['name']) ? $department_name['Department']['name'] :( isset($college_name) && !empty($college_name['College']['name']) ? $college_name['College']['name'] . (isset($stuList[0]['Student']['program_id']) && $stuList[0]['Student']['program_id'] == PROGRAM_REMEDIAL ? ' - Remedial Program' : ' - Pre/Freshman') : '')) ; ?><br>
                                                        <?= (isset($stuList[0]['Student']['program_id']) && $stuList[0]['Student']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial Program' : (isset($sectionDetail[2])  ?  $sectionDetail[2] : (isset($year_level_id) && !empty($year_level_id) ? $year_level_id : 'Pre/1st'))); ?> &nbsp;|&nbsp; <?= (isset($academic_year) && !empty($academic_year) ? $academic_year : $defaultacademicyear); ?> &nbsp;|&nbsp; <?= ($semester == 'I' ? '1st' :($semester == 'II' ? '2nd' :($semester == 'III' ? '3rd': $semester))); ?> semester
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th class="center" style="width: 5%;"><div style="margin-left: 30%;"><?= $this->Form->input('select_all', array('type' => 'checkbox', 'id' => 'select-all', 'div' => false, 'label' => false)); ?></div> <!-- <label for="select-all">All</label> --></th>
                                                <th class="center" style="width: 3%;">#</th>
                                                <th class="vcenter">Full Name</th>
                                                <th class="center">Sex</th>
                                                <th class="center">Student ID</th>
                                                <th class="center">Department</th>
                                                <th class="center">Program</th>
                                                <th class="center">Program Type</th>
                                                <th class="center"> 
                                                    <?php // __('Actions'); ?>
                                                    <?php //echo $this->Html->link(__('Register All'), array('action' => 'maintain_registration',0,$sectionDetail[4])); ?>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $count = 1;
                                            foreach ($stuList as $student) { ?>
                                                <tr>
                                                    <td class="center">
                                                        <div style="margin-left: 30%;"><?= $this->Form->input('CourseRegistration.' . $count . '.ggp', array('type' => 'checkbox', 'class' => 'checkbox1', 'label' => false, 'id' => 'StudentSelection' . $count)); ?></div>
                                                        <?= $this->Form->hidden('CourseRegistration.' . $count . '.student_id', array('value' => $student['Student']['id'], 'class' => 'checkbox1', 'label' => false)); ?>
                                                    </td>
                                                    <td class="center"><?= $count++; ?></td>
                                                    <td class="vcenter"><?= $this->Html->link($student['Student']['full_name'], '#', array('class' => 'jsview', 'data-animation' => "fade", 'data-reveal-id' => 'myModal', 'data-reveal-ajax' => "/students/get_modal_box/" . $student['Student']['id'])); ?></td>
                                                    <td class="center"><?= (strcasecmp(trim($student['Student']['gender']), 'male') == 0 ? 'M' :(strcasecmp(trim($student['Student']['gender']), 'female') == 0 ? 'F' : $student['Student']['gender'])); ?></td>
                                                    <td class="center"><?= $student['Student']['studentnumber']; ?></td>
                                                    <td class="center"><?= (isset($student['Department']['name']) ? $student['Department']['name'] :($student['Program']['id'] == PROGRAM_REMEDIAL ? 'Remedial Program' : 'Pre/Freshman')); ?></td>
                                                    <td class="center"><?= $student['Program']['name']; ?></td>
                                                    <td class="center"><?= $student['ProgramType']['name']; ?></td>
                                                    <td class="center"><?php //echo $this->Html->link(__('Register'), array('action' => 'maintain_registration', $student['Student']['id'])); ?></td>
                                                </tr>
                                                <?php 
                                            } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <hr>
                                <?= $this->Form->Submit('Register Selected Students', array('div' => false, 'class' => 'tiny radius button bg-blue', 'id' => 'registerSelected',  'name' => 'registerSelected_' . $st_count++)); ?>
                                <?= $this->Form->input('register_count', array('type' => 'hidden', 'value' => ($st_count - 1))); ?>
                                <?php
                            }
                        }
                    } ?>
                </div>

                <div>
                    <?php
                    if (isset($studentss) && !empty($studentss)) {
                        foreach ($studentss as $pk => $pv) {
                            if (!empty($pk)) {
                                echo "<div class='smallheading'> Program:" . $pk . "</div>";
                                foreach ($pv as $ptk => $ptv) {
                                    if (!empty($ptk)) {
                                        echo "<div class='fs16'> Program Type: " . $ptk . "</div>";
                                        foreach ($ptv as $yk => $yv) {
                                            if (!empty($yv)) {
                                                if ($yk == 0) {
                                                    echo "<div class='fs16'> Year Level: Freshman</div>";
                                                } else {
                                                    echo "<div class='fs16'> Year Level: " . $yearLevels[$yk] . "</div>";
                                                }

                                                foreach ($yv as $section_name => $section_value) { ?>
                                                    <div class='fs16'> Section : <?= $sections[$section_name]; ?></div>
                                                    <div style="overflow-x:auto;">
                                                        <table cellpadding=0 cellspacing=0 class="table">
                                                            <thead>
                                                                <tr>
                                                                    <td colspan=8 style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85); line-height: 1.5;">
                                                                        <span style="font-size:16px;font-weight:bold; margin-top: 25px;"> Section: <?= $sectionDetail[3]; ?></span>
                                                                        <br>
                                                                        <span class="text-gray" style="padding-top: 13px; font-size: 13px; font-weight: bold"> 
                                                                            <?= (isset($pk) && !empty($pk) ? $pk : $program_name); ?> &nbsp;|&nbsp; <?= (isset($ptk) && !empty($ptk) ? $ptk : $program_type_name); ?> &nbsp;|&nbsp;
                                                                            <?= (isset($department_name) && !empty($department_name['Department']['name']) ? $department_name['Department']['name'] : (isset($college_name) && !empty($college_name['College']['name']) ? $college_name['College']['name'] . ' - Pre/Freshman' : '')) ; ?><br>
                                                                            <?= (isset($yearLevels[$yk]) && !empty($yearLevels[$yk]) ? $yearLevels[$yk] : 'Pre/1st'); ?> &nbsp;|&nbsp; <?= isset($sectionDetail[2]) && !empty($sectionDetail[2]) ? $sectionDetail[2] : (isset($academic_year) && !empty($academic_year) ? $academic_year : $defaultacademicyear); ?> &nbsp;|&nbsp; <?= ($semester == 'I' ? '1st' :($semester == 'II' ? '2nd' :($semester == 'III' ? '3rd': $semester))); ?> semester
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <th class="center">#</th>
                                                                    <th class="vcenter"><?= ('Full Name'); ?></th>
                                                                    <th class="center" ><?= ('Student ID'); ?></th>
                                                                    <th class="center"><?= ('Sex'); ?></th>
                                                                    <th class="center" ><?= ('Department'); ?></th>
                                                                    <th class="center" ><?= ('Program'); ?></th>
                                                                    <th class="center" ><?= ('Program Type'); ?></th>
                                                                    <th class="center"><?php // __('Actions'); ?></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $count = 1;
                                                                foreach ($section_value as $student) { ?>
                                                                    <tr>
                                                                        <td class="center"><?= $count++; ?></td>
                                                                        <td class="vcenter"><?= $this->Html->link($student['Student']['full_name'], '#', array('class' => 'jsview', 'data-animation' => "fade", 'data-reveal-id' => 'myModal', 'data-reveal-ajax' => "/students/get_modal_box/" .$student['Student']['id'])); ?></td>
                                                                        <td class="center"><?= $student['Student']['studentnumber']; ?></td>
                                                                        <td class="center"><?= (strcasecmp(trim($student['Student']['gender']), 'male') == 0 ? 'M' :(strcasecmp(trim($student['Student']['gender']), 'female') == 0 ? 'F' : $student['Student']['gender'])); ?></td>
                                                                        <td class="center"><?= (isset($student['Department']['name']) ? $student['Department']['name'] : ($student['Program']['id'] == PROGRAM_REMEDIAL ? 'Remedial Program' : 'Pre/Freshman')); ?></td>
                                                                        <td class="center"><?= $student['Program']['name']; ?></td>
                                                                        <td class="center"><?= $student['ProgramType']['name']; ?></td>
                                                                        <td class="center"><?= $this->Html->link(__('Register'), array('action' => 'maintain_registration', $student['Student']['id'])); ?></td>
                                                                    </tr>
                                                                    <?php 
                                                                } ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <hr>
                                                    <?php
                                                }
                                            }
                                        }
                                    } 
                                } 
                            }
                        } 
                    } ?>
                </div>
                <?= $this->Form->end(); ?>
            </div>
        </div>
    </div>
</div>

<script type='text/javascript'>

    function toggleViewFullId(id) {
        if ($('#' + id).css("display") == 'none') {
            $('#' + id + 'Img').attr("src", '/img/minus2.gif');
            $('#' + id + 'Txt').empty();
            $('#' + id + 'Txt').append(' Hide Filter');
        } else {
            $('#' + id + 'Img').attr("src", '/img/plus2.gif');
            $('#' + id + 'Txt').empty();
            $('#' + id + 'Txt').append(' Display Filter');
        }
        $('#' + id).toggle("slow");
    }
    
    var semester = $("#Semester option:selected").text();
    var semster_text = '';

    if (semester == 'I') {
        semster_text = '1st';
    } else if (semester == 'II') {
        semster_text = '2nd';
    }  if (semester == 'III') {
        semster_text = '3rd';
    }

    <?php
    if (isset($departments)) { ?>
        semster_text + ' semester of ' + $("#AcademicYear option:selected").text() + ' academic year for ' + $('#DeptYrAcySem').html($("#YearLevelId option:selected").text() + ' year ' + $("#ProgramId option:selected").text() + ', ' + $("#ProgramTypeId option:selected").text() + ' students in ' + $("#DepartmentId option:selected").text());
        <?php
    } else { ?>
        semster_text + ' semester of ' + $("#AcademicYear option:selected").text() + ' academic year for ' + $('#DeptYrAcySem').html($("#YearLevelId option:selected").text() + ' year ' + $("#ProgramId option:selected").text() + ', ' + $("#ProgramTypeId option:selected").text() + ' freshman students in ' + $("#CollegeId option:selected").text());
        <?php
    } ?>

    function updateCourseListOnChangeofOtherField() {

        semester = $("#Semester option:selected").text();

        if (semester == 'I') {
            semster_text = '1st';
        } else if (semester == 'II') {
            semster_text = '2nd';
        }  if (semester == 'III') {
            semster_text = '3rd';
        }
        
        <?php 
        if (isset($departments)) { ?>
           semster_text + ' semester of ' + $("#AcademicYear option:selected").text() + ' academic year for ' + $('#DeptYrAcySem').html($("#YearLevelId option:selected").text() + ' year ' + $("#ProgramId option:selected").text() + ', ' + $("#ProgramTypeId option:selected").text() + ' students in ' + $("#DepartmentId option:selected").text());
            <?php
        } else { ?>
            semster_text + ' semester of ' + $("#AcademicYear option:selected").text() + ' academic year for ' + $('#DeptYrAcySem').html($("#YearLevelId option:selected").text() + ' year ' + $("#ProgramId option:selected").text() + ', ' + $("#ProgramTypeId option:selected").text() + ' freshman students in ' + $("#CollegeId option:selected").text());
            <?php
        } ?>

        //serialize form data

        $("#studentNumber").val('');

        var formData = '';
        var department_id = $("#DepartmentId").val();
        var college_id = $("#CollegeId").val();
        var academic_year = $("#AcademicYear").val().replace("/", "-");
        var program_id = $("#ProgramId").val();
        var program_type_id = $("#ProgramTypeId").val();
        var ylName = $("#YearLevelId option:selected").text();

        //alert(ylName);

        if (typeof department_id != "undefined" && typeof academic_year != "undefined" &&  typeof program_id != "undefined" &&  typeof program_type_id != "undefined" &&  typeof ylName != "undefined") {
            formData = department_id + '~' + academic_year + '~' + program_id + '~' + program_type_id + '~' + 'd' + '~' + ylName;
        } else if (typeof college_id !=  "undefined" && typeof academic_year != "undefined" &&  typeof program_id != "undefined" && typeof program_type_id != "undefined" &&  typeof ylName != "undefined") {
            formData = college_id + '~' + academic_year + '~' + program_id + '~' + program_type_id + '~' + 'c' + '~' + ylName;
        } else {
            return false;
        }

        $("#SectionId").attr('disabled', true);
        $("#Search").attr('disabled', true);
        //get form action
        var formUrl = '/courseRegistrations/get_section_combo/' + formData;
        $.ajax({
            type: 'get',
            url: formUrl,
            data: formData,
            success: function(data,textStatus, xhr) {
                $("#AcadamicYear").attr('disabled', false);
                $("#Semester").attr('disabled', false);
                $("#ProgramId").attr('disabled', false);
                $("#ProgramTypeId").attr('disabled', false);
                $("#DepartmentId").attr('disabled', false);
                $("#CollegeId").attr('disabled', false);
                $("#SectionId").attr('disabled', false);
                $("#SectionId").empty();
                $("#SectionId").append(data);
            },
            error: function(xhr, textStatus, error) {
                //alert(textStatus);
            }
        });

        $("#Search").attr('disabled', false);
        return false;
    }

    function updateDepartmentListOnProgramChange() {

        $("#studentNumber").val('');

        var formData = '';
        var programID = $("#ProgramId").val();

        if (typeof programID != "undefined") {
            formData = programID;
        } else {
            return false;
        }

        $("#ProgramId").attr('disabled', true);
        $("#DepartmentId").attr('disabled', true);
        $("#SectionId").attr('disabled', true);
        $("#Search").attr('disabled', true);
        //get form action
        var formUrl = '/programs/get_departments_combo/' + formData;
        $.ajax({
            type: 'get',
            url: formUrl,
            data: formData,
            success: function(data,textStatus, xhr) {
                $("#AcadamicYear").attr('disabled', false);
                $("#Semester").attr('disabled', false);
                $("#ProgramId").attr('disabled', false);
                $("#ProgramTypeId").attr('disabled', false);
                $("#DepartmentId").attr('disabled', false);
                $("#CollegeId").attr('disabled', false);
                $("#SectionId").attr('disabled', false);
                $("#DepartmentId").empty();
                $("#DepartmentId").append(data);
            },
            error: function(xhr, textStatus, error) {
                //alert(textStatus);
            }
        });

        $("#Search").attr('disabled', false);
        return false;
    }

    $('#registerSelected').click(function() {

        var isValid = true;
        var checkboxes = document.querySelectorAll('input[type="checkbox"]');
        var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);

        if (!checkedOne) {
            alert('At least one student must be selected to maintain registration.');
            validationMessageNonSelected.innerHTML = 'At least one student must be selected to maintain registration.';
            isValid = false;
            return false;
        }

        if (form_being_submitted) {
			alert('Course Registration in progress, please wait a moment or refresh your page...');
			$('#registerSelected').attr('disabled', true);
            isValid = false;
			return false;
		}

		if (!form_being_submitted && isValid) {
			$('#registerSelected').val('Registering...');
			form_being_submitted = true;
            isValid = true
			return true;
		} else {
			return false;
		}
    });

    $('#Search').click(function(event) {
        var studentIdNumber = $("#studentNumber").val();
        var selectedSection = $("#SectionId").val();

        if (studentIdNumber !='') {
            // Disable the required property for all required fields
            $('#CourseRegistrationMaintainRegistrationForm [required]').each(function() {
                $(this).removeAttr('required');
            });


            $('input[name*="data[CourseRegistration]"], select[name*="data[CourseRegistration]"]').each(function() {
				$(this).val(''); // Set their values to empty
			});

            // Optionally submit the form after disabling required
            //$('#CourseRegistrationMaintainRegistrationForm').submit();
        } else if (selectedSection != '' && studentIdNumber =='') {

            $('input[name*="data[CourseRegistration]"], select[name*="data[CourseRegistration]"]').each(function() {
				$(this).val(''); // Set their values to empty
                $(this).removeAttr('required');
			});

            // Optionally submit the form after disabling required
            //$('#CourseRegistrationMaintainRegistrationForm').submit();
        }
    });


	/* var form_being_submitted = false;

	var checkForm = function(form) {
	
		if (form_being_submitted) {
			alert("Registing Selected Students, please wait a moment...");
			form.registerIndivdualCourse.disabled = true;
			return false;
		}

        form.registerIndivdualCourse.value = 'Registing Selected Students...';
		form_being_submitted = true;
		return true; 
	}; */

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>