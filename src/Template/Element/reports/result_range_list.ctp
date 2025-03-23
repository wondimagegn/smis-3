<?php
if (isset($resultBy) && !empty($resultBy)) {
    foreach ($resultBy as $program => $statDetail) {
        $headerExplode = explode('~', $program); ?>
        
        <br />

        <h6 class="fs14 text-gray">
            <strong>College: &nbsp;&nbsp;</strong><?= $headerExplode[0]; ?><br />
            <strong>Program: &nbsp;&nbsp;</strong><?= $headerExplode[1]; ?><br />
            <strong>Program Type: &nbsp;&nbsp;</strong><?= $headerExplode[2]; ?><br />
        </h6>

        <table cellpadding="0" cellspacing="0" class="table">
            <thead>
                <tr>
                    <th class="center">#</th>
                    <th class="vcenter">Full Name</th>
                    <th class="vcenter">Student ID</th>
                    <th class="center">Sex</th>
                    <th class="center">Department</th>
                    <th class="center">Section</th>
                    <th class="center">Year Level</th>
                    <th class="center">ACY</th>
                    <th class="center">Sem</th>
                    <th class="center">SGPA</th>
                    <th class="center">CGPA</th>
                </tr>
            </thead>
            <tbody>

                <?php
                $count = 0;
                $totalMaleCount = 0;
                $totalFemaleCount = 0;

                foreach ($statDetail as $in => $val) {?>
                    <tr class='jsView' data-animation="fade" data-reveal-id="myModal" data-reveal-ajax="/students/get_modal_box/<?= $val['id']; ?>">
                        <td class="center"><?= ++$count; ?> </td>
                        <td class="vcenter"><?= $val['first_name'] . ' ' . $val['middle_name'] . ' ' . $val['last_name']; ?></td>
                        <td class="vcenter"><?= $val['studentnumber']; ?></td>
                        <td class="center"><?php if (strcasecmp(trim($val['gender']), 'male') == 0) {  echo 'M'; $totalMaleCount++; } else {  echo 'F'; $totalFemaleCount++;  } ?></td>
                        <td class="vcenter"><?= (isset($val['Department']) ? $val['Department'] : 'Pre/Freshman'); ?></td>
                        <td class="center"><?= $val['Section']; ?></td>
                        <td class="center"><?= $val['YearLevel']; ?></td>
                        <td class="center"><?= $val['AcademicYear']; ?></td>
                        <td class="center"><?= $val['Semester']; ?></td>
                        <td class="center"><?= $val['sgpa']; ?></td>
                        <td class="center"><?= $val['cgpa']; ?></td>
                    </tr>
                    <?php
                } ?>
                <tr>
                    <td> </td>
                    <td colspan="6"><strong class="text-black fs14">Male: <?= $totalMaleCount; ?>&nbsp;(<?= ($this->Number->precision((($totalMaleCount / ($totalFemaleCount + $totalMaleCount)) * 100), 2) . '%'); ?>)</strong></td>
                </tr>
                <tr>
                    <td> </td>
                    <td colspan="6"><strong class="text-black fs14">Female: <?php echo $totalFemaleCount; ?>&nbsp;(<?= ($this->Number->precision((($totalFemaleCount / ($totalFemaleCount + $totalMaleCount)) * 100), 2) . '%'); ?>)</strong></td>
                </tr>
            </tbody>
        </table>
        <?php
    }
} ?>