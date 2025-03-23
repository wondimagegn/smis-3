<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-check" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Fx Grade Management'); ?></span>
		</div>
	</div>
    <div class="box-body">
       	<div class="row">
	  		<div class="large-12 columns">
            
				<div class="examGrades manage_ng">

					<?= $this->Form->create('ExamGrade', array('novalidate' => true)); ?>

					<?= $this->element('publish_course_filter_by_dept'); ?>

					<div id="manage_ng_form">
						<?php //debug($students_with_ng); 
						if (!empty($students_with_ng)) { ?>
							<hr>
							<table cellpadding="0" cellspacing="0" class="table">
								<tr>
									<td class="center">
										<div class="row">
											<div class="large-3 columns">
												<br>
												Minute Number:
											</div>
											<div class="large-6 columns">
												<br>
												<?= $this->Form->input('ExamGrade.minute_number', array('id' => 'minuteNumber', 'required', 'style' => 'width: 70%', 'label' => false)); ?>
											</div>
											<div class="large-3 columns">
											</div>
										</div>
									</td>
								</tr>
							</table>
							<br>

							<h6 id="validation-message_non_selected" class="text-red fs14"></h6>
							<br>

							<div style="overflow-x:auto;">
								<table cellpadding="0" cellspacing="0" class="table">
									<thead>
										<tr>
											<th style="width:3%" class="center">#</th>
											<th class="vcenter">Full Name</th>
											<th class="center">Sex</th>
											<th class="center">Student ID</th>
											<th class="center">Current Grade</th>
											<th class="center">New Grade</th>
										</tr>
									</thead>
									<tbody>
										<?php
										$count = 0;
										foreach ($students_with_ng as $key => $student) {
											debug($student);
											$count++; ?>
											<tr>
												<td class="center"><?= $count; ?></td>
												<td class="vcenter"><?= $student['full_name']; ?></td>
												<td class="center"><?= (strcasecmp(trim($student['gender']), 'male') == 0 ? 'M' : (strcasecmp(trim($student['gender']), 'female') == 0 ? 'F' : '')); ?></td>
												<td class="center"><?= $student['studentnumber']; ?></td>
												<td class="center"><?= $student['grade'];?></td>
												<td class="center">
													<?= $this->Form->input('ExamGrade.'.$count.'.id', array('value' => $student['grade_id'], 'label' => false, 'type' => 'hidden')); ?>
													<?= $this->Form->input('ExamGrade.'.$count.'.grade', array('label' => false, 'type' => 'select', 'options' => $applicable_grades)); ?>
													<?= $this->Form->input('ExamGrade.'.$count.'.student_id', array('value' => $student['student_id'], 'label' => false, 'type' => 'hidden')); ?>
													<?= $this->Form->input('ExamGrade.'.$count.'.p_c_id', array('value' => (isset($student['p_c_id']) && !empty($student['p_c_id']) ? $student['p_c_id'] : (!empty($published_course_combo_id) ? $published_course_combo_id : 0)), 'label' => false, 'type' => 'hidden')); ?>
												</td>
											</tr>
											<?php
										} ?>
									</tbody>
								</table>
							</div>
							<hr>
							<?= $this->Form->submit(__('Change Fx Grade'), array('name' => 'changeNgGrade', 'id' => 'changeNgGrade', 'div' => false, 'class' =>' tiny radius button bg-blue')); 
						} ?>
					</div>
					<?= $this->Form->end(); ?>
				</div>
	  		</div>
		</div>
    </div>
</div>

<script>
	$(document).ready(function() {

		$("#PublishedCourse").change(function() {
			//serialize form data
			window.location.replace("/exam_grades/manage_fx/" + $("#PublishedCourse").val());
			$("#manage_ng_form").hide();

			if ($("#minuteNumber").length) {
				$("#minuteNumber").val('');
			}

			if ($("#changeNgGrade").length) {
				$("#changeNgGrade").attr('disabled', true);
			}

			$('select[name^="data[ExamGrade]"]').each(function() {
				const namePatternnn = /data\[ExamGrade\]\[\d+\]\[grade\]/;
				if (namePatternnn.test($(this).attr('name')) && $(this).val()) {
					$(this).val('');
				}
			});
			
		});

		//$("#manage_ng_form").show();
	});

	var form_being_submitted = false;

	const validationMessageNonSelected = document.getElementById('validation-message_non_selected');

	$('#changeNgGrade').click(function(event) {

		$('form').removeAttr('novalidate');

		var isValid = true;

		var minuteNumber = $('#minuteNumber').val();

		if (minuteNumber == '') { 
			//form.minuteNumber.focus();
			event.preventDefault(); // Prevent form submission
			$('#minuteNumber').focus();
			isValid = false;
			return false;
		}

		let atLeastOneSelected = false;

        $('select[name^="data[ExamGrade]"]').each(function() {
            const namePattern = /data\[ExamGrade\]\[\d+\]\[grade\]/;
            if (namePattern.test($(this).attr('name')) && $(this).val()) {
                atLeastOneSelected = true;
                return false; // Break out of the loop
            }
        });

		/* $('select[name^="data[ExamGrade]"]').each(function() {
            if ($(this).val()) {
                atLeastOneSelected = true;
                return false; // Break out of the loop
            }
        }); */

		//alert(atLeastOneSelected);

        if (!atLeastOneSelected) {
            event.preventDefault(); // Prevent form submission
			isValid = false;
            alert('Please select at least one student grade before submitting the form.');
			validationMessageNonSelected.innerHTML = 'Please select at least on student grade before submitting the form.';
        }

		// remove the validation cheking of the form after minitue number and atleast on student grade is selected
		$('form').attr('novalidate', 'novalidate');

		if (form_being_submitted) {
			alert("Managing Fx grade for the selected students, please wait a moment...");
			$('#changeNgGrade').attr('disabled', true);
			return false;
		}

		if (!form_being_submitted && isValid) {
			$('#changeNgGrade').val('Managing Fx Grade...');
			if ($("#listPublishedCourses").length) {
				$("#listPublishedCourses").attr('disabled', true);
			}
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
