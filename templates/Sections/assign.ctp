<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-plus"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Section Assignment Only for Freshman Students'); ?></span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <div style="margin-top: -30px;"><hr></div>
                <?= $this->Form->create('Section', array('id' => 'AssignmentForm')); ?>
                <blockquote>
                    <h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
                    <p style="text-align:justify;">
                        <span class="fs16"> Students can be involved in section management if and only if: </span>
                        <span>
                            <ol class="fs14 text-gray" style="font-weight: bold;">
                                <li>They have student ID/Number</li>
                                <li>They are admitted and </li>
                                <!-- <li>They are attached to a curriculum</li> -->
                            </ol>
                        </span>
                    </p>
                </blockquote>
                <hr>
            </div>

            <div class="large-6 columns">
                <table cellpadding="0" cellspacing="0" class="table">
                    <tr>
                        <th style="border-bottom-width: 0; border-bottom-style: none;">
                            <span class="text-gray fs13">
                                <br style="line-height: 0.5;">
                                College: &nbsp;<?= $collegename; ?>
                                <br style="line-height: 0.5;">
                                <?php
                                if (ROLE_COLLEGE != $role_id) { ?>
                                    Department: &nbsp;<?= $departmentname; ?>
                                    <?php
                                } ?>
                            </span>
                            <hr>
                        </th>
                    </tr>
                    <tr>
                        <td style="background-color: white;">
                            <div class="large-12 columns">
                                <?= $this->Form->input('Section.academicyearSearch', array('id' => 'academicyearSearch', 'label' => 'Academic Year: ', 'type' => 'select', 'style' => 'width:60%', 'options' => $acyear_array_data, 'onchange' => 'getSectionSummery()', 'empty' => "-- Select --", 'selected' => isset($academicyear) ? $academicyear : '', 'style' => 'width:150px')); ?>
                            </div>
                            
                            <div class="large-6 columns">
                                <?= $this->Form->input('Section.program_id', array('empty' => "-- Select --", 'label' => 'Program: ', 'style' => 'width:150px')); ?>
                            </div>
                            <div class="large-6 columns">
                                <?= $this->Form->input('Section.program_type_id', array('empty' => "-- Select --", 'label' => 'Program Type: ', 'style' => 'width:150px')); ?>
                            </div>
                            <div class="large-6 columns">
                                <?php
                                if (ROLE_COLLEGE != $role_id) { ?>
                                    <?= $this->Form->input('Section.year_level_id', array('readonly' => true, 'label' => 'Year Level: ', 'style' => 'width:150px')); ?>
                                    <?php
                                } ?>
                            </div>
                            <div class="large-6 columns">
                                <?= $this->Form->input('assignment_type', array('id' => 'assignmenttype', 'type' => 'select', 'options' => $assignment_type_array, 'style' => 'width:60%', 'label' => 'Assignment Type: ', 'empty' => "-- Select --", 'style' => 'width:150px')); ?>
                            </div>
                        </td>
                    </tr>
                </table>
                <hr>
                <?= $this->Form->Submit('Continue', array('name' => 'search', 'div' => false, 'id' => 'continueAssignment', 'class' => 'tiny radius button bg-blue')) ?>
            </div>

            <div class="large-6 columns">
                <!-- <div class="fs15">Table: Summary of students who are not assigned to section</div> -->
                <table id="sectionNotAssignClass" cellpadding="0" cellspacing="0" class="table">
                    <thead>
                        <tr>
                            <td style="border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85);" colspan="<?= (count($programs)+1); ?>">
                                <span class="text-gray">
                                <br style="line-height: 0.5;"> 
                                Table: Summary of students who are not assign to section
                                </span>
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $count_program = count($programs);
                        $count_program_type = count($programTypes); ?>
                        <tr>
                            <th><!-- ProgramType/ Program --></th>
                            <?php
                            if (!empty($programs)) {
                                foreach ($programs as $kp => $vp) { ?>
                                    <th class="center"><?= $vp; ?></th>
                                    <?php
                                }
                            } ?>
                        </tr>
                        <?php
                        if (!empty($programTypes)) {
                            for ($i = 1; $i <= $count_program_type; $i++) { ?>
                                <tr>
                                    <td class="vcenter"><?= $programTypes[$i]; ?></td>
                                    <?php
                                    for ($j = 1; $j <= $count_program; $j++) { ?>
                                        <td class="center"><?= (isset($programs[$j]) && isset($programTypes[$i]) && isset($summary_data[$programs[$j]][$programTypes[$i]])  && !empty($summary_data[$programs[$j]][$programTypes[$i]]) ? $summary_data[$programs[$j]][$programTypes[$i]] : '--'); ?></td>
                                        <?php
                                    } ?>
                                </tr>
                                <?php
                            } 
                        } ?>
                    </tbody>
                </table>

                <?php
                if (isset($curriculum_unattached_student_count) && $curriculum_unattached_student_count > 0) { ?>
                    <?= $curriculum_unattached_student_count; ?> students did not attached to the department curriculum, So these students did not participate in any section assignment.
                    <?php
                } ?>

            </div>

            <div class="sections form" id="assignmentDiv">
                <div class="large-12 columns">
                    <?php
                    if ($section_less_total_students > 0) {
                        if (isset($sectionlessStudentCurriculum)) { ?>
                            <div class='info-box info-message'><span style="margin-right: 15px;"></span> The system notes that there is more than 1 curriculum taken by section unassigned students, So please select curriculum and press continue button.</div>
                            <table cellpadding="0" cellspacing="0" class="table">
                                <tr>
                                    <td><?= $this->Form->input('Curriculum', array('type' => 'select', 'options' => $sectionlessStudentCurriculumArray, 'empty' => "--Select Curriculum --")) ?></td>
                                </tr>
                                <tr>
                                    <td><?= $this->Form->Submit('Continue', array('name' => 'continue', 'div' => false)); ?></td>
                                </tr>
                            </table>
                            <?php
                        }

                        if (!empty($sections)) { ?>
                            <hr>
                            <fieldset>
                                <legend> &nbsp;  &nbsp;  &nbsp; <?= __('Assign students to the given section'); ?> &nbsp;  &nbsp;  &nbsp; </legend>
                                <table cellpadding="0" cellspacing="0" class="table">
                                    <tr>
                                        <td>
                                            <?= $this->Form->input('Section.academicyear', array('id' => 'academicyear', 'value' => $academicyear, 'readonly' => 'readonly', 'style' => 'width: 40%')); ?>
                                            <h6 class='fs16 text-gray'><?= $collegename ?></h6>
                                            <?php
                                            if (ROLE_COLLEGE != $role_id) { ?>
                                                <h6 class='fs16 text-gray'><?= $departmentname; ?> department</h6>
                                                <?php
                                            } ?>
                                            <div class="font">Total number of <?= $selected_program_name; ?> students who are not assigned to any section: <?= $section_less_total_students; ?></div>
                                        </td>
                                    </tr>
                                    <?php
                                    $section_list_name = array();
                                    foreach ($sections as $key => $value) {
                                        echo $this->Form->hidden('Section.' . $key . '.id', array('value' => $value['Section']['id']));
                                        if ($assignmenttype == "result") {
                                            $section_list_name[] = $value['Section']['name'] . ' (Currently hosted students: ' . $current_sections_occupation[$key] . ' Section students curriculum ' . $sections_curriculum_name[$key] . ')';
                                        } else { ?>
                                            <tr>
                                                <td><?= $value['Section']['name']; ?> (Current hosted students: <?= $current_sections_occupation[$key]; ?> Section students curriculum <?= $sections_curriculum_name[$key]; ?> ) <?= '' . $this->Form->input('Section.' . $key . '.number'); ?></td>
                                            </tr>
                                            <?php
                                        }
                                    } ?>
                                    <tr>
                                        <td class="auto-width">
                                            <?php
                                            if ($assignmenttype == "result") { ?>
                                                <?= $this->Form->input('Section.Sections', array('type' => 'select', 'multiple' => 'checkbox', 'div' => 'input select', 'options' => $section_list_name)); ?>
                                                <?php
                                            } ?>
                                        </td>
                                    </tr>
                                </table>
                            </fieldset>
                            <hr>
                            <?= $this->Form->Submit('Assign to Section', array('div' => false, 'name' => 'assign', 'class' => 'tiny radius button bg-blue')); ?>
                            <?= $this->Form->end(); ?>
                            <?php
                        } else if (empty($sections) && !($isbeforesearch)) { ?>
                            <div class="large-12 columns">
                                <div class='info-box info-message'><span style="margin-right: 15px;"></span> No section is found with these search criteria</div>
                            </div>
                            <?php
                        }
                    } else if (($section_less_total_students <= 0) && !($isbeforesearch)) { ?>
                        <div class="large-12 columns">
                            <div class='info-box info-message'><span style="margin-right: 15px;"></span>There is no student who is not assigned to a section in the search criteria. </div>
                        </div>
                        <?php
                    } ?>
                </div>

            </div>
        </div>
    </div>
</div>

<script type='text/javascript'>
    var image = new Image();
    image.src = '/img/busy.gif';
    //$("#runautoplacementbutton").attr('disabled', true);
    //Get placement setting summery  continueAssignment
    function getSectionSummery() {
        //serialize form data
        var summery = $("#academicyearSearch").val();
        var exploded = summery.split('/');
        var academicYear = exploded[0] + '-' + exploded[1];

        $("#academicyearSearch").attr('disabled', true);
        $("#sectionNotAssignClass").empty().html('<img src="/img/busy.gif" class="displayed" >');
        //get form action
        var formUrl = '/sections/un_assigned_summeries/' + academicYear;
        $.ajax({
            type: 'get',
            url: formUrl,
            data: summery,
            success: function(data, textStatus, xhr) {
                $("#academicyearSearch").attr('disabled', false);
                $("#sectionNotAssignClass").empty();
                $("#sectionNotAssignClass").append(data);
            },
            error: function(xhr, textStatus, error) {
                alert(textStatus);
            }
        });
        return false;
    }
    window.location.hash = '#assignmentDiv';
</script>