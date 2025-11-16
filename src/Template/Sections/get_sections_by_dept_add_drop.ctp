<?php if (!empty($sections)): ?>
    <option value=""><?= __('[ Select Section ]') ?></option>
    <?php foreach ($sections as $id => $section): ?>
        <optgroup label="<?= h($id) ?>">
            <?php foreach ($section as $key => $value): ?>
                <option value="<?= h($key) ?>"><?= h($value) ?></option>
            <?php endforeach; ?>
        </optgroup>
    <?php endforeach; ?>
<?php else: ?>
    <option value="">
        <?= empty($college_id) ? __('[ No College Selected ]') :
            (empty($department_id) ? __('[ No Department Selected ]') :
                __('[ No Active %s Section Found ]', isset($year_level_name) ? ($department_id == -1 ? __('Freshman') : h($year_level_name) . __(' year')) : '')) ?>
    </option>
<?php endif; ?>
