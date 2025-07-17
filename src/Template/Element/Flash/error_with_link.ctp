<?php
/**
 * templates/element/flash/error_with_link.php
 */
?>
<div class="error-box error-message">
    <span style="margin-right: 15px;"></span>
    <?= h($message) ?>
    <?php if (isset($params['link_text']) && isset($params['link_url'])): ?>
        <a href="<?= $this->Url->build($params['link_url']) ?>"><?= h($params['link_text']) ?></a>
    <?php endif; ?>
</div>
