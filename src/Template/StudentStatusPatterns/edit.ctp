<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\StudentStatusPattern $studentStatusPattern
 * @var array $programs
 * @var array $programTypes
 * @var array $acYearList
 */
?>
<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;">
            <i class="fontello-edit" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;">
                <?= __('Edit Student Status Pattern') ?>
                <?= isset($studentStatusPattern->program->name) ? __(' for ') .
                    h($studentStatusPattern->program->name) .
                    (isset($studentStatusPattern->program_type->name) ? ', ' .
                        h($studentStatusPattern->program_type->name) : '') : '' ?>

            </span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <div style="margin-top: -30px;"><hr></div>
                <blockquote>
                    <h6><i class="fa fa-info"></i> &nbsp; <?= __('Important Note:') ?></h6>
                    <span style="text-align:justify;" class="fs16 text-black">
                        <?= __('The status pattern helps to display students\' academic status on their grade report by considering their program and program type. The academic year start specifies from which academic year the pattern begins and the pattern specifies how many semesters should be used for status determination.') ?>
                    </span>
                </blockquote>
                <hr>
                <div class="studentStatusPatterns form">
                    <fieldset style="padding-top: 15px; padding-bottom: 0px;">
                        <?= $this->Form->create($studentStatusPattern, ['url' => ['controller' => 'StudentStatusPatterns', 'action' => 'edit', $studentStatusPattern->id]]) ?>
                        <?= $this->Form->control('id', ['type' => 'hidden']) ?>
                        <div class="row">
                            <div class="large-3 columns">
                                <?= $this->Form->control('program_id', [
                                    'label' => __('Program:'),
                                    'style' => 'width: 90%;',
                                    'options' => $programs,
                                    'empty' => __('-- Select Program --')
                                ]) ?>
                            </div>
                            <div class="large-3 columns">
                                <?= $this->Form->control('program_type_id', [
                                    'label' => __('Program Type:'),
                                    'style' => 'width: 90%;',
                                    'options' => $programTypes,
                                    'empty' => __('-- Select Program Type --')
                                ]) ?>
                            </div>
                            <div class="large-3 columns">
                                <?= $this->Form->control('acadamic_year', [
                                    'label' => __('Starting From:'),
                                    'style' => 'width: 90%;',
                                    'options' => $acYearList,
                                    'empty' => __('-- Select Academic Year --')
                                ]) ?>
                            </div>
                            <div class="large-3 columns">
                                <?= $this->Form->control('application_date', [
                                    'label' => __('Application Date:'),
                                    'style' => 'width: 30%;',
                                    'type' => 'date',
                                    'minYear' => APPLICATION_START_YEAR,
                                    'maxYear' => date('Y'),
                                    'orderYear' => 'desc'
                                ]) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="large-3 columns">
                                <?= $this->Form->control('pattern', [
                                    'label' => __('Pattern: (No of Semesters)'),
                                    'style' => 'width: 90%;',
                                    'options' => [1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5],
                                    'empty' => __('-- Select Pattern --')
                                ]) ?>
                            </div>
                            <div class="large-6 columns">
                                <?= $this->Form->control('description', [
                                    'label' => __('Description:'),
                                    'style' => 'width: 95%;'
                                ]) ?>
                            </div>
                            <div class="large-3 columns">
                                &nbsp;
                            </div>
                        </div>
                        <hr>
                        <?= $this->Form->button(__('Save Changes'),
                            ['class' => 'tiny radius button bg-blue']) ?>
                        <?= $this->Form->end() ?>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
</div>
