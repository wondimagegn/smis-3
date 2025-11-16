<?php
use Cake\I18n\I18n;

$this->set('title', __('Import New Students to {0}', \Cake\Core\Configure::read('ApplicationShortName')));
$this->Html->script(['jquery-1.6.2.min'], ['block' => 'script']);
?>

<div class="box">
    <div class="card">

        <div class="box-header bg-transparent">
            <div class="box-title" style="margin-top: 10px;"><i class="fontello-download-outline" style="font-size: larger; font-weight: bold;"></i>
                <span style="font-size: medium; font-weight: bold; margin-top: 20px;">    <?= __('Import New Students to {0}', \Cake\Core\Configure::read('ApplicationShortName')) ?></span>
            </div>
        </div>

        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div style="overflow-x: auto;">
                        <?= $this->Form->create(null, ['type' => 'file', 'url' => ['controller' => 'AcceptedStudents', 'action' => 'importNewlyStudents'], 'class' => 'form-horizontal']) ?>
                        <table class="table">
                            <thead>
                            <tr>
                                <td colspan="4">
                                    <br>
                                    <blockquote>
                                        <h6 class="text-red"><i class="fa fa-info"></i> <?= __('Be-aware:') ?></h6>
                                        <span style="text-align:justify;" class="fs14 text-gray">
                                                <?= __('Before importing the Excel file,
 <b class="text-dark" style="text-decoration: underline;"><i>make sure that the value of college,
  region, program, program types, and department (if it exists or needed) fields are as listed below.
  </i></b> If you think there is a missing college, region, program type, program name, or department,
  please contact the system administrator to add them to the system.') ?>
                                                <br>
                                                <a href="<?= (INCLUDE_STUDENT_NUMBER_IN_IMPORT_TEMPLATE_FILE == 1 ?
                                                    STUDENT_IMPORT_TEMPLATE_FILE : STUDENT_IMPORT_TEMPLATE_FILE_WITHOUT_STUDENT_NUMBER) ?>">
                                                    <?= __('Download Import Template here') ?>
                                                </a>
                                                <?= __('that shows the required fields and sample pre-populated data
                                                that is compatible with the system database.') ?>
                                            </span>
                                    </blockquote>
                                </td>
                            </tr>
                            <?php if (isset($nonValidRows)): ?>
                                <tr>
                                    <td colspan="4" style="background-color: white;">
                                        <div class="alert alert-danger">
                                            <ol style="color: red;">
                                                <?php foreach ($nonValidRows as $v): ?>
                                                    <li><?= h($v) ?></li>
                                                <?php endforeach; ?>
                                            </ol>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            </thead>
                            <tbody>
                            <tr>
                                <td style="background-color: white;">
                                    <table class="table">
                                        <tbody>
                                        <tr>
                                            <th><?= __('Import Accepted Students') ?></th>
                                        </tr>
                                        <tr>
                                            <td style="background-color: white;">
                                                <?= $this->Form->control('AcceptedStudent.academic_year', [
                                                    'id' => 'academic-year',
                                                    'label' => ['text' => __('Academic Year'), 'class' => 'control-label'],
                                                    'type' => 'select',
                                                    'options' => $academicYearArrayData,
                                                    'empty' => __('[ Select Academic Year ]'),
                                                    'value' => $this->request->getData('AcceptedStudent.academic_year', ''),
                                                    'class' => 'form-control'
                                                ]) ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="background-color: white;">
                                                <?= $this->Form->control('AcceptedStudent.File', [
                                                    'type' => 'file',
                                                    'label' => false,
                                                    'class' => 'form-control-file'
                                                ]) ?>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <br>
                                    <div class="form-group">
                                        <?= $this->Form->button(__('Upload'), ['id' => 'upload-btn', 'class' => 'btn btn-primary']) ?>
                                    </div>
                                </td>
                                <td style="background-color: white;">
                                    <table class="table">
                                        <tbody>
                                        <tr>
                                            <th><?= __('Colleges / Institutes / Schools') ?></th>
                                        </tr>
                                        <?php foreach ($departmentsOrganizedByCollege as $college => $department): ?>
                                            <tr>
                                                <td><h6 class="fs-5"><?= h($college) ?></h6></td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <table class="table">
                                                        <?php foreach ($department as $k => $dep): ?>
                                                            <tr>
                                                                <td><?= h($dep) ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </table>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </td>
                                <td style="background-color: white;">
                                    <table class="table">
                                        <tbody>
                                        <tr>
                                            <th><?= __('Programs') ?></th>
                                        </tr>
                                        <?php foreach ($programs as $cv): ?>
                                            <tr>
                                                <td><?= h($cv) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <br>
                                    <table class="table">
                                        <tbody>
                                        <tr>
                                            <th><?= __('Program Types') ?></th>
                                        </tr>
                                        <?php foreach ($programTypes as $cv): ?>
                                            <tr>
                                                <td><?= h($cv) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </td>
                                <td style="background-color: white;">
                                    <table class="table">
                                        <tbody>
                                        <tr>
                                            <th><?= __('Regions') ?></th>
                                        </tr>
                                        <?php foreach ($regions as $cv): ?>
                                            <tr>
                                                <td><?= h($cv) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <?= $this->Form->end() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $("#upload-btn").click(function() {
            if (form_being_submitted) {
                alert('<?= __('Uploading Students, please wait a moment...') ?>');
                $(this).prop('disabled', true);
                return false;
            }
            $(this).val('<?= __('Uploading Students...') ?>');
            form_being_submitted = true;
            return true;
        });

        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    });

    var form_being_submitted = false;
</script>
