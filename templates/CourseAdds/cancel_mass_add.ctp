<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-cancel-outline" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= 'Cancel Mass Added Course'; ?></span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">

                <?= $this->Form->create('CourseAdd', array('onSubmit' => 'return checkForm(this);')); ?>

                <div style="margin-top: -20px;">
                    <blockquote>
                        <h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
                        <p style="text-align:justify;"><span class="fs16"> This tool will help you to Cancel/Delete Mass Add courses for a section. Mass Add cancellation or deletion is possible if <span class="text-red"> there is no grade submitted for the selected course(s). </span> </span></p> 
                    </blockquote>
                </div>

                <div onclick="toggleViewFullId('ListPublishedCourse')">
                    <?php
                    if (isset($organized_published_course_by_section) && !empty($organized_published_course_by_section)) {
                        echo $this->Html->image('plus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
                        <span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Display Filter</span>
                        <?php
                    } else {
                        echo $this->Html->image('minus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
                        <span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Hide Filter</span>
                        <?php
                    }  ?>
                </div>

                <div id="ListPublishedCourse" style="display:<?= (isset($organized_published_course_by_section) ? 'none' : 'display'); ?>">
                    <div style="margin-top: -10px;">
                        <fieldset style="padding-bottom: 5px;padding-top: 5px;">
                            <legend>&nbsp;&nbsp; Search / Filter &nbsp;&nbsp;</legend>
                            <div class="row">
                                <div class="large-3 columns">
                                    <?= $this->Form->input('Student.academic_year', array('label' => 'Academic Year: ', 'type' => 'select', 'options' => $acyear_array_data, 'empty' => "--Select ACY--", 'required', 'default' => (isset($defaultacademicyear) ? $defaultacademicyear : ''), 'style' => 'width:90%')); ?>
                                </div>
                                <div class="large-3 columns">
                                    <?= $this->Form->input('Student.semester', array('label' => 'Semester: ', 'options' => array('I' => 'I', 'II' => 'II', 'III' => 'III'), 'required', 'empty' => '--Select Semester--', 'style' => 'width:90%')); ?>
                                </div>
                                <div class="large-3 columns">
                                    <?= $this->Form->input('Student.program_id', array('label' => 'Program: ', 'style' => 'width:90%')); ?>
                                </div>
                                <div class="large-3 columns">
                                    <?= $this->Form->input('Student.program_type_id', array('label' => 'Program Type: ', 'required', 'style' => 'width:90%')); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="large-3 columns">
                                    <?= $this->Form->input('Student.year_level_id', array('label' => 'Year Level: ', 'required','empty' => "--Select Year Level--", 'style' => 'width:90%')); ?>
                                </div>
                                <div class="large-6 columns">
                                    <?= $this->Form->input('Student.department_id', array('label' => 'Department: ', 'empty' => "--Select Department--" , 'required', 'style' => 'width:95%')); ?>
                                </div>
                                <div class="large-3 columns">
                                </div>
                            </div>
                            <hr>
                            <?= $this->Form->submit('Continue', array('name' => 'getsection', 'class' => 'tiny radius button bg-blue', 'div' => 'false')); ?>
                        </fieldset>
                    </div>
                </div>
                <?php

                if (isset($organized_published_course_by_section) && !empty($organized_published_course_by_section)) { ?>
                    <hr>
                    <span class='fs14 text-gray'>
                       <strong> Department: <?= $department_name; ?></strong><br/>
                       <strong> Program: <?= $program_name; ?></strong><br/>
                       <strong> Program Type: <?= $program_type_name; ?></strong><br/>
                       <strong> Year Level: <?= $year_level_id; ?></strong><br/>
                       <strong> Academic Year: <?= $academic_year; ?></strong><br/>
                       <strong> Semester: <?= $semester; ?></strong><br/>
                    </span>
                    <br>

                    <?php
                    $display_button = 0;
                    $section_count = 0;

                    foreach ($organized_published_course_by_section as $section_id => $coursss) {
                        $section_count++;
                        //debug($coursss);
                        if (!empty($coursss)) { ?>
                            <div style="overflow-x:auto;">
                                <table id='fieldsForm' cellpadding="0" cellspacing="0" class="table">
                                    <thead>
                                        <tr>
                                            <th colspan=6>Section: <?= $sections[$section_id]; ?></td>
                                        </tr>
                                        <tr>
                                            <th colspan=6>Select Mass Added Course(s) you want to cancel</td>
                                        </tr>
                                        <tr>
                                            <th>&nbsp;</th>
                                            <th class="center"> # </th>
                                            <th class="vcenter">Course Title</th>
                                            <th class="center">Course Code</th>
                                            <th class="center"><?= $coursss[0]['type_credit']; ?></th>
                                            <th class="center"> L T L </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $count = 1;
                                        foreach ($coursss as $kc => $vc) { ?>
                                            <tr>
                                                <?php
                                                if ($vc['grade_submitted']) { ?>
                                                    <td class="center">**</td>
                                                    <?php
                                                } else { ?>
                                                    <td class="center"><?= $this->Form->checkbox('PublishedCourse.' . $section_id . '.' . $vc['PublishedCourse']['id'], array('class' => 'listOfPublishedCourse', 'id' => $count)); ?></td>
                                                    <?php
                                                } ?>
                                                <td class="center"><?= $count; ?></td>
                                                <td class="vcenter"><?= $vc['Course']['course_title']; ?></td>
                                                <td class="center"><?= $vc['Course']['course_code']; ?></td>
                                                <td class="center"><?= $vc['Course']['credit']; ?></td>
                                                <td class="center"><?= $vc['Course']['lecture_hours'] . '-' . $vc['Course']['tutorial_hours'] . '-' . $vc['Course']['laboratory_hours']; ?></td>
                                            </tr>
                                            <?php
                                            $count++;
                                        } ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan=6> ** Those courses are not allowed for cancellation since one or more students has got grade.</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <?php
                        } else {
                            $display_button++;
                        }
                    } 

                    if ($grade_submitted_counter != $publish_counter) { ?>
                        <hr>
                        <?= $this->Form->submit('Cancel Selected Mass Add', array('name' => 'cancelmassadd', 'id' => 'SubmitID', 'div' => 'false', 'class' => 'tiny radius button bg-blue')); ?>
                        <?php
                    } 
                } ?>
            </div>
        </div>
    </div>
</div>

<script type='text/javascript'>
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

    var form_being_submitted = false; /* global variable */

	var checkForm = function(form) {
	
		if (form_being_submitted) {
			alert("Cancelling Selected Mass Adds, please wait a moment...");
			form.SubmitID.disabled = true;
			return false;
		}

		form.SubmitID.value = 'Cancelling Selected Mass Adds...';
		form_being_submitted = true;
		return true; /* submit form */
	};

	// prevent possible form resubmission of a form 
	// and disable default JS form resubmit warning  dialog  caused by pressing browser back button or reload or refresh button

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>