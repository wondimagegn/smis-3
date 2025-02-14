<?php
if (isset($top) && !empty($top)) {
    foreach ($top as $program => $programType) {
        foreach ($programType as $programTypeName => $statDetail) { ?>
           <!--  <p class="fs16">
                Top <?php //echo $this->data['Report']['top'] . ' ' . $this->data['Report']['gender']; ?> students as of <?= $this->data['Report']['acadamic_year']; ?> A/Y, and Semester <?= $this->data['Report']['semester']; ?><br />
                <strong> Program : </strong><?php //echo $program; ?><br />
                <strong> Program Type: </strong><?= $programTypeName; ?><br />
            </p> -->
            <div style="overflow-x:auto;">
                <table cellpadding="0" cellspacing="0" class="table">
                    <thead>
                        <tr>
                            <td colspan="9" style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85); line-height: 1.5;">
                                <span style="font-size:15px;font-weight:bold;"><?= ' Top '. $this->data['Report']['top'] . ' '.  (isset($this->data['Report']['gender']) && $this->data['Report']['gender'] != 'all' ? (ucfirst($this->data['Report']['gender'])) : '') .  ' Student List ' . ' ' . (isset($this->request->data['Report']['acadamic_year'])  ?  ' (' . $this->request->data['Report']['acadamic_year'] . '' . (isset($this->request->data['Report']['semester']) ? ', ' . ($this->request->data['Report']['semester'] == 'I' ? '1st Semester' : ($this->request->data['Report']['semester'] == 'II' ? '2nd Semester' : ($this->request->data['Report']['semester'] == 'III' ? '3rd Semester' : $this->request->data['Report']['semester'] . ' Semester'))) : '') . ')' : ''); ?></span><br>
                                <span class="text-gray" style="font-size: 13px; font-weight: bold">
                                    <?php //echo (isset($headerExplode[1]) && !empty($headerExplode[1]) ? $headerExplode[1] : ($headerExplode[2] == 'Remedial' ? 'Remedial Program' : 'Pre/Freshman')) . '' . (isset($headerExplode[0]) && !empty($headerExplode[0]) ? ' &nbsp; | &nbsp; ' . $headerExplode[0] : ''); ?><!-- <br> -->
                                    <?= $program . ' &nbsp; | &nbsp; ' . $programTypeName; ?> <br>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="center">#</th>
                            <th class="vcenter">Full Name</th>
                            <th class="center">Sex </th>
                            <th class="center">Student ID</th>
                            <th class="center">Department</th>
                            <th class="center">Year</th>
                            <th class="center">SGPA</th>
                            <th class="center">CGPA</th>
                            <th class="center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $count = 0;
                        foreach ($statDetail as $in => $val) { ?>
                            <tr class='jsView' data-animation="fade" data-reveal-id="myModal" data-reveal-ajax="/students/get_modal_box/<?= $val['Student']['id']; ?>">
                                <td class="center"><?= ++$count; ?></td>
                                <td class="vcenter"><?= $val['Student']['full_name']; ?></td>
                                <td class="center"><?= ((strcasecmp(trim($val['Student']['gender']), 'male') == 0) ? 'M': ((strcasecmp(trim($val['Student']['gender']), 'female') == 0) ? 'F' : trim($val['Student']['gender']))); ?></td>
                                <td class="center"><?= $val['Student']['studentnumber']; ?></td>
                                <td class="center"><?= ((isset($val['Student']['Department']['name']) && !empty($val['Student']['Department']['name'])) ? $val['Student']['Department']['name'] : ($val['Student']['Program']['id'] == PROGRAM_REMEDIAL ? 'Remedial Program' : 'Pre/Freshman') . ' - ' . $val['Student']['College']['shortname']); ?></td>
                                <td class="center"><?= (isset($val['Student']['yearLevel']) ? $val['Student']['yearLevel'] : ($val['Student']['Program']['id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')); ?></td>
                                <td class="center"><?= $val['StudentExamStatus']['sgpa']; ?></td>
                                <td class="center"><?= $val['StudentExamStatus']['cgpa']; ?></td>
                                <td class="center"><?= (isset($val['StudentExamStatus']['academic_status_id']) && !empty($val['AcademicStatus']['name']) ? $val['AcademicStatus']['name'] : '---'); ?></td>
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