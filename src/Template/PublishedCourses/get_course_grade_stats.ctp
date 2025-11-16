<?php
$this->assign('title', __('Course Grade Statistics'));
?>

<?php if (isset($gradeStatistics['statistics']) && !empty($gradeStatistics['statistics'])): ?>
    <br><br><br>
    <h6 style="font-size: 13px;">
        <span class="text-dark"><?= __('Grade Distribution:') ?></span>
    </h6>
    <table class="table table-bordered" style="width: 30%;">
        <thead>
        <tr>
            <th style="text-align: center;"><?= __('Grade') ?></th>
            <th style="text-align: center;"><?= __('Frequency') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($gradeStatistics['statistics'] as $grade => $freq): ?>
            <tr>
                <td style="text-align: center;"><?= h($grade) ?></td>
                <td style="text-align: center;"><?= h($freq) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
