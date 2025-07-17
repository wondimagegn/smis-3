<div style="margin-top: -30px;">
    <hr>
    <div onclick="toggleViewFullId('ListPublishedCourse')">
        <?php
        // Check the current action
        if (in_array($this->request->getParam('action'), ['manage_ng', 'cancel_ng_grade', 'manage_fx'])) {
            if (isset($previous_academicyear) && !empty($previous_academicyear)) {
                $defaultacademicyear = $previous_academicyear;
            }
        }

        if (!empty($publishedCourses)) {
            echo $this->Html->image('/img/plus2.gif', ['id' => 'ListPublishedCourseImg']);
            ?>
            <span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt"> Display Filter</span>
            <?php
        } else {
            echo $this->Html->image('/img/minus2.gif', ['id' => 'ListPublishedCourseImg']);
            ?>
            <span style="font-size:10px; vertical-align:top; font-weight:bold" id="ListPublishedCourseTxt"> Hide Filter</span>
            <?php
        }
        ?>
    </div>

    <div id="ListPublishedCourse" style="display:<?= !empty($publishedCourses) ? 'none' : 'block'; ?>">
        <fieldset style="padding-bottom: 0px; padding-top: 15px;">
            <div class="row">
                <div class="large-3 columns">
                    <?= $this->Form->control('acadamic_year', [
                        'id' => 'AcadamicYear',
                        'label' => 'Acadamic Year: ',
                        'class' => 'fs14',
                        'style' => 'width:90%',
                        'type' => 'select',
                        'options' => $acyearArrayData,
                        'default' => isset($academicYearSelected) ? $academicYearSelected : $defaultAcademicYear
                    ]); ?>
                </div>
                <div class="large-3 columns">
                    <?= $this->Form->control('semester', [
                        'id' => 'Semester',
                        'class' => 'fs14',
                        'type' => 'select',
                        'style' => 'width:90%',
                        'label' => 'Semester: ',
                        'options' => \Cake\Core\Configure::read('semesters'),
                        'required' => true,
                        'default' => isset($semester_selected) ? $semester_selected : ''
                    ]); ?>
                </div>
                <div class="large-3 columns">
                    <?= $this->Form->control('program_id', [
                        'id' => 'Program',
                        'class' => 'fs14',
                        'label' => 'Program: ',
                        'style' => 'width:90%',
                        'type' => 'select',
                        'options' => $programs,
                        'required' => true,
                        'default' => isset($program_id) ? $program_id : ''
                    ]); ?>
                </div>
                <div class="large-3 columns">
                    <?= $this->Form->control('program_type_id', [
                        'id' => 'ProgramType',
                        'class' => 'fs14',
                        'label' => 'Program Type: ',
                        'style' => 'width:90%',
                        'type' => 'select',
                        'options' => $program_types,
                        'required' => true,
                        'default' => isset($program_type_id) ? $program_type_id : ''
                    ]); ?>
                </div>
            </div>
            <div class="row">
                <div class="large-6 columns">
                    <?php if (!(isset($departments[0]) && $departments[0] == 0)) { ?>
                        <?= $this->Form->control('department_id', [
                            'id' => 'DepartmentId',
                            'class' => 'fs14',
                            'label' => isset($only_pre_assigned) && $only_pre_assigned ? 'College: ' : 'Department: ',
                            'style' => 'width:95%',
                            'type' => 'select',
                            'options' => $departments,
                            'required' => true,
                            'default' => isset($department_id) ? $department_id : ''
                        ]); ?>
                    <?php } ?>
                </div>
                <div class="large-6 columns">
                     
                </div>
            </div>
            <hr>
            <?= $this->Form->button(
                isset($search_button_label) && !empty($search_button_label) ? $search_button_label : 'List Published Courses',
                ['name' => 'listPublishedCourses', 'id' => 'listPublishedCourses', 'class' => 'tiny radius button bg-blue']
            ); ?>
        </fieldset>
    </div>
    <hr>
</div>

<?php if (!empty($publishedCourses)) { ?>
    <div id="show_published_courses_drop_down">
        <table class="fs14" cellpadding="0" cellspacing="0" class="table">
            <tr>
                <td style="width:25%;" class="center">Published Courses</td>
                <td colspan="3">
                    <div class="large-10 columns">
                        <br>
                        <?= $this->Form->control('published_course_id', [
                            'style' => 'width: 90%;',
                            'class' => 'fs14',
                            'id' => 'PublishedCourse',
                            'label' => false,
                            'type' => 'select',
                            'required' => true,
                            'options' => $publishedCourses,
                            'default' => $published_course_combo_id
                        ]); ?>
                    </div>
                </td>
            </tr>
        </table>
    </div>
<?php } ?>


<?php
$redirectUrl = '';
$additionalParams = false;
// Check if any additional parameters are present in the URL
if (!empty($this->request->getParam('pass'))) {
    $additionalParams = true;
    // Use Router::url() to generate the correct URL with DashedRoute
    $redirectUrl = $this->Url->build([
        'controller' => $this->request->getParam('controller'),
        'action' => $this->request->getParam('action'),
        'pass' => $this->request->getParam('pass')
    ]);

}
?>

<script>
    function toggleViewFullId(id) {
        if ($('#' + id).css("display") === 'none') {
            $('#' + id + 'Img').attr("src", '/img/minus2.gif');
            $('#' + id + 'Txt').empty().append(' Hide Filter');
        } else {
            $('#' + id + 'Img').attr("src", '/img/plus2.gif');
            $('#' + id + 'Txt').empty().append(' Display Filter');
        }
        $('#' + id).toggle("slow");
    }

    $('#listPublishedCourses').click(function() {
        $('#listPublishedCourses').val('Looking for Published Courses...');
        $('#PublishedCourse').val(0);
        $("#show_published_courses_drop_down").hide();

        if ($('#show_search_results').length) {
            $("#show_search_results").hide();
        }

        if ($('#manage_ng_form').length) {
            $("#manage_ng_form").hide();
        }

        if ($('#minuteNumber').length) {
            $('#minuteNumber').val('');
        }

        if ($('#select-all').length) {
            $("#select-all").prop('checked', false);
        }
        var additionalParams = <?= json_encode($additionalParams); ?>;
        if (additionalParams) {
            var redirectUrl = '<?= $this->Url->build($redirectUrl); ?>';

            alert(redirectUrl);

            window.location.href = redirectUrl;
        }
    });
</script>
