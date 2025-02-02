<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-check" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Student Course Registion'); ?></span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">

                <div style="margin-top: -30px;"><hr></div>
                
                <div class="courseRegistrations form">

                    <?= $this->element('student_basic'); ?>

                    <?= $this->Form->create('CourseRegistration'); ?>
                    
                    <?php
                    if (isset($published_courses) && !empty($published_courses)) {  
                        //debug($published_courses); 
                        $elective_courses_count = 0;
                        $on_hold_registration_count = 0;
                        ?>
                        <h6 id="validation-message_non_selected" class="text-red fs14"></h6>

                        <div style="overflow-x:auto;">
                            <table id='fieldsForm' cellspacing="0" cellpadding="0" class="table">
                                <thead>
                                    <tr>
                                        <th class="center" style="width: 7%;">Elective</th>
                                        <th class="center" style="width: 3%;">#</th>
                                        <th class="vcenter">Course Title</th>
                                        <th class="center" style="width: 13%;">Course Code</th>
                                        <th class="center" style="width: 7%;">Lecture</th>
                                        <th class="center" style="width: 7%;">Tutorial</th>
                                        <th class="center" style="width: 7%;">Lab</th>
                                        <th class="center" style="width: 10%;"><?= (isset($published_courses[0]['Course']['Curriculum']['type_credit']) && count(explode('ECTS', $published_courses[0]['Course']['Curriculum']['type_credit'])) >= 2 ? 'ECTS' : 'Credit'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php

                                    $count = 1;

                                    //debug($student_section['Section'][0]['id']);

                                    foreach ($published_courses as $pk => $pv) {

                                        // allow registration without passing prerequiste but the registration should be cancelled by the registrar in case student grade is not changed.
                                        $style = " class='accepted'";

                                        // normal registration 

                                        if (!isset($pv['prequisite_taken_passsed']) && !isset($pv['exemption']) || (isset($pv['prequisite_taken_passsed']) && $pv['prequisite_taken_passsed'] == 1)) {
                                            echo $this->Form->hidden('CourseRegistration.' . $count . '.published_course_id', array('value' => $pv['PublishedCourse']['id']));
                                            echo $this->Form->hidden('CourseRegistration.' . $count . '.course_id', array('value' => $pv['Course']['id']));
                                            echo $this->Form->hidden('CourseRegistration.' . $count . '.semester', array('value' => $pv['PublishedCourse']['semester']));
                                            echo $this->Form->hidden('CourseRegistration.' . $count . '.academic_year', array('value' => $pv['PublishedCourse']['academic_year']));
                                            echo $this->Form->hidden('CourseRegistration.' . $count . '.student_id', array('value' => $student_section['Student']['id']));
                                            echo $this->Form->hidden('CourseRegistration.' . $count . '.section_id', array('value' => $student_section['Section'][0]['id']));
                                            echo $this->Form->hidden('CourseRegistration.' . $count . '.year_level_id', array('value' => $student_section['Section'][0]['year_level_id']));
                                            
                                            if (isset($pv['PublishedCourse']['elective']) && !empty($pv['PublishedCourse']['elective']) && $pv['PublishedCourse']['elective'] == 1) {
                                                echo $this->Form->hidden('CourseRegistration.' . $count . '.elective_course', array('value' => 1));
                                                $elective_courses_count++;
                                            }
                                        }

                                        if (isset($pv['prequisite_taken_passsed']) && $pv['prequisite_taken_passsed'] == 0) {
                                            $style = ' class="rejected"';
                                        }

                                        if (isset($pv['exemption']) && $pv['exemption'] == 1) {
                                            $style = ' class="exempted"';
                                        }

                                        // type of registration 

                                        if ((isset($pv['registration_type']) && $pv['registration_type'] == 2 && !isset($pv['exemption']))) {
                                            $style = ' class="on-process"';
                                            $on_hold_registration_count++;
                                            echo $this->Form->hidden('CourseRegistration.' . $count . '.type', array('value' => 11)); ?>
                                            <tr <?= $style; ?> >
                                                <td class="center">--</td>
                                                <td class="center"><?= $count++; ?></td>
                                                <td class="vcenter"><?= $pv['Course']['course_title'] . "**"; ?></td>
                                                <?php
                                        } else if (isset($pv['prequisite_taken_passsed']) && $pv['prequisite_taken_passsed'] == 2 && !isset($pv['exemption'])) {
                                            $style = ' class="on-process"';
                                            $on_hold_registration_count++;
                                            echo $this->Form->hidden('CourseRegistration.' . $count . '.type', array('value' => 11)); ?>
                                            <tr <?= $style; ?> >
                                                <td class="center">--</td>
                                                <td class="center"><?= $count++; ?></td>
                                                <td class="vcenter"><?= $pv['Course']['course_title'] . "**"; ?></td>
                                                <?php
                                        } else if ((isset($pv['registration_type']) && $pv['registration_type'] == 2) && (isset($pv['prequisite_taken_passsed']) && $pv['prequisite_taken_passsed'] == 2) && !isset($pv['exemption'])) {
                                            $style = ' class="on-process"';
                                            $on_hold_registration_count++;
                                            echo $this->Form->hidden('CourseRegistration.' . $count . '.type', array('value' => 13)); ?>
                                            <tr <?= $style; ?> >
                                                <td class="center">--</td>
                                                <td class="center"><?= $count++; ?></td>
                                                <td class="vcenter"><?= $pv['Course']['course_title'] . "**"; ?></td>
                                                <?php
                                        } else {
                                            if (($pv['PublishedCourse']['elective'] == 1)) { ?>
                                                <tr <?= $style; ?> >
                                                    <?php
                                                    if (isset($pv['prequisite_taken_passsed']) && $pv['prequisite_taken_passsed'] == 0) { ?>
                                                        <td class="center">--</td>
                                                        <td class="center"><?= $count++; ?></td>
                                                        <td class="vcenter"><?= $pv['Course']['course_title']; ?></td>
                                                        <?php
                                                    } else { ?>
                                                        <td class="center"><div style="margin-left: 40%;"><?= $this->Form->checkbox('CourseRegistration.' . $count . '.gp'); ?></div></td>
                                                        <td class="center"><?= $count++; ?></td>
                                                        <td class="vcenter"><?= $pv['Course']['course_title']; ?></td>
                                                        <?php
                                                    }
                                            } else { ?>
                                                <tr <?= $style; ?> >
                                                    <td class="center">--</td>
                                                    <td class="center"><?= $count++; ?></td>
                                                    <td class="vcenter"><?= $pv['Course']['course_title']; ?></td>
                                                    <?php
                                            }
                                        } ?>
                                                <td class="center"><?= $pv['Course']['course_code']; ?></td>
                                                <td class="center"><?= $pv['Course']['lecture_hours']; ?></td>
                                                <td class="center"><?= $pv['Course']['tutorial_hours']; ?></td>
                                                <td class="center"><?= $pv['Course']['laboratory_hours']; ?></td>
                                                <td class="center"><?= $pv['Course']['credit']; ?></td>
                                            </tr>
                                        <?php
                                    }
                                
                                    $options = array('1' => ' Cafe Consumer', '0' => ' Non Cafe');
                                    $attributes = array('id' => 'cafeteriaConsumer', 'legend' => false, /* 'label' => false, */ 'separator' => ' &nbsp; &nbsp; ', 'required' => 'true'); ?>

                                    <tr>
                                        <td colspan="2" class="center">Are you ? </td>
                                        <td colspan="6" class="vcenter"><br><?= $this->Form->radio('CourseRegistration.0.cafeteria_consumer', $options, $attributes); ?></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan=8 class="vcenter" style="font-weight: normal;">
                                            Important Note: 
                                            <ol>
                                                <li>Green marked courses are courses you are elegible for registration.</li>
                                                <li>Red marked courses are courses you are not elegible for registration since you didn't fullfilled prerequisite requirements.</li>
                                                <li>Blue marked courses are courses that are exempted.</li>
                                                <?php
                                                if ($on_hold_registration_count) { ?>
                                                    <li>Orange marked courses ending with ** are registration allowed on hold biases, either grade for the prerequsite course is not submitted or student academic status is not generated for the previous semester. These registrations will be dropped if the student fails to achieve a pass mark for the prequisite course or fails to achive minimum CGPA set for the semester.</li>
                                                    <?php
                                                }

                                                if ($elective_courses_count) { ?>
                                                    <li>Courses with checkbox in Elective column are published as elective by the department, <i class="rejected" style="font-weight: bold;">only select the courses you want to take.</i></li>
                                                    <?php
                                                } ?>
                                            </ol>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <hr>

                        <?php
                        if (!isset($deadlinepassed)) {
                            /* echo "<tr><td colspan=7>".$this->Form->end(array('onclick'=>"this.disable=true;this.value='Submitting...';this.form.submit();", 'label'=>__('Register'),'class'=>'tiny radius button bg-blue'))."</td></tr>";  */ ?>
                            <?= $this->Form->Submit('Register', array('id' => 'SubmitButton', 'name' => 'registerCourses', 'div' => false, 'class' => 'tiny radius button bg-blue')); ?>
                            <?php
                        }
                    }  ?>
                </div>
                <?= $this->Form->end(); ?>
            </div>
        </div>
    </div>
</div>

<style type="text/css">
    .ajax_status {
        display: none;
    }

    .ajax_success {
        color: #036A0D;
    }

    .ajax_error {
        color: #FF0000;
    }
</style>

<script type="text/javascript">
    $(document).ready(function () {
        $("#Ecardnumber").focusout(function () {
            var formUrl = '/students/ajax_check_ecardnumber';
            $.ajax({
                type: 'post',
                url: formUrl,
                data: { 'Student': { 'ecardnumber': $("#Ecardnumber").val() } },
                success: function (data, textStatus, xhr) {
                    if ($("#Ecardnumber").val().length < 8) {
                        $("#ecard_ajax_result").empty();
                        $("#ecard_ajax_result").append('<span style="color:red">Invalid</span>');
                    } else {
                        $("#ecard_ajax_result").empty();
                        $("#ecard_ajax_result").append(data);
                    }
                },
                error: function (xhr, textStatus, error) {
                    alert(textStatus);
                }
            });
        });
    });


    var form_being_submitted = false;

    var have_elective_courses = <?= $elective_courses_count; ?>;

    const validationMessageNonSelected = document.getElementById('validation-message_non_selected');

	$('#SubmitButton').click(function() {

        var isValid = true;

        // Check all required input, select, and textarea elements
        $('#CourseRegistrationRegisterForm [required]').each(function() {
            if ($(this).val() === '') {
                $(this).focus();
                //alert('Please fill out this field.');
                $(this).setAttribute('title', 'Please fill out this field.');
                //$(this).attr('title', 'Please fill out this field.');
                isValid = false;
                return false; // Break the loop
            }
        });

        // Check the required radio button
        if (isValid && !$('input[name="data[CourseRegistration][0][cafeteria_consumer]"]').is(':checked')) {
            $('input[name="data[CourseRegistration][0][cafeteria_consumer]"]').first().focus();
            $('#cafeteriaConsumer').setAttribute('title', 'Please select the cafeteria consumer option.');
            //$('#cafeteriaConsumer').attr('title', 'Please select the cafeteria consumer option.');
            isValid = false;
            return false; 
        }

        if (isValid) {
            //alert('All required fields are filled.');
            // Optionally, you can submit the form here
            //$('#CourseRegistrationRegisterForm').submit();
        }

        //alert(have_elective_courses);

        if (have_elective_courses) {
            var checkboxes = document.querySelectorAll('input[type="checkbox"]');
            var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);

            if (!checkedOne) {
                alert('At least one elective course must be selected to register.');
                validationMessageNonSelected.innerHTML = 'At least one elective course must be selected to register.';
                return false;
            }
        }

		if (form_being_submitted) {
			alert('Course Registration in progress, please wait a moment or refresh your page...');
			$('#SubmitButton').attr('disabled', true);
			return false;
		}

		if (!form_being_submitted && isValid) {
			$('#SubmitButton').val('Registering...');
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