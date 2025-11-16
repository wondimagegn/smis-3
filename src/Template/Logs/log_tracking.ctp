<?php
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
            <?= $this->Form->create(null, ['url' => ['action' => 'log_tracking'], 'type' => 'post']); ?>
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
                        <td>Role:</td>
                        <td>
                            <?= $this->Form->control('Log.role_id', [
                                'label' => false,
                                'type' => 'select',
                                'options' => $roles,
                                'style' => 'width:373px'
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
                <?php if (!empty($logs)) { ?>
                    <p class="fs15"><?= __('List of logs based on the above given condition/s') ?></p>
                    <table cellpadding="0" cellspacing="0" style="table-layout:fixed" class="small_padding">
                        <tr>
                            <th style="width:3%"><?= $this->Paginator->sort('id', 'No') ?></th>
                            <th style="width:8%"><?= $this->Paginator->sort('foreign_key', 'Key') ?></th>
                            <th style="width:13%"><?= $this->Paginator->sort('user_id', 'User') ?></th>
                            <th style="width:8%"><?= $this->Paginator->sort('ip', 'IP') ?></th>
                            <th style="width:15%"><?= $this->Paginator->sort('model', 'Model') ?></th>
                            <th style="width:8%"><?= $this->Paginator->sort('action', 'Action') ?></th>
                            <th style="width:12%"><?= $this->Paginator->sort('message', 'Message') ?></th>
                            <th style="width:24%"><?= $this->Paginator->sort('change', 'Change') ?></th>
                            <th style="width:10%"><?= $this->Paginator->sort('created', 'Date') ?></th>
                        </tr>
                        <?php
                        $start = $this->Paginator->counter(['format' => '%start%']);
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
                                <td><?= h($context['foreign_key'] ?? 'N/A') ?></td>
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
                                <td><?= h($context['ip'] ?? 'N/A') ?></td>
                                <td><?= h($context['model'] ?? 'N/A') ?></td>
                                <td><?= h($context['action'] ?? 'N/A') ?></td>
                                <td><?= h($log->message) ?></td>
                                <td><?= h(isset($context['change']) ? json_encode($context['change']) : 'N/A') ?></td>
                                <td><?= $this->Time->format($log->created, 'MMM d, YYYY HH:mm:ss') ?></td>
                            </tr>
                        <?php } ?>
                    </table>
                    <p>
                        <?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} records out of {{count}} total, starting on record {{start}}, ending on {{end}}')]) ?>
                    </p>
                    <div class="paging">
                        <?= $this->Paginator->prev('<< ' . __('previous'), [], null, ['class' => 'disabled']) ?>
                        | <?= $this->Paginator->numbers() ?> |
                        <?= $this->Paginator->next(__('next') . ' >>', [], null, ['class' => 'disabled']) ?>
                    </div>
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
