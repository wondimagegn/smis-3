<?php
if (isset($notAssignedCourseeList) && !empty($notAssignedCourseeList)) { ?>
    <h6 class="text-gray"><?= $headerLabel; ?></h6>
    <div style="overflow-x:auto;">
        <table style="width:100%" cellpadding="0" cellspacing="0" class='table'>
            <thead>
                <tr>
                    <th class="center">#</th>
                    <th class="vcenter">Course Department </th>
                    <th class="vcenter">Course</th>
                    <th class="center">Section</th>
                    <th class="center">Program</th>
                    <th class="center">Program Type</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $count = 0;
                foreach ($notAssignedCourseeList as $departmentNamee => $courseList) {
                    $dptName = !empty($departmentNamee) ? $departmentNamee : 'Freshman'; ?>
                    <tr>
                        <td> </td>
                        <td colspan="5">Student Department: <?= $dptName;  ?></td>
                    </tr>
                    <?php
                    if (!empty($courseList)) {
                        foreach ($courseList as $rkey => $rvalue) {
                            $count = 0;
                            $year = !empty($rkey) ? $rkey : '1st'; ?>

                            <tr> 
                                <td style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85);"> </td>
                                <td colspan="5" style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85);">Year Level: <?= $year; ?></td>
                            </tr>

                            <?php
                            if (isset($rvalue) && !empty($rvalue)) {
                                foreach ($rvalue as $mn => $ym) { ?>
                                    <tr>
                                        <td class="center"><?= ++$count; ?></td>
                                        <td class="vcenter"><?= isset($ym['GivenByDepartment']['name']) ? $ym['GivenByDepartment']['name'] : '<<-- Not Dispatched -->>'; ?></td>
                                        <td class="vcenter"><?= $ym['Course']['course_title'] . ' (' . $ym['Course']['course_code'] .')'; ?> </td>
                                        <td class="center"><?= $ym['Section']['name'] ?></td>
                                        <td class="center"><?= $ym['Program']['name'] ?></td>
                                        <td class="center"><?= $ym['ProgramType']['name']; ?></td>
                                    </tr>
                                    <?php
                                }
                            } else { ?>
                                <tr>
                                    <td colspan="6" class="info-message info-box" style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85);">Either all courses are assigned to instructors, or no course is published for <?= $rkey; ?> year <?= $departmentNamee; ?> students for <?= $this->data['Report']['acadamic_year'] . ', '. $this->data['Report']['semester'] .' semester.'; ?></td>
                                </tr>
                                <?php
                            }
                        }
                    }
                } ?>
            </tbody>
        </table>
    </div>
    <br>
    <?php
} ?>