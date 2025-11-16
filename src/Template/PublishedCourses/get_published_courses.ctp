<?php
$this->assign('title', __('Published Courses for Merged Sections'));
?>

<?php
$list_of_courses = $this->request->getSession()->read('list_of_courses');
?>

<?php if (!empty($list_of_courses)): ?>
    <?= $this->Form->create('MergedSectionsCourse', ['url' => ['controller' => 'PublishedCourses', 'action' => 'mergeSections']]) ?>
    <table id="fieldsForm" class="table table-bordered">
        <thead>
        <tr>
            <th style="padding:0"><?= __('S.No') ?></th>
            <th style="padding:0"><?= __('Select') ?></th>
            <th style="padding:0"><?= __('Course Title') ?></th>
            <th style="padding:0"><?= __('Course Code') ?></th>
            <th style="padding:0"><?= __('Credit') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php $count = 1; ?>
        <?php foreach ($list_of_courses as $list_of_course): ?>
            <tr>
                <td><?= h($count++) ?></td>
                <td>
                    <?= $this->Form->checkbox("MergedSectionsCourses.selected.{$list_of_course['Course']['id']}") ?>
                    <?= $this->Form->hidden("MergedSectionsCourses.{$list_of_course['Course']['id']}.published_course_id", [
                        'value' => $list_of_course['PublishedCourse']['id']
                    ]) ?>
                </td>
                <td><?= h($list_of_course['Course']['course_title']) ?></td>
                <td><?= h($list_of_course['Course']['course_code']) ?></td>
                <td><?= h($list_of_course['Course']['credit']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="5">
                <?= $this->Form->control('merged_section_name', [
                    'label' => false,
                    'placeholder' => __('Merged Section Name')
                ]) ?>
            </td>
        </tr>
        <tr>
            <td colspan="5">
                <?= $this->Form->button(__('Merge Selected Sections'), [
                    'type' => 'submit',
                    'name' => 'merge',
                    'value' => 'merge',
                    'class' => 'btn btn-primary btn-sm'
                ]) ?>
            </td>
        </tr>
        </tfoot>
    </table>
    <?= $this->Form->end() ?>
<?php else: ?>
    <div class="alert alert-info">
        <?= __('Please select section that have course for final exam.') ?>
    </div>
<?php endif; ?>
