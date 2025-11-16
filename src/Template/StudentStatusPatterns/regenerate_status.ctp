<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;">
            <i class="fontello-check-outline" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Regenerate Status By Published Course') ?></span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <?= $this->Form->create(null, ['type' => 'post',
                    'url' => ['controller'=>'StudentStatusPatterns','action' => 'regenerateStatus']]) ?>
                <!-- Use null or dummy entity for non-namespaced form -->
                <?= $this->element('publish_course_filter_by_dept') ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>
