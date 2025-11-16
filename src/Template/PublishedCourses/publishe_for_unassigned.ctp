<?php
$this->assign('title', __('Publish Courses for Unassigned Students'));

$coursesss = $this->request->getSession()->read('candidate_publish_courses');
$taken_courses_allow_to_publishe_it = $this->request->getSession()->read('taken_courses_allow_to_publishe_it');
$selected_section = $this->request->getSession()->read('selected_section');
$published_courses_disable_not_to_published = $this->request->getSession()->read('published_courses_disable_not_to_published');
?>

<?= $this->Form->create('PublishedCourse', ['url' => ['controller' => 'PublishedCourses', 'action' => 'publishForUnassigned']]) ?>
<?php if (!empty($coursesss)): ?>
    <?php
    $display_button = 0;
    $section_count = 0;
    $enable_publish_button = 1;
    $enable_publish_as_add_button = 1;
    ?>
    <?php foreach ($coursesss as $section_id => $coursss): ?>
        <?php $section_count++; ?>
        <?php if (!empty($coursss)): ?>
            <h6 id="validation-message_non_selected" class="text-danger" style="font-size: 14px;"></h6>
            <br>
            <div style="overflow-x:auto;">
                <table id="fieldsForm" class="table table-bordered">
                    <thead>
                    <tr>
                        <th colspan="8"><?= __('Section: %s', h($selected_section[$section_id])) ?></th>
                    </tr>
                    <tr>
                        <th colspan="8"><?= __('Select the course you want to publish') ?></th>
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
                    <?php $count = 0; ?>
                    <?php foreach ($coursss as $kc => $vc): ?>
                        <tr>
                            <?php if (isset($published_courses_disable_not_to_published[$section_id]) && in_array($vc['Course']['id'], $published_courses_disable_not_to_published[$section_id])): ?>
                                <td class="text-center">**</td>
                            <?php else: ?>
                                <td class="text-center">
                                    <?= $this->Form->checkbox("Course.{$section_id}.{$vc['Course']['id']}") ?>
                                </td>
                            <?php endif; ?>
                            <td class="text-center"><?= h(++$count) ?></td>
                            <td class="text-center"><?= h($vc['Course']['course_title']) ?></td>
                            <td class="text-center"><?= h($vc['Course']['course_code']) ?></td>
                            <td class="text-center"><?= h($vc['Course']['credit']) ?></td>
                            <td class="text-center"><?= h($vc['Course']['lecture_hours']) ?></td>
                            <td class="text-center"><?= h($vc['Course']['tutorial_hours']) ?></td>
                            <td class="text-center"><?= h($vc['Course']['laboratory_hours']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <?php if (isset($published_courses_disable_not_to_published[$section_id]) && count($published_courses_disable_not_to_published[$section_id]) > 0): ?>
                        <tfoot>
                        <tr>
                            <td colspan="2">**</td>
                            <td colspan="6" style="font-weight: normal;">
                                <?= __('Courses marked ** are already published for the section.') ?>
                            </td>
                        </tr>
                        </tfoot>
                    <?php endif; ?>
                </table>
            </div>
            <br>
        <?php else: ?>
            <?php $display_button++; ?>
        <?php endif; ?>
    <?php endforeach; ?>
    <div class="row">
        <div class="col-md-12">
            <hr>
            <?php if ($display_button != $section_count): ?>
                <div class="col-md-4">
                    <?= $enable_publish_button ? $this->Form->button(__('Publish Selected Courses'), [
                        'type' => 'submit',
                        'name' => 'publishselected',
                        'id' => 'publishselected',
                        'class' => 'btn btn-primary btn-sm'
                    ]) : '' ?>
                </div>
                <div class="col-md-8">
                    <?= Configure::read('ALLOW_PUBLISH_AS_ADD_COURSE_FOR_COLLEGE_ROLE') && $enable_publish_as_add_button ? $this->Form->button(__('Publish Selected as Mass Add'), [
                        'type' => 'submit',
                        'name' => 'publishselectedadd',
                        'id' => 'publishSelectedAsAdd',
                        'class' => 'btn btn-danger btn-sm'
                    ]) : '' ?>
                </div>
            <?php else: ?>
                <h6 class="text-muted" style="font-size: 14px;">
                    <?= __('It seems there is no course in selected curriculum. You need to define courses under the curriculum before publishing it.') ?>
                </h6>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
<?= $this->Form->end() ?>

<script type="text/javascript">
    var form_being_submitted = false;
    const validationMessageNonSelected = document.getElementById('validation-message_non_selected');

    $('#publishSelectedAsAdd').click(function(e) {
        var checkboxes = document.querySelectorAll('input[type="checkbox"][name^="data[Course]"]');
        var checkedOne = Array.from(checkboxes).some(x => x.checked);
        if (!checkedOne) {
            alert('At least one course must be selected to publish as mass add.');
            validationMessageNonSelected.innerHTML = 'At least one course must be selected to publish as mass add.';
            return false;
        }
        if (form_being_submitted) {
            alert("Publishing Selected as Mass Add, please wait a moment...");
            $('#publishSelectedAsAdd').prop('disabled', true);
            return false;
        }
        var confirmed = confirm('Are you sure you want to publish the selected courses as Mass Add for the selected section? Use this option if and only if there is a previous course publication for the section using the same academic year and semester with section students already registered for the courses or you are unable to publish the courses using Publish Selected option, i.e. if you forgot to publish the courses for the section in the given academic year and semester or the students are taking the courses as a block course.');
        if (confirmed) {
            $('#publishSelectedAsAdd').val('Publishing as Mass Add...');
            form_being_submitted = true;
            return true;
        }
        return false;
    });

    $('#publishselected').click(function(e) {
        var checkboxes = document.querySelectorAll('input[type="checkbox"][name^="data[Course]"]');
        var checkedOne = Array.from(checkboxes).some(x => x.checked);
        if (!checkedOne) {
            alert('At least one course must be selected to publish.');
            validationMessageNonSelected.innerHTML = 'At least one course must be selected to publish.';
            return false;
        }
        if (form_being_submitted) {
            alert("Publishing Selected Courses, please wait a moment...");
            $('#publishselected').prop('disabled', true);
            return false;
        }
        $('#publishselected').val('Publishing Selected Courses...');
        form_being_submitted = true;
        return true;
    });

    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>
