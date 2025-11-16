<?php
$this->assign('title', __('Edit Section Details: %s', isset($section['Section']['name']) ? h($section['Section']['name'] . (isset($section['YearLevel']['name']) ? ' (' . $section['YearLevel']['name'] . ', ' . $section['Section']['academicyear'] . ')' : ' (Pre/1st) in ' . $section['Section']['academicyear'])) : ''));
?>

<div class="box">
    <div class="box-header bg-transparent">
        <h3 class="box-title" style="margin-top: 10px;">
            <i class="fa fa-edit" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;">
                <?= __('Edit Section Details: %s', isset($section['Section']['name']) ? h($section['Section']['name'] . (isset($section['YearLevel']['name']) ? ' (' . $section['YearLevel']['name'] . ', ' . $section['Section']['academicyear'] . ')' : ' (Pre/1st) in ' . $section['Section']['academicyear'])) : '') ?>
            </span>
        </h3>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <?php if (!empty($section)): ?>
                    <div style="margin-top: -30px;">
                        <?= $this->Form->create('Section', ['url' => ['controller' => 'Sections', 'action' => 'edit', $section['Section']['id']]]) ?>
                        <fieldset style="padding-bottom: 0px;">
                            <div class="row">
                                <div class="col-md-3">
                                    <?= $this->Form->control('id') ?>
                                    <?= $this->Form->control('name', [
                                        'value' => trim($this->request->getData('Section.name', '')),
                                        'type' => 'text',
                                        'maxlength' => 30,
                                        'class' => 'form-control',
                                        'style' => 'width: 90%;'
                                    ]) ?>
                                </div>
                                <div class="col-md-3">
                                    <?= $this->Form->control('program_id', [
                                        'disabled' => true,
                                        'class' => 'form-control',
                                        'style' => 'width: 90%;'
                                    ]) ?>
                                </div>
                                <div class="col-md-3">
                                    <?= $this->Form->control('program_type_id', [
                                        'disabled' => true,
                                        'class' => 'form-control',
                                        'style' => 'width: 90%;'
                                    ]) ?>
                                </div>
                                <div class="col-md-3">
                                    <?php if (!empty($this->request->getData('Section.department_id'))): ?>
                                        <?= $this->Form->control('department_id', [
                                            'disabled' => true,
                                            'class' => 'form-control',
                                            'style' => 'width: 90%;'
                                        ]) ?>
                                    <?php elseif (!empty($this->request->getData('Section.college_id'))): ?>
                                        <?= $this->Form->control('college_id', [
                                            'disabled' => true,
                                            'class' => 'form-control',
                                            'style' => 'width: 90%;'
                                        ]) ?>
                                    <?php endif; ?>
                                    <?= $this->Form->hidden('year_level_id') ?>
                                    <?= $this->Form->hidden('academicyear') ?>
                                    <?= $this->Form->hidden('program_id') ?>
                                    <?= $this->Form->hidden('program_type_id') ?>
                                </div>
                            </div>
                            <hr>
                            <?= $this->Form->button(__('Submit'), [
                                'type' => 'submit',
                                'name' => 'submit',
                                'class' => 'btn btn-primary btn-sm'
                            ]) ?>
                        </fieldset>
                        <?= $this->Form->end() ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
                        <span style="margin-right: 15px;"></span>
                        <?= __('Section not found or you don\'t have the privilege to view the selected Section.') ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
