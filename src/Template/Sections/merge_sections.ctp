<?php
$this->assign('title', __('Merge Sections'));
?>

<script type="text/javascript">
    $(document).ready(function() {
        var merging_selected_sections = false;
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

        $('#mergeSections').click(function(e) {
            var checkboxes = document.querySelectorAll('input[type="checkbox"][name^="data[Section][Sections]"]');
            var checkedOne = Array.from(checkboxes).some(x => x.checked);
            var selectedCount = Array.from(checkboxes).filter(x => x.checked).length;

            if (!checkedOne || selectedCount < 2) {
                alert('At least two sections must be selected for section merge.');
                validationMessageNonSelected.innerHTML = 'At least two sections must be selected for section merge.';
                return false;
            } else if (selectedCount > 3) {
                alert('Merging more than 3 sections at a time is not allowed.');
                validationMessageNonSelected.innerHTML = 'Merging more than 3 sections at a time is not allowed.';
                return false;
            }

            if (merging_selected_sections) {
                alert('Merging Selected Sections, please wait a moment...');
                $('#mergeSections').prop('disabled', true);
                return false;
            }

            var confirmed = confirm('The selected ' + selectedCount + ' sections will be merged into one section and all students currently assigned in these sections will be moved to the new section. Are you sure you want to merge these ' + selectedCount + ' sections into one section?');
            if (!merging_selected_sections && confirmed) {
                $('#mergeSections').val('Merging Selected Sections...');
                merging_selected_sections = true;
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
                <?= __('Merge Sections') ?>
            </span>
        </h3>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <?= $this->Form->create('Section', ['url' => ['controller' => 'Sections', 'action' => 'mergeSections']]) ?>
                <div style="margin-top: -30px;">
                    <hr>
                    <blockquote>
                        <h6><i class="fa fa-info"></i> &nbsp; <?= __('Important Note:') ?></h6>
                        <p style="text-align: justify;">
                            <span class="text-dark" style="font-size: 15px;">
                                <?= __('This tool will help you to merge sections for the purpose of management if the number of students in a given section is too small.') ?>
                            </span>
                            <br>
                            <i class="text-danger" style="font-size: 16px;">
                                <?= __('To avoid possible complications, you\'re not advised to merge sections which have different curriculum attachments, same/different course publication, instructor assignments, or grade submissions. Section merges which originate from a previous section split are okay.') ?>
                            </i>
                        </p>
                    </blockquote>
                    <hr>
                    <div>
                        <?php if (!empty($sections)): ?>
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
                    <div id="ListPublishedCourse" style="display: <?= !empty($sections) ? 'none' : 'block' ?>;">
                        <fieldset style="padding-bottom: 0px; padding-top: 15px;">
                            <div class="row">
                                <div class="col-md-3">
                                    <?= $this->Form->control('academicyear', [
                                        'label' => __('Academic Year: '),
                                        'required' => true,
                                        'type' => 'select',
                                        'options' => $custom_acy_list,
                                        'default' => $this->request->getData('Section.academicyear', $defaultacademicyear ?? ''),
                                        'class' => 'form-control',
                                        'style' => 'width: 90%;'
                                    ]) ?>
                                </div>
                                <div class="col-md-3">
                                    <?= $this->Form->control('program_id', [
                                        'label' => __('Program: '),
                                        'required' => true,
                                        'class' => 'form-control',
                                        'style' => 'width: 90%;'
                                    ]) ?>
                                </div>
                                <div class="col-md-3">
                                    <?= $this->Form->control('program_type_id', [
                                        'label' => __('Program Type: '),
                                        'required' => true,
                                        'class' => 'form-control',
                                        'style' => 'width: 90%;'
                                    ]) ?>
                                </div>
                                <div class="col-md-3">
                                    <?php if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_DEPARTMENT): ?>
                                        <?= $this->Form->control('year_level_id', [
                                            'label' => __('Year Level: '),
                                            'empty' => '[ Select Year Level ]',
                                            'required' => true,
                                            'class' => 'form-control',
                                            'style' => 'width: 90%;'
                                        ]) ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <hr>
                            <?= $this->Form->button(__('Search'), [
                                'type' => 'submit',
                                'name' => 'search',
                                'class' => 'btn btn-primary btn-sm'
                            ]) ?>
                        </fieldset>
                    </div>
                    <hr>
                </div>
                <?php if (!empty($sections)): ?>
                    <?php
                    $section_list_name = [];
                    $no_of_sections = 0;
                    foreach ($sections as $key => $value) {
                        if (isset($current_sections_occupation[$key]) && !empty($current_sections_occupation[$key])) {
                            echo $this->Form->hidden("Section.{$key}.id", ['value' => $value['Section']['id']]);
                            $section_list_name[] = sprintf(
                                '%s (Currently hosted students: %s%s)',
                                h($value['Section']['name']),
                                h($current_sections_occupation[$key]),
                                isset($sections_curriculum_name[$key]) && !empty($sections_curriculum_name[$key]) ? sprintf(', %s: %s', __('Section Curriculum'), h($sections_curriculum_name[$key])) : ''
                            );
                            $no_of_sections++;
                        }
                    }
                    ?>
                    <?php if (!empty($section_list_name)): ?>
                        <h6 id="validation-message_non_selected" class="text-danger" style="font-size: 14px;"></h6>
                        <fieldset style="padding-bottom: 15px; padding-top: 15px;">
                            <legend>&nbsp;&nbsp; <?= __('Select Sections to Merge') ?> &nbsp;&nbsp;</legend>
                            <div class="row">
                                <div class="col-md-12">
                                    <?= $this->Form->control('Sections', [
                                        'label' => false,
                                        'type' => 'select',
                                        'multiple' => 'checkbox',
                                        'options' => $section_list_name,
                                        'class' => 'form-control'
                                    ]) ?>
                                </div>
                            </div>
                        </fieldset>
                        <hr>
                        <?= $this->Form->button(__('Merge Sections'), [
                            'type' => 'submit',
                            'name' => 'merge',
                            'id' => 'mergeSections',
                            'disabled' => $no_of_sections < 2,
                            'class' => 'btn btn-primary btn-sm'
                        ]) ?>
                    <?php else: ?>
                        <div class="alert alert-info" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
                            <span style="margin-right: 15px;"></span>
                            <?= __('Unfortunately, all sections are empty, and no section to merge without students.') ?>
                        </div>
                    <?php endif; ?>
                <?php elseif (empty($sections) && !$isbeforesearch): ?>
                    <div class="alert alert-info" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
                        <span style="margin-right: 15px;"></span>
                        <?= __('No section is found with the selected search criteria.') ?>
                    </div>
                <?php endif; ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>
