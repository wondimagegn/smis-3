<?php if (isset($sections) && !empty($sections)): ?>
    <option value=""><?= __('[ Select Section ]') ?></option>
    <?php foreach ($sections as $id => $section): ?>
        <optgroup label="<?= h($id) ?>">
            <?php foreach ($section as $key => $value): ?>
                <option value="<?= h($key) ?>"><?= h($value) ?></option>
            <?php endforeach; ?>
        </optgroup>
    <?php endforeach; ?>
<?php else: ?>
    <option value=""><?= __('[ No Active Section Found ]') ?></option>
<?php endif; ?>
