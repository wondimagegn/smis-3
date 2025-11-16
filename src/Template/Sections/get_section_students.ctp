<option value="0"><?= __('--- Select Student ---') ?></option>
<?php if (!empty($students)): ?>
    <?php foreach ($students as $key => $name): ?>
        <option value="<?= h($key) ?>"><?= h($name) ?></option>
    <?php endforeach; ?>
<?php endif; ?>
