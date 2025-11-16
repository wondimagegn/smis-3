<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\StudentStatusPattern[]|\Cake\Datasource\ResultSetInterface $studentStatusPatterns
 * @var array $programs
 * @var array $programTypes
 * @var array $acyearArrayData
 * @var string $defaultAcademicYear
 * @var array $yearLevels
 */
?>
<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;">
            <i class="fontello-list" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Student Status Patterns') ?></span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <div style="margin-top: -30px;"><hr></div>
                <div class="studentStatusPatterns index">
                    <h3><?= __('Student Status Patterns') ?></h3>
                    <table cellpadding="0" cellspacing="0" class="table table-striped">
                        <thead>
                        <tr>
                            <th scope="col"><?= $this->Paginator->sort('id', __('ID')) ?></th>
                            <th scope="col"><?= $this->Paginator->sort('program_id', __('Program')) ?></th>
                            <th scope="col"><?= $this->Paginator->sort('program_type_id', __('Program Type')) ?></th>
                            <th scope="col"><?= $this->Paginator->sort('academic_year', __('Academic Year')) ?></th>
                            <th scope="col"><?= $this->Paginator->sort('application_date', __('Application Date')) ?></th>
                            <th scope="col"><?= $this->Paginator->sort('pattern', __('Pattern')) ?></th>
                            <th scope="col"><?= $this->Paginator->sort('description', __('Description')) ?></th>
                            <th scope="col" class="actions"><?= __('Actions') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($studentStatusPatterns as $studentStatusPattern):
                            ?>
                            <tr>
                                <td><?= h($studentStatusPattern->id) ?></td>
                                <td><?= isset($studentStatusPattern->program->name) ? h($studentStatusPattern->program->name) : '' ?></td>
                                <td><?= isset($studentStatusPattern->program_type->name) ? h($studentStatusPattern->program_type->name) : '' ?></td>
                                <td><?= h($studentStatusPattern->acadamic_year) ?></td>
                                <td class="center"><?= $studentStatusPattern->application_date->nice('M j, Y') ?></td>
                                <td><?= h($studentStatusPattern->pattern) ?></td>
                                <td><?= h($studentStatusPattern->description) ?></td>
                                <td class="actions">
                                    <?= $this->Html->link(__('View'), ['action' => 'view', $studentStatusPattern->id], ['class' => 'tiny radius button bg-blue']) ?>
                                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $studentStatusPattern->id], ['class' => 'tiny radius button bg-green']) ?>
                                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $studentStatusPattern->id], ['confirm' => __('Are you sure you want to delete # {0}?', $studentStatusPattern->id), 'class' => 'tiny radius button bg-red']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="paginator">
                        <ul class="pagination">
                            <?= $this->Paginator->first('<< ' . __('first')) ?>
                            <?= $this->Paginator->prev('< ' . __('previous')) ?>
                            <?= $this->Paginator->numbers() ?>
                            <?= $this->Paginator->next(__('next') . ' >') ?>
                            <?= $this->Paginator->last(__('last') . ' >>') ?>
                        </ul>
                        <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
