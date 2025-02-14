<?php
if (isset($studentListForExitExam) && !empty($studentListForExitExam)) {
    foreach ($studentListForExitExam as $program => $programType) {
        foreach ($programType as $programTypeName => $statDetail) { ?>
            <br />
            <hr />
            <p class="fs16">
                Showing <?= $this->data['Report']['top'] . ' ' . $this->data['Report']['gender']; ?> students as of
                <strong><?= $this->data['Report']['acadamic_year']; ?></strong> Academic Year,
                <strong>
                    <?php
                    if ($this->data['Report']['semester'] == 'I') {
                        echo '1st ';
                    } else if ($this->data['Report']['semester'] == 'II') {
                        echo '2nd ';
                    } else if ($this->data['Report']['semester'] == 'III') {
                        echo '3rd ';
                    } ?>
                </strong> Semester <br />
                <strong> Program : </strong>
                <strong><?= $program; ?></strong><br />
                <strong> Program Type: </strong>
                <strong><?= $programTypeName; ?></strong><br />
            </p>

            <div style="overflow-x:auto;">
                <table cellpadding="0" cellspacing="0" class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th class="center">#</th>
                            <th class="vcenter">First Name</th>
                            <th class="vcenter">Middle Name</th>
                            <th class="vcenter">Last Name</th>
                            <th class="center">Sex</th>
                            <th class="center">Student ID</th>
                            <th class="center">College</th>
                            <th class="center">Department</th>
                            <th class="center">Curriculum Name</th>
                            <th class="center">Degree Nomenclature</th>
                            <th class="center">Specialization</th>
                            <th class="center">Cr. Type</th>
                            <th class="center">Min Cr. Req.</th>
                            <th class="center">Curriculum Courses</th>
                            <th class="center">Taken courses</th>
                            <th class="center">Taken Credit</th>
                            <th class="center">Registered courses</th>
                            <th class="center">Registered Credit</th>
                            <th class="center">Added courses</th>
                            <th class="center">Added Credit</th>
                            <th class="center">Dropped courses</th>
                            <th class="center">Dropped Credit</th>
                            <th class="center">Exempted courses</th>
                            <th class="center">Exempted Credit</th>
                            <th class="center">Thesis Taken</th>
                            <th class="center">Thesis Credit</th>
                            <th class="center">Year</th>
                            <th class="center">CrHr Sum</th>
                            <th class="center">SGPA</th>
                            <th class="center">CGPA</th>
                            <th class="center">Rem Cr.</th>
                            <th class="center">Email</th>
                            <th class="center">Mobile</th>
                            <th class="center">Graduated</th>
                            <!-- <th class="center">Photo</th> -->
                            <!-- <th class="center">Photo File Name</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $count = 0;
                        //debug($statDetail);
                        //$row_style = '';
                        foreach ($statDetail as $in => $val) {
                            $taken = ClassRegistry::init('StudentExamStatus')->getStudentTakenCreditsForExitExam($val['Student']['id']);
                            if (!empty($val['Student']['Curriculum']['id'])) {
                                $row_style = ' ';
                            } else {
                                $row_style = ' style="color: red;" ';
                            }  ?>
                            <tr class="jsView" data-animation="fade" data-reveal-id="myModal" data-reveal-ajax="/students/get_modal_box/<?= $val['Student']['id']; ?>">
                                <td class="center"><?= ++$count; ?></td>
                                <td class="vcenter"><?= $val['Student']['first_name']; ?></td>
                                <td class="vcenter"><?= $val['Student']['middle_name']; ?></td>
                                <td class="vcenter"><?= $val['Student']['last_name']; ?></td>
                                <td <?= $row_style; ?> class="center"><?= ((strcasecmp(trim($val['Student']['gender']), 'male') == 0) ? 'M' : 'F'); ?></td>
                                <td <?= $row_style; ?> class="center"><?= $val['Student']['studentnumber']; ?></td>
                                <td <?= $row_style; ?> class="vcenter"><?= ((isset($val['Student']['College']['name']) && !empty($val['Student']['College']['name'])) ? $val['Student']['College']['name'] : ''); ?></td>
                                <td class="vcenter"><?= ((isset($val['Student']['Department']['name']) && !empty($val['Student']['Department']['name'])) ? $val['Student']['Department']['name'] : 'Pre/Freshman'); ?></td>
                                <td class="vcenter"><?= ((isset($val['Student']['Curriculum']['name']) && !empty($val['Student']['Curriculum']['name'])) ? $val['Student']['Curriculum']['name'] : '---'); ?></td>
                                <td class="vcenter"><?= ((isset($val['Student']['Curriculum']['english_degree_nomenclature']) && !empty($val['Student']['Curriculum']['english_degree_nomenclature'])) ?  $val['Student']['Curriculum']['english_degree_nomenclature'] : '---'); ?></td>
                                <td class="vcenter"><?= ((isset($val['Student']['Curriculum']['specialization_english_degree_nomenclature']) && !empty($val['Student']['Curriculum']['specialization_english_degree_nomenclature'])) ? $val['Student']['Curriculum']['specialization_english_degree_nomenclature'] : '---'); ?></td>
                                <td class="center"><?= ((isset($val['Student']['Curriculum']['type_credit']) && !empty($val['Student']['Curriculum']['type_credit'])) ? $val['Student']['Curriculum']['type_credit'] : '---'); ?></td>
                                <td class="center"><?= ((isset($val['Student']['Curriculum']['minimum_credit_points']) && !empty($val['Student']['Curriculum']['minimum_credit_points'])) ? $val['Student']['Curriculum']['minimum_credit_points'] : '---'); ?></td>
                                <td class="center">
                                    <?php
                                    //$all_curriculum_courses = $taken['curriculum_major_course_count'] + $taken['curriculum_minor_course_count'];
                                    echo $taken['curriculum_major_course_count'] + $taken['curriculum_minor_course_count'];
                                    //echo ' ( Major: ' . $taken['curriculum_major_course_count']  . ' Minor: ' . $taken['curriculum_minor_course_count']. ' )';
                                    ?>
                                </td>
                                <td class="center"><?= ((isset($taken['taken_course_count']) && $taken['taken_course_count'] != 0) ? $taken['taken_course_count'] : '---'); ?></td>
                                <td class="center"><?= ((isset($taken['credit_sum']) && $taken['credit_sum'] != 0) ? $taken['credit_sum'] : '---'); ?></td>
                                <td class="center"><?= ((isset($taken['course_count_registration']) && $taken['course_count_registration'] != 0) ? $taken['course_count_registration'] : '---'); ?></td>
                                <td class="center"><?= ((isset($taken['credit_sum_registration']) && $taken['credit_sum_registration'] != 0) ? $taken['credit_sum_registration'] : '---'); ?></td>
                                <td class="center"><?= ((isset($taken['course_count_add']) && $taken['course_count_add'] != 0) ? $taken['course_count_add'] : '---'); ?></td>
                                <td class="center"><?= ((isset($taken['credit_sum_add']) && $taken['credit_sum_add'] != 0) ? $taken['credit_sum_add'] : '---'); ?></td>
                                <td class="center"><?= (isset($taken['droped_courses_count']) && $taken['droped_courses_count'] != 0) ? $taken['droped_courses_count'] : '---'; ?></td>
                                <td class="center"><?= ((isset($taken['droped_credit_sum']) && $taken['droped_credit_sum'] != 0) ? $taken['droped_credit_sum'] : '---'); ?></td>
                                <td class="center"><?= ((isset($taken['exempted_course_count']) && $taken['exempted_course_count'] != 0) ? $taken['exempted_course_count'] : '---'); ?></td>
                                <td class="center"><?= ((isset($taken['exempted_credit_sum']) && $taken['exempted_credit_sum'] != 0) ? $taken['exempted_credit_sum'] : '---'); ?></td>
                                <td class="center"><?= ((isset($taken['thesis_taken']) && $taken['thesis_taken'] == 1) ? 'Yes' : 'No'); ?></td>
                                <td class="center"><?= (($taken['thesis_taken'] == 1 && isset($taken['thesis_credit'])) ? $taken['thesis_credit'] : '---'); ?></td>
                                <td class="center"><?= $val['Student']['yearLevel']; ?></td>
                                <td class="center"><?= $val['StudentExamStatus']['credit_hour_sum']; ?></td>
                                <td class="center"><?= $val['StudentExamStatus']['sgpa']; ?></td>
                                <td class="center"><?= $val['StudentExamStatus']['cgpa']; ?></td>
                                <td class="center">
                                    <?php
                                    if (isset($val['Student']['Curriculum']) && isset($val['Student']['Curriculum']['minimum_credit_points'])) {
                                        if ($val['Student']['Curriculum']['minimum_credit_points'] != 0) {
                                            $remaining_credits = ($taken['credit_sum'] + $taken['exempted_credit_sum']) - $val['Student']['Curriculum']['minimum_credit_points'];
                                            echo $remaining_credits;
                                        } else {
                                            echo 'Invalid minimun credit point for the curriculum';
                                        }
                                    } else {
                                        echo 'student not attached to curriculum';
                                    }
                                    /* if (isset($taken) && !empty($taken)) {
                                            echo 'All: <b>'. $taken['credit_sum'] .' </b>( <b>' . $taken['taken_course_count']  .' </b> course(s) <br/>';
                                                echo  '( Major: <b>' . $taken['taken_major_course_count']  . ' </b> credits: ' . $taken['taken_major_course_credit']. ' </b>)<br/>';
                                                //echo '( Minor: <b>' . $taken['taken_minor_course_count']  . ' </b> credits: ' . $taken['taken_minor_course_credit']. ' </b>)<br/>';
                                                echo  '( Registered for <b> ' . $taken['course_count_registration']  . ' </b>  Courses with <b>' . $taken['credit_sum_registration']. ' </b> credits total)<br/>';
                                                echo  '( Added <b>' . $taken['course_count_add']  . ' </b>  Courses with <b>' . $taken['credit_sum_add']. ' </b> credits total)<br/>';
                                                echo  '( Dropped <b>' . $taken['droped_courses_count']  . ' </b>  Courses with <b>' . $taken['droped_credit_sum']. ' </b> credits total)<br/>';
                                                
                                            echo 'Exempted: <b>'. $taken['exempted_credit_sum'] .'</b>  ( <b>' . $taken['exempted_course_count']  .' </b> course(s))' .'<br/>';
                                            echo 'Thesis taken: ';
                                            if ($taken['thesis_taken'] == 1) {
                                                echo '<b>Yes</b> ';
                                            } else {
                                                echo '<b> No</b> ';
                                            }
                                            
                                        } else {
                                            echo ' ---';
                                        } */
                                    ?>
                                </td>
                                <td class="vcenter"><?= $val['Student']['email']; ?></td>
                                <td  class="vcenter"><?= $val['Student']['phone_mobile']; ?></td>
                                <td class="center"><?= (($val['Student']['graduated'] == 1) ? 'Yes' : 'No'); ?></td>

                                <!-- <td class="center">
                                    <?php
                                    /*  if (isset($taken['photo_dirname']) && !empty($taken['photo_dirname'])) {
                                            if (!empty($taken['photo_basename']) && !empty($taken['photo_basename'])) {
                                                echo $this->Media->embed($this->Media->file($taken['photo_dirname'] . DS . $taken['photo_basename']), array('width' => '100'));
                                            }
                                        } else {
                                            echo '<img src="/img/noimage.jpg" width="100" class="profile-picture">';
                                        } */
                                    ?>
                                </td> -->

                                <!-- <td class="center">
                                    <?php
                                    /*  if (isset($taken['photo_dirname']) && !empty($taken['photo_dirname'])) {
                                            if (!empty($taken['photo_basename']) && !empty($taken['photo_basename'])) {
                                                echo $taken['photo_basename'];
                                            }
                                        } else {
                                            echo '---';
                                        } */
                                    ?>
                                </td> -->
                            </tr>
                            <?php
                        } ?>
                    </tbody>
                </table>
            </div>
            <?php
        } ?>
        <?php
    }
} ?>