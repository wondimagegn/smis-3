<script type="text/javascript">
    $(document).ready(function () {
        $("#publish_department_id").change(function () {
            var formData = $("#publish_department_id").val();
            var selected_program_id = $("#PublishedCourseProgramId").val();
            var remedial_program_id_selected = false;

            if (selected_program_id != '') {
                //alert(selected_program_id);
                if (selected_program_id == <?= PROGRAM_REMEDIAL; ?>) {
                    remedial_program_id_selected = true;
                }
            }

            $("#publish_curriculum_id").empty();
            //$("#publish_department_id").attr('disabled', true);
            $("#publish_curriculum_id").attr('disabled', true);
            $("#publish_department_id").attr('disabled', true);
            $("#disabled_publish").attr('disabled', true);

            if (formData) {
                <?php
                if ($remedial) { ?>
                    if (remedial_program_id_selected) {
                        var formUrl = '/curriculums/get_curriculum_combo/' + formData + '/' + '<?= PROGRAM_REMEDIAL; ?>';
                    } else {
                        var formUrl = '/curriculums/get_curriculum_combo/' + formData + '/' + '<?= PROGRAM_UNDEGRADUATE; ?>';
                    }
                    <?php
                } else { ?>
                    var formUrl = '/curriculums/get_curriculum_combo/' + formData + '/' + '<?= PROGRAM_UNDEGRADUATE; ?>';
                    <?php
                } ?>

                $.ajax({
                    type: 'get',
                    url: formUrl,
                    data: formData,
                    success: function (data, textStatus, xhr) {
                        $("#disabled_publish").attr('disabled', false);
                        $("#publish_curriculum_id").attr('disabled', false);
                        $("#publish_department_id").attr('disabled', false);
                        $("#publish_curriculum_id").empty();
                        $("#publish_curriculum_id").append(data);
                    },
                    error: function (xhr, textStatus, error) {
                        alert(textStatus);
                    }
                }); 
                return false;

            } else {
                $('#publish_curriculum_id').empty().append('<option value="">[ Select Curriculum ]</option>');
                $("#publish_department_id").attr('disabled', false);
                $("#publish_curriculum_id").attr('disabled', false);
            }

        });
    });
</script>

<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Publish or Prepare Semester Courses For Pre/Freshman/Remedial'); ?></span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">

                <?= $this->Form->create('PublishedCourse'); ?>

                <div class="publishedCourses form">
                    <div style="margin-top: -30px;">
                        <hr>
                        <?php
                        if (!isset($turn_off_search)) { ?>
                            <fieldset style="padding-bottom: 5px; padding-top: 15px;">
                                <!-- <legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend> -->
                                <div class="row">
                                    <div class="large-3 columns">
                                        <?= $this->Form->input('PublishedCourse.academicyear', array('label' => 'Academic Year: ', 'type' => 'select', 'options' => $acyear_array_data, 'empty' => "[ Select Academic Year ]", 'default' => (isset($defaultacademicyear) ? $defaultacademicyear : '') , 'style' => 'width:90%;', 'required' => 'required')); ?>
                                    </div>
                                    <div class="large-3 columns">
                                        <?= $this->Form->input('PublishedCourse.semester', array('label' => 'Semester: ', 'options' => Configure::read('semesters'), 'empty' => '[ Select Semester ]', 'style' => 'width:90%;', 'required' => 'required')); ?>
                                    </div>
                                    <div class="large-3 columns">
                                        <?= $this->Form->input('PublishedCourse.program_id', array('label' => 'Program: ', 'empty' => "[ Select Program ]", 'style' => 'width:90%;', 'required' => 'required')); ?>
                                    </div>
                                    <div class="large-3 columns">
                                        <?= $this->Form->input('PublishedCourse.program_type_id', array('label' => 'Program Type: ', 'options' => $programTypess, 'empty' => "[ Select Program Type ]", 'style' => 'width:90%;', 'required' => 'required')); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="large-6 columns">
                                        <?= $this->Form->input('PublishedCourse.department_id', array('label' => 'Publish From  Department: ', 'empty' => "[ Select Department ]", 'default' => (isset($department_id) ? $department_id : ''),   'id' => 'publish_department_id', 'style' => 'width:95%;', 'required' => 'required')); ?>
                                    </div>
                                    <div class="large-6 columns">
                                        <?= $this->Form->input('PublishedCourse.curriculum_id', array('id' => 'publish_curriculum_id', 'label' => 'Curriculum: ', 'empty' => '[ Select Curriculum ]', 'default' => (isset($curriculum_id) ? $curriculum_id : ''), 'style' => 'width:95%;', 'required' => 'required')); ?>
                                    </div>
                                </div>
                                <hr>
                                <?= $this->Form->submit('Continue', array('name' => 'getsection', 'div' => 'false', 'id' => 'disabled_publish', 'class' => 'tiny radius button bg-blue')); ?>
                            </fieldset>
                            <?php
                        } ?>
                    </div>

                    <div style="overflow-x:auto;">
                        <table cellpadding="0" cellspacing="0" class="table">
                            <tbody>
                                <?php

                                if (isset($turn_off_search) && !empty($sections)) { ?>

                                    <tr><td><h6 class="fs14 text-gray">Select section(s) you want to publish course(s)</h6></td></tr>
                                    <?php
                                    echo $this->Form->hidden('PublishedCourse.semester', array('value' => $semester));
                                    echo $this->Form->hidden('PublishedCourse.program_id', array('value' => $program_id));
                                    echo $this->Form->hidden('PublishedCourse.program_type_id', array('value' => $program_type_id));
                                    echo $this->Form->hidden('PublishedCourse.academic_year', array('value' => $academic_year));
                                    echo $this->Form->hidden('PublishedCourse.department_id', array('value' => $department_id));
                                    echo $this->Form->hidden('PublishedCourse.curriculum_id', array('value' => $curriculum_id));

                                    foreach ($sections as $key => $value) { ?>
                                        <tr><td class="vcenter"><?= $this->Form->input('Section.selected.' . $key, array('class' => 'candidatePublishCourse', 'label' => $value, 'type' => 'checkbox', 'value' => $key, 'checked' => isset($selectedsection) && in_array($key, $selectedsection) ? 'checked' : '')); ?></td></tr>
                                        <?php
                                    }

                                    echo $this->Js->get("input.candidatePublishCourse")->event(
                                        "change",
                                        $this->Js->request(array(
                                            'controller' => 'publishedCourses',
                                            'action' => 'publisheForUnassigned'
                                        ), array(
                                            'update' => "#candidate_published_course_list",
                                            'async' => true,
                                            'method' => 'post',
                                            'dataExpression' => true,
                                            'beforeSend' => '$("#busy_indicator").show();',
                                            'complete' => '$("#busy_indicator").hide();',
                                            'data' => $this->Js->serializeForm(array(
                                                'isForm' => false,
                                                'inline' => true
                                            ))
                                        ))
                                    );
                                } ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- AJAX LOADING -->
                    <div id="candidate_published_course_list">

                    </div>
                    <!-- AJAX LOADING -->

                    <?php //echo $this->Form->end(); ?>
                </div>


            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $(".candidatePublishCourse").each(function () {
            if ($(this).is(":checked")) {
                $('#candidate_published_course_list').load('/publishedCourses/publisheForUnassigned/2');
            }
        });
    });
</script>
<?= $this->Js->writeBuffer(); ?>