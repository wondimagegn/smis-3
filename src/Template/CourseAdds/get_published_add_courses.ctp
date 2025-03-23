<?php
if (!empty($otherAdds)) { ?>
    <hr>
    <div class='smallheading fs14 text-gray'> Select courses you want to add.</div>
    <h6 id="validation-message_non_selected" class="text-red fs14"></h6>
    <br>
    <div style="overflow-x:auto;">
        <table id="fieldsForm" cellpadding="0" cellspacing="0" class="table student_list">
            <thead>
                <tr>
                    <th class="center" style="width: 4%;">&nbsp;</th>
                    <th class="center" style="width: 3%;">#</th>
                    <th class="vcenter">Course Title </th>
                    <th class="center">Course Code</th>
                    <th class="center">Credit</th>
                    <th class="center" style="width: 10%;"> L T L </th>
                </tr>
            </thead>
            <tbody>
                <?php
                $count = 0;
                $button_visible = 0;
                $failed_prequisite = 0;
                $already_taken_courses = 0;
                
                foreach ($otherAdds as $pk => $pv) {
                    debug($pv);
                    if ($pv['already_added'] == 0) { ?>
                        <tr>
                            <td class="center"><div style="padding-left: 25%;"><?= $this->Form->checkbox('CourseAdd.add.' . $pv['PublishedCourse']['id']); ?></div></td>
                            <td class="center"><?= ++$count; ?></td>
                            <td class="vcenter"><?= $pv['Course']['course_title']; ?></td>
                            <td class="center"><?= $pv['Course']['course_code']; ?></td>
                            <td class="center"><?= $pv['Course']['credit']; ?></td>
                            <td class="center"><?= $pv['Course']['course_detail_hours']; ?></td>
                        </tr>
                        <?php
                        $button_visible++;
                    } else {
                        if (isset($pv['prerequiste_failed']) && $pv['prerequiste_failed'] == 1) { ?>
                            <tr>
                                <td class="center" style="color:red;">--</td>
                                <td class="center" style="color:red;"><?= ++$count; ?></td>
                                <td class="vcenter" style="color:red;"><?= $pv['Course']['course_title']; ?></td>
                                <td class="center" style="color:red;"><?= $pv['Course']['course_code']; ?></td>
                                <td class="center" style="color:red;"><?= $pv['Course']['credit']; ?></td>
                                <td class="center" style="color:red;"><?= $pv['Course']['course_detail_hours']; ?></td>
                            </tr>
                            <?php
                            $failed_prequisite++;
                        } else { ?>
                            <tr>
                                <td class="center" style="color:green;">**</td>
                                <td class="center" style="color:green;"><?= ++$count; ?></td>
                                <td class="vcenter" style="color:green;"><?= $pv['Course']['course_title']; ?></td>
                                <td class="center" style="color:green;"><?= $pv['Course']['course_code']; ?></td>
                                <td class="center" style="color:green;"><?= $pv['Course']['credit']; ?></td>
                                <td class="center" style="color:green;"><?= $pv['Course']['course_detail_hours']; ?></td>
                            </tr>
                            <?php
                            $already_taken_courses++;
                        }
                    }
                } ?>
            </tbody>
            <?php
            if ($failed_prequisite || $already_taken_courses) { ?>
                <tfoot>
                    <tr>
                        <td class="hcenter"><?= ($already_taken_courses != 0 ? '**' : ''); ?></td>
                        <td colspan=6>
                            <span class="fs14" style="margin-bottom: 5px; font-weight: normal;">

                                <?= ($already_taken_courses != 0 ? 'Marked Green Courses: you have already registred or taken the course and got pass grade, you don\'t need to add again.' . ($failed_prequisite != 0 ? '<br>' : '') : ''); ?>
                                <?= ($failed_prequisite != 0 ? 'Red marked Courses: prerequiste course requirement not fullfilled.' : ''); ?>
                            </span>
                        </td>
                    </tr>
                </tfoot>
                <?php
            } ?>
        </table>
    </div>
    <hr>
    
    <?php
    if ($button_visible > 0) {
        echo $this->Form->submit('Add Selected', array('name' => 'addSelected', 'id' => 'addSelected', 'class' => 'tiny radius button bg-blue'));
        //echo $this->Form->end('Add Selected', array('class' => 'tiny radius button bg-blue'));
    } ?>

    <script type="text/javascript">

        var form_being_submitted = false;
        var submitButtonUsedBefore = false;

        var maxCoursesAllowedPerSemester = '<?= MAXIMUM_COURSES_TO_ADD_PER_SEMESTER; ?>';

        const validationMessageNonSelected = document.getElementById('validation-message_non_selected');

        $('#addSelected').click(function() {
            
            var checkboxes = document.querySelectorAll('input[type="checkbox"]');
            var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);

            //alert(checkedOne);
            if (!checkedOne) {
                alert('At least one course must be selected to add.');
                validationMessageNonSelected.innerHTML = 'At least one course must be selected to add.';
                return false;
            }

            if (form_being_submitted && !submitButtonUsedBefore) {
                alert("Processing the selected course add requests, please wait a moment...");
                $('#addSelected').attr('disabled', true);
                submitButtonUsedBefore = true;
                return false;
            } 
            
            if (submitButtonUsedBefore) {
                $('#addSelected').val('Refresh Page');
                $('#addSelected').attr('disabled', true);
            }

            var confirmm = confirm('Only ' + maxCoursesAllowedPerSemester + ' courses are allowed to add per semester. Please confirm you choosen the right courses. Are you sure you want to add the selected course(s)?');

            if (confirmm && !form_being_submitted) {
                $('#addSelected').val('Processing Selected Course Adds...');
                form_being_submitted = true;
                submitButtonUsedBefore = true;
                return true;
            } else {
                return false;
            }

        });

        if (form_being_submitted || submitButtonUsedBefore) {
            $('#addSelected').val('Refresh Page');
            $('#addSelected').attr('disabled', true);
        }

        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
        
    </script>
    <?php
} ?>
