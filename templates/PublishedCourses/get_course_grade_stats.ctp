<?php
if (isset($gradeStatistics['statistics']) && !empty($gradeStatistics['statistics'])) { ?>
    <?php //echo $this->element('reports/graph-grade-statistics'); ?>
    <br><br><br>
    <h6 class="fs13"><span class="text-black">Grade Distribution: </h6>
    <table cellpadding="0" cellspacing="0" class="table" style="width:30%;">
        <tr>
            <th style="text-align: center;"> Grade </th>
            <th style="text-align: center;"> Frequency </th>
        </tr>
        <?php
        $count = 0;
        foreach ($gradeStatistics['statistics'] as $grade => $freq) { ?>
            <tr>
                <td style="text-align: center;"> <?= $grade; ?> </td>
                <td style="text-align: center;"><?= $freq; ?> </td>
            </tr>
            <?php 
        } ?>
    </table>
    <?php
} ?>