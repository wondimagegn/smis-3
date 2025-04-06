<?php
/**
 * ACL Management Plugin
 *
 * @copyright     Copyright 2010, Joseph B Crawford II
 * @link          http://www.jbcrawford.net
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
?>
<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;">
            <i class="fontello-edit" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;">
                <?= __('Edit Permission to <u>{0}</u>', !empty($aco->privilage) ? h($aco->privilage) : h(implode('/', $path))) ?>
            </span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns" style="margin-top: -20px;">
                <?= $this->Form->create($permission, ['url' => ['action' => 'edit', $permission->id]]) ?>
                <?= $this->Form->control('id', ['type' => 'hidden']) ?>
                <?= $this->Form->control('aco_id', ['type' => 'hidden']) ?>
                <?= $this->Form->control('aro_id', ['type' => 'hidden']) ?>
                <hr>
                <table cellpadding="0" cellspacing="0" class="table">
                    <tbody>
                    <tr>
                        <td style="width:10%"><?= h($aroType) ?>:</td>
                        <td style="width:90%"><?= h($aroName) ?></td>
                    </tr>
                    <tr>
                        <td style="background-color: white;"><?= __('Privilege:') ?></td>
                        <td style="background-color: white;">
                            <?= $this->Form->control('privilege', [
                                'label' => false,
                                'style' => 'width:40%',
                                'options' => $perms,
                            ]) ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <hr>
                <div class="row">
                    <div class="large-3 columns">
                        <?= $this->Form->button(__('Submit'), ['class' => 'tiny radius button bg-blue']) ?>
                    </div>
                    <div class="large-9 columns">
                        <?= $this->Html->link(__('Cancel'), ['action' => 'index', $permission->aco_id], ['class' => 'tiny radius button bg-blue']) ?>
                    </div>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>
