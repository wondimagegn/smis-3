<div class="box">
    <div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-check-outline" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Evaluate your instructor'); ?></span>
		</div>
	</div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
            <?= $this->Form->create('StudentEvalutionRate', array('onSubmit' => 'return checkForm(this);')); ?>
                <?php 

                //debug($_POST);
                // Clearing form data after submit
                if (!empty($_POST['data']['StudentEvalutionRate']) && $_SERVER['REQUEST_METHOD'] == 'POST') { 
                    //debug($_POST['data']['StudentEvalutionRate']);
                    unset($this->request->data['StudentEvalutionRate']);
                }

                if (!empty($_POST['data']['StudentEvalutionComment']) && $_SERVER['REQUEST_METHOD'] == 'POST') { 
                    //debug($_POST['data']['StudentEvalutionComment']);
                    unset($this->request->data['StudentEvalutionComment']);
                }

                if (!empty($courseList)) { 
                    //debug($courseList); 
                    $allQuestionCount = 0;
                    $allActiveObjectiveTypeQuestionCount = ClassRegistry::init('InstructorEvalutionQuestion')->find('count', array(
                        'conditions' => array(
                            'InstructorEvalutionQuestion.type' => 'objective',
                            'InstructorEvalutionQuestion.for' => 'student',
                            'InstructorEvalutionQuestion.active' => 1
                        )
                    ));

                    $allActiveOpenEndedTypeQuestionCount = ClassRegistry::init('InstructorEvalutionQuestion')->find('count', array(
                        'conditions' => array(
                            'InstructorEvalutionQuestion.type' => 'open-ended',
                            'InstructorEvalutionQuestion.for' => 'student',
                            'InstructorEvalutionQuestion.active' => 1
                        )
                    ));

                    if (isset($allActiveObjectiveTypeQuestionCount) && !empty($allActiveObjectiveTypeQuestionCount)) {
                        $allQuestionCount += $allActiveObjectiveTypeQuestionCount;
                    }

                    if (isset($allActiveOpenEndedTypeQuestionCount) && !empty($allActiveOpenEndedTypeQuestionCount)) {
                        $allQuestionCount += $allActiveOpenEndedTypeQuestionCount;
                    } 

                    $course_academic_year = (isset($courseList['CourseRegistration']['academic_year']) ? $courseList['CourseRegistration']['academic_year'] : $courseList['CourseAdd']['academic_year']);
                    $course_semester = (isset($courseList['CourseRegistration']['semester']) ? $courseList['CourseRegistration']['semester'] : $courseList['CourseAdd']['semester']); ?>

                    <blockquote>
                        <span class="fs14 text-black"><u><b><?= $courseList['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['Title']['title'] . ' ' . $courseList['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['full_name'] . '(' . $courseList['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['Position']['position'] . ')'?></b></u> is waiting your evaluation for <u><b><?= $courseList['PublishedCourse']['Course']['course_title'] . ' (' . $courseList['PublishedCourse']['Course']['course_code'] . ')' ?></b></u> which <?= strcasecmp($courseList['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['gender'], 'male') == 0 ? 'he':'she' ?>  thought you on  <b><?= ($course_semester == 'I' ? '1st' : ($course_semester == 'II' ? '2nd':'3rd')) .'  Semester of '.  $course_academic_year . ' Academic year'; ?></b>. </span>
						<br/><br/>
                        <p class="fs16" style="text-align:justify;"><u><b>Notes:</b></u> This questionnaire has been prepared to get your views regarding the teaching performance of your instructor. Please read carefully each of the statements listed from <?= $allQuestionCount!=0 ?'1 - '. $allQuestionCount : ' ' ?> below and indicate how you evaluate your instructor on each statement by selecting one of the following options against each statement. <strong>Your evaluation will be kept private to you and your identity will not be revealed to instructor or for the department.</strong> Thus, Please evaluate your instructor frankly and honestly as possible as <strong> your evaluation will be used for improving quality of eduation.</strong>.</p>
					</blockquote>

                    <?= $this->Form->hidden('Instructor.full_name', array('label' => false, 'value' => $courseList['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['Title']['title'] . ' ' . $courseList['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['full_name'] . ' ' . $courseList['PublishedCourse']['CourseInstructorAssignment'][0]['Staff']['Position']['position'] . ' ')); ?>
                    
                    <table cellspacing="0" cellpadding="0" class="fs14 table">
                        <thead>
                            <tr>
								<td style="width:2%;" class="center">#</td>
								<td style="width:63%;" class="vcenter">Question</td>
								<td style="width:35%;" class="vcenter">Your Response</td>
							</tr>
                        </thead>
                        <tbody>
                            <?php
                            $count = 1;
                            $options = array(5 => 'Very good', 4 => 'Good', 3 => 'Fair', 2 => 'Poor', 1 => 'Very Poor', 0 => 'Do not know');
                            $attributes = array('label' => true, 'div' => false, 'legend' => false, 'separator' => '<br/>', 'required' => true);

                            if (!empty($instructorEvalutionQuestionsObjective)) {
                                foreach ($instructorEvalutionQuestionsObjective as $kc => $vc) { ?>
                                   <tr>
                                        <td class="hcenter"><?= $count; ?></td>
                                        <td>
                                            <?= $this->Form->hidden('StudentEvalutionRate.' . $count . '.instructor_evalution_question_id', array('value' => $vc['InstructorEvalutionQuestion']['id'])); ?>
                                            <?= $this->Form->hidden('StudentEvalutionRate.' . $count . '.student_id', array('value' => (isset($courseList['CourseRegistration']['student_id']) ? $courseList['CourseRegistration']['student_id'] : $courseList['CourseAdd']['student_id']))); ?>
                                            <?= $this->Form->hidden('StudentEvalutionRate.' . $count . '.published_course_id', array('value' => (isset($courseList['CourseRegistration']['published_course_id']) ? $courseList['CourseRegistration']['published_course_id'] : $courseList['CourseAdd']['published_course_id']))); ?>
                                            <span class="fs14" style="padding-right:5%; text-align:justify">
                                                <?= $vc['InstructorEvalutionQuestion']['question'] . (isset($vc['InstructorEvalutionQuestion']['question_amharic']) && !empty($vc['InstructorEvalutionQuestion']['question_amharic']) ? '<br/>' . $vc['InstructorEvalutionQuestion']['question_amharic'] . '' : ''); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <p style="padding:1%">
                                                <?= $this->Form->radio('StudentEvalutionRate.' . $count . '.rating', $options, $attributes); ?>
                                            </p>
                                        </td>
                                    </tr>
                                    <?php
                                    $count++;
                                }
                            }

                            if (!empty($instructorEvalutionQuestionsOpenEnded)) {
                                foreach ($instructorEvalutionQuestionsOpenEnded as $kc => $vc) { ?>
                                    <tr>
                                        <td class="hcenter"><?= $count; ?></td>
                                        <td>
                                            <?= $this->Form->hidden('StudentEvalutionComment.' . $count . '.instructor_evalution_question_id', array('value' => $vc['InstructorEvalutionQuestion']['id'])); ?>
                                            <?= $this->Form->hidden('StudentEvalutionComment.' . $count . '.student_id', array('value' => (isset($courseList['CourseRegistration']['student_id']) ? $courseList['CourseRegistration']['student_id'] : $courseList['CourseAdd']['student_id']))); ?>
                                            <?= $this->Form->hidden('StudentEvalutionComment.' . $count . '.published_course_id', array('value' => (isset($courseList['CourseRegistration']['published_course_id']) ? $courseList['CourseRegistration']['published_course_id'] : $courseList['CourseAdd']['published_course_id']))); ?>

                                            <?= $vc['InstructorEvalutionQuestion']['question'] . (isset($vc['InstructorEvalutionQuestion']['question_amharic']) && !empty($vc['InstructorEvalutionQuestion']['question_amharic']) ? '<br/>' . $vc['InstructorEvalutionQuestion']['question_amharic'] . '' : ''); ?>
                                        </td>
                                        <td><?= $this->Form->input('StudentEvalutionComment.' . $count . '.comment', array('label' => false, /* 'type' => 'text' */)); ?></td>
                                   </tr>
                                   <?php
                                   $count++;
                                } 
                            } ?>
                        </tbody>
                    </table>

                    <div class="row">
						<div class="large-12 columns">
                            <hr>
                            <?= $this->Form->submit('Submit Evaluation', array('id' => 'submitEvaluationResult', 'class' => 'tiny radius button bg-blue', 'div' => 'false')); ?>
							<?= $this->Form->end(); ?>
						</div>
					</div>

                    <?php
                } ?>
            </div>
        </div>
    </div>
</div>

<script>

	var form_being_submitted = false;

	var checkForm = function(form) {

		if (form_being_submitted) {
			alert("Your evaluation is being submitted, please wait a moment...");
			form.submitEvaluationResult.disabled = true;
			return false;
		}

		form.submitEvaluationResult.value = 'Submitting Evalution...';
		form_being_submitted = true;
		return true;
	};

	var resetForm = function(form) {
		form.submitEvaluationResult.disabled = false;
		form.submitEvaluationResult.value = "Submit";
		form_being_submitted = false;
	};


	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>