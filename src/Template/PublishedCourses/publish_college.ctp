<?php
$this->assign('title', __('Publish or Prepare Semester Courses for Department Unassigned Students'));
?>

<script type="text/javascript">
    $(document).ready(function () {
        $("#publish_department_id").change(function() {
            var formData = $(this).val();
            $("#publish_curriculum_id").empty().prop('disabled', true);
            $("#publish_department_id").prop('disabled', true);
            $("#disabled_publish").prop('disabled', true);
            var formUrl = '<?= $this->Url->build(['controller' => 'Curriculums', 'action' => 'getCurriculumCombo']) ?>/' + encodeURIComponent(formData) + '/<?= PROGRAM_UNDEGRADUATE ?>';
            $.ajax({
                type: 'GET',
                url: formUrl,
                data: { department_id: formData },
                success: function(data) {
                    $("#disabled_publish").prop('disabled', false);
                    $("#publish_curriculum_id").prop('disabled', false).empty().append(data);
                    $("#publish_department_id").prop('disabled', false);
                },
                error: function(xhr, textStatus, error) {
                    alert(textStatus);
                }
            });
            return false;
        });

        $(".candidatePublishCourse").change(function() {
            if ($(this).is(":checked")) {
                var formData = $('form').serialize();
                $.ajax({
                    type: 'POST',
                    url: '<?= $this->Url->build(['controller' => 'PublishedCourses', 'action' => 'publishForUnassigned']) ?>',
                    data: formData,
                    success: function(data) {
                        $('#candidate_published_course_list').html(data);
                    },
                    error: function(xhr, textStatus, error) {
                        alert(textStatus);
                    }
                });
            }
        });
    });
</script>

<div class="box">
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <?= $this->Form->create('PublishedCourse', ['url' => ['controller' => 'PublishedCourses', 'action' => 'publishCollege']]) ?>
                <div class="published-courses-form">
                    <?php if (!isset($turn_off_search)): ?>
                        <table class="table table-bordered">
                            <tr>
                                <td colspan="2" class="font-weight-bold"><?= __('Publish or Prepare Semester Courses For Department Unassigned Students.') ?></td>
                            </tr>
                            <tr>
                                <td>
                                    <?= $this->Form->control('academicyear', [
                                        'label' => __('Academic Year'),
                                        'type' => 'select',
                                        'options' => $acyear_array_data,
                                        'empty' => '--Select Academic Year--',
                                        'default' => isset($defaultacademicyear) ? $defaultacademicyear : ''
                                    ]) ?>
                                </td>
                                <td>
                                    <?= $this->Form->control('semester', [
                                        'label' => false,
                                        'type' => 'select',
                                        'options' => ['I' => 'I', 'II' => 'II'],
                                        'empty' => '--select semester--'
                                    ]) ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?= $this->Form->control('department_id', [
                                        'label' => __('Publish From Department'),
                                        'type' => 'select',
                                        'empty' => '--Select Department--',
                                        'id' => 'publish_department_id'
                                    ]) ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?= $this->Form->control('curriculum_id', [
                                        'label' => __('Curriculum'),
                                        'type' => 'select',
                                        'empty' => '--select curriculum--',
                                        'id' => 'publish_curriculum_id'
                                    ]) ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <?= $this->Form->button(__('Continue'), [
                                        'type' => 'submit',
                                        'name' => 'getsection',
                                        'value' => 'getsection',
                                        'id' => 'disabled_publish',
                                        'class' => 'btn btn-primary btn-sm'
                                    ]) ?>
                                </td>
                            </tr>
                        </table>
                    <?php endif; ?>

                    <?php if (isset($turn_off_search) && !empty($sections)): ?>
                        <table class="table table-bordered">
                            <tr>
                                <td class="font-weight-bold"><?= __('Select section you want to publish/unpublish course') ?></td>
                            </tr>
                            <?= $this->Form->hidden('semester', ['value' => $semester]) ?>
                            <?= $this->Form->hidden('program_id', ['value' => $program_id]) ?>
                            <?= $this->Form->hidden('program_type_id', ['value' => $program_type_id]) ?>
                            <?= $this->Form->hidden('academic_year', ['value' => $academic_year]) ?>
                            <?= $this->Form->hidden('department_id', ['value' => $department_id]) ?>
                            <?= $this->Form->hidden('curriculum_id', ['value' => $curriculum_id]) ?>
                            <?php foreach ($sections as $key => $value): ?>
                                <tr>
                                    <td>
                                        <?= $this->Form->control("Section.selected.{$key}", [
                                            'class' => 'candidatePublishCourse',
                                            'label' => h($value),
                                            'type' => 'checkbox',
                                            'value' => $key,
                                            'checked' => isset($selectedsection) && in_array($key, $selectedsection) ? true : false
                                        ]) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    <?php endif; ?>
                    <div id="candidate_published_course_list"></div>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>
