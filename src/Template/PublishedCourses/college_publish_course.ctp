<?php
/**
 * @var \App\View\AppView $this
 * @var array $acyear_array_data
 * @var string $defaultacademicyear
 * @var array $sections
 * @var array $programTypess
 * @var int $program_id
 * @var int $program_type_id
 * @var string $academic_year
 * @var string $semester
 * @var int $department_id
 * @var int $curriculum_id
 * @var array $selectedsection
 * @var bool $turn_off_search
 * @var bool $show_publish_page
 */
use Cake\Core\Configure;
?>
<?= $this->Form->create(null, ['id' => 'publishCourseForm', 'type' => 'post']) ?>
<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;">
            <i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;">
                <?= __('Publish or Prepare Semester Courses For Pre/Freshman/Remedial') ?>
            </span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <div class="publishedCourses form">
                    <div style="margin-top: -30px;">
                        <hr>
                        <?php if (!isset($turn_off_search)) : ?>
                            <fieldset style="padding-bottom: 5px; padding-top: 15px;">
                                <div class="row">
                                    <div class="large-3 columns">
                                        <?= $this->Form->control('PublishedCourse.academicyear', [
                                            'label' => 'Academic Year: ',
                                            'type' => 'select',
                                            'options' => $acyear_array_data,
                                            'empty' => '[ Select Academic Year ]',
                                            'default' => isset($defaultacademicyear) ? $defaultacademicyear : '',
                                            'style' => 'width:90%;',
                                            'required' => true
                                        ]) ?>
                                    </div>
                                    <div class="large-3 columns">
                                        <?= $this->Form->control('PublishedCourse.semester', [
                                            'label' => 'Semester: ',
                                            'type' => 'select',
                                            'options' => Configure::read('semesters'),
                                            'empty' => '[ Select Semester ]',
                                            'style' => 'width:90%;',
                                            'required' => true
                                        ]) ?>
                                    </div>
                                    <div class="large-3 columns">
                                        <?= $this->Form->control('PublishedCourse.program_id', [
                                            'label' => 'Program: ',
                                            'type' => 'select',
                                            'empty' => '[ Select Program ]',
                                            'style' => 'width:90%;',
                                            'required' => true
                                        ]) ?>
                                    </div>
                                    <div class="large-3 columns">
                                        <?= $this->Form->control('PublishedCourse.program_type_id', [
                                            'label' => 'Program Type: ',
                                            'type' => 'select',
                                            'options' => $programTypess,
                                            'empty' => '[ Select Program Type ]',
                                            'style' => 'width:90%;',
                                            'required' => true
                                        ]) ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="large-6 columns">
                                        <?= $this->Form->control('PublishedCourse.department_id', [
                                            'label' => 'Publish From Department: ',
                                            'type' => 'select',
                                            'empty' => '[ Select Department ]',
                                            'default' => isset($department_id) ? $department_id : '',
                                            'id' => 'publish_department_id',
                                            'style' => 'width:95%;',
                                            'required' => true
                                        ]) ?>
                                    </div>
                                    <div class="large-6 columns">
                                        <?= $this->Form->control('PublishedCourse.curriculum_id', [
                                            'label' => 'Curriculum: ',
                                            'type' => 'select',
                                            'empty' => '[ Select Curriculum ]',
                                            'default' => isset($curriculum_id) ? $curriculum_id : '',
                                            'id' => 'publish_curriculum_id',
                                            'style' => 'width:95%;',
                                            'required' => true
                                        ]) ?>
                                    </div>
                                </div>
                                <hr>
                                <?= $this->Form->button('Continue', [
                                    'name' => 'getsection',
                                    'value'=>'getsection',
                                    'id' => 'disabled_publish',
                                    'class' => 'tiny radius button bg-blue'
                                ]) ?>
                            </fieldset>
                        <?php endif; ?>
                    </div>
                    <div style="overflow-x:auto;">
                        <table cellpadding="0" cellspacing="0" class="table">
                            <tbody>
                            <?php if (isset($turn_off_search) && !empty($sections)) : ?>
                                <tr>
                                    <td>
                                        <h6 class="fs14 text-gray">Select section(s) you want to publish course(s)</h6>
                                    </td>
                                </tr>
                                <?= $this->Form->hidden('PublishedCourse.semester', ['value' => $semester]) ?>
                                <?= $this->Form->hidden('PublishedCourse.program_id', ['value' => $program_id]) ?>
                                <?= $this->Form->hidden('PublishedCourse.program_type_id', ['value' => $program_type_id]) ?>
                                <?= $this->Form->hidden('PublishedCourse.academic_year', ['value' => $academic_year]) ?>
                                <?= $this->Form->hidden('PublishedCourse.department_id', ['value' => $department_id]) ?>
                                <?= $this->Form->hidden('PublishedCourse.curriculum_id', ['value' => $curriculum_id]) ?>
                                <?php foreach ($sections as $key => $value) : ?>
                                    <tr>
                                        <td class="vcenter">
                                            <?= $this->Form->control('Section.selected.' . $key, [
                                                'class' => 'candidatePublishCourse',
                                                'label' => h($value),
                                                'type' => 'checkbox',
                                                'value' => $key,
                                                'checked' => isset($selectedsection) && in_array($key, $selectedsection)
                                            ]) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div id="busy_indicator" style="display:none;">Loading...</div>
                    <div id="candidate_published_course_list"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->Form->end() ?>

<script>
    $(document).ready(function() {
        $("#publish_department_id").change(function() {
            var formData = $("#publish_department_id").val();
            var selected_program_id = $("#PublishedCourseProgramId").val();
            var remedial_program_id_selected = false;
            if (selected_program_id !== '') {
                if (selected_program_id == <?= defined('PROGRAM_REMEDIAL') ? PROGRAM_REMEDIAL : '0' ?>) {
                    remedial_program_id_selected = true;
                }
            }
            $("#publish_curriculum_id").empty();
            $("#publish_curriculum_id").attr('disabled', true);
            $("#publish_department_id").attr('disabled', true);
            $("#disabled_publish").attr('disabled', true);
            if (formData) {
                var formUrl = remedial_program_id_selected
                    ? '/curriculums/getCurriculumCombo/' + formData + '/' + '<?= defined('PROGRAM_REMEDIAL') ? PROGRAM_REMEDIAL : '0' ?>'
                    : '/curriculums/getCurriculumCombo/' + formData + '/' + '<?= defined('PROGRAM_UNDERGRADUATE') ? PROGRAM_UNDERGRADUATE : '0' ?>';
                $.ajax({
                    type: 'GET',
                    url: formUrl,
                    data: formData,
                    success: function(data, textStatus, xhr) {
                        $("#disabled_publish").attr('disabled', false);
                        $("#publish_curriculum_id").attr('disabled', false);
                        $("#publish_department_id").attr('disabled', false);
                        $("#publish_curriculum_id").empty().append(data);
                    },
                    error: function(xhr, textStatus, error) {
                        alert(textStatus);
                    }
                });
            } else {
                $("#publish_curriculum_id").empty().append('<option value="">[ Select Curriculum ]</option>');
                $("#publish_department_id").attr('disabled', false);
                $("#publish_curriculum_id").attr('disabled', false);
            }
            return false;
        });

        $(".candidatePublishCourse").on("change", function() {
            if ($(this).is(":checked")) {
                $("#busy_indicator").show();
                $.ajax({
                    url: '/published-courses/publishForUnassigned/2',
                    type: 'POST',
                    data: $("#publishCourseForm").serialize(),
                    success: function(data) {
                        $("#candidate_published_course_list").html(data);
                        $("#busy_indicator").hide();
                    },
                    error: function() {
                        $("#busy_indicator").hide();
                        alert("Error loading courses.");
                    }
                });
            }
        });
    });
</script>
