<?php
$this->assign('title', __('Upgrade Student Section'));
?>

<div class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-6">
                <?= $this->Form->create('Section', ['url' => ['controller' => 'Sections',
                    'action' => 'sectionMoveUpdate']]) ?>
                <?= $this->Form->control('Selected_section_id', [
                    'label' => false,
                    'id' => 'Selected_section_id',
                    'type' => 'select',
                    'options' => $sections,
                    'empty' => '--Select Section--',
                    'class' => 'form-control'
                ]) ?>
                <?= $this->Form->hidden('previous_section_id', ['value' => $previous_section_id]) ?>
            </div>
            <div class="col-md-6">
                <?= $this->Form->button(__('Move'), [
                    'type' => 'submit',
                    'name' => 'attach',
                    'class' => 'btn btn-primary btn-sm'
                ]) ?>
            </div>
        </div>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th colspan="5" class="font-weight-bold">
                    <?= __(
                        '%s-%s (%s-%s-%s)',
                        h($previousSectionName['Section']['name']),
                        h($previousSectionName['YearLevel']['name']),
                        h($previousSectionName['Program']['name']),
                        h($previousSectionName['ProgramType']['name']),
                        h($previousSectionName['Department']['name'])
                    ) ?>
                </th>
            </tr>
            <tr>
                <th class="text-center"><?= __('No.') ?></th>
                <th class="text-center"><?= __('Student Number') ?></th>
                <th class="text-center"><?= __('Name') ?></th>
                <th class="text-center"><?= __('Sex') ?></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="text-center">1</td>
                <?= $this->Form->hidden('Section.1.selected_id', ['value' => 1]) ?>
                <?= $this->Form->hidden('Section.1.student_id', ['value' => $students['Student']['id']]) ?>
                <td class="text-center"><?= h($students['Student']['studentnumber']) ?></td>
                <td class="text-center"><?= h($students['Student']['full_name']) ?></td>
                <td class="text-center"><?= h(ucfirst(strtolower(trim($students['Student']['gender'])))) ?></td>
            </tr>
            </tbody>
        </table>
        <?= $this->Form->end() ?>
    </div>
</div>
<a class="close-reveal-modal">&#215;</a>
