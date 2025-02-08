<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= __('Publish or Prepare Semester Courses'); ?></span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <?= $this->Form->create('PublishedCourse'); ?>
                <?php
                if (!isset($turn_off_search)) { ?>
                    <div style="margin-top: -30px;">
                        <hr>
                        <!-- <h6 class="text-gray fs14">Publish or Prepare Semester Courses</h6> -->
                        <fieldset style="padding-bottom: 5px; padding-top: 15px;">
                            <!-- <legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend> -->
                            <div class="row">
                                <div class="large-2 columns">
                                    <?= $this->Form->input('Course.academicyear', array('label' => 'Academic Year: ', 'required', 'type' => 'select', 'style' => 'width:90%;', 'options' => $acyear_array_data, 'empty' => "[ Select Academic Year ]", 'default' => isset($defaultacademicyear) ? $defaultacademicyear : '')); ?>
                                </div>
                                <div class="large-2 columns">
                                    <?= $this->Form->input('Curriculum.semester', array('label' => 'Semester: ', 'options' => Configure::read('semesters'), 'required', 'empty' => '[ Select semester ]', 'style' => 'width:90%;')); ?>
                                </div>
                                <div class="large-3 columns">
                                    <?= $this->Form->input('Curriculum.program_id', array('label' => 'Program: ', 'required', 'empty' => '[ Select Program ]', 'style' => 'width:90%;')); ?>
                                </div>
                                <div class="large-3 columns">
                                    <?= $this->Form->input('Curriculum.program_type_id', array('label' => 'Program Type: ', 'required', 'empty' => '[ Select Program Type ]', 'style' => 'width:90%;')); ?>
                                </div>
                                <div class="large-2 columns">
                                    <?= $this->Form->input('Course.year_level_id', array('label' => 'Year Level: ', 'required', 'empty' => '[ Select Year Level ]', 'style' => 'width:90%;')); ?>
                                </div>
                            </div>
                            <hr>
                            <?= $this->Form->submit('Continue', array('name' => 'getsection', 'class' => 'tiny radius button bg-blue', 'div' => 'false')); ?>
                        </fieldset>
                    </div>
                    <?php
                } ?>

                <!-- COURSES LOADING -->
                <div id="loading">
                
                </div>
                <!-- END COURSES LOADING -->


                <?php
                if (isset($turn_off_search)) { ?>
                   <table cellpadding="0" cellspacing="0" class="table-borderless">
                        <tr>
                            <td> <h6 class="text-gray fs16">Select the course you want to publish for <?= ($semester == 'I' ? '1st' : ($semester == 'II' ? '2nd' : ($semester == 'III' ? '3rd' : $semester))) . '  Semester of ' . $academic_year . ' Academic Year'; ?></h6></td>
                        </tr>
                        <?php
                        echo $this->Form->hidden('PublishedCourse.semester', array('value' => $semester));
                        echo $this->Form->hidden('PublishedCourse.program_id', array('value' => $program_id));
                        echo $this->Form->hidden('PublishedCourse.program_type_id', array('value' => $program_type_id));
                        echo $this->Form->hidden('PublishedCourse.academic_year', array('value' => $academic_year));
                        echo $this->Form->hidden('PublishedCourse.year_level_id', array('value' => $year_level_id));

                        if (isset($sections) && !empty($sections)) {
                            foreach ($sections as $key => $value) { ?>
                                <tr>
                                    <td><?= $this->Form->input('Section.selected.' . $key, array('class' => 'candidatePublishCourse', 'label' => $value, 'type' => 'checkbox', 'value' => $key, 'checked' => isset($selectedsection) && in_array($key, $selectedsection) ? 'checked' : '')); ?></td>
                                </tr>
                                <?php
                            }
                            
                            echo $this->Js->get("input.candidatePublishCourse")->event("change",
                                $this->Js->request(
                                    array(
                                        'controller' => 'publishedCourses',
                                        'action' => 'selectedPublishedCourses'
                                    ),
                                    array(
                                        'update' => "#candidate_published_course_list",
                                        'async' => true,
                                        'method' => 'post',
                                        'dataExpression' => true,
                                        'beforeSend' => '$("#busy_indicator").show();',
                                        'complete' => '$("#busy_indicator").hide();',
                                        'data' => $this->Js->serializeForm(array(
                                            'isForm' => false,
                                            'inline' => true
                                        ))
                                    )
                                )
                            );
                        } ?>
                    </table>
                    <?php
                } ?>

                <!-- AJAX LOADING -->
                <div id="candidate_published_course_list">

                </div>
                <!-- END AJAX LOADING -->

                <?php
                /* if (isset($show_publish_page)) {
                    if (!empty($coursesss)) {  ?>
                        <?php
                        // echo "Check All/Uncheck All <br/>".$this->Form->checkbox(null, array('id' => 'select-all','checked'=>''));
                        $display_button = 0;
                        $section_count = 0;
                        foreach ($coursesss as $section_id => $coursss) {
                            $section_count++;

                            if (!empty($coursss)) { ?>
                                <table id='fieldsForm' cellpadding="0" cellspacing="0" class="table-borderless">
                                    <tbody>
                                        <tr>
                                            <th colspan=7><?php echo "Section: " . $selected_section[$section_id]; ?></td>
                                        </tr>
                                        <tr>
                                            <th colspan=7><?php echo "Select the course you want to publish for " . $academic_year . " of semester " . $semester . " "; ?></td>
                                        </tr>
                                        <tr>
                                            <th style='padding:0'> &nbsp;</th>
                                            <th style='padding:0'> S.No </th>
                                            <th style='padding:0'> Course Title </th>
                                            <th style='padding:0'> Course Code </th>
                                            <th style='padding:0'> Lecture hour </th>
                                            <th style='padding:0'> Tutorial hour </th>
                                            <th style='padding:0'> Credit </th>
                                        </tr>
                                        <?php
                                        $count = 1;
                                        foreach ($coursss as $kc => $vc) { ?>
                                            <tr>
                                                <td>
                                                    <?= $this->Form->checkbox(
                                                        'Course.' . $section_id . '.' . $vc['Course']['id'],
                                                        array(
                                                            'disabled' => in_array(
                                                                $vc['Course']['id'],
                                                                $published_courses_disable_not_to_published[$section_id]
                                                            ) ? true : false
                                                        )
                                                    ); ?>
                                                </td>
                                                <td><?= $count; ?></td>
                                                <td><?= $vc['Course']['course_title']; ?></td>
                                                <td><?= $vc['Course']['course_code']; ?></td>
                                                <td><?= $vc['Course']['lecture_hours']; ?></td>
                                                <td><?= $vc['Course']['tutorial_hours']; ?></td>
                                                <td><?= $vc['Course']['credit']; ?></td>
                                            </tr>
                                        <?php
                                            $count++;
                                        } ?>
                                    </tbody>
                                </table>
                                <?php
                            } else {
                                $display_button++;
                            }
                        } ?>

                        <table cellpadding="0" cellspacing="0" class="table-borderless">
                            <tr>
                                <?php
                                if ($display_button != $section_count) { ?>
                                    <td style='padding:0'>
                                        <?php echo $this->Form->submit('Publish Selected', array('name' => 'publishselected', 'class' => 'tiny radius button bg-blue', 'div' => 'false')); ?>
                                    </td>
                                    <td style='padding:0'> 
                                        <?php echo $this->Form->submit('Publish Selected as Add', array('name' => 'publishselectedasadd', 'class' => 'tiny radius button bg-red', 'div' => 'false')); ?>
                                    </td>
                                    <?php 
                                } ?>
                            </tr>
                        </table>

                        <?php
                        if (!empty($taken_courses_allow_to_publishe_it)) { ?>
                            <div class='smallheading'>
                                Already taken coures of the selected section, you can check it the already taken courses to allow students to register for the courses again. 
                                This happens when all students fail the course or not able to follow the course.
                            </div>
                            <?php
                            foreach ($taken_courses_allow_to_publishe_it as $section_id => $coursss) {
                                if (!empty($coursss)) { ?>
                                    <table id='fieldsForm' cellpadding="0" cellspacing="0" class="table-borderless">
                                        <tbody>
                                            <tr><th colspan=7><?php echo "Section: " . $selected_section[$section_id]; ?></td></tr>
                                            <tr>
                                                <th style='padding:0'> &nbsp;</th>
                                                <th style='padding:0'> S.No </th>
                                                <th style='padding:0'> Course Title </th>
                                                <th style='padding:0'> Course Code </th>
                                                <th style='padding:0'> Lecture hour </th>
                                                <th style='padding:0'> Tutorial hour </th>
                                                <th style='padding:0'> Credit </th>
                                            </tr>
                                            <?php
                                            $count = 1;
                                            foreach ($coursss as $kc => $vc) { ?>
                                                <tr>
                                                    <td><?= $this->Form->checkbox('Course.' . $section_id . '.' . $vc['Course']['id']); ?></td>
                                                    <td><?= $count; ?></td>
                                                    <td><?= $vc['Course']['course_title']; ?></td>
                                                    <td><?= $vc['Course']['course_code']; ?></td>
                                                    <td><?= $vc['Course']['lecture_hours']; ?></td>
                                                    <td><?= $vc['Course']['tutorial_hours']; ?></td>
                                                    <td><?= $vc['Course']['credit']; ?></td>
                                                </tr>
                                                <?php
                                                $count++;
                                            } ?>
                                        </tbody>
                                    </table>
                                    <?php
                                }
                            } ?>
                            <?php
                        }
                    }
                } */ ?>
                <?php //echo $this->Form->end(); ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $(".candidatePublishCourse").each(function() {
            if ($(this).is(":checked")) {
                $('#candidate_published_course_list').load('/publishedCourses/selectedPublishedCourses/2');
            }
        });
    });
</script>

<?= $this->Js->writeBuffer(); ?>