<?php
$this->assign('title', __('Unpublish or Delete Courses'));
$this->Html->script('jquery-selectall', ['block' => true]);
?>

<?= $this->Form->create('PublishedCourse', ['url' => ['controller' => 'PublishedCourses', 'action' => 'unpublishCollege']]) ?>
    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="published-courses-form">
                        <?php if (!isset($turn_off_search)): ?>
                            <table class="table table-bordered">
                                <tr>
                                    <td colspan="2" class="font-weight-bold"><?= __('Unpublish or delete courses from the publish list.') ?></td>
                                </tr>
                                <tr>
                                    <td>
                                        <?= $this->Form->control('academic_year', [
                                            'label' => __('Academic Year'),
                                            'type' => 'select',
                                            'options' => $acyear_array_data,
                                            'empty' => '--Select Academic Year--',
                                            'default' => isset($defaultacademicyear) ? $defaultacademicyear : ''
                                        ]) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?= $this->Form->control('semester', [
                                            'label' => false,
                                            'type' => 'select',
                                            'options' => ['I' => 'I', 'II' => 'II', 'III' => 'III'],
                                            'empty' => '--select semester--'
                                        ]) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?= $this->Form->button(__('Continue'), [
                                            'type' => 'submit',
                                            'name' => 'getsection',
                                            'class' => 'btn btn-primary btn-sm'
                                        ]) ?>
                                    </td>
                                </tr>
                            </table>
                        <?php endif; ?>

                        <?php if (isset($show_unpublish_page) && !empty($publishedcourses)): ?>
                            <table class="table table-bordered">
                                <tr>
                                    <td><div class="font-weight-bold"><?= __('Select the course you want to unpublish/publish as drop/delete') ?></div></td>
                                </tr>
                            </table>
                            <table id="fieldsForm" class="table table-bordered">
                                <thead>
                                <?php foreach ($publishedcourses as $section_name => $sectioned_published_courses): ?>
                                    <tr>
                                        <td colspan="7"><h3><?= h($section_name) ?></h3></td>
                                    </tr>
                                    <tr>
                                        <th style="padding:0"><?= __('Select') ?></th>
                                        <th style="padding:0"><?= __('S.No') ?></th>
                                        <th style="padding:0"><?= __('Course Title') ?></th>
                                        <th style="padding:0"><?= __('Course Code') ?></th>
                                        <th style="padding:0"><?= __('Lecture hour') ?></th>
                                        <th style="padding:0"><?= __('Tutorial hour') ?></th>
                                        <th style="padding:0"><?= __('Credit') ?></th>
                                    </tr>
                                    <?php
                                    $count = 1;
                                    $course_registered_only = 0;
                                    foreach ($sectioned_published_courses as $vc):
                                        $red = null;
                                        if (isset($courses_not_allowed[$vc['PublishedCourse']['section_id']]) && in_array($vc['Course']['id'], $courses_not_allowed[$vc['PublishedCourse']['section_id']])) {
                                            $red = 'style="color:red;"';
                                        }
                                        ?>
                                        <tr <?= $red ?>>
                                            <?php if ($vc['PublishedCourse']['unpublish_readOnly']): ?>
                                                <td>**</td>
                                                <?php $course_registered_only++; ?>
                                            <?php else: ?>
                                                <td>
                                                    <?= $this->Form->checkbox("Course.pub.{$vc['PublishedCourse']['section_id']}.{$vc['Course']['id']}") ?>
                                                </td>
                                            <?php endif; ?>
                                            <td><?= h($count++) ?></td>
                                            <td><?= h($vc['Course']['course_title']) ?></td>
                                            <td><?= h($vc['Course']['course_code']) ?></td>
                                            <td><?= h($vc['Course']['lecture_hours']) ?></td>
                                            <td><?= h($vc['Course']['tutorial_hours']) ?></td>
                                            <td><?= h($vc['Course']['credit']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if ($course_registered_only > 0): ?>
                                        <tr>
                                            <td colspan="7">
                                                <?= __('**: Those courses with ** are not allowed to unpublish since students have already registered or grade has been submitted.') ?>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                </thead>
                            </table>
                            <table class="table">
                                <tr>
                                    <td style="padding:0">
                                        <?= $this->Form->button(__('Delete Selected'), [
                                            'type' => 'submit',
                                            'name' => 'deleteselected',
                                            'class' => 'btn btn-primary btn-sm'
                                        ]) ?>
                                    </td>
                                    <td style="padding:0">
                                        <?= $this->Form->button(__('Publish As Drop Selected'), [
                                            'type' => 'submit',
                                            'name' => 'dropselected',
                                            'class' => 'btn btn-danger btn-sm'
                                        ]) ?>
                                    </td>
                                </tr>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->Form->end() ?>
