<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-check-outline" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Evaluate Your Colleagues'); ?></span>
		</div>
	</div>

	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<div class="examGrades ">

					<div style="margin-top: -30px;"><hr></div>
					<?= $this->Form->create('ColleagueEvalutionRate'); ?>

					<div onclick="toggleViewFullId('ListSection')">
						<?php
						debug($_POST);
						//debug($_SERVER);
						// Clearing form data after submit
						if(!empty($this->request->data['ColleagueEvalutionRate']) && $_SERVER['REQUEST_METHOD'] == 'POST'){ 
							//debug($_POST['data']['ColleagueEvalutionRate']);
							unset($this->request->data['ColleagueEvalutionRate']);
							//header("Refresh:0"); 
						}

						if (!empty($colleagueLists)) {
							echo $this->Html->image('plus2.gif', array('id' => 'ListSectionImg')); ?>
							<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListSectionTxt"> Display Filter</span>
							<?php
						} else {
							echo $this->Html->image('minus2.gif', array('id' => 'ListSectionImg')); ?>
							<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListSectionTxt"> Hide Filter</span>
							<?php
						} ?>
					</div>
					
					<div id="ListSection" style="display:display;">
						<fieldset style="padding-bottom: 0px;padding-top: 15px;">
							<!-- <legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend> -->
							<div class="large-3 columns">
								<?= $this->Form->input('Search.acadamic_year', array('id' => 'AcadamicYear', 'label' => 'Academic Year: ', 'class' => 'fs14', 'style' => 'width:90%;', 'type' => 'select', 'options' => $acyear_array_data, /* 'default' => (isset($academic_year_selected) ? $academic_year_selected : $defaultacademicyear) */)); ?>
							</div>
							<div class="large-3 columns">
								<?= $this->Form->input('Search.semester', array('options' => Configure::read('semesters'), 'style' => 'width:90%;', 'type' => 'select', 'label' => 'Semester: ', 'default' => (isset($semester_selected) ? $semester_selected : $current_semester))); ?>
							</div>
							<div class="large-6 columns">
								<?= $this->Form->input('Search.name', array('label' => 'Staff Name: ', 'style' => 'width:90%;', 'placeholder' => 'Leave empty for all staffs or enter name here...', 'maxlength' => 25)); ?>
							</div>
							<hr>
							<?= $this->Form->submit(__('Search'), array('name' => 'getInstructorList', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
							<?php //echo $this->Form->end(); ?>
						</fieldset>
					</div>
					<hr>
					
					<?php
					/* if (isset($this->request->data['getInstructorList']) && !empty($this->request->data['Search']['name']) && empty($colleagueLists)) { ?>
						<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>No staff is found with name starting with <b><i>"<?= $this->request->data['Search']['name']; ?>"</i></b> who is assigned for a course for <?= ($this->request->data['Search']['semester'] == 'I' ? '1st' : ($this->request->data['Search']['semester'] == 'II' ? '2nd' : '3rd')) . '  semester of ' . $this->request->data['Search']['acadamic_year']; ?> academic year or you already evaluated him/her previously. </div>
						<?php
					} else if (empty($colleagueLists)) { ?>
						<div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>No instructor found to evaluate for now. Either all instructors have been evaluated by you, or academic calendar for staff evaluation is not opened/deadline passed for <i><u><?= ($this->request->data['Search']['semester'] == 'I' ? '1st' : ($this->request->data['Search']['semester'] == 'II' ? '2nd' : '3rd')) . '  semester of ' . $this->request->data['Search']['acadamic_year']; ?></u></i> academic year. </div>
						<?php
					}  */
					
					//echo $this->Form->create('ColleagueEvalutionRate', array('onSubmit' => 'return checkForm(this);')); 

					if (!empty($colleagueLists)) {
						debug($default_staff_id); ?>
						<table class="fs14" cellpadding="0" cellspacing="0" class='table'>
							<tr>
								<td style="width:25%;" class="center">Instructor</td>
								<td colspan="3">
									<div class="large-8 columns">
										<br>
										<?= $this->Form->input('Search.staff_id', array('style' => 'width: 90%;', 'class' => 'fs14', 'id' => 'Staff', 'label' => false, 'type' => 'select', 'options' => $colleagueLists, 'onchange' => 'toggleFields("Staff")', 'default' => (isset($default_staff_id) ? $default_staff_id : $this->request->data['Search']['staff_id']))); ?>
									</div>
								</td>
							</tr>
						</table>
						<?php
					}

					if (isset($instructorEvalutionQuestionsObjective) && !empty($instructorEvalutionQuestionsObjective) && !empty($colleagueLists)) { ?>
						
						<hr>
						<blockquote>
							<span class="fs16 text-black" style="text-align:justify;"><u><b id="nameOfStaff"></b></u> is waiting your evaluation for <?= ($this->request->data['Search']['semester'] == 'I' ? '1st' : ($this->request->data['Search']['semester'] == 'II' ? '2nd' : '3rd')) . '  Semester of ' . $this->request->data['Search']['acadamic_year'] . ' Academic Year.'; ?>
								<br /><br />
								<p class="fs16" style="text-align:justify;"><u><b>Notes:</b></u> Listed below are statements, which describe aspects of your colleague’s behavior. Please rate him/her on each of these items by selectin the appropriate coded response category. Your ratings should be based on a comparison between the particular individual and the other members of the department. If you feel that you cannot rate him/her on a particular item or that the item is not applicable to his/her work, then mark the response category <strong>Do not know</strong>. Please make sure that the submitted evaluation is correct as <strong> your evaluation will be used for improving quality of eduation</strong>.
							</span></p>
						</blockquote>
						<hr>

						<div style="overflow-x:auto;">
							<table cellspacing="0" cellpadding="0" class="fs14 table">
								<thead>
									<tr>
										<td style="width:2%;">#</td>
										<td style="width:58%;" class="vcenter">Question</td>
										<td class="vcenter">Your Response</td>
									</tr>
								</thead>
								<tbody>
									<?php
									$count = 1;
									$options = array(5 => ' Very good', 4 => ' Good', 3 => ' Fair', 2 => ' Poor', 1 => ' Very Poor', 0 => ' Do not know');
									$attributes = array('label' => true, 'div' => true, 'legend' => false, 'separator' => '<br/>', 'required' => true);

									foreach ($instructorEvalutionQuestionsObjective as $kc => $vc) { ?>
										<tr>
											<td>
												<?= $count; ?>
												<?= $this->Form->hidden('ColleagueEvalutionRate.' . $count . '.instructor_evalution_question_id', array('label' => false, 'div' => false, 'value' => $vc['InstructorEvalutionQuestion']['id'])); ?>
											</td>
											<td><span class="fs14" style="padding-right:2%;"><?= $vc['InstructorEvalutionQuestion']['question'] . (isset($vc['InstructorEvalutionQuestion']['question_amharic']) && !empty($vc['InstructorEvalutionQuestion']['question_amharic']) ? '<br/>' . $vc['InstructorEvalutionQuestion']['question_amharic'] . '</span>' : '</span>'); ?></td>
											<td><p style="padding:2%;"><?= $this->Form->radio('ColleagueEvalutionRate.' . $count . '.rating', $options, $attributes); ?></p></td>
										</tr>
										<?php
										$count++;
									} ?>
								</tbody>
							</table>
						</div>
						<hr>

						<?= $this->Form->submit(__('Submit Evalution'), array('name' => 'submitEvaluationResult', 'id' => 'submitEvaluationResult', 'div' => false, 'class' => 'tiny radius button bg-blue')); ?>
						
						<?php
					} ?>

					<?= $this->Form->end(); ?>
				</div>
			</div>
		</div>
	</div>
</div>

<script>

	$('#nameOfStaff').html($("#Staff option:selected").text());

	function toggleView(obj) {
		if ($('#c' + obj.id).css("display") == 'none'){
			$('#i' + obj.id).attr("src", '/img/minus2.gif');
		} else {
			$('#i' + obj.id).attr("src", '/img/plus2.gif');
		}
		$('#c' + obj.id).toggle("slow");
	}

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

	function toggleFields(id) {
		$('#nameOfStaff').html($("#Staff option:selected").text());
	}

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
		form.submitEvaluationResult.value = "Submit Evalution";
		form_being_submitted = false;
	};

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>