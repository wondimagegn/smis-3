<?php

use Cake\ORM\TableRegistry;

$this->assign('title', __('View Logs'));
?>
<style>
    table.small_padding tr td {
        padding: 2px;
    }
</style>

<div class="box">
    <div class="box-body">
        <div class="row">
            <?= $this->Form->create(null, ['url' => ['action' => 'view_logs'], 'type' => 'post']); ?>
            <div class="large-12 columns">
                <h5 class="box-title"><?= __('View Logs') ?></h5>
                <table class="fs13 small_padding">
                    <tr>
                        <td style="width:5%">From:</td>
                        <td style="width:45%">
                            <?= $this->Form->control('Log.change_date_from', [
                                'label' => false,
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
                                'style' => 'width:50px'
                            ]); ?>
                        </td>
                        <td style="width:5%">To:</td>
                        <td style="width:45%">
                            <?= $this->Form->control('Log.change_date_to', [
                                'label' => false,
                                'type' => 'date',
                                'dateFormat' => 'MDY',
                                'minYear' => date('Y') - 2,
                                'maxYear' => date('Y'),
                                'orderYear' => 'desc',
                                'style' => 'width:50px'
                            ]); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Action:</td>
                        <td>
                            <?= $this->Form->control('Log.action', [
                                'label' => false,
                                'type' => 'select',
                                'options' => [
                                    'create' => 'Created',
                                    'update' => 'Update',
                                    'delete' => 'Delete/Cancel'
                                ],
                                'empty' => '--select action--',
                                'required' => true,
                                'style' => 'width:373px'
                            ]); ?>
                        </td>
                        <td>Activity:</td>
                        <td>
                            <?= $this->Form->control('Log.model', [
                                'label' => false,
                                'type' => 'select',
                                'options' => [
                                    'Colleges' => 'Colleges',
                                    'ExamGrades' => 'Exam Grade',
                                    'CourseRegistrations' => 'Course Registration',
                                    'CourseAdd'=>'Course Add',
                                    'Students' => 'Student Admission',
                                    'Sections' => 'Section',
                                    'Curriculums' => 'Curriculum'
                                ],
                                'required' => true,
                                'style' => 'width:370px'
                            ]); ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Limit:</td>
                        <td>
                            <?= $this->Form->control('Log.limit', [
                                'label' => false,
                                'type' => 'number',
                                'maxlength' => 5,
                                'value' => 5,
                                'style' => 'width:50px'
                            ]); ?>
                        </td>
                        <td>User:</td>
                        <td>
                            <?= $this->Form->control('Log.username', [
                                'label' => false,
                                'type' => 'text',
                                'style' => 'width:370px'
                            ]); ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <?= $this->Form->submit(__('View logs'), ['div' => false]); ?>
                        </td>
                    </tr>
                </table>
            </div>
            <?= $this->Form->end(); ?>
            <div class="large-12 columns">
                <div class="row">
                    <div class="large-6 columns">
                        <p>
                            <strong>Note:-</strong>
                        <ul>
                            <li>Logs are searchable only if they are not more than 2 years old.</li>
                            <li>Messages indicate actions like "Created Colleges: Test College (ID: 1)".</li>
                            <li>Changes are logged as JSON (e.g., {"name":{"old":"Test College","new":"New College"}}).</li>
                        </ul>
                        </p>
                    </div>
                    <div class="large-6 columns">
                        <p>
                        <ul>
                            <li>Created: New record added.</li>
                            <li>Update: Record modified.</li>
                            <li>Delete: Record removed.</li>
                        </ul>
                        </p>
                    </div>
                </div>
                <?php if (!empty($logs)) { ?>
                    <p class="fs15"><?= __('List of logs based on the above given condition/s') ?></p>
                    <table cellpadding="0" cellspacing="0" style="table-layout:fixed" class="small_padding">
                        <tr>
                            <th style="width:3%"><?= $this->Paginator->sort('id', 'No') ?></th>
                            <th style="width:15%"><?= $this->Paginator->sort('message', 'Description') ?></th>
                            <th style="width:10%"><?= $this->Paginator->sort('created', 'Event Date') ?></th>
                        </tr>
                        <?php
                        $start = 1;
                        foreach ($logs as $log) {
                            $context = json_decode($log->context, true);
                            $usersTable = TableRegistry::getTableLocator()->get('Users');
                            $user = $usersTable->find()
                                ->select(['id', 'username', 'first_name', 'middle_name', 'last_name'])
                                ->where(['id' => $context['user_id'] ?? null])
                                ->first();
                            $class = ($start % 2 == 0) ? ' class="altrow"' : '';
                            ?>
                            <tr<?= $class ?>>
                                <td><?= $start++ ?></td>
                                <td>
                                    <?= h($log->message) ?>
                                    <?php if ($user) {
                                        $userDisplay = !empty($user->first_name)
                                            ? h($user->first_name . ' ' . $user->middle_name . ' ' . $user->last_name . ' (' . $user->username . ')')
                                            : h($user->username);
                                        echo ' by ' . $this->Html->link(
                                                $userDisplay,
                                                ['controller' => 'Users', 'action' => 'view', $user->id]
                                            );
                                    } else {
                                        echo ' by ' . h($context['username'] ?? 'N/A');
                                    } ?>
                                    (Action: <?= h($context['action'] ?? 'N/A') ?>, Model: <?= h($context['model'] ?? 'N/A') ?>)
                                </td>
                                <td><?= $this->Time->format($log->created, 'MMM d, YYYY HH:mm:ss') ?></td>
                            </tr>
                        <?php } ?>
                    </table>
                <?php } else { ?>
                    <div class="info-box info-message" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
                        <span style="margin-right: 15px;"></span>
                        No logs found for the selected criteria.
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
