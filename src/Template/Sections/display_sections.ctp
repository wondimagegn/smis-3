<?php
$role_id=$this->request->getSession()->read('Auth.User.role_id') ;
$this->assign('title', __('Display Sections: (%s)',
    ($this->request->getSession()->read('Auth.User.role_id') != ROLE_COLLEGE ? h($departmentname) : h($collegename))));
?>

<div class="box">
    <div class="box-header bg-transparent">
        <h3 class="box-title" style="margin-top: 10px;">
            <i class="fa fa-th-list" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;">
                <?= __('Display Sections: (%s)', ($this->request->getSession()->read('Auth.User.role_id') != ROLE_COLLEGE ? h($departmentname) : h($collegename))) ?>
            </span>
        </h3>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <div style="margin-top: -30px;"><hr></div>
                <?= $this->Form->create('Section', ['url' => ['controller' => 'Sections', 'action' => 'displaySections']]) ?>
                <fieldset style="padding-bottom: 0px; padding-top: 15px;">
                    <div class="row">
                        <div class="col-md-3">
                            <?= $this->Form->control('academicyear', [
                                'label' => __('Academic Year: '),
                                'options' => $acyear_array_data,
                                'required' => true,
                                'class' => 'form-control',
                                'style' => 'width: 90%;'
                            ]) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $this->Form->control('program_id', [
                                'label' => __('Program: '),
                                'required' => true,
                                'class' => 'form-control',
                                'style' => 'width: 90%;'
                            ]) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $this->Form->control('program_type_id', [
                                'label' => __('Program Type: '),
                                'required' => true,
                                'class' => 'form-control',
                                'style' => 'width: 90%;'
                            ]) ?>
                        </div>
                        <div class="col-md-3">
                            <?php if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_DEPARTMENT): ?>
                                <?= $this->Form->control('year_level_id', [
                                    'label' => __('Year Level: '),
                                    'empty' => '[ Select Year Level ]',
                                    'required' => true,
                                    'class' => 'form-control',
                                    'style' => 'width: 90%;'
                                ]) ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <hr>
                    <?= $this->Form->button(__('Search'), [
                        'type' => 'submit',
                        'name' => 'search',
                        'value' => 'search',
                        'class' => 'btn btn-primary btn-sm'
                    ]) ?>
                </fieldset>
                <hr>
                <?php if (!empty($sections)): ?>
                    <div class="col-md-12">
                    <br>
                    <?php if (!empty($studentsections)): ?>
                        <?php foreach ($studentsections as $k => $studentsection): ?>
                            <?php $students_per_section = count($studentsection['Student']); ?>
                            <div style="overflow-x:auto;">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <td colspan="4" style="vertical-align: middle; border-bottom: 2px solid #555; line-height: 1.5;">
                                                    <span style="font-size: 16px; font-weight: bold; margin-top: 25px;">
                                                        <?= __(
                                                            'Section: %s',
                                                            h($studentsection['Section']['name'] . ' ' . (isset($studentsection['YearLevel']['name']) ? '(' . $studentsection['YearLevel']['name'] : ($studentsection['Program']['id'] == PROGRAM_REMEDIAL ? '(Remedial' : '(Pre/1st')) . ', ' . $studentsection['Section']['academicyear'] . ')')
                                                        ) ?>
                                                    </span>
                                            <br>
                                            <span class="text-muted" style="padding-top: 13px; font-size: 13px; font-weight: bold;">
                                                        <?= (isset($studentsection['Department']) && !empty($studentsection['Department']['name'])
                                                            ? h($studentsection['Department']['name'])
                                                            : h($studentsection['College']['name']) . ($studentsection['Program']['id'] == PROGRAM_REMEDIAL ? ' - Remedial Program' : ' - Pre/Freshman')) ?>
                                                <?= isset($studentsection['Program']['name']) && !empty($studentsection['Program']['name']) ? __(' &nbsp; | &nbsp; %s', h($studentsection['Program']['name'])) : '' ?>
                                                <?= isset($studentsection['ProgramType']['name']) && !empty($studentsection['ProgramType']['name']) ? __(' &nbsp; | &nbsp; %s', h($studentsection['ProgramType']['name'])) : '' ?>
                                                        <br>
                                                    </span>
                                            <span class="text-muted" style="padding-top: 15px; font-size: 13px; font-weight: normal;">
                                                        <?= __('Curriculum: %s', !empty($sections_curriculum_name[$k]) ? h($sections_curriculum_name[$k]) : __('Pre/Freshman Section without Curriculum Attachment')) ?>
                                                        <br>
                                                        <?= __('Hosted: %s', $current_sections_occupation[$k] . ' ' . ($current_sections_occupation[$k] > 1 ? __('Students') : __('Student'))) ?>
                                                    </span>
                                        </td>
                                        <td style="text-align: right; vertical-align: middle; border-bottom: 2px solid #555;">
                                            <?= $students_per_section ? $this->Html->link(
                                                $this->Html->image('/img/xls-icon.gif', ['alt' => __('Export to Excel')]) . ' ' . __('Export to Excel'),
                                                ['action' => 'export', $studentsection['Section']['id']],
                                                ['escape' => false]
                                            ) : '' ?>
                                            &nbsp;&nbsp;&nbsp;&nbsp;
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-center"><?= __('#') ?></th>
                                        <th class="text-center"><?= __('Student Name') ?></th>
                                        <th class="text-center"><?= __('Sex') ?></th>
                                        <th class="text-center"><?= __('Student ID') ?></th>
                                        <th class="text-center"><?= __('Actions') ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $counter = 1; ?>
                                    <?php for ($i = 0; $i < $students_per_section; $i++): ?>
                                        <?php if ($studentsection['Student'][$i]['StudentsSection']['archive'] == 0): ?>
                                            <?php
                                            $isStudentRegisteredInThisSection = $this->Sections->CourseRegistrations->find()
                                                ->where([
                                                    'CourseRegistration.section_id' => $studentsection['Section']['id'],
                                                    'CourseRegistration.student_id' => $studentsection['Student'][$i]['id']
                                                ])
                                                ->count();
                                            ?>
                                            <tr>
                                                <td class="text-center"><?= h($counter++) ?></td>
                                                <td class="text-center">
                                                    <?= $this->Html->link(
                                                        h($studentsection['Student'][$i]['full_name']),
                                                        ['controller' => 'Students', 'action' => 'studentAcademicProfile', $studentsection['Student'][$i]['id']]
                                                    ) ?>
                                                </td>
                                                <td class="text-center">
                                                    <?= strcasecmp(trim($studentsection['Student'][$i]['gender']), 'male') == 0 ? 'M' :
                                                        (strcasecmp(trim($studentsection['Student'][$i]['gender']), 'female') == 0 ? 'F' :
                                                            h($studentsection['Student'][$i]['gender'])) ?>
                                                </td>
                                                <td class="text-center"><?= h($studentsection['Student'][$i]['studentnumber']) ?></td>
                                                <td class="text-center" id="ajax_student_<?= h($i) ?>_<?= h($k) ?>">
                                                    <?php if ($studentsection['Student'][$i]['graduated'] == 0 && !$isStudentRegisteredInThisSection): ?>
                                                        <?= $this->Html->link(
                                                            __('Delete'),
                                                            ['controller' => 'Sections', 'action' => 'deleteStudentforThisSection', $studentsection['Section']['id'], str_replace('/', '-', $studentsection['Student'][$i]['studentnumber'])],
                                                            ['confirm' => __(
                                                                'Are you sure you want to delete %s from "%s" section?',
                                                                h($studentsection['Student'][$i]['full_name'] . ' (' . str_replace('/', '-', $studentsection['Student'][$i]['studentnumber']) . ')'),
                                                                h($studentsection['Section']['name'])
                                                            )]
                                                        ) ?>
                                                    <?php else: ?>
                                                        <span class="text-muted"><?= __('Delete') ?></span>&nbsp;&nbsp;
                                                        <?= $this->Html->link(
                                                            __('Archive'),
                                                            ['controller' => 'Sections', 'action' => 'archiveUnarchiveStudentSection', $studentsection['Section']['id'], $studentsection['Student'][$i]['id'], 1],
                                                            ['confirm' => __(
                                                                'Are you sure you want to Archive %s from %s section?',
                                                                h($studentsection['Student'][$i]['full_name'] . ' (' . str_replace('/', '-', $studentsection['Student'][$i]['studentnumber']) . ')'),
                                                                h($studentsection['Section']['name'])
                                                            )]
                                                        ) ?>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td class="text-center">
                                            <?= $this->Html->link(
                                                __('Move'),
                                                '#',
                                                [
                                                    'data-animation' => 'fade',
                                                    'data-reveal-id' => 'myModalMove',
                                                    'data-reveal-ajax' => $this->Url->build(['controller' => 'Sections', 'action' => 'moveSelectedStudentSection', $studentsection['Section']['id']])
                                                ]
                                            ) ?>
                                        </td>
                                        <td class="text-center" id="ajax_student_<?= h($k) ?>">
                                            <?= $this->Html->link(
                                                __('Add'),
                                                '#',
                                                [
                                                    'data-animation' => 'fade',
                                                    'data-reveal-id' => 'myModalAdd',
                                                    'data-reveal-ajax' => $this->Url->build(['controller' => 'Sections', 'action' => 'addStudentSection', $studentsection['Section']['id']])
                                                ]
                                            ) ?>
                                        </td>
                                        <td colspan="2">&nbsp;</td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <br>
                        <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php elseif (empty($sections) && !$isbeforesearch): ?>
                    <div class="col-md-12">
                        <div class="alert alert-info" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
                            <span style="margin-right: 15px;"></span>
                            <?= __('No Active section is found with the search criteria. You can use "List Sections" to view Archived sections instead.') ?>
                        </div>
                    </div>
                <?php endif; ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>

<div id="myModalMove" class="reveal-modal" data-reveal></div>
<div id="myModalAdd" class="reveal-modal" data-reveal></div>
