<?php
use Cake\I18n\I18n;

$this->set('title', __('Add Accepted Student'));
?>

<script type="text/javascript">
    $(document).ready(function() {
        $("#college-id").change(function() {
            $("#department-id").prop('disabled', true);
            $("#college-id").prop('disabled', true);
            var cid = $("#college-id").val();
            var formUrl = '<?= $this->Url->build(['controller' => 'Departments', 'action' => 'getDepartmentCombo', '_ext' => 'json']) ?>/' + cid;

            $.ajax({
                type: 'GET',
                url: formUrl,
                data: { cid: cid },
                dataType: 'json',
                success: function(data, textStatus, xhr) {
                    $("#department-id").prop('disabled', false);
                    $("#college-id").prop('disabled', false);
                    $("#department-id").empty();
                    $("#department-id").append('<option><?= __('No department') ?></option>');
                    $.each(data, function(key, value) {
                        $("#department-id").append('<option value="' + key + '">' + value + '</option>');
                    });
                },
                error: function(xhr, textStatus, error) {
                    alert(textStatus);
                }
            });
            return false;
        });
    });
</script>

<div class="container">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="accepted-students-form">
                        <h2><?= __('Add Accepted Student') ?></h2>
                        <p class="text-muted">
                            <strong><?= __('Important Note:') ?></strong> <?= __('Accepted students can be involved in auto/manual placement by their college if their college is only known.') ?>
                        <ol style="padding-top: 0; margin-top: 0;">
                            <li><?= __('No department for those college students without department.') ?></li>
                            <li><?= __('The system displays the current academic year and one year in the future (Academic year starts from September 1 and ends in August 31.)') ?></li>
                        </ol>
                        </p>

                        <?= $this->Form->create(null, ['type' => 'post', 'class' => 'form-horizontal']) ?>
                        <table>
                            <tr>
                                <td>
                                    <table>
                                        <tbody>
                                        <tr>
                                            <td>
                                                <?= $this->Form->control('AcceptedStudent.first_name', [
                                                    'label' => ['text' => __('First Name'), 'class' => 'control-label'],
                                                    'class' => 'form-control'
                                                ]) ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <?= $this->Form->control('AcceptedStudent.middle_name', [
                                                    'label' => ['text' => __('Middle Name'), 'class' => 'control-label'],
                                                    'class' => 'form-control'
                                                ]) ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <?= $this->Form->control('AcceptedStudent.last_name', [
                                                    'label' => ['text' => __('Last Name'), 'class' => 'control-label'],
                                                    'class' => 'form-control'
                                                ]) ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding-left: 150px;">
                                                <?= $this->Form->control('AcceptedStudent.sex', [
                                                    'type' => 'radio',
                                                    'options' => ['male' => __('Male'), 'female' => __('Female')],
                                                    'label' => false,
                                                    'separator' => '<br/>',
                                                    'class' => 'form-check-input'
                                                ]) ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <?= $this->Form->control('AcceptedStudent.EHEECE_total_results', [
                                                    'label' => ['text' => __('EHEECE Total Results'), 'class' => 'control-label'],
                                                    'class' => 'form-control'
                                                ]) ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <?= $this->Form->control('AcceptedStudent.region_id', [
                                                    'label' => ['text' => __('Region'), 'class' => 'control-label'],
                                                    'class' => 'form-control',
                                                    'style' => 'width: 200px'
                                                ]) ?>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                                <td>
                                    <table>
                                        <tbody>
                                        <tr>
                                            <td>
                                                <?= $this->Form->control('AcceptedStudent.academic_year', [
                                                    'id' => 'academic-year',
                                                    'type' => 'select',
                                                    'options' => $academicYearList,
                                                    'label' => ['text' => __('Academic Year'), 'class' => 'control-label'],
                                                    'class' => 'form-control',
                                                    'style' => 'width: 120px',
                                                    'value' => $this->request->getData('AcceptedStudent.academic_year', $defaultAcademicYear)
                                                ]) ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <?= $this->Form->control('AcceptedStudent.college_id', [
                                                    'id' => 'college-id',
                                                    'empty' => __('--select college--'),
                                                    'label' => ['text' => __('College'), 'class' => 'control-label'],
                                                    'class' => 'form-control',
                                                    'style' => 'width: 200px'
                                                ]) ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <?= $this->Form->control('AcceptedStudent.department_id', [
                                                    'id' => 'department-id',
                                                    'empty' => __('No department'),
                                                    'label' => ['text' => __('Department'), 'class' => 'control-label'],
                                                    'class' => 'form-control',
                                                    'style' => 'width: 200px'
                                                ]) ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <?= $this->Form->control('AcceptedStudent.program_id', [
                                                    'label' => ['text' => __('Program'), 'class' => 'control-label'],
                                                    'class' => 'form-control',
                                                    'style' => 'width: 150px'
                                                ]) ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <?= $this->Form->control('AcceptedStudent.program_type_id', [
                                                    'label' => ['text' => __('Program Type'), 'class' => 'control-label'],
                                                    'class' => 'form-control',
                                                    'style' => 'width: 150px'
                                                ]) ?>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </table>
                        <div class="form-group">
                            <?= $this->Form->button(__('Submit'), ['class' => 'btn btn-primary']) ?>
                        </div>
                        <?= $this->Form->end() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
