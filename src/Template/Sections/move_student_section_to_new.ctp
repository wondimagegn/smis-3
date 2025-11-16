<?php
$this->assign('title', __('Move Student to New Section'));
?>

<script type="text/javascript">
    $(document).ready(function() {
        var form_being_submitted = false;

        $('#SubmitID').click(function(e) {
            var newSectionSelection = $('#Selected_section_id').val();
            if (!newSectionSelection) {
                $('#Selected_section_id').focus();
                $('#Selected_section_id').attr('title', 'Please select target section to move the student');
                return false;
            }
            if (form_being_submitted) {
                alert('Moving to Selected Section, please wait a moment...');
                $('#SubmitID').prop('disabled', true);
                return false;
            }
            $('#SubmitID').val('Moving to Selected Section...');
            form_being_submitted = true;
            return true;
        });

        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    });
</script>

<div class="box">
    <div class="box-header bg-transparent">
        <h3 class="box-title" style="margin-top: 10px;">
            <i class="fa fa-vcard" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;">
                <?= __('Move Student to New Section') ?>
            </span>
        </h3>
        <a class="close-reveal-modal">&#215;</a>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div style="margin-top: -10px;"><hr></div>
            <?= $this->Form->create('Section', ['url' => ['controller' => 'Sections', 'action' => 'sectionMoveUpdate']]) ?>
            <fieldset style="padding-bottom: 5px; padding-top: 5px;">
                <legend>&nbsp;&nbsp;
                    <span class="text-muted" style="font-size: 14px;">
                        <?= h($students['Student']['full_name'] . ' (' . $students['Student']['studentnumber'] . ')') ?>
                    </span>&nbsp;&nbsp;
                </legend>
                <span class="text-muted" style="font-size: 14px;">
                    <?= $this->Form->hidden('Section.1.selected_id', ['value' => 1]) ?>
                    <?= $this->Form->hidden('Section.1.student_id', ['value' => $students['Student']['id']]) ?>
                    <strong><?= __('Current Section: ') ?></strong>
                    <b>
                        <?= __(
                            '%s (%s, %s)',
                            h($previousSectionName['Section']['name']),
                            isset($previousSectionName['YearLevel']) && !empty($previousSectionName['YearLevel']['name']) ?
                                h($previousSectionName['YearLevel']['name']) :
                                ($students['Student']['program_id'] == PROGRAM_REMEDIAL ? __('Remedial') : __('Pre/1st')),
                            h($previousSectionName['Section']['academicyear'])
                        ) ?>
                        <br>
                        <?= h($previousSectionName['Program']['name']) ?> &nbsp;&nbsp; | &nbsp;&nbsp;
                        <?= h($previousSectionName['ProgramType']['name']) ?> &nbsp;&nbsp; | &nbsp;&nbsp;
                        <?= isset($previousSectionName['Department']) && !empty($previousSectionName['Department']['name']) ?
                            h($previousSectionName['Department']['name']) :
                            h($previousSectionName['College']['name']) ?>
                    </b>
                    <br>
                </span>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <?= $this->Form->hidden('previous_section_id', ['value' => $previous_section_id]) ?>
                        <?= $this->Form->control('Selected_section_id', [
                            'label' => __('Select Target Section: '),
                            'id' => 'Selected_section_id',
                            'type' => 'select',
                            'required' => true,
                            'options' => $sections,
                            'empty' => '[ Select Section ]',
                            'class' => 'form-control',
                            'style' => 'width: 80%;'
                        ]) ?>
                    </div>
                </div>
            </fieldset>
            <hr>
            <?= $this->Form->button(__('Move to Selected Section'), [
                'type' => 'submit',
                'id' => 'SubmitID',
                'name' => 'move_to_section',
                'class' => 'btn btn-primary btn-sm'
            ]) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
