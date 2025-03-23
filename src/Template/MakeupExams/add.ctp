<script type="text/javascript">

    function disableEnale(tf) {
        $("#ProgramID").attr('disabled', tf);
        $("#ExamGrade1").attr('disabled',  tf);
        $("#StudentSection").attr('disabled', tf);
        $("#Student").attr('disabled', tf);
        $("#CourseRegistered").attr('disabled', tf);
        $("#ExamPublishedCourse").attr('disabled', tf);
        $("#ExamSection").attr('disabled', tf);
        $("#Department").attr('disabled', tf);
        $("#addMakeUpExam").attr('disabled', tf);
    }
    
    $(document).ready(function() {

        $("#addMakeUpExam").attr('disabled', true);
        $("#showAssesmentArea").hide();
        $("#StudentSection").empty().append('<option value="0">[ Select Program First ]</option>');
        $("#Student").empty().append('<option value="0">[ Select Section First ]</option>');
        $("#CourseRegistered").empty().append('<option value="0">[ Select Course ]</option>');
        $("#ExamSection").empty().append('<option value="0">[ Select Makeup Section ]</option>');
        $("#ExamPublishedCourse").empty().append('<option value="0">[ Select Makeup Course ]</option>');

        $("#ProgramID").change(function() {
            $("#flashMessage").remove();
            $("#ProgramID").attr('disabled', true);
            $("#StudentSection").empty();

            $("#PreviousGarde").empty().append('---');
            $("#MinuteNumber").val('');
            $("#showAssesmentArea").hide();

            var p_id = $("#ProgramID").val();

            if (p_id != '' && p_id !== '0' && p_id !== 0) {

                disableEnale(true);
                var formUrl = '/sections/get_sections_by_program_supp_exam/' + p_id;

                $.ajax({
                    type: 'get',
                    url: formUrl,
                    // data: p_id,
                    success: function(data, textStatus, xhr) {
                        $("#StudentSection").empty().append(data);
                        disableEnale(false);
                    },
                    error: function(xhr,  textStatus, error) {
                        alert(textStatus);
                    }
                });

                return false;

            } else {
                $("#StudentSection").empty().append('<option value="0">[ Select Program First ]</option>');
                $("#Student").empty().append('<option value="0">[ Select Section First ]</option>');
                $("#CourseRegistered").empty().append('<option value="0">[ Select Student First ]</option>');
                $("#PreviousGarde").empty().append('---');
                $("#showAssesmentArea").hide();
                $("#addMakeUpExam").attr('disabled', true);
                $("#MinuteNumber").val('');
                $("#ExamSection").empty().append('<option value="0">[ Select Makeup Department First ]</option>');
                $("#ExamPublishedCourse").empty().append('<option value="0">[ Select Makeup Section First ]</option>');
                $("#ProgramID").attr('disabled', false);
            }
        });

        $("#StudentSection").change(function() {
            $("#flashMessage").remove();
            $("#ProgramID").attr('disabled', true);
            $("#StudentSection").attr('disabled', true);
            $("#Student").empty().append('<option value="0">[ Select Student ]</option>');
            $("#Student").attr('disabled', true);
            $("#CourseRegistered").empty().append('<option value="0">[ Select Course ]</option>');

            $("#PreviousGarde").empty().append('---');
            $("#MinuteNumber").val('');
            $("#showAssesmentArea").hide();
            $("#ExamSection").empty().append('<option value="0">[ Select Makeup Department First ]</option>');
            $("#ExamPublishedCourse").empty().append('<option value="0">[ Select Makeup Section First ]</option>');

            var s_id = $("#StudentSection").val();

            if (s_id != '' && s_id !== '0' && s_id !== 0) {
                //var formUrl = '/sections/get_section_students/' + s_id;
                var formUrl = '/sections/get_sup_students/' + s_id;
                disableEnale(true);
                $.ajax({
                    type: 'get',
                    url: formUrl,
                    //data: s_id,
                    success: function(data, textStatus, xhr) {
                        $("#Student").empty().append(data);
                        disableEnale(false);
                    },
                    error: function(xhr, textStatus, error) {
                        alert(textStatus);
                    }
                });

                return false;
            } else {
                $("#Student").empty().append('<option value="0">[ Select Section First ]</option>');
                $("#CourseRegistered").empty().append('<option value="0">[ Select Student First ]</option>');
                $("#PreviousGarde").empty().append('---');
                $("#MinuteNumber").val('');
                $("#showAssesmentArea").hide();
                $("#addMakeUpExam").attr('disabled', true);
                $("#ExamSection").empty().append('<option value="0">[ Select Makeup Department First ]</option>');
                $("#ExamPublishedCourse").empty().append('<option value="0">[ Select Makeup Section First ]</option>');
            }

        });

        $("#Student").change(function() {
            $("#flashMessage").remove();
            $("#ProgramID").attr('disabled', true);
            $("#StudentSection").attr('disabled', true);
            $("#Student").attr('disabled', true);
            $("#CourseRegistered").empty().append('<option value="0">[ Select Course ]</option>');
            $("#CourseRegistered").attr('disabled', true);

            $("#PreviousGarde").empty().append('---');
            $("#MinuteNumber").val('');
            $("#showAssesmentArea").hide();
            $("#ExamSection").empty().append('<option value="0">[ Select Makeup Department First ]</option>');
            $("#ExamPublishedCourse").empty().append('<option value="0">[ Select Makeup Section First ]</option>');

            var stu_id = $("#Student").val();

            if (stu_id != '' && stu_id !== '0' && stu_id !== 0) {

                var formUrl = '/students/get_possible_sup_registered_and_add/' + stu_id;

                $.ajax({
                    type: 'get',
                    url: formUrl,
                    data: stu_id,
                    success: function(data, textStatus, xhr) {
                        $("#CourseRegistered").empty().append(data);
                        disableEnale(false);
                    },
                    error: function(xhr, textStatus, error) {
                        alert(textStatus);
                    }
                });

                return false;

            } else {
                $("#CourseRegistered").empty().append('<option value="0">[ Select Student First ]</option>');
                $("#PreviousGarde").empty().append('---');
                $("#MinuteNumber").val('');
                $("#showAssesmentArea").hide();
                $("#addMakeUpExam").attr('disabled', true);
                $("#ExamSection").empty().append('<option value="0">[ Select Makeup Department First ]</option>');
                $("#ExamPublishedCourse").empty().append('<option value="0">[ Select Makeup Section First ]</option>');
            }
        });

        $("#CourseRegistered").change(function() {
            $("#flashMessage").remove();
            $("#PreviousGarde").empty().append('Loading ...');
            $("#MinuteNumber").val('');

            $("#showAssesmentArea").hide();
            $("#addMakeUpExam").attr('disabled', true);
            $("#ExamSection").empty().append('<option value="0">[ Select Makeup Department First ]</option>');
            $("#ExamPublishedCourse").empty().append('<option value="0">[ Select Makeup Section First ]</option>');

            var pc_id = $("#CourseRegistered").val();

            if (pc_id != '' && pc_id !== '0' && pc_id !== 0) {

                disableEnale(true);
                $("#showAssesmentArea").show();

                var formUrl ='/course_registrations/get_course_registered_grade_result/' + pc_id;

                $.ajax({
                    type: 'get',
                    url: formUrl,
                    data: pc_id,
                    success: function(data, textStatus, xhr) {
                        $("#PreviousGarde").empty().append(data);
                        disableEnale(false);
                    },
                    error: function(xhr, textStatus, error) {
                        alert(textStatus);
                    }
                });

                return false;
            } else {
                $("#PreviousGarde").empty().append('---');
                $("#MinuteNumber").val('');
                $("#showAssesmentArea").hide();
                $("#addMakeUpExam").attr('disabled', true);
                $("#ExamSection").empty().append('<option value="0">[ Select Makeup Department First ]</option>');
                $("#ExamPublishedCourse").empty().append('<option value="0">[ Select Makeup Section First ]</option>');
            }
        });

        $("#Department").change(function() {
            $("#flashMessage").remove();
            $("#Department").attr('disabled', true);
            $("#ExamSection").attr('disabled', true);
            $("#ExamPublishedCourse").empty().append('<option value="0">[ Select Makeup Course ]</option>');

            var d_id = $("#Department").val();
            var p_id = $("#ProgramID").val();

            if (d_id != '' && d_id !== '0' && d_id !== 0 && p_id != '' && p_id !== '0' && p_id !== 0) {

                var formUrl = '/sections/get_sections_by_program_and_dept/' + d_id + '/' + p_id;

                $.ajax({
                    type: 'get',
                    url: formUrl,
                    data: d_id,
                    success: function(data, textStatus, xhr) {
                        $("#ExamSection").empty().append(data);
                        disableEnale(false);
                    },
                    error: function(xhr, textStatus, error) {
                        alert(textStatus);
                    }
                });

                return false;
            } else {
                $("#addMakeUpExam").attr('disabled', true);
                $("#MinuteNumber").val('');
                $("#ExamSection").empty().append('<option value="0">[ Select Makeup Department First ]</option>');
                $("#ExamPublishedCourse").empty().append('<option value="0">[ Select Makeup Section First ]</option>');
            }
        });

        $("#ExamSection").change(function() {
            $("#flashMessage").remove();
            $("#ExamSection").attr('disabled', true);
            $("#Department").attr('disabled', true);
            $("#ExamPublishedCourse").empty();
            $("#ExamPublishedCourse").attr('disabled', true);

            var sec_id = $("#ExamSection").val();

            if (sec_id != '' && sec_id !== '0' && sec_id !== 0) {

                var formUrl = '/published_courses/get_course_published_for_section/' + sec_id + '/2';

                $.ajax({
                    type: 'get',
                    url: formUrl,
                    data: sec_id,
                    success: function(data, textStatus, xhr) {
                        $("#ExamPublishedCourse").empty().append(data);
                        disableEnale(false);
                    },
                    error: function(xhr, textStatus, error) {
                        alert(textStatus);
                    }
                });

                return false;
            } else {
                $("#ExamPublishedCourse").empty().append('<option value="0">[ Select Makeup Section First ]</option>');
                $("#addMakeUpExam").attr('disabled', true);
            }
        });

    });
</script>

<div class="box">
    <div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Assign Makeup Exam'); ?></span>
		</div>
	</div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <div style="margin-top: -30px;"><hr></div>

                <div class="makeupExams form">
                    <?= $this->Form->create('MakeupExam'); ?>

                    <blockquote>
                        <h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
                        <span class="fs14 text-gray" style="text-align:justify;">After you assign a makeup exam, the student will be available for exam result entry and grade submition to <b style="text-decoration: underline;"><i>the assigned instructor of the course for the selected section</i></b>.</span>
                    </blockquote>
                    <hr>

                    <fieldset style="padding-bottom: 15px; padding-top: 25px;">
                        <!-- <legend>&nbsp;&nbsp; Record Supplementary Exam Result &amp; Grade' &nbsp;&nbsp;</legend> -->
                        <div class="row">
                            <div class="large-4 columns">
                                <?=  $this->Form->input('program_id', array('id' => 'ProgramID', 'style' => 'width: 100%;', 'label' => 'Student Program: ', 'empty' => '[ Select Program ]', 'options' => $programs, 'type' => 'select', 'required', 'class' => 'fs14', 'default' => (isset($program_id) && !empty($program_id) ? $program_id : ''))); ?>
                                <hr>
                            </div>
                            <div class="large-8 columns">
                                <div id="StudentSectionList">
                                    <?= $this->Form->input('student_section_id', array('id' => 'StudentSection', 'label' => 'Current Student Section: ', 'style' => 'width: 100%;', 'type' => 'select', 'class' => 'fs14', 'options' => $student_sections, 'default' => (isset($student_section_id) && !empty($student_section_id) ? $student_section_id : ''))); ?>
                                </div>
                                <hr>
                            </div>
                        </div>
                        <div class="row">
                            <div class="large-5 columns">
                                <div id="StudentList">
                                    <?=  $this->Form->input('student_id', array('id' => 'Student', 'style' => 'width: 100%;', 'label' => 'Student taking the Makeup Exam: ', 'required',  'type' => 'select', 'class' => 'fs14', 'options' => $students, 'default' => (isset($student_id) && !empty($student_id) ? $student_id :  ''))); ?>
                                </div>
                                <hr>
                            </div>
                            <div class="large-7 columns">
                                <div id="CourseRegistered1">
                                    <?=  $this->Form->input('course_registration_id', array('id' => 'CourseRegistered', 'style' => 'width: 100%;', 'class' => 'fs14', 'required', 'label' => 'The Course the student is taking as a Makeup Exam: ', 'type' => 'select', 'options' => $student_registered_courses, 'default' => (isset($registered_course_id) && !empty($registered_course_id) ? $registered_course_id : ''))); ?>
                                </div>
                                <hr>
                            </div>
                        </div>

                        <div id="showAssesmentArea">
                            <div class="row">
                                <div class="large-6 columns">
                                <b>Previous Garde History:</b> 
                                    <div id="PreviousGarde">
                                        <?= $this->element('registered_or_add_course_grade_history'); ?>
                                    </div>
                                    <hr>
                                </div>
                                <div class="large-3 columns">
                                    <div>
                                        
                                    </div>
                                </div>
                                <div class="large-3 columns">

                                </div>
                            </div>
                            <div class="row">
                                <div class="large-6 columns">
                                    <?= $this->Form->input('department_id', array('id' => 'Department', 'label' => 'Department the student is going to take the makeup exam: ', 'required', 'style' => 'width: 100%;', 'type' => 'select', 'class' => 'fs14', 'options' => $departments, 'default' => $department_id)); ?>
                                    <hr>
                                </div>

                                <div class="large-6 columns">
                                    <?= $this->Form->input('exam_section_id', array('id' => 'ExamSection', 'label' => 'Section the student is taking the makeup exam: ', 'required', 'style' => 'width: 100%;', 'type' => 'select', 'class' => 'fs14', 'options' => $exam_sections, 'default' => $exam_section_id)); ?>
                                    <hr>
                                </div>
                            </div>
                            <div class="row">
                                <div class="large-6 columns">
                                    <?= $this->Form->input('exam_published_course_id', array('id' => 'ExamPublishedCourse', 'label' => 'Course the student is taking as makeup exam: ', 'required', 'style' => 'width: 100%;', 'type' => 'select', 'class' => 'fs14', 'options' => $exam_published_courses, 'default' => $exam_published_course_id)); ?>
                                    <br>
                                </div>
                                <div class="large-3 columns">
                                    <?=  $this->Form->input('minute_number', array('label' => 'Minute Number: ', 'id' => 'MinuteNumber', 'style' => 'width: 90%;', 'required', 'class' => 'fs14')); ?>
                                    <br>
                                </div>
                                <div class="large-3 columns">
                                    <br>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <hr>
                    <?= $this->Form->end(array('label' => __('Assign Makeup Exam'), 'id' => 'addMakeUpExam', 'class' => 'tiny radius button bg-blue')); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">

    var form_being_submitted = false;

	$('#addMakeUpExam').click(function(e) {

        var isValid = true;
        var minuteNumber = $('#MinuteNumber').val();

        if (minuteNumber != '') {
           
        } else {
            $('#MinuteNumber').focus();
            $('#MinuteNumber').setAttribute('title', 'Please fill out this field.');
            isValid = false;
            return false;
        }

		if (form_being_submitted) {
			alert('Assigning Makeup Exam, please wait a moment or refresh your page...');
			$('#addMakeUpExam').attr('disabled', true);
			return false;
		}

		if (!form_being_submitted && isValid) {
			$('#addMakeUpExam').val('Assigning Makeup Exam...');
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