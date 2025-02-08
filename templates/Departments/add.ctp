<div class="box">
	<div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-plus" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Add Department'); ?></span>
		</div>
	</div>
	<div class="box-body">
		<div class="row">
			<?= $this->Html->script('amharictyping'); ?>
			<?= $this->Form->create('Department', array('data-abide', 'onSubmit' => 'return checkForm(this);')); ?>
			<div class="large-12 columns" style="margin-top: -30px;">
				<hr>
			</div>
			<div class="large-12 columns">
				<div class="large-6 columns">
					<?php
					echo $this->Form->input('college_id', array('style' => 'width:90%'));
					echo $this->Form->input('name', array('style' => 'width:90%', 'placeholder' => 'Like: Computer Science', 'required', 'pattern' => '[a-zA-Z]+', 'label' => 'Name <small></small></label><small class="error" style="width:90%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">Department Name is required and must be a string.</small>'));
					echo $this->Form->input('shortname', array('style' => 'width:90%', 'placeholder' => 'Like: COMP', 'pattern' => 'alpha', 'label' => 'Short Name <small></small></label><small class="error" style="width:90%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">Department short name must be a single word.</small>'));
					echo $this->Form->input('phone', array('style' => 'width:90%','type' => 'tel', 'label' => 'Phone Office', 'id'=>'etPhone'));
					echo $this->Form->input('institution_code', array('style' => 'width:90%', 'placeholder' => 'Like: AMU-AMIT-COMP', 'pattern' => 'institution_code', 'label' => 'Institution Code <small></small></label><small class="error" style="width:90%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">Institution Code must be a single word, with Hyphen separated; Like AMU-AMIT-COMP</small>'));
					echo '<br>' . $this->Form->input('active', array('type'=>'checkbox', 'checked' =>'checked')); 
					echo '<br>' . $this->Form->input('allow_year_based_curriculums', array('type'=>'checkbox')); 
					echo '<br>';
					?>
				</div>
				<div class="large-6 columns">
					<?php
					echo $this->Form->input('amharic_name',array('style' => 'width:90%', 'id' => 'AmharicText', 'onkeypress' => "return AmharicPhoneticKeyPress(event,this);"));
					echo $this->Form->input('amharic_short_name', array('style' => 'width:90%', 'id' => 'AmharicText', 'onkeypress' => "return AmharicPhoneticKeyPress(event,this);"));
					echo $this->Form->input('description' , array('style' => 'width:90%')); 
					echo $this->Form->input('type', array('style' => 'width:90%', 'id' => 'departmentType', 'onchange' => 'updateDepartmentType()', 'options' => Configure::read('department_types'), 'default' => 'Department'));
					echo $this->Form->hidden('type_amharic', array('id' => 'departmentTypeAmharic', 'value' => (isset($this->data['Department']['type_amharic']) && !empty($this->data['Department']['type_amharic']) ?  $this->data['Department']['type_amharic'] : DEPARTMENT_TYPE_AMHARIC_DEPARTMENT)));
					echo $this->Form->input('moodle_category_id', array('id' => 'moodleCategoryId ', 'type' => 'number', 'min'=>'1', 'max'=>'1000', 'step'=>'1', 'class' => 'fs13', 'label' =>'Moodle Category ID: ', 'style' => 'width:25%'));
					?>
				</div>
			</div>
			<div class="large-12 columns">
				<hr>
				<?= $this->Form->end(array('label' => 'Add Department', 'id' => 'SubmitID', 'class' => 'tiny radius button bg-blue')); ?>
			</div>
		</div>
	</div>
</div>

<script>

	function updateDepartmentType() {
		var dept_amharic = "<?= DEPARTMENT_TYPE_AMHARIC_DEPARTMENT; ?>";
		var faculty_amharic = "<?= DEPARTMENT_TYPE_AMHARIC_FACULTY; ?>";
		var school_amharic = "<?= DEPARTMENT_TYPE_AMHARIC_SCHOOL; ?>";
		var department_type = $("#departmentType").val();

		if (department_type == 'Department') {
			$("#departmentTypeAmharic").val(dept_amharic);
		} else if (department_type == 'Faculty') {
			$("#departmentTypeAmharic").val(faculty_amharic);
		} else if (department_type == 'School') {
			$("#departmentTypeAmharic").val(school_amharic);
		} else {
			$("#departmentTypeAmharic").val('');
		}
	}

	var form_being_submitted = false; /* global variable */

	var checkForm = function(form) {
	
		if (form_being_submitted) {
			alert("Adding Department, please wait a moment...");
			form.SubmitID.disabled = true;
			return false;
		}

		form.SubmitID.value = 'Adding Department...';
		form_being_submitted = true;
		return true; /* submit form */
	};

	// prevent possible form resubmission of a form 
	// and disable default JS form resubmit warning  dialog  caused by pressing browser back button or reload or refresh button

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>