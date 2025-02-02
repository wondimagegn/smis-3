<?php
if ($this->Session->read('candidate_publish_courses')) {

    $coursesss = $this->Session->read('candidate_publish_courses');
    $taken_courses_allow_to_publishe_it = $this->Session->read('taken_courses_allow_to_publishe_it');
    debug($taken_courses_allow_to_publishe_it);
    $selected_section = $this->Session->read('selected_section');
    $published_courses_disable_not_to_published = $this->Session->read('published_courses_disable_not_to_published');

    if (!empty($coursesss)) { ?>
        <hr>
        <blockquote>
            <h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
            <p style="text-align:justify;"> <span class="fs16 text-black"> Use <b>Publish selected as Add</b> </span>  button in a situations where there is a missed course which should be published for a given academic year and semester.</span></p> 
        </blockquote>
        <hr>
        
        <?php
        $display_button = 0;
        $section_count = 0;

        foreach ($coursesss as $section_id => $coursss) {
            $section_count++;
            if (!empty($coursss)) { ?>


                <h6 id="validation-message_non_selected" class="text-red fs14"></h6>
                <br>

                <div style="overflow-x:auto;">
                    <table id='fieldsForm' cellpadding="0" cellspacing="0" class="table">
                        <thead>
                            <tr>
                                <th colspan="10"><?= "Section: " . $selected_section[$section_id]; ?></td>
                            </tr>
                            <tr>
                                <th colspan="10"><?= "Select the course you want to publish."; ?></td>
                            </tr>
                            <tr>
                                <th class="center">&nbsp;</th>
                                <th class="center">#</th>
                                <th class="center">Year</th>
                                <th class="center">SEM</th>
                                <th class="vcenter">Course Title </th>
                                <th class="center">Course Code </th>
                                <th class="center">Prerequisite </th>
                                <th class="center">Credit </th>
                                <th class="center">L T L</th>
                                <th class="center">Elective</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $count = 1;
                            foreach ($coursss as $kc => $vc) { ?>
                                <tr>
                                    <?php
                                    if (isset($published_courses_disable_not_to_published[$section_id]) && in_array($vc['Course']['id'], $published_courses_disable_not_to_published[$section_id])) {
                                        echo '<td class="center">**</td>';
                                    } else {
                                        echo '<td class="center">' . $this->Form->checkbox('Course.' . $section_id . '.' . $vc['Course']['id']) . '</td>';
                                    } ?>
                                    <td class="center"><?= $count; ?></td>
                                    <td class="center"><?= $vc['YearLevel']['name']; ?></td>
                                    <td class="center"><?= $vc['Course']['semester']; ?></td>
                                    <td class="vcenter"><?= $vc['Course']['course_title']; ?></td>
                                    <td class="center"><?= $vc['Course']['course_code']; ?></td>
                                    <td class="center">
                                        <?php
                                        if (!empty($vc['Prerequisite'])) {
                                            foreach ($vc['Prerequisite'] as $ppindex => $pvlll) {
                                                //debug($vc['Prerequisite'][0]['prerequisite_course_id']);
                                                /* if (isset($pvlll['pre_code']) && !empty($pvlll['pre_code'])) {
                                                    echo $pvlll['pre_code'];
                                                }  */
                                                if (isset($pvlll['prerequisite_course_id']) && !empty($pvlll['prerequisite_course_id'])) {
                                                    //echo $pvlll['prerequisite_course_id'];
                                                    $pre_code =  ClassRegistry::init('Course')->field('course_code', array('Course.id' => $pvlll['prerequisite_course_id']));
                                                
                                                    if (count($pre_code) == 1) {
                                                        echo $pre_code ." ";
                                                    } /* else if (count($pre_code) > 1 ) {
                                                        debug($pre_code);
                                                        //echo $pre_code[0]. ", " . count($pre_code) .' More.';
                                                    } */
                                                
                                                }
                                            }
                                        } else {
                                            echo 'none';
                                        } ?>
                                    </td>

                                    <td class="center"><?= $vc['Course']['credit']; ?></td>
                                    <td class="center"><?= $vc['Course']['course_detail_hours']; ?></td>

                                    <?php
                                    if (isset($published_courses_disable_not_to_published[$section_id]) && in_array($vc['Course']['id'], $published_courses_disable_not_to_published[$section_id])) {
                                        //find the publish course id
                                        foreach ($published_courses_disable_not_to_published[$section_id] as $p_id => $p_course_id) {
                                            if ($p_course_id == $vc['Course']['id']) {
                                                $published_id = $p_id;
                                                break 1;
                                            }
                                        }
                                    }

                                    if (isset($published_courses_disable_not_to_published[$section_id]) && in_array($vc['Course']['id'], $published_courses_disable_not_to_published[$section_id])) {
                                        echo '<td class="center">**</td>';
                                    } else {
                                        //echo "<td class="center">".$this->Form->input('Course.'.$section_id.'.elective.'.$vc['Course']['id'].'', array('type' => 'checkbox', 'label' => false))."</td>";
                                        echo '<td> <div style="padding-left: 25px;">' . $this->Form->checkbox('Elective.' . $section_id . '.' . $vc['Course']['id'], array('checked' => (isset($vc['Course']['elective']) && $vc['Course']['elective'] == 1 ? 'checked': false))) . '</div></td>';
                                    } ?>

                                </tr>
                                <?php
                                $count++;
                            } ?>
                        </tbody>
                        <?php
                        if (isset($published_courses_disable_not_to_published[$section_id]) && count($published_courses_disable_not_to_published[$section_id]) > 0) { ?>
                            <tfoot>
                                <tr>
                                    <td class="center"> ** </td>
                                    <td colspan=9 class="vcenter">Those courses with two asterik are courses that are already published for the selected section using the given search criteria.</td>
                                </tr>
                            </tfoot>
                            <?php
                        } ?>
                    </table>
                </div>
                <br>
                <?php
            } else {
                $display_button++;
            }
        } ?>

        <?php
        if ($display_button != $section_count) { ?>
            <hr>
            <div class="row">
                <div class="large-4 columns">
                    <?= $this->Form->submit('Publish Selected', array('id' => 'publishSelected', 'name' => 'publishselected', 'class' => 'tiny radius button bg-blue', 'div' => 'false')); ?>
                </div>
                <div class="large-8 columns">
                    <?= (ALLOW_PUBLISH_AS_ADD_COURSE_FOR_DEPARTMENT_ROLE ? $this->Form->submit('Publish as Mass Add', array('id' => 'publishSelectedAsAdd', 'name' => 'publishselectedasadd', 'class' => 'tiny radius button bg-red', 'div' => 'false')) : ''); ?>
                </div>
            </div>
            <?php
        } ?>

        <?php
        if (!empty($taken_courses_allow_to_publishe_it) && count($taken_courses_allow_to_publishe_it) > 0 && 0) {
            foreach ($taken_courses_allow_to_publishe_it as $section_id => $coursss) {
                if (!empty($coursss)) { ?>
                    <div class='info-box info-message'>
                        <span></span>
                        Already taken courses of the selected section. You can repulish courses to allow students to register for the courses again.
                        This happens when all students fail the course or not able to follow the courses.
                    </div>
                    <div style="overflow-x:auto;">
                        <table id='fieldsForm' cellpadding="0" cellspacing="0" class="table">
                            <thead>
                                <tr>
                                    <th colspan="8"><?php echo "Section: " . $selected_section[$section_id]; ?></td>
                                </tr>
                                <tr>
                                    <th class="center">&nbsp;</th>
                                    <th class="center">#</th>
                                    <th class="center">Year </th>
                                    <th class="center">SEM</th>
                                    <th class="vcenter">Course Title</th>
                                    <th class="center">Course Code</th>
                                    <th class="center">Prerequisite</th>
                                    <th class="center">Credit</th>
                                    <th class="center">L T L</th>
                                    <th class="center">Elective</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $count = 1;
                                foreach ($coursss as $kc => $vc) { ?>
                                    <tr>
                                        <td class="center"><?= $this->Form->checkbox('Course.' . $section_id . '.' . $vc['Course']['id']); ?></td>
                                        <td class="center"><?= $count; ?></td>
                                        <td class="center"><?= $vc['YearLevel']['name']; ?></td>
                                        <td class="center"><?= $vc['Course']['semester']; ?></td>
                                        <td class="vcenter"><?= $vc['Course']['course_title']; ?></td>
                                        <td class="center"><?= $vc['Course']['course_code']; ?></td>
                                        <td class="center">
                                            <?php
                                            if (!empty($vc['Prerequisite'])) {
                                                foreach ($vc['Prerequisite'] as $ppindex => $pvlll) {
                                                    if (isset($pvlll['pre_code']) && !empty($pvlll['pre_code'])) {
                                                        echo $pvlll['pre_code'];
                                                    }
                                                }
                                            } else {
                                                echo 'none';
                                            } ?>
                                        </td>

                                        <td class="center"><?= $vc['Course']['credit']; ?></td>
                                        <td class="center"><?= $vc['Course']['course_detail_hours']; ?></td>
                                        <td class="center"></td>
                                    </tr>
                                    <?php
                                    $count++;
                                } ?>
                            </tbody>
                        </table>
                    </div>
                    <br>
                    <?php
                }
            }
        }
    }
} ?>

<?= $this->Form->end(); ?>

<script type="text/javascript">

    var form_being_submitted = false;

    const validationMessageNonSelected = document.getElementById('validation-message_non_selected');

    $(document).ready(function() {
        $('#publishSelectedAsAdd').click(function() {

            var checkboxes = document.querySelectorAll('input[type="checkbox"][name^="data[Course]"]');
            var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);
            var chckboxs = document.querySelectorAll('input[type="checkbox"][name^="data[Course]"]:checked');

            if (!checkedOne || chckboxs.length == 0) {
                alert('At least one course must be selected to publish as mass add.');
                //validationMessageNonSelected.innerHTML = 'At least one course must be selected to publish as mass add.';
                return false;
            }

            if (form_being_submitted) {
                alert("Publishing Selected as Mass Add, please wait a moment...");
                $('#publishSelectedAsAdd').prop('disabled', true);
                return false;
            }

            var confirmmed = confirm('Are you sure you want to publish the selected courses as Mass Add for the selected section? Use this option if and only if there is a previous course publication for the section using the same academic year and semester with section students already registerd for the courses or you are unable to publish the courses using Publish Selected option, i.e. if you forgot to to publish the courses for the section in the given academic year and semester or the students are taking the courses as a block course.');

            if (confirmmed) {
                $('#publishSelectedAsAdd').val('Publishing as Mass Add...');
                form_being_submitted = true;
                return true;
            } else {
                return false;
            }
            
        });

        $('#publishSelected').click(function() {

           /*  var checkboxes = document.querySelectorAll('input[type="checkbox"]');
            var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);
            var chckboxs = document.querySelectorAll('input[type="checkbox"]:checked'); */

            var checkboxes = document.querySelectorAll('input[type="checkbox"][name^="data[Course]"]');
            var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);
            var chckboxs = document.querySelectorAll('input[type="checkbox"][name^="data[Course]"]:checked');

            if (!checkedOne || chckboxs.length == 0) {
                alert('At least one course must be selected to publish.');
                validationMessageNonSelected.innerHTML = 'At least one course must be selected to publish.';
                return false;
            }

            if (form_being_submitted) {
                alert("Publishing Selected Courses, please wait a moment...");
                $('#publishSelected').prop('disabled', true);
                return false;
            }

            $('#publishSelected').val('Publishing Selected Courses...');
            form_being_submitted = true;
            return true;

        });
    });

    if (window.history.replaceState) {
		window.history.replaceState(null, null, window.location.href);
	}
</script>
