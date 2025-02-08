<div class="box">
    <div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-check" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Cancel Registration of a Student'); ?></span>
		</div>
	</div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">

                <?= $this->Form->create('CourseRegistration'); ?>
                
                <div style="margin-top: -20px;">
                    <blockquote>
                        <h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
                        <p style="text-align:justify;"><span class="fs16">This tool will help you to cancel/delete course registration. It is important when course registration of the student was wrong. <span class="text-red">Note that Registration Cancelation is possible if grade is not submitted for one or more courses of the semester, if grade is submitted, use Course Drop instead.</span><!-- The student will not be visible to the instructor if you cancel the registration --></span></p> 
                    </blockquote>
                </div>

                <hr>

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
                    } ?>
                </div>

                <div id="ListPublishedCourse" style="display:<?= (isset($organized_published_course_by_section) ? 'none' : 'display'); ?>">
                    <fieldset style="padding-bottom: 5px;">
                        <legend>&nbsp;&nbsp; Search &nbsp;&nbsp;</legend>
                        <div class="row">
                            <div class="large-3 columns">
                                <?= $this->Form->input('Student.academic_year', array('label' => 'Academic Year: ', 'type' => 'select', 'style' => 'width: 90%;', 'required', 'options' => $academicYearList, /* 'empty' => "[ Select Academic Year ]", */ 'default' => isset($this->request->data['Student']['academic_year'])  ? $this->request->data['Student']['academic_year'] : $defaultacademicyear)); ?>
                            </div>
                            <div class="large-3 columns">
                                <?= $this->Form->input('Student.semester', array('label' => 'Semester: ', 'options' => Configure::read('semesters'), 'empty' => '[ Select Semester ]', 'required')); ?>
                            </div>
                            <div class="large-3 columns">
                                <?= $this->Form->input('Student.studentnumber', array('label' => 'Student ID', 'placeholder' => 'Student ID here...', 'required', 'maxlength' => 25)); ?>
                            </div>
                            <div class="large-3 columns">
                                &nbsp;
                            </div>
                        </div>
                        <?= $this->Form->submit('Search', array('name' => 'getstudentregistration', 'class' => 'tiny radius button bg-blue', 'div' => 'false')); ?>
                    </fieldset>
                </div>

                <hr>

                <?php
                if (isset($organized_published_course_by_section) && !empty($organized_published_course_by_section)) {
                    echo $this->element('student_basic');
                    foreach ($organized_published_course_by_section as $section_id => $coursss) {
                        if (!empty($coursss)) { ?>
                            <div style="overflow-x:auto;">
                                <table id='fieldsForm' cellspacing="0" cellpadding="0" class="table">
                                    <thead>
                                        <tr>
                                            <td class="center">&nbsp;</td>
                                            <td class="center">#</td>
                                            <td class="vcenter">Course Title</td>
                                            <td class="center">Course Code</td>
                                            <td class="center"><?= (count(explode('ECTS', $coursss[0]['Course']['Curriculum']['type_credit'])) >= 2 ? 'ECTS' : 'Credit'); ?></td>
                                            <td class="center">Lecture</td>
                                            <td class="center">Tutorial</td>
                                            <td class="center">Laboratory</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $count = 1;
                                        foreach ($coursss as $kc => $vc) { ?>
                                            <tr>
                                                <?= ($vc['grade_submitted'] ? '<td class="center">**</td>' : '<td class="center">&nbsp;</td>'); ?>
                                                <td class="center"><?= $count; ?></td>
                                                <td class="vcenter"><?= $vc['Course']['course_title']; ?></td>
                                                <td class="center"><?= $vc['Course']['course_code']; ?></td>
                                                <td class="center"><?= $vc['Course']['credit']; ?></td>
                                                <td class="center"><?= $vc['Course']['lecture_hours']; ?></td>
                                                <td class="center"><?= $vc['Course']['tutorial_hours']; ?></td>
                                                <td class="center"><?= $vc['Course']['laboratory_hours']; ?></td>
                                            </tr>
                                            <!-- <tr>
                                                <td class="center" colspan=8 id="cancel_<?php //echo $count; ?>"></td>
                                            </tr> -->
                                            <?php
                                            $count++;
                                        } ?>
                                    </tbody>
                                    <?php
                                    if ($isGradeSubmittedToAnyCourse) { ?>
                                        <tfoot>
                                            <tr>
                                                <td class="center">**</td>
                                                <td class="vcenter" colspan=7><span class="text-gray">marked courses are not allowed for cancellation since grade has been started to be submitted.</span></td>
                                            </tr>
                                        </tfoot>
                                        <?php
                                    } ?>
                                </table>
                            </div>
                            <?php
                        }
                    }

                    if (!empty($course_registration_id_publish_ids)) {
                        foreach ($course_registration_id_publish_ids as $key => $value) {
                            echo $this->Form->hidden('CourseRegistration.' . $key . '.id',  array('value' => $key));
                        }
                    } 

                    if (!$isGradeSubmittedToAnyCourse) { ?>
                        <hr>
                        <?= $this->Form->submit('Cancel Registration', array('name' => 'canceregistration', 'class' => 'tiny radius button bg-blue', 'div' => 'false')); ?>
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
</script>