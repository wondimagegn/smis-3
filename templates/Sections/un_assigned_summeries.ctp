<?php
if (count($programss) > 0) { ?>
    <table id="sectionNotAssignClass" cellpadding="0" cellspacing="0" class="table">
        <thead>
            <tr>
                <td style="border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85);" colspan="<?= (count($programss)+1); ?>" class="vcenter">
                    <span class="text-gray">
                    <br style="line-height: 0.5;"> 
                    Table: Summary of students who are not assigned to a section for <?= $sselectedAcademicYear; ?> <?php //echo $selectedYearLevelName . ' year, ' . $selectedProgramName .' - '. $selectedProgramTypeName . '.'; ?>
                    <?php // (isset($selectedCurriculumName) ? '<br>Selected Curriculum:  '. $selectedCurriculumName : ''); ?>
                    </span>
                </td>
            </tr>
            <tr>
                <th class="center"><!-- ProgramType/Program --></th>
                <?php
                $count_program = count($programss);
                $count_program_type = count($programTypess);

                foreach ($programss as $kp => $vp) { ?>
                    <th class="center"><?= (isset($vp) ? $vp: ''); ?></th>
                    <?php
                } ?>
            </tr>
        </thead>
        <tbody>
            <?php
            for ($i = 1; $i <= $count_program_type; $i++) {
                if (isset($programTypess[$i])) { ?>
                    <tr>
                        <td class="vcenter"><?= (isset($programTypess[$i]) ? $programTypess[$i] : ''); ?></td>
                        <?php
                        for ($j = 1; $j <= $count_program; $j++) { 
                            if (isset($programss[$j])) {?>
                                <td class="center"><?= (isset($summary_data[$programss[$j]][$programTypess[$i]]) && $summary_data[$programss[$j]][$programTypess[$i]] > 0 ? $summary_data[$programss[$j]][$programTypess[$i]] : '--'); ?></td>
                                <?php
                            } else { ?>
                                <td class="center">--</td>
                                <?php
                            }
                        } ?>
                    </tr>
                    <?php
                } 
            } ?>

            <?php
            if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT && isset($curriculum_unattached_student_count) && $curriculum_unattached_student_count > 0) { ?>
                <tr>
                    <td colspan="<?= (count($programs) + 1); ?>" class="vcenter"><?= ($curriculum_unattached_student_count > 1 ? $curriculum_unattached_student_count . ' students are' : $curriculum_unattached_student_count . ' student is'); ?>  not attached to any curriculum in your department from all programs. Thus, <?= ($curriculum_unattached_student_count > 1 ? ' these students' : ' this student'); ?> will not participate in any section assignment.</td>
                </tr>
                <?php
            } ?>
        </tbody>
    </table>
    <?php
} else { ?>
    <table id="sectionNotAssignClass" cellpadding="0" cellspacing="0" class="table">
        <thead>
            <tr>
                <td style="border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85);" colspan="<?= (count($programss)+1); ?>">
                    <span class="text-gray">
                    <br style="line-height: 0.5;"> 
                    You don't have any curriculums defined in your ROLE_DEPARTMENT
                    </span>
                </td>
            </tr>
        </thead>
    </table>
    <?php
} ?>