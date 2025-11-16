<?php
$this->assign('title', __('Split Section'));
?>

<script type="text/javascript">
    $(document).ready(function() {
        var splitting_selected_section = false;

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

        $('#splitSection').click(function(e) {
            var selectedSectionName = $('#selectedSectionName').val();
            var splittingInToSections = $('#splittingInToSections').val();

            if (selectedSectionName === '' || selectedSectionName === '-1') {
                $('#selectedSectionName').focus();
                return false;
            }
            selectedSectionName = $('#selectedSectionName').find(':selected').text();

            if (splitting_selected_section) {
                alert('Splitting Selected Section, please wait a moment...');
                $('#splitSection').prop('disabled', true);
                return false;
            }

            var confirmed = confirm(selectedSectionName + ' section will be split into ' + splittingInToSections + ' different sections and all students currently assigned in this section will be evenly distributed to ' + splittingInToSections + ' sections. Are you sure you want to split the section?');
            if (confirmed) {
                $('#splitSection').val('Splitting Selected Section...');
                splitting_selected_section = true;
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
                <?= __('Split Section') ?>
            </span>
        </h3>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-12">
                <?= $this->Form->create('Section', ['url' => ['controller' => 'Sections', 'action' => 'splitSection']]) ?>
                <div style="margin-top: -30px;">
                    <hr>
                    <blockquote>
                        <h6><i class="fa fa-info"></i> &nbsp; <?= __('Important Note:') ?></h6>
                        <p style="text-align: justify;">
                            <span class="text-dark" style="font-size: 15px;">
                                <?= __('This tool will help you to split a section for the purpose of management if the number of students in the given section is too large.') ?>
                            </span>
                            <br>
                            <i class="text-danger" style="font-size: 16px;">
                                <?= __('To avoid possible complications, you\'re not advised to split sections which have course instructor assignments or grade submissions.') ?>
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
                    $sections_array = [-1 => '[ Please Select Section ]'];
                    foreach ($sections as $key => $value) {
                        if (isset($current_sections_occupation[$key]) && !empty($current_sections_occupation[$key])) {
                            echo $this->Form->hidden("Section.{$key}.id", ['value' => $value['Section']['id']]);
                            $sections_array[] = sprintf(
                                '%s (Currently hosted students: %s)',
                                h($value['Section']['name']),
                                h($current_sections_occupation[$key])
                            );
                        }
                    }
                    ?>
                    <fieldset style="padding-bottom: 0px; padding-top: 15px;">
                        <legend>&nbsp;&nbsp; <?= __('Select Section to Split') ?> &nbsp;&nbsp;</legend>
                        <div class="row">
                            <div class="col-md-8">
                                <?= $this->Form->control('selectedsection', [
                                    'label' => __('Sections: '),
                                    'id' => 'selectedSectionName',
                                    'type' => 'select',
                                    'options' => $sections_array,
                                    'class' => 'form-control',
                                    'style' => 'width: 90%;'
                                ]) ?>
                            </div>
                            <div class="col-md-2">
                                <?= $this->Form->control('number_of_section', [
                                    'label' => __('Split to: '),
                                    'id' => 'splittingInToSections',
                                    'type' => 'select',
                                    'options' => ['2' => '2 sections', '3' => '3 sections'],
                                    'class' => 'form-control',
                                    'style' => 'width: 90%;'
                                ]) ?>
                            </div>
                            <div class="col-md-2"></div>
                        </div>
                    </fieldset>
                    <hr>
                    <?= $this->Form->button(__('Split Selected Section'), [
                        'type' => 'submit',
                        'name' => 'split',
                        'id' => 'splitSection',
                        'class' => 'btn btn-primary btn-sm'
                    ]) ?>
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
