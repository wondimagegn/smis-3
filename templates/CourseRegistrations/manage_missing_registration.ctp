<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-check" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Student Missing Registration and Wrong NG Management '); ?></span>
		</div>

		<a class="close-reveal-modal">&#215;</a>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">

				<div style="margin-top: -25px;">
					<hr>
					<blockquote>
						<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
						<span style="text-align:justify;" class="fs14 text-gray">This tool will help you to manage missing registration <b style="text-decoration: underline;"><i>due to prerequisite and cancel wrong NG grade</i></b>. The system will retrieve students registration based on current active section and list the courses. <b> NG grades without any assesment data will be permanently deleted along with the associated registration data.</b> <?= (DELETE_ASSESMENT_AND_ASSOCIATED_RECORDS_ON_NG_CANCELATION == 1 ? '<br> <b style="text-decoration: underline; color: red;"><i>WARNING: This Server is set to delete all assesment data, associated grades, course registrations, course adds while cancelling NG.</i></b>' : '<br> <b style="text-decoration: underline;"><i class="on-process">If there are NG grades that have assesment data, These NG grades need Manual NG to F conversion.</i></b>'); ?> </span>
					</blockquote>
					<hr>
				</div>

				<table cellspacing="0" cellpadding="2" class="table">
					<tr>
						<td style="width: 2%;">&nbsp;</td>
						<td>
							<div class="row">
								<div class="large-4 columns" style="margin-top: 10px;">
									<?= $this->Form->input('acadamic_year', array('class' => 'AYS', 'id' => 'AcadamicYear', 'label' => 'Academic Year: ', 'style' => 'width:90%', 'type' => 'select', 'options' => $academicYearList, /* 'empty' => '[ Select ACY ]',  */'onchange' => 'updateCourseListOnChangeofOtherField()')); ?>
								</div>
								<div class="large-4 columns" style="margin-top: 10px;">
									<?= $this->Form->input('semester', array('class' => 'AYS', 'id' => 'Semester', 'label' => 'Semester:', 'style' => 'width:90%', 'type' => 'select', 'options' => Configure::read('semesters'), 'empty' => '[ Select Semester ]', 'onchange' => 'updateCourseListOnChangeofOtherField()')); ?>
									<?= $this->Form->input('student_id', array('id' => 'StudentId', 'type' => 'hidden', 'value' => $studentID)); ?>
								</div>
								<div class="large-4 columns" style="margin-top: 10px;">
									&nbsp;
								</div>
							</div>
						</td>
					</tr>
				</table>

			</div>

			<!-- AJAX LOADING -->

			<div class="large-12 columns" id="ListPublishCourse">

			</div>

			<!-- END AJAX LOADING -->

		</div>
	</div>
</div>

<script>
	function updateCourseListOnChangeofOtherField() {
		$("#ListPublishCourse").empty();
		
		var formData = '';
		var AcadamicYearStr = $("#AcadamicYear").val();
		var AcadamicYear = AcadamicYearStr.replace('/', '-');

		var Semester = $("#Semester").val();
		var StudentId = $("#StudentId").val();

		if ((typeof AcadamicYear != "undefined" && typeof Semester != "undefined" && typeof StudentId != "undefined") || (AcadamicYear != '' && Semester != '' && StudentId != '')) {
			formData = AcadamicYear + '~' + Semester + '~' + StudentId;
		} else {
			return false;
		}

		//$("#AcadamicYear").attr('disabled', true);
		//get form action
		if (Semester != '') {

			$("#ListPublishCourse").empty();
			$("#ListPublishCourse").append('Loading ...');

			var formUrl = '/courseRegistrations/getIndividualRegistration/' + formData;
			$.ajax({
				type: 'get',
				url: formUrl,
				data: formData,
				success: function(data, textStatus, xhr) {
					$("#AcadamicYear").attr('disabled', false);
					$("#Semester").attr('disabled', false);
					$("#ListPublishCourse").empty();
					$("#ListPublishCourse").append(data);
				},
				error: function(xhr, textStatus, error) {
					// alert(textStatus);
				}
			});
			return false;
		}
	}
</script>