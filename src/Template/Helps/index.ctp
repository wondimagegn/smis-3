<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Help[] $helps
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
                <div style="margin-top: -30px;"><hr></div>
                <div style="overflow-x:auto;">
                    <?php
                    // Preserve pagination query params
                    if ($this->request->getQuery('page')) {
                        echo $this->Form->control('page', ['type' => 'hidden', 'value' => $this->request->getQuery('page')]);
                    }
                    if ($this->request->getQuery('sort')) {
                        echo $this->Form->control('sort', ['type' => 'hidden', 'value' => $this->request->getQuery('sort')]);
                    }
                    if ($this->request->getQuery('direction')) {
                        echo $this->Form->control('direction', ['type' => 'hidden', 'value' => $this->request->getQuery('direction')]);
                    }
                    ?>
                    <table cellpadding="0" cellspacing="0" class="table">
                        <thead>
                        <tr>
                            <th style="width:3%" class="center"><?= __('#') ?></th>
                            <th style="width:38%" class="vcenter"><?= $this->Paginator->sort('title', __('Title of the Manual')) ?></th>
                            <th class="center"><?= $this->Paginator->sort('document_release_date', __('Manual Release Date')) ?></th>
                            <th class="center"><?= $this->Paginator->sort('version', __('Version')) ?></th>
                            <th class="center"><?= __('Manual') ?></th>
                            <?php if ($this->request->getSession()->read('Auth.User.role_id')
                                == ROLE_SYSADMIN): ?>
                                <th class="center"><?= __('Active') ?></th>
                                <th class="center"><?= __('Actions') ?></th>
                            <?php endif; ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $start = $this->Paginator->counter(['format' => '{{start}}']);
                        foreach ($helps as $help):
                            ?>
                            <tr>
                                <td class="center"><?= $start++ ?></td>
                                <td class="vcenter"><?= h($help->title) ?></td>
                                <td class="center">
                                    <?= $help->document_release_date ? h($help->document_release_date->format('M j, Y')) : '' ?>
                                </td>
                                <td class="center"><?= h($help->version) ?></td>
                                <td class="center">
                                    <?php
                                    $missingAttachment = false;
                                    if (!empty($help->attachments)) {
                                        foreach ($help->attachments as $attachment) {
                                            if($attachment->isLegacy()){
                                                $legacyURl = $attachment->getLegacyUrlForCake2();
                                                echo $this->Html->link(
                                                    __('View Manual'),
                                                    $legacyURl,
                                                    [
                                                        'target' => '_blank',
                                                        'class' => 'attachment-link'
                                                    ]
                                                );
                                            } else {
                                                $filePath = $attachment->getFullPath();
                                                  if (file_exists($filePath) ) {
                                                      echo $this->Html->link(
                                                          __('View Manual'),
                                                          $attachment->getUrl(),
                                                          [
                                                              'target' => '_blank',
                                                              'class' => 'attachment-link'
                                                          ]
                                                      );
                                                  }
                                            }
                                        }
                                    } else {
                                        $missingAttachment = true;
                                        echo '<span class="rejected">' .
                                            __('Attachment not found') . '</span>';
                                    }
                                    ?>
                                </td>
                                <?php if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_SYSADMIN): ?>
                                    <td class="center">
                                        <?= $help->active ? '<span class="accepted">' . __('Yes') . '</span>' : '<span class="rejected">' . __('No') . '</span>' ?>
                                    </td>
                                    <td class="center">
                                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $help->id], ['class' => 'tiny radius button bg-green']) ?>
                                        &nbsp;
                                        <?= $this->Form->postLink(
                                            __('Delete'),
                                            ['action' => 'delete', $help->id],
                                            [
                                                'confirm' => __('Are you sure you want to delete user manual {0} (version: {1})?', $help->title, $help->version),
                                                'class' => 'tiny radius button bg-red'
                                            ]
                                        ) ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
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
                                <?= $this->Paginator->prev('<< ' . __('previous'), ['tag' => 'li', 'disabledTag' => 'span', 'class' => 'arrow']) ?>
                                <?= $this->Paginator->numbers(['separator' => '', 'tag' => 'li']) ?>
                                <?= $this->Paginator->next(__('next') . ' >>', ['tag' => 'li', 'disabledTag' => 'span', 'class' => 'arrow']) ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
