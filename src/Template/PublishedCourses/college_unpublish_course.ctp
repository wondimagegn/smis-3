<?php
$this->assign('title', __('Unpublish or Delete Semester Courses Form Pre/Freshman/Remedial'));
$this->Html->script('jquery-selectall', ['block' => true]);

use Cake\Core\Configure;
?>
<div class="box">
    <div class="box-header bg-transparent">
        <h3 class="box-title" style="margin-top: 10px;">
            <i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;">
                <?= __('Unpublish or Delete Semester Courses Form Pre/Freshman/Remedial') ?>
            </span>
        </h3>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <?= $this->Form->create('PublishedCourse', ['url' => ['controller' => 'PublishedCourses', 'action' => 'collegeUnpublishCourse']]) ?>
                <div class="published-courses-form">
                    <div style="margin-top: -30px;">
                        <hr>
                        <?php if (!isset($turn_off_search)): ?>
                            <fieldset style="padding-bottom: 5px; padding-top: 15px;">
                                <div class="row">
                                    <div class="col-md-3">
                                        <?= $this->Form->control('academic_year', [
                                            'label' => __('Academic Year: '),
                                            'type' => 'select',
                                            'options' => $acyear_array_data,
                                            'empty' => '[ Select Academic Year ]',
                                            'default' => isset($defaultacademicyear) ? $defaultacademicyear : '',
                                            'style' => 'width:90%;',
                                            'required' => 'required'
                                        ]) ?>
                                    </div>
                                    <div class="col-md-3">
                                        <?= $this->Form->control('semester', [
                                            'label' => __('Semester: '),
                                            'type' => 'select',
                                            'options' => Configure::read('semesters'),
                                            'empty' => '[ Select Semester ]',
                                            'style' => 'width:90%;',
                                            'required' => 'required'
                                        ]) ?>
                                    </div>
                                    <div class="col-md-3">
                                        <?= $this->Form->control('program_id', [
                                            'label' => __('Program: '),
                                            'type' => 'select',
                                            'empty' => '[ Select Program ]',
                                            'style' => 'width:90%;',
                                            'required' => 'required'
                                        ]) ?>
                                    </div>
                                    <div class="col-md-3">
                                        <?= $this->Form->control('program_type_id', [
                                            'label' => __('Program Type: '),
                                            'type' => 'select',
                                            'options' => $programTypess,
                                            'empty' => '[ Select Program Type ]',
                                            'style' => 'width:90%;',
                                            'required' => 'required'
                                        ]) ?>
                                    </div>
                                </div>
                                <hr>
                                <?= $this->Form->button(__('Continue'), [
                                    'type' => 'submit',
                                    'name' => 'getsection',
                                    'value' => 'getsection',
                                    'id' => 'disabled_publish',
                                    'class' => 'btn btn-primary btn-sm'
                                ]) ?>
                            </fieldset>
                        <?php endif; ?>
                    </div>

                    <?php if (isset($show_unpublish_page)): ?>
                        <?php if (!empty($publishedcourses)): ?>
                            <?php
                            $enable_delete_button = 0;
                            $enable_publish_as_drop_button = 0;
                            ?>
                            <h6 id="validation-message_non_selected" class="text-danger fs14"></h6>
                            <br>
                            <?php foreach ($publishedcourses as $section_name => $sectioned_published_courses): ?>
                                <div style="overflow-x:auto;">
                                    <table id="fieldsForm" class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th colspan="8"><?= __('Section: %s', h($section_name)) ?></th>
                                        </tr>
                                        <tr>
                                            <th colspan="8">
                                                <?= __('Select the course(s) you want to unpublish/delete') ?>
                                                <?= Configure::read('ALLOW_PUBLISH_AS_DROP_COURSE_FOR_COLLEGE_ROLE') ? __(' or publish as mass drop.') : '.' ?>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="text-center">&nbsp;</th>
                                            <th class="text-center">#</th>
                                            <th class="text-center">Course Title</th>
                                            <th class="text-center">Course Code</th>
                                            <th class="text-center">Credit</th>
                                            <th class="text-center">Lecture hour</th>
                                            <th class="text-center">Tutorial hour</th>
                                            <th class="text-center">Lab hour</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $count = 1; $course_registered_only = 0; ?>
                                        <?php foreach ($sectioned_published_courses as $vc): ?>
                                            <?php
                                            $red = null;
                                            if (isset($courses_not_allowed[$vc['PublishedCourse']['section_id']]) && in_array($vc['Course']['id'], $courses_not_allowed[$vc['PublishedCourse']['section_id']])) {
                                                $red = 'style="color:red;"';
                                            }
                                            if (!$vc['PublishedCourse']['unpublish_readOnly'] && $vc['PublishedCourse']['have_course_registration_or_add']) {
                                                $enable_publish_as_drop_button++;
                                            }
                                            if (!$vc['PublishedCourse']['unpublish_readOnly'] && !$vc['PublishedCourse']['have_course_registration_or_add']) {
                                                $enable_delete_button++;
                                            }
                                            ?>
                                            <tr <?= $red ?>>
                                                <?php if ($vc['PublishedCourse']['unpublish_readOnly']): ?>
                                                    <td class="text-center">**</td>
                                                    <?php $course_registered_only++; ?>
                                                <?php else: ?>
                                                    <td class="text-center">
                                                        <?= $this->Form->checkbox("Course.pub.{$vc['PublishedCourse']['section_id']}.{$vc['Course']['id']}") ?>
                                                    </td>
                                                <?php endif; ?>
                                                <td class="text-center"><?= h($count++) ?></td>
                                                <td class="text-center"><?= h($vc['Course']['course_title']) ?></td>
                                                <td class="text-center"><?= h($vc['Course']['course_code']) ?></td>
                                                <td class="text-center"><?= h($vc['Course']['credit']) ?></td>
                                                <td class="text-center"><?= h($vc['Course']['lecture_hours']) ?></td>
                                                <td class="text-center"><?= h($vc['Course']['tutorial_hours']) ?></td>
                                                <td class="text-center"><?= h($vc['Course']['laboratory_hours']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                        <?php if ($course_registered_only > 0): ?>
                                            <tfoot>
                                            <tr>
                                                <td colspan="2">**</td>
                                                <td colspan="6" style="font-weight: normal;">
                                                    <?= __('Courses marked ** are not allowed to unpublish since students already registered or grade has been submitted.') ?>
                                                </td>
                                            </tr>
                                            </tfoot>
                                        <?php endif; ?>
                                    </table>
                                </div>
                                <br>
                            <?php endforeach; ?>
                            <hr>
                            <div class="row">
                                <div class="col-md-4">
                                    <?= $enable_delete_button ? $this->Form->button(__('Delete Selected'), [
                                        'type' => 'submit',
                                        'name' => 'deleteselected',
                                        'value' => 'deleteselected',
                                        'id' => 'deleteSelected',
                                        'class' => 'btn btn-primary btn-sm'
                                    ]) : '' ?>
                                </div>
                                <div class="col-md-8">
                                    <?= Configure::read('ALLOW_PUBLISH_AS_DROP_COURSE_FOR_COLLEGE_ROLE') && $enable_publish_as_drop_button ? $this->Form->button(__('Publish Selected as Mass Drop'), [
                                        'type' => 'submit',
                                        'name' => 'dropselected',
                                        'value' => 'dropselected',
                                        'id' => 'publishSelectedAsMassDrop',
                                        'class' => 'btn btn-danger btn-sm'
                                    ]) : '' ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
                                <span style="margin-right: 15px;"></span>
                                <?= __('No published courses are found to unpublish or drop with the given search criteria.') ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var form_being_submitted = false;
    const validationMessageNonSelected = document.getElementById('validation-message_non_selected');

    $('#publishSelectedAsMassDrop').click(function(e) {
        var checkboxes = document.querySelectorAll('input[type="checkbox"]');
        var checkedOne = Array.from(checkboxes).some(x => x.checked);
        if (!checkedOne) {
            alert('At least one course must be selected to publish as mass drop.');
            validationMessageNonSelected.innerHTML = 'At least one course must be selected to publish as mass drop.';
            return false;
        }
        if (form_being_submitted) {
            alert("Publishing Selected Courses as Mass Drop, please wait a moment...");
            $('#publishSelectedAsMassDrop').prop('disabled', true);
            return false;
        }
        var confirmed = confirm('Are you sure you want to mass drop the selected course(s) for the selected section? Use this option if and only if you are unable to delete the course(s) using Delete Selected option or if one or more students are registered for the course.');
        if (confirmed) {
            $('#publishSelectedAsMassDrop').val('Publishing as Mass Drop...');
            form_being_submitted = true;
            return true;
        }
        return false;
    });

    $('#deleteSelected').click(function(e) {
        var checkboxes = document.querySelectorAll('input[type="checkbox"]');
        var checkedOne = Array.from(checkboxes).some(x => x.checked);
        if (!checkedOne) {
            alert('At least one course must be selected to delete a published course.');
            validationMessageNonSelected.innerHTML = 'At least one course must be selected to delete a published course.';
            return false;
        }
        if (form_being_submitted) {
            alert("Deleting Selected Courses, please wait a moment...");
            $('#deleteSelected').prop('disabled', true);
            return false;
        }
        var confirmed = confirm('Are you sure you want to delete the selected course(s) for the selected section?');
        if (confirmed) {
            $('#deleteSelected').val('Deleting Selected Courses...');
            form_being_submitted = true;
            return true;
        }
        return false;
    });

    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>
