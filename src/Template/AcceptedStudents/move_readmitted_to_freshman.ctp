<?php
use Cake\I18n\I18n;

$this->set('title', __('Find Readmitted Applicant for Freshman Program'));
$this->Html->script(['jquery-1.6.2.min'], ['block' => 'script']);
?>

<script type="text/javascript">
    $(document).ready(function() {
        var image = new Image();
        image.src = '<?= $this->Url->image('busy.gif') ?>';

        function getCollege() {
            var clg = $("#ajax-campus-id").val();
            $("#selected-college-id").prop('disabled', true);
            $("#selected-college-id").html('<img src="<?= $this->Url->image('busy.gif') ?>" class="img-fluid mx-auto d-block" />');

            var formUrl = '<?= $this->Url->build(['controller' => 'Colleges', 'action' => 'getCollegeCombo', '_ext' => 'json']) ?>/' + clg;

            $.ajax({
                type: 'GET',
                url: formUrl,
                data: { clg: clg },
                dataType: 'json',
                success: function(data, textStatus, xhr) {
                    $("#selected-college-id").prop('disabled', false);
                    $("#selected-college-id").empty();
                    $.each(data, function(key, value) {
                        $("#selected-college-id").append('<option value="' + key + '">' + value + '</option>');
                    });
                },
                error: function(xhr, textStatus, error) {
                    alert(textStatus);
                }
            });
            return false;
        }

        $("#ajax-campus-id").on('change', getCollege);

        $("#select-all").click(function() {
            $(".checkbox1").prop('checked', $(this).prop('checked'));
        });
    });
</script>

<div class="container">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="accepted-students-index">
                        <?php if (empty($showListGenerated) || empty($acceptedStudents)): ?>
                            <h2><?= __('Find readmitted applicant for freshman program.') ?></h2>
                            <?= $this->Form->create(null, ['type' => 'post', 'url' => ['action' => 'moveReadmittedToFreshman'], 'novalidate' => true, 'class' => 'form-horizontal']) ?>
                            <table class="table">
                                <tr>
                                    <td>
                                        <?= $this->Form->control('Search.academic_year', [
                                            'id' => 'academic-year',
                                            'label' => ['text' => __('Readmission Academic Year'), 'class' => 'control-label'],
                                            'type' => 'select',
                                            'options' => $readmittedAC,
                                            'empty' => __('--Select Academic Year--'),
                                            'value' => $selectedAcademicYear ?? '',
                                            'class' => 'form-control'
                                        ]) ?>
                                    </td>
                                    <td>
                                        <?= $this->Form->control('Search.college_id', [
                                            'empty' => __('--Select College--'),
                                            'class' => 'form-control'
                                        ]) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?= $this->Form->control('Search.program_id', [
                                            'empty' => __('--Select Program--'),
                                            'class' => 'form-control'
                                        ]) ?>
                                    </td>
                                    <td>
                                        <?= $this->Form->control('Search.program_type_id', [
                                            'empty' => __('--Select Program Type--'),
                                            'class' => 'form-control'
                                        ]) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <?= $this->Form->control('Search.name', [
                                            'label' => ['text' => __('Name'), 'class' => 'control-label'],
                                            'class' => 'form-control'
                                        ]) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <?= $this->Form->button(__('Find Readmitted Students'), ['name' => 'continue', 'class' => 'btn btn-primary']) ?>
                                        </div>
                                    </td>
                                    <td></td>
                                </tr>
                            </table>
                            <?= $this->Form->end() ?>
                        <?php endif; ?>
                        <?php if (!empty($acceptedStudents)): ?>
                            <?= $this->Form->create(null, ['type' => 'post', 'url' => ['action' => 'moveReadmittedToFreshman'], 'class' => 'form-horizontal']) ?>
                            <table class="table">
                                <tbody>
                                <tr>
                                    <td width="100%">
                                        <table class="table">
                                            <tbody>
                                            <tr>
                                                <td colspan="2">
                                                    <table class="table">
                                                        <tr>
                                                            <td colspan="2"><?= __('Select the campus and the college you want to readmit the selected student in freshman program.') ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <?= $this->Form->control('campus_id', [
                                                                    'empty' => __('--select campus--'),
                                                                    'required' => true,
                                                                    'options' => $availableCampuses,
                                                                    'label' => ['text' => __('Select the target campus'), 'class' => 'control-label'],
                                                                    'id' => 'ajax-campus-id',
                                                                    'class' => 'form-control'
                                                                ]) ?>
                                                            </td>
                                                            <td>
                                                                <?= $this->Form->control('selected_college_id', [
                                                                    'empty' => __('--select campus--'),
                                                                    'id' => 'selected-college-id',
                                                                    'required' => true,
                                                                    'options' => $selectedColleges,
                                                                    'label' => ['text' => __('Select the target college'), 'class' => 'control-label'],
                                                                    'class' => 'form-control'
                                                                ]) ?>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <table class="table table-bordered table-striped">
                                                        <tr>
                                                            <th colspan="11" class="h2"><?= __('List of students who applied for readmission application.') ?></th>
                                                        </tr>
                                                        <tr>
                                                            <th><?= __('No.') ?></th>
                                                            <th>
                                                                <?= __('Select/Unselect All') ?><br/>
                                                                <?= $this->Form->checkbox('SelectAll', ['id' => 'select-all']) ?>
                                                            </th>
                                                            <th><?= __('Full Name') ?></th>
                                                            <th><?= __('Sex') ?></th>
                                                            <th><?= __('Student Number') ?></th>
                                                            <th><?= __('College') ?></th>
                                                            <th><?= __('Department') ?></th>
                                                            <th><?= __('Academic Year') ?></th>
                                                            <th><?= __('Campus') ?></th>
                                                        </tr>
                                                        <?php $serialNumber = 1; ?>
                                                        <?php foreach ($acceptedStudents as $index => $acceptedStudent): ?>
                                                            <tr class="<?= $index % 2 == 0 ? 'altrow' : '' ?>">
                                                                <td><?= $serialNumber++ ?></td>
                                                                <td>
                                                                    <?= $this->Form->checkbox("AcceptedStudent.approve.{$acceptedStudent->Student->AcceptedStudent->id}", ['class' => 'checkbox1']) ?>
                                                                </td>
                                                                <td><?= h($acceptedStudent->Student->AcceptedStudent->full_name) ?></td>
                                                                <td><?= h($acceptedStudent->Student->AcceptedStudent->sex) ?></td>
                                                                <td><?= h($acceptedStudent->Student->AcceptedStudent->studentnumber) ?></td>
                                                                <td><?= h($acceptedStudent->Student->College->name) ?></td>
                                                                <td><?= h($acceptedStudent->Student->Department->name) ?></td>
                                                                <td><?= h($acceptedStudent->Student->AcceptedStudent->academic_year) ?></td>
                                                                <td><?= h($acceptedStudent->Student->College->Campus->name) ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="form-group">
                                                        <?= $this->Form->button(__('Readmit Selected'), ['name' => 'readmitted', 'class' => 'btn btn-primary']) ?>
                                                    </div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <?= $this->Form->end() ?>
                        <?php elseif (empty($acceptedStudents) && !$isBeforeSearch): ?>
                            <div class="alert alert-info">
                                <span></span><?= __('No students who applied for readmission in selected criteria') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
