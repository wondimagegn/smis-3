<?php
$this->assign('title', __('Course Grade Scale Details'));
$formatOptions = ['places' => 2, 'before' => false, 'decimals' => '.', 'thousands' => ','];
?>

<?php if (!empty($grade_scale) && !isset($grade_scale['error'])): ?>
    <h6 class="fs13">
        <span class="text-muted"><?= __('Grade Type:') ?></span>
        <i class="text-muted"><?= h($grade_scale['GradeType']['type']) ?></i>
    </h6>
    <h6 class="fs13">
        <span class="text-muted"><?= __('Grade Scale:') ?></span>
        <i class="text-muted"><?= h($grade_scale['GradeScale']['name'] . ' (' . $grade_scale['scale_by'] . ' level)') ?></i>
    </h6>
    <h6 class="fs13">
        <span class="text-muted"><?= __('Course:') ?></span>
        <span class="text-dark"><?= h($grade_scale['Course']['course_code_title']) ?></span>
    </h6>
    <div style="overflow-x:auto;">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th style="text-align: center; width:10%"><?= __('Grade') ?></th>
                <th style="text-align: center; width:20%"><?= __('Min') ?></th>
                <th style="text-align: center; width:20%"><?= __('Max') ?></th>
                <th style="text-align: center; width:20%"><?= __('Grade Point') ?></th>
                <th style="text-align: center; width:15%"><?= __('Pass Grade') ?></th>
                <th style="text-align: center; width:15%"><?= __('Repeatable') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($grade_scale['GradeScaleDetail'] as $grade_scale_detail): ?>
                <tr>
                    <td style="text-align: center;"><?= h($grade_scale_detail['grade']) ?></td>
                    <td style="text-align: center;"><?= $this->Number->format($grade_scale_detail['minimum_result'], $formatOptions) ?></td>
                    <td style="text-align: center;"><?= $this->Number->format($grade_scale_detail['maximum_result'], $formatOptions) ?></td>
                    <td style="text-align: center;"><?= h($grade_scale_detail['point_value']) ?></td>
                    <td style="text-align: center;">
                        <?= $grade_scale_detail['pass_grade'] == 1 ? '<span class="text-success">' . __('Yes') . '</span>' : '<span class="text-danger">' . __('No') . '</span>' ?>
                    </td>
                    <td style="text-align: center;">
                        <?= $grade_scale_detail['repeatable'] == 1 ? '<span class="text-success">' . __('Yes') . '</span>' : '<span>' . __('No') . '</span>' ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php elseif (isset($grade_scale['error'])): ?>
    <div class="alert alert-warning" style="font-family: 'Times New Roman', Times, serif; font-weight: normal; text-align: justify;">
        <span style="margin-right: 15px;"></span><?= h($grade_scale['error']) ?>
    </div>
<?php else: ?>
    <div class="alert alert-warning" style="font-family: 'Times New Roman', Times, serif; font-weight: normal; text-align: justify;">
        <span style="margin-right: 15px;"></span><?= __('Grade scale for the selected course is not found in the system.') ?>
    </div>
<?php endif; ?>
