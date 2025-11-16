<?php
$this->assign('title', __('Add New Section'));
$role_id= $this->request->getSession()->read('Auth.User.role_id');
?>
<div class="box">
    <div class="box-header bg-transparent">
        <h3 class="box-title" style="margin-top: 10px;">
            <i class="fa fa-plus"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;">
                <?= __('Add New Section') ?>
            </span>
        </h3>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <?= $this->Form->create('Section', ['url' => ['controller' => 'Sections', 'action' => 'add']]) ?>
                <div style="margin-top: -30px;"><hr></div>
                <blockquote>
                    <h6><i class="fa fa-info"></i> &nbsp; <?= __('Important Note:') ?></h6>
                    <p style="text-align: justify;">
                        <span style="font-size: 16px;">
                            <?= __('Students can be involved in section management if and only if:') ?>
                        </span>
                    <ol class="text-muted" style="font-weight: bold; font-size: 14px;">
                        <li><?= __('They have student ID/Number') ?></li>
                        <li><?= __('They are admitted') ?></li>
                        <li><?= __('They are attached to a curriculum') ?></li>
                    </ol>
                    </p>
                </blockquote>
                <hr>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td>
                                <br>
                                <div class="col-md-12">
                                    <div class="font-weight-bold"><?= __('College:') ?> &nbsp;<?= h($collegename) ?></div>
                                    <?php if ($this->request->getSession()->read('Auth.User.role_id') != ROLE_COLLEGE): ?>
                                        <div class="font-weight-bold"><?= __('Department:') ?> &nbsp;<?= h($departmentname) ?></div>
                                    <?php endif; ?>
                                    <hr>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-6">
                                        <?= $this->Form->hidden('name') ?>
                                        <?= $this->Form->control('academicyear', [
                                            'label' => __('Academic Year: '),
                                            'type' => 'select',
                                            'options' => $acyear_array_data,
                                            'empty' => '[ Select Academic Year ]',
                                            'required' => true,
                                            'id' => 'academicyear',
                                            'default' => isset($thisacademicyear) ? $thisacademicyear : '',
                                            'class' => 'form-control'
                                        ]) ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?php if ($this->request->getSession()->read('Auth.User.role_id') != ROLE_COLLEGE): ?>
                                            <?= $this->Form->control('year_level_id', [
                                                'label' => __('Year Level: '),
                                                'required' => true,
                                                'class' => 'form-control'
                                            ]) ?>
                                            <br>
                                        <?php else: ?>
                                            &nbsp;
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-6">
                                        <?= $this->Form->control('program_id', [
                                            'label' => __('Program: '),
                                            'id' => 'ProgramId',
                                            'class' => 'form-control'
                                        ]) ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $this->Form->control('program_type_id', [
                                            'label' => __('Program Type: '),
                                            'id' => 'ProgramTypeId',
                                            'class' => 'form-control'
                                        ]) ?>
                                    </div>
                                </div>
                                <?php if ($this->request->getSession()->read('Auth.User.role_id') != ROLE_COLLEGE): ?>
                                    <div class="col-md-12">
                                        <div class="col-md-6">
                                            <?= $this->Form->control('prefix_section_name', [
                                                'label' => __('Prefix: '),
                                                'type' => 'select',
                                                'options' => $prefix_section_name,
                                                'id' => 'PrefixSectionName',
                                                'class' => 'form-control'
                                            ]) ?>
                                        </div>
                                        <div class="col-md-6">
                                            <?= $this->Form->control('additionalprefix_section_name', [
                                                'label' => __('Additional Prefix: '),
                                                'type' => 'text',
                                                'pattern' => '[a-zA-Z]+',
                                                'maxlength' => 10,
                                                'id' => 'AdditionalPrefixSectionName',
                                                'class' => 'form-control'
                                            ]) ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <div class="col-md-12">
                                    <div class="col-md-6">
                                        <?= $this->Form->control('fixed_section_name', [
                                            'label' => false,
                                            'id' => 'FixedSectionName',
                                            'readonly' => true,
                                            'value' => $this->request->getData('fixed_section_name', $FixedSectionName),
                                            'class' => 'form-control'
                                        ]) ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $this->Form->control('variable_section_name', [
                                            'label' => __('Variable Section Name'),
                                            'type' => 'select',
                                            'options' => $variable_section_name_array,
                                            'default' => $this->request->getData('variable_section_name', 'Alphabet'),
                                            'id' => 'variablesectionname',
                                            'class' => 'form-control'
                                        ]) ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-md-12">
                                        <?= $this->Form->control('number_of_class', [
                                            'label' => __('Sections to create: '),
                                            'type' => 'select',
                                            'options' => $number_of_class,
                                            'default' => $this->request->getData('number_of_class', '1'),
                                            'class' => 'form-control',
                                            'style' => 'width: 30%;'
                                        ]) ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <?php if (count($programss) > 0): ?>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td>
                                    <table id="sectionNotAssignClass" class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th colspan="<?= count($programss) + 1 ?>" class="text-center">
                                                <?= __('Table: Summary of students who are not assigned to a section for %s academic year.', h($thisacademicyear)) ?>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="text-center"><?= __('ProgramType/Program') ?></th>
                                            <?php foreach ($programss as $kp => $vp): ?>
                                                <th class="text-center"><?= h($vp) ?></th>
                                            <?php endforeach; ?>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($programTypess as $i => $programType): ?>
                                            <tr>
                                                <td class="text-center"><?= h($programType) ?></td>
                                                <?php foreach ($programss as $j => $program): ?>
                                                    <td class="text-center">
                                                        <?= isset($summary_data[$program][$programType]) && $summary_data[$program][$programType] > 0 ? h($summary_data[$program][$programType]) : '--' ?>
                                                    </td>
                                                <?php endforeach; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_DEPARTMENT && isset($curriculum_unattached_student_count) && $curriculum_unattached_student_count > 0): ?>
                                            <tr>
                                                <td colspan="<?= count($programss) + 1 ?>" class="text-center">
                                                    <?= __(
                                                        '%s not attached to any curriculum in your department from all programs. Thus, %s will not participate in any section assignment.',
                                                        $curriculum_unattached_student_count > 1 ? sprintf(__('%s students are'), $curriculum_unattached_student_count) : sprintf(__('%s student is'), $curriculum_unattached_student_count),
                                                        $curriculum_unattached_student_count > 1 ? __('these students') : __('this student')
                                                    ) ?>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </div>
                <?php endif; ?>
                <div class="col-md-12">
                    <?php if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_DEPARTMENT): ?>
                        <hr>
                        <fieldset style="padding-bottom: 5px; padding-top: 5px;">
                            <div class="col-md-2">&nbsp;</div>
                            <div class="col-md-8">
                                <?= $this->Form->control('curriculum_id', [
                                    'id' => 'CurriculumID',
                                    'label' => __('Section Curriculum:'),
                                    'type' => 'select',
                                    'empty' => '[ Select Curriculum ]',
                                    'required' => true,
                                    'class' => 'form-control',
                                    'style' => 'width: 90%;'
                                ]) ?>
                            </div>
                            <div class="col-md-2">&nbsp;</div>
                        </fieldset>
                    <?php endif; ?>
                    <hr style="margin-top: 5px;">
                    <?= $this->Form->button(__('Create Section(s)'), [
                        'type' => 'submit',
                        'name' => 'submit',
                        'id' => 'SubmitID',
                        'class' => 'btn btn-primary btn-sm'
                    ]) ?>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $("#FixedSectionName").val("<?= h($FixedSectionName) ?>");

        $("#academicyear").change(function() {
            var summery = $(this).val();
            var exploded = summery.split('/');
            var academicYear = exploded[0] + '-' + exploded[1];
            $(this).prop('disabled', true);
            $("#sectionNotAssignClass").empty().html('<img src="<?= $this->Url->build('/img/busy.gif') ?>" class="d-block mx-auto" alt="Loading">');
            var formUrl = '<?= $this->Url->build(['controller' => 'Sections', 'action' => 'unAssignedSummaries']) ?>/' + encodeURIComponent(academicYear);
            $.ajax({
                type: 'GET',
                url: formUrl,
                data: { summery: summery },
                success: function(data) {
                    $("#academicyear").prop('disabled', false);
                    $("#sectionNotAssignClass").empty().append(data);
                },
                error: function(xhr, textStatus) {
                    alert(textStatus);
                }
            });
        });

        $("#ProgramId").change(function() {
            var pid = $(this).val();
            $("#CurriculumID").prop('disabled', true).empty();
            var formUrl = '<?= $this->Url->build(['controller' => 'Curriculums', 'action' => 'getCurriculumsBasedOnProgramCombo']) ?>/' + encodeURIComponent(pid);
            $.ajax({
                type: 'GET',
                url: formUrl,
                data: { pid: pid },
                success: function(data) {
                    $("#CurriculumID").prop('disabled', false).empty().append(data);
                    toggleFields();
                },
                error: function(xhr, textStatus) {
                    alert(textStatus);
                }
            });

            function toggleFields() {
                var programId = $("#ProgramId").val();
                if (programId == 1) {
                    $("#PrefixSectionName").val('UG');
                } else if (programId == 2) {
                    $("#PrefixSectionName").val('PG');
                } else if (programId == 3) {
                    $("#PrefixSectionName").val('PhD');
                } else if (programId == 4) {
                    $("#PrefixSectionName").val('PGDT');
                } else if (programId == 5) {
                    $("#PrefixSectionName").val('REM');
                }
            }
        });

        $("form").submit(function(e) {
            var form = this;
            if (!form.academicyear.value) {
                $(form.academicyear).focus();
                return false;
            }
            if (form_being_submitted) {
                alert("Creating section(s), please wait a moment...");
                $("#SubmitID").prop('disabled', true);
                return false;
            }
            $("#SubmitID").val('Creating Section(s)...');
            form_being_submitted = true;
            return true;
        });

        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    });

    var form_being_submitted = false;
</script>
