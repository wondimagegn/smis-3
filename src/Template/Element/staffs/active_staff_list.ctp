<?php
if (isset($distributionStatistics['getActiveStaffList']) && !empty($distributionStatistics['getActiveStaffList'])) { 
    foreach ($distributionStatistics['getActiveStaffList'] as $departmentNamee => $listStaff) {
        if (isset($listStaff) && !empty($listStaff)) { ?>
            <h6 class="fs16 text-gray"><?= $departmentNamee; ?></h6>
            <div style="overflow-x:auto;">
                <table cellpadding="0" cellspacing="0" class="table">
                    <thead>
                    <tr>
                        <th class="center" style="width: 5%;">#</th>
                        <th class="vcenter" style="width: 55%;">Full Name</th>
                        <th class="vcenter">Position</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php
                        $count = 0;
                        foreach ($listStaff as $k => $v) { ?>
                            <tr>
                                <td class="center"><?= ++$count; ?></td>
                                <td class="vcenter">
                                    <?php
                                    echo $v['Title']['title'] . ' ' . $v['Staff']['full_name'];
                                    if ($v['User']['is_admin'] == 1) {
                                        echo ' <strong>(Department Head Account)</strong> ';
                                    } ?>
                                </td>
                                <td class="vcenter"><?= $v['Position']['position']; ?></td>
                            </tr>
                            <?php 
                        } ?>
                    </tbody>
                </table>
            </div>
            <br>
            <?php
        }
    } ?>
    <?php
} ?>