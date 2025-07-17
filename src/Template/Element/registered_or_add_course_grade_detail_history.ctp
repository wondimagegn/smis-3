<?php
/**
 * @var \App\View\AppView $this
 * @var array $student_exam_grade_history
 * @var array $student_exam_grade_change_history
 * @var bool $freshman_program
 * @var string $approver
 * @var string $approver_c
 */
use Cake\I18n\Time;

$approver = $freshman_program ? 'freshman program' : 'department';
$approver_c = $freshman_program ? 'Freshman Program' : 'Department';
?>

<?php if (!empty($student_exam_grade_history)): ?>
    <div style="font-weight:bold; font-size:14px; padding: 10px;"><?= __('Grade History Detail (From recent to old)') ?></div>
<table cellpadding="0" cellspacing="0" class="table">
    <?php endif; ?>

    <?php if (!empty($student_exam_grade_change_history)): ?>
        <?php for ($i = count($student_exam_grade_change_history) - 1; $i >= 0; $i--): ?>
            <?php if (strcasecmp($student_exam_grade_change_history[$i]['type'], 'Change') == 0): ?>
                <?php
                $exam_grade_change = $student_exam_grade_change_history[$i]['ExamGrade'];
                $reject_count = 1;
                ?>
                <tr>
                    <th colspan="2">
                        <?php
                        $department_reply = false;
                        if (isset($exam_grade_change['manual_ng_conversion']) && $exam_grade_change['manual_ng_conversion'] == 1) {
                            echo '<b>' . __('Registrar NG Grade Conversion') . '</b>';
                        } elseif (isset($exam_grade_change['auto_ng_conversion']) && $exam_grade_change['auto_ng_conversion'] == 1) {
                            echo '<b>' . __('Automatic F') . '</b>';
                        } elseif (!is_null($exam_grade_change['makeup_exam_result'] ?? null)) {
                            echo isset($exam_grade_change['department_reply']) && $exam_grade_change['department_reply'] == 0
                                ? (is_null($exam_grade_change['makeup_exam_id'] ?? null) ? __('Supplementary Exam') : __('Makeup Exam'))
                                : h($approver_c) . ' ' . __('response for registrar') . ' ' . (is_null($exam_grade_change['makeup_exam_id'] ?? null) ? __('supplementary exam') : __('makeup exam')) . ' ' . __('grade rejection');
                            if (isset($exam_grade_change['department_reply']) && $exam_grade_change['department_reply'] == 1) {
                                $department_reply = true;
                            }
                        } else {
                            echo __('Exam Grade Change') . ' (' . (isset($exam_grade_change['initiated_by_department']) && $exam_grade_change['initiated_by_department'] == 1 ? __('By the Department') : __('By the Instructor')) . ')';
                        }
                        ?>
                    </th>
                </tr>
                <?php if (isset($exam_grade_change['manual_ng_conversion']) && $exam_grade_change['manual_ng_conversion'] == 1): ?>
                    <tr>
                        <td style="width:30%; font-weight:bold; background-color:white;"><?= __('NG Converted to') ?>:</td>
                        <td style="width:70%; background-color:white;"><b><?= !empty($exam_grade_change['grade']) ? h($exam_grade_change['grade']) : '---' ?></b></td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold"><?= __('Minute Number') ?>:</td>
                        <td><b><?= !empty($exam_grade_change['minute_number']) ? h($exam_grade_change['minute_number']) : '---' ?></b></td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold; background-color:white;"><?= __('Conversion Date') ?>:</td>
                        <td style="background-color:white;">
                            <?= $exam_grade_change['created'] instanceof \Cake\I18n\FrozenTime
                                ? $exam_grade_change['created']->format('F j, Y h:i:s A')
                                : (new Time($exam_grade_change['created']))->format('F j, Y h:i:s A') ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold"><?= __('Converted By') ?>:</td>
                        <td><?= !empty($exam_grade_change['manual_ng_converted_by_name']) ? h($exam_grade_change['manual_ng_converted_by_name']) : '---' ?></td>
                    </tr>
                <?php elseif (isset($exam_grade_change['auto_ng_conversion']) && $exam_grade_change['auto_ng_conversion'] == 1): ?>
                    <tr>
                        <td style="width:30%; font-weight:bold; background-color:white;"><?= __('Auto Grade') ?>:</td>
                        <td style="width:70%; background-color:white;"><b><?= !empty($exam_grade_change['grade']) ? h($exam_grade_change['grade']) : '---' ?></b></td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold"><?= __('Auto Conversion Date') ?>:</td>
                        <td>
                            <?= $exam_grade_change['created'] instanceof \Cake\I18n\FrozenTime
                                ? $exam_grade_change['created']->format('F j, Y h:i:s A')
                                : (new Time($exam_grade_change['created']))->format('F j, Y h:i:s A') ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php if (!$department_reply): ?>
                        <tr>
                            <td style="width:30%; font-weight:bold; background-color:white;"><?= __('Grade') ?>:</td>
                            <td style="width:70%; background-color:white;"><b><?= !empty($exam_grade_change['grade']) ? h($exam_grade_change['grade']) : '---' ?></b></td>
                        </tr>
                        <?php if (!empty($exam_grade_change['minute_number'])): ?>
                            <tr>
                                <td style="width:28%; font-weight:bold"><?= __('Minute Number') ?>:</td>
                                <td style="width:72%"><?= h($exam_grade_change['minute_number']) ?></td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <td style="width:28%; font-weight:bold; background-color:white;"><?= __('Exam Result') ?>:</td>
                            <td style="width:72%; background-color:white;"><b><?= is_null($exam_grade_change['makeup_exam_result'] ?? null) ? h($exam_grade_change['result'] ?? '---') : h($exam_grade_change['makeup_exam_result']) ?></b></td>
                        </tr>
                        <?php if (is_null($exam_grade_change['makeup_exam_result'] ?? null)): ?>
                            <tr>
                                <td style="width:28%; font-weight:bold"><?= __('Grade Change Reason') ?>:</td>
                                <td style="width:72%"><?= !empty($exam_grade_change['reason']) ? h($exam_grade_change['reason']) : '---' ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if (!is_null($exam_grade_change['makeup_exam_result'] ?? null) && is_null($exam_grade_change['makeup_exam_id'] ?? null)): ?>
                            <tr>
                                <td style="width:28%; font-weight:bold"><?= __('Remark') ?>:</td>
                                <td style="width:72%"><?= !empty($exam_grade_change['reason']) ? h($exam_grade_change['reason']) : '---' ?></td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <td style="font-weight:bold; background-color:white;"><?= __('Request Date') ?>:</td>
                            <td style="background-color:white;">
                                <?= $exam_grade_change['created'] instanceof \Cake\I18n\FrozenTime
                                    ? $exam_grade_change['created']->format('F j, Y h:i:s A')
                                    : (new Time($exam_grade_change['created']))->format('F j, Y h:i:s A') ?>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php if (!(empty($exam_grade_change['makeup_exam_id'] ?? null) && !is_null($exam_grade_change['makeup_exam_result'] ?? null))): ?>
                        <tr>
                            <td style="font-weight:bold"><?= h($approver_c) ?> <?= __('Approval') ?>:</td>
                            <td class="<?= isset($exam_grade_change['department_approval']) && $exam_grade_change['department_approval'] == 1 ? 'accepted' : ($exam_grade_change['department_approval'] == -1 ? 'rejected' : 'on-process') ?>">
                                <?= isset($exam_grade_change['department_approval']) && $exam_grade_change['department_approval'] == 1 ? __('Accepted') : ($exam_grade_change['department_approval'] == -1 ? __('Rejected') : __('On Process')) ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="font-weight:bold; background-color:white;"><?= isset($exam_grade_change['department_approval']) && $exam_grade_change['department_approval'] == -1 ? __('Rejected By') : __('Approved By') ?>:</td>
                            <td style="background-color:white;"><?= !empty($exam_grade_change['department_approved_by_name']) ? h($exam_grade_change['department_approved_by_name']) : '---' ?></td>
                        </tr>
                        <tr>
                            <td style="font-weight:bold"><?= h($approver_c) ?> <?= __('Remark') ?>:</td>
                            <td><?= !empty($exam_grade_change['department_reason']) ? h($exam_grade_change['department_reason']) : '---' ?></td>
                        </tr>
                        <tr>
                            <td style="font-weight:bold; background-color:white;"><?= h($approver_c) ?> <?= isset($exam_grade_change['department_approval']) && $exam_grade_change['department_approval'] == -1 ? __('Rejection Date') : __('Approval Date') ?>:</td>
                            <td style="background-color:white;">
                                <?= empty($exam_grade_change['department_approval_date']) || is_null($exam_grade_change['department_approval_date']) || $exam_grade_change['department_approval_date'] == '0000-00-00 00:00:00'
                                    ? '---'
                                    : ($exam_grade_change['department_approval_date'] instanceof \Cake\I18n\FrozenTime
                                        ? $exam_grade_change['department_approval_date']->format('F j, Y h:i:s A')
                                        : (new Time($exam_grade_change['department_approval_date']))->format('F j, Y h:i:s A')) ?>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php if (isset($exam_grade_change['department_reply']) && $exam_grade_change['department_reply'] == 1 && empty($exam_grade_change['makeup_exam_id'] ?? null) && !is_null($exam_grade_change['makeup_exam_result'] ?? null)): ?>
                        <tr>
                            <td style="width:30%; font-weight:bold; background-color:white;"><?= __('Grade') ?>:</td>
                            <td style="width:70%; background-color:white;"><b><?= !empty($exam_grade_change['grade']) ? h($exam_grade_change['grade']) : '---' ?></b></td>
                        </tr>
                        <tr>
                            <td style="width:28%; font-weight:bold"><?= __('Exam Result') ?>:</td>
                            <td style="width:72%"><?= is_null($exam_grade_change['makeup_exam_result'] ?? null) ? h($exam_grade_change['result'] ?? '---') : h($exam_grade_change['makeup_exam_result']) ?></td>
                        </tr>
                        <tr>
                            <td style="font-weight:bold; background-color:white;"><?= h($approver_c) ?> <?= __('Reply') ?>:</td>
                            <td style="background-color:white;"><?= !empty($exam_grade_change['department_reason']) ? h($exam_grade_change['department_reason']) : '---' ?></td>
                        </tr>
                        <tr>
                            <td style="font-weight:bold"><?= __('Reply By') ?>:</td>
                            <td><?= !empty($exam_grade_change['department_approved_by_name']) ? h($exam_grade_change['department_approved_by_name']) : '---' ?></td>
                        </tr>
                        <tr>
                            <td style="font-weight:bold; background-color:white;"><?= __('Reply Date') ?>:</td>
                            <td style="background-color:white;">
                                <?= empty($exam_grade_change['department_approval_date']) || is_null($exam_grade_change['department_approval_date']) || $exam_grade_change['department_approval_date'] == '0000-00-00 00:00:00'
                                    ? '---'
                                    : ($exam_grade_change['department_approval_date'] instanceof \Cake\I18n\FrozenTime
                                        ? $exam_grade_change['department_approval_date']->format('F j, Y h:i:s A')
                                        : (new Time($exam_grade_change['department_approval_date']))->format('F j, Y h:i:s A')) ?>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php if (is_null($exam_grade_change['makeup_exam_result'] ?? null)): ?>
                        <tr>
                            <td style="font-weight:bold; background-color:white;"><?= __('College Approval') ?>:</td>
                            <td style="background-color:white;" class="<?= isset($exam_grade_change['college_approval']) && $exam_grade_change['college_approval'] == 1 ? 'accepted' : ($exam_grade_change['college_approval'] == -1 ? 'rejected' : (isset($exam_grade_change['department_approval']) && $exam_grade_change['department_approval'] == 1 ? 'on-process' : '')) ?>">
                                <?= isset($exam_grade_change['college_approval']) && $exam_grade_change['college_approval'] == 1 ? __('Accepted') : ($exam_grade_change['college_approval'] == -1 ? __('Rejected') : (isset($exam_grade_change['department_approval']) && $exam_grade_change['department_approval'] == 1 ? __('On Process') : '---')) ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="font-weight:bold"><?= isset($exam_grade_change['college_approval']) && $exam_grade_change['college_approval'] == -1 ? __('Rejected By') : __('Approved By') ?>:</td>
                            <td><?= !empty($exam_grade_change['college_approved_by_name']) ? h($exam_grade_change['college_approved_by_name']) : '---' ?></td>
                        </tr>
                        <tr>
                            <td style="font-weight:bold; background-color:white;"><?= __('College Remark') ?>:</td>
                            <td style="background-color:white;"><?= !empty($exam_grade_change['college_reason']) ? h($exam_grade_change['college_reason']) : '---' ?></td>
                        </tr>
                        <tr>
                            <td style="font-weight:bold"><?= isset($exam_grade_change['college_approval']) && $exam_grade_change['college_approval'] == -1 ? __('College Rejected Date') : __('College Approval Date') ?>:</td>
                            <td>
                                <?= empty($exam_grade_change['college_approval_date']) || is_null($exam_grade_change['college_approval_date']) || $exam_grade_change['college_approval_date'] == '0000-00-00 00:00:00'
                                    ? '---'
                                    : ($exam_grade_change['college_approval_date'] instanceof \Cake\I18n\FrozenTime
                                        ? $exam_grade_change['college_approval_date']->format('F j, Y h:i:s A')
                                        : (new Time($exam_grade_change['college_approval_date']))->format('F j, Y h:i:s A')) ?>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php if (!is_null($exam_grade_change['makeup_exam_result'] ?? null)): ?>
                        <tr>
                            <td style="font-weight:bold; background-color:white;"><?= __('Registrar Confirmation') ?>:</td>
                            <td style="background-color:white;" class="<?= isset($exam_grade_change['registrar_approval']) && $exam_grade_change['registrar_approval'] == 1 ? 'accepted' : ($exam_grade_change['registrar_approval'] == -1 ? 'rejected' : (isset($exam_grade_change['department_approval']) && $exam_grade_change['department_approval'] == 1 ? 'on-process' : '')) ?>">
                                <?= isset($exam_grade_change['registrar_approval']) && $exam_grade_change['registrar_approval'] == 1 ? __('Accepted') : ($exam_grade_change['registrar_approval'] == -1 ? __('Rejected') : (isset($exam_grade_change['department_approval']) && $exam_grade_change['department_approval'] == 1 ? __('On Process') : '---')) ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td style="font-weight:bold; background-color:white;"><?= __('Registrar Confirmation') ?>:</td>
                            <td style="background-color:white;" class="<?= isset($exam_grade_change['registrar_approval']) && $exam_grade_change['registrar_approval'] == 1 ? 'accepted' : ($exam_grade_change['registrar_approval'] == -1 ? 'rejected' : (isset($exam_grade_change['college_approval']) && $exam_grade_change['college_approval'] == 1 ? 'on-process' : '')) ?>">
                                <?= isset($exam_grade_change['registrar_approval']) && $exam_grade_change['registrar_approval'] == 1 ? __('Accepted') : ($exam_grade_change['registrar_approval'] == -1 ? __('Rejected') : (isset($exam_grade_change['college_approval']) && $exam_grade_change['college_approval'] == 1 ? __('On Process') : '---')) ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <tr>
                        <td style="font-weight:bold"><?= isset($exam_grade_change['registrar_approval']) && $exam_grade_change['registrar_approval'] == -1 ? __('Registrar Rejected By') : __('Registrar Confirmed By') ?>:</td>
                        <td><?= !empty($exam_grade_change['registrar_approved_by_name']) ? h($exam_grade_change['registrar_approved_by_name']) : '---' ?></td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold; background-color:white;"><?= __('Registrar Remark') ?>:</td>
                        <td style="background-color:white;"><?= !empty($exam_grade_change['registrar_reason']) ? h($exam_grade_change['registrar_reason']) : '---' ?></td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold"><?= isset($exam_grade_change['registrar_approval']) && $exam_grade_change['registrar_approval'] == -1 ? __('Registrar Rejected Date') : __('Registrar Confirmation Date') ?>:</td>
                        <td>
                            <?= empty($exam_grade_change['registrar_approval_date']) || is_null($exam_grade_change['registrar_approval_date']) || $exam_grade_change['registrar_approval_date'] == '0000-00-00 00:00:00'
                                ? '---'
                                : ($exam_grade_change['registrar_approval_date'] instanceof \Cake\I18n\FrozenTime
                                    ? $exam_grade_change['registrar_approval_date']->format('F j, Y h:i:s A')
                                    : (new Time($exam_grade_change['registrar_approval_date']))->format('F j, Y h:i:s A')) ?>
                        </td>
                    </tr>
                <?php endif; ?>
            <?php endif; ?>
        <?php endfor; ?>
    <?php endif; ?>

    <?php if (!empty($student_exam_grade_history)): ?>
        <?php
        $reject_count = 1;
        foreach ($student_exam_grade_history as $key => $exam_grade_detail): ?>
            <?php
            if (isset($exam_grade_detail['ExamGrade'])) {
                $exam_grade_detail = $exam_grade_detail['ExamGrade'];
            }
            ?>
            <tr>
                <th colspan="2">
                    <?= isset($exam_grade_detail['department_reply']) && $exam_grade_detail['department_reply'] == 0
                        ? __('Grade History') . ' ' . $reject_count++
                        : h($approver_c) . ' ' . __('response for registrar exam grade rejection') ?>
                </th>
            </tr>
            <?php if (isset($exam_grade_detail['department_reply']) && $exam_grade_detail['department_reply'] == 0): ?>
                <tr>
                    <td style="width:28%; font-weight:bold;background-color:white;"><?= __('Grade') ?>:</td>
                    <td style="width:72%;background-color:white;"><b><?= !empty($exam_grade_detail['grade']) ? h($exam_grade_detail['grade']) : '---' ?></b></td>
                </tr>
                <tr>
                    <td style="font-weight:bold"><?= __('Date Grade Submitted') ?>:</td>
                    <td>
                        <?= empty($exam_grade_detail['created']) || is_null($exam_grade_detail['created']) || $exam_grade_detail['created'] == '0000-00-00 00:00:00'
                            ? '---'
                            : ($exam_grade_detail['created'] instanceof \Cake\I18n\FrozenTime
                                ? $exam_grade_detail['created']->format('F j, Y h:i:s A')
                                : (new Time($exam_grade_detail['created']))->format('F j, Y h:i:s A')) ?>
                    </td>
                </tr>
            <?php endif; ?>
            <tr>
                <td style="font-weight:bold;background-color:white;"><?= h($approver_c) ?> <?= __('Approval') ?>:</td>
                <td style="background-color:white;" class="<?= isset($exam_grade_detail['department_approval']) && $exam_grade_detail['department_approval'] == 1 ? 'accepted' : (isset($exam_grade_detail['department_approval']) && $exam_grade_detail['department_approval'] == -1 && (isset($exam_grade_detail['department_reply']) && isset($exam_grade_detail['registrar_approval']) && $exam_grade_detail['department_reply'] && $exam_grade_detail['registrar_approval'] == 1) ? 'accepted' : 'rejected') ?>">
                    <?= isset($exam_grade_detail['department_approval']) && $exam_grade_detail['department_approval'] == 1
                        ? __('Accepted')
                        : (isset($exam_grade_detail['department_approval']) && $exam_grade_detail['department_approval'] == -1
                            ? __('Rejected') . (isset($exam_grade_detail['department_reply']) && isset($exam_grade_detail['registrar_approval']) && $exam_grade_detail['department_reply'] && $exam_grade_detail['registrar_approval'] == 1
                                ? '<span class="accepted" style="padding-left: 20px;">' . __('(Rejected Registrar Rejection)') . '</span>'
                                : '')
                            : __('On Process')) ?>
                </td>
            </tr>
            <tr>
                <td style="font-weight:bold"><?= isset($exam_grade_detail['department_approval']) && $exam_grade_detail['department_approval'] == -1 ? __('Rejected By') : __('Approved By') ?>:</td>
                <td><?= !empty($exam_grade_detail['department_approved_by_name']) ? h($exam_grade_detail['department_approved_by_name']) : '---' ?></td>
            </tr>
            <tr>
                <td style="font-weight:bold;background-color:white;"><?= h($approver_c) ?> <?= __('Remark') ?>:</td>
                <td style="background-color:white;"><?= !empty($exam_grade_detail['department_reason']) ? h($exam_grade_detail['department_reason']) : '---' ?></td>
            </tr>
            <tr>
                <td style="font-weight:bold"><?= h($approver_c) ?> <?= isset($exam_grade_detail['department_approval']) && $exam_grade_detail['department_approval'] == -1 ? __('Rejected Date') : __('Approved Date') ?>:</td>
                <td>
                    <?= empty($exam_grade_detail['department_approval_date']) || is_null($exam_grade_detail['department_approval_date']) || $exam_grade_detail['department_approval_date'] == '0000-00-00 00:00:00'
                        ? '---'
                        : ($exam_grade_detail['department_approval_date'] instanceof \Cake\I18n\FrozenTime
                            ? $exam_grade_detail['department_approval_date']->format('F j, Y h:i:s A')
                            : (new Time($exam_grade_detail['department_approval_date']))->format('F j, Y h:i:s A')) ?>
                </td>
            </tr>
            <tr>
                <td style="font-weight:bold;background-color:white;"><?= __('Registrar Confirmation') ?>:</td>
                <td style="background-color:white;" class="<?= isset($exam_grade_detail['registrar_approval']) && $exam_grade_detail['registrar_approval'] == 1 ? 'accepted' : (isset($exam_grade_detail['registrar_approval']) && $exam_grade_detail['registrar_approval'] == -1 ? 'rejected' : (isset($exam_grade_detail['department_approval']) && $exam_grade_detail['department_approval'] != -1 ? 'on-process' : '')) ?>">
                    <?= isset($exam_grade_detail['registrar_approval']) && $exam_grade_detail['registrar_approval'] == 1
                        ? __('Accepted')
                        : (isset($exam_grade_detail['registrar_approval']) && $exam_grade_detail['registrar_approval'] == -1
                            ? __('Rejected')
                            : (isset($exam_grade_detail['department_approval']) && $exam_grade_detail['department_approval'] == 1
                                ? __('On Process')
                                : '---')) ?>
                </td>
            </tr>
            <tr>
                <td style="font-weight:bold"><?= isset($exam_grade_detail['registrar_approval']) && $exam_grade_detail['registrar_approval'] == -1 ? __('Registrar Rejected By') : __('Registrar Confirmed By') ?>:</td>
                <td><?= !empty($exam_grade_detail['registrar_approved_by_name']) ? h($exam_grade_detail['registrar_approved_by_name']) : '---' ?></td>
            </tr>
            <tr>
                <td style="font-weight:bold;background-color:white;"><?= __('Registrar Remark') ?>:</td>
                <td style="background-color:white;"><?= !empty($exam_grade_detail['registrar_reason']) ? h($exam_grade_detail['registrar_reason']) : '---' ?></td>
            </tr>
            <tr>
                <td style="font-weight:bold"><?= isset($exam_grade_detail['registrar_approval']) && $exam_grade_detail['registrar_approval'] == -1 ? __('Registrar Rejected Date') : __('Registrar Confirmation Date') ?>:</td>
                <td>
                    <?= empty($exam_grade_detail['registrar_approval_date']) || is_null($exam_grade_detail['registrar_approval_date']) || $exam_grade_detail['registrar_approval_date'] == '0000-00-00 00:00:00'
                        ? '---'
                        : ($exam_grade_detail['registrar_approval_date'] instanceof \Cake\I18n\FrozenTime
                            ? $exam_grade_detail['registrar_approval_date']->format('F j, Y h:i:s A')
                            : (new Time($exam_grade_detail['registrar_approval_date']))->format('F j, Y h:i:s A')) ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if (!empty($student_exam_grade_history)): ?>
</table>
<?php endif; ?>
