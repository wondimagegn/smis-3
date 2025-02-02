<div class="box">
    <div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-check" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Approve Mass Add for a Section'); ?></span>
		</div>
	</div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">

                <?= $this->Form->create('CourseAdd'); ?>

                <div style="margin-top: -30px;">
                    <hr>
                    <blockquote>
                        <h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
                        <p style="text-align:justify;"><span class="fs16"> This tool will help you to approve add courses which are published as a Mass Add by department. Mass Add should be only used when: </span>
                            <ol class="fs14">
                                <li><b>The course is a block course.</b></li>
                                <li><b>To correct missed course from semester publication</b></li>
                                <li><b>Course must not be a Thesis/Project/Exit exam</b></li>
                            </ol>
                            <span class="fs16">Mass Added courses are not considered as an add course rather <span class="text-red"> they are part of semester courses.</span></span>
                        </p> 
                    </blockquote>
                </div>
                <hr>
                <div onclick="toggleViewFullId('ListPublishedCourse')">
                    <?php
                    if (isset($organized_published_course_by_section) && !empty($organized_published_course_by_section)) {
                        echo $this->Html->image('plus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
                        <span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt"> Display Filter</span>
                        <?php
                    } else {
                        echo $this->Html->image('minus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
                        <span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt"> Hide Filter</span>
                        <?php
                    }  ?>
                </div>

                <div id="ListPublishedCourse" style="display:<?= (isset($organized_published_course_by_section) && !empty($organized_published_course_by_section) ? 'none' : 'display'); ?>">
                    <fieldset style="padding-bottom: 0px; padding-top: 15px;">
                        <!-- <legend>&nbsp;&nbsp; Search / Filter &nbsp;&nbsp;</legend> -->
                        <div class="row">
                            <div class="large-3 columns">
                                <?= $this->Form->input('Student.academic_year', array('label' => 'Academic Year: ', 'type' => 'select', 'options' => $acyear_array_data, 'empty' => "[ Select ACY ]", 'required', 'default' => (isset($defaultacademicyear) ? $defaultacademicyear : ''), 'style' => 'width:90%;')); ?>
                            </div>
                            <div class="large-3 columns">
                                <?= $this->Form->input('Student.semester', array('label' => 'Semester: ', 'options' => Configure::read('semesters'), 'required', 'empty' => '[ Select Semester ]', 'style' => 'width:90%;')); ?>
                            </div>
                            <div class="large-3 columns">
                                <?= $this->Form->input('Student.program_id', array('label' => 'Program: ', 'style' => 'width:90%')); ?>
                            </div>
                            <div class="large-3 columns">
                                <?= $this->Form->input('Student.program_type_id', array('label' => 'Program Type: ', 'required', 'style' => 'width:90%;')); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="large-6 columns">
                                <?= $this->Form->input('Student.department_id', array('label' => 'Department: ', 'empty' => "[ Select Department ]" , 'required', 'style' => 'width:95%;')); ?>
                            </div>
                            <div class="large-3 columns">
                                <?= $this->Form->input('Student.year_level_id', array('label' => 'Year Level: ', 'required','empty' => "[ Select Year Level ]", 'style' => 'width:90%;')); ?>
                            </div>
                            <div class="large-3 columns">
                            </div>
                        </div>
                        <hr>
                        <?= $this->Form->submit('Search', array('name' => 'getsection', 'id' => 'getsection', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
                    </fieldset>
                </div>
                <hr>

                <div id="show_search_results">
                    <?php
                    if (isset($organized_published_course_by_section) && !empty($organized_published_course_by_section)) { ?>
                        <hr>
                        <!-- <span class='fs14 text-gray'>
                        <strong> Department: <?php //echo $department_name; ?></strong><br/>
                        <strong> Program: <?php //echo $program_name; ?></strong><br/>
                        <strong> Program Type: <?php //echo $program_type_name; ?></strong><br/>
                        <strong> Year Level: <?php //echo $year_level_id; ?></strong><br/>
                        <strong> Academic Year: <?php //echo $academic_year; ?></strong><br/>
                        <strong> Semester: <?php //echo $semester; ?></strong><br/>
                        </span>
                        <br> -->
                        
                        <?php
                        $display_button = 0;
                        $section_count = 0;

                        foreach ($organized_published_course_by_section as $section_id => $coursss) {
                            $section_count++;
                            if (!empty($coursss)) { ?>
                                <h6 class="fs13 text-gray">Please select courses to approve as mass add</h6>

                                <h6 id="validation-message_non_selected" class="text-red fs14"></h6>
                                <br>

                                <div style="overflow-x:auto;">
                                    <table id='fieldsForm' cellpadding="0" cellspacing="0" class="table">
                                        <thead>
                                            <td colspan="6" style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85); line-height: 1.5;">
                                                <span style="font-size:16px;font-weight:bold; margin-top: 25px;"> 
                                                <?= $coursss[0]['Section']['name'] . ' (' . (isset($coursss[0]['Section']['YearLevel']['id']) && !empty($coursss[0]['Section']['YearLevel']['name']) ? $coursss[0]['Section']['YearLevel']['name'] : 'Pre/1st') .', '. $coursss[0]['Section']['academicyear']. ')'; ?>
                                                </span>
                                                <br style="line-height: 0.35;">
                                                <span class="text-gray" style="padding-top: 13px; font-size: 13px; font-weight: bold">
                                                    <?= (isset($coursss[0]['Section']['Department']['id']) && !empty($coursss[0]['Section']['Department']['name']) ? $coursss[0]['Section']['Department']['name'] : $coursss[0]['Section']['College']['name'] . ' - Pre/Freshman'); ?> &nbsp; | &nbsp; <?= $coursss[0]['Section']['Program']['name'] ?>  &nbsp; | &nbsp; <?= $coursss[0]['Section']['Program']['name'] ; ?> <br>
                                                </span>
                                                <span class="text-gray" style="padding-top: 14px; font-size: 13px; font-weight: normal">
                                                    <?php
                                                    $curriculum_name = '';
                                                    $credit_type = 'Credit'; 
                                                    if (isset($coursss[0]['Section']['Curriculum']['id']) && !empty($coursss[0]['Section']['Curriculum']['name'])) {
                                                        $credit_type = (count(explode('ECTS', $coursss[0]['Section']['Curriculum']['type_credit'])) >= 2 ? 'ECTS' : 'Credit');
                                                        $curriculum_name = $coursss[0]['Section']['Curriculum']['name'] . ' - ' . $coursss[0]['Section']['Curriculum']['year_introduced'] . ' (' .  $credit_type . ') <br style="line-height: 0.35;">';
                                                    } else if (isset($coursss[0]['Course']['Curriculum']['id']) && !empty($coursss[0]['Course']['Curriculum']['name'])) {
                                                        $credit_type = (count(explode('ECTS', $coursss[0]['Course']['Curriculum']['type_credit'])) >= 2 ? 'ECTS' : 'Credit');
                                                        $curriculum_name = $coursss[0]['Course']['Curriculum']['name'] . ' - ' . $coursss[0]['Course']['Curriculum']['year_introduced'] . ' (' .  $credit_type . ') <br style="line-height: 0.35;">';
                                                    } ?>
                                                    <?= !empty($curriculum_name) ? '<b><i>Curriculum: <i></b> <i class="text-gray fs13">'. $curriculum_name : '</i>'; ?>
                                                </span>
                                            </td>
                                            <tr>
                                                <th class="center">&nbsp;</th>
                                                <th class="center">#</th>
                                                <th class="vcenter">Course Title</th>
                                                <th class="center">Course Code</th>
                                                <th class="center"><?= $credit_type; ?></th>
                                                <th class="center">L T L</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $count = 1;
                                            foreach ($coursss as $kc => $vc) { ?>
                                                <tr>
                                                    <td class="center" style="width: 4%;">
                                                        <div style="margin-left: 45%;"><?= ($vc['Course']['thesis'] == 1 || $vc['Course']['exit_exam'] == 1 ? '**' : $this->Form->checkbox('PublishedCourse.' . $section_id . '.' . $vc['PublishedCourse']['id'], array('class' => 'listOfPublishedCourse', 'id' => $count))); ?></div>
                                                    </td>
                                                    <td class="center" style="width: 3%;"><?= $count; ?></td>
                                                    <td class="vcenter"><?= $vc['Course']['course_title'] . ($vc['Course']['thesis'] == 1 ? ' &nbsp;&nbsp;<span class="on-process">(Thesis/Project Course)</span>' : ($vc['Course']['exit_exam'] == 1 ? ' &nbsp;&nbsp;<span class="on-process">(Exit Exam Course)</span>' : ($vc['Course']['elective'] == 1 ? ' &nbsp;&nbsp;<span class="accepted">(Elective Course)</span>' : ''))); ?></td>
                                                    <td class="center"><?= $vc['Course']['course_code']; ?></td>
                                                    <td class="center"><?= $vc['Course']['credit']; ?></td>
                                                    <td class="center"><?= $vc['Course']['lecture_hours'] . '-' . $vc['Course']['tutorial_hours'] . '-' . $vc['Course']['laboratory_hours']; ?></td>
                                                </tr>
                                                <?php
                                                $count++;
                                            } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php
                            } else {
                                $display_button++;
                            } 
                        } 
                        if ($display_button != $section_count) { ?>
                            <hr>
                            <?= $this->Form->submit('Approve Mass Add for Selected', array('name' => 'massadd', 'id' => 'addMassAdd', 'class' => 'tiny radius button bg-blue', 'div' => 'false')); ?>
                            <?php 
                        } 
                    } ?>
                </div>
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

    $("#show_search_results").show();

    var search_button_clicked = false;

	$('#getsection').click(function(event) {

        //event.preventDefault();

        let formIsValid = true;
        
        // Iterate over required fields
        $(':input[required]').each(function() {
            if ($(this).val() === '') {
                if (formIsValid) {
                    $(this).focus();
                    formIsValid = false;
                    return false;
                }
            }
        });

        $('input[type="checkbox"][name^="data[PublishedCourse]"]').each(function() {
            /* const namePatternSelected = /data\[PublishedCourse\]\[\d+\]\[\d+\]/;
            if (namePatternSelected.test($(this).attr('name')) && $(this).is(':checked')) {
                $(this).prop('checked', false);
            } */
            if ($(this).is(':checked')) {
                // If the checkbox is checked, uncheck it
                $(this).prop('checked', false);
            }
        });

        $('#show_search_results').hide();

        if (!formIsValid) {
            event.preventDefault();
            formIsValid = false;
            return false;
        }

        if (search_button_clicked) {
            alert('Looking for Mass Add requests, please wait a moment...');
            $('#getsection').attr('disabled', true);
            formIsValid = false;
            return false;
        }

        if (!search_button_clicked && formIsValid) {
            $('#getsection').val('Looking for Mass Add requests...');
            $('#addMassAdd').attr('disabled', true);
            search_button_clicked = true;
            formIsValid = true
            return true;
        } else {
            search_button_clicked = false;
            return false;
        }
	});

    var form_being_submitted = false;

    const validationMessageNonSelected = document.getElementById('validation-message_non_selected');

    $('#addMassAdd').click(function(event) {

        //$('form').removeAttr('novalidate');

        var isValid = true;

        let atLeastOneSelected = false;

        /*  $('input[type="checkbox"][name^="data[PublishedCourse]"]').each(function() {
            const namePatternSelected = /data\[PublishedCourse\]\[\d+\]\[\d+\]/;
            if (namePatternSelected.test($(this).attr('name')) && $(this).is(':checked')) {
                atLeastOneSelected = true;
            }
        }); */

        $('input[type="checkbox"][name^="data[PublishedCourse]"]').each(function() {
            if ($(this).is(':checked')) {
                atLeastOneSelected = true;
            }
        });

        //alert(atLeastOneSelected);

        if (!atLeastOneSelected) {
            event.preventDefault(); // Prevent form submission
            isValid = false;
            alert('Please select at least one course before submitting the form.');
            validationMessageNonSelected.innerHTML = 'Please select at least one course before submitting the form.';
            return false;
        }

        // remove the validation cheking of the form after minitue number and atleast on student grade is selected
        //$('form').attr('novalidate', 'novalidate');

        if (form_being_submitted) {
            alert('Approving Mass Add for Selected Courses, please wait a moment...');
            $('#addMassAdd').attr('disabled', true);
            $('#getsection').attr('disabled', true);
            return false;
        }

        if (!form_being_submitted && isValid) {
            $('#addMassAdd').val('Approving Mass Add for Selected Courses...');
            $('#getsection').attr('disabled', true);
            form_being_submitted = true;
            isValid = true;
            return true;
        } else {
            return false;
        }
    });

    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>