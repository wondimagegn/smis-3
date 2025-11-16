<?php
use Cake\I18n\I18n;

$this->set('title', __('Accepted Students'));
?>

<div class="box">

        <div class="box-header bg-transparent">
            <div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
                <span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('Accepted Students'); ?></span>
            </div>
        </div>

        <div class="box-body">
            <div class="row">
                <div class="large-12 columns">
                    <?php if ($this->request->getSession()->read('Auth.User.role_id') != ROLE_STUDENT): ?>
                        <?= $this->Form->create(null, ['type' => 'get',
                            'url' => ['action' => 'index'], 'class' => 'form-horizontal']) ?>


                        <div style="margin-top: -30px;">
                            <hr>
                            <div onclick="toggleViewFullId('list-published-course')">
                                <?php if (!empty($turnOffSearch)): ?>
                                    <?= $this->Html->image('plus2.gif', ['id' => 'list-published-course-img']) ?>
                                    <span style="font-size: 10px; vertical-align: top; font-weight: bold;" id="list-published-course-txt"><?= __('Display Filter') ?></span>
                                <?php else: ?>
                                    <?= $this->Html->image('minus2.gif', ['id' => 'list-published-course-img']) ?>
                                    <span style="font-size: 10px; vertical-align: top; font-weight: bold;" id="list-published-course-txt"><?= __('Hide Filter') ?></span>
                                <?php endif; ?>
                            </div>
                            <div id="list-published-course" style="display: <?= !empty($turnOffSearch) ? 'none' : 'block' ?>;">
                                <fieldset style="padding-bottom: 0; padding-top: 5px;">
                                    <legend><?= __('Search Filters') ?></legend>
                                    <div class="row">
                                        <div class="large-3 columns">
                                            <?= $this->Form->control('Search.academicyear', [
                                                'label' => ['text' => __('Admission Year'), 'class' => 'control-label'],
                                                'style' => 'width: 90%',
                                                'empty' => __('All Admission Year'),
                                                'options' => $academicYearMinusSeparated,
                                                'value' => $this->request->getQuery('Search.academicyear'),

                                                'class' => 'form-control'
                                            ]) ?>
                                        </div>
                                        <div class="large-3 columns">
                                            <?= $this->Form->control('Search.program_id', [
                                                'label' => ['text' => __('Program'), 'class' => 'control-label'],
                                                'style' => 'width: 90%',
                                                'empty' => __('All Programs'),
                                                'options' => $programs,
                                                'value' => $this->request->getQuery('Search.program_id'),

                                                'class' => 'form-control'
                                            ]) ?>
                                        </div>
                                        <div class="large-3 columns">
                                            <?= $this->Form->control('Search.program_type_id', [
                                                'label' => ['text' => __('Program Type'), 'class' => 'control-label'],
                                                'style' => 'width: 90%',
                                                'empty' => __('All Program Types'),
                                                'options' => $programTypes,
                                                'value' => $this->request->getQuery('Search.program_type_id'),

                                                'class' => 'form-control'
                                            ]) ?>
                                        </div>
                                        <div class="large-3 columns">
                                            <?= $this->Form->control('Search.admitted', [
                                                'label' => ['text' => __('Status'), 'class' => 'control-label'],
                                                'style' => 'width: 90%',
                                                'options' => ['0' => __('All'), '1' => __('Not Admitted'), '2' => __('Admitted')],
                                                'value' => '2',
                                                'value' => $this->request->getQuery('Search.admitted', '2'),

                                                'class' => 'form-control'
                                            ]) ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="large-6 columns">
                                            <?php if (!empty($colleges)): ?>
                                                <?= $this->Form->control('Search.college_id', [
                                                    'label' => ['text' => __('College'), 'class' => 'control-label'],
                                                    'style' => 'width: 90%',
                                                    'value' => $this->request->getQuery('Search.college_id'),

                                                    'empty' => __('All Colleges'),
                                                    'class' => 'form-control'
                                                ]) ?>
                                            <?php elseif (!empty($departments)): ?>
                                                <?= $this->Form->control('Search.department_id', [
                                                    'label' => ['text' => __('Department'), 'class' => 'control-label'],
                                                    'style' => 'width: 90%',
                                                    'value' => $this->request->getQuery('Search.department_id'),

                                                    'empty' => __('All Departments'),
                                                    'class' => 'form-control'
                                                ]) ?>
                                            <?php endif; ?>
                                        </div>
                                        <div class="large-3 columns">
                                            <?= $this->Form->control('Search.name', [
                                                'label' => ['text' => __('Student Name ID'), 'class' => 'control-label'],
                                                'placeholder' => __('Name or Student ID...'),
                                                'value' => $this->request->getQuery('Search.name'),

                                                'style' => 'width: 90%',
                                                'class' => 'form-control'
                                            ]) ?>
                                        </div>
                                        <div class="large-3 columns">
                                            <?= $this->Form->control('Search.limit', [
                                                'id' => 'limit',
                                                'type' => 'number',
                                                'min' => 1,
                                                'max' => 5000,
                                                'value' => $this->request->getData('Search.limit', $limit),
                                                'step' => 1,
                                                'label' => ['text' => __('Limit'), 'class' => 'control-label'],
                                                'style' => 'width: 40%',
                                                'class' => 'form-control'
                                            ]) ?>
                                            <?= $this->Form->hidden('page',
                                                ['value' => $this->request->getData('Search.page', '')]) ?>
                                            <?= $this->Form->hidden('sort',
                                                ['value' => $this->request->getData('Search.sort', '')]) ?>
                                            <?= $this->Form->hidden('direction',
                                                ['value' => $this->request->getData('Search.direction',
                                                    '')]) ?>
                                        </div>
                                    </div>
                                    <?php if (!empty($departments) && $this->request->getSession()->read('Auth.User.role_id') != ROLE_STUDENT && $this->request->getSession()->read('Auth.User.role_id') != ROLE_REGISTRAR && $this->request->getSession()->read('Auth.User.role_id') != ROLE_COLLEGE && $this->request->getSession()->read('Auth.User.role_id') != ROLE_DEPARTMENT): ?>
                                        <div class="row">
                                            <div class="large-6 columns">
                                                <?= $this->Form->control('Search.department_id', [
                                                    'label' => ['text' => __('Department'), 'class' => 'control-label'],
                                                    'style' => 'width: 90%',
                                                    'empty' => __('All Departments'),
                                                    'value' => $this->request->getQuery('Search.department_id'),

                                                    'class' => 'form-control'
                                                ]) ?>
                                            </div>
                                            <div class="large-6 columns"></div>
                                        </div>
                                    <?php endif; ?>
                                    <hr>
                                    <div class="form-group">
                                        <?= $this->Form->button(__('Search'), ['name' => 'search',
                                            'id' => 'search','value' => 'search', 'class' => 'btn btn-primary']) ?>
                                    </div>
                                </fieldset>
                                <?php if ($this->request->getSession()->read('Auth.User.role_id')
                                    == ROLE_REGISTRAR): ?>
                                    <br>
                                    <hr>
                                    <div style="margin-top: 5px;">
                                        <blockquote>
                                            <h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
                                            <span style="text-align:justify;" class="fs16 text-gray">The student list you will get here depends on your <b style="text-decoration: underline;"><i>assigned College or Department, assigned Program and Program Types, and with your search conditions</i></b>. You can contact the registrar to adjust permissions assigned to you if you miss your students here.</span>
                                        </blockquote>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <hr>
                        <?= $this->Form->end() ?>
                    <?php endif; ?>
                    <?php $notAdmittedStudentsCount = 0; ?>
                    <?php if (!empty($acceptedStudents)): ?>
                        <?php if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR || $this->request->getSession()->read('Auth.User.Role.parent_id') == ROLE_REGISTRAR): ?>
                            <?= $this->Form->create(null, ['id' => 'accepted-form',
                                'url' => ['action' => 'delete'], 'class' => 'form-horizontal']) ?>
                        <?php endif; ?>
                        <h6 id="validation-message-non-selected" class="text-danger fs-5"></h6>
                        <br>

                        <div style="overflow-x:auto;">
                            <table cellpadding="0" cellspacing="0" class="table">
                                <thead>
                                <tr>
                                    <?php if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR && $this->request->getSession()->read('Auth.User.is_admin') == 1): ?>
                                        <td class="text-center"><?= $this->Form->checkbox('select-all', ['id' => 'select-all']) ?></td>
                                    <?php endif; ?>
                                    <?php if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR || $this->request->getSession()->read('Auth.User.role_id') == ROLE_COLLEGE): ?>
                                        <td class="text-center">&nbsp;</td>
                                    <?php endif; ?>
                                    <td class="text-center"><?= $this->Paginator->sort('id', __('#')) ?></td>
                                    <td style="width: 25%;" class="align-middle"><?= $this->Paginator->sort('full_name', __('Full Name')) ?></td>
                                    <td class="text-center"><?= $this->Paginator->sort('sex', __('Sex')) ?></td>
                                    <td class="text-center"><?= $this->Paginator->sort('studentnumber', __('Student ID')) ?></td>
                                    <td class="text-center"><?= $this->Paginator->sort('eheece_total_results', __('EHEECE')) ?></td>
                                    <?php if ($this->request->getSession()->read('Auth.User.role_id') != ROLE_COLLEGE && $this->request->getSession()->read('Auth.User.role_id') != ROLE_DEPARTMENT): ?>
                                        <td class="text-center"><?= $this->Paginator->sort('college_id', __('College')) ?></td>
                                    <?php endif; ?>
                                    <?php if ($this->request->getSession()->read('Auth.User.role_id') != ROLE_DEPARTMENT): ?>
                                        <td class="text-center"><?= $this->Paginator->sort('department_id', __('Department')) ?></td>
                                    <?php endif; ?>
                                    <td class="text-center"><?= $this->Paginator->sort('program_id', __('Program')) ?></td>
                                    <td class="text-center"><?= $this->Paginator->sort('program_type_id', __('Program Type')) ?></td>
                                    <td class="text-center"><?= $this->Paginator->sort('Placement_Approved_By_Department', __('Department Approval')) ?></td>
                                    <td class="text-center"><?= $this->Paginator->sort('placementtype', __('Placement Type')) ?></td>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $start = $this->Paginator->counter(['format' => '{{start}}']); ?>
                                <?php
                                foreach ($acceptedStudents as $acceptedStudent): ?>
                                    <?php
                                    $class = in_array($acceptedStudent->id, $studentNotDeleted ?? []) ? 'redrow' : '';
                                    ?>
                                    <tr class="<?= $class ?>">
                                        <?php if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR && $this->request->getSession()->read('Auth.User.is_admin') == 1): ?>
                                            <td class="text-center">
                                                <?php if (!empty($acceptedStudent->Student->id)): ?>
                                                    **
                                                <?php else: ?>
                                                    <div style="margin-left: 15%;">
                                                        <?= $this->Form->checkbox("AcceptedStudent.delete.{$acceptedStudent->id}",
                                                            ['class' => 'checkbox1']) ?>
                                                    </div>
                                                    <?php $notAdmittedStudentsCount++; ?>
                                                <?php endif; ?>
                                            </td>
                                        <?php endif; ?>
                                        <?php if ($this->request->getSession()->read('Auth.User.role_id') == ROLE_REGISTRAR || $this->request->getSession()->read('Auth.User.role_id') == ROLE_COLLEGE): ?>
                                            <td class="text-center">
                                                <?= $this->Html->link(
                                                    $this->request->getSession()->read('Auth.User.role_id') == ROLE_COLLEGE ? __('Update Disability') : __('Edit'),
                                                    ['action' => 'edit', $acceptedStudent->id]
                                                ) ?>
                                            </td>
                                        <?php endif; ?>
                                        <td class="text-center"><?= $start++ ?></td>
                                        <td class="align-middle"><?= h($acceptedStudent->full_name) ?></td>
                                        <td class="text-center"><?= h(strcasecmp(trim($acceptedStudent->sex), 'male') == 0 ? 'M' : (strcasecmp(trim($acceptedStudent->sex), 'female') == 0 ? 'F' : '')) ?></td>
                                        <td class="text-center"><?= h($acceptedStudent->studentnumber) ?></td>
                                        <td class="text-center"><?= h($acceptedStudent->eheece_total_results ?? '') ?></td>
                                        <?php if ($this->request->getSession()->read('Auth.User.role_id') != ROLE_COLLEGE && $this->request->getSession()->read('Auth.User.role_id') != ROLE_DEPARTMENT): ?>
                                            <td class="text-center"><?= h($acceptedStudent->college->shortname) ?></td>
                                        <?php endif; ?>
                                        <?php if ($this->request->getSession()->read('Auth.User.role_id') != ROLE_DEPARTMENT): ?>
                                            <td class="text-center">
                                                <?= h($acceptedStudent->department->name ??
                                                    ($acceptedStudent->program_type->id
                                                    == PROGRAM_UNDERGRADUATE ?
                                                        'Pre/Freshman' : ($acceptedStudent->program_id
                                                        == PROGRAM_REMEDIAL ||
                                                        $acceptedStudent->program->id ==
                                                        PROGRAM_REMEDIAL ? 'Remedial Program' : ''))) ?>
                                            </td>
                                        <?php endif; ?>
                                        <td class="text-center"><?= h($acceptedStudent->program->shortname ?? $acceptedStudent->Program->name) ?></td>
                                        <td class="text-center"><?= h($acceptedStudent->program_type->name) ?></td>
                                        <td class="text-center"><?= $acceptedStudent->placement_approved_by_department
                                            == 1 ? '<span class="text-success">' . __('Yes') . '</span>' : '' ?></td>
                                        <td class="text-center">
                                            <?= empty($acceptedStudent->placementtype) &&
                                            !empty($acceptedStudent->online_applicant_id) ?
                                                __('Online Processed') : h(ucwords(strtolower(
                                                    $acceptedStudent->placementtype))) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <br>
                        <?php if ($notAdmittedStudentsCount > 0 &&
                            ($this->request->getSession()->read('Auth.User.role_id') ==
                                ROLE_REGISTRAR ||
                                $this->request->getSession()->read('Auth.User.Role.parent_id')
                                == ROLE_REGISTRAR) &&
                            $this->request->getSession()->read('Auth.User.is_admin') == 1): ?>
                            <div class="form-group">
                                <?= $this->Form->button(__('Delete Selected'),
                                    ['id' => 'delete-selected', 'class' => 'btn btn-primary']) ?>
                            </div>
                        <?php endif; ?>
                        <hr>

                        <div class="row">
                            <div class="large-5 columns">
                                <?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} records out of {{count}} total')]) ?>
                            </div>
                            <div class="large-7 columns">
                                <div class="pagination-centered">
                                    <ul class="pagination">
                                        <?= $this->Paginator->prev('<< ' . __(''),
                                            ['tag' => 'li'], null,
                                            ['class' => 'arrow unavailable']) ?>
                                        <?= $this->Paginator->numbers(['separator' => '',
                                            'tag' => 'li']) ?>
                                        <?= $this->Paginator->next(__('') . ' >>',
                                            ['tag' => 'li'], null,
                                            ['class' => 'arrow unavailable']) ?>
                                    </ul>
                                </div>
                            </div>
                        </div>

                    <?php else: ?>
                        <div class="alert alert-info" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
                            <span style="margin-right: 15px;"></span>
                            <?= __('No Accepted Student is found with the given search criteria') ?>
                        </div>
                    <?php endif; ?>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
</div>

<script type="text/javascript">
    function toggleView(obj) {
        if ($('#c' + obj.id).css("display") == 'none')
            $('#i' + obj.id).attr("src", '/img/minus2.gif');
        else
            $('#i' + obj.id).attr("src", '/img/plus2.gif');
        $('#c' + obj.id).toggle("slow");
    }

    function toggleViewFullId(id) {
        if ($('#' + id).css("display") == 'none') {
            $('#' + id + 'Img').attr("src", '/img/minus2.gif');
            $('#' + id + 'Txt').empty();
            $('#' + id + 'Txt').append('Hide Filter');
        } else {
            $('#' + id + 'Img').attr("src", '/img/plus2.gif');
            $('#' + id + 'Txt').empty();
            $('#' + id + 'Txt').append('Display Filter');
        }
        $('#' + id).toggle("slow");
    }

    var form_being_submitted = false;

    const validationMessageNonSelected = document.getElementById('validation-message_non_selected');

    var checkForm = function(form) {
        var checkboxes = document.querySelectorAll('input[type="checkbox"]');
        var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);

        //alert(checkedOne);
        if (!checkedOne) {
            alert('At least one accepted student must be selected to delete!');
            validationMessageNonSelected.innerHTML = 'At least one accepted student must be selected to delete!';
            return false;
        }

        if (form_being_submitted) {
            alert("Deleting Selected Accepted Students, please wait a moment...");
            form.deleteSelected.disabled = true;
            return false;
        }

        form.deleteSelected.value = 'Deleting Selected Accepted Students...';
        form_being_submitted = true;
        return true;
    };

    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>
