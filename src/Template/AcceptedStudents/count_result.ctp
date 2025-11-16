<?php
use Cake\I18n\I18n;

$this->set('title', __('Count Result'));
?>

<div class="container">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <?php if (isset($resultCount)): ?>
                        <?php if (!empty($from) && !empty($to)): ?>
                            <strong><?= __('Total students from {0} to {1} is {2}', h($from), h($to), h($resultCount)) ?></strong>
                        <?php else: ?>
                            <strong><?= h($resultCount) ?></strong>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
