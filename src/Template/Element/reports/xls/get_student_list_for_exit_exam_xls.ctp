<?php 
/*
This file should be in app/views/elements/export_xls.ctp
Thanks to Marco Tulio Santos for this simple XLS Report
*/
header ("Expires: " . gmdate("D,d M YH:i:s") . " GMT");
header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");
header ("Content-type: application/vnd.ms-excel");
header ("Content-Disposition: attachment; filename=".$filename.".xls" );
header ("Content-Description: Exported as XLS" );
?>

<?php
if (isset($studentListForExitExam) && !empty($studentListForExitExam)) {
    foreach ($studentListForExitExam as $program => $programType) {
        foreach ($programType as $programTypeName => $statDetail) { ?>
            </p>
            <!-- <br/> -->
            <p class="fs16">
                <strong> <?= $program;  ?> , <?= $programTypeName; ?>  program type students for  <?= $this->data['Report']['acadamic_year']; ?>  Academic Year,
                    <?php
                    if ($this->data['Report']['semester'] == 'I') {
                        echo '1st ';
                    } else if ($this->data['Report']['semester'] == 'II') {
                        echo '2nd ';
                    } else if ($this->data['Report']['semester'] == 'III') {
                        echo '3rd ';
                    }
                    ?>
                Semester  </strong> <br>
            </p>

            <table>
                <thead>
                    <tr>
                        <td>
                            #
                        </td>

                        <td>
                            First Name
                        </td>

                        <td>
                            Middle Name
                        </td>

                        <td>
                            Last Name
                        </td>

                        <td style="text-align:center; vertical-align:middle">
                            Sex
                        </td>

                        <td style="text-align:center; vertical-align:middle">
                            Student ID
                        </td>

                        <td style="text-align:center; vertical-align:middle">
                            Student National ID
                        </td>

                        <td style="text-align:center; vertical-align:middle">
                            College
                        </td>

                        <td style="text-align:center; vertical-align:middle">
                            Department
                        </td>

                        <td style="text-align:center; vertical-align:middle">
                            Curriculum Name
                        </td>

                        <td style="text-align:center; vertical-align:middle">
                            Degree Nomenclature
                        </td>

                        <td style="text-align:center; vertical-align:middle">
                            Cr. Type
                        </td>

                        <td style="text-align:center; vertical-align:middle">
                            Min Cr. Req.
                        </td>

                        <td style="text-align:center; vertical-align:middle">
                            Curriculum Courses
                        </td>

                        <td style="text-align:center; vertical-align:middle">
                            Taken courses
                        </td>

                        <td style="text-align:center; vertical-align:middle">
                            Taken Credit
                        </td>

                        <td style="text-align:center; vertical-align:middle">
                            Registered courses
                        </td>

                        <td style="text-align:center; vertical-align:middle">
                            Registered Credit
                        </td>

                        <td style="text-align:center; vertical-align:middle">
                            Added courses
                        </td>

                        <td style="text-align:center; vertical-align:middle">
                            Added Credit
                        </td>

                        <td style="text-align:center; vertical-align:middle">
                            Dropped courses
                        </td>

                        <td style="text-align:center; vertical-align:middle">
                            Dropped Credit
                        </td>

                        <td style="text-align:center; vertical-align:middle">
                            Exempted courses
                        </td>

                        <td style="text-align:center; vertical-align:middle">
                            Exempted Credit
                        </td>

                        <td style="text-align:center; vertical-align:middle">
                            Thesis Taken
                        </td>

                        <td style="text-align:center; vertical-align:middle">
                            Thesis Credit
                        </td>

                        <td style="text-align:center; vertical-align:middle">
                            Year
                        </td>

                        <td style="text-align:center; vertical-align:middle">
                            CrHr Sum
                        </td>

                        <td style="text-align:center; vertical-align:middle">
                            SGPA
                        </td>

                        <td style="text-align:center; vertical-align:middle">
                            CGPA
                        </td>

                        <td style="text-align:center; vertical-align:middle">
                            Rem Cr.
                        </td>

                        <td style="text-align:center; vertical-align:middle">
                            Email
                        </td>

                        <td style="text-align:center; vertical-align:middle">
                            Mobile
                        </td>

                        <td style="text-align:center; vertical-align:middle">
                            Graduated
                        </td>

                        

                    </tr>
                </thead>

                <tbody>
                    <?php

                    $count = 0;

                    foreach ($statDetail as $in => $val) {

                        $taken = ClassRegistry::init('StudentExamStatus')->getStudentTakenCreditsForHeims($val['Student']['id']); ?>

                        <tr>
                            <td>
                                <?= ++$count; ?>
                            </td>

                            <td>
                                <?= $val['Student']['first_name']; ?>
                            </td>

                            <td>
                                <?= $val['Student']['middle_name']; ?>
                            </td>

                            <td>
                                <?= $val['Student']['last_name']; ?>
                            </td>

                            <td style="text-align:center; vertical-align:middle">
                                <?php
                                if (strcasecmp($val['Student']['gender'], 'male') == 0) {
                                    echo 'M';
                                } else if (strcasecmp($val['Student']['gender'], 'female') == 0) {
                                    echo 'F';
                                } else {
                                    echo $val['Student']['gender'];
                                }
                                ?>
                            </td>

                            <td style="text-align:center; vertical-align:middle">
                                <?= $val['Student']['studentnumber']; ?>
                            </td>

                            <td style="text-align:center; vertical-align:middle">
                                <?= (isset($val['Student']['student_national_id']) && !empty($val['Student']['student_national_id']) ? $val['Student']['student_national_id'] : '---'); ?>
                            </td>

                            <td style="vertical-align:middle">
                                <?php
                                if (isset($val['Student']['College']['name']) && !empty($val['Student']['College']['name'])) {
                                    echo $val['Student']['College']['name'];
                                }
                                ?>
                            </td>

                            <td style="vertical-align:middle">
                                <?php
                                if (isset($val['Student']['Department']['name']) && !empty($val['Student']['Department']['name'])) {
                                    echo $val['Student']['Department']['name'];
                                } else {
                                    echo 'Pre/Freshman';
                                }
                                ?>
                            </td>

                            <td style="vertical-align:middle">
                                <?php
                                if (isset($val['Student']['Curriculum']['name']) && !empty($val['Student']['Curriculum']['name'])) {
                                    echo $val['Student']['Curriculum']['name'];
                                } else {
                                    echo '---';
                                }
                                ?>
                            </td>

                            <td style="vertical-align:middle;">
                                <?php
                                if (isset($val['Student']['Curriculum']['english_degree_nomenclature']) && !empty($val['Student']['Curriculum']['english_degree_nomenclature'])) {
                                    echo $val['Student']['Curriculum']['english_degree_nomenclature'];
                                } else {
                                    echo '---';
                                }
                                ?>
                            </td>

                            <td style="text-align:center; vertical-align:middle">
                                <?php
                                if (isset($val['Student']['Curriculum']['type_credit']) && !empty($val['Student']['Curriculum']['type_credit'])) {
                                    echo $val['Student']['Curriculum']['type_credit'];
                                } else {
                                    echo '---';
                                }
                                ?>
                            </td>

                            <td style="text-align:center; vertical-align:middle">
                                <?php
                                if (isset($val['Student']['Curriculum']['minimum_credit_points']) && !empty($val['Student']['Curriculum']['minimum_credit_points'])) {
                                    echo $val['Student']['Curriculum']['minimum_credit_points'];
                                } else {
                                    echo '---';
                                }
                                ?>
                            </td>

                            <td style="text-align:center; vertical-align:middle">
                                <?php
                                //$all_curriculum_courses = $taken['curriculum_major_course_count'] + $taken['curriculum_minor_course_count'];
                                echo $taken['curriculum_major_course_count'] + $taken['curriculum_minor_course_count'];
                                //echo ' ( Major: ' . $taken['curriculum_major_course_count']  . ' Minor: ' . $taken['curriculum_minor_course_count']. ' )';
                                ?>
                            </td>

                            <td style="text-align:center; vertical-align:middle">
                                <?= ((isset($taken['taken_course_count']) && $taken['taken_course_count'] != 0) ? $taken['taken_course_count'] : '---');  ?>
                            </td>

                            <td style="text-align:center; vertical-align:middle">
                                <?= ((isset($taken['credit_sum']) && $taken['credit_sum'] != 0) ? $taken['credit_sum'] : '---'); ?>
                            </td>



                            <td style="text-align:center; vertical-align:middle">
                                <?= ((isset($taken['course_count_registration']) && $taken['course_count_registration'] != 0) ? $taken['course_count_registration'] : '---');  ?>
                            </td>

                            <td style="text-align:center; vertical-align:middle">
                                <?= ((isset($taken['credit_sum_registration']) && $taken['credit_sum_registration'] != 0) ? $taken['credit_sum_registration'] : '---'); ?>
                            </td>

                            <td style="text-align:center; vertical-align:middle">
                                <?= ((isset($taken['course_count_add']) && $taken['course_count_add'] != 0) ? $taken['course_count_add'] : '---'); ?>
                            </td>

                            <td style="text-align:center; vertical-align:middle">
                                <?= ((isset($taken['credit_sum_add']) && $taken['credit_sum_add'] != 0) ? $taken['credit_sum_add'] : '---'); ?>
                            </td>

                            <td style="text-align:center; vertical-align:middle">
                                <?= ((isset($taken['droped_courses_count']) && $taken['droped_courses_count'] != 0) ? $taken['droped_courses_count'] : '---');  ?>
                            </td>

                            <td style="text-align:center; vertical-align:middle">
                                <?= ((isset($taken['droped_credit_sum']) && $taken['droped_credit_sum'] != 0) ? $taken['droped_credit_sum'] : '---'); ?>
                            </td>

                            <td style="text-align:center; vertical-align:middle">
                                <?= ((isset($taken['exempted_course_count']) && $taken['exempted_course_count'] != 0) ? $taken['exempted_course_count'] : '---'); ?>
                            </td>

                            <td style="text-align:center; vertical-align:middle">
                                <?= ((isset($taken['exempted_credit_sum']) && $taken['exempted_credit_sum'] != 0) ? $taken['exempted_credit_sum'] : '---'); ?>
                            </td>

                            <td style="text-align:center; vertical-align:middle">
                                <?php
                                echo (isset($taken['thesis_taken']) && $taken['thesis_taken'] == 1) ? 'Yes' : 'No';
                                ?>
                            </td>

                            <td style="text-align:center; vertical-align:middle">
                                <?php
                                if ($taken['thesis_taken'] == 1 && isset($taken['thesis_credit'])) {
                                    echo $taken['thesis_credit'];
                                } else {
                                    echo '---';
                                }
                                // echo (isset($taken['thesis_credit']) && $taken['thesis_credit'] != 0 ) ? $taken['thesis_credit'] : '---' ;
                                ?>
                            </td>

                            <td style="text-align:center; vertical-align:middle">
                                <?= $val['Student']['yearLevel']; ?>
                            </td>

                            <td style="text-align:center; vertical-align:middle">
                                <?= $val['StudentExamStatus']['credit_hour_sum']; ?>
                            </td>

                            <td style="text-align:center; vertical-align:middle">
                                <?= $val['StudentExamStatus']['sgpa']; ?>
                            </td>

                            <td style="text-align:center; vertical-align:middle">
                                <?= $val['StudentExamStatus']['cgpa']; ?>
                            </td>

                            <td style="text-align:center; vertical-align:middle">
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

                                ?>
                            </td>

                            <td>
                                <?= $val['Student']['email']; ?>
                            </td>

                            <td>
                                <?= $val['Student']['phone_mobile']; ?>
                            </td>

                            <td style="text-align:center; vertical-align:middle">
                                <?= (($val['Student']['graduated'] == 1) ? 'Yes' : 'No'); ?>
                            </td>

                        </tr>
                        <?php
                    } ?>
                </tbody>
            </table>
            <?php
        } 
    }
}
?>