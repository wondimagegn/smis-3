<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;">
            <i class="fas fa-list" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Student List with Incomplete Profile Information') ?></span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <?php
                // Migrate session check
                if ($this->getRequest()->getSession()->check('display_field_student')) {
                    $this->setRequest($this->getRequest()->withData('Display', $this->getRequest()->getSession()->read('display_field_student')));
                }
                ?>
                <?= $this->Form->create(null, ['url' => ['controller' => 'Students', 'action' => 'search_profile']]) ?>
                <?php if ($role_id != ROLE_STUDENT) { ?>
                    <div style="margin-top: -30px;">
                        <hr>
                        <hr>
                        <div onclick="toggleViewFullId('ListPublishedCourse')">
                            <?php if (!empty($turn_off_search)) {
                                echo $this->Html->image('/img/plus2.gif', ['id' => 'ListPublishedCourseImg']);
                                ?>
                                <span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt"> Display Filter</span>
                            <?php } else {
                                echo $this->Html->image('/img/minus2.gif', ['id' => 'ListPublishedCourseImg']);
                                ?>
                                <span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt"> Hide Filter</span>
                            <?php } ?>
                        </div>
                        <div id="ListPublishedCourse" style="display:<?= (!empty($turn_off_search) ? 'none' : 'block') ?>;">
                            <fieldset style="padding-bottom: 0px;padding-top: 15px;">
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
                                        <?php if (isset($colleges) && !empty($colleges)) {
                                            echo $this->Form->control('Search.college_id', [
                                                'label' => 'College: ',
                                                'style' => 'width:95%',
                                                'empty' => 'All Assigned Colleges'
                                            ]);
                                        } elseif (isset($departments) && !empty($departments)) {
                                            echo $this->Form->control('Search.department_id', [
                                                'label' => 'Department: ',
                                                'style' => 'width:90%',
                                                'empty' => 'All Assigned Departments'
                                            ]);
                                        } ?>
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
                                            'default' => $name,
                                            'style' => 'width:90%'
                                        ]) ?>
                                    </div>
                                    <div class="large-2 columns">
                                        <?= $this->Form->control('Search.limit', [
                                            'id' => 'limit',
                                            'value' => $this->getRequest()->getData('Search.limit', $limit),
                                            'type' => 'number',
                                            'min' => 0,
                                            'max' => 5000,
                                            'step' => 100,
                                            'label' => 'Limit: ',
                                            'style' => 'width:90%'
                                        ]) ?>
                                        <?= $this->Form->hidden('page', ['value' => $this->getRequest()->getData('Search.page')]) ?>
                                        <?= $this->Form->hidden('sort', ['value' => $this->getRequest()->getData('Search.sort')]) ?>
                                        <?= $this->Form->hidden('direction', ['value' => $this->getRequest()->getData('Search.direction')]) ?>
                                    </div>
                                </div>
                                <?php
                                $userRoleId = $this->getRequest()->getSession()->read('Auth.User.role_id') ?? null;
                                $userParentRoleId = $this->getRequest()->getSession()->read('Auth.User.Role.parent_id') ?? null;
                                if (isset($departments) && !empty($departments) && !in_array($userRoleId, [ROLE_STUDENT, ROLE_REGISTRAR, ROLE_COLLEGE, ROLE_DEPARTMENT])) { ?>
                                    <div class="row">
                                        <div class="large-6 columns">
                                            <?= $this->Form->control('Search.department_id', [
                                                'label' => 'Department: ',
                                                'style' => 'width:90%',
                                                'empty' => 'All Departments'
                                            ]) ?>
                                        </div>
                                        <div class="large-6 columns"></div>
                                    </div>
                                <?php } ?>
                                <hr>
                                <div class="large-12 columns">
                                    <div onclick="toggleViewFullId('ListStudents')">
                                        <?php if (!empty($students)) {
                                            echo $this->Html->image('/img/plus2.gif', ['id' => 'ListStudentsImg']);
                                            ?>
                                            <span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListStudentsTxt"> Adjust Fields</span>
                                        <?php } else {
                                            echo $this->Html->image('/img/minus2.gif', ['id' => 'ListStudentsImg']);
                                            ?>
                                            <span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListStudentsTxt"> Hide Fields</span>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="large-12 columns" id="ListStudents" style="display:<?= (!empty($students) ? 'none' : 'block') ?>;">
                                    <div class="row">
                                        <div class="large-12 columns">&nbsp;</div>
                                    </div>
                                    <div class="row">
                                        <div class="large-2 columns">
                                            <?= $this->Form->control('Display.full_name', ['label' => 'Full Name', 'type' => 'checkbox', 'checked' => true]) ?>
                                        </div>
                                        <div class="large-2 columns">
                                            <?= $this->Form->control('Display.full_am_name', ['label' => 'Amharic Name', 'type' => 'checkbox']) ?>
                                        </div>
                                        <div class="large-2 columns">
                                            <?= $this->Form->control('Display.gender', ['label' => 'Sex', 'type' => 'checkbox', 'checked' => true]) ?>
                                        </div>
                                        <div class="large-2 columns">
                                            <?= $this->Form->control('Display.studentnumber', ['label' => 'Student ID', 'type' => 'checkbox', 'checked' => true]) ?>
                                        </div>
                                        <div class="large-2 columns">
                                            <?= $this->Form->control('Display.academicyear', ['label' => 'Admission Year', 'type' => 'checkbox']) ?>
                                        </div>
                                        <div class="large-2 columns">
                                            <?= $this->Form->control('Display.program_id', ['label' => 'Program', 'type' => 'checkbox', 'checked' => true]) ?>
                                        </div>
                                        <div class="large-2 columns">
                                            <?= $this->Form->control('Display.program_type_id', ['label' => 'Program Type', 'type' => 'checkbox', 'checked' => true]) ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="large-2 columns">
                                            <?= $this->Form->control('Display.college_id', ['label' => 'College', 'type' => 'checkbox']) ?>
                                        </div>
                                        <div class="large-2 columns">
                                            <?= $this->Form->control('Display.curriculum_id', ['label' => 'Specialization', 'type' => 'checkbox']) ?>
                                        </div>
                                        <div class="large-2 columns">
                                            <?= $this->Form->control('Display.birthdate', ['label' => 'Birthdate', 'type' => 'checkbox']) ?>
                                        </div>
                                        <div class="large-2 columns">
                                            <?= $this->Form->control('Display.is_disable', ['label' => 'Disabled', 'type' => 'checkbox']) ?>
                                        </div>
                                        <div class="large-2 columns">
                                            <?= $this->Form->control('Display.city_id', ['label' => 'City', 'type' => 'checkbox']) ?>
                                        </div>
                                        <div class="large-2 columns">
                                            <?= $this->Form->control('Display.region_id', ['label' => 'Region', 'type' => 'checkbox']) ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="large-2 columns">
                                            <?= $this->Form->control('Display.department_id', [
                                                'label' => 'Department',
                                                'type' => 'checkbox',
                                                'checked' => $this->getRequest()->getData('Display.department_id', false)
                                            ]) ?>
                                        </div>
                                        <div class="large-2 columns">
                                            <?= $this->Form->control('Display.zone_id', ['label' => 'Zone', 'type' => 'checkbox']) ?>
                                        </div>
                                        <div class="large-2 columns">
                                            <?= $this->Form->control('Display.woreda_id', ['label' => 'Woreda', 'type' => 'checkbox']) ?>
                                        </div>
                                        <div class="large-2 columns">
                                            <?= $this->Form->control('Display.email', ['label' => 'Email', 'type' => 'checkbox']) ?>
                                        </div>
                                        <div class="large-2 columns">
                                            <?= $this->Form->control('Display.phone_mobile', ['label' => 'Phone', 'type' => 'checkbox']) ?>
                                        </div>
                                        <div class="large-2 columns">
                                            <?= $this->Form->control('Display.student_national_id', ['label' => 'National ID', 'type' => 'checkbox']) ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="large-12 columns">&nbsp;</div>
                                    </div>
                                </div>
                                <br>
                                <hr>
                                <?= $this->Form->button('Search', ['class' => 'tiny radius button bg-blue']) ?>
                            </fieldset>
                        </div>
                        <hr>
                    </div>
                <?php } ?>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
    <div class="box-body">
        <div class="dataTables_wrapper">
            <?php if (!empty($students)) { ?>
                <div style="overflow-x:auto;">
                    <table id="studentTableIndex" cellpadding="0" cellspacing="0" class="table">
                        <thead>
                        <tr>
                            <td class="center">#</td>
                            <?php
                            $displayFields = $this->getRequest()->getData('Display', $this->getRequest()->getSession()->read('display_field_student', []));
                            if (!empty($displayFields)) {
                                foreach ($displayFields as $dk => $dv) {
                                    if ($dv == 1) {
                                        echo $dk == 'full_name' ? '<td class="vcenter">' : '<td class="center">';
                                        if ($dk == 'gender') {
                                            echo $this->Paginator->sort('gender', 'Sex') . '</td>';
                                        } elseif ($dk == 'department_id') {
                                            echo $this->Paginator->sort('department_id', 'Department') . '</td>';
                                        } elseif ($dk == 'academicyear') {
                                            echo $this->Paginator->sort('academicyear', 'Admission Year') . '</td>';
                                        } elseif ($dk == 'studentnumber') {
                                            echo $this->Paginator->sort('studentnumber', 'Student ID') . '</td>';
                                        } elseif ($dk == 'student_national_id') {
                                            echo $this->Paginator->sort('student_national_id', 'National ID') . '</td>';
                                        } else {
                                            echo $this->Paginator->sort($dk) . '</td>';
                                        }
                                    }
                                }
                            } else {
                                ?>
                                <td class="vcenter"><?= $this->Paginator->sort('full_name') ?></td>
                                <td class="center"><?= $this->Paginator->sort('gender', 'Sex') ?></td>
                                <td class="center"><?= $this->Paginator->sort('studentnumber') ?></td>
                                <td class="center"><?= $this->Paginator->sort('academicyear', 'Admission Year') ?></td>
                                <td class="center"><?= $this->Paginator->sort('program_id', 'Program') ?></td>
                                <td class="center"><?= $this->Paginator->sort('program_type_id', 'Program Type') ?></td>
                                <td class="center"><?= $this->Paginator->sort('department_id', 'Department') ?></td>
                            <?php } ?>
                            <td class="center">Actions</td>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $start = $this->Paginator->counter(['format' => '%start%']);
                        foreach ($students as $student) { ?>
                            <tr>
                                <td class="center"><?= $start++ ?></td>
                                <?php
                                if (!empty($displayFields)) {
                                    foreach ($displayFields as $dk => $dv) {
                                        if ($dv == 1) {
                                            if ($dk == 'full_name') {
                                                echo '<td class="vcenter">' . h($student['full_name']) . '</td>';
                                            } elseif ($dk == 'program_type_id') {
                                                echo '<td class="center">' . h($student['program_type']['name'] ?? '') . '</td>';
                                            } elseif ($dk == 'gender') {
                                                echo '<td class="center">' . (strcasecmp(trim($student['gender'] ?? ''), 'male') == 0 ? 'M' : (strcasecmp(trim($student['gender'] ?? ''), 'female') == 0 ? 'F' : h($student['gender'] ?? ''))) . '</td>';
                                            } elseif ($dk == 'program_id') {
                                                echo '<td class="center">' . h($student['program']['name'] ?? '') . '</td>';
                                            } elseif ($dk == 'college_id') {
                                                echo '<td class="center">' . h($student['college']['name'] ?? '') . '</td>';
                                            } elseif ($dk == 'department_id') {
                                                echo '<td class="center">' . (!empty($student['department']['name']) ? h($student['department']['name']) : ($student['program_id'] == \Cake\Core\Configure::read('PROGRAM_REMEDIAL') ? 'Remedial Program' : 'Pre/Freshman')) . '</td>';
                                            } elseif ($dk == 'region_id') {
                                                echo '<td class="center">' . h($student['region']['name'] ?? '') . '</td>';
                                            } elseif ($dk == 'zone_id') {
                                                echo '<td class="center">' . h($student['zone']['name'] ?? '') . '</td>';
                                            } elseif ($dk == 'woreda_id') {
                                                echo '<td class="center">' . h($student['woreda']['name'] ?? '') . '</td>';
                                            } elseif ($dk == 'city_id') {
                                                echo '<td class="center">' . h($student['city']['name'] ?? '') . '</td>';
                                            } elseif ($dk == 'curriculum_id') {
                                                echo '<td class="center">' . h($student['curriculum']['english_degree_nomenclature'] ?? '') . '</td>';
                                            } elseif ($dk == 'birthdate') {
                                                echo '<td class="center">' . (!empty($student['birthdate']) ? h((new \Cake\I18n\FrozenTime($student['birthdate']))->format('M j, Y')) : '') . '</td>';
                                            } else {
                                                echo '<td class="center">' . h($student[$dk] ?? '') . '</td>';
                                            }
                                        }
                                    }
                                } else {
                                    ?>
                                    <td class="vcenter"><?= h($student['full_name']) ?></td>
                                    <td class="center"><?= (strcasecmp(trim($student['gender'] ?? ''), 'male') == 0 ? 'M' : (strcasecmp(trim($student['gender'] ?? ''), 'female') == 0 ? 'F' : h($student['gender'] ?? ''))) ?></td>
                                    <td class="center"><?= h($student['studentnumber']) ?></td>
                                    <td class="center"><?= h($student['academicyear']) ?></td>
                                    <td class="center"><?= h($student['program']['name'] ?? '') ?></td>
                                    <td class="center"><?= h($student['program_type']['name'] ?? '') ?></td>
                                    <td class="center"><?= (!empty($student['department']['name']) ? h($student['department']['name']) : ($student['program_id'] == \Cake\Core\Configure::read('PROGRAM_REMEDIAL') ? 'Remedial Program' : 'Pre/Freshman')) ?></td>
                                <?php } ?>
                                <td class="center">
                                    <?php
                                    $userRoleId = $this->getRequest()->getSession()->read('Auth.User.role_id') ?? null;
                                    $userParentRoleId = $this->getRequest()->getSession()->read('Auth.User.Role.parent_id') ?? null;
                                    // View link (commented out in original)
                                    // if ($role_id != ROLE_STUDENT) {
                                    //     echo $this->Html->link('', ['action' => 'get_modal_box', $student['id']], [
                                    //         'class' => 'fas fa-eye',
                                    //         'title' => 'View',
                                    //         'data-animation' => 'fade',
                                    //         'data-reveal-id' => 'myModal',
                                    //         'data-reveal-ajax' => $this->Url->build(['action' => 'get_modal_box', $student['id']])
                                    //     ]);
                                    // }
                                    ?>
                                    &nbsp;
                                    <?= ($userRoleId == ROLE_REGISTRAR || $userParentRoleId == ROLE_REGISTRAR ? $this->Html->link(__(''), ['action' => 'edit', $student['id']], ['class' => 'fas fa-pencil-alt', 'title' => 'Edit Profile']) : '') ?>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
                <br>
                <hr>
                <div class="row">
                    <div class="large-5 columns">
                        <?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} records out of {{count}} total')]) ?>
                    </div>
                    <div class="large-7 columns">
                        <div class="pagination-centered">
                            <ul class="pagination">
                                <?= $this->Paginator->prev('<< ' . __('Previous'), ['tag' => 'li', 'disabledTag' => 'li', 'class' => 'page-item', 'disabledClass' => 'disabled']) ?>
                                <?= $this->Paginator->numbers(['tag' => 'li', 'currentTag' => 'li', 'currentClass' => 'active']) ?>
                                <?= $this->Paginator->next(__('Next') . ' >>', ['tag' => 'li', 'disabledTag' => 'li', 'class' => 'page-item', 'disabledClass' => 'disabled']) ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<script>
    function toggleViewFullId(id) {
        if ($('#' + id).css("display") == 'none') {
            $('#' + id + 'Img').attr("src", '<?= $this->Url->build('/img/minus2.gif') ?>');
            $('#' + id + 'Txt').empty();
            $('#' + id + 'Txt').append(' Hide Fields');
        } else {
            $('#' + id + 'Img').attr("src", '<?= $this->Url->build('/img/plus2.gif') ?>');
            $('#' + id + 'Txt').empty();
            $('#' + id + 'Txt').append(' Adjust Fields');
        }
        $('#' + id).toggle("slow");
    }
</script>
