<script>
    $(document).ready(function() {
        $("#PublishedCourse").change(function() {
            // hide existing view
            $("#show_search_results").hide();
            
            //serialize form data
            var pc = $("#PublishedCourse").val().split('~', 2);

            if (pc.length > 1) {
                if (Array.isArray(pc) == false) {
                    window.location.replace("/exam_grades/<?= $this->request->action; ?>/" + pc + "/section/" + $("#AcadamicYear").val() + "/" + $("#Semester").val());
                } else {
                    window.location.replace("/exam_grades/<?= $this->request->action; ?>/" + pc[1] + "/section/" + $("#AcadamicYear").val() + "/" + $("#Semester").val());
                }
            } else {
                if (Array.isArray(pc) == false) {
                    window.location.replace("/exam_grades/<?= $this->request->action; ?>/" + pc + "/pc/" + $("#AcadamicYear").val() + "/" + $("#Semester").val());
                } else {
                    window.location.replace("/exam_grades/<?= $this->request->action; ?>/" + pc[0] + "/pc/" + $("#AcadamicYear").val() + "/" + $("#Semester").val());
                }
            }
        });
    });

    function toggleView(obj) {
        if ($('#c' + obj.id).css("display") == 'none') {
            $('#i' + obj.id).attr("src", '/img/minus2.gif');
        } else {
            $('#i' + obj.id).attr("src", '/img/plus2.gif');
        }
        $('#c' + obj.id).toggle("slow");
    }
</script>

<div class="box">
    <div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-info-outline"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Exam Grade View'); ?></span>
		</div>
	</div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">

                <div class="examGrades <?= $this->request->action; ?>">
                    
                    <?= $this->Form->create('ExamGrade'); ?>
                    <?= $this->element('publish_course_filter_by_dept'); ?>
                    <?= $this->Form->end(); ?>

                    <div id="show_search_results">
                        <?php
                        //Displaying list of students with their grade
                        if (isset($students) && count($students) > 0 && !empty($course_detail) && !empty($section_detail)) { ?>
                            <hr>
                            <div class="smallheading fs14 text-gray"><strong class="text-black"><?= $course_detail['course_title'] . ' (' . $course_detail['course_code'] . ')'; ?></strong>  exam grade for <strong class="text-black"><?= $section_detail['name']; ?></strong> section.</div> <br>
                            <?php
                        } 

                        if (!empty($publishedCourses) && !isset($published_course_id)) { ?>
                            <hr>
                            <div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>Please select a course.</div>
                            <hr>
                            <?php
                        } else if (isset($published_course_id) && count($students) <= 0 && count($student_adds) <= 0) { ?>
                            <hr>
                            <div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>Unable to find list of students who are registered for the course you selected. Please contact your department for more information.</div>
                            <hr>
                            <?php
                        } else if (isset($students)) {

                            if (count($students) > 0) {
                            
                                $in_progress = 0;
                                $students_process = $students;
                                $makeup_exam = false;
                                $count = 1;
                                $st_count = 0;
                                
                                $this->set(compact('students_process', 'count', 'st_count', 'in_progress', 'makeup_exam'));
                                echo $this->element('exam_sheet'); 
                            }

                            if (count($student_adds) > 0) { ?>
                                <div class="smallheading fs14 text-gray">Students who added <strong class="text-black"><?= $course_detail['course_title'] . ' (' . $course_detail['course_code'] . ')'; ?></strong> course from other sections.</div> <br>
                                <?php
                                $students_process = $student_adds;
                                $makeup_exam = false;
                                $count = ((count($students)) + 1);
                                $st_count = count($students);
                                $in_progress = count($students);
                                
                                $this->set(compact('students_process', 'count', 'st_count', 'in_progress', 'makeup_exam'));
                                echo $this->element('exam_sheet');
                            }

                            if (count($student_makeup) > 0) { ?>
                                <div class="smallheading fs14 text-gray">Students who are taking makeup exam for <strong class="text-black"><?= $course_detail['course_title'] . ' (' . $course_detail['course_code'] . ')'; ?></strong> course.</div> <br>
                                <?php
                                $students_process = $student_makeup;
                                $makeup_exam = true;
                                $count = (count($students) + count($student_adds) + 1);
                                $st_count = (count($students) + count($student_adds));
                                $in_progress = (count($students) + count($student_adds));
                                
                                $this->set(compact('students_process', 'count', 'st_count', 'in_progress', 'makeup_exam'));
                                echo $this->element('exam_sheet');
                            }
                        }

                        if (!empty($published_course_id) && ($role_id == ROLE_DEPARTMENT || $role_id == ROLE_REGISTRAR)) { ?>

                            <?php
                            /* 
                            $button_options = array('name' => 'exportExamGradePDF', 'div' => false, 'class' => 'tiny radius button bg-blue');
                            $button_options['disabled'] = 'false';
                            echo $this->Form->submit(__('Export PDF', true), $button_options);
                            */ ?>

                            <hr>
                            <!-- <span><?php //echo $this->Html->link('View Pdf', array('controller' => 'examGrades', 'action' => 'view_pdf', $published_course_id), array('class' => 'tiny radius button bg-blue', 'target' => '_blank')); ?></span> -->
                            <span><!-- &nbsp;&nbsp;&nbsp;&nbsp; --> <?= $this->Html->link('Download Xls', array('controller' => 'examGrades', 'action' => 'view_xls', $published_course_id), array('class' => 'tiny radius button bg-blue')); ?></span>

                            <?php
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>