<script type="text/javascript">
function disableEnale(tf) {
    $("#ProgramID").attr('disabled',
        tf);
    $("#ExamGrade").attr('disabled',
        tf);
    $("#StudentSection").attr(
        'disabled', tf);
    $("#Student").attr('disabled', tf);
    $("#CourseRegistered").attr(
        'disabled', tf);
}
$(document).ready(function() {
    $("#ProgramID").change(
        function() {
            //serialize form data
            $("#flashMessage")
                .remove();
            $("#StudentSection")
                .empty();
            $("#StudentSection")
                .empty();
            $("#StudentSection")
                .append(
                    '<option value="0">--- Select Section ---</option>'
                );
            $("#Student")
                .empty();
            $("#Student")
                .append(
                    '<option value="0">--- Select Student ---</option>'
                );
            $("#CourseRegistered")
                .empty();
            $("#CourseRegistered")
                .append(
                    '<option value="0">--- Select Course ---</option>'
                );
            $("#ExamGrade")
                .empty();
            $("#ExamGrade")
                .append(
                    '<option value="0">--- Select Grade ---</option>'
                );
            $("#PreviousGarde")
                .empty();
            $("#PreviousGarde")
                .append(
                    '---');
            disableEnale(
                true);
            var p_id = $(
                "#ProgramID"
            ).val();
            var formUrl =
                '/sections/get_sections_by_program_supp_exam/' +
                p_id;
            $.ajax({
                type: 'get',
                url: formUrl,
                //data: p_id,
                success: function(
                    data,
                    textStatus,
                    xhr
                ) {
                    $("#StudentSection")
                        .empty();
                    $("#StudentSection")
                        .append(
                            data
                        );
                    disableEnale
                        (
                            false
                        );
                },
                error: function(
                    xhr,
                    textStatus,
                    error
                ) {
                    alert
                        (
                            textStatus
                        );
                }
            });

            return false;
        });

    $("#StudentSection").change(
        function() {
            //serialize form data
            $("#flashMessage")
                .remove();
            $("#Student")
                .empty();
            $("#Student")
                .append(
                    '<option value="0">--- Select Student ---</option>'
                );
            $("#CourseRegistered")
                .empty();
            $("#CourseRegistered")
                .append(
                    '<option value="0">--- Select Course ---</option>'
                );
            $("#ExamGrade")
                .empty();
            $("#ExamGrade")
                .append(
                    '<option value="0">--- Select Grade ---</option>'
                );
            $("#PreviousGarde")
                .empty();
            $("#PreviousGarde")
                .append(
                    '---');
            var s_id = $(
                "#StudentSection"
            ).val();
            disableEnale(
                true);
            var formUrl =
                '/sections/get_sup_students/' +
                s_id;
            $.ajax({
                type: 'get',
                url: formUrl,
                success: function(
                    data,
                    textStatus,
                    xhr
                ) {
                    $("#Student")
                        .empty();
                    $("#Student")
                        .append(
                            data
                        );
                    disableEnale
                        (
                            false
                        );
                },
                error: function(
                    xhr,
                    textStatus,
                    error
                ) {
                    console
                        .log(
                            textStatus
                        );
                }
            });

            return false;
        });

    $("#Student").change(
        function() {
            //serialize form data
            $("#flashMessage")
                .remove();
            $("#ExamGrade")
                .empty();
            $("#ExamGrade")
                .append(
                    '<option value="0">--- Select Grade ---</option>'
                );
            $("#CourseRegistered")
                .empty();
            $("#CourseRegistered")
                .append(
                    '<option value="0">--- Select Course ---</option>'
                );
            $("#PreviousGarde")
                .empty();
            $("#PreviousGarde")
                .append(
                    '---');
            var stu_id = $(
                "#Student"
            ).val();
            disableEnale(
                true);
            var formUrl =
                '/students/get_possible_sup_registered_and_add/' +
                stu_id;
            $.ajax({
                type: 'get',
                url: formUrl,
                //data: stu_id,
                success: function(
                    data,
                    textStatus,
                    xhr
                ) {
                    $("#CourseRegistered")
                        .attr(
                            'disabled',
                            false
                        );
                    $("#CourseRegistered")
                        .empty();
                    $("#CourseRegistered")
                        .append(
                            data
                        );
                    disableEnale
                        (
                            false
                        );
                },
                error: function(
                    xhr,
                    textStatus,
                    error
                ) {
                    console
                        .log(
                            textStatus
                        );
                }
            });

            return false;
        });

    $("#CourseRegistered")
        .change(function() {
            //serialize form data
            $("#flashMessage")
                .remove();
            $("#ExamGrade")
                .empty();
            $("#ExamGrade")
                .append(
                    '<option value="0">--- Select Grade ---</option>'
                );
            $("#PreviousGarde")
                .empty();
            $("#PreviousGarde")
                .append(
                    'Loading ...'
                );
            var pc_id = $(
                "#CourseRegistered"
            ).val();
            disableEnale(
                true);
            var formUrl =
                '/course_registrations/get_course_registered_grade_list/' +
                pc_id;
            $.ajax({
                type: 'get',
                url: formUrl,
                // data: pc_id,
                success: function(
                    data,
                    textStatus,
                    xhr
                ) {
                    $("#ExamGrade")
                        .empty();
                    $("#ExamGrade")
                        .append(
                            data
                        );
                    disableEnale
                        (
                            false
                        );
                    //Previous Grade
                    var formUrl =
                        '/course_registrations/get_course_registered_grade_result/' +
                        pc_id;
                    $.ajax({
                        type: 'get',
                        url: formUrl,
                        data: pc_id,
                        success: function(
                            data,
                            textStatus,
                            xhr
                        ) {
                            $("#PreviousGarde")
                                .empty();
                            $("#PreviousGarde")
                                .append(
                                    data
                                );
                        },
                        error: function(
                            xhr,
                            textStatus,
                            error
                        ) {
                            alert
                                (
                                    textStatus
                                );
                        }
                    });
                    //Previous Grade Ended
                },
                error: function(
                    xhr,
                    textStatus,
                    error
                ) {
                    console
                        .log(
                            textStatus
                        );
                }
            });

            return false;
        });

});


function updateGrade() {
    //serialize form data

    //get form action
    var formUrl =
        '/gradeScales/get_grade_scale/';

    $.ajax({
        type: 'post',
        url: formUrl,
        data: $('form')
            .serialize(),
        success: function(data,
            textStatus, xhr
        ) {
            $("#ExamGrade1")
                .attr(
                    'disabled',
                    false);
            $("#ExamGrade1")
                .empty();
            $("#ExamGrade1")
                .append(
                    data);
            var inputF =
                document
                .getElementById(
                    "ExamGradeR"
                );
            inputF.value =
                data;



        },
        error: function(xhr,
            textStatus,
            error) {
            alert(
                textStatus
            );
        }
    });
    return false;
}
</script>
<div class="examGradeChanges form">
    <?php echo $this->Form->create('ExamGradeChange'); ?>
    <div class="smallheading">
        <?php echo __('Record Supplementary Exam Result &amp; Grade'); ?>
    </div>
    <div class="info-box info-message">
        <span></span>After you submite
        the supplementary exam grade
        result, it will be on pending
        state till it get approved by
        the registrar. You can also
        enter a remark that will be
        visible to the registrar.
    </div>
    <table class="fs14">
        <tr>
            <td style="width:30%">Minute
                Number</td>
            <td style="width:70%">
                <?php echo $this->Form->input('minute_number', array('label' => false, 'class' => 'fs14')); ?>
            </td>
        </tr>
        <tr>
            <td style="width:25%">
                Student Program</td>
            <td style="width:75%">
                <?php echo $this->Form->input('program_id', array('id' => 'ProgramID', 'label' => false, 'type' => 'select', 'options' => $programs, 'class' => 'fs14', 'default' => $program_id)); ?>
            </td>
        </tr>
        <tr>
            <td>Student Section</td>
            <td id="StudentSectionList">
                <?php echo $this->Form->input('student_section_id', array('id' => 'StudentSection', 'label' => false, 'type' => 'select', 'class' => 'fs14', 'options' => $student_sections, 'default' => $student_section_id)) ?>
            </td>
        </tr>
        <tr>
            <td>Student taking the
                supplementary exam</td>
            <td id="StudentList">
                <?php echo $this->Form->input('student_id', array('id' => 'Student', 'label' => false, 'type' => 'select', 'class' => 'fs14', 'options' => $students, 'default' => $student_id)) ?>
            </td>
        </tr>
        <tr>
            <td>Course the student is
                registered/add and for
                which s/he is taking
                exam</td>
            <td id="CourseRegistered1">
                <?php echo $this->Form->input('course_registration_id', array('id' => 'CourseRegistered', 'class' => 'fs14', 'style' => 'width:600px', 'label' => false, 'type' => 'select', 'options' => $student_registered_courses, 'default' => $registered_course_id)) ?>
            </td>
        </tr>
        <tr>
            <td>Previous Garde</td>
            <td id="PreviousGarde">
                <?php echo $this->element('registered_or_add_course_grade_history'); ?>
            </td>
        </tr>
        <tr>
            <td>Exam Result</td>
            <td><?php echo $this->Form->input('makeup_exam_result', array(
                    'id' => 'MakeupExamResult', 'label' => false, 'style' => 'width:100px',
                    'onBlur' => 'updateGrade();'
                )) ?>
                <?php
                /*

				echo $this->Form->input('ExamGradeChange.'.$st_count.'.exam_grade_id', array('type' => 'hidden', 'value' => (!isset($student['MakeupExam']) ? $student['ExamGrade'][0]['id'] : $student['ExamGradeChange'][0]['ExamGrade']['id'])));
										echo $this->Form->input('ExamGradeChange.'.$st_count.'.result', array('id' => 'GradeChangeResult_result_'.$st_count, 'label' => false, 'maxlength' => 5, 'style' => 'width:100px', 'onBlur' => 'updateExamGradeChange(this, '.$st_count.')'));
										*/
                ?>
            </td>
        </tr>
        <tr>
            <td>Exam Grade</td>
            <td id="ExamGrade1">

            </td>
        </tr>
        <tr>
            <td>Remark</td>
            <td><?php echo $this->Form->input('reason', array('label' => false, 'type' => 'textarea', 'cols' => 40, 'rows' => 5)); ?>

                <?php

                echo $this->Form->hidden('grade', array('id' => 'ExamGradeR', 'label' => false,  'default' => $grade))
                ?>
            </td>
        </tr>
    </table>
    <?php echo $this->Form->Submit('Add Supplementary Exam', array('div' => false, 'class' => 'tiny radius button bg-blue')); ?>
    <?php
    /*
    echo $this->Form->end(
        __('Add Supplementary Exam'),
        array('class' => 'tiny radius button bg-blue')
    );*/

    ?>
</div>