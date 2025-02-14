<div style="margin-top: -30px;">
	<hr>
	<div onclick="toggleViewFullId('ListPublishedCourse')">
		<?php
		
		if (isset($this->request->params['action']) && in_array($this->request->params['action'], array('manage_ng', 'cancel_ng_grade', 'manage_fx'))) {
			//debug('action detected = manage_ng');
			if (isset($previous_academicyear) && !empty($previous_academicyear)) {
				$defaultacademicyear = $previous_academicyear;
			}
			//debug($defaultacademicyear);
		}
		   
		if (!empty($publishedCourses)) {
			echo $this->Html->image('plus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
			<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt"> Display Filter</span>
			<?php
		} else {
			echo $this->Html->image('minus2.gif', array('id' => 'ListPublishedCourseImg')); ?>
			<span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt"> Hide Filter</span>
			<?php
		} ?>
	</div>

	<div id="ListPublishedCourse" style="display:<?= (!empty($publishedCourses) ? 'none' : 'display'); ?>">
		<fieldset style="padding-bottom: 0px;padding-top: 15px;">
			<!-- <legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend> -->
			<div class="row">
				<div class="large-3 columns">
					<?= $this->Form->input('acadamic_year', array('id' => 'AcadamicYear', 'label' => 'Acadamic Year: ', 'class' => 'fs14', 'style' => 'width:90%', 'type' => 'select', 'options' => $acyear_array_data, 'default' => (isset($academic_year_selected) ? $academic_year_selected : $defaultacademicyear))); ?>
				</div>
				<div class="large-3 columns">
					<?= $this->Form->input('semester', array('id' => 'Semester', 'class' => 'fs14', 'type' => 'select', 'style' => 'width:90%', 'label' => 'Semester: ', 'options' => Configure::read('semesters'), 'required', 'default' => (isset($semester_selected) ? $semester_selected : ''))); ?>
				</div>
				<div class="large-3 columns">
					<?= $this->Form->input('program_id', array('id' => 'Program', 'class' => 'fs14', 'label' => 'Program: ', 'style' => 'width:90%','type' => 'select', 'options' => $programs, 'required', 'default' => (isset($program_id) ? $program_id : ''))); ?>
				</div>
				<div class="large-3 columns">
					<?= $this->Form->input('program_type_id', array('id' => 'ProgramType', 'class' => 'fs14', 'label' => 'Program Type: ', 'style' => 'width:90%', 'type' => 'select', 'options' => $program_types, 'required', 'default' => (isset($program_type_id) ? $program_type_id : ''))); ?>
				</div>
			</div>
			<div class="row">
				<div class="large-6 columns">
					<?php
					if (!(isset($departments[0]) && $departments[0] == 0)) { ?>
						<?php //debug($departments); ?>
						<?= $this->Form->input('department_id', array('id' => 'DepartmentId', 'class' => 'fs14', 'label' => isset($only_pre_assigned) && $only_pre_assigned ? 'College: ' : 'Department: ', 'style' => 'width:95%', 'type' => 'select', 'options' => $departments, 'required', 'default' => (isset($department_id) ? $department_id : ''))); ?>
						<?php
					} ?>
				</div>
				<div class="large-6 columns">
					&nbsp;
				</div>
			</div>
			<hr>
			<?= $this->Form->submit(__((isset($search_button_label) && !empty($search_button_label) ? $search_button_label : 'List Published Courses')), array('name' => 'listPublishedCourses', 'id' => 'listPublishedCourses', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
		</fieldset>
	</div>
	<hr>
</div>

<?php
if (!empty($publishedCourses)) { ?>
	<div id="show_published_courses_drop_down">
		<table class="fs14" cellpadding="0" cellspacing="0" class='table'>
			<tr>
				<td style="width:25%;" class="center">Published Courses</td>
				<td colspan="3">
					<div class="large-10 columns">
					<br>
					<?= $this->Form->input('published_course_id', array('style' => 'width: 90%;',  'class' => 'fs14', 'id' => 'PublishedCourse', 'label' => false, 'type' => 'select', 'required', 'options' => $publishedCourses, 'default' => $published_course_combo_id)); ?>
					</div>
				</td>
			</tr>
		</table>
	</div>
	<?php
} ?>

<?php
$redirectUrl = '';
$additionalParams = false;
// Check if any additional parameters are present in the URL
if (isset($this->request->params['pass']) && !empty($this->request->params['pass'])) {
	debug($this->request->params['pass']);
	$additionalParams = true;
	$redirectUrl = '/'. $this->request->params['controller'] . '/' . $this->request->params['action'];
	debug($redirectUrl);
} ?>

<script>

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

	$('#listPublishedCourses').click(function() {

		$('#listPublishedCourses').val('Looking for Published Courses...');

		$('#PublishedCourse').val(0);

		$("#show_published_courses_drop_down").hide();

		if ($('#show_search_results').length) {
			$("#show_search_results").hide();
		}

		if ($('#select-all').length) {
			$("#select-all").prop('checked', false);
		}

		var additionalParams = <?= $additionalParams; ?>;
		//var additionalParams = <?php //echo json_encode($additionalParams); ?>;

		if (additionalParams) {
			var protocol = window.location.protocol;
			var hostname = window.location.hostname;
			var customPath = '<?= $redirectUrl; ?>';
			var redirectUrl = protocol + '//' + hostname + customPath;

			//alert(redirectUrl);
			window.location.href = redirectUrl;
		}
	});
</script>