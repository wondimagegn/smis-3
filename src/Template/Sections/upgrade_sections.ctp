<?php
$this->assign('title', __('Upgrade Sections: (%s)', !empty($department_name) ? h($department_name) : (!empty($college_name) ? h($college_name) : '')));
?>

<script type="text/javascript">
    $(document).ready(function() {
        var form_being_submitted = false;
        const validationMessageNonSelected = document.getElementById('validation-message_non_selected');

        function toggleViewFullId(id) {
            if ($('#' + id).css("display") === 'none') {
                $('#' + id + 'Img').attr("src", '<?= $this->Url->build('/img/minus2.gif') ?>');
                $('#' + id + 'Txt').empty().append('Hide Filter');
            } else {
                $('#' + id + 'Img').attr("src", '<?= $this->Url->build('/img/plus2.gif') ?>');
                $('#' + id + 'Txt').empty().append('Display Filter');
            }
            $('#' + id).toggle("slow");
        }

        $('#ListPublishedCourse').parent().click(function() {
            toggleViewFullId('ListPublishedCourse');
        });

        $('#upgradeSelected').click(function(e) {
            var checkboxes = document.querySelectorAll('input[type="checkbox"]');
            var checkedOne = Array.from(checkboxes).some(x => x.checked);

            if (!checkedOne) {
                alert('At least one section must be selected to upgrade year level.');
                validationMessageNonSelected.innerHTML = 'At least one section must be selected to upgrade year level.';
                return false;
            }

            if (form_being_submitted) {
                alert('Upgrading Selected Sections, please wait a moment...');
                $('#upgradeSelected').prop('disabled', true);
                return false;
            }

            var confirmed = confirm('Are you sure you want to upgrade selected sections to the next year level?');
            if (confirmed) {
                $('#upgradeSelected').val('Upgrading Selected Sections...');
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
                <?= __('Upgrade Sections: (%s)', !empty($department_name) ? h($department_name) : (!empty($college_name) ? h($college_name) : '')) ?>
            </span>
        </h3>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <?= $this->Form->create('Section', ['url' => ['controller' => 'Sections', 'action' => 'upgradeSections']]) ?>
                <?php if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_DEPARTMENT || $this->request->getSession()->read('Auth.User.Role.parent_id') == ROLE_DEPARTMENT): ?>
                    <div style="margin-top: -30px;">
                        <?php if (empty($formatedSections)): ?>
                            <hr>
                            <blockquote>
                                <h6><i class="fa fa-info"></i> &nbsp; <?= __('Important Note:') ?></h6>
                                <span style="text-align: justify; font-size: 14px;" class="text-muted">
                                    <?= __('This tool will help you to upgrade sections to the next year level.') ?>
                                    <b style="text-decoration: underline;">
                                        <i><?= __('All published course grades for a section should be fully submitted in order to qualify for year level upgrade.') ?></i>
                                    </b>
                                </span>
                            </blockquote>
                        <?php endif; ?>
                        <hr>
                        <div>
                            <?php if (!empty($formatedSections)): ?>
                                <?= $this->Html->image('/img/plus2.gif', ['id' => 'ListPublishedCourseImg']) ?>
                                <span style="font-size: 10px; vertical-align: top; font-weight: bold;" id="ListPublishedCourseTxt">
                                    <?= __('Display Filter') ?>
                                </span>
                            <?php else: ?>
                                <?= $this->Html->image('/img/minus2.gif', ['id' => 'ListPublishedCourseImg']) ?>
                                <span style="font-size: 10px; vertical-align: top; font-weight: bold;" id="ListPublishedCourseTxt">
                                    <?= __('Hide Filter') ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div id="ListPublishedCourse" style="display: <?= !empty($formatedSections) ? 'none' : 'block' ?>;">
                            <fieldset style="padding-bottom: 5px; padding-top: 5px;">
                                <legend>&nbsp;&nbsp; <?= __('Search / Filter') ?> &nbsp;&nbsp;</legend>
                                <div class="row">
                                    <div class="col-md-3">
                                        <?= $this->Form->control('academicyear', [
                                            'options' => $acyear_array_data,
                                            'required' => true,
                                            'class' => 'form-control',
                                            'style' => 'width: 90%'
                                        ]) ?>
                                    </div>
                                    <?php if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_DEPARTMENT): ?>
                                        <div class="col-md-3">
                                            <?= $this->Form->control('year_level_id', [
                                                'empty' => '[ All Year Levels ]',
                                                'class' => 'form-control',
                                                'style' => 'width: 90%'
                                            ]) ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="col-md-3">
                                        <?= $this->Form->control('program_id', [
                                            'empty' => '[ All Programs ]',
                                            'class' => 'form-control',
                                            'style' => 'width: 90%'
                                        ]) ?>
                                    </div>
                                    <div class="col-md-3">
                                        <?= $this->Form->control('program_type_id', [
                                            'empty' => '[ All Program Types ]',
                                            'class' => 'form-control',
                                            'style' => 'width: 90%'
                                        ]) ?>
                                    </div>
                                </div>
                            </fieldset>
                            <?= $this->Form->button(__('Search'), [
                                'type' => 'submit',
                                'name' => 'search',
                                'class' => 'btn btn-primary btn-sm'
                            ]) ?>
                            <br>
                        </div>
                    </div>
                    <hr>
                    <?php $enableSubmitButton = 0; ?>
                    <?php if (isset($formatedSections) && !empty($formatedSections)): ?>
                        <h6 id="validation-message_non_selected" class="text-danger" style="font-size: 14px;"></h6>
                        <?php foreach ($formatedSections as $fsk => $fsv): ?>
                            <h6 class="text-muted" style="font-size: 14px;">
                                <?= (!empty($this->request->getData('Section.program_id')) ? h($programs[$this->request->getData('Section.program_id')]) . ' ' : '') ?>
                                <?= (!empty($this->request->getData('Section.program_type_id')) ? ' ' . h($program_types[$this->request->getData('Section.program_type_id')]) . ' ' : '') ?>
                                <?= h($fsk) ?> <?= __('year') ?>
                            </h6>
                            <div style="overflow-x:auto;">
                                <table style="border: 2px solid #ccc;" class="table table-bordered">
                                    <?php if (isset($fsv['Upgradable']) && !empty($fsv['Upgradable'])): ?>
                                        <thead>
                                        <tr>
                                            <td><h6 class="text-muted" style="font-size: 14px;"><?= __('Upgradeable Sections') ?></h6></td>
                                        </tr>
                                        </thead>
                                        <tr>
                                            <td>
                                                <table class="table table-bordered">
                                                    <tbody>
                                                    <?php foreach ($fsv['Upgradable'] as $ufsk => $ufsv): ?>
                                                        <?php
                                                        $unqualified_count = isset($unqualified_students_count[$ufsk]) && !empty($unqualified_students_count[$ufsk]) ? count($unqualified_students_count[$ufsk]) : 0;
                                                        ?>
                                                        <tr>
                                                            <td class="text-center" style="background-color: white;">
                                                                <div style="margin-left: 1%; margin-top: 1%;">
                                                                    <?= $this->Form->control("Upgradbale_Selected.{$ufsk}", [
                                                                        'class' => 'upgradableSelectedSection',
                                                                        'type' => 'checkbox',
                                                                        'value' => $ufsk,
                                                                        'label' => h($ufsv)
                                                                    ]) ?>
                                                                    <?php if ($unqualified_count != 0): ?>
                                                                        (<?= $this->Html->link(
                                                                            __('%s unqualified students', $unqualified_count),
                                                                            '#',
                                                                            [
                                                                                'data-animation' => 'fade',
                                                                                'data-reveal-id' => 'myModalUpgrade',
                                                                                'data-reveal-ajax' => $this->Url->build(['controller' => 'Sections', 'action' => 'getModalBox', $ufsk])
                                                                            ]
                                                                        ) ?>)
                                                                    <?php endif; ?>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                        <?php $enableSubmitButton++; ?>
                                    <?php endif; ?>
                                    <?php if (isset($fsv['Unupgradable']) && !empty($fsv['Unupgradable'])): ?>
                                        <thead>
                                        <tr>
                                            <td class="font-weight-bold"><?= __('The following list of sections do not qualify for year level upgrade') ?></td>
                                        </tr>
                                        </thead>
                                        <tr>
                                            <td>
                                                <table class="table table-bordered">
                                                    <tbody>
                                                    <?php foreach ($fsv['Unupgradable'] as $uufsk => $uufsv): ?>
                                                        <tr>
                                                            <td class="text-center" style="background-color: white;"><?= h($uufsv) ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </table>
                            </div>
                            <hr>
                        <?php endforeach; ?>
                        <?php if ($enableSubmitButton): ?>
                            <hr>
                            <?= $this->Form->button(__('Upgrade Selected Sections'), [
                                'type' => 'submit',
                                'id' => 'upgradeSelected',
                                'name' => 'upgrade',
                                'class' => 'btn btn-primary btn-sm'
                            ]) ?>
                        <?php endif; ?>
                        <?php if (isset($fsv['Unupgradable']) && !empty($fsv['Unupgradable'])): ?>
                            <br>
                            <?php if (isset($last_year_level_sections_count) && $last_year_level_sections_count): ?>
                                <div class="alert alert-info" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
                                    <span style="margin-right: 15px;"></span>
                                    <?= __(
                                        '%s section(s) are in their last year level according to the section\'s attached curriculum or year levels available in your department and you can\'t upgrade these sections. You can update the section\'s attached curriculum course breakdown if you feel one or all of these sections need year level upgrade.',
                                        $last_year_level_sections_count
                                    ) ?>
                                </div>
                            <?php endif; ?>
                            <div class="alert alert-info" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
                                <span style="margin-right: 15px;"></span>
                                <?= __(
                                    'Please check if all published course grades are submitted or check if there is a mass dropped/elective course in one of the semesters in %s and unpublish such courses from published courses if any.',
                                    $this->request->getData('Section.academicyear', '')
                                ) ?>
                            </div>
                        <?php endif; ?>
                    <?php elseif (empty($formatedSections) && !$isbeforesearch): ?>
                        <div class="alert alert-info" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
                            <span style="margin-right: 15px;"></span>
                            <?= __('There is no section found to upgrade with the selected search criteria.') ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div id="myModalUpgrade" class="reveal-modal" data-reveal></div>
    </div>
</div>
