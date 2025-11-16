<?php
$this->assign('title', __('List of Sections'));
?>

<div class="box">
    <div class="box-header bg-transparent">
        <h3 class="box-title" style="margin-top: 10px;">
            <i class="fa fa-th-list" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;">
                <?= __('List of Sections') ?>
            </span>
        </h3>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <?= $this->Form->create('Section', ['url' => ['controller' => 'Sections', 'action' => 'index']]) ?>
                <div style="margin-top: -30px;">
                    <hr>
                    <fieldset style="padding-bottom: 0px; padding-top: 15px;">
                        <div class="row">
                            <div class="col-md-3">
                                <?= $this->Form->control('academicyearSection', [
                                    'id' => 'academicyearSearch',
                                    'label' => __('Academic Year: '),
                                    'type' => 'select',
                                    'options' => $acyear_array_options,
                                    'default' => $selected_academic_year,
                                    'class' => 'form-control',
                                    'style' => 'width: 90%;'
                                ]) ?>
                            </div>
                            <div class="col-md-3">
                                <?= $this->Form->control('year_level_id', [
                                    'label' => __('Year Level: '),
                                    'empty' => '[ All Year Levels ]',
                                    'class' => 'form-control',
                                    'style' => 'width: 80%;'
                                ]) ?>
                            </div>
                            <div class="col-md-3">
                                <?= $this->Form->control('program_id', [
                                    'label' => __('Program: '),
                                    'empty' => '[ All Programs ]',
                                    'class' => 'form-control',
                                    'style' => 'width: 90%;'
                                ]) ?>
                            </div>
                            <div class="col-md-3">
                                <?= $this->Form->control('program_type_id', [
                                    'label' => __('Program Type: '),
                                    'empty' => '[ All Program Types ]',
                                    'class' => 'form-control',
                                    'style' => 'width: 90%;'
                                ]) ?>
                            </div>
                        </div>
                        <?php if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_COLLEGE && $onlyFreshman == 0): ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <?= $this->Form->control('department_id', [
                                        'label' => __('Department: '),
                                        'id' => 'ajax_department_id_section',
                                        'empty' => '[ All Departments ]',
                                        'class' => 'form-control',
                                        'style' => 'width: 90%;'
                                    ]) ?>
                                </div>
                                <div class="col-md-2">
                                    <?= $this->Form->control('section_name', [
                                        'label' => __('Section Name:'),
                                        'placeholder' => __('Leave empty or specify'),
                                        'default' => $name,
                                        'class' => 'form-control',
                                        'style' => 'width: 90%;'
                                    ]) ?>
                                </div>
                                <div class="col-md-2">
                                    <?= $this->Form->control('active', [
                                        'label' => __('Section Status: '),
                                        'id' => 'active',
                                        'type' => 'select',
                                        'options' => ['0' => 'Active', '1' => 'Archived'],
                                        'empty' => '[ All ]',
                                        'class' => 'form-control',
                                        'style' => 'width: 90%;'
                                    ]) ?>
                                </div>
                                <div class="col-md-2">
                                    <?= $this->Form->control('limit', [
                                        'id' => 'limit',
                                        'type' => 'number',
                                        'min' => 100,
                                        'max' => 500,
                                        'value' => $limit,
                                        'step' => 50,
                                        'label' => __('Limit: '),
                                        'class' => 'form-control',
                                        'style' => 'width: 85%;'
                                    ]) ?>
                                </div>
                            </div>
                        <?php elseif ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR && $onlyFreshman == 0): ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <?= $this->Form->control('department_id', [
                                        'label' => __('Department: '),
                                        'id' => 'ajax_department_id_section',
                                        'empty' => '[ All Assigned Departments ]',
                                        'class' => 'form-control',
                                        'style' => 'width: 90%;'
                                    ]) ?>
                                </div>
                                <div class="col-md-2">
                                    <?= $this->Form->control('section_name', [
                                        'label' => __('Section Name:'),
                                        'placeholder' => __('Leave empty or specify'),
                                        'default' => $name,
                                        'class' => 'form-control',
                                        'style' => 'width: 90%;'
                                    ]) ?>
                                </div>
                                <div class="col-md-2">
                                    <?= $this->Form->control('active', [
                                        'label' => __('Section Status: '),
                                        'id' => 'active',
                                        'type' => 'select',
                                        'options' => ['0' => 'Active', '1' => 'Archived'],
                                        'empty' => '[ All ]',
                                        'class' => 'form-control',
                                        'style' => 'width: 90%;'
                                    ]) ?>
                                </div>
                                <div class="col-md-2">
                                    <?= $this->Form->control('limit', [
                                        'id' => 'limit',
                                        'type' => 'number',
                                        'min' => 100,
                                        'max' => 500,
                                        'value' => $limit,
                                        'step' => 50,
                                        'label' => __('Limit: '),
                                        'class' => 'form-control',
                                        'style' => 'width: 85%;'
                                    ]) ?>
                                </div>
                            </div>
                        <?php elseif ($this->request->getSession()->read('Auth.User.role_id') == ROLE_DEPARTMENT): ?>
                            <div class="row">
                                <div class="col-md-3">
                                    <?= $this->Form->control('section_name', [
                                        'label' => __('Section Name:'),
                                        'placeholder' => __('Leave empty or specify'),
                                        'default' => $name,
                                        'class' => 'form-control',
                                        'style' => 'width: 90%;'
                                    ]) ?>
                                </div>
                                <div class="col-md-3">
                                    <?= $this->Form->control('active', [
                                        'label' => __('Section Status: '),
                                        'id' => 'active',
                                        'type' => 'select',
                                        'options' => ['0' => 'Active', '1' => 'Archived'],
                                        'empty' => '[ All ]',
                                        'class' => 'form-control',
                                        'style' => 'width: 90%;'
                                    ]) ?>
                                </div>
                                <div class="col-md-3">
                                    <?= $this->Form->control('limit', [
                                        'id' => 'limit',
                                        'type' => 'number',
                                        'min' => 100,
                                        'max' => 500,
                                        'value' => $limit,
                                        'step' => 50,
                                        'label' => __('Limit: '),
                                        'class' => 'form-control',
                                        'style' => 'width: 90%;'
                                    ]) ?>
                                </div>
                                <div class="col-md-3">&nbsp;</div>
                            </div>
                        <?php elseif ($onlyFreshman == 1): ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <?= $this->Form->control('college_id', [
                                        'label' => __('College: '),
                                        'empty' => '[ All Assigned Colleges ]',
                                        'class' => 'form-control',
                                        'style' => 'width: 90%;'
                                    ]) ?>
                                </div>
                                <div class="col-md-2">
                                    <?= $this->Form->control('section_name', [
                                        'label' => __('Section Name:'),
                                        'placeholder' => __('Leave empty or specify'),
                                        'default' => $name,
                                        'class' => 'form-control',
                                        'style' => 'width: 90%;'
                                    ]) ?>
                                </div>
                                <div class="col-md-2">
                                    <?= $this->Form->control('active', [
                                        'label' => __('Section Status: '),
                                        'id' => 'active',
                                        'type' => 'select',
                                        'options' => ['0' => 'Active', '1' => 'Archived'],
                                        'empty' => '[ All ]',
                                        'class' => 'form-control',
                                        'style' => 'width: 90%;'
                                    ]) ?>
                                </div>
                                <div class="col-md-2">
                                    <?= $this->Form->control('limit', [
                                        'id' => 'limit',
                                        'type' => 'number',
                                        'min' => 100,
                                        'max' => 500,
                                        'value' => $limit,
                                        'step' => 50,
                                        'label' => __('Limit: '),
                                        'class' => 'form-control',
                                        'style' => 'width: 85%;'
                                    ]) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?= $this->Form->hidden('page', ['value' => $this->request->getData('Section.page', null)]) ?>
                        <?= $this->Form->hidden('sort', ['value' => $this->request->getData('Section.sort', null)]) ?>
                        <?= $this->Form->hidden('direction', ['value' => $this->request->getData('Section.direction', null)]) ?>
                        <hr>
                        <?= $this->Form->button(__('Search'), [
                            'type' => 'submit',
                            'name' => 'search',
                            'class' => 'btn btn-primary btn-sm'
                        ]) ?>
                    </fieldset>
                </div>
                <hr>
                <br>
                <?php if (isset($sections) && !empty($sections)): ?>
                    <div style="overflow-x:auto;">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th class="text-center"><?= __('#') ?></th>
                                <th class="text-center"><?= __('Section') ?></th>
                                <th class="text-center"><?= __('Year Level') ?></th>
                                <th class="text-center"><?= __('Department') ?></th>
                                <th class="text-center"><?= __('Program') ?></th>
                                <th class="text-center"><?= __('Program Type') ?></th>
                                <th class="text-center"><?= __('ACY') ?></th>
                                <th class="text-center"><?= __('Actions') ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $start = $this->Paginator->counter(['format' => '%start%']); ?>
                            <?php foreach ($sections as $section): ?>
                                <tr>
                                    <td class="text-center"><?= h($start++) ?></td>
                                    <td class="text-center"><?= h($section['Section']['name']) ?></td>
                                    <td class="text-center">
                                        <?= isset($section['YearLevel']['name']) ? h($section['YearLevel']['name']) :
                                            ($section['Section']['program_id'] == PROGRAM_REMEDIAL ? __('Remedial') : __('Pre/1st')) ?>
                                    </td>
                                    <td class="text-center">
                                        <?= isset($section['Department']['name']) ? h($section['Department']['name']) :
                                            h($section['College']['shortname']) . ($section['Section']['program_id'] == PROGRAM_REMEDIAL ? ' - Remedial' : ' - Pre/Freshman') ?>
                                    </td>
                                    <td class="text-center"><?= h($section['Program']['shortname']) ?></td>
                                    <td class="text-center"><?= h($section['ProgramType']['name']) ?></td>
                                    <td class="text-center"><?= h($section['Section']['academicyear']) ?></td>
                                    <td class="text-center">
                                        <?= $this->Html->link(
                                            '',
                                            ['action' => 'view', $section['Section']['id']],
                                            ['class' => 'fa fa-eye', 'title' => __('View')]
                                        ) ?>
                                        &nbsp;
                                        <?php if (!$section['Section']['archive']): ?>
                                            <?php if ((is_null($section['Section']['department_id']) || !isset($section['Section']['department_id'])) &&
                                                $this->request->getSession()->read('Auth.User.role_id') == ROLE_COLLEGE &&
                                                $this->request->getSession()->read('Auth.User.is_admin') == 1): ?>
                                                <?= $this->Html->link(
                                                    '',
                                                    ['action' => 'edit', $section['Section']['id']],
                                                    ['class' => 'fa fa-pencil', 'title' => __('Edit')]
                                                ) ?>
                                                &nbsp;
                                                <?= $this->Html->link(
                                                    '',
                                                    ['action' => 'delete', $section['Section']['id']],
                                                    [
                                                        'class' => 'fa fa-trash',
                                                        'title' => __('Delete'),
                                                        'confirm' => __('Are you sure you want to delete %s section?', $section['Section']['name'])
                                                    ]
                                                ) ?>
                                            <?php elseif ($this->request->getSession()->read('Auth.User.role_id') == ROLE_DEPARTMENT &&
                                                isset($section['Section']['department_id']) &&
                                                $this->request->getSession()->read('Auth.User.is_admin') == 1): ?>
                                                <?= $this->Html->link(
                                                    '',
                                                    ['action' => 'edit', $section['Section']['id']],
                                                    ['class' => 'fa fa-pencil', 'title' => __('Edit')]
                                                ) ?>
                                                &nbsp;
                                                <?= $this->Html->link(
                                                    '',
                                                    ['action' => 'delete', $section['Section']['id']],
                                                    [
                                                        'class' => 'fa fa-trash',
                                                        'title' => __('Delete'),
                                                        'confirm' => __('Are you sure you want to delete %s section?', $section['Section']['name'])
                                                    ]
                                                ) ?>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <br>
                    <hr>
                    <div class="row">
                        <div class="col-md-5">
                            <?= $this->Paginator->counter(['format' => __('Page %page% of %pages%, showing %current% records out of %count% total')]) ?>
                        </div>
                        <div class="col-md-7 text-center">
                            <ul class="pagination">
                                <?= $this->Paginator->prev('«', ['tag' => 'li'], null, ['class' => 'page-item disabled']) ?>
                                <?= $this->Paginator->numbers(['tag' => 'li', 'currentTag' => 'span', 'currentClass' => 'page-item active']) ?>
                                <?= $this->Paginator->next('»', ['tag' => 'li'], null, ['class' => 'page-item disabled']) ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>
