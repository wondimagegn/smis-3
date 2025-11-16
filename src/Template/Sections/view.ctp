<?php
$this->assign('title', __('Section Details: %s', isset($section['Section']['name']) ? h($section['Section']['name'] . (isset($section['YearLevel']['name']) ? ' (' . $section['YearLevel']['name'] . ', ' . $section['Section']['academicyear'] . ')' : ' (Pre/1st) in ' . $section['Section']['academicyear'])) : ''));
?>

<div class="box">
    <div class="box-header bg-transparent">
        <h3 class="box-title" style="margin-top: 10px;">
            <i class="fa fa-info-circle" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;">
                <?= __('Section Details: %s', isset($section['Section']['name']) ? h($section['Section']['name'] . (isset($section['YearLevel']['name']) ? ' (' . $section['YearLevel']['name'] . ', ' . $section['Section']['academicyear'] . ')' : ' (Pre/1st) in ' . $section['Section']['academicyear'])) : '') ?>
            </span>
        </h3>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <hr style="margin-top: -15px;">
                <?php if (!empty($section)): ?>
                    <table class="table table-bordered">
                        <tbody>
                        <tr>
                            <td>
                                <span class="text-muted" style="font-weight: bold;"><?= __('Name:') ?></span> &nbsp;
                                <?= h($section['Section']['name']) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span class="text-muted" style="font-weight: bold;"><?= __('College:') ?></span> &nbsp;
                                <?= $this->Html->link(
                                    h($section['College']['name']),
                                    ['controller' => 'Colleges', 'action' => 'view', $section['College']['id']]
                                ) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span class="text-muted" style="font-weight: bold;"><?= __('Department:') ?></span> &nbsp;
                                <?= isset($section['Department']['name']) ?
                                    $this->Html->link(
                                        h($section['Department']['name']),
                                        ['controller' => 'Departments', 'action' => 'view', $section['Department']['id']]
                                    ) :
                                    __('Pre/Freshman') ?>
                            </td>
                        </tr>
                        <?php if (isset($section['Curriculum']['name']) && !empty($section['Curriculum']['name'])): ?>
                            <tr>
                                <td>
                                    <span class="text-muted" style="font-weight: bold;"><?= __('Curriculum:') ?></span> &nbsp;
                                    <?= $this->Html->link(
                                        h(ucwords(strtolower($section['Curriculum']['name'])) . ' - ' . $section['Curriculum']['year_introduced'] . ' (' . (count(explode('ECTS', $section['Curriculum']['type_credit'])) >= 2 ? 'ECTS' : 'Credit') . ')'),
                                        ['controller' => 'Curriculums', 'action' => 'view', $section['Curriculum']['id']]
                                    ) ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <td>
                                <span class="text-muted" style="font-weight: bold;"><?= __('Program:') ?></span> &nbsp;
                                <?= $this->Html->link(
                                    h($section['Program']['name']),
                                    ['controller' => 'Programs', 'action' => 'view', $section['Program']['id']]
                                ) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span class="text-muted" style="font-weight: bold;"><?= __('Program Type:') ?></span> &nbsp;
                                <?= $this->Html->link(
                                    h($section['ProgramType']['name']),
                                    ['controller' => 'ProgramTypes', 'action' => 'view', $section['ProgramType']['id']]
                                ) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span class="text-muted" style="font-weight: bold;"><?= __('Academic Year:') ?></span> &nbsp;
                                <?= h($section['Section']['academicyear']) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span class="text-muted" style="font-weight: bold;"><?= __('Year Level:') ?></span> &nbsp;
                                <?= isset($section['YearLevel']['name']) ? h($section['YearLevel']['name']) : __('Pre/1st') ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span class="text-muted" style="font-weight: bold;"><?= __('Archived:') ?></span> &nbsp;
                                <?= $section['Section']['archive'] == 1 ? __('Yes') : __('No') ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span class="text-muted" style="font-weight: bold;"><?= __('Created:') ?></span> &nbsp;
                                <?= $this->Time->format($section['Section']['created'], 'MMM d, yyyy h:mm a') ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span class="text-muted" style="font-weight: bold;"><?= __('Modified:') ?></span> &nbsp;
                                <?= $this->Time->format($section['Section']['modified'], 'MMM d, yyyy h:mm a') ?>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="col-md-12">
                        <div class="alert alert-danger">
                            <span style="margin-right: 15px;"></span>
                            <?= __('Section not found or you don\'t have the privilege to view the selected Section.') ?>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (!empty($section['Student'])): ?>
                    <hr>
                    <h6 class="text-muted"><?= __('Related Students') ?></h6>
                    <hr>
                    <div style="overflow-x:auto;">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th class="text-center"><?= __('#') ?></th>
                                <th class="text-center"><?= __('Full Name') ?></th>
                                <th class="text-center"><?= __('Student ID') ?></th>
                                <th class="text-center"><?= __('Sex') ?></th>
                                <th class="text-center"><?= __('College') ?></th>
                                <th class="text-center"><?= __('Department') ?></th>
                                <th class="text-center"><?= __('Program') ?></th>
                                <th class="text-center"><?= __('Program Type') ?></th>
                                <th class="text-center"><?= __('Email') ?></th>
                                <th class="text-center"><?= __('Mobile') ?></th>
                                <th class="text-center"><?= __('Actions') ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $count = 1; ?>
                            <?php foreach ($section['Student'] as $student): ?>
                                <tr>
                                    <td class="text-center"><?= h($count++) ?></td>
                                    <td class="text-center"><?= h($student['full_name']) ?></td>
                                    <td class="text-center"><?= h($student['studentnumber']) ?></td>
                                    <td class="text-center">
                                        <?= strcasecmp(trim($student['gender']), 'male') == 0 ? 'M' :
                                            (strcasecmp(trim($student['gender']), 'female') == 0 ? 'F' :
                                                h($student['gender'])) ?>
                                    </td>
                                    <td class="text-center"><?= h($student['College']['shortname']) ?></td>
                                    <td class="text-center"><?= isset($student['Department']['name']) ? h($student['Department']['name']) : __('Pre/Freshman') ?></td>
                                    <td class="text-center"><?= h($student['Program']['shortname']) ?></td>
                                    <td class="text-center"><?= h($student['ProgramType']['name']) ?></td>
                                    <td class="text-center"><?= h($student['email']) ?></td>
                                    <td class="text-center"><?= h($student['phone_mobile']) ?></td>
                                    <td class="text-center">
                                        <?= $this->Html->link(
                                            __('View'),
                                            ['controller' => 'Students', 'action' => 'view', $student['id']]
                                        ) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
