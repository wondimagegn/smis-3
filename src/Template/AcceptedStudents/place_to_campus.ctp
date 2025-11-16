<?php
use Cake\I18n\I18n;

$this->set('title', __('Place Student To Campuses'));
?>

<div class="container">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="accepted-students-index">
                        <h2><?= __('Place Student To Campuses') ?></h2>
                        <?= $this->Form->create(null, ['type' => 'post', 'url' => ['action' => 'placeToCampus'], 'class' => 'form-horizontal']) ?>
                        <?php if (empty($showListGenerated) || empty($acceptedStudents)): ?>
                            <table class="table">
                                <tr>
                                    <td>
                                        <?= $this->Form->control('AcceptedStudent.academic_year', [
                                            'id' => 'academic-year',
                                            'label' => ['text' => __('Academic Year'), 'class' => 'control-label'],
                                            'type' => 'select',
                                            'options' => $academicYearList,
                                            'empty' => __('--Select Academic Year--'),
                                            'value' => $selectedAcademicYear ?? '',
                                            'class' => 'form-control'
                                        ]) ?>
                                    </td>
                                    <td>
                                        <?= $this->Form->control('AcceptedStudent.college_id', [
                                            'empty' => __('--Select College--'),
                                            'class' => 'form-control'
                                        ]) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?= $this->Form->control('AcceptedStudent.program_id', [
                                            'empty' => __('--Select Program--'),
                                            'class' => 'form-control'
                                        ]) ?>
                                    </td>
                                    <td>
                                        <?= $this->Form->control('AcceptedStudent.program_type_id', [
                                            'empty' => __('--Select Program Type--'),
                                            'class' => 'form-control'
                                        ]) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <?= $this->Form->button(__('Randomly Place To Campus'), ['name' => 'search', 'class' => 'btn btn-primary']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <?= $this->Form->button(__('Cancel The Assignment'), ['name' => 'cancel', 'class' => 'btn btn-primary']) ?>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        <?php endif; ?>
                        <?php if (empty($acceptedStudents) && !$isBeforeSearch): ?>
                            <div class="alert alert-info">
                                <span></span><?= __('No Accepted students without campus assignment in these selected criteria') ?>
                            </div>
                        <?php endif; ?>
                        <?= $this->Form->end() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
