<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Help $help
 * @var array $roles
 */
?>
<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;">
            <i class="fontello-plus" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Add Latest Released Help Document') ?></span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <div style="margin-top: -30px;"><hr></div>
                <?= $this->Form->create($help, ['type' => 'file', 'url' => ['action' => 'add']]) ?>
                <div class="row">
                    <div class="large-4 columns">
                        <?= $this->Form->control('title', [
                            'label' => __('Title:'),
                            'style' => 'width: 90%;'
                        ]) ?>
                    </div>
                    <div class="large-4 columns">
                        <?= $this->Form->control('document_release_date', [
                            'label' => __('Document Release Date:'),
                            'style' => 'width: 25%;',
                            'type' => 'date'
                        ]) ?>
                    </div>
                    <div class="large-2 columns">
                        <?= $this->Form->control('version', [
                            'label' => __('Version:'),
                            'style' => 'width: 70%;'
                        ]) ?>
                    </div>
                    <div class="large-2 columns">
                        <?= $this->Form->control('sort_order', [
                            'label' => __('Order:'),
                            'style' => 'width: 70%;'
                        ]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="large-6 columns">
                        <label>
                            <h6 class="fs13 text-gray"><?= __('Target:') ?></h6>
                            <?= $this->Form->control('target', [
                                'label' => false,
                                'type' => 'select',
                                'multiple' => 'checkbox',
                                'options' => $roles
                            ]) ?>
                        </label>
                    </div>
                    <div class="large-6 columns">
                        <div style="margin-top: 20px; margin-bottom: 20px;">&nbsp;</div>
                        <fieldset>
                            <legend><?= __('Attachments') ?></legend>
                            <?= $this->Form->control('attachments.0.upload', [
                                'type' => 'file',
                                'label' => __('Upload File'),
                                 'accept' => 'application/pdf,application/msword',
                                'templates' => [
                                    'error' => '<small class="error" style="width:100%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">{{content}}</small>'
                                ],
                                'class' => 'upload-file'
                            ]) ?>


                            <?= $this->Form->control('attachments.0.model', [
                                'type' => 'hidden',
                                'value' => 'Help'
                            ]) ?>

                        </fieldset>
                    </div>
                </div>
                <hr>
                <?= $this->Form->submit(__('Add Manual'), ['class' => 'tiny radius button bg-blue']) ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>
