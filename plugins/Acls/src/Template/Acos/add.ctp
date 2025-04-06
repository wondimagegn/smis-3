<?php
/**
 * ACL Management Plugin
 *
 * @copyright     Copyright 2010, Joseph B Crawford II
 * @link          http://www.jbcrawford.net
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
use Cake\Core\Configure;

?>
    <h2><?= __('Add ACO') ?></h2>
<?= $this->Form->create($aco, ['url' => ['action' => 'add']]) ?>
<?= $this->Form->control('parent_id', ['options' => $parents, 'empty' => 'None', 'label' => 'Parent']) ?>
<?= $this->Form->control('alias', ['label' => 'Alias']) ?>
<?= $this->Form->control('model', ['label' => 'Model']) ?>
<?= $this->Form->control('foreign_key', ['label' => 'Foreign Key']) ?>
<?= $this->Form->button(__('Submit')) ?>
<?= $this->Html->link(__('Cancel'), ['action' => 'index', $aco->parent_id ?? null], ['class' => 'button']) ?>
<?= $this->Form->end() ?>
