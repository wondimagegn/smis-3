<?php ?>
<script type="text/javascript">
$(document).ready(function() {
    $("#ProgramID").change(
        function() {
            //serialize form data
            $("#flashMessage")
                .remove();
            $("#ProgramID")
                .attr(
                    'disabled',
                    true);
            $("#StudentSection")
                .empty();

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
            $("#ExamSection")
                .empty();
            $("#ExamSection")
                .append(
                    '<option value="0">--- Select Sction ---</option>'
                );
            $("#Department")
                .val(0);
            $("#ExamPublishedCourse")
                .empty();
            $("#ExamPublishedCourse")
                .append(
                    '<option value="0">--- Select Course ---</option>'
                );
            //$("#ExamGrade").empty();
            //$("#ExamGrade").append('<option value="0">--- Select Course ---</option>');

            var p_id = $(
                "#ProgramID"
            ).val();
            var formUrl =
                '/sections/get_sections_by_program_supp_exam/' +
                p_id;
            $.ajax({
                type: 'get',
                url: formUrl,
                // data: p_id,
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
                    $("#ProgramID")
                        .attr(
                            'disabled',
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
            $("#ProgramID")
                .attr(
                    'disabled',
                    true);
            $("#StudentSection")
                .attr(
                    'disabled',
                    true);
            $("#Student")
                .empty();
            $("#Student")
                .attr(
                    'disabled',
                    true);
            $("#CourseRegistered")
                .empty();
            $("#CourseRegistered")
                .append(
                    '<option value="0">--- Select Course ---</option>'
                );
            var s_id = $(
                "#StudentSection"
            ).val();
            var formUrl =
                '/sections/get_section_students/' +
                s_id;
            $.ajax({
                type: 'get',
                url: formUrl,
                //data: s_id,
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
                    $("#StudentSection")
                        .attr(
                            'disabled',
                            false
                        );
                    $("#ProgramID")
                        .attr(
                            'disabled',
                            false
                        );
                    $("#Student")
                        .attr(
                            'disabled',
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

    $("#Student").change(
        function() {
            //serialize form data
            $("#flashMessage")
                .remove();
            $("#ProgramID")
                .attr(
                    'disabled',
                    true);
            $("#StudentSection")
                .attr(
                    'disabled',
                    true);
            $("#Student")
                .attr(
                    'disabled',
                    true);
            $("#CourseRegistered")
                .empty();
            $("#CourseRegistered")
                .attr(
                    'disabled',
                    true);
            var stu_id = $(
                "#Student"
            ).val();
            var formUrl =
                '/students/get_possible_sup_registered_and_add/' +
                stu_id;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: stu_id,
                success: function(
                    data,
                    textStatus,
                    xhr
                ) {
                    $("#StudentSection")
                        .attr(
                            'disabled',
                            false
                        );
                    $("#ProgramID")
                        .attr(
                            'disabled',
                            false
                        );
                    $("#Student")
                        .attr(
                            'disabled',
                            false
                        );
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

    $("#CourseRegistered")
        .change(function() {
            //serialize form data
            $("#flashMessage")
                .remove();
            $("#PreviousGarde")
                .empty();
            $("#PreviousGarde")
                .append(
                    'Loading ...'
                );
            var pc_id = $(
                "#CourseRegistered"
            ).val();
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

            return false;
        });

    $("#Department").change(
        function() {
            //serialize form data
            $("#flashMessage")
                .remove();
            $("#Department")
                .attr(
                    'disabled',
                    true);
            $("#ExamSection")
                .attr(
                    'disabled',
                    true);
            $("#CoursePublishID")
                .empty();
            $("#ExamPublishedCourse")
                .empty();
            $("#ExamPublishedCourse")
                .append(
                    '<option value="0">--- Select Course ---</option>'
                );
            var d_id = $(
                "#Department"
            ).val();
            var p_id = $(
                "#ProgramID"
            ).val();
            var formUrl =
                '/sections/get_sections_by_program_and_dept/' +
                d_id + '/' +
                p_id;
            $.ajax({
                type: 'get',
                url: formUrl,
                data: d_id,
                success: function(
                    data,
                    textStatus,
                    xhr
                ) {
                    $("#ExamSection")
                        .empty();
                    $("#ExamSection")
                        .append(
                            data
                        );
                    $("#Department")
                        .attr(
                            'disabled',
                            false
                        );
                    $("#ExamSection")
                        .attr(
                            'disabled',
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

    $("#ExamSection").change(
        function() {
            //serialize form data
            $("#flashMessage")
                .remove();
            $("#ExamSection")
                .attr(
                    'disabled',
                    true);
            $("#Department")
                .attr(
                    'disabled',
                    true);
            $("#ExamPublishedCourse")
                .empty();
            $("#ExamPublishedCourse")
                .attr(
                    'disabled',
                    true);
            var sec_id = $(
                "#ExamSection"
            ).val();
            var formUrl =
                '/published_courses/get_course_published_for_section/' +
                sec_id +
                '/2';
            $.ajax({
                type: 'get',
                url: formUrl,
                data: sec_id,
                success: function(
                    data,
                    textStatus,
                    xhr
                ) {

                    $("#ExamPublishedCourse")
                        .empty();
                    $("#ExamPublishedCourse")
                        .append(
                            data
                        );
                    $("#Department")
                        .attr(
                            'disabled',
                            false
                        );
                    $("#ExamPublishedCourse")
                        .attr(
                            'disabled',
                            false
                        );
                    $("#ExamSection")
                        .attr(
                            'disabled',
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

});
</script>

<div class="box">
    <div class="box-body">
        <div class="row">
            <div
                class="large-12 columns">

                <div
                    class="makeupExams form">
                    <?php echo $this->Form->create('MakeupExam');?>
                    <div
                        class="smallheading">
                        <?php echo __('Add Makeup Exam'); ?>
                    </div>
                    <div
                        class="info-box info-message">
                        <span></span>After
                        you create a
                        makeup exam, the
                        system will make
                        available the
                        student for exam
                        result entry and
                        grade submition
                        to the
                        instructor
                        curentlly
                        assigned to the
                        selected course
                        the student is
                        taking.
                    </div>
                    <table class="fs14">
                        <tr>
                            <td
                                style="width:30%">
                                Minute
                                Number
                            </td>
                            <td
                                style="width:70%">
                                <?php echo $this->Form->input('minute_number', array('label' => false, 'class' => 'fs14')); ?>
                            </td>
                        </tr>
                        <tr>
                            <td
                                style="width:25%">
                                Student
                                Program
                            </td>
                            <td
                                style="width:75%">
                                <?php echo $this->Form->input('program_id', array('id' => 'ProgramID', 'label' => false, 'type' => 'select', 'options' => $programs, 'class' => 'fs14', 'default' => $program_id)); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Student
                                Section
                            </td>
                            <td
                                id="StudentSectionList">
                                <?php echo $this->Form->input('student_section_id', array('id' => 'StudentSection', 'label' => false, 'type' => 'select', 'class' => 'fs14', 'options' => $student_sections, 'default' => $student_section_id)) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Student
                                taking
                                the
                                makeup
                                exam
                            </td>
                            <td
                                id="StudentList">
                                <?php echo $this->Form->input('student_id', array('id' => 'Student', 'label' => false, 'type' => 'select', 'class' => 'fs14', 'options' => $students, 'default' => $student_id)) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Course
                                the
                                student
                                is
                                registered/add
                                and for
                                which
                                s/he is
                                taking
                                exam
                            </td>
                            <td
                                id="CourseRegistered1">
                                <?php echo $this->Form->input('course_registration_id', array('id' => 'CourseRegistered', 'class' => 'fs14', 'style' => 'width:600px', 'label' => false, 'type' => 'select', 'options' => $student_registered_courses, 'default' => $registered_course_id)) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Garde
                                History
                            </td>
                            <td
                                id="PreviousGarde">
                                <?php echo $this->element('registered_or_add_course_grade_history'); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Department
                                in whcih
                                the
                                student
                                is
                                taking
                                the exam
                            </td>
                            <td><?php echo $this->Form->input('department_id', array('id' => 'Department', 'label' => false, 'type' => 'select', 'class' => 'fs14', 'options' => $departments, 'default' => $department_id)) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>In which
                                section
                                the
                                student
                                is
                                taking
                                the exam
                            </td>
                            <td><?php echo $this->Form->input('exam_section_id', array('id' => 'ExamSection', 'label' => false, 'type' => 'select', 'class' => 'fs14', 'options' => $exam_sections, 'default' => $exam_section_id)) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>From
                                which
                                course
                                the
                                student
                                is
                                taking
                                exam?
                            </td>
                            <td><?php echo $this->Form->input('exam_published_course_id', array('id' => 'ExamPublishedCourse', 'label' => false, 'type' => 'select', 'class' => 'fs14', 'options' => $exam_published_courses, 'default' => $exam_published_course_id)) ?>
                            </td>
                        </tr>

                    </table>
                    <?php echo $this->Form->end(array('label'=>__('Add Makeup Exam'),'class'=>'tiny radius button bg-blue'));?>
                </div>
            </div>
            <!-- end of columns 12 -->
        </div> <!-- end of row --->
    </div> <!-- end of box-body -->
</div><!-- end of box -->