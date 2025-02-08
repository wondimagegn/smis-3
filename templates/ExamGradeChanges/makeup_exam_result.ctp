<script type="text/javascript">

    function disableEnale(tf) {
        $("#ProgramID").attr('disabled', tf);
        $("#ExamGrade1").attr('disabled',  tf);
        $("#StudentSection").attr('disabled', tf);
        $("#Student").attr('disabled', tf);
        $("#CourseRegistered").attr('disabled', tf);
        $("#MakeupExamResult").attr('disabled', tf);
        $("#addSupplementaryExam").attr('disabled', tf);
    }

    $(document).ready(function () {

        $("#addSupplementaryExam").attr('disabled', true);
        $("#showAssesmentArea").hide();
        
        $("#ProgramID").change( function () {
            //serialize form data
            $("#flashMessage").remove();
            $("#StudentSection").empty().append('<option value="0">[ Select Section ]</option>');
            $("#Student").empty().append('<option value="0">[ Select Student ]</option>');
            $("#CourseRegistered").empty().append('<option value="0">[ Select Course ]</option>');
            $("#ExamGrade1").empty();
            $("#PreviousGarde").empty().append('---');
            $("#MakeupExamResult").val('');
            $("#MinuteNumber").val('');
            $("#ReasonRemark").val('');
            $("#showAssesmentArea").hide();

            //disableEnale(true);

            var p_id = $("#ProgramID").val();

            if (p_id != '' && p_id !== '0' && p_id !== 0) {

                disableEnale(true);
                //var formUrl = '/sections/get_sections_by_program/' + p_id;
                var formUrl = '/sections/get_sections_by_program_supp_exam/' + p_id;

                $.ajax({
                    type: 'get',
                    url: formUrl,
                    //data: p_id,
                    success: function (data, textStatus, xhr) {
                        $("#StudentSection").empty();
                        $("#StudentSection").append(data);
                        disableEnale(false);
                    },
                    error: function (xhr, textStatus, error) {
                        alert(textStatus);
                    }
                });

                return false;

            } else {
                $("#StudentSection").empty().append('<option value="0">[ Select Program First ]</option>');
                $("#Student").empty().append('<option value="0">[ Select Section First ]</option>');
                $("#CourseRegistered").empty().append('<option value="0">[ Select Student First ]</option>');
                $("#ExamGrade1").empty();
                $("#PreviousGarde").empty().append('---');
                $("#MakeupExamResult").val('');
                $("#showAssesmentArea").hide();
                $("#addSupplementaryExam").attr('disabled', true);
                $("#MinuteNumber").val('');
                $("#ReasonRemark").val('');
            }
        });

        $("#StudentSection").change(function () {
            
            $("#flashMessage").remove();
            $("#Student").empty().append('<option value="0">[ Select Student ]</option>');
            $("#CourseRegistered").empty().append('<option value="0">[ Select Course ]</option>');
            $("#ExamGrade1").empty();
            $("#PreviousGarde").empty().append('---');
            $("#MakeupExamResult").val('');
            $("#showAssesmentArea").hide();

            var s_id = $("#StudentSection").val();
            //disableEnale(true);

            if (s_id != '' && s_id !== '0' && s_id !== 0) {

                disableEnale(true);
                var formUrl = '/sections/get_sup_students/' + s_id;

                $.ajax({
                    type: 'get',
                    url: formUrl,
                    success: function (data, textStatus, xhr) {
                        $("#Student").empty();
                        $("#Student").append(data);
                        disableEnale(false);
                    },
                    error: function ( xhr, textStatus, error) {
                        console.log(textStatus);
                    }
                });

                return false;

            } else {
                $("#Student").empty().append('<option value="0">[ Select Section First ]</option>');
                $("#CourseRegistered").empty().append('<option value="0">[ Select Student First ]</option>');
                $("#ExamGrade1").empty();
                $("#PreviousGarde").empty().append('---');
                $("#MakeupExamResult").val('');
                $("#addSupplementaryExam").attr('disabled', true);
                $("#MinuteNumber").val('');
                $("#ReasonRemark").val('');
                $("#showAssesmentArea").hide();
            }
        });

        $("#Student").change(function () {

            $("#flashMessage").remove();
            $("#ExamGrade1").empty();
            $("#CourseRegistered").empty().append('<option value="0">[ Select Course ]</option>');
            $("#PreviousGarde").empty().append('---');
            $("#MakeupExamResult").val('');
            $("#MinuteNumber").val('');
            $("#ReasonRemark").val('');
            $("#showAssesmentArea").hide();

            var stu_id = $("#Student").val();
            //disableEnale(true);

            if (stu_id != '' && stu_id !== '0' && stu_id !== 0) {

                var formUrl = '/students/get_possible_sup_registered_and_add/' + stu_id;
                disableEnale(true);

                $.ajax({
                    type: 'get',
                    url: formUrl,
                    //data: stu_id,
                    success: function (data, textStatus, xhr) {
                        $("#CourseRegistered").attr('disabled', false);
                        $("#CourseRegistered").empty();
                        $("#CourseRegistered").append(data);
                        disableEnale(false);
                    },
                    error: function (xhr, textStatus, error) {
                        console.log(textStatus);
                    }
                });

                return false;
            } else {
                $("#CourseRegistered").empty().append('<option value="0">[ Select Student First ]</option>');
                $("#ExamGrade1").empty();
                $("#PreviousGarde").empty().append('---');
                $("#MakeupExamResult").val('');
                $("#MinuteNumber").val('');
                $("#ReasonRemark").val('');
                $("#showAssesmentArea").hide();
                $("#addSupplementaryExam").attr('disabled', true);
            }
        });

        $("#CourseRegistered").change(function () {
            //serialize form data
            $("#flashMessage").remove();
            $("#ExamGrade1").empty();
            $("#PreviousGarde").empty().append('Loading ...');
            $("#MakeupExamResult").val('');
            $("#MinuteNumber").val('');
            $("#ReasonRemark").val('');

            var pc_id = $("#CourseRegistered").val();
            //disableEnale(true);

            if (pc_id != '' && pc_id !== '0' && pc_id !== 0) {

                disableEnale(true);
                $("#showAssesmentArea").show();

                var formUrl = '/course_registrations/get_course_registered_grade_list/' + pc_id;
                $.ajax({
                    type: 'get',
                    url: formUrl,
                    // data: pc_id,
                    success: function (data, textStatus, xhr) {
                        $("#ExamGrade").empty();
                        $("#ExamGrade").append(data);
                        disableEnale(false);
                        //Previous Grade
                        var formUrl = '/course_registrations/get_course_registered_grade_result/' + pc_id;
                        $.ajax({
                            type: 'get',
                            url: formUrl,
                            data: pc_id,
                            success: function (data, textStatus, xhr) {
                                $("#PreviousGarde").empty();
                                $("#PreviousGarde").append(data);
                            },
                            error: function (xhr, textStatus, error) {
                                alert(textStatus);
                            }
                        });

                        return false;
                        //Previous Grade Ended
                    },
                    error: function (xhr, textStatus, error) {
                        console.log(textStatus);
                    }
                });

                return false;
            } else {
                $("#ExamGrade1").empty();
                $("#PreviousGarde").empty().append('---');
                $("#MakeupExamResult").val('');
                $("#MinuteNumber").val('');
                $("#ReasonRemark").val('');
                $("#showAssesmentArea").hide();
                $("#addSupplementaryExam").attr('disabled', true);
            }
        });
    });

    function updateGrade() {
        //serialize form data
        //get form action
        var formUrl = '/gradeScales/get_grade_scale/';
        //alert($('form').serialize());

        $("#ExamGrade1").empty();

        var pc_id = $("#CourseRegistered").val();
        var makeup_result = $("#MakeupExamResult").val();

        if (makeup_result != '' && pc_id != 0 && pc_id != '0') {

            $("#addSupplementaryExam").attr('disabled', false);

            $.ajax({
                type: 'post',
                url: formUrl,
                data: $('form').serialize(),
                success: function (data, textStatus, xhr) {
                    $("#ExamGrade1").attr('disabled', false);
                    $("#ExamGrade1").empty();
                    $("#ExamGrade1").append(data);
                    var inputF = document.getElementById("ExamGradeR");
                    inputF.value = data;
                },
                error: function (xhr, textStatus, error) {
                    alert(textStatus);
                }
            });

            return false;

        } else {
            $("#ExamGrade1").empty();
            $("#MakeupExamResult").empty();
            $("#addSupplementaryExam").attr('disabled', true);
        }
    }
</script>

<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-check" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?=  __('Record Supplementary Exam Result &amp; Grade') . (isset($years_to_look_list_for_display) ? ' (Allowed ' . $years_to_look_list_for_display . ')' : ''); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">

                <div style="margin-top: -30px;"><hr></div>

                <?=  $this->Form->create('ExamGradeChange'); ?>

                <div class="examGradeChanges form">

                    <blockquote>
                        <h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
                        <span class="fs14 text-gray" style="text-align:justify;">This tool will help you to submit Makeup/Supplementary Exam Result entry for students <b style="text-decoration: underline;"><i>which are approved by your college academic commission(AC)</i></b>. <br>In addition to NG and I grades, only students who are assigned in sections <?= isset($years_to_look_list_for_display) ? $years_to_look_list_for_display : ''; ?> and scored grades which are defined as repeatable in the course grade scale definition(mosty, grades below C+ for post graduate studies and below C- for undergraduate programs) are available for result entry. <br><span class="rejected">You are NOT ADVISED to request result entry for NG grades without any previous assesment records, thesis, project or any other courses that can be used as graduation requirement.</span></span>
                    </blockquote>

                    <!-- <div class="info-box info-message" style="font-family: 'Times New Roman', Times, serif;"><span style="margin-right: 15px;"></span>After you submit the supplementary exam grade result, it will be on pending state until the grade is approved by the registrar.</div> -->
                    <hr>

                    <fieldset style="padding-bottom: 15px; padding-top: 25px;">
                        <!-- <legend>&nbsp;&nbsp; Record Supplementary Exam Result &amp; Grade' &nbsp;&nbsp;</legend> -->
                        <div class="row">
                            <div class="large-4 columns">
                                <?=  $this->Form->input('program_id', array('id' => 'ProgramID', 'style' => 'width: 100%;', 'label' => 'Student Program: ', 'type' => 'select', 'required',  'options' => $programsss, 'class' => 'fs14', 'default' => (isset($program_id) && !empty($program_id) ? $program_id : ''))); ?>
                                <hr>
                            </div>
                            <div class="large-8 columns">
                                <div id="StudentSectionList">
                                    <?=  $this->Form->input('student_section_id', array('id' => 'StudentSection', 'style' => 'width: 100%;', 'label' => 'Student Section: ', 'required',  'type' => 'select', 'class' => 'fs14', 'options' => $student_sections, 'default' => (isset($student_section_id) && !empty($student_section_id) ? $student_section_id : ''))) ?>
                                </div>
                                <hr>
                            </div>
                        </div>
                        <div class="row">
                            <div class="large-5 columns">
                                <div id="StudentList">
                                    <?=  $this->Form->input('student_id', array('id' => 'Student', 'style' => 'width: 100%;', 'label' => 'Student taking the Supplementary Exam: ', 'required',  'type' => 'select', 'class' => 'fs14', 'options' => $students, 'default' => (isset($student_id) && !empty($student_id) ? $student_id :  ''))) ?>
                                </div>
                                <hr>
                            </div>
                            <div class="large-7 columns">
                                <div id="CourseRegistered1">
                                    <?=  $this->Form->input('course_registration_id', array('id' => 'CourseRegistered', 'style' => 'width: 100%;', 'class' => 'fs14', 'required', 'label' => 'The Course the student is taking as a Supplementary Exam: ', 'type' => 'select', 'options' => $student_registered_courses, 'default' => (isset($registered_course_id) && !empty($registered_course_id) ? $registered_course_id : ''))) ?>
                                </div>
                                <hr>
                            </div>
                        </div>

                        <div id="showAssesmentArea">
                            <div class="row">
                                <div class="large-6 columns">
                                <b>Previous Garde History:</b> 
                                    <div id="PreviousGarde">
                                        <?=  $this->element('registered_or_add_course_grade_history'); ?>
                                    </div>
                                    <hr>
                                </div>
                                <div class="large-3 columns">
                                    <div>
                                        <?= $this->Form->input('makeup_exam_result', array('id' => 'MakeupExamResult', 'style' => 'width: 50%;', 'label' => 'New Exam Result: ', 'required', 'type' => 'number', 'min' => 0, 'max' => 100, 'step' => 'any', 'onBlur' => 'updateGrade();')) ?>
                                        <?php
                                        //echo $this->Form->input('ExamGradeChange.'.$st_count.'.exam_grade_id', array('type' => 'hidden', 'value' => (!isset($student['MakeupExam']) ? $student['ExamGrade'][0]['id'] : $student['ExamGradeChange'][0]['ExamGrade']['id'])));
                                        // echo $this->Form->input('ExamGradeChange.'.$st_count.'.result', array('id' => 'GradeChangeResult_result_'.$st_count, 'label' => false, 'maxlength' => 5, 'style' => 'width:100px', 'onBlur' => 'updateExamGradeChange(this, '.$st_count.')'));
                                        ?>
                                    </div>
                                </div>
                                <div class="large-3 columns">
                                    <b>New Exam Grade: </b>
                                    <div id="ExamGrade1">
                                        
                                    </div>
                                    <hr>
                                </div>
                            </div>
                            <div class="row">
                                <div class="large-3 columns">
                                    <?=  $this->Form->input('minute_number', array('label' => 'Minute Number: ', 'id' => 'MinuteNumber', 'style' => 'width: 90%;', 'class' => 'fs14')); ?>
                                </div>

                                <div class="large-6 columns">
                                    <?=  $this->Form->input('reason', array('label' => 'Remark: ', 'id' => 'ReasonRemark', 'type' => 'textarea', 'cols' => 40, 'rows' => 5, 'required')); ?>
                                    <?=  $this->Form->hidden('grade', array('id' => 'ExamGradeR', 'label' => false, 'default' => $grade)); ?>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <hr>
                    <?=  $this->Form->Submit('Add Supplementary Exam Result', array('div' => false, 'id' => 'addSupplementaryExam', 'class' => 'tiny radius button bg-blue')); ?>
                </div>
                <?= $this->Form->end(); ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    var form_being_submitted = false;

	$('#addSupplementaryExam').click(function() {

        var isValid = true;
        var reasonRemark = $('#ReasonRemark').val();

        // Check all required input, select, and textarea elements
        /* $('#ExamGradeChangeDepartmentMakeupExamResultForm [required]').each(function() {
            if ($(this).val() === '' || $(this).val() == '' || $(this).val() === 0 || $(this).val() === '0') {
                $(this).focus();
                $(this).setAttribute('title', 'Please fill out this field.');
                //$(this).attr('title', 'Please fill out this field.');
                isValid = false;
                return false; // Break the loop
            }
        }); */

        $('input[name*="data[ExamGradeChange]"], select[name*="data[ExamGradeChange]"]').each(function() {
			//$(this).val(''); // Set their values to empty
			//$(this).removeAttr('required');
            if (/* $(this).is('[required]') &&  */($(this).val() === '' || $(this).val() === 0 || $(this).val() === '0')) {
                $(this).focus();
                $(this).setAttribute('title', 'Please fill out this field.');
                //$(this).attr('title', 'Please fill out this field.');
                isValid = false;
                return false; // Break the loop
            }
		});
        
        if (reasonRemark != '') {
           
        } else {
            $('#ReasonRemark').focus();
            $('#ReasonRemark').setAttribute('title', 'Please fill out this field.');
            isValid = false;
            return false;
        }

		if (form_being_submitted) {
			alert('Adding Supplementary Exam Result, please wait a moment or refresh your page...');
			$('#SubmitButton').attr('disabled', true);
			return false;
		}

		if (!form_being_submitted && isValid) {
			$('#addSupplementaryExam').val('Adding Supplementary Exam Result...');
			form_being_submitted = true;
			return true;
		} else {
			return false;
		}

	});

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}

    /* if (window.history && window.history.pushState) {
        // Add a custom history state when the page loads
        window.history.pushState('forward', null, window.location.href);

        // Listen for the popstate event to handle back and forward navigation
        $(window).on('popstate', function() {
            //alert(window.location.href);
            // Check if the current URL is your target URL (replace 'your_target_url' with the actual URL)
            var targetUrl = window.location.href;

            if (window.location.href === targetUrl) {
                // Reload the page to clear form inputs
                location.reload(true);
            } else {
                // Redirect back to the target page
                window.location.href = targetUrl;
            }
        });
    } */
</script>