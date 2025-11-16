<?php if (isset($sections) && !empty($sections)): ?>
    <option value="0"><?= __('[ Select Section ]') ?></option>
    <?php foreach ($sections as $id => $section): ?>
        <optgroup label="<?= h($id) ?>">
            <?php foreach ($section as $key => $value): ?>
                <option value="<?= h($key) ?>"><?= h($value) ?></option>
            <?php endforeach; ?>
        </optgroup>
    <?php endforeach; ?>
<?php else: ?>
    <option value="">
        <?= isset($department_id_selected) && empty($department_id_selected) ?
            __('[ Department Not Selected ]') :
            __('[ No Section Found by Criteria ]') ?>
    </option>
<?php endif; ?>
