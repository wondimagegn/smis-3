<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;">
            <i class="fontello-check" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('NG Grade Management'); ?></span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <?= $this->Form->create(null, [
                    'id' => 'examGradeForm',
                    'novalidate' => true,
                    'url' => [
                        'controller' => 'ExamGrades',
                        'action' => 'manageNg'
                    ]
                ]); ?>

                <?= $this->element('publish_course_filter_by_dept'); ?>

                <div id="manage_ng_form">
                    <?php if (!empty($students_with_ng)) { ?>
                        <hr>
                        <table cellpadding="0" cellspacing="0" class="table">
                            <tr>
                                <td class="center">
                                    <div class="row">
                                        <div class="large-3 columns">
                                            <br>
                                            Minute Number:
                                        </div>
                                        <div class="large-6 columns">
                                            <br>
                                            <?= $this->Form->control('minute_number', [
                                                'id' => 'minuteNumber',
                                                'required' => true,
                                                'style' => 'width: 70%',
                                                'label' => false
                                            ]); ?>
                                        </div>
                                        <div class="large-3 columns">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <br>

                        <h6 id="validation-message_non_selected" class="text-red fs14"></h6>
                        <br>

                        <div style="overflow-x:auto;">
                            <table cellpadding="0" cellspacing="0" class="table">
                                <thead>
                                <tr>
                                    <td class="center">#</td>
                                    <td class="vcenter">Full Name</td>
                                    <td class="center">Student ID</td>
                                    <td class="center">Sex</td>
                                    <td class="center">Current Grade</td>
                                    <td class="center">New Grade</td>
                                    <td class="center">Is Cheating</td>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $count = 0;
                                foreach ($students_with_ng as $key => $student) {
                                    $count++;
                                    ?>
                                    <tr>
                                        <td class="center"><?= $count; ?></td>
                                        <td class="vcenter">
                                            <?= h($student['full_name']); ?>
                                            <?= isset($student['haveAssesmentData']) && !$student['haveAssesmentData'] ? '<br><span class="text-gray fs12"><i>(Empty assessment data, you may want to cancel it instead)</i></span>' : ''; ?>
                                        </td>
                                        <td class="center"><?= h($student['studentnumber']); ?></td>
                                        <td class="center"><?= strcasecmp(trim($student['gender']), 'male') === 0 ? 'M' : (strcasecmp(trim($student['gender']), 'female') === 0 ? 'F' : ''); ?></td>
                                        <td class="center"><?= isset($student['grade']) ? h($student['grade']) : 'NG'; ?></td>
                                        <td class="center">
                                            <br>
                                            <?= $this->Form->control("ExamGrade.{$count}.id", [
                                                'value' => $student['grade_id'],
                                                'type' => 'hidden',
                                                'label' => false
                                            ]); ?>
                                            <?= $this->Form->control("ExamGrade.{$count}.grade_id", [
                                                'value' => $student['grade_id'],
                                                'type' => 'hidden',
                                                'label' => false
                                            ]); ?>
                                            <?= $this->Form->control("ExamGrade.{$count}.grade", [
                                                'type' => 'select',
                                                'options' => $applicable_grades,
                                                'label' => false
                                            ]); ?>
                                        </td>
                                        <td class="center">
                                            <div style="margin-left: 45%;">
                                                <?= $this->Form->control("ExamGrade.{$count}.cheating", [
                                                    'type' => 'checkbox',
                                                    'label' => false
                                                ]); ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <hr>

                        <?= $this->Form->button(__('Change NG Grade'), [
                            'name' => 'changeNgGrade',
                            'id' => 'changeNgGrade',
                            'class' => 'tiny radius button bg-blue'
                        ]); ?>
                    <?php } ?>
                </div>

                <?= $this->Form->end(); ?>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $("#PublishedCourse").change(function() {
            // Redirect to the correct URL using CakePHP's UrlHelper
            window.location.replace(
                '<?= $this->Url->build(['controller' => 'ExamGrades', 'action' => 'manageNg']); ?>/' + $("#PublishedCourse").val()
            );

            $("#manage_ng_form").hide();

            if ($("#minuteNumber").length) {
                $("#minuteNumber").val('');
            }

            if ($("#changeNgGrade").length) {
                $("#changeNgGrade").attr('disabled', true);
            }

            $('select[name^="ExamGrade"]').each(function() {
                const namePattern = /ExamGrade\[\d+\]\[grade\]/;
                if (namePattern.test($(this).attr('name')) && $(this).val()) {
                    $(this).val('');
                }
            });
        });

        $("#manage_ng_form").show();
    });

    var form_being_submitted = false;
    const validationMessageNonSelected = document.getElementById('validation-message_non_selected');

    $('#changeNgGrade').click(function(event) {
        $('form').removeAttr('novalidate');

        var isValid = true;
        var minuteNumber = $('#minuteNumber').val();

        if (minuteNumber === '') {
            event.preventDefault();
            $('#minuteNumber').focus();
            isValid = false;
            return false;
        }

        let atLeastOneSelected = false;
        $('select[name^="ExamGrade"]').each(function() {
            const namePattern = /ExamGrade\[\d+\]\[grade\]/;
            if (namePattern.test($(this).attr('name')) && $(this).val()) {
                atLeastOneSelected = true;
                return false;
            }
        });

        if (!atLeastOneSelected) {
            event.preventDefault();
            isValid = false;
            alert('Please select at least one student grade before submitting the form.');
            validationMessageNonSelected.innerHTML = 'Please select at least one student grade before submitting the form.';
        }

        $('form').attr('novalidate', 'novalidate');

        if (form_being_submitted) {
            alert("Managing NG grade for the selected students, please wait a moment...");
            $('#changeNgGrade').attr('disabled', true);
            return false;
        }

        if (!form_being_submitted && isValid) {
            $('#changeNgGrade').val('Managing NG Grade...');
            if ($("#listPublishedCourses").length) {
                $("#listPublishedCourses").attr('disabled', true);
            }
            $("#PublishedCourse").attr('disabled', true);
            form_being_submitted = true;
            return true;
        } else {
            return false;
        }
    });

    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>
