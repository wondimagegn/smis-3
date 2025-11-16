
<option value="0"><?= __('[ Select Section ]') ?></option>
<?php if (isset($sections) && !empty($sections)): ?>
    <?php foreach ($sections as $id => $section): ?>
        <optgroup label="<?= h($id) ?>">
            <?php foreach ($section as $key => $value): ?>
                <option value="<?= h($key) ?>"><?= h($value) ?></option>
            <?php endforeach; ?>
        </optgroup>
    <?php endforeach; ?>
<?php endif; ?>
