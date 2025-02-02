<?php
if (isset($gradeChangeLists) && !empty($gradeChangeLists)) { ?>
    <h5><?= $headerLabel; ?></h5>
    <div style="overflow-x:auto;">
        <table cellpadding="0" cellspacing="0" class="table">
            <thead>
                <tr>
                    <th class="center"> # </th>
                    <th></th>
                    <th class="vcenter">Instructor</th>
                    <th class="vcenter">Student Name</th>
                    <th class="center">Sex</th>
                    <th class="center">Student ID </th>
                    <th class="center">Old</th>
                    <th class="center">New</th>
                    <th class="center">Course </th>
                    <th class="center">Initiated By </th>
                </tr>
            </thead>
            <tbody>
                <?php
                $count = 0;
                foreach ($gradeChangeLists as $staffName => $courseList) {
                    foreach ($courseList as $ck => $cd) { ?>
                        <tr>
                            <td class="center"><?= ++$count; ?></td>
                            <td class="vcenter" onclick="toggleView(this)" id="<?= $count; ?>"><?= $this->Html->image('plus2.gif', array('id' => 'i' . $count)); ?></td>
                            <td class="vcenter"><?= $staffName; ?></td>
                            <td class="vcenter"><?= $cd['full_name']; ?></td>
                            <td class="center"><?= (strcasecmp(trim($cd['gender']), 'male') == 0 ? 'M' : 'F'); ?></td>
                            <td class="center">
                                <?= $this->Html->link($cd['studentnumber'], '#', array('class' => 'jsview', 'data-animation' => "fade", 'data-reveal-id' => 'myModal', 'data-reveal-ajax' => "/students/get_modal_box/" . $cd['student_id'])); ?>
                            </td>
                            <td class="center"><?= $cd['oldGrade']; ?> </td>
                            <td class="center"><?= $cd['grade']; ?> </td>
                            <td class="center"><?= $cd['course']; ?> </td>
                            <td class="center">
                                <?php
                                if ($cd['manual_ng_conversion'] == 1) {
                                    echo '<strong style="color:red">Manual NG to F Conversion</strong>';
                                } else if ($cd['auto_ng_conversion'] == 1) {
                                    echo '<strong style="color:red">Automatic NG to F Conversion</strong>';
                                } else if ($cd['initiated_by_department'] == 1) {
                                    echo "Department";
                                } else if ($cd['initiated_by_department'] == 0 && $cd['manual_ng_conversion'] != 1 && $cd['auto_ng_conversion'] != 1) {
                                    echo "Instructor";
                                } ?>
                            </td>
                        </tr>
                        <tr id="c<?= $count; ?>" style="display:none;">
                            <td colspan="2" style="background-color: white;"> </td>
                            <td colspan="8" style="font-size:14px;background-color: white;">
                                <strong> Instructor: </strong> <?= $staffName; ?> <br />
                                <strong> Student: </strong> <?= $cd['full_name'] . ' (' . $cd['studentnumber'] . ')'; ?><br />
                                <strong> Grade change: </strong> from <strong> <?= $cd['oldGrade']; ?></strong> to <strong> <?= $cd['grade'] . '</strong> for <strong> ' . $cd['course'] . '</strong> Course'; ?> <br />
                                <?php
                                if ($cd['initiated_by_department'] != 1) { ?>
                                    <?= $cd['initiated_by_department'] == 0 && $cd['manual_ng_conversion'] != 1 && $cd['auto_ng_conversion'] != 1 ? '<strong> Request initiated by: </strong> Instructor <br />' : ($cd['manual_ng_conversion'] == 1  ? '<strong style="color:red">Manual NG to F Grade Conversion by Registrar</strong> <br />' : '<strong style="color:red">Automatic NG to F Conversion by the System</strong> <br />'); ?>
                                    <?php
                                    if ($cd['manual_ng_conversion'] != 1 && $cd['auto_ng_conversion'] != 1) { ?>
                                        <strong> Department Approved by: </strong> <?= $cd['department_approved_by'] . ' <strong> Reason: </strong> ' . (!empty($cd['department_reason']) ? $cd['department_reason'] : '---') . ' <strong> at </strong>' . (($cd['department_approval_date'] == '0000-00-00 00:00:00' || $cd['department_approval_date'] == '' || is_null($cd['department_approval_date'])) ? '' : ($this->Time->format("F j, Y h:i:s A", $cd['department_approval_date'], NULL, NULL))); ?> <br />
                                        <strong> College Approved by: </strong> <?= $cd['college_approved_by']  . ' <strong> Reason: </strong> ' . (!empty($cd['college_reason']) ? $cd['college_reason'] : '---') . ' <strong> at </strong>' . (($cd['college_approval_date'] == '0000-00-00 00:00:00' || $cd['college_approval_date'] == '' || is_null($cd['college_approval_date'])) ? '' : ($this->Time->format("F j, Y h:i:s A", $cd['college_approval_date'], NULL, NULL))); ?><br />
                                        <strong> Registrar Approved by: </strong> <?= $cd['registrar_approved_by']  . ' <strong> Reason: </strong> ' . (!empty($cd['registrar_reason']) ? $cd['registrar_reason'] : '---') . ' <strong> at </strong>' . (($cd['registrar_approval_date'] == '0000-00-00 00:00:00' || $cd['registrar_approval_date'] == '' || is_null($cd['registrar_approval_date'])) ? '' : ($this->Time->format("F j, Y h:i:s A", $cd['registrar_approval_date'], NULL, NULL))); ?><br />
                                        <?php
                                    } else if ($cd['manual_ng_conversion'] == 1 || $cd['auto_ng_conversion'] == 1) {
                                        echo '<strong> Converted by: </strong>' . $cd['registrar_approved_by']  . ' <strong> Reason: </strong> ' . (!empty($cd['registrar_reason']) ? $cd['registrar_reason'] : '---') . '<strong> Date Converted </strong>' . (($cd['modified'] == '0000-00-00 00:00:00' || $cd['modified'] == '' || is_null($cd['modified'])) ? '' : ($this->Time->format("F j, Y h:i:s A", $cd['modified'], NULL, NULL))) . '<br />';
                                    }
                                } else if ($cd['initiated_by_department'] == 1) { ?>
                                    <strong> Request initiated by: </strong> Department <br />
                                    <strong> College Approved by: </strong> <?= $cd['college_approved_by'] . ' <strong> Reason: </strong> ' . (!empty($cd['college_reason']) ? $cd['college_reason'] : '---') . ' <strong> at </strong>' . (($cd['college_approval_date'] == '0000-00-00 00:00:00' || $cd['college_approval_date'] == '' || is_null($cd['college_approval_date'])) ? '' : ($this->Time->format("F j, Y h:i:s A", $cd['college_approval_date'], NULL, NULL))); ?><br />
                                     <strong> Registrar Approved by: </strong> <?= $cd['registrar_approved_by'] . ' <strong> Reason: </strong> ' . (!empty($cd['registrar_reason']) ? $cd['registrar_reason'] : '---') . ' <strong> at </strong>' . (($cd['registrar_approval_date'] == '0000-00-00 00:00:00' || $cd['registrar_approval_date'] == '' || is_null($cd['registrar_approval_date'])) ? '' : ($this->Time->format("F j, Y h:i:s A", $cd['registrar_approval_date'], NULL, NULL))); ?><br />
                                    <?php
                                }
                                ?>
                            </td>
                        </tr>
                        <?php
                    }
                } ?>
            </tbody>
        </table>
    </div>
    <?php
} ?>