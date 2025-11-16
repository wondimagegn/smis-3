<?php
$this->assign('title', __('Add Course Number of Session'));

$role_id=$this->getRequest()->getSession()->read('Auth')['User']['role_id'];
?>
<div class="box">
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <div class="course-number-of-sessions-form">
                    <?= $this->Form->create('PublishedCourse', ['url' => ['controller' => 'PublishedCourses', 'action' => 'addCourseSession']]) ?>
                    <h4 class="smallheading"><?= __('Add Course Number of Session') ?></h4>
                    <table class="table table-bordered">
                        <tr>
                            <td class="font"><?= __('Academic Year') ?></td>
                            <td>
                                <?= $this->Form->control('academicyear', [
                                    'label' => false,
                                    'type' => 'select',
                                    'options' => $acyear_array_data,
                                    'default' => isset($selected_academicyear) ? $selected_academicyear : '',
                                    'empty' => '--Select Academic Year--',
                                    'style' => 'width:150px'
                                ]) ?>
                            </td>
                            <td class="font"><?= __('Program') ?></td>
                            <td>
                                <?= $this->Form->control('program_id', [
                                    'label' => false,
                                    'type' => 'select',
                                    'default' => isset($selected_program) ? $selected_program : '',
                                    'empty' => '--Select Program--',
                                    'style' => 'width:150px'
                                ]) ?>
                            </td>
                            <td class="font"><?= __('Program Type') ?></td>
                            <td>
                                <?= $this->Form->control('program_type_id', [
                                    'label' => false,
                                    'type' => 'select',
                                    'default' => isset($selected_program_type) ? $selected_program_type : '',
                                    'empty' => '--Select Program Type--',
                                    'style' => 'width:150px'
                                ]) ?>
                            </td>
                        </tr>
                        <?php if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_COLLEGE): ?>
                            <tr>
                                <td class="font"><?= __('Department') ?></td>
                                <td>
                                    <?= $this->Form->control('department_id', [
                                        'label' => false,
                                        'id' => 'ajax_department_published_course',
                                        'default' => isset($selected_department) ? $selected_department : '',
                                        'empty' => 'Pre/(Unassign Freshman)',
                                        'style' => 'width:150px'
                                    ]) ?>
                                </td>
                                <td class="font"><?= __('Year Level') ?></td>
                                <td id="ajax_year_level_published_course">
                                    <?= $this->Form->control('year_level_id', [
                                        'label' => false,
                                        'id' => 'ajax_year_level_published',
                                        'default' => isset($selected_year_level) ? $selected_year_level : '',
                                        'empty' => 'All',
                                        'style' => 'width:150px'
                                    ]) ?>
                                </td>
                                <td class="font"><?= __('Semester') ?></td>
                                <td>
                                    <?= $this->Form->control('semester', [
                                        'label' => false,
                                        'type' => 'select',
                                        'options' => ['I' => 'I', 'II' => 'II', 'III' => 'III'],
                                        'default' => isset($selected_semester) ? $selected_semester : '',
                                        'empty' => '--select semester--',
                                        'style' => 'width:150px'
                                    ]) ?>
                                </td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td class="font"><?= __('Year Level') ?></td>
                                <td>
                                    <?= $this->Form->control('year_level_id', [
                                        'label' => false,
                                        'default' => isset($selected_year_level) ? $selected_year_level : '',
                                        'empty' => 'All',
                                        'style' => 'width:150px'
                                    ]) ?>
                                </td>
                                <td class="font"><?= __('Semester') ?></td>
                                <td>
                                    <?= $this->Form->control('semester', [
                                        'label' => false,
                                        'type' => 'select',
                                        'options' => ['I' => 'I', 'II' => 'II', 'III' => 'III'],
                                        'default' => isset($selected_semester) ? $selected_semester : '',
                                        'empty' => '--select semester--',
                                        'style' => 'width:150px'
                                    ]) ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <td colspan="6">
                                <?= $this->Form->button(__('Search'), [
                                    'type' => 'submit',
                                    'name' => 'search',
                                    'value' => 'search',
                                    'class' => 'btn btn-primary btn-sm'
                                ]) ?>
                            </td>
                        </tr>
                    </table>

                    <?php if (isset($sections_array)): ?>
                        <?php
                        $dropdown_data_array = [];
                        foreach ($sections_array as $sak => $sav) {
                            foreach ($sav as $sv) {
                                $dropdown_data_array[$sak][$sv['published_course_id']] = sprintf(
                                    '%s (%s - Cr.%s (L T L - %s))',
                                    $sv['course_title'],
                                    $sv['course_code'],
                                    $sv['credit'],
                                    $sv['credit_detail']
                                );
                            }
                        }
                        ?>
                        <table class="table table-bordered">
                            <tr>
                                <td class="font">
                                    <?= $this->Form->control('courses', [
                                        'id' => 'ajax_course',
                                        'type' => 'select',
                                        'empty' => '---Please Select Course---',
                                        'options' => $dropdown_data_array
                                    ]) ?>
                                </td>
                            </tr>
                            <tr>
                                <td id="ajax_course_type_session"></td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <?= $this->Form->button(__('Submit'), [
                                        'type' => 'submit',
                                        'name' => 'submit',
                                        'value' => 'submit',
                                        'class' => 'btn btn-primary btn-sm'
                                    ]) ?>
                                </td>
                            </tr>
                        </table>

                        <?php if (isset($PublishedCourseHistory_formatted_array)): ?>
                            <h4 class="smallheading"><?= __('Already Recorded Course Number of Session') ?></h4>
                            <?php foreach ($PublishedCourseHistory_formatted_array as $pchfk => $pchfv): ?>
                                <table class="table table-bordered" style="border: #CCC solid 1px">
                                    <tr>
                                        <td colspan="8" style="border-right: #CCC solid 1px"><?= h($pchfk) ?></td>
                                    </tr>
                                    <tr>
                                        <th style="border-right: #CCC solid 1px"><?= __('No.') ?></th>
                                        <th style="border-right: #CCC solid 1px"><?= __('Published Course') ?></th>
                                        <th style="border-right: #CCC solid 1px"><?= __('Course Code') ?></th>
                                        <th style="border-right: #CCC solid 1px"><?= __('Credit') ?></th>
                                        <th style="border-right: #CCC solid 1px"><?= __('L T L') ?></th>
                                        <th style="border-right: #CCC solid 1px"><?= __('Lecture Number of Session') ?></th>
                                        <th style="border-right: #CCC solid 1px"><?= __('Tutorial Number of Session') ?></th>
                                        <th style="border-right: #CCC solid 1px"><?= __('Lab Number of Session') ?></th>
                                    </tr>
                                    <?php $count = 1; ?>
                                    <?php foreach ($pchfv as $publishedcoursedata): ?>
                                        <tr>
                                            <td style="border-right: #CCC solid 1px"><?= h($count++) ?></td>
                                            <td style="border-right: #CCC solid 1px">
                                                <?= $this->Html->link(
                                                    h($publishedcoursedata['course_title']),
                                                    ['controller' => 'PublishedCourses', 'action' => 'view', $publishedcoursedata['course_id']]
                                                ) ?>
                                            </td>
                                            <td style="border-right: #CCC solid 1px"><?= h($publishedcoursedata['course_code']) ?></td>
                                            <td style="border-right: #CCC solid 1px"><?= h($publishedcoursedata['credit']) ?></td>
                                            <td style="border-right: #CCC solid 1px"><?= h($publishedcoursedata['credit_detail']) ?></td>
                                            <td style="border-right: #CCC solid 1px"><?= h($publishedcoursedata['lecture_number_of_session']) ?></td>
                                            <td style="border-right: #CCC solid 1px"><?= h($publishedcoursedata['tutorial_number_of_session']) ?></td>
                                            <td style="border-right: #CCC solid 1px"><?= h($publishedcoursedata['lab_number_of_session']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </table>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
    </div>
</div>
