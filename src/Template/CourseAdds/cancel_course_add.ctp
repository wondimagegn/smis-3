<?php
use Cake\Core\Configure;
use Cake\View\Helper\UrlHelper;
?>
<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;">
            <i class="fontello-check" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;">
                <?= __('Course Add Cancellation Interface') ?>
            </span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <div style="margin-top: -30px;"><hr></div>
                <?= $this->Form->create(null, ['url' => ['action' => 'cancelCourseAdd']]) ?>
                <blockquote>
                    <h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
                    <p style="text-align:justify;">
                        <span class="fs14 text-gray" style="font-weight: bold;">
                            This tool will help you to cancel student course add. If the selected students has course add for selected academic year and semester, it will display those course.
                            <strong class="text-red"><br><br>WARNING!! Deleting course adds which got exam grade will also delete the associated grades and assesment data of the student!, Before proceeding, please check that the student doesn't got any grade for a given course via student academic profile!!</strong>
                        </span>
                    </p>
                </blockquote>
                <hr>
                <div onclick="toggleViewFullId('ListSection')">
                    <?php if (!empty($publishedCourses)) : ?>
                        <?= $this->Html->image('plus2.gif', ['id' => 'ListSectionImg']) ?>
                        <span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListSectionTxt">Display Filter</span>
                    <?php else : ?>
                        <?= $this->Html->image('minus2.gif', ['id' => 'ListSectionImg']) ?>
                        <span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListSectionTxt">Hide Filter</span>
                    <?php endif; ?>
                </div>
                <div id="ListSection" style="display:<?= !empty($sections) ? 'none' : 'block' ?>">
                    <fieldset style="padding-bottom: 5px; padding-top: 15px;">
                        <div class="row">
                            <div class="large-3 columns">
                                <?= $this->Form->control('CourseAdd.academic_year', [
                                    'id' => 'AcadamicYear',
                                    'label' => 'Academic Year: ',
                                    'style' => 'width:90%',
                                    'options' => $acyearList,
                                    'default' => isset($academic_year_selected) ? $academic_year_selected : $defaultacademicyear
                                ]) ?>
                            </div>
                            <div class="large-3 columns">
                                <?= $this->Form->control('CourseAdd.semester', [
                                    'id' => 'Semester',
                                    'label' => 'Semester: ',
                                    'style' => 'width:90%',
                                    'options' => Configure::read('semesters')
                                ]) ?>
                                <?= isset($semester_selected) ? $this->Form->hidden('
                                CourseAdd.semester_selected', [
                                    'id' => 'SemesterSelected',
                                    'value' => $semester_selected
                                ]) : '' ?>
                            </div>
                            <div class="large-3 columns">
                                <?= $this->Form->control('CourseAdd.studentnumber', [
                                    'id' => 'StudentNumber',
                                    'class' => 'fs14',
                                    'label' => 'Student ID: ',
                                    'style' => 'width:90%',
                                    'type' => 'text',
                                    'placeholder' => 'Type Student ID...',
                                    'required' => true,
                                    'maxlength' => 25
                                ]) ?>
                            </div>
                            <div class="large-3 columns">
                                <?= $this->Form->control('CourseAdd.password', [
                                    'id' => 'Password',
                                    'style' => 'width:90%',
                                    'type' => 'password',
                                    'label' => 'Password: ',
                                    'placeholder' => 'Your Password here..',
                                    'required' => true
                                ]) ?>
                            </div>
                        </div>
                        <hr>
                        <?= $this->Form->button(__('Get Courses'), [
                            'name' => 'listAddedCourses',
                            'value'=>'listAddedCourses',
                            'id' => 'listAddedCourses',
                            'class' => 'tiny radius button bg-blue'
                        ]) ?>

                    </fieldset>
                </div>

                <hr>
                <!-- AJAX STUDENT PROFILE LOADING -->
                <div id="dialog-modal" title="Academic Profile"></div>
                <!-- END AJAX STUDENT PROFILE LOADING -->
                <div id="manage_main_data">
                    <?= !empty($studentAcademicProfile) ? '<h6 class="fs14 text-gray">' .
                        $studentAcademicProfile['BasicInfo']['Student']['first_name'] . ' ' .
                        $studentAcademicProfile['BasicInfo']['Student']['middle_name'] . ' ' .
                        $studentAcademicProfile['BasicInfo']['Student']['last_name'] . ' (' .
                        $studentAcademicProfile['BasicInfo']['Department']['name'] . ')' .
                        '</h6>' : '' ?>
                    <?= !empty($studentAcademicProfile) ? $this->Html->link(
                        '[ Open Student Academic Profile ]',
                        '#',
                        [
                            'class' => 'jsview',
                            'data-animation' => 'fade',
                            'data-reveal-id' => 'myModal',
                            'data-reveal-ajax' => '/students/get_modal_box/' .
                                $studentAcademicProfile['BasicInfo']['Student']['id']
                        ]
                    ) : '' ?>
                    <?php if (empty($publishedCourses) && !empty(
                        $this->request->getData('listAddedCourses'))) : ?>
                        <div class='info-box info-message' style="font-family: 'Times New Roman',
                         Times, serif; font-weight: bold;">
                            <span style='margin-right: 15px;'></span>
                            There is no course add found for <?= !empty($studentAcademicProfile)
                                ? $studentAcademicProfile['BasicInfo']['Student']['first_name'] . ' ' .
                                $studentAcademicProfile['BasicInfo']['Student']['middle_name'] . ' ' .
                                $studentAcademicProfile['BasicInfo']['Student']['last_name'] . ' (' .
                                $studentAcademicProfile['BasicInfo']['Student']['studentnumber'] . ') '
                                : $this->request->getData('CourseAdd.studentnumber') ?>
                            <?= !empty($this->request->getData('CourseAdd.acadamic_year'))
                                ? ' in ' . $this->request->getData('CourseAdd.acadamic_year') . '
                                academic year ' .
                                (!empty($this->request->getData('CourseAdd.semester'))
                                    ? ', semester: ' . $this->request->getData('CourseAdd.semester')
                                    : '')
                                : ' in the given criteria.' ?>
                        </div>
                    <?php elseif (!empty($publishedCourses) && !empty($publishedCourses)) : ?>

                        <hr>
                        <h6 class="fs13 text-gray">Please Select Course(s)</h6>
                        <h6 id="validation-message_non_selected" class="text-red fs14"></h6>
                        <br>
                        <div style="overflow-x:auto;">
                            <table cellpadding="0" cellspacing="0" class="table">
                                <thead>
                                <tr>
                                    <td class="center" style="width:5%;">
                                        <?= $this->Form->control('select_all', [
                                            'type' => 'checkbox',
                                            'name' => 'select-all',
                                            'id' => 'select-all',
                                            'label' => false
                                        ]) ?>
                                    </td>
                                    <td class="vcenter" style="width:40%;">Course Title</td>
                                    <td class="center" style="width:15%;">Course Code</td>
                                    <td class="center" style="width:10%;">
                                        <?= strpos($studentAcademicProfile['Curriculum']
                                        ['type_credit'], 'ECTS') !== false ? 'ECTS' :
                                            'Credit' ?>
                                    </td>
                                    <td class="center" style="width:15%;">ACY/SEM</td>
                                    <td class="center" style="width:15%;">Have Grade?</td>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $st_count = 0; ?>
                                <?php foreach ($publishedCourses as $key => $course) : ?>
                                    <tr>
                                        <td class="center">
                                            <div style="margin-left: 10%;">
                                                <?= $this->Form->control('CourseAdd.' .
                                                    $st_count . '.gp', [
                                                    'type' => 'checkbox',
                                                    'class' => 'checkbox1',
                                                    'label' => false,
                                                    'id' => 'StudentSelection' . $st_count
                                                ]) ?>
                                            </div>
                                            <?= $this->Form->hidden('CourseAdd.' . $st_count . '.student_id', [
                                                'value' =>
                                                    $studentAcademicProfile['BasicInfo']['Student']['id']
                                            ]) ?>
                                            <?= $this->Form->hidden('CourseAdd.' . $st_count . '.id', [
                                                'value' => $course->id
                                            ]) ?>
                                            <?= $this->Form->hidden('CourseAdd.' . $st_count . '.published_course_id', [
                                                'value' => $course->published_course_id
                                            ]) ?>
                                        </td>
                                        <td class="vcenter"><?= $course->published_course->course->course_title ?></td>
                                        <td class="center"><?= $course->published_course->course->course_code ?></td>
                                        <td class="center"><?= $course->published_course->course->credit ?></td>
                                        <td class="center"><?=  $course->published_course->academic_year .
                                            ' / ' .  $course->published_course->semester ?></td>
                                        <td class="center">
                                            <?= !empty($course->exam_grades[0]) &&
                                            !empty($course->exam_grades[0])
                                                ? 'Yes (' . $course->exam_grades[0]['grade'] .
                                                (!empty($course->exam_grades[0]->exam_grade_change[0])
                                                &&
                                                !empty($course->exam_grades[0]->exam_grade_change[0]['grade'])
                                                    ? ' => ' . $course->exam_grades[0]->exam_grade_change[0]['grade']
                                                    : '') . ')'
                                                : 'No' ?>
                                        </td>
                                    </tr>
                                    <?php $st_count++; ?>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <hr>
                        <?php
echo $this->Form->button(__('Cancel Add & Delete Grade'), [
                            'name' => 'deleteGrade',
                            'id' => 'cancelNGandDeleteGrade',
                            'class' => 'tiny radius button bg-blue'
                        ]);
?>

                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->Form->end() ?>
<script type="text/javascript">
    function toggleView(obj) {
        if ($('#c' + obj.id).css("display") == 'none') {
            $('#i' + obj.id).attr("src", '/img/minus2.gif');
        } else {
            $('#i' + obj.id).attr("src", '/img/plus2.gif');
        }
        $('#c' + obj.id).toggle("slow");
    }

    function toggleViewFullId(id) {
        if ($('#' + id).css("display") == 'none') {
            $('#' + id + 'Img').attr("src", '/img/plus2.gif');
            $('#' + id + 'Txt').empty();
            $('#' + id + 'Txt').append('Hide Filter');
        } else {
            $('#' + id + 'Img').attr("src", '/img/minus2.gif');
            $('#' + id + 'Txt').empty();
            $('#' + id + 'Txt').append('Display Filter');
        }
        $('#' + id).toggle("slow");
    }

    const validationMessageNonSelected = document.getElementById('validation-message_non_selected');
    var form_being_submitted = false;

    $(document).ready(function() {
        $("#manage_main_data").show();

        $('#cancelNGandDeleteGrade').click(function() {
            var checkboxes = document.querySelectorAll('input[type="checkbox"]');
            var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);
            var isValid = true;

            if (!checkedOne) {
                alert('At least one Course Add must be selected to cancel!');
                validationMessageNonSelected.innerHTML = 'At least one Course Add must be selected to cancel!';
                isValid = false;
                return false;
            }

            if (form_being_submitted) {
                alert('Canceling Add and Deleting Grade. please wait a moment...');
                $('#cancelNGandDeleteGrade').attr('disabled', true);
                isValid = false;
                return false;
            }

            var confirmm = confirm('Are you sure you want to cancel the selected course adds? Canceling the selected course adds will permanently delete exixting course grades and assesment if any, and it is not recoverable! Are you sure you want to proceed?');

            if (!form_being_submitted && isValid && confirmm) {
                $('#cancelNGandDeleteGrade').val('Canceling Add and Deleting Grade...');
                form_being_submitted = true;
                isValid = true;
                return true;
            } else {
                return false;
            }
        });

        $('#listAddedCourses').click(function() {
            $("#manage_main_data").hide();
            $("#listAddedCourses").val('Fetching Courses...');
        });
    });

    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>
