<?php
$this->assign('title', __('Move Student to Section'));
?>

<div class="row">
    <div class="col-md-12">
        <h6 class="text-muted" style="font-size: 14px;"><?= __('Select the target section to move the selected student:') ?></h6>
        <div class="row">
            <div class="col-md-12">
                <fieldset style="margin: 5px;">
                    <div class="col-md-6">
                        <?= $this->Form->create('Section', ['url' => ['controller' => 'Sections', 'action' => 'sectionMoveUpdate']]) ?>
                        <?= $this->Form->control('Selected_section_id', [
                            'label' => __('Target Section: '),
                            'id' => 'Selected_section_id',
                            'type' => 'select',
                            'options' => $sections,
                            'empty' => '[ Select Section ]',
                            'class' => 'form-control',
                            'style' => 'width: 90%;'
                        ]) ?>
                        <?= $this->Form->hidden('student_id', ['value' => $student_id]) ?>
                        <?= $this->Form->hidden('previous_section_id', ['value' => $previous_section_id]) ?>
                    </div>
                    <div class="col-md-6">
                        <br>
                        <?= $this->Form->button(__('Submit'), [
                            'type' => 'submit',
                            'class' => 'btn btn-primary btn-sm'
                        ]) ?>
                        <?= $this->Form->end() ?>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
</div>
<a class="close-reveal-modal">&#215;</a>
