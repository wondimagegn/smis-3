<?php
$this->assign('title', __('Unassigned Students Summary'));
?>

<?php if (!empty($programss)): ?>
    <div style="overflow-x:auto;">
        <table id="sectionNotAssignClass" class="table table-bordered">
            <thead>
            <tr>
                <td style="border-bottom: 2px solid #555;" colspan="<?= count($programss) + 1 ?>" class="text-center">
                        <span class="text-muted">
                            <br style="line-height: 0.5;">
                            <?= __(
                                'Table: Summary of students%s by Program and Program Type',
                                isset($sselectedAcademicYear) && !empty($sselectedAcademicYear) && $sselectedAcademicYear != '/undefined'
                                    ? sprintf(__(' admitted for %s'), h($sselectedAcademicYear))
                                    : ''
                            ) ?>
                        </span>
                </td>
            </tr>
            <tr>
                <th class="text-center"><?= __('ProgramType/Program') ?></th>
                <?php foreach ($programss as $kp => $vp): ?>
                    <th class="text-center"><?= h($vp ?? '') ?></th>
                <?php endforeach; ?>
            </tr>
            </thead>
            <tbody>
            <?php $count_program = count($programss); ?>
            <?php $count_program_type = count($programTypess); ?>
            <?php for ($i = 1; $i <= $count_program_type; $i++): ?>
                <?php if (isset($programTypess[$i])): ?>
                    <tr>
                        <td class="text-center"><?= h($programTypess[$i] ?? '') ?></td>
                        <?php for ($j = 1; $j <= $count_program; $j++): ?>
                            <td class="text-center">
                                <?= isset($programss[$j]) && isset($summary_data[$programss[$j]][$programTypess[$i]]) && $summary_data[$programss[$j]][$programTypess[$i]] > 0
                                    ? h($summary_data[$programss[$j]][$programTypess[$i]])
                                    : '--' ?>
                            </td>
                        <?php endfor; ?>
                    </tr>
                <?php endif; ?>
            <?php endfor; ?>
            <?php if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_DEPARTMENT && isset($curriculum_unattached_student_count) && $curriculum_unattached_student_count > 0): ?>
                <tr>
                    <td colspan="<?= count($programss) + 1 ?>" class="text-center">
                        <?= __(
                            '%s not attached to any curriculum in your department from all programs. Thus, %s will not participate in any section assignment.',
                            $curriculum_unattached_student_count > 1
                                ? sprintf(__('%s students are'), $curriculum_unattached_student_count)
                                : sprintf(__('%s student is'), $curriculum_unattached_student_count),
                            $curriculum_unattached_student_count > 1 ? __('these students') : __('this student')
                        ) ?>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <br>
<?php else: ?>
    <div style="overflow-x:auto;">
        <table id="sectionNotAssignClass" class="table table-bordered">
            <thead>
            <tr>
                <td style="border-bottom: 2px solid #555;" colspan="<?= count($programss) + 1 ?>">
                        <span class="text-muted">
                            <br style="line-height: 0.5;">
                            <?= __('You don\'t have any curriculums defined in your department.') ?>
                        </span>
                </td>
            </tr>
            </thead>
        </table>
    </div>
    <br>
<?php endif; ?>
