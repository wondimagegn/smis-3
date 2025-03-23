<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-check-outline" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Add Preference Choice on-behalf of the student'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">
				<?= $this->Form->create('PlacementPreference'); ?>
				<div style="margin-top: -30px;">
					<fieldset style="padding-bottom: 10px;">
						<legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend>
						<div class="row">
							<div class="large-3 columns">
								<?= $this->Form->input('Search.academic_year', array('class' => 'AYS', 'id' => 'AcademicYear', 'label' => 'Academic Year: ', 'style' => 'width:90%', 'type' => 'select', 'onchange' => 'updateSection();',  'options' => $acyear_array_data, 'default' => isset($this->request->data['Search']['academic_year']) ? $this->request->data['Search']['academic_year'] : (isset($defaultacademicyear) ? $defaultacademicyear : ''))); ?>
							</div>
							<div class="large-3 columns">
								<?= $this->Form->input('Search.placement_round', array('class' => 'PlacementRound', 'id' => 'PlacementRound', 'label' => 'Placement Round:', 'style' => 'width:90%', 'type' => 'select', 'onchange' => 'updateSection();', 'options' => Configure::read('placement_rounds'), 'default' => (isset($this->request->data['Search']['placement_round']) ? $this->request->data['Search']['placement_round'] : (isset($latestACY) ? $latestACY['round'] : '')))); ?>
							</div>
							<div class="large-3 columns">
								<?= $this->Form->input('Search.program_id', array('class' => 'AYS', 'id' => 'ProgramId', 'label' => 'Program: ', 'style' => 'width:90%', 'type' => 'select', 'onchange' => "updateSection();", 'options' => $programs)); ?>
							</div>
							<div class="large-3 columns">
								<?= $this->Form->input('Search.program_type_id', array('class' => 'AYS', 'id' => 'ProgramTypeId', 'label' => 'Program Type: ', 'style' => 'width:90%', 'type' => 'select', 'onchange' => 'updateSection();', 'options' => $programTypes)); ?>
							</div>
						</div>
						<div class="row">
							<div class="large-6 columns">
								<?= $this->Form->input('Search.applied_for', array(/* 'options' => $allUnits, */ 'options' => $appliedForList, 'onchange' => 'updateSection();', 'id' => 'AppliedFor', 'type' => 'select', 'label' => 'Applied For Those Student In: ', 'empty' => '[ Select Applied Unit ]', 'style' => 'width:95%')); ?>
							</div>
							<div class="large-6 columns">
								<?php // $this->Form->input('Search.current_unit', array('options' => $currentUnits, 'id' => 'CurrentUnit', 'type' => 'select', 'label' =>' Current College/Department: ', 'onchange' => 'updateSection();', 'empty' => '[ Select Current Unit ]', 'style' => 'width:95%')); ?>
								&nbsp;
							</div>
						</div>
						<div class="row">
							<div class="large-6 columns">
								<?= $this->Form->input('Search.section_id', array('id' => 'Section', 'label' => 'Assigned Section:', 'style' => 'width:95%', 'type' => 'select', 'options' => $sections, 'empty' =>  '[ Select Section ]', 'onchange' => 'refreshDiv();', 'default' => $section_combo_id)); ?>
							</div>
							<div class="large-6 columns">
								<p style="margin-top: 15px;">
									<?= $this->Form->input('Search.include', array('class' => 'AYS', 'id' => 'Include', 'label' => 'Only Those Who Took Entrance', 'onclick' => "refreshDiv();", /* 'onclick' => "updateSection();refreshDiv();", */ 'type' => 'checkbox')); ?> <!-- <br> -->
									<?= $this->Form->input('Search.only_with_status', array('class' => 'AYS', 'id' => 'withStaus', 'label' => 'Only Students with Status', 'onclick' => "refreshDiv();", /* 'onclick' => "updateSection();refreshDiv();", */ 'type' => 'checkbox')); ?>
								</p>
							</div>
						</div>
					</fieldset>
				</div>
					
				<!-- AJAX LOAD DATA -->
				<div id="ExamResultDiv">

				</div>

				<!-- END AJAX LOAD DATA -->

				<?= $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">

	function updateSection() {
		$("#Section").empty();
		//$("#Section").append('<p>Loading ...</p>');
		var formUrl = '/PlacementEntranceExamResultEntries/get_selected_section';
		$.ajax({
			type: 'POST',
			url: formUrl,
			data: $('form').serialize(),
			success: function(response) {
				$("#Section").attr('disabled', false);
				$("#Section").empty();
				$("#Section").append(response);
			},
			error: function(xhr, textStatus, error) {
				alert(textStatus);
			}
		});
		return false;
	}

	function updateParticipant() {
		var formUrl = '/PlacementPreferences/get_selected_participant';
		$.ajax({
			type: 'POST',
			url: formUrl,
			data: $('form').serialize(),
			success: function(response) {
				$("#PlacementRoundParticipant").attr('disabled', false);
				$("#PlacementRoundParticipant").empty();
				$("#PlacementRoundParticipant").append(response);
			},
			error: function(xhr, textStatus, error) {
				alert(textStatus);
			}
		});
		return false;
	}

	function appliedFor() {
		$("#ExamResultDiv").empty();
		$("#ExamResultDiv").append('<p>Loading ...</p>');
		//get form action
		var formUrl = '/PlacementPreferences/get_selected_student';
		$.ajax({
			type: 'POST',
			url: formUrl,
			data: $('form').serialize(),
			success: function(data, textStatus, xhr) {
				$("#ExamResultDiv").empty();
				$("#ExamResultDiv").append(data);
				$("#CurrentUnit").attr('disabled', false);
				$("#Section").attr('disabled', false);
				$("#AppliedFor").attr('disabled', false);
			},
			error: function(xhr, textStatus, error) {
				alert(textStatus);
			}
		});

		return false;
	}

	function refreshDiv() {
		appliedFor();
	}
	
</script>