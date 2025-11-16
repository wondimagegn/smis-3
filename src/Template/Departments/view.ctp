<?php
$this->assign('title', __('Department Details: ' . ($department->name . ($department->shortname ? ' (' . $department->shortname . ')' : ''))));

?>

<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;">
            <i class="fontello-info-outline"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;">
                <?= __('Department Details: ' . ($department->name . ($department->shortname ? ' (' . $department->shortname . ')' : ''))) ?>
            </span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <div style="margin-top: -30px;">
                    <hr>
                    <table cellpadding="0" cellspacing="0" class="table">
                        <tbody>
                        <tr>
                            <td><span class="text-gray" style="font-weight: bold;">Name:</span> &nbsp;&nbsp; <?= h($department->name) ?></td>
                        </tr>
                        <tr>
                            <td><span class="text-gray" style="font-weight: bold;">Short Name:</span> &nbsp;&nbsp; <?= h($department->shortname) ?></td>
                        </tr>
                        <tr>
                            <td><span class="text-gray" style="font-weight: bold;">Institution Code:</span> &nbsp;&nbsp; <?= h($department->institution_code ?? '---') ?></td>
                        </tr>
                        <tr>
                            <td><span class="text-gray" style="font-weight: bold;">Active:</span> &nbsp;&nbsp; <?= $department->active ? 'Yes' : 'No' ?></td>
                        </tr>
                        <tr>
                            <td><span class="text-gray" style="font-weight: bold;">Year Level Curriculum Definition Allowed:</span> &nbsp;&nbsp; <?= $department->allow_year_based_curriculums ? 'Yes' : 'No' ?></td>
                        </tr>
                        <tr>
                            <td><span class="text-gray" style="font-weight: bold;">Moodle Category ID:</span> &nbsp;&nbsp; <?= h($department->moodle_category_id ?? 'N/A') ?></td>
                        </tr>
                        <?php if (!empty($department->created) && $department->created != '0000-00-00') { ?>
                            <tr>
                                <td><span class="text-gray" style="font-weight: bold;">Created on:</span> &nbsp;&nbsp; <?= $this->Time->format($department->created, 'MMMM d, YYYY') ?></td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td><span class="text-gray" style="font-weight: bold;">Modified on:</span> &nbsp;&nbsp; <?= $this->Time->format($department->modified, 'MMMM d, YYYY') ?></td>
                        </tr>
                        <tr>
                            <td><span class="text-gray" style="font-weight: bold;">Located at:</span> &nbsp;&nbsp;
                                <?= $this->Html->link(
                                    h($department->college->name . ' (' . $department->college->campus->name . ')'),
                                    ['controller' => 'Campuses', 'action' => 'view', $department->college->campus->id]
                                ) ?>
                            </td>
                        </tr>
                        <?php if (!empty($department->description)) { ?>
                            <tr>
                                <td>
                                    <span class="text-gray" style="font-weight: bold;">Description:</span>
                                    <p class="fs14" style="text-align: justify; margin: 10px"> <?= h($department->description) ?></p>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <div class="related">
                        <hr>
                        <h6 class="text-gray">Related Grade Scales (Department Level: <?= h($department->name) ?>)</h6>
                        <br>
                        <?php if (!empty($department->grade_scales)) { ?>
                            <div style="overflow-x:auto;">
                                <table cellpadding="0" cellspacing="0" class="table">
                                    <thead>
                                    <tr>
                                        <td class="center">#</td>
                                        <td class="vcenter"><?= __('Name') ?></td>
                                        <td class="vcenter"><?= __('Grade Type') ?></td>
                                        <td class="center"><?= __('Program') ?></td>
                                        <td class="center"><?= __('Own') ?></td>
                                        <td class="center"><?= __('One-Time') ?></td>
                                        <td class="center"><?= __('Active') ?></td>
                                        <td class="center"><?= __('Actions') ?></td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $count1 = 1; foreach ($department->grade_scales as $gradeScale) { ?>
                                        <tr>
                                            <td class="center"><?= $count1++ ?></td>
                                            <td class="vcenter"><?= h($gradeScale->name) ?></td>
                                            <td class="vcenter"><?= h($gradeScale->grade_type->type ?? '---') ?></td>
                                            <td class="center"><?= h($gradeScale->program->name) ?></td>
                                            <td class="center"><?= $gradeScale->own ? '<span style="color:green">Yes</span>' : '<span style="color:red">No</span>' ?></td>
                                            <td class="center"><?= $gradeScale->one_time ? '<span style="color:green">Yes</span>' : '<span style="color:red">No</span>' ?></td>
                                            <td class="center"><?= $gradeScale->active ? '<span style="color:green">Yes</span>' : '<span style="color:red">No</span>' ?></td>
                                            <td class="center">
                                                <?= $this->Html->link(
                                                    __(''),
                                                    ['controller' => 'GradeScales', 'action' => 'view', $gradeScale->id],
                                                    ['class' => 'fontello-eye', 'title' => 'View']
                                                ) ?>
                                                &nbsp;
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            <br>
                        <?php } ?>
                    </div>
                    <div class="related">
                        <hr>

                        <h6 class="text-gray">Related Grade Scales (College Level:
                            <?= h($college_level_defined_grade_scales->name) ?>)</h6>
                        <br>
                        <?php if (!empty($college_level_defined_grade_scales->grade_scales)) { ?>
                            <div style="overflow-x:auto;">
                                <table cellpadding="0" cellspacing="0" class="table">
                                    <thead>
                                    <tr>
                                        <td class="center">#</td>
                                        <td class="vcenter"><?= __('Name') ?></td>
                                        <td class="vcenter"><?= __('Grade Type') ?></td>
                                        <td class="center"><?= __('Program') ?></td>
                                        <td class="center"><?= __('Own') ?></td>
                                        <td class="center"><?= __('One-Time') ?></td>
                                        <td class="center"><?= __('Active') ?></td>
                                        <td class="center"><?= __('Actions') ?></td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $count1 = 1; foreach ($college_level_defined_grade_scales->grade_scales as $gradeScale) { ?>
                                        <tr>
                                            <td class="center"><?= $count1++ ?></td>
                                            <td class="vcenter"><?= h($gradeScale->name) ?></td>
                                            <td class="vcenter"><?= h($gradeScale->grade_type->type ?? '---') ?></td>
                                            <td class="center"><?= h($gradeScale->program->name) ?></td>
                                            <td class="center"><?= $gradeScale->own ? '<span style="color:green">Yes</span>' : '<span style="color:red">No</span>' ?></td>
                                            <td class="center"><?= $gradeScale->one_time ? '<span style="color:green">Yes</span>' : '<span style="color:red">No</span>' ?></td>
                                            <td class="center"><?= $gradeScale->active ? '<span style="color:green">Yes</span>' : '<span style="color:red">No</span>' ?></td>
                                            <td class="center">
                                                <?= $this->Html->link(
                                                    __(''),
                                                    ['controller' => 'GradeScales', 'action' => 'view', $gradeScale->id],
                                                    ['class' => 'fontello-eye', 'title' => 'View']
                                                ) ?>
                                                &nbsp;
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            <br>
                        <?php } ?>
                    </div>
                    <div class="related">
                        <hr>
                        <h6 class="text-gray"><?= __('Related Staffs') ?></h6>
                        <br>
                        <?php

                        if (!empty($department->staffs)) { ?>
                            <div style="overflow-x:auto;">
                                <table cellpadding="0" cellspacing="0" class="table">
                                    <thead>
                                    <tr>
                                        <td class="center">#</td>
                                        <td class="center"><?= __('Title') ?></td>
                                        <td class="vcenter"><?= __('First Name') ?></td>
                                        <td class="vcenter"><?= __('Middle Name') ?></td>
                                        <td class="vcenter"><?= __('Last Name') ?></td>
                                        <td class="vcenter"><?= __('Position') ?></td>
                                        <td class="vcenter"><?= __('Department') ?></td>
                                        <td class="center"><?= __('Active') ?></td>
                                        <td class="center"><?= __('Actions') ?></td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $i = 1; foreach ($department->staffs as $staff) { ?>
                                        <tr>
                                            <td class="center"><?= $i++ ?></td>
                                            <td class="center"><?= h($staff->title->title ?? '') . (isset($staff->title->title) ? '.' : '') ?></td>
                                            <td class="vcenter"><?= h($staff->first_name) ?></td>
                                            <td class="vcenter"><?= h($staff->middle_name) ?></td>
                                            <td class="vcenter"><?= h($staff->last_name) ?></td>
                                            <td class="vcenter"><?= h($staff->position->position ?? '') ?></td>
                                            <td class="vcenter"><?= h($staff->department->name ?? '') ?></td>
                                            <td class="center"><?= $staff->active ? '<span style="color:green">Yes</span>' : '<span style="color:red">No</span>' ?></td>
                                            <td class="center">
                                                <?= $this->Html->link(
                                                    __(''),
                                                    ['controller' => 'Staffs', 'action' => 'view', $staff->id],
                                                    ['class' => 'fontello-eye', 'title' => 'View']
                                                ) ?>
                                                &nbsp;
                                                <?php if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_SYSADMIN) { ?>
                                                    <?= $this->Html->link(
                                                        __(''),
                                                        ['controller' => 'Staffs', 'action' => 'edit', $staff->id],
                                                        ['class' => 'fontello-pencil', 'title' => 'Edit']
                                                    ) ?>
                                                    &nbsp;
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            <br>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
