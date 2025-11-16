<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Help $help
 */
?>
<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;">
            <i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('SMIS Users Manuals') ?></span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
<div class="helps view">
    <h2><?= __('Help') ?></h2>
    <dl class="definition-list">
        <?php
        $i = 0;
        $class = $i % 2 == 0 ? ' class="altrow"' : '';
        ?>
        <dt<?= $class ?>><?= __('Title') ?></dt>
        <dd<?= $class ?>>
            <?= h($help->title) ?>
            &nbsp;
        </dd>
        <?php $class = $i % 2 == 0 ? ' class="altrow"' : ''; $i++; ?>
        <dt<?= $class ?>><?= __('Document Release Date') ?></dt>
        <dd<?= $class ?>>
            <?= $help->document_release_date ? h($help->document_release_date->format('M j, Y')) : '' ?>
            &nbsp;
        </dd>
        <?php $class = $i % 2 == 0 ? ' class="altrow"' : ''; $i++; ?>
        <dt<?= $class ?>><?= __('Version') ?></dt>
        <dd<?= $class ?>>
            <?= h($help->version) ?>
            &nbsp;
        </dd>
        <?php $class = $i % 2 == 0 ? ' class="altrow"' : ''; $i++; ?>
        <dt<?= $class ?>><?= __('Created') ?></dt>
        <dd<?= $class ?>>
            <?= $help->created ? h($help->created->format('M j, Y')) : '' ?>
            &nbsp;
        </dd>
        <?php $class = $i % 2 == 0 ? ' class="altrow"' : ''; $i++; ?>
        <dt<?= $class ?>><?= __('Modified') ?></dt>
        <dd<?= $class ?>>
            <?= $help->modified ? h($help->modified->format('M j, Y')) : '' ?>
            &nbsp;
        </dd>
    </dl>
</div>
            </div>
        </div>
    </div>
</div>
