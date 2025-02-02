<?= $this->Form->create('ExamResult'); ?>
<script type="text/javascript">
	var grade_scale = Array();
	<?php
	if (isset($grade_scale['GradeScaleDetail'])) {
		$grade_scale_count = 0;
		foreach ($grade_scale['GradeScaleDetail'] as $key => $scale_detail) { ?>
			grade_scale[<?= $grade_scale_count; ?>] = Array();
			grade_scale[<?= $grade_scale_count; ?>][0] = <?= $scale_detail['minimum_result']; ?>;
			grade_scale[<?= $grade_scale_count; ?>][1] = <?= $scale_detail['maximum_result']; ?>;
			grade_scale[<?= $grade_scale_count; ?>][2] = '<?= $scale_detail['grade']; ?>';
			<?php
			$grade_scale_count++;
		}
	} ?>

	function updateExamGradeChange(obj, st_count) {
		if (obj.value != "" && isNaN(obj.value)) {
			alert('Please enter a valid result.');
			$('#' + obj.id).focus();
			$('#' + obj.id).select();
			$('#GradeChangeResult_grade_' + st_count).empty();
			$('#GradeChangeResult_grade_' + st_count).append('---');
			return false;
		} else if (obj.value != "" && parseFloat(obj.value) > 100) {
			alert('The maximum value of exam result is 100.');
			$('#' + obj.id).focus();
			$('#' + obj.id).select();
			$('#GradeChangeResult_grade_' + st_count).empty();
			$('#GradeChangeResult_grade_' + st_count).append('---');
			return false;
		} else if (obj.value != "" && parseFloat(obj.value) < 0) {
			alert('The minimum value of exam result is 0.');
			$('#' + obj.id).focus();
			$('#' + obj.id).select();
			$('#GradeChangeResult_grade_' + st_count).empty();
			$('#GradeChangeResult_grade_' + st_count).append('---');
			return false;
		} else {
			for (var i = 0; i < grade_scale.length; i++) {
				if (parseFloat(obj.value) >= grade_scale[i][0] && parseFloat(obj.value) <= grade_scale[i][1]) {
					$('#GradeChangeResult_grade_' + st_count).empty();
					$('#GradeChangeResult_grade_' + st_count).append(grade_scale[i][2]);
				}
			}
			return true;
		}
	}

	function submitGrdeChangeRequest(id, st_count) {
		if ($('#' + id + '_result_' + st_count).attr('value') == "") {
			alert('You are required to submit exam result');
			return false;
		}
		else if ($('#' + id + '_reason_' + st_count).attr('value') == "") {
			alert('You are required to submit exam grade change reason');
			return false;
		} else {
			return true
		}
	}

	function toggleView(obj) {
		if ($('#c' + obj.id).css("display") == 'none')
			$('#i' + obj.id).attr("src", '/img/minus2.gif');
		else
			$('#i' + obj.id).attr("src", '/img/plus2.gif');
		$('#c' + obj.id).toggle("slow");
	}

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

	var grade = new Array();

	function updateExamTotal(obj, row, exam_num, percent, exam_name, sum_it) {
		var sum = 0;
		var result = 0;
		var invalid = false;
		var result_found = false;
		if (obj.value != "" && isNaN(obj.value)) {
			alert('Please enter a valid result.');
			$('#' + obj.id).focus();
			$('#' + obj.id).select();
			return false;
		} else if (obj.value != "" && parseFloat(obj.value) > parseFloat(percent)) {
			alert('The maximum value of "' + exam_name + '" exam result is ' + percent + '.');
			$('#' + obj.id).focus();
			$('#' + obj.id).select();
			return false;
		} else if (obj.value != "" && parseFloat(obj.value) < 0) {
			alert('The minimum value of "' + exam_name + '" exam result is 0.');
			$('#' + obj.id).focus();
			$('#' + obj.id).select();
			return false;
		}

		if (!sum_it) {
			return true;
		}

		//contingency ???
		for (var i = 1; i <= exam_num; i++) {
			result = $('#result_' + row + '_' + i).val();
			//alert('#result_'+row+'_'+i);
			//alert(result);
			//alert(percent)
			if (isNaN(result)) {
				invalid = true;
				//alert('Please enter a valid result.');
				//$('#result_'+row+'_'+i).focus();
				//$('#result_'+row+'_'+i).select();
				break;
			} else {
				if (result != "") {

					//if(parseFloat(result) > parseFloat(percent)) {
					/*alert('The maximum value of "'+exam_name+'" exam result is '+percent+'.');
					$('#result_'+row+'_'+i).focus();
					$('#result_'+row+'_'+i).select();
					
					break;	
					*/
					//}
					//else {
					sum += parseFloat(result);
					result_found = true;
					//}
				}
			}
		}

		if (invalid) {
			$('#total_100_' + row).empty();
			$('#total_100_' + row).append('---');
		} else {
			$('#total_100_' + row).empty();
			if (result_found) {
				$('#total_100_' + row).append(sum);
			} else {
				$('#total_100_' + row).append('---');
			}
		}
	}

	function confirmGradeSubmitCancelation() {
		return confirm("Are you sure you want to rollback the selected grade submissions?");
	}

	function courseInProgress(c, obj) {
		if (obj.checked) {
			grade[c] = window.document.getElementById('G_' + c).innerHTML;
			window.document.getElementById('G_' + c).innerHTML = '**';
		}
		else {
			window.document.getElementById('G_' + c).innerHTML = grade[c];
		}
	}

	function showHideGradeScale() {
		if ($("#ShowHideGradeScale").val() == 'Show Grade Scale') {
			var p_course_id = $("#PublishedCourse").val();
			$("#GradeScale").empty();
			$("#GradeScale").append('Loading ...');
			var formUrl = '/published_courses/get_course_grade_scale/' + p_course_id;
			$.ajax({
				type: 'get',
				url: formUrl,
				data: p_course_id,
				success: function (data, textStatus, xhr) {
					$("#GradeScale").empty();
					$("#GradeScale").append(data);
					$("#ShowHideGradeScale").attr('value', 'Hide Grade Scale');
				},
				error: function (xhr, textStatus, error) {
					alert(textStatus);
				}
			});
		} else {
			$("#GradeScale").empty();
			$("#ShowHideGradeScale").attr('value', 'Show Grade Scale');
		}

		return false;
	}

	function showHideGradeStatistics() {
		var p_course_id = $("#PublishedCourse").val();
		//alert(p_course_id);
		if ($("#ShowHideGradeDistribution").val() == 'Show Grade Distribution') {
			//var p_course_id = $("#PublishedCourse").val();
			$("#GradeDistribution").empty();
			$("#GradeDistribution").append('Loading ...');
			var formUrl = '/published_courses/get_course_grade_stats/' + p_course_id;
			$.ajax({
				type: 'get',
				url: formUrl,
				data: p_course_id,
				success: function(data, textStatus, xhr) {
					$("#GradeDistribution").empty();
					$("#GradeDistribution").append(data);
					$("#ShowHideGradeDistribution").attr('value', 'Hide Grade Distribution');
				},
				error: function(xhr, textStatus, error) {
					alert(textStatus);
				}
			});
		} else {
			$("#GradeDistribution").empty();
			$("#ShowHideGradeDistribution").attr('value', 'Show Grade Distribution');
		}

		return false;
	}

	$(document).ready(function () {
		$("#PublishedCourse").change(function () {
			$("#flashMessage").remove();
			$("#PublishedCourse").attr('disabled', true);
			var pc = $("#PublishedCourse").val();
			$("#ExamResultDiv").empty();

			if (pc != '' && pc > 1) {
				$("#ExamResultDiv").append('<p>Loading ...</p>');
				//get form action
				var formUrl = '/examResults/rollback_entry_form/' + pc;
				$.ajax({
					type: 'get',
					url: formUrl,
					//data: pc,
					success: function (data, textStatus, xhr) {
						// alert(data);
						$("#ExamResultDiv").empty();
						$("#ExamResultDiv").append(data);
						$("#PublishedCourse").attr('disabled', false);
					},
					error: function (xhr, textStatus, error) {
						alert(textStatus);
					}
				});

				return false;

			} else {
				$("#PublishedCourse").attr('disabled', false);
			}
			return false;
		});
	});
</script>

<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-check" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Rollback Grade Submission Back To Instructor'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<div class="examResults index" style="margin-top: -30px;">
					<hr>
					<div onclick="toggleViewFullId('ListPublishedCourse')">
						<?php
						if (!empty($publishedCourses)) {
							echo $this->Html->image('plus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
							<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">&nbsp; Display Filter</span>
							<?php
						} else {
							echo $this->Html->image('minus2.gif', array('id' => 'ListPublishedCourseImg'));
							?><span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">&nbsp; Hide Filter</span>
							<?php
						} ?>
					</div>

					<div id="ListPublishedCourse" style="display:<?= (!empty($publishedCourses) ? 'none' : 'display'); ?>">
						<fieldset style="padding-bottom: 0px;padding-top: 15px;">
							<!-- <legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend> -->
							<div class="row">
								<div class="large-3 columns">
									<?= $this->Form->input('acadamic_year', array('id' => 'AcadamicYear', 'label' => 'Acadamic Year: ', 'class' => 'fs14', 'style' => 'width:90%', 'type' => 'select', 'options' => $acyear_list, 'default' => (isset($selected_acadamic_year) ? $selected_acadamic_year : (isset($academic_year) && !empty($academic_year) ? $academic_year : $defaultacademicyear)))); ?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('semester', array('id' => 'Semester', 'class' => 'fs14', 'type' => 'select',  'style' => 'width:85%', 'label' => 'Semester: ', 'options' => Configure::read('semesters'), 'default' => $semester)); ?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('program_id', array('id' => 'Program', 'class' => 'fs14', 'label' => 'Program: ', 'style' => 'width:90%', 'type' => 'select', 'options' => $programs, 'default' => isset($program_id) ? $program_id : "")); ?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('program_type_id', array('id' => 'ProgramType', 'class' => 'fs14', 'label' => 'Program Type: ', 'style' => 'width:90%', 'type' => 'select', 'options' => $program_types, 'default' => $program_type_id)); ?>
								</div>
							</div>
							<div class="row">
								<div class="large-6 columns">
									<?php
									if (!empty($departments)) {
										echo $this->Form->input('department_id', array('label' => 'Department: ', 'style' => 'width:90%;'));
									} else if (!empty($colleges)) {
										echo $this->Form->input('college_id', array('label' => 'College: ', 'style' => 'width:90%;'));
									}
									?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('year_level_id', array('id' => 'YearLevel', 'class' => 'fs14','label' => 'Year Level: ', 'style' => 'width:90%', 'type' => 'select', 'options' => $yearLevels)); ?>
								</div>
								<div class="large-3 columns">
									&nbsp;
								</div>
							</div>
							<hr>
							<?= $this->Form->submit(__('List Published Courses'), array('name' => 'listPublishedCourses', 'id' => 'listPublishedCourses', 'div' => false, 'class' => 'tiny radius button bg-blue')); ?>
						</fieldset>
					</div>
					<hr>

					
					<?php
					if (!empty($publishedCourses)) { ?>
					<div id="show_published_courses_drop_down">
						<table class="fs14" cellpadding="0" cellspacing="0" class='table'>
							<tr>
								<td style="width:25%;" class="center">Published Courses</td>
								<td colspan="3">
									<div class="large-10 columns">
									<br>
									<?= $this->Form->input('published_course_id', array('style' => 'width: 90%;', 'id' => 'PublishedCourse', 'class' => 'fs14', 'label' => false, 'type' => 'select', 'options' => $publishedCourses, 'default' => $published_course_combo_id)); ?>
									</div>
								</td>
							</tr>
						</table>
						</div>
						<?php
					} ?>

					
					<div id="show_search_results">
						<!-- LOAD RESULTS DIV -->
						<div id="ExamResultDiv">


						</div>
						<!-- END LOAD RESULTS DIV -->
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?= $this->Form->end(); ?>

<script>
	$('#listPublishedCourses').click(function(event) {
		$('#listPublishedCourses').val('Looking for Published Courses...');
		$("#show_published_courses_drop_down").hide();
		if ($('#show_search_results').length) {
			$("#show_search_results").hide();
		}

		if ($('#ExamResultDiv').length) {
			$("#ExamResultDiv").empty();
			$("#ExamResultDiv").hide();
		}

		if ($('#SubmitID').length) {
			$("#SubmitID").attr('disabled', true);
		}

		if ($('#select-all').length) {
			$("#select-all").prop('checked', false);
		}

		$('input[type="checkbox"][name^="data[ExamResult]"]').each(function() {
            const namePatternSelected = /data\[ExamResult\]\[\d+\]\[gp\]/;
            if (namePatternSelected.test($(this).attr('name')) && $(this).is(':checked')) {
                $(this).prop('checked', false);
            }
        });

	});
</script>