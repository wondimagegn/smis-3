<?php
$this->assign('title', __('Add Students to Section'));
?>

<script type="text/javascript">
    var form_being_submitted = false;
    const validationMessageNonSelected = document.getElementById('validation-message_non_selected');
    var sectionName = "<?= h($section_detail['Section']['name']) ?>";

    $(document).ready(function() {
        $("form").submit(function(e) {
            var checkboxes = document.querySelectorAll('input[type="checkbox"][class="checkbox1"]');
            var checkedOne = Array.from(checkboxes).some(x => x.checked);
            if (!checkedOne) {
                alert('At least one student must be selected to add to ' + sectionName + ' Section.');
                validationMessageNonSelected.innerHTML = 'At least one student must be selected to add to ' + sectionName + ' Section.';
                return false;
            }
            if (form_being_submitted) {
                alert('Adding Selected Students to ' + sectionName + ' Section, please wait a moment...');
                $("#SubmitID").prop('disabled', true);
                return false;
            }
            $("#SubmitID").val('Adding Selected to ' + sectionName + ' Section...');
            form_being_submitted = true;
            return true;
        });

        $("#select-all").change(function() {
            var isChecked = $(this).prop('checked');
            $("input.checkbox1").prop('checked', isChecked);
        });

        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    });
</script>

<div class="box">
    <div class="box-header bg-transparent">
        <h3 class="box-title" style="margin-top: 10px;">
            <i class="fa fa-th-list" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;">
                <?= __('Add Students to Section: %s', h($section_detail['Section']['name'])) ?>
            </span>
        </h3>
        <a class="close-reveal-modal">&#215;</a>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div style="margin-top: -10px;"></div>
            <hr>
            <?php if (isset($students) && !empty($students)): ?>
                <?= $this->Form->create('Section', ['url' => ['controller' => 'Sections', 'action' => 'massStudentSectionAdd']]) ?>
                <?= $this->Form->hidden('SectionDetail.section_id', ['value' => $section_detail['Section']['id']]) ?>
                <h6 id="validation-message_non_selected" class="text-danger" style="font-size: 14px;"></h6>
                <br>
                <h6 class="text-muted" style="font-size: 14px;"><?= __('Select students to add') ?></h6>
                <div style="overflow-x:auto;">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <td colspan="6" class="font-weight-bold" style="border-bottom: 2px solid #555; line-height: 0.5px;">
                                <h6 class="text-muted">
                                    <?php if (!empty($section_detail['Department']['name'])): ?>
                                        <?= __('%s (%s, %s)', h($section_detail['Section']['name']), h($section_detail['YearLevel']['name']), h($section_detail['Section']['academicyear'])) ?>
                                        <br style="line-height: 0.5;">
                                        <span style="font-size: 13px;">
                                                <?= h($section_detail['Department']['name']) ?> &nbsp;&nbsp; | &nbsp;&nbsp;
                                                <?= h($section_detail['Program']['name']) ?> &nbsp;&nbsp; | &nbsp;&nbsp;
                                                <?= h($section_detail['ProgramType']['name']) ?>
                                            </span>
                                    <?php else: ?>
                                        <?= __('%s (%s-%s)', h($section_detail['Section']['name']), h($section_detail['Program']['name']), h($section_detail['ProgramType']['name'])) ?>
                                    <?php endif; ?>
                                </h6>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-center" style="width: 5%;"><?= __('#') ?></th>
                            <th class="text-center" style="width: 5%;">
                                <?= $this->Form->checkbox('SelectAll', ['id' => 'select-all', 'label' => false]) ?>
                                &nbsp;
                            </th>
                            <th class="text-center" style="width: 25%;"><?= __('Student Name') ?></th>
                            <th class="text-center" style="width: 10%;"><?= __('Sex') ?></th>
                            <th class="text-center" style="width: 20%;"><?= __('Student ID') ?></th>
                            <th class="text-center"><?= __('Department') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $count = 1; ?>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td class="text-center"><?= h($count++) ?></td>
                                <td class="text-center">
                                    <?php if ($student['Student']['graduated'] == 0): ?>
                                        <div style="margin-left: 15%;">
                                            <?= $this->Form->checkbox("Section.{$count}.selected_id", ['class' => 'checkbox1']) ?>
                                            <?= $this->Form->hidden("Section.{$count}.student_id", ['value' => $student['Student']['id']]) ?>
                                            <?= $this->Form->hidden("Section.{$count}.section_id", ['value' => $section_detail['Section']['id']]) ?>
                                        </div>
                                    <?php else: ?>
                                        **
                                    <?php endif; ?>
                                </td>
                                <td class="text-center"><?= h($student['Student']['full_name']) ?></td>
                                <td class="text-center">
                                    <?= strcasecmp(trim($student['Student']['gender']), 'male') == 0 ? 'M' : (strcasecmp(trim($student['Student']['gender']), 'female') == 0 ? 'F' : h($student['Student']['gender'])) ?>
                                </td>
                                <td class="text-center"><?= h($student['Student']['studentnumber']) ?></td>
                                <td class="text-center">
                                    <?= !empty($student['Department']['name']) ? h($student['Department']['name']) : (isset($student['College']['shortname']) ? __('Pre/Freshman (%s)', h($student['College']['shortname'])) : __('Pre/Freshman')) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <hr>
                <?php if (!empty($students)): ?>
                    <?= $this->Form->button(__('Add to Section'), [
                        'type' => 'submit',
                        'name' => 'submit',
                        'id' => 'SubmitID',
                        'class' => 'btn btn-primary btn-sm'
                    ]) ?>
                    <?= $this->Form->end() ?>
                <?php endif; ?>
            <?php else: ?>
                <div class="col-md-12">
                    <div class="alert alert-info" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
                        <span style="margin-right: 15px;"></span>
                        <?= __(
                            'No student is found who attended classes in the last %s academic years and sectionless for %s to add to %s section. %s',
                            ACY_BACK_FOR_SECTION_LESS,
                            h($section_detail['Section']['academicyear']),
                            h($section_detail['Section']['name']) . ' (' . (isset($section_detail['YearLevel']['name']) && !empty($section_detail['YearLevel']['name']) ? h($section_detail['YearLevel']['name']) : ($section_detail['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')) . ', ' . h($section_detail['Section']['academicyear']) . ')',
                            isset($section_detail['YearLevel']['name']) && !empty($section_detail['YearLevel']['name'])
                                ? __('Either the section\'s curriculum %s is different from the available sectionless students\' curriculum or all students are assigned to a section or there are no recently admitted students who are attached to a curriculum in your department.', !empty($section_detail['Curriculum']['name']) ? '(' . h($section_detail['Curriculum']['name']) . '-' . h($section_detail['Curriculum']['year_introduced']) . ')' : '')
                                : __('Either all students are assigned to a section or there are no recently admitted students in your college that need section assignment.')
                        ) ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
