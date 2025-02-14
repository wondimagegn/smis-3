<?php

use Cake\Core\Configure;

?>
<?= $this->Form->create(null, ['type' => 'get']); ?>
<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;">
            <i class="fontello-calendar" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;">
                <?= __('Academic Calendars'); ?>
            </span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <fieldset style="padding-bottom: 5px;padding-top: 15px;">
                <div class="row">
                    <div class="col-md-3">
                        <?= $this->Form->control('Search.academic_year', [
                            'label' => 'Academic Year:',
                            'type' => 'select',
                            'options' => $acyearArrayData,
                            'default' => $defaultacademicyear ?? '',
                            'class' => 'form-control'
                        ]); ?>

                    </div>
                    <div class="col-md-3">
                        <?= $this->Form->control('Search.semester', [
                            'label' => 'Semester:',
                            'type' => 'select',
                            'options' => Configure::read('semesters'),
                            'empty' => '[ All Semesters ]',
                            'class' => 'form-control'
                        ]); ?>
                    </div>
                    <div class="col-md-3">
                        <?= $this->Form->control('Search.program_id', [
                            'label' => 'Program:',
                            'type' => 'select',
                            'empty' => '[ All Programs ]',
                            'class' => 'form-control'
                        ]); ?>
                    </div>
                    <div class="col-md-3">
                        <?= $this->Form->control('Search.program_type_id', [
                            'label' => 'Program Type:',
                            'type' => 'select',
                            'empty' => '[ All Program Types ]',
                            'class' => 'form-control'
                        ]); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?php
                        $roleId = $this->request->getSession()->read('Auth.User.role_id');
                        $departmentFieldOptions = ['label' => 'Department:', 'class' => 'form-control'];
                        if ($roleId == Configure::read('ROLE_DEPARTMENT')  || $roleId == Configure::read('ROLE_STUDENT') ) {
                            echo $this->Form->control('Search.department_id', $departmentFieldOptions);
                        } else {
                            $departmentFieldOptions['empty'] = '[ All Departments ]';
                          echo $this->Form->control('Search.department_id', $departmentFieldOptions);
                        }
                        ?>
                    </div>
                    <div class="col-md-3">
                        <?= $this->Form->control('Search.year_level_id', [
                            'label' => 'Year Level:',
                            'type' => 'select',
                            'empty' => '[ All Year Levels ]',
                            'class' => 'form-control'
                        ]); ?>
                    </div>
                </div>
                <hr>
                <?= $this->Form->button(__('View Academic Calendar'), [
                    'type' => 'submit',
                    'class' => 'btn btn-primary'
                ]); ?>
            </fieldset>
        </div>
    </div>
</div>
<?= $this->Form->end(); ?>

<?php if (!empty($academicCalendars)): ?>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th><?= $this->Paginator->sort('full_year', 'Academic Year - Semester'); ?></th>
                <th><?= $this->Paginator->sort('year_name', 'Year Level'); ?></th>
                <th><?= $this->Paginator->sort('program_id', 'Program'); ?></th>
                <th><?= $this->Paginator->sort('program_type_id', 'Program Type'); ?></th>
                <th><?= $this->Paginator->sort('department_id', 'Department Name'); ?></th>

                <?php if ($roleId ==  Configure::read('ROLE_REGISTRAR')   ): ?>
                    <th>Actions</th>
                <?php endif; ?>
            </tr>
            </thead>
            <tbody>
            <?php $count = 1; ?>
            <?php foreach ($academicCalendars as $academicCalendar):

                ?>
                <tr>
                    <td><?= $count++; ?></td>
                    <td><?= h($academicCalendar->full_year); ?></td>
                    <td><?= h($academicCalendar->year_level_name)?></td>
                    <td><?= $this->Html->link(h($academicCalendar->program->name), ['controller' => 'Programs',
                            'action' => 'view', $academicCalendar->program->id]); ?></td>
                    <td><?= $this->Html->link(h($academicCalendar->program_type->name),
                            ['controller' => 'ProgramTypes', 'action' => 'view',
                                $academicCalendar->program_type->id]); ?></td>

                    <td><?= h($academicCalendar->department_name) ?></td>



                    <?php if ($roleId ==  Configure::read('ROLE_REGISTRAR') ): ?>
                        <td>
                            <?= $this->Html->link(__('Edit'), ['action' => 'edit',
                                $academicCalendar->id], ['class' => 'btn btn-sm btn-warning']); ?>
                        </td>
                    <?php endif; ?>
                </tr>
                <?php $count++; ?>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="pagination">
        <?= $this->Paginator->prev('« Previous', ['class' => 'btn btn-secondary']); ?>
        <?= $this->Paginator->numbers(['class' => 'btn btn-light']); ?>
        <?= $this->Paginator->next('Next »', ['class' => 'btn btn-secondary']); ?>
    </div>
<?php else: ?>
    <div class="alert alert-info">
        <?= __('No recent academic calendar defined for ') . ($defaultacademicyear ?? 'the current') . __(' academic year, try adjusting search filters to get previous academic calendars.'); ?>
    </div>
<?php endif; ?>

<script>
    $(document).ready(function() {
        $('form input').on('change', function() {
            updateExtension($(this));
        });

        function updateExtension(el) {
            let val = el.val();
            if (val !== "" && isNaN(val)) {
                alert('Please enter a valid result.');
                el.focus().blur();
                return false;
            } else if (val !== "" && parseInt(val) < 0) {
                el.focus().blur();
                return false;
            }

            $.ajax({
                url: "<?= $this->Url->build(['controller' => 'AcademicCalendars', 'action' => 'autoSaveExtension']); ?>",
                type: 'POST',
                data: $('form').serialize(),
                success: function(data) {
                    console.log('Saved successfully');
                }
            });
        }
    });
</script>
