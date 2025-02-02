<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-calendar" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Define/Setup Academic Calendar'); ?></span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <?= $this->Form->create('AcademicCalendar', array('onSubmit' => 'return checkForm(this);')); ?>
                <div style="margin-top: -30px;">
					<hr>
                    <fieldset style="padding-bottom: 15px;padding-top: 15px;">
                        <!-- <legend>&nbsp;&nbsp; Search / Filter &nbsp;&nbsp;</legend> -->
                        <div class="row">
                            <div class="large-2 columns">
                            <?= $this->Form->input('academic_year', array('id' => 'academicYear', 'label' => 'Academic Year: ', 'style' => 'width:90%', 'type' => 'select', 'options' => $acyear_array_data, 'required', /* 'empty' => "[ Select ]",  */ 'default' => isset($defaultacademicyear) ? $defaultacademicyear : '')); ?>
                            </div>
							<div class="large-2 columns">
                                <?= $this->Form->input('semester', array('id' => 'semester', 'label' => 'Semester: ', 'style' => 'width:90%', 'options' => Configure::read('semesters'), 'required', 'empty' => '[ Select ]')); ?>
                            </div>
                            <div class="large-3 columns">
                                <?= $this->Form->input('program_id',  array('id' => 'programType', 'onchange' => 'getDepartmentsOnProgramChange()', 'label' => 'Program: ', 'style' => 'width:90%', 'required', 'empty' => '[ Select ]')); ?>
                            </div>
                            <div class="large-3 columns">
                                <?= $this->Form->input('program_type_id', array('id' => 'programTypeId', 'onchange' => 'getDepartmentsOnProgramChange()', 'label' => 'Program Type: ', 'style' => 'width:90%', 'required', 'empty' => '[ Select ]')); ?>
                            </div>
                            <div class="large-2 columns">
								<h6 id="validation-message_non_selected" class="text-red fs14"></h6>
                                <?= $this->Form->input('year_level_id', array('type' => 'select', 'id' => 'yearLevels', 'multiple' => 'checkbox')); ?>
                            </div>
                        </div>
                    </fieldset>
                </div>

				<h6 id="validation-message_non_selected_department" class="text-red fs14"></h6>

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
	function getDepartmentsOnProgramChange() {

		var academicyear = document.getElementById("academicYear");
		var  academicyearValue = academicyear.options[academicyear.selectedIndex].value;

		var semester = document.getElementById("semester");
		var semesterValue = semester.options[semester.selectedIndex].value;

		var programType = document.getElementById("programType");
		var  programTypeValue = programType.options[programType.selectedIndex].value; 
        
        var programTypeId = document.getElementById("programTypeId");
		var  programTypeIdValue = programTypeId.options[programTypeId.selectedIndex].value;

		//alert(programTypeIdValue);

		//if (typeof semesterValue != 'undefined' && semesterValue != '' && typeof programTypeIdValue != 'undefined' && programTypeIdValue != '') {
			$("#ExclusionInclusion").empty();
			$("#ExclusionInclusion").append('<p>Loading ...</p>');
			//get form action
			var formUrl = '/AcademicCalendars/get_departments_that_have_the_selected_program';
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
		/* } else {
			//window.location.replace("/AcademicCalendars/add/");
		} */
	
	}

	function refreshDiv(chkPassport) {
		getDepartmentsOnProgramChange();
	}

    $("input[type='checkbox']").change(function () { 
        getDepartmentsOnProgramChange();
    });
	
	const validationMessageNonSelectedYearLevel = document.getElementById('validation-message_non_selected');
	const validationMessageNonSelectedDepartment = document.getElementById('validation-message_non_selected_department');

	var form_being_submitted = false; 

    var checkForm = function(form) {
		
		var checkboxesYearLevels =  document.getElementsByName('data[AcademicCalendar][year_level_id][]'); //$("#yearLevels"); //document.querySelectorAll('input[type="checkbox"]');
		var checkedOneYearLevel = Array.prototype.slice.call(checkboxesYearLevels).some(x => x.checked);

		var checkboxesDepartments =  document.getElementsByName('data[AcademicCalendar][department_id][]');
		var checkedOneDepartment = Array.prototype.slice.call(checkboxesDepartments).some(x => x.checked);

		//alert(checkedOneYearLevel);
		//alert(checkedOneDepartment);

		if (!checkedOneYearLevel) {
			alert('At least one year level must be selected.');
			validationMessageNonSelectedYearLevel.innerHTML = 'Select Year Level';
			return false;
		}

		if (!checkedOneDepartment) {
			alert('At least one department must be selected.');
			validationMessageNonSelectedDepartment.innerHTML = 'Select at least one department';
			return false;
		}
	
		if (form_being_submitted) {
			alert("Setting Academic Calendar, please wait a moment...");
			form.setCalendar.disabled = true;
			return false;
		}

        form.setCalendar.value = 'Setting Academic Calendar...';
		form_being_submitted = true;
		return true; 
	};
</script>