<?php
/**
 * @var \App\View\AppView $this
 * @var array $publishedcourses
 * @var array $courses_not_allowed
 * @var array $acyear_array_data
 * @var string $defaultacademicyear
 * @var bool $turn_off_search
 * @var bool $show_unpublish_page
 */
use Cake\Core\Configure;
?>

<?= $this->Html->script('jquery-selectall', ['block' => 'script']) ?>
<?= $this->Form->create(null, ['onsubmit' => 'return checkForm(this);', 'type' => 'post']) ?>
    <div class="box">
        <div class="box-header bg-transparent">
            <div class="box-title" style="margin-top: 10px;">
                <i class="fontello-minus" style="font-size: larger; font-weight: bold;"></i>
                <span style="font-size: medium; font-weight: bold; margin-top: 20px;">
                <?= __('Unpublish or Delete Courses from Publish Courses List') ?>
            </span>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="large-12 columns">
                    <div class="publishedCourses form">
                        <?php if (!isset($turn_off_search)) : ?>
                            <div style="margin-top: -30px;">
                                <hr>
                                <fieldset style="padding-bottom: 5px; padding-top: 15px;">
                                    <div class="row">
                                        <div class="large-2 columns">
                                            <?= $this->Form->control('PublishedCourse.academic_year', [
                                                'label' => 'Academic Year: ',
                                                'type' => 'select',
                                                'style' => 'width:90%;',
                                                'options' => $acyear_array_data,
                                                'default' => isset($defaultacademicyear) ? $defaultacademicyear : '',
                                                'empty' => '[ Select Academic Year ]'
                                            ]) ?>
                                        </div>
                                        <div class="large-2 columns">
                                            <?= $this->Form->control('PublishedCourse.semester', [
                                                'label' => 'Semester: ',
                                                'type' => 'select',
                                                'options' => Configure::read('semesters'),
                                                'required' => true,
                                                'empty' => '[ Select semester ]',
                                                'style' => 'width:90%;'
                                            ]) ?>
                                        </div>
                                        <div class="large-3 columns">
                                            <?= $this->Form->control('PublishedCourse.program_id', [
                                                'label' => 'Program: ',
                                                'type' => 'select',
                                                'required' => true,
                                                'empty' => '[ Select Program ]',
                                                'style' => 'width:90%;'
                                            ]) ?>
                                        </div>
                                        <div class="large-3 columns">
                                            <?= $this->Form->control('PublishedCourse.program_type_id', [
                                                'label' => 'Program Type: ',
                                                'type' => 'select',
                                                'required' => true,
                                                'empty' => '[ Select Program Type ]',
                                                'style' => 'width:90%;'
                                            ]) ?>
                                        </div>
                                        <div class="large-2 columns">
                                            <?= $this->Form->control('PublishedCourse.year_level_id', [
                                                'label' => 'Year Level: ',
                                                'type' => 'select',
                                                'required' => true,
                                                'empty' => '[ Select Year Level ]',
                                                'style' => 'width:90%;'
                                            ]) ?>
                                        </div>
                                    </div>
                                    <hr>
                                    <?= $this->Form->button('Continue', [
                                        'name' => 'getsection',
                                        'class' => 'tiny radius button bg-blue'
                                    ]) ?>
                                </fieldset>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($show_unpublish_page) && !empty($publishedcourses)) : ?>
                            <div style="margin-top: -30px;">
                                <hr>
                                <h6 class="tf16 text-gray">Select the course you want to unpublish/publish as drop course</h6>
                                <h6 id="validation-message_non_selected" class="text-red fs14"></h6>
                                <br>
                                <div style="overflow-x:auto;">
                                    <table id="fieldsForm" cellpadding="0" cellspacing="0" class="table">
                                        <tbody>
                                        <?php foreach ($publishedcourses as $section_name => $sectioned_published_courses) : ?>
                                            <tr>
                                                <td colspan="9" class="vcenter" style="border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85);">
                                                    <h6>
                                                        <?= h($section_name . ' (' . (!empty($sectioned_published_courses[0]->section->year_level->name) ? $sectioned_published_courses[0]->section->year_level->name : ($sectioned_published_courses[0]->section->program_id == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')) . ', ' . $sectioned_published_courses[0]->section->academicyear . ', ' . (isset($sectioned_published_courses[0]->semester) ? ($sectioned_published_courses[0]->semester == 'I' ? '1st Semester' : ($sectioned_published_courses[0]->semester == 'II' ? '2nd Semester' : ($sectioned_published_courses[0]->semester == 'III' ? '3rd Semester' : $sectioned_published_courses[0]->semester . ' Semester'))) : '')) . ')'; ?>
                                                    </h6>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th class="center">&nbsp;</th>
                                                <th class="center">#</th>
                                                <th class="vcenter">Course Title</th>
                                                <th class="center">Course Code</th>
                                                <th class="center">Lecture hour</th>
                                                <th class="center">Tutorial hour</th>
                                                <th class="center">
                                                    <?= h(isset($sectioned_published_courses[0]->course->curriculum->type_credit) ? (str_contains($sectioned_published_courses[0]->course->curriculum->type_credit, 'ECTS') ? 'ECTS' : 'Credit') : (isset($sectioned_published_courses[0]->section->curriculum->type_credit) ? (str_contains($sectioned_published_courses[0]->section->curriculum->type_credit, 'ECTS') ? 'ECTS' : 'Credit') : 'Credit')) ?>
                                                </th>
                                                <th class="center">Year</th>
                                                <th class="center">Sem</th>
                                            </tr>
                                            <?php
                                            $count = 1;
                                            $course_registered_only = 0;
                                            foreach ($sectioned_published_courses as $vc) :
                                                $red = null;
                                                if (isset($courses_not_allowed[$vc->section_id]) && in_array($vc->course->id, $courses_not_allowed[$vc->section_id])) {
                                                    $red = 'class="redrow"';
                                                }
                                                ?>
                                                <tr <?= $red ?>>
                                                    <?php if ($vc->unpublish_read_only) : ?>
                                                        <td class="center">**</td>
                                                        <?php $course_registered_only++; ?>
                                                    <?php else : ?>
                                                        <td class="center">
                                                            <?= $this->Form->checkbox('Course.pub.' . $vc->section_id . '.' . $vc->course->id) ?>
                                                        </td>
                                                    <?php endif; ?>
                                                    <td class="center"><?= $count ?></td>
                                                    <td class="vcenter"><?= h($vc->course->course_title) ?></td>
                                                    <td class="center"><?= h($vc->course->course_code) ?></td>
                                                    <td class="center"><?= h($vc->course->lecture_hours) ?></td>
                                                    <td class="center"><?= h($vc->course->tutorial_hours) ?></td>
                                                    <td class="center"><?= h($vc->course->credit) ?></td>
                                                    <td class="center"><?= h($vc->year_level->name) ?></td>
                                                    <td class="center"><?= h($vc->course->semester) ?></td>
                                                </tr>
                                                <?php $count++; ?>
                                            <?php endforeach; ?>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <br>
                                <?php if ($course_registered_only > 0) : ?>
                                    <h6 class="text-red fs14">
                                        ** marked courses are not allowed to delete. Students are already registered for the course or grade submission has been started. You can try Mass dropping such courses if grade is not submitted.
                                    </h6>
                                <?php endif; ?>
                                <?php if ($course_registered_only != ($count - 1)) : ?>
                                    <hr>
                                    <div class="row">
                                        <div class="large-3 columns">
                                            <?= $this->Form->button('Delete Selected', [
                                                'name' => 'deleteselected',
                                                'id' => 'deleteselected',
                                                'value'=>'deleteselected',
                                                'class' => 'tiny radius button bg-blue'
                                            ]) ?>
                                        </div>
                                        <div class="large-3 columns">
                                            <?= $this->Form->button('Publish Selected as Mass Drop', [
                                                'name' => 'dropselected',
                                                'value'=>'dropselected',
                                                'id' => 'dropselected',
                                                'class' => 'tiny radius button bg-red'
                                            ]) ?>
                                        </div>
                                        <div class="large-6 columns">&nbsp;</div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php else : ?>
                            <div class="info-box info-message">
                                <span style="margin-right: 15px;"></span>
                                <i style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
                                    No active section with a course publication is found in the given criteria to unpublish courses or to mass drop.
                                </i>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->Form->end() ?>
<script>
    var form_being_submitted = false;
    const validationMessageNonSelected = document.getElementById('validation-message_non_selected');

    function checkForm(form) {
        var checkboxes = document.querySelectorAll('input[type="checkbox"][name^="Course[pub]"]');
        var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);
        if (!checkedOne) {
            validationMessageNonSelected.innerHTML = 'At least one course must be selected to unpublish or to publish as mass drop.';
            return false;
        }
        if (form_being_submitted) {
            alert("Processing your request, please wait a moment...");
            form.deleteselected.disabled = true;
            form.dropselected.disabled = true;
            return false;
        }
        form_being_submitted = true;
        return true;
    }

    $(document).ready(function() {
        $('#dropselected').click(function() {
            var checkboxes = document.querySelectorAll('input[type="checkbox"][name^="Course[pub]"]');
            var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);
            if (!checkedOne) {
                alert('At least one course must be selected to publish as mass drop.');
                validationMessageNonSelected.innerHTML = 'At least one course must be selected to publish as mass drop.';
                return false;
            }
            return confirm('Are you sure you want to publish the selected course(s) as Mass Drop for the selected section? Use this option if and only if the students are registered for published course and registrar can\'t cancel the students registration normally or you can\'t use Delete Selected Option to delete the selected published courses.');
        });

        $('#deleteselected').click(function() {
            var checkboxes = document.querySelectorAll('input[type="checkbox"][name^="Course[pub]"]');
            var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);
            if (!checkedOne) {
                alert('At least one course must be selected to delete.');
                validationMessageNonSelected.innerHTML = 'At least one course must be selected to delete.';
                return false;
            }
            return confirm('Are you sure you want to delete the selected course(s)?');
        });

        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    });
</script>
