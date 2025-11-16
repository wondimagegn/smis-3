<?php

use Cake\ORM\TableRegistry;

$this->assign('title', __('View Logs'));
?>

<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;">
            <i class="fontello-info-outline"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('View Logs') ?></span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <?= $this->Form->create(null, ['url' => ['action' => 'index'], 'type' => 'post']); ?>
            <div class="large-12 columns">
                <div style="margin-top: -30px;">
                    <hr>
                    <blockquote>
                        <h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
                        <p style="text-align:justify;">
                            <span class="fs16">
                                This tool will help you to get user logs with some defined search criteria.
                            </span>
                        </p>
                        <p style="text-align:justify;">
                            <span class="fs16">
                                <ol class="fs14">
                                    <li>You can enter more than one IP, username, model, and action. <span class="text-red" style="font-weight: bold;">Use comma to separate each entry.</span><br>
                                        Eg. 10.144.10.128, 10.144.10.121, 10.144.10.102 will bring logs recorded from 10.144.10.128, 10.144.10.121, 10.144.10.102 IP address.
                                    </li>
                                    <li>You can exclude one or more IP, username, model, and action <span class="text-red" style="font-weight: bold;">by using minus (-) before the entry.</span><br>
                                        Eg. -10.144.10.128 will exclude any log from 10.144.10.128 IP address.
                                    </li>
                                </ol>
                            </span>
                        </p>
                    </blockquote>
                </div>
                <hr>
                <fieldset style="padding-bottom: 5px; padding-top: 5px;">
                    <legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend>
                    <div class="row">
                        <div class="large-6 columns">
                            <?= $this->Form->control('Log.change_date_from', [
                                'label' => 'Logs From: ',
                                'type' => 'date',
                                'dateFormat' => 'MDY',
                                'minYear' => date('Y') - 2,
                                'maxYear' => date('Y'),
                                'orderYear' => 'desc',
                                'value' => [
                                    'year' => $this->request->getData('Log.change_date_from.year', date('Y')),
                                    'month' => $this->request->getData('Log.change_date_from.month', date('m')),
                                    'day' => $this->request->getData('Log.change_date_from.day', date('d') - 14)
                                ],
                                'style' => 'width:15%;'
                            ]); ?>
                        </div>
                        <div class="large-6 columns">
                            <?= $this->Form->control('Log.change_date_to', [
                                'label' => 'Logs To: ',
                                'type' => 'date',
                                'dateFormat' => 'MDY',
                                'minYear' => date('Y') - 2,
                                'maxYear' => date('Y'),
                                'orderYear' => 'desc',
                                'style' => 'width:15%;'
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="large-3 columns">
                            <?= $this->Form->control('Log.role_id', [
                                'label' => 'Role: ',
                                'type' => 'select',
                                'options' => $roles,
                                'style' => 'width:90%;'
                            ]); ?>
                        </div>
                        <div class="large-3 columns">
                            <?= $this->Form->control('Log.username', [
                                'maxlength' => 1000,
                                'label' => 'Username: ',
                                'placeholder' => 'eg: username or -username or username1, username2 ..',
                                'style' => 'width:90%;',
                                'type' => 'text'
                            ]); ?>
                        </div>
                        <div class="large-3 columns">
                            <?= $this->Form->control('Log.ip', [
                                'maxlength' => 1000,
                                'label' => 'IP Address(es)',
                                'placeholder' => 'eg: 10.144.140.10 or -10.144.140.10 ..',
                                'style' => 'width:90%;',
                                'type' => 'text'
                            ]); ?>
                        </div>
                        <div class="large-3 columns" style="line-height: 1.5px;">
                            &nbsp;<br>
                            <div style="margin-top: 15px;">
                                <?= $this->Form->control('Log.active', [
                                    'label' => 'Active User Account',
                                    'type' => 'checkbox',
                                    'checked' => $this->request->getData('Log.active', 1) == 1
                                ]); ?>
                                <br>
                                <?= $this->Form->control('Log.deactive', [
                                    'label' => 'Non Active User Account',
                                    'type' => 'checkbox',
                                    'checked' => $this->request->getData('Log.deactive', 1) == 1
                                ]); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="large-3 columns">
                            <?= $this->Form->control('Log.action', [
                                'maxlength' => 1000,
                                'label' => 'Action: ',
                                'placeholder' => 'eg: Add, Edit, mass_register..',
                                'style' => 'width:90%;',
                                'type' => 'text'
                            ]); ?>
                        </div>
                        <div class="large-3 columns">
                            <?= $this->Form->control('Log.model', [
                                'maxlength' => 1000,
                                'label' => 'Model: ',
                                'placeholder' => 'eg: ExamGrade, CourseRegistration, ExamGradeChange..',
                                'style' => 'width:90%;',
                                'type' => 'text'
                            ]); ?>
                        </div>
                        <div class="large-3 columns">
                            <?= $this->Form->control('Log.change', [
                                'maxlength' => 1000,
                                'label' => 'Change: ',
                                'placeholder' => 'eg: last_login, exam_grade_id, ExamGradeChange..',
                                'style' => 'width:90%;',
                                'type' => 'text'
                            ]); ?>
                        </div>
                        <div class="large-3 columns">
                            <?= $this->Form->control('Log.description', [
                                'maxlength' => 1000,
                                'label' => 'Message: ',
                                'placeholder' => 'eg: Created Colleges: Test College (ID: 1)..',
                                'style' => 'width:90%;',
                                'type' => 'text'
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="large-3 columns">
                            <?= $this->Form->control('Log.key', [
                                'maxlength' => 1000,
                                'label' => 'Key(Table ID): ',
                                'placeholder' => 'eg: 103774 or 102445, 133774, 37736',
                                'style' => 'width:90%;',
                                'type' => 'text'
                            ]); ?>
                        </div>
                        <div class="large-3 columns">
                            <?= $this->Form->control('Log.limit', [
                                'id' => 'limit',
                                'type' => 'number',
                                'min' => 50,
                                'max' => 1000,
                                'value' => $limit,
                                'step' => 50,
                                'label' => 'Limit: ',
                                'style' => 'width:40%;'
                            ]); ?>
                        </div>
                        <div class="large-3 columns">&nbsp;</div>
                        <div class="large-3 columns">&nbsp;</div>
                    </div>
                    <hr>
                    <?= $this->Form->submit(__('Search Logs'), [
                        'name' => 'searchLogs',
                        'div' => false,
                        'class' => 'tiny radius button bg-blue'
                    ]); ?>
                </fieldset>
                <hr>
            </div>
            <div class="large-12 columns">
                <?php if (!empty($logs)) { ?>
                    <hr>
                    <h6 class="fs16 text-gray"><?= __('List of logs based on the given search criteria') ?></h6>
                    <div style="overflow-x:auto;">
                        <table cellpadding="0" cellspacing="0" class="table">
                            <thead>
                            <tr>
                                <th style="width:3%"><?= $this->Paginator->sort('id', '#') ?></th>
                                <th style="width:8%"><?= $this->Paginator->sort('foreign_key', 'Table ID') ?></th>
                                <th style="width:13%"><?= $this->Paginator->sort('user_id', 'User') ?></th>
                                <th style="width:8%"><?= $this->Paginator->sort('ip', 'IP Address') ?></th>
                                <th style="width:15%"><?= $this->Paginator->sort('model', 'Model') ?></th>
                                <th style="width:8%"><?= $this->Paginator->sort('action', 'Action') ?></th>
                                <th style="width:12%"><?= $this->Paginator->sort('message', 'Message') ?></th>
                                <th style="width:24%"><?= $this->Paginator->sort('change', 'Change') ?></th>
                                <th style="width:10%"><?= $this->Paginator->sort('created', 'Date') ?></th>
                                <th style="width:10%"><?= __('Actions') ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $start = $this->Paginator->counter(['format' => '{{start}}']);

                            foreach ($logs as $log) {
                                $context = json_decode($log->context, true);
                                $usersTable = TableRegistry::getTableLocator()->get('Users');
                                $user = $usersTable->find()
                                    ->select(['id', 'username', 'first_name', 'middle_name', 'last_name'])
                                    ->where(['id' => $context['user_id'] ?? null])
                                    ->first();
                                ?>
                                <tr>
                                    <td class="center"><?= $start++ ?></td>
                                    <td class="center"><?= h($context['foreign_key'] ?? 'N/A') ?></td>
                                    <td>
                                        <?php
                                        if ($user) {
                                            $userDisplay = !empty($user->first_name)
                                                ? h($user->first_name . ' ' . $user->middle_name . ' ' . $user->last_name . ' (' . $user->username . ')')
                                                : h($user->username);
                                            echo $this->Html->link(
                                                $userDisplay,
                                                ['controller' => 'Users', 'action' => 'view', $user->id]
                                            );
                                        } else {
                                            echo h($context['username'] ?? 'N/A');
                                        }
                                        ?>
                                    </td>
                                    <td class="center"><?= h($context['ip'] ?? 'N/A') ?></td>
                                    <td class="center"><?= h($context['model'] ?? 'N/A') ?></td>
                                    <td class="center"><?= h($context['action'] ?? 'N/A') ?></td>
                                    <td><?= h($log->message) ?></td>
                                    <td><?= h(isset($context['change']) ? json_encode($context['change']) : 'N/A') ?></td>
                                    <td class="center"><?= $this->Time->format($log->created, 'MMM d, YYYY HH:mm:ss') ?></td>
                                    <td class="center">
                                        <?= $this->Html->link(
                                            __(''),
                                            ['action' => 'delete', $log->id],
                                            [
                                                'class' => 'fontello-trash',
                                                'title' => 'Delete',
                                                'confirm' => __('Are you sure you want to delete this log entry?')
                                            ]
                                        ) ?>
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
                        No logs found for the selected criteria.
                    </div>
                    <hr>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<?= $this->Form->end(); ?>
