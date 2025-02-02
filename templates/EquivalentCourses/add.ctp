<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-plus"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Map Equivalent Courses'); ?></span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <?= $this->Form->create('EquivalentCourse', array('data-abide', 'onSubmit' => 'return checkForm(this);')); ?>

                <h5>Note: Only those curriculum which was approved by the registrar will be in the dropdown option</h5>
                <table cellpadding="0" cellspacing="0" class="table">
                    <thead>
                        <tr>
                            <th>Your Department Course</th>
                            <th>Other Department Course</th>
                        </tr>
                    </thead>
                    <tr>
                        <td>
                            <table cellpadding="0" cellspacing="0" class="table">
                                <tr>
                                    <td><?= $this->Form->input('curriculum_id', array('id' => 'curriculum_id_1', 'empty' => '--select curriculum--', 'onchange' => 'updateCourse(1)', 'style' => 'width:200px')); ?></td>
                                </tr>
                                <tr>
                                    <td><?= $this->Form->input('course_for_substitued_id', array('id' => 'course_id_1', 'empty' => '--select course--', 'label' => 'Course To be equivalent', 'style' => 'width:200px')); ?></td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <?= $this->Form->input('department_id', array('onchange' => 'updateSubCurriculum(2)', 'empty' => '--select department--', 'id' => "department_id_2", 'style' => 'width:200px')); ?>
                            <table cellpadding="0" cellspacing="0" class="table">
                                <tr>
                                    <td><?= $this->Form->input('other_curriculum_id', array('id' => 'curriculum_id_2', 'onchange' => 'updateCourse(2)', 'option' => $otherCurriculums, 'type' => 'select', 'empty' => ' --select curriculum -- ', 'style' => 'width:200px')); ?></td>
                                </tr>
                                <tr>
                                    <td><?= $this->Form->input('course_be_substitued_id', array('id' => 'course_id_2', 'style' => 'width:200px')) ?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <?php
                    /* echo '<tr><th width="30%">Your Department Course</th><th> Other Department Course </th></tr>';
                    echo '<tr><td>'.$this->Form->input('curriculum_id', array('id'=>'own_department_id','empty'=>'--select curriculum--')).'</td><td>'.$this->Form->input('department_id',array('id'=>'other_department_id','empty'=>'--select department--')).'</td></tr>';
                    echo '<tr><td>'.$this->Form->input('course_for_substitued_id', array('id'=>'course_for_substitued_id','empty'=>'--select course--','label'=>'Course To be equivalent')).'</td><td id="kk">&nbsp;</td></tr>';
                    echo '<td>'.$this->Form->input('other_curriculum_id',array('id'=>'other_curriculum_id','empty'=>'--select curriculum--')).'</td></tr>';
                    echo '<tr><td>&nbsp;</td><td>'.$this->Form->input('course_be_substitued_id', array('id'=>'course_be_substitued_id','empty'=>'--select course--','label'=>'Equivalent Course')).'</td></tr>'; */
                    ?>
                </table>

                <?= $this->Form->submit('Map', array('id' => 'equivalent_submit_button', 'div' => 'false', 'class' => 'tiny radius button bg-blue')); ?>
            </div>
        </div>
    </div>
</div>

<script type='text/javascript'>
    function updateSubCurriculum(id) {
        //serialize form data
        var formData = $("#department_id_" + id).val();
        
        $("#curriculum_id_" + id).empty();
        $("#curriculum_id_" + id).attr('disabled', true);
        $("#course_id_" + id).attr('disabled', true);
        $("#equivalent_submit_button").attr('disabled', true);
        
        //get form action
        var formUrl = '/curriculums/get_curriculum_combo/' + formData;

        $.ajax({
            type: 'get',
            url: formUrl,
            data: formData,
            success: function(data, textStatus, xhr ) {
                $("#curriculum_id_" + id).attr('disabled', false);
                $("#curriculum_id_" + id).empty();
                $("#curriculum_id_" + id).append(data);
                //Items list
                var subCat = $("#curriculum_id_" + id).val();
                $("#course_id_" + id).empty();
                //get form action
                var formUrl = '/curriculums/get_courses/' + subCat;
                $.ajax({
                    type: 'get',
                    url: formUrl,
                    data: subCat,
                    success: function(data, textStatus, xhr) {
                        $("#equivalent_submit_button").attr('disabled', false);
                        $("#course_id_" + id).attr('disabled', false);
                        $("#course_id_" + id).empty();
                        $("#course_id_" + id).append(data);
                    },
                    error: function(xhr, textStatus, error) {
                        alert(textStatus);
                    }
                });
                //End of items list
            },
            error: function(xhr, textStatus, error) {
                alert(textStatus);
            }
        });

        return false;
    }

    //update course combo
    function updateCourse(id) {
        //serialize form data
        var subCat = $("#curriculum_id_" + id).val();
        
        $("#course_id_" + id).attr('disabled', true);
        $("#equivalent_submit_button").attr('disabled', true);
        $("#course_id_" + id).empty();
        
        //get form action
        var formUrl = '/curriculums/get_courses/' + subCat;
        
        $.ajax({
            type: 'get',
            url: formUrl,
            data: subCat,
            success: function(data, textStatus, xhr) {
                $("#equivalent_submit_button").attr('disabled', false);
                $("#course_id_" + id).attr('disabled', false);
                $("#course_id_" + id).empty();
                $("#course_id_" + id).append(data);
            },
            error: function(xhr, textStatus, error) {
                alert(textStatus);
            }
        });

        return false;
    }

    var form_being_submitted = false; /* global variable */
    
	var checkForm = function(form) {
		if (form_being_submitted) {
			alert("Adding Course, please wait a moment...");
			form.SubmitID.disabled = true;
			return false;
		}
		form.addStudentToGraduateList.value = 'Adding Course...';
		form_being_submitted = true;
		return true; /* submit form */
	};

	// prevent possible form resubmission of a form 
	// and disable default JS form resubmit warning  dialog  caused by pressing browser back button or reload or refresh button

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>