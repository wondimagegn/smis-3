<?php
$this->assign('title', __('Move Students from %s', h($previousSectionName['Section']['name'] . ' (' . (isset($previousSectionName['YearLevel']['name']) ? $previousSectionName['YearLevel']['name'] : ($previousSectionName['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')) . ', ' . $previousSectionName['Section']['academicyear'] . ')')));
?>

<script type="text/javascript">
    $(document).ready(function() {
        var form_being_submitted = false;
        const validationMessageNonSelected = document.getElementById('validation-message_non_selected');
        const sectionName = "<?= h($previousSectionName['Section']['name']) ?>";

        $('#select-all').change(function() {
            var isChecked = $(this).prop('checked');
            $('input.checkbox1').prop('checked', isChecked);
        });

        $('#SubmitID').click(function(e) {
            var checkboxes = document.querySelectorAll('input.checkbox1');
            var checkedOne = Array.from(checkboxes).some(x => x.checked);
            var selectedDropDownValue = $('#selectedSectionId').val();
            var selectedDropDownText = $('#selectedSectionId option:selected').text();
            var selectedDropDownTextExploded = selectedDropDownText.split(") (");
            if (selectedDropDownTextExploded[0]) {
                selectedDropDownText = selectedDropDownTextExploded[0] + ')';
            }

            if (!selectedDropDownValue) {
                alert('Select the destination section to move students from ' + sectionName + ' section.');
                validationMessageNonSelected.innerHTML = 'Select the destination section to move students from ' + sectionName + ' section.';
                $('#selectedSectionId').focus();
                return false;
            }

            if (!checkedOne) {
                alert('At least one student must be selected to move from ' + sectionName + ' to ' + selectedDropDownText + ' section.');
                validationMessageNonSelected.innerHTML = 'At least one student must be selected to move from ' + sectionName + ' to ' + selectedDropDownText + ' section.';
                return false;
            }

            if (form_being_submitted) {
                alert('Moving selected students from ' + sectionName + ' to ' + selectedDropDownText + ' section. Please wait a moment...');
                $('#SubmitID').prop('disabled', true);
                return false;
            }

            var confirmed = confirm('Are you sure you want to move the selected students from ' + sectionName + ' to ' + selectedDropDownText + ' section?');
            if (confirmed) {
                $('#SubmitID').val('Moving to ' + selectedDropDownText + '...');
                form_being_submitted = true;
                return true;
            }
            return false;
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
                <?= __('Move Students from %s', h($previousSectionName['Section']['name'] . ' (' . (isset($previousSectionName['YearLevel']['name']) ? $previousSectionName['YearLevel']['name'] : ($previousSectionName['Section']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')) . ', ' . $previousSectionName['Section']['academicyear'] . ')')) ?>
            </span>
        </h3>
        <a class="close-reveal-modal">&#215;</a>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-12">
                        <div style="margin-top: -30px;"></div>
                        <hr>
                        <blockquote>
                            <h6><i class="fa fa-info"></i> &nbsp; <?= __('Important Note:') ?></h6>
                            <span style="text-align: justify; font-size: 14px;" class="text-muted">
                                <i class="text-dark">
                                    <?= __('Pre Conditions for student section movement: Students that don\'t have any course registration from %s section or only students that have registration in %s section with %s will be listed here for section movement.',
                                        h($previousSectionName['Section']['name']),
                                        h($previousSectionName['Section']['name']),
                                        '<span class="text-danger">' . __('all registered course grades are submitted and all grades are pass grades or don\'t contain (NG, F, DO, I, W) grades') . '</span>'
                                    ) ?>
                                    <br><br>
                                    <?= __(
                                        'Destination section selection options only contain active sections that have the same curriculum, academic year, year level, program, and program type to that of %s section%s',
                                        h($previousSectionName['Section']['name']),
                                        defined('ALLOW_STUDENT_SECTION_MOVE_TO_NEXT_YEAR_LEVEL') && ALLOW_STUDENT_SECTION_MOVE_TO_NEXT_YEAR_LEVEL == 1 && $this->request->getSession()->read('Auth.User.role_id') != ROLE_COLLEGE ? __(' and active sections that are one year up, if there are any.') : '.'
                                    ) ?>
                                </i>
                            </span>
                        </blockquote>
                        <hr>
                        <?php if (isset($studentsections['Student']) && !empty($studentsections['Student'])): ?>
                            <?= $this->Form->create('Section', ['url' => ['controller' => 'Sections', 'action' => 'sectionMoveUpdate']]) ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <fieldset style="margin: 5px;">
                                        <div class="col-md-6">
                                            <?= $this->Form->control('Selected_section_id', [
                                                'label' => __('Destination Section: '),
                                                'id' => 'selectedSectionId',
                                                'type' => 'select',
                                                'options' => $sections,
                                                'empty' => '[ Select Destination Section ]',
                                                'class' => 'form-control'
                                            ]) ?>
                                            <?= $this->Form->hidden('previous_section_id', ['value' => $previous_section_id]) ?>
                                        </div>
                                        <div class="col-md-6">
                                            <br>
                                            <?= $this->Form->button(__('Move Selected'), [
                                                'type' => 'submit',
                                                'id' => 'SubmitID',
                                                'name' => 'attach',
                                                'class' => 'btn btn-primary btn-sm'
                                            ]) ?>
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                            <br>
                            <h6 id="validation-message_non_selected" class="text-danger" style="font-size: 14px;"></h6>
                            <br>
                            <div style="overflow-x:auto;">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th colspan="5">
                                            <h6 class="text-muted" style="font-size: 14px;">
                                                <?= __('List of eligible students to move from %s',
                                                    h($previousSectionName['Section']['name'] . ' (' . $previousSectionName['Program']['name'] . ', ' . $previousSectionName['ProgramType']['name'] . ' - ' . (!empty($previousSectionName['Department']['name']) ? $previousSectionName['Department']['name'] : $previousSectionName['College']['name']) . ')')
                                                ) ?>
                                            </h6>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th class="text-center" style="width: 5%;"><?= __('#') ?></th>
                                        <th class="text-center" style="width: 5%;">
                                            <?= $this->Form->checkbox('SelectAll', ['id' => 'select-all']) ?>
                                        </th>
                                        <th class="text-center" style="width: 30%;"><?= __('Student Name') ?></th>
                                        <th class="text-center" style="width: 10%;"><?= __('Sex') ?></th>
                                        <th class="text-center"><?= __('Student ID') ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $count = 1; ?>
                                    <?php foreach ($studentsections['Student'] as $student): ?>
                                        <?php if ($student['StudentsSection']['archive'] == 0): ?>
                                            <tr>
                                                <td class="text-center"><?= h($count) ?></td>
                                                <td class="text-center">
                                                    <div style="margin-left: 15%;">
                                                        <?= $this->Form->checkbox("Section.{$count}.selected_id", ['class' => 'checkbox1']) ?>
                                                        <?= $this->Form->hidden("Section.{$count}.student_id", ['value' => $student['id']]) ?>
                                                    </div>
                                                </td>
                                                <td class="text-center"><?= h($student['full_name']) ?></td>
                                                <td class="text-center">
                                                    <?= strcasecmp(trim($student['gender']), 'male') == 0 ? 'M' :
                                                        (strcasecmp(trim($student['gender']), 'female') == 0 ? 'F' :
                                                            h($student['gender'])) ?>
                                                </td>
                                                <td class="text-center"><?= h($student['studentnumber']) ?></td>
                                            </tr>
                                            <?php $count++; ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <br>
                            <?= $this->Form->end() ?>
                        <?php else: ?>
                            <div class="col-md-12">
                                <div class="alert alert-info" style="font-family: 'Times New Roman', Times, serif;">
                                    <span style="margin-right: 15px;"></span>
                                    <?= empty($previousSectionName['StudentsSection']) ?
                                        __('%s section is empty.', h($previousSectionName['Section']['name'])) :
                                        __('No eligible student is found to move from %s section. All students assigned in this section are registered for published course(s) and grade is not fully submitted.', h($previousSectionName['Section']['name'])) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
