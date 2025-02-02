<?php
header("Expires: " . gmdate("D,d M YH:i:s") . " GMT");
header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=" . $filename . ".xls");
header("Content-Description: Exported as XLS");
?>

<?php
if (isset($top) && !empty($top)) {
    foreach ($top as $program => $programType) {
        foreach ($programType as $programTypeName => $statDetail) { ?>
            <p class="fs16">
                Top <?= $this->data['Report']['top'] . ' ' . $this->data['Report']['gender']; ?> students as of <?= $this->data['Report']['acadamic_year']; ?> A/Y, and Semester <?= $this->data['Report']['semester']; ?><br />
                <strong> Program : </strong><?= $program; ?><br />
                <strong> Program Type: </strong><?= $programTypeName; ?><br />
            </p>
            <div style="overflow-x:auto;">
                <table cellpadding="0" cellspacing="0" class="table">
                    <thead>
                        <tr>
                            <th class="center">#</th>
                            <th class="vcenter">Full Name</th>
                            <th class="center">Sex </th>
                            <th class="center">Student ID</th>
                            <th class="center">Department</th>
                            <th class="center">Year</th>
                            <th class="center">SGPA</th>
                            <th class="center">CGPA</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $count = 0;
                        foreach ($statDetail as $in => $val) { ?>
                            <tr class='jsView' data-animation="fade" data-reveal-id="myModal" data-reveal-ajax="/students/get_modal_box/<?= $val['Student']['id']; ?>">
                                <td class="vcenter"><?= ++$count; ?></td>
                                <td class="vcenter"><?= $val['Student']['full_name']; ?></td>
                                <td class="center"><?= ((strcasecmp(trim($val['Student']['gender']), 'male') == 0) ? 'M':'F'); ?></td>
                                <td class="center"><?= $val['Student']['studentnumber']; ?></td>
                                <td class="vcenter"><?= ((isset($val['Student']['Department']['name']) && !empty($val['Student']['Department']['name'])) ? $val['Student']['Department']['name'] : $val['Student']['College']['name']); ?></td>
                                <td class="center"><?= $val['Student']['yearLevel']; ?></td>
                                <td class="center"><?= $val['StudentExamStatus']['sgpa']; ?></td>
                                <td class="center"><?= $val['StudentExamStatus']['cgpa']; ?></td>
                            </tr>
                            <?php
                        } ?>
                    </tbody>
                </table>
            </div>
            <?php
        }
    }
} ?>