<?php
/**
 * ACL Management Plugin
 *
 * @copyright     Copyright 2010, Joseph B Crawford II
 * @link          http://www.jbcrawford.net
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
use Cake\Utility\Inflector;
use Cake\Core\Configure;
?>
<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;">
            <i class="fontello-edit" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;">
                <?= __('Edit Privilege: ') . (!empty($aco->privilage) ? Inflector::humanize(Inflector::underscore($aco->privilage)) : Inflector::humanize(Inflector::underscore($aco->alias))) ?>
            </span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">

                <?= $this->Form->create($aco) ?>

                <?= $this->Form->control('id', ['type' => 'hidden']) ?>
                <?= $this->Form->control('parent_id', ['type' => 'hidden']) ?>

                <table cellpadding="0" cellspacing="0" class="table-borderless">
                    <tbody>
                    <tr><td><?= __('Parent Privilege: ') . h($aco->parent_aco ? $aco->parent_aco->privilage : 'None') ?></td></tr>
                    <tr><td style="background-color: white;"><?= __('Parent Alias: ') . h($aco->parent_aco ? $aco->parent_aco->alias : 'None') ?></td></tr>
                    <tr><td><?= __('Alias: ') . h($aco->alias) ?></td></tr>
                    <tr><td style="background-color: white;"><?= $this->Form->control('privilage', ['label' => 'Privilege Title', 'style' => 'width:300px']) ?></td></tr>
                    <tr><td><?= $this->Form->control('order', [
                                'label' => 'Order',
                                'after' => ' When it is displayed on the menu structure and on the permission management',
                                'style' => 'width:100px',
                            ]) ?></td></tr>
                    <?php if ($aco->parent_id != 1): ?>
                        <tr><td style="background-color: white;"><?= $this->Form->control('admin', [
                                    'label' => 'Administrator/s',
                                    'type' => 'select',
                                    'multiple' => 'checkbox',
                                    'options' => $roles,
                                ]) ?></td></tr>
                    <?php endif; ?>
                    <tr><td style="background-color: white;"><?= $this->Form->control('note', ['label' => 'Privilege Note', 'style' => 'width:300px']) ?></td></tr>
                    </tbody>
                </table>
                <hr>

                <div class="row">
                    <div class="large-2 columns">
                        <?= $this->Form->button(__('Submit'), ['class' => 'tiny radius button bg-blue']) ?>
                    </div>
                    <div class="large-10 columns">
                        <?= $this->Html->link(__('Cancel'), ['action' => 'index', $aco->parent_id], ['class' => 'tiny radius button bg-blue']) ?>
                    </div>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>
