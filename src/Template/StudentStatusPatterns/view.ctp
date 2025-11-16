<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;">
            <i class="fontello-list" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;">
                <?= __('Status Patterns Detail') ?></span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
<div class="studentStatusPatterns view">

    <dl>
        <?php $i = 0; $class = ' class="altrow"'; ?>

        <dt<?= ($i % 2 == 0 ? $class : '') ?>><?= __('Program') ?></dt>
        <dd<?= ($i++ % 2 == 0 ? $class : '') ?>>
            <?= $this->Html->link(h($studentStatusPattern->program->name ?? ''), ['controller' => 'Programs', 'action' => 'view', $studentStatusPattern->program->id ?? '']) ?>
            &nbsp;
        </dd>
        <dt<?= ($i % 2 == 0 ? $class : '') ?>><?= __('Program Type') ?></dt>
        <dd<?= ($i++ % 2 == 0 ? $class : '') ?>>
            <?= $this->Html->link(h($studentStatusPattern->program_type->name ?? ''), ['controller' => 'ProgramTypes', 'action' => 'view', $studentStatusPattern->program_type->id ?? '']) ?>
            &nbsp;
        </dd>
        <dt<?= ($i % 2 == 0 ? $class : '') ?>><?= __('Academic Year') ?></dt>
        <dd<?= ($i++ % 2 == 0 ? $class : '') ?>>
            <?= h($studentStatusPattern->acadamic_year) ?>
            &nbsp;
        </dd>
        <dt<?= ($i % 2 == 0 ? $class : '') ?>><?= __('Application Date') ?></dt>
        <dd<?= ($i++ % 2 == 0 ? $class : '') ?>>
           <?= $studentStatusPattern->application_date ?
                    h($studentStatusPattern->application_date->format('M j, Y')) : '' ?>
            &nbsp;
        </dd>
        <dt<?= ($i % 2 == 0 ? $class : '') ?>><?= __('Pattern') ?></dt>
        <dd<?= ($i++ % 2 == 0 ? $class : '') ?>>
            <?= h($studentStatusPattern->pattern) ?>
            &nbsp;
        </dd>
        <dt<?= ($i % 2 == 0 ? $class : '') ?>><?= __('Created') ?></dt>
        <dd<?= ($i++ % 2 == 0 ? $class : '') ?>>

            <?= $studentStatusPattern->created ?
                h($studentStatusPattern->created->format('M j, Y')) : '' ?>
            &nbsp;
        </dd>
        <dt<?= ($i % 2 == 0 ? $class : '') ?>><?= __('Modified') ?></dt>
        <dd<?= ($i++ % 2 == 0 ? $class : '') ?>>


            <?= $studentStatusPattern->modified ?
                h($studentStatusPattern->modified->format('M j, Y')) : '' ?>
            &nbsp;
        </dd>
    </dl>
</div>
            </div>
        </div>
    </div>
