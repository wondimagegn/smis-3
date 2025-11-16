<?php
$this->assign('title', __('Colleges'));
?>

<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;">
            <i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Colleges') ?></span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <div style="margin-top: -30px;">
                    <hr>
                    <?php if (!empty($colleges)) { ?>
                        <div style="overflow-x:auto;">
                            <table cellpadding="0" cellspacing="0" class="table">
                                <thead>
                                <tr>
                                    <th class="center">#</th>
                                    <th class="vcenter"><?= $this->Paginator->sort('name') ?></th>
                                    <th class="center"><?= $this->Paginator->sort('shortname', 'Short') ?></th>
                                    <th class="center"><?= $this->Paginator->sort('type') ?></th>
                                    <th class="center"><?= $this->Paginator->sort('institution_code') ?></th>
                                    <th class="center"><?= $this->Paginator->sort('active') ?></th>
                                    <th class="center"><?= $this->Paginator->sort('campus_id') ?></th>
                                    <th class="center"><?= __('Actions') ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $count = $this->Paginator->counter(['format' => '{{start}}']);

                                foreach ($colleges as $college) { ?>
                                    <tr>
                                        <td class="center"><?= $count++ ?></td>
                                        <td class="vcenter"><?= h($college->name) ?></td>
                                        <td class="center"><?= h($college->shortname) ?></td>
                                        <td class="center"><?= h($college->type) ?></td>
                                        <td class="center"><?= h($college->institution_code ?? '') ?></td>
                                        <td class="center">
                                            <?= $college->active ?
                                                '<span style="color:green">Yes</span>' :
                                                '<span style="color:red">No</span>' ?>
                                        </td>
                                        <td class="center">
                                            <?= $this->Html->link(
                                                h($college->campus->name),
                                                ['controller' => 'Campuses', 'action' => 'view', $college->campus->id]
                                            ) ?>
                                        </td>
                                        <td class="center">
                                            <?= $this->Html->link(
                                                __(''),
                                                ['action' => 'view', $college->id],
                                                ['class' => 'fontello-eye', 'title' => 'View']
                                            ) ?>
                                            &nbsp;
                                            <?php if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_SYSADMIN ||
                                                (in_array($this->request->getSession()->read('Auth.User.role_id'), [ROLE_COLLEGE, ROLE_REGISTRAR]) &&
                                                    $this->request->getSession()->read('Auth.User.is_admin') == 1)) { ?>
                                                <?= $this->Html->link(
                                                    __(''),
                                                    ['action' => 'edit', $college->id],
                                                    ['class' => 'fontello-pencil', 'title' => 'Edit']
                                                ) ?>
                                                &nbsp;
                                            <?php } ?>
                                            <?php if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_SYSADMIN) { ?>
                                                <?= $this->Html->link(
                                                    __(''),
                                                    ['action' => 'delete', $college->id],
                                                    [
                                                        'class' => 'fontello-trash',
                                                        'title' => 'Delete',
                                                        'confirm' => __('Are you sure you want to delete {0} college?', $college->name)
                                                    ]
                                                ) ?>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <br>
                        <hr>
                        <div class="row">
                            <div class="large-5 columns">
                                <?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} records out of {{count}} total')]) ?>
                            </div>
                            <div class="large-7 columns">
                                <div class="pagination-centered">
                                    <ul class="pagination">
                                        <?= $this->Paginator->prev('<< ' . __(''), ['tag' => 'li', 'disabledTag' => 'li', 'class' => 'arrow unavailable']) ?>
                                        <?= $this->Paginator->numbers(['separator' => '', 'tag' => 'li']) ?>
                                        <?= $this->Paginator->next(__('') . ' >>', ['tag' => 'li', 'disabledTag' => 'li', 'class' => 'arrow unavailable']) ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="info-box info-message" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
                            <span style="margin-right: 15px;"></span>
                            Unable to load colleges data. Please make sure that you have the privilege to view/list colleges.
                        </div>
                        <hr>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
