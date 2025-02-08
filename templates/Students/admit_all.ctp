<div class="box">
    <div class="box-header bg-transparent">
		<div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
			<span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Batch Admit Students'); ?></span>
		</div>
	</div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <div style="margin-top: -20px;">

                    <?= $this->Form->create('Student'); ?>
                    
                    <?php
                    if (!isset($admitsearch) || isset($this->request->data['Search']) || 1) { ?>
                        <?php //echo $this->Form->create('Student'); ?>
                        <div style="margin-top: -30px;">
                            <hr>
                            <blockquote>
                                <h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
                                <span style="text-align:justify;" class="fs14 text-gray">Admit selected students at once. <b style="text-decoration: underline;"><i>Please don't forget to record and maintain each students record after batch admission</i></b>.</span>
                            </blockquote>
                            <hr>
                            <fieldset style="padding-bottom: 0px;padding-top: 15px;">
                                <legend>&nbsp;&nbsp; Search Filter &nbsp;&nbsp;</legend>
                                <div class="row">
                                    <div class="large-4 columns">
                                        <?= $this->Form->input('Search.academicyear', array('id' => 'academicyear', 'style' => 'width:90%;', 'label' => 'Academic Year: ', 'type' => 'select', 'options' => $acyear_array_data, 'empty' => "[ Select Academic Year ]", 'default' => isset($defaultacademicyear) ? $defaultacademicyear : '')); ?>
                                    </div>
                                    <div class="large-4 columns">
                                        <?= $this->Form->input('Search.program_id', array('style' => 'width:90%;', 'label' => 'Program: '/* , 'empty' => "[ Select Program ]" */)); ?>
                                    </div>
                                    <div class="large-4 columns">
                                        <?= $this->Form->input('Search.program_type_id', array('style' => 'width:90%;', 'label' => 'Program Type: ', /* 'empty' => "[ Select Program Type ]" */)); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="large-4 columns">
                                        <?php
                                        if (!empty($college_level)) {
                                            echo $this->Form->input('Search.college_id', array('style' => 'width:90%;', 'label' => 'College: ', 'empty' => "[ Select College ]", 'required' => 'required'));
                                        } else if (!empty($department_level)) {
                                            echo $this->Form->input('Search.department_id', array('style' => 'width:90%;', 'label' => 'Department: ', 'empty' => "[ Select Department ]", /* 'required' => 'required' */));
                                        } ?>
                                    </div>
                                    <div class="large-4 columns">
                                        <?= $this->Form->input('Search.name', array('style' => 'width:90%;', 'label' => 'Student Name: ', 'type' => 'text')); ?>
                                    </div>
                                    <div class="large-4 columns">
                                        <?= $this->Form->input('Search.limit', array('style' => 'width:90%;', 'label' => 'Limit: ', 'type' => 'number', 'min' => '0',  'max' => '2000', 'step' => '100')); ?>
                                    </div>
                                </div>
                                <hr>
                                <?= $this->Form->Submit('Search', array('div' => false, 'name' => 'getacceptedstudent',  'class' => 'tiny radius button bg-blue')); ?>
                                <?php //echo $this->Form->end(); ?>
                            </fieldset>
                        </div>
                        <?php 
                    } ?>
                </div>

                <?php
                if (!empty($acceptedStudents)) { ?>

                    <?php //echo $this->Form->create('Student', array('onSubmit' => 'return checkForm(this);')); ?>
                    <hr>
                   
                    <h6 id="validation-message_non_selected" class="text-red fs14"></h6>
                    <br>

                    <div style="overflow-x:auto;">
                        <table cellpadding="0" cellspacing="0" class="table">
                            <thead>
                                <tr>
                                    <td colspan=11><h6 class="fs14 text-gray">Select Students you want to batch admit</h6></th>
                                </tr>
                                <tr>
                                    <td class="center" style="width: 5%;"><?= $this->Form->checkbox("SelectAll", array('id' => 'select-all', 'checked' => '')); ?> </td>
                                    <td class="center" style="width: 3%;">#</td>
                                    <td class="vcenter">Full Name</td>
                                    <td class="center">Sex</td>
                                    <td class="center">Student ID</td>
                                    <td class="center">EHEECE</td>
                                    <td class="center">Department</td>
                                    <td class="center">ACY</td>
                                </tr>
                                <?php
                                if (isset($curriculums) && !empty($curriculums)) {  ?>
                                    <tr>
                                        <td colspan="8"><?= $this->Form->input('Curriculum.curriculum_id', array('empty' => '[ Select Curriculum ]', 'required' => true, 'label' => '<h5>Please attach the students to a curricula.</h5>')); ?></td>
                                    </tr>
                                    <?php
                                } ?>
                            </thead>
                            <tbody>
                                <?php
                                $serial_number = 1;
                                foreach ($acceptedStudents as $acceptedStudent) { ?>
                                    <tr>
                                        <td class="center"><?= $this->Form->checkbox('AcceptedStudent.approve.' . $acceptedStudent['AcceptedStudent']['id'], array('class' => 'checkbox1')); ?></td>
                                        <td class="center"><?= $serial_number++; ?></td>
                                        <td class="vcenter"><?= $acceptedStudent['AcceptedStudent']['full_name']; ?></td>
                                        <td class="center"><?= (strcasecmp(trim($acceptedStudent['AcceptedStudent']['sex']), 'male') == 0 ? 'M' : (strcasecmp(trim($acceptedStudent['AcceptedStudent']['sex']), 'female') == 0 ? 'F' : '')); ?></td>
                                        <td class="center"><?= $acceptedStudent['AcceptedStudent']['studentnumber']; ?></td>
                                        <td class="center"><?= $acceptedStudent['AcceptedStudent']['EHEECE_total_results']; ?></td>
                                        <td class="center"><?= (isset($acceptedStudent['Department']['name']) && !empty($acceptedStudent['Department']['name']) ? $acceptedStudent['Department']['name'] : ($acceptedStudent['AcceptedStudent']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial Program' : 'Pre/Freshman')); ?></td>
                                        <td class="center"><?= $acceptedStudent['AcceptedStudent']['academicyear']; ?></td>
                                    </tr>
                                    <?php
                                } ?>
                            </tbody>
                        </table>
                    </div>
                    <hr>

                    <?= $this->Form->Submit('Admit Selected Students', array('div' => false, 'name' => 'admit', 'id' => 'admitAll', 'class' => 'tiny radius button bg-blue')); ?>
                    
                    <?php
                } ?>

                <?= $this->Form->end(); ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    
    var form_being_submitted = false; 

    const validationMessageNonSelected = document.getElementById('validation-message_non_selected');


    $('#admitAll').click(function() {
		
		var checkboxes = document.querySelectorAll('input[type="checkbox"]');
		var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);

		if (!checkedOne) {
            alert('At least one student must be selected to admit!');
			validationMessageNonSelected.innerHTML = 'At least one student must be selected to admit!';
			return false;
		}

		if (form_being_submitted) {
			alert("Admitting Students, please wait a moment...");
			$('#admitAll').prop('disabled', true);
			return false;
		}

		$('#admitAll').val('Admitting Students...');
        form_being_submitted = true;
        return true;

	});

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>