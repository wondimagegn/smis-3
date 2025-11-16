<?php
// File: templates/CourseAdds/approve_mass_add.php
use Cake\Core\Configure;
?>

<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;">
            <i class="fontello-check" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;">
                <?= __('Approve Mass Add for a Section') ?>
            </span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <?= $this->Form->create(null, ['id' => 'CourseAddForm']) ?>
                <div style="margin-top: -30px;">
                    <hr>
                    <blockquote>
                        <h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
                        <p style="text-align:justify;">
                            <span class="fs16">
                                This tool will help you to approve add courses which are published as a Mass Add by department. Mass Add should be only used when:
                            </span>
                        <ol class="fs14">
                            <li><b>The course is a block course.</b></li>
                            <li><b>To correct missed course from semester publication</b></li>
                            <li><b>Course must not be a Thesis/Project/Exit exam</b></li>
                        </ol>
                        <span class="fs16">
                                Mass Added courses are not considered as an add course rather
                                <span class="text-red"> they are part of semester courses.</span>
                            </span>
                        </p>
                    </blockquote>
                </div>
                <hr>
                <div onclick="toggleViewFullId('ListPublishedCourse')">
                    <?php if (!empty($organizedPublishedCourseBySection)) : ?>
                        <?= $this->Html->image('plus2.gif', ['id' => 'ListPublishedCourseImg']) ?>
                        <span style="font-size:10px; vertical-align:top; font-weight:bold"
                              id="ListPublishedCourseTxt">
                            Display Filter
                        </span>
                    <?php else : ?>
                        <?= $this->Html->image('minus2.gif', ['id' => 'ListPublishedCourseImg']) ?>
                        <span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt">
                            Hide Filter
                        </span>
                    <?php endif; ?>
                </div>
                <div id="ListPublishedCourse" style="display:<?=
                !empty($organizedPublishedCourseBySection) ? 'none' : 'block' ?>;">
                    <fieldset style="padding-bottom: 0px; padding-top: 15px;">
                        <div class="row">
                            <div class="large-3 columns">
                                <?= $this->Form->control('Student.academic_year', [
                                    'label' => 'Academic Year: ',
                                    'type' => 'select',
                                    'options' => $academicYearArrayData,
                                    'empty' => '[ Select ACY ]',
                                    'required' => true,
                                    'default' => $defaultacademicyear ?? '',
                                    'style' => 'width:90%;'
                                ]) ?>
                            </div>
                            <div class="large-3 columns">
                                <?= $this->Form->control('Student.semester', [
                                    'label' => 'Semester: ',
                                    'options' => Configure::read('semesters'),
                                    'required' => true,
                                    'empty' => '[ Select Semester ]',
                                    'style' => 'width:90%;'
                                ]) ?>
                            </div>
                            <div class="large-3 columns">
                                <?= $this->Form->control('Student.program_id', [
                                    'label' => 'Program: ',
                                    'type' => 'select',
                                    'options' => $programs,
                                    'empty' => '[ Select Program ]',
                                    'style' => 'width:90%;'
                                ]) ?>
                            </div>
                            <div class="large-3 columns">
                                <?= $this->Form->control('Student.program_type_id', [
                                    'label' => 'Program Type: ',
                                    'type' => 'select',
                                    'options' =>$programTypes,
                                     'required' => true,
                                    'empty' => '[ Select Program Type ]',
                                    'style' => 'width:90%;'
                                ]) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="large-6 columns">
                                <?= $this->Form->control('Student.department_id', [
                                    'label' => 'Department: ',
                                    'type' => 'select',
                                    'options' => $departments, // Adjust as needed
                                    'empty' => '[ Select Department ]',
                                    'required' => true,
                                    'style' => 'width:95%;'
                                ]) ?>
                            </div>
                            <div class="large-3 columns">
                                <?= $this->Form->control('Student.year_level_id', [
                                    'label' => 'Year Level: ',
                                    'type' => 'select',
                                    'options' =>$yearLevels, // Adjust as needed
                                    'required' => true,
                                    'empty' => '[ Select Year Level ]',
                                    'style' => 'width:90%;'
                                ]) ?>
                            </div>
                            <div class="large-3 columns"></div>
                        </div>
                        <hr>
                        <?= $this->Form->button('Search', [
                            'name' => 'getsection',
                            'id' => 'getsection',
                            'class' => 'tiny radius button bg-blue'
                        ]) ?>
                    </fieldset>
                </div>
                <hr>
                <div id="show_search_results">
                    <?php if (isset($organizedPublishedCourseBySection)
                        && !empty($organizedPublishedCourseBySection)) : ?>
                        <hr>
                        <h6 class="fs13 text-gray">Please select courses to approve as mass add</h6>
                        <h6 id="validation-message_non_selected" class="text-red fs14"></h6>
                        <br>
                        <?php
                        $display_button = 0;
                        $section_count = count($organizedPublishedCourseBySection);
                        foreach ($organizedPublishedCourseBySection as $section_id => $coursss) :
                            if (!empty($coursss)) :
                                $forTableHeader = reset($coursss);
                                ?>
                                <div style="overflow-x:auto;">
                                    <table id="fieldsForm" cellpadding="0" cellspacing="0" class="table">
                                        <thead>
                                        <tr>
                                            <td colspan="6" style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85); line-height: 1.5;">
                                                    <span style="font-size:16px;font-weight:bold; margin-top: 25px;">
                                                        <?= h($forTableHeader->section->name) .
                                                        ' (' . ($forTableHeader->section->
                                                        year_level->name ?? 'Pre/1st') . ', ' .
                                                        h($forTableHeader->section->academicyear) .
                                                        ')' ?>
                                                    </span>
                                                <br style="line-height: 0.35;">
                                                <span class="text-gray" style="padding-top: 13px; font-size: 13px; font-weight: bold">
                                                        <?= $forTableHeader->section->department->name ?? h($forTableHeader->section->college->name) . ' - Pre/Freshman' ?>
                                                        &nbsp; | &nbsp; <?= h($forTableHeader->section->program->name) ?>
                                                    </span>
                                                <span class="text-gray" style="padding-top: 14px; font-size: 13px; font-weight: normal">
                                                        <?php
                                                        $curriculum_name = '';
                                                        $credit_type = 'Credit';
                                                        if ($forTableHeader->section->curriculum && $forTableHeader->section->curriculum->name) {
                                                            $credit_type = strpos($forTableHeader->section->curriculum->type_credit, 'ECTS') !== false ? 'ECTS' : 'Credit';
                                                            $curriculum_name = h($forTableHeader->section->curriculum->name) . ' - ' . h($forTableHeader->section->curriculum->year_introduced) . ' (' . $credit_type . ')';
                                                        } elseif ($forTableHeader->course->curriculum && $forTableHeader->course->curriculum->name) {
                                                            $credit_type = strpos($forTableHeader->course->curriculum->type_credit, 'ECTS') !== false ? 'ECTS' : 'Credit';
                                                            $curriculum_name = h($forTableHeader->course->curriculum->name) . ' - ' . h($forTableHeader->course->curriculum->year_introduced) . ' (' . $credit_type . ')';
                                                        }
                                                        echo !empty($curriculum_name) ? '<b><i>Curriculum: </i></b><i class="text-gray fs13">' . $curriculum_name . '</i>' : '';
                                                        ?>
                                                    </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="center" style="width: 5%;">&nbsp;</th>
                                            <th class="center" style="width: 3%;">#</th>
                                            <th class="vcenter">Course Title</th>
                                            <th class="center">Course Code</th>
                                            <th class="center"><?= h($credit_type) ?></th>
                                            <th class="center">L T L</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $count = 1; foreach ($coursss as $vc) : ?>
                                            <tr>
                                                <td class="center">
                                                    <div style="margin-left: 30%;">
                                                        <?= $vc->course->thesis || $vc->course->exit_exam ? '**' :
                                                            $this->Form->checkbox("PublishedCourse.$section_id.{$vc->id}", [
                                                            'class' => 'listOfPublishedCourse',
                                                            'id' => "checkbox-$count"
                                                        ]) ?>
                                                    </div>
                                                </td>
                                                <td class="center"><?= $count ?></td>
                                                <td class="vcenter">
                                                    <?= h($vc->course->course_title) ?>
                                                    <?php if ($vc->course->thesis) : ?>
                                                        <span class="on-process">(Thesis/Project Course)</span>
                                                    <?php elseif ($vc->course->exit_exam) : ?>
                                                        <span class="on-process">(Exit Exam Course)</span>
                                                    <?php elseif ($vc->course->elective) : ?>
                                                        <span class="accepted">(Elective Course)</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="center"><?= h($vc->course->course_code) ?></td>
                                                <td class="center"><?= h($vc->course->credit) ?></td>
                                                <td class="center"><?= h($vc->course->lecture_hours) . '-' . h($vc->course->tutorial_hours) . '-' . h($vc->course->laboratory_hours) ?></td>
                                            </tr>
                                            <?php $count++; endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <br>
                            <?php else : ?>
                                <?php $display_button++; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <?php if ($display_button != $section_count) : ?>
                            <hr>
                            <?= $this->Form->button('Approve Mass Add for Selected', [
                                'name' => 'massadd',
                                'id' => 'addMassAdd',
                                'class' => 'tiny radius button bg-blue'
                            ]) ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>

<script type='text/javascript'>
    function toggleViewFullId(id) {
        const element = document.getElementById(id);
        const img = document.getElementById(id + 'Img');
        const txt = document.getElementById(id + 'Txt');
        if (element.style.display === 'none') {
            img.src = '/img/minus2.gif';
            txt.innerText = ' Hide Filter';
        } else {
            img.src = '/img/plus2.gif';
            txt.innerText = ' Display Filter';
        }
        element.style.display = element.style.display === 'none' ? 'block' : 'none';
    }

    document.getElementById('show_search_results').style.display = 'block';

    let search_button_clicked = false;
    document.getElementById('getsection').addEventListener('click', function(event) {
        let formIsValid = true;
        document.querySelectorAll(':input[required]').forEach(input => {
            if (input.value === '') {
                if (formIsValid) {
                    input.focus();
                    formIsValid = false;
                }
            }
        });

        document.querySelectorAll('input[type="checkbox"][name^="PublishedCourse"]').forEach(checkbox => {
            if (checkbox.checked) {
                checkbox.checked = false;
            }
        });

        document.getElementById('show_search_results').style.display = 'none';

        if (!formIsValid) {
            event.preventDefault();
            return false;
        }

        if (search_button_clicked) {
            alert('Looking for Mass Add requests, please wait a moment...');
            document.getElementById('getsection').disabled = true;
            return false;
        }

        if (!search_button_clicked && formIsValid) {
            document.getElementById('getsection').value = 'Looking for Mass Add requests...';
            document.getElementById('addMassAdd').disabled = true;
            search_button_clicked = true;
            return true;
        }

        search_button_clicked = false;
        return false;
    });

    let form_being_submitted = false;
    const validationMessageNonSelected = document.getElementById('validation-message_non_selected');
    document.getElementById('addMassAdd')?.addEventListener('click', function(event) {
        let isValid = true;
        let atLeastOneSelected = false;
        document.querySelectorAll('input[type="checkbox"][name^="PublishedCourse"]').forEach(checkbox => {
            if (checkbox.checked) {
                atLeastOneSelected = true;
            }
        });

        if (!atLeastOneSelected) {
            event.preventDefault();
            isValid = false;
            alert('Please select at least one course before submitting the form.');
            validationMessageNonSelected.innerHTML = 'Please select at least one course before submitting the form.';
            return false;
        }

        if (form_being_submitted) {
            alert('Approving Mass Add for Selected Courses, please wait a moment...');
            document.getElementById('addMassAdd').disabled = true;
            document.getElementById('getsection').disabled = true;
            return false;
        }

        if (!form_being_submitted && isValid) {
            document.getElementById('addMassAdd').value = 'Approving Mass Add for Selected Courses...';
            document.getElementById('getsection').disabled = true;
            form_being_submitted = true;
            return true;
        }

        return false;
    });

    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>
