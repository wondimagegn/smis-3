<?php
/**
 * ACL Management Plugin - Add ACO Form
 *
 * @copyright     Copyright 2010, Joseph B Crawford II
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
?>

<h2>Add ACO</h2>

<?= $this->Form->create(null, ['url' => ['controller' => 'Acos', 'action' => 'add']]) ?>

<?= $this->Form->control('parent_id', [
    'type' => 'select',
    'options' => $parents,
    'empty' => 'None',
    'label' => 'Parent'
]) ?>

<?= $this->Form->control('alias', [
    'type' => 'text',
    'label' => 'Alias'
]) ?>

<?= $this->Form->control('model', [
    'type' => 'text',
    'label' => 'Model'
]) ?>

<?= $this->Form->control('foreign_key', [
    'type' => 'text',
    'label' => 'Foreign Key'
]) ?>

<div class="form-group">
    <?= $this->Form->button('Submit') ?>
    <?= ' or ' . $this->Html->link('Cancel', ['action' => 'index', $this->request->getData('parent_id')]) ?>
</div>

<?= $this->Form->end() ?>
