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
if (isset($notRegisteredList) &&  !empty($notRegisteredList)) { $totalStudents = 0;
	
    $totalStudents = 0;
    $totalMaleStudents = 0;
	$totalFemaleStudents = 0;
	$withUnknowntatus = 0;

    if (!empty($headerLabel)) { ?>
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td colspan= <?= (isset($this->data['Report']['exclude_graduated']) &&  $this->data['Report']['exclude_graduated'] == 1 ? '8' : '7'); ?>>
					<hr><?= $headerLabel; ?>
				</td>
			</tr>
		</table>
		<?php
	}

    foreach ($notRegisteredList as $programD => $list) {
        $headerExplode = explode('~', $programD);

        if (count($list) == 0) {
            continue;
        }
        //debug($list); ?>

        <!-- <br /> -->

       <!-- <p class="fs16">
			<strong>College: </strong> <?php //echo $headerExplode[0]; ?><br />
			<strong>Department: </strong> <?php //echo $headerExplode[1]; ?><br />
			<strong>Program: </strong> <?php //echo $headerExplode[2]; ?><br />
			<strong>Program Type: </strong> <?php //echo $headerExplode[3]; ?><br />
			<strong>Section: </strong> <?php //echo $headerExplode[4]; ?><br />
			<strong>Academic Year: </strong> <?php //echo $headerExplode[5]; ?><br />
			<strong>Semester: </strong> <?php //echo $headerExplode[6]; ?><br />
		</p> -->

        <!-- <div style="overflow-x:auto;"> -->
            <table cellpadding="0" cellspacing="0" class="table">
                <thead>
                    <tr>
						<td colspan="8">
                            <hr>
							<?= $headerExplode[4] . ' ' . (isset($headerExplode[5])  ?  ' ('.  $headerExplode[7] . ',  ' . $headerExplode[5] . '' . (isset($headerExplode[6]) ? ', ' . ($headerExplode[6] == 'I' ? '1st Semester' : ($headerExplode[6] == 'II' ? '2nd Semester' : ($headerExplode[6] == 'III' ? '3rd Semester' : $headerExplode[6] . ' Semester'))) : '') . ')' : ''); ?><br>
							<?= (isset($headerExplode[1]) && !empty($headerExplode[1]) ? $headerExplode[1] : ($headerExplode[2] == 'Remedial' ? 'Remedial Program' : 'Pre/Freshman')) . '' . (isset($headerExplode[0]) && !empty($headerExplode[0]) ? ' &nbsp; | &nbsp; ' . $headerExplode[0] : ''); ?><br>
							<?= $headerExplode[2] . ' &nbsp; | &nbsp; ' . $headerExplode[3]; ?>
                            <hr>
						</td>
					</tr>
                    <tr>
                        <th style="vertical-align: middle; text-align: center;">#</th>
                        <th style="vertical-align: middle; text-align: left;">Full Name</th>
                        <th style="vertical-align: middle; text-align: center;">Student ID</th>
                        <th style="vertical-align: middle; text-align: center;">Sex</th>
                        <th style="vertical-align: middle; text-align: center;"><?= (isset($headerExplode[8]) && !empty($headerExplode[8]) ? $headerExplode[8] : 'Credit'); ?></th>
                        <th style="vertical-align: middle; text-align: center;">SGPA</th>
                        <th style="vertical-align: middle; text-align: center;">CGPA</th>
                        <th style="vertical-align: middle; text-align: center;">Last Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $count = 0;
                    foreach ($list as $ko => $val) {
                        if ($val['academic_status_id'] != DISMISSED_ACADEMIC_STATUS_ID) { ?>
                            <?php $totalStudents++; ?>
                            <tr class='jsView' data-animation="fade" data-reveal-id="myModal" data-reveal-ajax="/students/get_modal_box/<?= $val['id']; ?>">
                                <td style="vertical-align: middle; text-align: center;"><?= ++$count; ?></td>
                                <td style="vertical-align: middle; text-align: left;"><?= $val['first_name'] . ' ' . $val['middle_name'] . ' ' . $val['last_name']; ?></td>
                                <td style="vertical-align: middle; text-align: center;"><?= $val['studentnumber']; ?></td>
                                <td style="vertical-align: middle; text-align: center;"><?php if (strcasecmp(trim($val['gender']), 'male') == 0) { echo 'M'; $totalMaleStudents++; } else { echo 'F'; $totalFemaleStudents++; } ?></td>
                                <td style="vertical-align: middle; text-align: center;"><?= $val['credit_hour_sum']; ?></td>
                                <td style="vertical-align: middle; text-align: center;"><?= $val['sgpa']; ?></td>
                                <td style="vertical-align: middle; text-align: center;" style="font-weight: bold;"><?= $val['cgpa']; ?></td>
                                <td style="vertical-align: middle; text-align: center;">
                                    <?php
                                    if (isset($academicStatus[$val['academic_status_id']])) {
                                        echo $academicStatus[$val['academic_status_id']];
                                    } else {
                                        echo '---';
                                        $withUnknowntatus++;
                                    } ?>
                                </td>
                            </tr>
                            <?php
                        }
                    } ?>
                </tbody>
            </table>
        <!--  </div> -->
        <!-- <hr> -->
        <br>
        <?php
    } ?>

    <br />
    <table cellpadding="0" cellspacing="0" class="table">
		<tr>
			<td colspan="8">
                <span class="text-black fs14">
                    <!-- <hr /> -->
                    <strong>Stats for selected Active List: </strong><br />
                    Total: <?= ($totalStudents) ?> <br />
                    Male: <?= ($totalMaleStudents) . ($totalMaleStudents != 0 && $totalStudents != 0 ? '&nbsp; (' . ($this->Number->precision((($totalMaleStudents / $totalStudents) * 100), 2) . '%)') : ''); ?><br />
                    Female: <?= ($totalFemaleStudents) . ($totalFemaleStudents != 0 && $totalStudents != 0 ? '&nbsp; (' . ($this->Number->precision((($totalFemaleStudents / $totalStudents) * 100), 2) . '%)') : ''); ?><br />
                    With Unknown Status: <?= ($withUnknowntatus) . ($withUnknowntatus != 0 && $totalStudents != 0 ? '&nbsp; (' . ($this->Number->precision((($withUnknowntatus / $totalStudents) * 100), 2) . '%)') : ''); ?><br />
                    <!-- <hr /> -->
                </span>

                <?php
                if ($withUnknowntatus != 0) { ?>
                    <hr>
                    <blockquote>
                        <cite>Important Note</cite>
                        If there are students with unknown Status, it might be connected with not maintaining the minimum credits required for status determination defined for the selected program and/or program type.
                        Make sure stuudents registered for courses above the defined minimum credit for status determination by referring the following table.
                    </blockquote>
                    
                    <hr>
                    <div style="overflow-x:auto;">
                        <table cellpadding="0" cellspacing="0" class="table">
                            <thead>
                                <tr>
                                    <td style="vertical-align: middle; text-align: left;">Program</td>
                                    <td style="vertical-align: middle; text-align: left;">Program Type</td>
                                    <td style="vertical-align: middle; text-align: center;">Credit (Min)</td>
                                    <td style="vertical-align: middle; text-align: center;">ECTS (Min)</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $generalSettings = ClassRegistry::init('GeneralSetting')->find('all', array('recursive' => -1));

                                if (!empty($generalSettings)) {
                                    foreach ($generalSettings as $keyyy => &$valll) {
                                        $programs = ClassRegistry::init('Program')->find('list', array('conditions' => array('id' => unserialize($valll['GeneralSetting']['program_id']))));
                                        $programTypes = ClassRegistry::init('ProgramType')->find('list', array('conditions' => array('id' => unserialize($valll['GeneralSetting']['program_type_id']))));
                                        $valll['GeneralSetting']['program_id'] = array_values($programs);
                                        $valll['GeneralSetting']['program_type_id'] = array_values($programTypes);
                                    }
                                    //debug($generalSettings);

                                    foreach ($generalSettings as $generalSetting) { ?>
                                        <tr>
                                            <td style="vertical-align: middle; text-align: left;">
                                                <?php
                                                foreach ($generalSetting['GeneralSetting']['program_id'] as $key => $value) {
                                                    echo $value . '<br/>';
                                                } ?>
                                            </td>
                                            <td style="vertical-align: middle; text-align: left;">
                                                <?php
                                                foreach ($generalSetting['GeneralSetting']['program_type_id'] as $key => $value) {
                                                    echo $value . ', ';
                                                } ?>
                                            </td>
                                            <td style="vertical-align: middle; text-align: center;"><?= $generalSetting['GeneralSetting']['minimumCreditForStatus']; ?></td>
                                            <td style="vertical-align: middle; text-align: center;"><?= round(($generalSetting['GeneralSetting']['minimumCreditForStatus'] * CREDIT_TO_ECTS), 0); ?></td>
                                        </tr>
                                        <?php
                                    }  
                                } ?>
                            </tbody>
                        </table>
                    </div>
                    <?php
                } ?>
            </td>
        </tr>
    </table>
    <?php
} ?>
