<?php
$this->assign('title', __('Select Section by Year Level'));
?>
 <script type="text/javascript">
        $(document).ready(function() {
            var form_being_submitted = false;
            $('#Add_To_Section_Button').click(function(e) {
                var assignedSectionSelection = $('#SectionAssignedSection').val();
                if (!assignedSectionSelection) {
                    $('#SectionAssignedSection').focus();
                    $('#SectionAssignedSection').attr('title', 'Please select target section to add the student');
                    return false;
                }
                if (form_being_submitted) {
                    alert('Adding to Selected Section, please wait a moment...');
                    $('#Add_To_Section_Button').prop('disabled', true);
                    return false;
                }
                $('#Add_To_Section_Button').val('Adding to Selected Section...');
                form_being_submitted = true;
                return true;
            });

            if (window.history.replaceState) {
                window.history.replaceState(null, null, window.location.href);
            }
        });
    </script>

<?php if (!empty($sections_organized_by_acy)): ?>
    <?= $this->Form->control('assigned_section', [
        'label' => __('Target Section: '),
        'type' => 'select',
        'options' => $sections_organized_by_acy,
        'empty' => '[ Select Section ]',
        'required' => true,
        'id' => 'SectionAssignedSection',
        'class' => 'form-control',
        'style' => 'width: 45%;'
    ]) ?>
    <hr>
    <?= $this->Form->button(__('Add to Selected Section'), [
        'type' => 'submit',
        'id' => 'Add_To_Section_Button',
        'value'=>'submit',
        'class' => 'btn btn-primary btn-sm'
    ]) ?>
<?php else: ?>
    <div class="alert alert-info" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
        <span style="margin-right: 15px;"></span>
        <?= __('No active section is available that corresponds with the specified year level and the student\'s attached curriculum, or the student is already assigned in a section for this year level.') ?>
    </div>
<?php endif; ?>
