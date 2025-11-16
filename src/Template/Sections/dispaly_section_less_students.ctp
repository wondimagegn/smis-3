<?php
$this->assign('title', __('Sectionless Students List (%s)', ($this->request->getSession()->read('Auth.User.role_id') != ROLE_COLLEGE ? h($department_name) : h($college_name))));
?>

<div class="box">
    <div class="box-header bg-transparent">
        <h3 class="box-title" style="margin-top: 10px;">
            <i class="fa fa-th-list" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;">
                <?= __('Sectionless Students List (%s)', ($this->request->getSession()->read('Auth.User.role_id') != ROLE_COLLEGE ? h($department_name) : h($college_name))) ?>
            </span>
        </h3>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <div style="margin-top: -30px;"></div>
                <hr>
                <blockquote>
                    <h6><i class="fa fa-info"></i> &nbsp; <?= __('Important Note:') ?></h6>
                    <p style="text-align: justify;">
                        <span class="text-dark" style="font-size: 14px;">
                            <?= __('Note that Sectionless Students list doesn\'t include graduated, disciplinary dismissed, or drop out students. It only lists students, including readmitted ones, who are eligible for Section Assignment (not assigned to any active section currently).') ?>
                        </span>
                    </p>
                </blockquote>
                <hr>
                <?= $this->Form->create('Section', ['url' => ['controller' => 'Sections', 'action' => 'displaySectionLessStudents']]) ?>
                <fieldset style="padding-bottom: 10px; padding-top: 15px;">
                    <div class="row">
                        <div class="col-md-3">
                            <?= $this->Form->control('academicyear', [
                                'options' => $acyear_array_data,
                                'required' => true,
                                'class' => 'form-control',
                                'style' => 'width: 90%;'
                            ]) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $this->Form->control('program_id', [
                                'required' => true,
                                'class' => 'form-control',
                                'style' => 'width: 90%;'
                            ]) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $this->Form->control('program_type_id', [
                                'required' => true,
                                'class' => 'form-control',
                                'style' => 'width: 90%;'
                            ]) ?>
                        </div>
                        <div class="col-md-3">
                            <br>
                            <?= $this->Form->button(__('Search'), [
                                'type' => 'submit',
                                'name' => 'search',
                                'value' => 'search',
                                'class' => 'btn btn-primary btn-sm'
                            ]) ?>
                        </div>
                    </div>
                </fieldset>
                <hr>
                <?php if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_DEPARTMENT): ?>
                    <?php if (!empty($sectionless_students_last_sections_details)): ?>
                        <div style="overflow-x:auto;">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th class="text-center"><?= __('#') ?></th>
                                    <th class="text-center"><?= __('Full Name') ?></th>
                                    <th class="text-center"><?= __('Student ID') ?></th>
                                    <th class="text-center"><?= __('Last Section') ?></th>
                                    <th class="text-center"><?= __('ACY') ?></th>
                                    <th class="text-center"><?= __('Year Level') ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $count = 1; ?>
                                <?php foreach ($sectionless_students_last_sections_details as $sslsdv): ?>
                                    <tr>
                                        <td class="text-center"><?= h($count++) ?></td>
                                        <td class="text-center">
                                            <?php if (isset($sslsdv['Student']) && !empty($sslsdv['Student'])): ?>
                                                <?= $this->Html->link(
                                                    h($sslsdv['Student'][0]['full_name']),
                                                    ['controller' => 'Students', 'action' => 'studentAcademicProfile', $sslsdv['Student'][0]['id']]
                                                ) ?>
                                            <?php else: ?>
                                                <?php debug($sslsdv); ?>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center"><?= isset($sslsdv['Student']) && !empty($sslsdv['Student']) ? h($sslsdv['Student'][0]['studentnumber']) : '' ?></td>
                                        <td class="text-center"><?= h($sslsdv['Section']['name']) ?></td>
                                        <td class="text-center"><?= h($sslsdv['Section']['academicyear']) ?></td>
                                        <td class="text-center"><?= h($sslsdv['YearLevel']['name']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <br>
                    <?php elseif (empty($sectionless_students_last_sections_details) && !$isbeforesearch): ?>
                        <div class="alert alert-info" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
                            <span style="margin-right: 15px;"></span>
                            <?= __('No Sectionless Student is found with the given search criteria.') ?>
                        </div>
                    <?php endif; ?>
                <?php elseif ($this->request->getSession()->read('Auth.User.role_id') == ROLE_COLLEGE): ?>
                    <?php if (!empty($sectionless_students_last_sections_details)): ?>
                        <div style="overflow-x:auto;">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th class="text-center"><?= __('#') ?></th>
                                    <th class="text-center"><?= __('Full Name') ?></th>
                                    <th class="text-center"><?= __('Student ID') ?></th>
                                    <th class="text-center"><?= __('Last Section') ?></th>
                                    <th class="text-center"><?= __('ACY') ?></th>
                                    <th class="text-center"><?= __('Year Level') ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $count = 1; ?>
                                <?php foreach ($sectionless_students_last_sections_details as $sslsdv): ?>
                                    <tr>
                                        <td class="text-center"><?= h($count++) ?></td>
                                        <td class="text-center">
                                            <?= $this->Html->link(
                                                h($sslsdv['Student'][0]['full_name']),
                                                ['controller' => 'Students', 'action' => 'studentAcademicProfile', $sslsdv['Student'][0]['id']]
                                            ) ?>
                                        </td>
                                        <td class="text-center"><?= h($sslsdv['Student'][0]['studentnumber']) ?></td>
                                        <td class="text-center"><?= h($sslsdv['Section']['name']) ?></td>
                                        <td class="text-center"><?= h($sslsdv['Section']['academicyear']) ?></td>
                                        <td class="text-center"><?= !empty($sslsdv['YearLevel']['name']) ? h($sslsdv['YearLevel']['name']) : __('Pre/1st') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <br>
                    <?php elseif (empty($sectionless_students_last_sections_details) && !$isbeforesearch): ?>
                        <div class="alert alert-info" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
                            <span style="margin-right: 15px;"></span>
                            <?= __('No Sectionless Student is found with the given search criteria.') ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>
