<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-plus"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Add New Section'); ?></span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <?= $this->Form->create('Section', array('onSubmit' => 'return checkForm(this);')); ?>
                <div style="margin-top: -30px;"><hr></div>
                <blockquote>
                    <h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
                    <p style="text-align:justify;">
                        <span class="fs16"> Students can be involved in section management if and only if: </span>
                        <span>
                            <ol class="fs14 text-gray" style="font-weight: bold;">
                                <li>They have student ID/Number</li>
                                <li>They are admitted and </li>
                                <li>They are attached to a curriculum</li>
                            </ol>
                        </span>
                        <!-- <span class="fs14" style="font-style: italic; text-align: justify;">NB: Defining/adding a new year level for a department doesn't update year level dropdown list in different parts of the system unless there is a course defined on that year level in any of the curriculums under the department. If you didn't find a year level in year level dropdown list, check if there is at least one course in any curriculum is defined using the required year level.</span> -->
                    </p>
                </blockquote>
                <hr>
            </div>
            <div class="large-6 columns">
                <table cellpadding="0" cellspacing="0" class="table-borderless">
                    <tr>
                        <td>
                            <!-- Heading -->
                            <br>
                            <div class="large-12 columns">
                                <div class='font'>College: &nbsp;<?= $collegename; ?></div>
                                <?php
                                if (ROLE_COLLEGE != $role_id) { ?>
                                    <div class='font'>Department: &nbsp;<?= $departmentname; ?></div>
                                    <?php
                                } ?>
                                <hr>
                            </div>
                            <!-- End Heading -->

                            <div class="large-12 columns">
                                <div class="large-6 columns">
                                    <?= $this->Form->hidden('name'); ?>
                                    <?= $this->Form->input('academicyear', array('label' => 'Academic Year: ', 'type' => 'select', 'options' => $acyear_array_data, 'empty' => '[ Select Aacemic Year ]', 'required', 'id' => 'academicyear', 'onchange' => 'getSectionSummery()', 'default' => isset($thisacademicyear) ? $thisacademicyear : '', 'style' => 'width:100%;')); ?>
                                </div>
                                <div class="large-6 columns">
                                    <?php
                                    if ($role_id != ROLE_COLLEGE) { ?>
                                        <?= $this->Form->input('year_level_id', array('label' => 'Year Level: ', 'style' => 'width:100%;', /* 'empty' => '[ Select Year Level ]', */ 'required')); ?>
                                        <br />
                                        <?php
                                    } else {
                                        echo '&nbsp;';
                                    } ?>
                                </div>
                            </div>
                            <div class="large-12 columns">
                                <div class="large-6 columns">
                                    <?= $this->Form->input('program_id', array('label' => 'Program: ', 'id' => 'ProgramId', 'style' => 'width:100%;', 'onchange' => 'getCurriculumList();toggleFields("ProgramId");')); ?>
                                </div>
                                <div class="large-6 columns">
                                    <?= $this->Form->input('program_type_id', array('label' => 'Program Type: ', 'id' => 'ProgramTypeId', 'style' => 'width:100%;')); ?>
                                </div>
                            </div>
                            <?php
                            if ($role_id !=  ROLE_COLLEGE) { ?>
                                <div class="large-12 columns">
                                    <div class="large-6 columns">
                                        <?= $this->Form->input('prefix_section_name', array('label' => 'Prefix: ', 'options' => $prefix_section_name, 'id' => 'PrefixSectionName', 'style' => 'width:100%;')); ?>
                                    </div>
                                    <div class="large-6 columns">
                                        <?= $this->Form->input('additionalprefix_section_name', array('label' => 'Additional Prefix: ', 'pattern' => "[a-zA-Z]+", 'type' => 'text', 'maxlength' => "10", 'style' => 'width:100%;', 'id' => 'Additional Prefix Section Name')); ?>
                                    </div>
                                </div>
                                <?php
                            } ?>
                            <div class="large-12 columns">
                                <div class="large-6 columns">
                                    <?= $this->Form->input('fixed_section_name', array('after' => '', 'id' => 'FixedSectionName', 'readOnly', 'value' => (isset($this->data['Section']['fixed_section_name']) ? $this->data['Section']['fixed_section_name'] : $FixedSectionName), 'style' => 'width:100%;')); ?>
                                </div>
                                <div class="large-6 columns">
                                    <?= $this->Form->input('variable_section_name', array('label' => 'Variable Section Name', 'id' => 'variablesectionname', 'type' => 'select', 'options' => $variable_section_name_array, 'default' => (isset($this->data['Section']['variable_section_name']) ? $this->data['Section']['variable_section_name'] : 'Alphabet'), 'style' => 'width:100%;')); ?>
                                </div>
                            </div>
                            <div class="large-12 columns">
                                <div class="large-12 columns">
                                    <?= $this->Form->input('number_of_class', array('label' => 'Sections to create: ', 'default' => (isset($this->data['Section']['number_of_class']) ? $this->data['Section']['number_of_class'] : '1'), 'options' => $number_of_class, 'style' => 'width:30%;')); ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
                <!-- </td> -->
            </div>
            <?php
            if (count($programss) > 0) { ?>
                <div class="large-6 columns">
                    <table cellpadding="0" cellspacing="0" class="table-borderless">
                        <tr>
                            <td>
                                <table id="sectionNotAssignClass" cellpadding="0" cellspacing="0" class="table">
                                    <thead>
                                        <tr>
                                            <th colspan="<?= (count($programss) + 1); ?>" class="vcenter">Table: Summary of students who are not assigned to a section for <?= $thisacademicyear; ?> academic year.</th>
                                        </tr>
                                        <?php
                                        $count_program = count($programss);
                                        $count_program_type = count($programTypess); ?>
                                        <tr>
                                            <th class="center"><!-- ProgramType/ Program --></th>
                                            <?php
                                            if (!empty($programss)) {
                                                foreach ($programss as $kp => $vp) { ?>
                                                    <th class="center"><?= (isset($vp) ? $vp : ''); ?></th>
                                                    <?php
                                                }
                                            } ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        for ($i = 1; $i <= $count_program_type; $i++) {
                                            if (isset($programTypess[$i])) { ?>
                                                <tr>
                                                    <td class="vcenter"><?= (isset($programTypess[$i]) ? $programTypess[$i] : ''); ?></td>
                                                    <?php
                                                    for ($j = 1; $j <= $count_program; $j++) { 
                                                        if (isset($programss[$j])) {?>
                                                            <td class="center"><?= (isset($summary_data[$programss[$j]][$programTypess[$i]]) && $summary_data[$programss[$j]][$programTypess[$i]] > 0 ? $summary_data[$programss[$j]][$programTypess[$i]] : '--'); ?></td>
                                                            <?php
                                                        } else { ?>
                                                            <td class="center">--</td>
                                                            <?php
                                                        }
                                                    } ?>
                                                </tr>
                                                <?php
                                            } 
                                        } ?>

                                        <?php
                                        if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT && isset($curriculum_unattached_student_count) && $curriculum_unattached_student_count > 0) { ?>
                                            <tr>
                                                <td colspan="<?= (count($programs) + 1); ?>" class="vcenter"><?= ($curriculum_unattached_student_count > 1 ? $curriculum_unattached_student_count . ' students are' : $curriculum_unattached_student_count . ' student is'); ?>  not attached to any curriculum in your department from all programs. Thus, <?= ($curriculum_unattached_student_count > 1 ? ' these students' : ' this student'); ?> will not participate in any section assignment.</td>
                                            </tr>
                                            <?php
                                        } ?>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
                <?php
            } ?>

            <div class="large-12 columns">

                <?php
				if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) { ?>
					<hr>
					<!-- <blockquote>
						    <h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
						<span style="text-align:justify;" class="fs16 text-black"><b style="text-decoration: underline;" class="text-red"><i>Only recent curriculums which are active, locked and approved by the registrar appear here for attachent.</i></b> If you miss any of the curriculums in the options list here but available in "Curriculum > List Curriculums" page, Contact your respective registrar to approve, lock or activate the required curriculum to appear here for attachement.</span>
					</blockquote>
					<hr> -->

					<fieldset style="padding-bottom: 5px;padding-top: 5px;">
						<div class="large-2 columns">
							&nbsp;
						</div>
						<div class="large-8 columns">
						    <?= $this->Form->input('curriculum_id', array('id' => 'CurriculumID', 'label' => 'Section Curriculum: <span></span>', 'empty' => '[ Select Curriculum ]', 'required' => true, 'style' => 'width: 90%;', /* 'onchange' => 'getSectionSummery();' */)); ?>
						</div>
						<div class="large-2 columns">
							&nbsp;
						</div>
					</fieldset>
					<?php
				} ?>
                <hr style="margin-top: 5px;">
                <?= $this->Form->Submit('Create Section(s)', array('name' => 'submit', 'id' => 'SubmitID', 'div' => false, 'class' => 'tiny radius button bg-blue')); ?>
                <?= $this->Form->end(); ?>
            </div>
        </div>
    </div>
</div>

<script type='text/javascript'>
    var image = new Image();
    image.src = '/img/busy.gif';
    //$("#runautoplacementbutton").attr('disabled', true);
    //Get placement setting summery

    function getSectionSummery() {
        //serialize form data
        var summery = $("#academicyear").val();
        var exploded = summery.split('/');
        var academicYear = exploded[0] + '-' + exploded[1];

        $("#academicyear").attr('disabled', true);
        $("#sectionNotAssignClass").empty().html('<img src="/img/busy.gif" class="displayed" >');
        //get form action
        var formUrl = '/sections/un_assigned_summeries/' + academicYear;

        $.ajax({
            type: 'get',
            url: formUrl,
            data: summery,
            success: function(data, textStatus, xhr) {
                $("#academicyear").attr('disabled', false);
                $("#sectionNotAssignClass").empty();
                $("#sectionNotAssignClass").append(data);
                // $("#FixedSectionName").val(data.FixedSectionName);
            },
            error: function(xhr, textStatus, error) {
                alert(textStatus);
            }
        });
        return false;
    }

    function getCurriculumList() {
        var pid = $("#ProgramId").val();
        $("#CurriculumID").attr('disabled', true);
		$("#CurriculumID").empty();
		
		//get form action
		var formUrl = '/curriculums/get_curriculums_based_on_program_combo/' + pid;
		$.ajax({
			type: 'get',
			url: formUrl,
			data: pid,
			success: function(data,textStatus,xhr){
			    $("#CurriculumID").attr('disabled', false);
				$("#CurriculumID").empty();
				$("#CurriculumID").append(data);
			},
			error: function(xhr,textStatus,error){
				alert(textStatus);
			}
		});
		
		return false;
    }

    /* $(document).ready(function() {
        $("#ProgramId").change(
            function() {
                //alert($(this).text());
                //$("#PrefixSectionName").val();
            }
        );
    }); */

    $(document).ready(function() {
        $("#FixedSectionName").val("<?= $FixedSectionName; ?>");
    });

    //$('#selectedProgram').html($("#ProgramId option:selected").text());

    function toggleFields(id) {
		//$('#selectedProgram').html($("#ProgramId option:selected").text());
		if ($("#ProgramId").val() == 1) {
			$("#PrefixSectionName").val('UG');
		} else if ($("#ProgramId").val() == 2) {
			$("#PrefixSectionName").val('PG');
		} else if ($("#ProgramId").val() == 3) {
			$("#PrefixSectionName").val('PhD');
		} else if ($("#ProgramId").val() == 4) {
			$("#PrefixSectionName").val('PGDT');
		} else if ($("#ProgramId").val() == 5) {
			$("#PrefixSectionName").val('REM');
		}
	}

    var form_being_submitted = false; /* global variable */

	var checkForm = function(form) {

		if (form.academicyear.value == '') { 
			form.academicyear.focus();
			return false;
		}
        
		if (form_being_submitted) {
			alert("Creating section(s), please wait a moment...");
			form.SubmitID.disabled = true;
			return false;
		}

		form.SubmitID.value = 'Creating Section(s)...';
		form_being_submitted = true;
		return true; /* submit form */
	};

	// prevent possible form resubmission of a form 
	// and disable default JS form resubmit warning  dialog  caused by pressing browser back button or reload or refresh button

	if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>

