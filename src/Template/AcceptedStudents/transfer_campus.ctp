<?php
use Cake\I18n\I18n;

$this->set('title', __('Transfer Student Campus'));
$this->Html->script(['jquery-1.6.2.min'], ['block' => 'script']);
?>

<script type="text/javascript">
    $(document).ready(function() {
        function toggleViewFullId(id) {
            var display = $("#" + id).css("display") === 'none';
            $("#" + id + "-img").attr("src", display ? '<?= $this->Url->image('minus2.gif') ?>' : '<?= $this->Url->image('plus2.gif') ?>');
            $("#" + id + "-txt").text(display ? '<?= __('Hide Filter') ?>' : '<?= __('Display Filter') ?>');
            $("#" + id).toggle("slow");
        }

        $("[onclick^='toggleViewFullId']").click(function() {
            toggleViewFullId('list-published-course');
        });

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
                    <div class="accepted-students-form">
                        <p class="fs-5">
                            <strong><?= __('Important Note:') ?></strong> <?= __('This tool will help you to change freshman student campus. By providing some criteria you can find the target student for change. After change you need to do the following:') ?>
                        <ul>
                            <li><?= __('Using the dean account, place the student to the new section of the campus') ?></li>
                            <li><?= __('Register the students to the courses') ?></li>
                        </ul>
                        </p>
                        <?= $this->Form->create(null, ['type' => 'post', 'url' => ['action' => 'transferCampus'], 'class' => 'form-horizontal']) ?>
                        <div onclick="toggleViewFullId('list-published-course')">
                            <?php if (!empty($autoApprove)): ?>
                                <?= $this->Html->image('plus2.gif', ['id' => 'list-published-course-img']) ?>
                                <span style="font-size: 10px; vertical-align: top; font-weight: bold;" id="list-published-course-txt"><?= __('Display Filter') ?></span>
                            <?php else: ?>
                                <?= $this->Html->image('minus2.gif', ['id' => 'list-published-course-img']) ?>
                                <span style="font-size: 10px; vertical-align: top; font-weight: bold;" id="list-published-course-txt"><?= __('Hide Filter') ?></span>
                            <?php endif; ?>
                        </div>
                        <div id="list-published-course" style="display: <?= !empty($autoApprove) ? 'none' : 'block' ?>">
                            <table class="table fs-5">
                                <tr>
                                    <td><?= __('Academic Year') ?></td>
                                    <td>
                                        <?= $this->Form->control('AcceptedStudent.academic_year', [
                                            'id' => 'academic-year',
                                            'label' => false,
                                            'type' => 'select',
                                            'options' => $academicYearLists,
                                            'empty' => __('--Select Academic Year--'),
                                            'value' => $selectedAcademicYear ?? '',
                                            'class' => 'form-control'
                                        ]) ?>
                                    </td>
                                    <td><?= __('Current College') ?></td>
                                    <td>
                                        <?= $this->Form->control('AcceptedStudent.college_id', [
                                            'label' => false,
                                            'class' => 'form-control'
                                        ]) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?= __('Program') ?></td>
                                    <td>
                                        <?= $this->Form->control('AcceptedStudent.program_id', [
                                            'label' => false,
                                            'class' => 'form-control'
                                        ]) ?>
                                    </td>
                                    <td><?= __('Program Type') ?></td>
                                    <td>
                                        <?= $this->Form->control('AcceptedStudent.program_type_id', [
                                            'label' => false,
                                            'class' => 'form-control'
                                        ]) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?= __('Name') ?></td>
                                    <td>
                                        <?= $this->Form->control('AcceptedStudent.name', [
                                            'label' => false,
                                            'class' => 'form-control'
                                        ]) ?>
                                    </td>
                                    <td><?= __('Current Campus') ?></td>
                                    <td>
                                        <?= $this->Form->control('AcceptedStudent.campus_id', [
                                            'label' => false,
                                            'class' => 'form-control'
                                        ]) ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4">
                                        <div class="form-group">
                                            <?= $this->Form->button(__('Continue'), ['name' => 'searchbutton', 'class' => 'btn btn-primary']) ?>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <?php if (!empty($autoPlacedStudents)): ?>
                            <?= $this->Form->hidden('AcceptedStudent.academic_year', ['value' => $selectedAcademicYear]) ?>
                            <?php if (empty($turnOffApproveButton)): ?>
                                <table class="table">
                                    <tr>
                                        <td><?= __('Select the campus you want to transfer the selected student.') ?></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <?= $this->Form->control('campus_id', [
                                                'empty' => __('--select campus--'),
                                                'required' => true,
                                                'options' => $availableCampuses,
                                                'label' => ['text' => __('Select the target campus'), 'class' => 'control-label'],
                                                'class' => 'form-control'
                                            ]) ?>
                                        </td>
                                        <td>
                                            <?= $this->Form->control('selected_college_id', [
                                                'empty' => __('--select campus--'),
                                                'required' => true,
                                                'options' => $selectedColleges,
                                                'label' => ['text' => __('Select the target college'), 'class' => 'control-label'],
                                                'class' => 'form-control'
                                            ]) ?>
                                        </td>
                                    </tr>
                                </table>
                            <?php endif; ?>
                            <table class="table table-bordered table-striped">
                                <tr>
                                    <th colspan="11" class="h2"><?= __('List of students placed to campus.') ?></th>
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
                                    <th><?= __('Academic Year') ?></th>
                                    <th><?= __('Campus') ?></th>
                                </tr>
                                <?php $serialNumber = 1; $count = 0; ?>
                                <?php foreach ($autoPlacedStudents as $index => $acceptedStudent): ?>
                                    <tr class="<?= $index % 2 == 0 ? 'altrow' : '' ?>">
                                        <td><?= $serialNumber++ ?></td>
                                        <td>
                                            <?= $this->Form->checkbox("AcceptedStudent.approve.{$acceptedStudent->id}", ['class' => 'checkbox1']) ?>
                                            <?= $this->Form->hidden("AcceptedStudent.{$count}.id", ['value' => $acceptedStudent->id]) ?>
                                        </td>
                                        <td><?= h($acceptedStudent->full_name) ?></td>
                                        <td><?= h($acceptedStudent->sex) ?></td>
                                        <td><?= h($acceptedStudent->studentnumber) ?></td>
                                        <td><?= h($acceptedStudent->College->name) ?></td>
                                        <td><?= h($acceptedStudent->academic_year) ?></td>
                                        <td><?= h($acceptedStudent->Campus->name) ?></td>
                                    </tr>
                                    <?php $count++; ?>
                                <?php endforeach; ?>
                            </table>
                            <tr>
                                <td>
                                    <div class="form-group">
                                        <?= $this->Form->button(__('Transfer'), ['name' => 'transfer', 'class' => 'btn btn-primary']) ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <?= $this->Form->end() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
