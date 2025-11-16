<?php if (!empty($list_of_courses)): ?>
    <?= $this->Form->create('SectionSplitForPublishedCourse', ['url' => ['controller' => 'PublishedCourses', 'action' => 'splitSectionsForPublishedCourses']]) ?>
    <table id="fieldsForm" class="table table-bordered">
        <thead>
        <tr>
            <th style="padding:0"><?= __('S.No') ?></th>
            <th style="padding:0"><?= __('Select') ?></th>
            <th style="padding:0"><?= __('Select Course Type') ?></th>
            <th style="padding:0"><?= __('Course Title') ?></th>
            <th style="padding:0"><?= __('Course Code') ?></th>
            <th style="padding:0"><?= __('Credit') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php $count = 1; ?>
        <?php foreach ($list_of_courses as $key => $list_of_course): ?>
            <tr>
                <td><?= h($count++) ?></td>
                <?php if ($list_of_course['GradeSubmitted'] > 0): ?>
                    <td>***</td>
                    <td>&nbsp;</td>
                <?php else: ?>
                    <td>
                        <?= $this->Form->checkbox("SectionSplitForPublishedCourses.selected.{$list_of_course['Course']['id']}") ?>
                        <?= $this->Form->hidden("SectionSplitForPublishedCourses.{$list_of_course['Course']['id']}.published_course_id", [
                            'value' => $list_of_course['PublishedCourse']['id']
                        ]) ?>
                    </td>
                    <td>
                        <?= $this->Form->control("SectionSplitForPublishedCourses.type.{$list_of_course['Course']['id']}", [
                            'label' => false,
                            'type' => 'select',
                            'options' => $course_type_array[$key]
                        ]) ?>
                    </td>
                <?php endif; ?>
                <td><?= h($list_of_course['Course']['course_title']) ?></td>
                <td><?= h($list_of_course['Course']['course_code']) ?></td>
                <td><?= h($list_of_course['Course']['credit']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="6"><?= __('*** are courses you cannot split because result entry has already begun.') ?></td>
        </tr>
        <tr>
            <td colspan="6">
                <?= $this->Form->button(__('Split Selected Sections'), [
                    'type' => 'submit',
                    'name' => 'split',
                    'value' => 'split',
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
