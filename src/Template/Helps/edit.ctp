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
            <i class="fontello-edit" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?=
                __('Edit Help Document') ?></span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <div style="margin-top: -30px;"><hr></div>
                <?= $this->Form->create($help, ['type' => 'file', 'url' => ['action' => 'edit',
                    $help->id]]) ?>
                <div class="row">
                    <div class="large-4 columns">
                        <?= $this->Form->control('id', ['type' => 'hidden']) ?>
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
                            <?php
                            $missingAttachment = false;
                            $hasValidAttachment = false;
                            if (!empty($help->attachments)) {
                                foreach ($help->attachments as $attachment) {
                                    if($attachment->isLegacy()){
                                        $legacyURl = $attachment->getLegacyUrlForCake2();
                                        echo $this->Html->link(
                                            __('View Manual'),
                                            $legacyURl,
                                            [
                                                'target' => '_blank',
                                                'class' => 'attachment-link'
                                            ]
                                        );
                                    } else {
                                        $filePath = $attachment->getFullPath();
                                        if (file_exists($filePath) &&
                                        $attachment->verifyChecksum($filePath) ) {
                                            $hasValidAttachment=true;
                                            echo $this->Html->link(
                                                __('View Manual'),
                                                $attachment->getUrl(),
                                                [
                                                    'target' => '_blank',
                                                    'class' => 'attachment-link'
                                                ]
                                            );
                                        }
                                    }
                                }
                            } else {
                                $missingAttachment = true;
                                echo '<span class="rejected">' .
                                    __('Attachment not found') . '</span>';
                            }
                            /*
                            if (!empty($help->attachments)) {
                                foreach ($help->attachments as $attachment) {

                                        $filePath = $attachment->getFullPath();
                                        if (file_exists($filePath) &&
                                            $attachment->verifyChecksum($filePath)) {
                                            $hasValidAttachment = true;
                                            if ($attachment->isImage()) {
                                                echo $this->Html->image(
                                                    $attachment->getThumbnailUrl('m'),
                                                    [
                                                        'alt' => h($attachment->file_name ?? 'Attachment'),
                                                        'style' => 'max-width: 200px; max-height: 200px;',
                                                        'class' => 'attachment-thumbnail'
                                                    ]
                                                );
                                                echo '<br>';
                                            }
                                            echo $this->Html->link(
                                                h($attachment->file_name ?? $attachment->basename ?? 'Download'),
                                                $attachment->getUrl(),
                                                [
                                                    'target' => '_blank',
                                                    'class' => 'attachment-link'
                                                ]
                                            );
                                            echo '<br>';
                                            if ($attachment->isLegacy()) {
                                                echo '<small class="text-muted">' . __('Legacy attachment') . '</small>';
                                            }
                                        } else {
                                            echo '<span class="rejected">' .
                                                __('Attachment not found or
                                                 checksum mismatch') . '</span>';
                                            $missingAttachment = true;
                                        }

                                }
                            }
                            */

                            if (!$hasValidAttachment || $missingAttachment) {
                                echo '<div class="new-attachment">';
                                echo $this->Form->control('attachments.0.upload', [
                                    'type' => 'file',
                                    'label' => __('Upload New File'),

                                    'accept' => 'application/pdf,application/msword',
                                    'templates' => [
                                        'error' => '<small class="error" style="width:100%; background: #fff; color:red; border-style: solid; border-width: thin; border-color: red; border-radius: 5px;">{{content}}</small>'
                                    ],
                                    'class' => 'upload-file'
                                ]);
                                echo $this->Form->control('attachments.0.model', [
                                    'type' => 'hidden',
                                    'value' => 'Help'
                                ]);

                                echo '</div>';
                            }
                            ?>
                        </fieldset>
                    </div>
                </div>
                <hr>
                <?= $this->Form->submit(__('Save Changes'), ['class' => 'tiny radius button bg-blue']) ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>
