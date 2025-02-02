<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-plus" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Add Year Levels'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<div class="large-12 columns">

				<div style="margin-top: -30px;"><hr></div>

				<blockquote>
					<h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
					<span style="text-align:justify;" class="fs14 text-gray">This tool will help you in Adding New or Extending existing Year Levels for your department. <strong>The maximum allowed year level is <?= MAXIMUM_YEAR_LEVELS_ALLOWED ; ?> years.</strong></span> 
					<br><br>
					<span class="fs14" style="font-style: italic; text-align: justify;">NB: Defining/adding a new year level for a department doesn't update year level dropdown list in different parts of the system unless there is a course defined on that year level in any of the curriculums under the department. If you didn't find a year level in year level drop down list, check if there is at least one course in any curriculum is defined using the required year level. Please also remember to logout and login back if you add or modify year levels.</span>
				</blockquote>

				<?= $this->Form->create('YearLevel', array('onSubmit' => 'return checkForm(this);')); ?>

				<fieldset>
					<!-- <legend>&nbsp;&nbsp; Year Level Add &nbsp;&nbsp;</legend> -->
					<div class="row align-items-center" style="margin-bottom:5px;">
						<div class="large-6 columns">
							<?= $this->Form->input('department_id', array('label' => 'Department: ', 'style' => 'width: 70%')); ?>
						</div>
						<div class="large-6 columns">
							<?= $this->Form->input('numberofyear', array('label' => 'Year Levels(Max):',  'style' => 'width: 30%', 'id'=> 'numberOfYears', 'type'=> 'number', 'min' => ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT && isset($already_created_year_levels_count) && $already_created_year_levels_count ? $already_created_year_levels_count : 2 ), 'max'=> MAXIMUM_YEAR_LEVELS_ALLOWED, 'value' => ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT && isset($already_created_year_levels_count) && $already_created_year_levels_count ? $already_created_year_levels_count : 2 ), 'step'=>'1')); ?>
						</div>
					</div>
					<hr>
					<?= $this->Form->end(array('label' => __('Submit'), 'id' => 'SubmitID', 'class' => 'tiny radius button bg-blue')); ?>
				</fieldset>
			</div>
		</div>
	</div>
</div>

<script>

	var form_being_submitted = false; /* global variable */

	var checkForm = function(form) {
	
		if (form_being_submitted) {
			alert("Adding/Updatting Year Levels, please wait a moment...");
			form.SubmitID.disabled = true;
			return false;
		}

		form.SubmitID.value = 'Adding/Updatting Year Levels...';
		form_being_submitted = true;
		return true; /* submit form */
	};

	// prevent possible form resubmission of a form 
	// and disable default JS form resubmit warning  dialog  caused by pressing browser back button or reload or refresh button

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>