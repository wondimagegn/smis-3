<option value="0"><?= __('[ Select Course ]') ?></option>
<?php if (!empty($published_courses_list)): ?>
    <?php foreach ($published_courses_list as $key => $course): ?>
        <option value="<?= h($key) ?>"><?= h($course) ?></option>
    <?php endforeach; ?>
<?php endif; ?>
