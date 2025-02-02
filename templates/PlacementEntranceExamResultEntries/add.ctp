<?= $this->Form->create('PlacementEntranceExamResultEntry'); ?>
<script type="text/javascript">
	function updateSection() {
		$("#ExamResultDiv").empty();
		$("#ExamResultDiv").append('<p>Loading ...</p>');
		var formUrl = '/PlacementEntranceExamResultEntries/get_selected_section';
		$.ajax({
			type: 'POST',
			url: formUrl,
			data: $('form').serialize(),
			success: function (response) {
				$("#Section").attr('disabled', false);
				$("#Section").empty();
				$("#Section").append(response);
			},
			error: function (xhr, textStatus, error) {
				alert(textStatus);
			}
		});
		return false;
	}

	function updateParticipant() {
		var formUrl = '/PlacementEntranceExamResultEntries/get_selected_participant_exam';
		$.ajax({
			type: 'POST',
			url: formUrl,
			data: $('form').serialize(),
			success: function (response) {
				$("#PlacementRoundParticipant").attr('disabled', false);
				$("#PlacementRoundParticipant").empty();
				$("#PlacementRoundParticipant").append(response);
			},
			error: function (xhr, textStatus, error) {
				alert(textStatus);
			}
		});
		return false;
	}

	function updateExamTotal(obj, row) {
		var result = 0;
		var result = $('#result_' + row + '_1').val();

		if (result != "" && isNaN(result)) {
			return false;
		} else if (result != "" && parseFloat(result)) {
			$('#' + obj.id).focus();
			$('#' + obj.id).select();
			//return false;
		} else if (result != "" && parseFloat(result) < 0) {
			//alert("third=="+result);
			$('#' + obj.id).focus();
			$('#' + obj.id).select();
			return false;
		}

		var autoSaveResult = result;
		$.ajax({
			url: "/PlacementEntranceExamResultEntries/autoSaveResult",
			type: 'POST',
			data: $('form').serialize(),
			success: function (data) { }
		});

		return result;
	}

	function isNumeric(n) {
		return !isNaN(parseFloat(n)) && isFinite(n);
	}

	$(document).ready(function () {
		//Students list
		$("#Section").change(function () {
			var pc = $("#PlacementRoundParticipant").val();
			if (pc != '') {
				$("#ExamResultDiv").empty();
				$("#ExamResultDiv").append('<p>Loading ...</p>');
				//get form action
				var formUrl = '/PlacementEntranceExamResultEntries/get_selected_student';
				$.ajax({
					type: 'POST',
					url: formUrl,
					data: $('form').serialize(),
					success: function (data, textStatus, xhr) {
						$("#ExamResultDiv").empty();
						$("#ExamResultDiv").append(data);
						$("#CurrentUnit").attr('disabled', false);
						$("#Section").attr('disabled', false);
						$("#AppliedFor").attr('disabled', false);
					},
					error: function (xhr, textStatus, error) {
						alert(textStatus);
					}
				});
			}
			return false;
		});
	});
</script>

<div class="box">
	<div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Department Entrance Exam Entry For Student Preference Choice'); ?></span>
        </div>
    </div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<div class="examTypes form">

					<div style="margin-top: -30px;">
						<fieldset style="padding-bottom: 10px;">
							<legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend>
							<div class="row">
								<div class="large-3 columns">
									<?= $this->Form->input('Search.academic_year', array('class' => 'AYS', 'id' => 'AcademicYear', 'label' => 'Academic Year: ', 'style' => 'width:90%;', 'type' => 'select', 'options' => $acyear_array_data, 'default' => isset($this->request->data['Search']['academic_year']) ? $this->request->data['Search']['academic_year'] : (isset($defaultacademicyear) ? $defaultacademicyear : ''))); ?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('Search.placement_round', array('class' => 'PlacementRound', 'id' => 'PlacementRound', 'label' => 'Placement Round:', 'style' => 'width:90%;', 'type' => 'select', 'onchange' => 'updateParticipant();', 'options' => Configure::read('placement_rounds'))); ?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('Search.program_id', array('class' => 'AYS', 'id' => 'ProgramId', 'label' => 'Program: ', 'style' => 'width:90%;', 'type' => 'select', 'options' => $programs)); ?>
								</div>
								<div class="large-3 columns">
									<?= $this->Form->input('Search.program_type_id', array('class' => 'AYS', 'id' => 'ProgramTypeId', 'label' => 'Program Type: ', 'style' => 'width:90%;',  'type' => 'select', 'options' => $programTypes)); ?>
								</div>
							</div>
							<div class="row">
								<div class="large-6 columns">
									<?= $this->Form->input('Search.applied_for', array('options' => $allUnits, /* 'options' => $appliedForList, */ 'id' => 'AppliedFor', 'type' => 'select', 'label' => 'Applied For Those Student In: ', 'empty' => '[ Select Applied Unit ]', 'style' => 'width:95%;', 'onchange' => 'updateSection();updateParticipant();',  /* 'onchange' => 'updateParticipant();' */)); ?>
								</div>
								<div class="large-6 columns">
									<?= $this->Form->input('Search.current_unit', array('options' => $currentUnits, 'id' => 'CurrentUnit', 'type' => 'select', 'label' =>' Current College/Department: ', 'onchange' => 'updateSection();updateParticipant();',  'empty' => '[ Select Current Unit ]', 'style' => 'width:95%;')); ?>
								</div>
							</div>
							<div class="row">
								<div class="large-6 columns">
									<?= $this->Form->input('Search.placement_round_participant_id', array('id' => 'PlacementRoundParticipant', 'label' => 'Enrance Exam Result For: ', 'empty' => '[ Select Applied Unit ]', 'onchange' => 'updateSection();', 'style' => 'width:95%;', 'type' => 'select')); ?>
								</div>
								<div class="large-6 columns">
									<?= $this->Form->input('Search.section_id', array('id' => 'Section', 'label' => 'Assigned Section: ', 'style' => 'width:95%;', 'empty' => '[ Select Applied Unit ]', 'type' => 'select', 'options' => $sections, 'default' => $section_combo_id)); ?>
								</div>
							</div>
						</fieldset>
					</div>
					
					<!-- AJAX Loading -->
					<div id="ExamResultDiv">

					</div>

					<!-- End AJAX Loading -->

					<?= $this->Form->end(); ?>
				</div>
			</div>
		</div>
	</div>
</div>