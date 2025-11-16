<?php
$this->assign('title', __('Students Not Qualified for Year Level Upgrade'));
?>

<div class="box">
    <div class="box-header bg-transparent">
        <h3 class="box-title" style="margin-top: 10px;">
            <i class="fa fa-th-list" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;">
                <?= __('Students Not Qualified for Year Level Upgrade') ?>
            </span>
        </h3>
        <a class="close-reveal-modal">&#215;</a>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <div style="margin-top: -40px;"><hr></div>
                <?php if (isset($students_details)): ?>
                    <?php $unqualified_students_count = count($students_details); ?>
                    <?php if ($unqualified_students_count == 0): ?>
                        <div class="alert alert-info" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
                            <span style="margin-right: 15px;"></span>
                            <?= __('All Students of this Section are qualified for the year level upgrade.') ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
                            <span style="margin-right: 15px;"></span>
                            <?= __(
                                '%s not qualified to upgrade with this section. Thus, %s will be section-less if this section is upgraded to the next year level.',
                                $unqualified_students_count == 1 ? sprintf(__('%s student is'), $unqualified_students_count) : sprintf(__('%s students are'), $unqualified_students_count),
                                $unqualified_students_count == 1 ? __('the student') : __('they')
                            ) ?>
                        </div>
                        <div style="overflow-x:auto;">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th class="text-center" style="width: 5%"><?= __('#') ?></th>
                                    <th class="text-center" style="width: 25%"><?= __('Full Name') ?></th>
                                    <th class="text-center" style="width: 5%"><?= __('Sex') ?></th>
                                    <th class="text-center" style="width: 15%"><?= __('Student ID') ?></th>
                                    <th class="text-center"><?= __('Reason') ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $count = 1; ?>
                                <?php foreach ($students_details as $sdv): ?>
                                    <tr>
                                        <td class="text-center"><?= h($count++) ?></td>
                                        <td class="text-center"><?= h($sdv['Student']['full_name']) ?></td>
                                        <td class="text-center">
                                            <?= strcasecmp(trim($sdv['Student']['gender']), 'male') == 0 ? 'M' :
                                                (strcasecmp(trim($sdv['Student']['gender']), 'female') == 0 ? 'F' :
                                                    h($sdv['Student']['gender'])) ?>
                                        </td>
                                        <td class="text-center"><?= h($sdv['Student']['studentnumber']) ?></td>
                                        <td class="text-center">
                                            <?= !empty($status_name) ?
                                                __('Status not generated or Have invalid grades or %s', h($status_name)) :
                                                __('Status not generated/Student has invalid grade') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
