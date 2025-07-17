<?php
use Cake\I18n\FrozenTime;
// Set display data from session
$session = $this->request->getSession();
if ($session->check('display_field_student')) {
    $this->request->getData()['Display'] = $session->read('display_field_student');
}

// Define field labels for table headers
$fieldLabels = [
    'gender' => 'Sex',
    'department_id' => 'Department',
    'academicyear' => 'Admission Year',
    'studentnumber' => 'Student ID',
    'student_national_id' => 'National ID'
];
?>

<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;">
            <i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('List Admitted Students') ?></span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <?php if ($role_id != ROLE_STUDENT): ?>
                    <div style="margin-top: -30px;">
                        <hr>
                        <?php if ($session->read('Auth.User.role_id') == ROLE_REGISTRAR): ?>
                            <div style="margin-top: -5px;">
                                <blockquote>
                                    <h6><i class="fa fa-info"></i> Important Note:</h6>
                                    <span style="text-align:justify;" class="fs14 text-gray">
                                        The student list you will get here depends on your <b style="text-decoration: underline;"><i>assigned College or Department, assigned Program and Program Types, and with your search conditions</i></b>. You can contact the registrar to adjust permissions assigned to you if you miss your students here.
                                    </span>
                                </blockquote>
                            </div>
                        <?php endif; ?>

                        <hr>

                        <div onclick="toggleViewFullId('ListPublishedCourse')">
                            <?php if (!empty($turn_off_search)): ?>
                                <?= $this->Html->image('img/plus2.gif', ['id' => 'ListPublishedCourseImg']) ?>
                                <span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Display Filter</span>
                            <?php else: ?>
                                <?= $this->Html->image('img/minus2.gif', ['id' => 'ListPublishedCourseImg']) ?>
                                <span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">Hide Filter</span>
                            <?php endif; ?>
                        </div>

                        <div id="ListPublishedCourse" style="display: <?= !empty($turn_off_search) ? 'none' : 'block' ?>">
                            <?= $this->Form->create(null, ['url' => ['action' => 'search']]) ?>
                            <fieldset style="padding-bottom: 0px; padding-top: 15px;">
                                <div class="row">
                                    <div class="large-3 columns">
                                        <?= $this->Form->control('Search.academicyear', [
                                            'label' => 'Admission Year: ',
                                            'style' => 'width:90%',
                                            'empty' => 'All Admission Year',
                                            'options' => $acyear_array_data
                                        ]) ?>
                                    </div>
                                    <div class="large-3 columns">
                                        <?= $this->Form->control('Search.program_id', [
                                            'label' => 'Program: ',
                                            'style' => 'width:90%',
                                            'empty' => 'All Programs',
                                            'options' => $programs
                                        ]) ?>
                                    </div>
                                    <div class="large-3 columns">
                                        <?= $this->Form->control('Search.program_type_id', [
                                            'label' => 'Program Type: ',
                                            'style' => 'width:90%',
                                            'empty' => 'All Program Types',
                                            'options' => $programTypes
                                        ]) ?>
                                    </div>
                                    <div class="large-3 columns">
                                        <?= $this->Form->control('Search.gender', [
                                            'label' => 'Sex',
                                            'style' => 'width:90%',
                                            'type' => 'select',
                                            'empty' => 'All',
                                            'options' => ['female' => 'Female', 'male' => 'Male']
                                        ]) ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="large-6 columns">
                                        <?php if (isset($colleges) && !empty($colleges)): ?>
                                            <?= $this->Form->control('Search.college_id', [
                                                'label' => 'College: ',
                                                'style' => 'width:95%',
                                                'empty' => 'All Assigned Colleges',
                                                'options' => $colleges
                                            ]) ?>
                                        <?php elseif (isset($departments) && !empty($departments)): ?>
                                            <?= $this->Form->control('Search.department_id', [
                                                'label' => 'Department: ',
                                                'style' => 'width:90%',
                                                'empty' => 'All Assigned Departments',
                                                'options' => $departments
                                            ]) ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="large-2 columns">
                                        <?= $this->Form->control('Search.status', [
                                            'label' => 'Status: ',
                                            'empty' => 'All',
                                            'options' => ['0' => 'Not Graduated', '1' => 'Graduated'],
                                            'default' => 0,
                                            'type' => 'select',
                                            'style' => 'width:90%'
                                        ]) ?>
                                    </div>
                                    <div class="large-2 columns">
                                        <?= $this->Form->control('Search.name', [
                                            'label' => 'Student Name or ID:',
                                            'placeholder' => 'Name or ID ..',
                                            'value' => $name,
                                            'style' => 'width:90%'
                                        ]) ?>
                                    </div>
                                    <div class="large-2 columns">
                                        <?= $this->Form->control('Search.limit', [
                                            'id' => 'limit',
                                            'type' => 'number',
                                            'min' => 0,
                                            'max' => 5000,
                                            'step' => 100,
                                            'label' => 'Limit: ',
                                            'style' => 'width:90%',
                                            'value' => $this->request->getData('Search.limit', $limit)
                                        ]) ?>

                                        <?= $this->Form->hidden('Search.page', ['value' => $this->request->getData('Search.page')]) ?>
                                        <?= $this->Form->hidden('Search.sort', ['value' => $this->request->getData('Search.sort')]) ?>
                                        <?= $this->Form->hidden('Search.direction', ['value' => $this->request->getData('Search.direction')]) ?>
                                    </div>
                                </div>

                                <?php if (isset($departments) && !empty($departments) &&
                                    !in_array($session->read('Auth.User.role_id'), [ROLE_STUDENT, ROLE_REGISTRAR, ROLE_COLLEGE, ROLE_DEPARTMENT])): ?>
                                    <div class="row">
                                        <div class="large-6 columns">
                                            <?= $this->Form->control('Search.department_id', [
                                                'label' => 'Department: ',
                                                'style' => 'width:90%',
                                                'empty' => 'All Departments',
                                                'options' => $departments
                                            ]) ?>
                                        </div>
                                        <div class="large-6 columns"></div>
                                    </div>
                                <?php endif; ?>

                                <hr>
                                <div class="large-12 columns">
                                    <div onclick="toggleViewFullId('ListStudents')">
                                        <?php if (!empty($students)): ?>
                                            <?= $this->Html->image('img/plus2.gif', ['id' => 'ListStudentsImg']) ?>
                                            <span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListStudentsTxt">Adjust Fields</span>
                                        <?php else: ?>
                                            <?= $this->Html->image('img/minus2.gif', ['id' => 'ListStudentsImg']) ?>
                                            <span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListStudentsTxt">Hide Fields</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="large-12 columns" id="ListStudents" style="display: <?= !empty($students) ? 'none' : 'block' ?>">
                                    <div class="row">
                                        <div class="large-12 columns"> </div>
                                    </div>
                                    <div class="row">
                                        <div class="large-2 columns">
                                            <?= $this->Form->control('Display.full_name', [
                                                'label' => 'Full Name',
                                                'type' => 'checkbox',
                                                'checked' => true
                                            ]) ?>
                                        </div>
                                        <div class="large-2 columns">
                                            <?= $this->Form->control('Display.full_am_name', [
                                                'label' => 'Amharic Name',
                                                'type' => 'checkbox'
                                            ]) ?>
                                        </div>
                                        <div class="large-2 columns">
                                            <?= $this->Form->control('Display.gender', [
                                                'label' => 'Sex',
                                                'type' => 'checkbox',
                                                'checked' => true
                                            ]) ?>
                                        </div>
                                        <div class="large-2 columns">
                                            <?= $this->Form->control('Display.studentnumber', [
                                                'label' => 'Student ID',
                                                'type' => 'checkbox',
                                                'checked' => true
                                            ]) ?>
                                        </div>
                                        <div class="large-2 columns">
                                            <?= $this->Form->control('Display.academicyear', [
                                                'label' => 'Admission Year',
                                                'type' => 'checkbox'
                                            ]) ?>
                                        </div>
                                        <div class="large-2 columns">
                                            <?= $this->Form->control('Display.program_id', [
                                                'label' => 'Program',
                                                'type' => 'checkbox',
                                                'checked' => true
                                            ]) ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="large-2 columns">
                                            <?= $this->Form->control('Display.program_type_id', [
                                                'label' => 'Program Type',
                                                'type' => 'checkbox',
                                                'checked' => true
                                            ]) ?>
                                        </div>
                                        <div class="large-2 columns">
                                            <?= $this->Form->control('Display.college_id', [
                                                'label' => 'College',
                                                'type' => 'checkbox'
                                            ]) ?>
                                        </div>
                                        <div class="large-2 columns">
                                            <?= $this->Form->control('Display.curriculum_id', [
                                                'label' => 'Specialization',
                                                'type' => 'checkbox'
                                            ]) ?>
                                        </div>
                                        <div class="large-2 columns">
                                            <?= $this->Form->control('Display.birthdate', [
                                                'label' => 'Birthdate',
                                                'type' => 'checkbox'
                                            ]) ?>
                                        </div>
                                        <div class="large-2 columns">
                                            <?= $this->Form->control('Display.is_disable', [
                                                'label' => 'Disabled',
                                                'type' => 'checkbox'
                                            ]) ?>
                                        </div>
                                        <div class="large-2 columns">
                                            <?= $this->Form->control('Display.city_id', [
                                                'label' => 'City',
                                                'type' => 'checkbox'
                                            ]) ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="large-2 columns">
                                            <?= $this->Form->control('Display.region_id', [
                                                'label' => 'Region',
                                                'type' => 'checkbox'
                                            ]) ?>
                                        </div>
                                        <div class="large-2 columns">
                                            <?= $this->Form->control('Display.department_id', [
                                                'label' => 'Department',
                                                'type' => 'checkbox',
                                                'checked' => $this->request->getData('Display.department_id', false)
                                            ]) ?>
                                        </div>
                                        <div class="large-2 columns">
                                            <?= $this->Form->control('Display.zone_id', [
                                                'label' => 'Zone',
                                                'type' => 'checkbox'
                                            ]) ?>
                                        </div>
                                        <div class="large-2 columns">
                                            <?= $this->Form->control('Display.woreda_id', [
                                                'label' => 'Woreda',
                                                'type' => 'checkbox'
                                            ]) ?>
                                        </div>
                                        <div class="large-2 columns">
                                            <?= $this->Form->control('Display.email', [
                                                'label' => 'Email',
                                                'type' => 'checkbox'
                                            ]) ?>
                                        </div>
                                        <div class="large-2 columns">
                                            <?= $this->Form->control('Display.phone_mobile', [
                                                'label' => 'Phone',
                                                'type' => 'checkbox'
                                            ]) ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="large-2 columns">
                                            <?= $this->Form->control('Display.student_national_id', [
                                                'label' => 'National ID',
                                                'type' => 'checkbox'
                                            ]) ?>
                                        </div>
                                        <div class="large-10 columns"> </div>
                                    </div>
                                </div>
                                <br>
                                <hr>
                                <?= $this->Form->button('Search', ['class' => 'tiny radius button bg-blue']) ?>
                            </fieldset>
                            <?= $this->Form->end() ?>
                        </div>
                        <hr>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="box-body">
        <div class="dataTables_wrapper">
            <?php if (!empty($students)): ?>
                <div style="overflow-x:auto;">
                    <table id="studentTableIndex" class="table" cellpadding="0" cellspacing="0">
                        <thead>
                        <tr>
                            <th class="center">#</th>
                            <?php if ($this->request->getData('Display') && $session->check('display_field_student')): ?>
                                <?php foreach ($this->request->getData('Display', []) as $dk => $dv): ?>
                                    <?php if ($dv == 1): ?>
                                        <th class="<?= $dk == 'full_name' ? 'vcenter' : 'center' ?>">
                                            <?= $this->Paginator->sort($dk, $fieldLabels[$dk] ?? $dk) ?>
                                        </th>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <th class="vcenter"><?= $this->Paginator->sort('full_name') ?></th>
                                <th class="center"><?= $this->Paginator->sort('gender', 'Sex') ?></th>
                                <th class="center"><?= $this->Paginator->sort('studentnumber') ?></th>
                                <th class="center"><?= $this->Paginator->sort('academicyear', 'Admission Year') ?></th>
                                <th class="center"><?= $this->Paginator->sort('Program') ?></th>
                                <th class="center"><?= $this->Paginator->sort('Program Type') ?></th>
                                <th class="center"><?= $this->Paginator->sort('Department') ?></th>
                            <?php endif; ?>
                            <th class="center">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $start = $this->Paginator->counter(['format' => '{{start}}']); ?>


                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td class="center"><?= $start++ ?></td>
                                <?php if ($this->request->getData('Display') && $session->check('display_field_student')): ?>
                                    <?php foreach ($this->request->getData('Display', []) as $dk => $dv): ?>
                                        <?php if ($dv == 1): ?>
                                            <?php if ($dk == 'full_name'): ?>
                                                <td class="vcenter"><?= h($student->full_name) ?></td>
                                            <?php elseif ($dk == 'program_type_id'): ?>
                                                <td class="center"><?= h($student->program_type->name ?? '') ?></td>
                                            <?php elseif ($dk == 'gender'): ?>
                                                <td class="center">
                                                    <?= h(strcasecmp($student->gender, 'male') == 0 ? 'M' : (strcasecmp($student->gender, 'female') == 0 ? 'F' : $student->gender)) ?>
                                                </td>
                                            <?php elseif ($dk == 'program_id'): ?>
                                                <td class="center"><?= h($student->program->name ?? '') ?></td>
                                            <?php elseif ($dk == 'college_id'): ?>
                                                <td class="center"><?= h($student->college->name ?? '') ?></td>
                                            <?php elseif ($dk == 'department_id'): ?>
                                                <td class="center">
                                                    <?= h($student->department->name ?? ($student->program_id == PROGRAM_REMEDIAL ? 'Remedial Program' : 'Pre/Freshman')) ?>
                                                </td>
                                            <?php elseif ($dk == 'region_id'): ?>
                                                <td class="center"><?= h($student->region->name ?? '') ?></td>
                                            <?php elseif ($dk == 'zone_id'): ?>
                                                <td class="center"><?= h($student->zone->name ?? '') ?></td>
                                            <?php elseif ($dk == 'woreda_id'): ?>
                                                <td class="center"><?= h($student->woreda->name ?? '') ?></td>
                                            <?php elseif ($dk == 'city_id'): ?>
                                                <td class="center"><?= h($student->city->name ?? '') ?></td>
                                            <?php elseif ($dk == 'specialization_id'): ?>
                                                <td class="center"><?= h($student->specialization->name ?? '') ?></td>
                                            <?php elseif ($dk == 'birthdate'): ?>
                                                <td class="center">
                                                    <?= $student->birthdate ? h($this->Time->format($student->birthdate, 'MMM d, Y')) : '' ?>
                                                </td>
                                            <?php elseif ($dk == 'curriculum_id'): ?>
                                                <td class="center"><?= h($student->curriculum->english_degree_nomenclature ?? '') ?></td>
                                            <?php else: ?>
                                                <td class="center"><?= h($student->$dk ?? '') ?></td>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <td class="vcenter"><?= h($student->full_name) ?></td>
                                    <td class="center">
                                        <?= h(strcasecmp($student->gender, 'male') == 0 ? 'M' : (strcasecmp($student->gender, 'female') == 0 ? 'F' : $student->gender)) ?>
                                    </td>
                                    <td class="center"><?= h($student->studentnumber) ?></td>
                                    <td class="center"><?= h($student->academicyear) ?></td>
                                    <td class="center"><?= h($student->program->name ?? '') ?></td>
                                    <td class="center"><?= h($student->program_type->name ?? '') ?></td>
                                    <td class="center">
                                        <?= h($student->department->name ?? ($student->program_id == PROGRAM_REMEDIAL ? 'Remedial Program' : 'Pre/Freshman')) ?>
                                    </td>
                                <?php endif; ?>
                                <td class="center">
                                    <?php if ($role_id != ROLE_STUDENT): ?>
                                        <?= $this->Html->link('', '#', [
                                            'class' => 'jsview fontello-eye',
                                            'title' => 'View',
                                            'data-animation' => 'fade',
                                            'data-reveal-id' => 'myModal',
                                            'data-reveal-ajax' => $this->Url->build(['action' => 'getStudentModal', $student->id])
                                        ]) ?>
                                    <?php endif; ?>
                                    <?php if (in_array($session->read('Auth.User.role_id'), [ROLE_REGISTRAR]) || ROLE_REGISTRAR == $session->read('Auth.User.Role.id')): ?>
                                        <?= $this->Html->link('', ['action' => 'edit', $student->id], [
                                            'class' => 'fontello-pencil',
                                            'title' => 'Edit Profile'
                                        ]) ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <br>
                <hr>
                <div class="row">
                    <div class="large-5 columns">

                        <?=$this->Paginator->counter([ 'format' => 'Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total' ]) ?>


                    </div>
                    <div class="large-7 columns">
                        <div class="pagination-centered">
                            <ul class="pagination">
                                <?= $this->Paginator->prev('<<', ['tag' => 'li'], null, ['class' => 'arrow disabled']) ?>
                                <?= $this->Paginator->numbers(['separator' => '', 'tag' => 'li']) ?>
                                <?= $this->Paginator->next('>>', ['tag' => 'li'], null, ['class' => 'arrow disabled']) ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    function toggleViewFullId(id) {
        const element = document.getElementById(id);
        const img = document.getElementById(id + 'Img');
        const txt = document.getElementById(id + 'Txt');

        if (element.style.display === 'none') {
            img.src = 'img/minus2.gif';
            txt.textContent = 'Hide Fields';
            element.style.display = 'block';
        } else {
            img.src = 'img/plus2.gif';
            txt.textContent = 'Adjust Fields';
            element.style.display = 'none';
        }
    }
</script>
