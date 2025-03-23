<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Prepare the students for auto placement.'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">

				<?= $this->Form->create('PlacementSetting', array('onSubmit' => 'return checkForm(this);')); ?>

				<div style="margin-top: -30px;">
					<fieldset style="padding-bottom: 5px;">
						<div class="large-3 columns">
							<?= $this->Form->input('PlacementSetting.academic_year', array('id' => 'AcademicYear', 'label' => 'Academic Year: ', 'style' => 'width:150px', 'type' => 'select', 'options' => $availableAcademicYears, 'default' => (isset($this->request->data['PlacementSetting']['academic_year']) ? $this->request->data['PlacementSetting']['academic_year'] : (isset($latestACYRoundAppliedFor['academic_year']) ? $latestACYRoundAppliedFor['academic_year'] : '')))); ?>
						</div>
						<div class="large-3 columns">
							<?= $this->Form->input('PlacementSetting.round', array('onchange' => 'appliedFor()', 'id' => 'PlacementRound', 'label' => 'Placement Round: ', 'style' => 'width:150px', 'type' => 'select', 'options' => Configure::read('placement_rounds'),  'default' => (isset($this->request->data['PlacementSetting']['round']) ? $this->request->data['PlacementSetting']['round'] : (isset($latestACYRoundAppliedFor) ? $latestACYRoundAppliedFor['round'] : '')))); ?>
						</div>
						<div class="large-3 columns">
							<?= $this->Form->input('PlacementSetting.program_id', array('id' => 'ProgramId', 'label' => 'Program: ', 'style' => 'width:90%;', 'type' => 'select', 'options' => $programs)); ?>
						</div>
						<div class="large-3 columns">
							<?= $this->Form->input('PlacementSetting.program_type_id', array( 'id' => 'ProgramTypeId', 'label' => 'Program Type: ', 'style' => 'width:90%;', 'type' => 'select', 'options' => $programTypes)); ?>
						</div>
						<div class="large-6 columns">
							<?= $this->Form->input('PlacementSetting.applied_for', array('options' => $appliedForList, /* 'options' => $allUnits, */ 'id' => 'AppliedFor', 'type' => 'select', 'label' => 'Applied for Students in: ', 'empty' => '[ Select ]', 'style' => 'width:90%;', 'onchange' => 'appliedFor()')); ?>
						</div>
						<div class="large-3 columns">
							<?= $this->Form->input('PlacementSetting.limit', array('id' => 'Limit', 'label' => 'Limit: ', 'style' => 'width:40%;', /* 'style' => 'width:80px;float:left;', */ 'type' => 'number', 'min'=>'50',  'max'=>'500', 'value' => (isset($selectedLimit) ? $selectedLimit : ''), 'step' => '50')); ?>
						</div>
						<div class="large-3 columns">
							<p style="margin-top: 15px;">
								<?= $this->Form->input('PlacementSetting.include', array('id' => 'Include', 'label' => 'Include Previously Prepared', 'onclick' => "refreshDiv(this)", 'type' => 'checkbox')); ?>
								<?= $this->Form->input('PlacementSetting.with_entrance', array('id' => 'Include', 'label' => 'Who Took Entrance', 'onclick' => "refreshDiv(this)", 'type' => 'checkbox')); ?>
								<?php // $this->Form->input('PlacementSetting.only_with_status', array('id' => 'withStaus', 'label' => 'Only with Status', 'onclick' => "refreshDiv(this)", 'type' => 'checkbox', 'checked')); ?>
							</p>
						</div>
					</fieldset>
				</div>

				<!-- AJAX LOAD DATA -->

				<div id="ExclusionInclusion">

				</div>

				<!-- END AJAX LOAD DATA -->

				<?= $this->Form->end(); ?>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	function appliedFor() {

		var appliedFor = document.getElementById("AppliedFor");
		var  appliedForValue = appliedFor.options[appliedFor.selectedIndex].value;

		var academicYear = document.getElementById("AcademicYear");
		var  academicYearValue = academicYear.options[academicYear.selectedIndex].value;

		var placementRound = document.getElementById("PlacementRound");
		var  placementRoundValue = placementRound.options[placementRound.selectedIndex].value;

		//alert(academicYearValue);
		if (typeof appliedForValue != 'undefined' && appliedForValue != '' && typeof academicYearValue != 'undefined' && academicYearValue != '' && typeof placementRoundValue != 'undefined' && placementRoundValue != '') {
			$("#ExclusionInclusion").empty();
			$("#ExclusionInclusion").append('<p>Loading ...</p>');
			//get form action
			var formUrl = '/PlacementSettings/get_preference_applied_student';
			$.ajax({
				type: 'POST',
				url: formUrl,
				data: $('form').serialize(),
				success: function(data, textStatus, xhr) {
					$("#ExclusionInclusion").empty();
					$("#ExclusionInclusion").append(data);
					$("#AppliedFor").attr('disabled', false);
				},
				error: function(xhr, textStatus, error) {
					alert(textStatus);
				}
			});
			return false;
		} else {
			window.location.replace("/placementSettings/prepare/");
		}
	
	}

	function refreshDiv(chkPassport) {
		appliedFor();
	}

	$(document).ready(function() {
	
		var appliedFor = document.getElementById("AppliedFor");
		//var appliedForText = appliedFor.options[appliedFor.selectedIndex].innerText;
		var  appliedForValue = appliedFor.options[appliedFor.selectedIndex].value;

		var academicYear = document.getElementById("AcademicYear");
		//var academicYearText = academicYear.options[academicYear.selectedIndex].innerText;
		var  academicYearValue = academicYear.options[academicYear.selectedIndex].value;

		var placementRound = document.getElementById("PlacementRound");
		//var placementRoundText = placementRound.options[placementRound.selectedIndex].innerText;
		var  placementRoundValue = placementRound.options[placementRound.selectedIndex].value;

		//alert(academicYearValue);

		if (typeof appliedForValue != 'undefined' && appliedForValue != '' && typeof academicYearValue != 'undefined' && academicYearValue != '' && typeof placementRoundValue != 'undefined' && placementRoundValue != '') {
			$("#ExclusionInclusion").empty();
			$("#ExclusionInclusion").append('<p>Loading ...</p>');
			//get form action
			var formUrl = '/PlacementSettings/get_preference_applied_student';
			$.ajax({
				type: 'POST',
				url: formUrl,
				data: $('form').serialize(),
				success: function(data, textStatus, xhr) {
					$("#ExclusionInclusion").empty();
					$("#ExclusionInclusion").append(data);
					$("#AppliedFor").attr('disabled', false);
				},
				error: function(xhr, textStatus, error) {
					alert(textStatus);
				}
			});
			return false;
		}
	});

	
	/* $(document).ready(function() {
		//Students list
		$("#AppliedFor").change(function() {
			$("#ExclusionInclusion").empty();
			$("#ExclusionInclusion").append('<p>Loading ...</p>');
			//get form action
			var formUrl = '/PlacementSettings/get_preference_applied_student';
			$.ajax({
				type: 'POST',
				url: formUrl,
				data: $('form').serialize(),
				success: function(data, textStatus, xhr) {
					$("#ExclusionInclusion").empty();
					$("#ExclusionInclusion").append(data);
					$("#AppliedFor").attr('disabled', false);
				},
				error: function(xhr, textStatus, error) {
					alert(textStatus);
				}
			});
			return false;
		});
	}); */
</script>