<?php ?>
<div class="row">
    <div class="large-12 columns">
        <div
            class="tabs-content edumix-tab-horz">

            <?php

			echo '<h1>' . $student_academic_profile['BasicInfo']['Student']['full_name'] . '-' . $student_academic_profile['BasicInfo']['Student']['studentnumber'] . '</h1>';
			$student_copys = $student_academic_profile['Exam Result'];
			if (isset($student_copys) && !empty($student_copys)) {
			?>
            <style>
            .low_padding_table tr td {
                padding: 2px
            }
            </style>
            <table
                class="low_padding_table fs13">
                <tr>
                    <th
                        style="width:5%">
                        N<u>o</u></th>
                    <th
                        style="width:13%">
                        Course Code</th>
                    <th
                        style="width:35%">
                        Course Title
                    </th>
                    <th
                        style="width:10%; text-align:center">
                        Credit Hour</th>
                    <th
                        style="width:10%; text-align:center">
                        Grade</th>
                    <th>Curriculum</th>
                </tr>
                <?php
					$c_count = 0;
					$credit_hour_sum = 0;
					$grade_point_sum = 0;
					$takenCourses = array();
					$takenAndEquivalent = array();
					foreach ($student_copys as $index => $student_copy) {
						if (isset($student_copy['courses']) && !empty($student_copy['courses'])) {
					?>
                <?php
							foreach ($student_copy['courses'] as $key => $course_reg_add) {
								$c_count++;
								if (isset($course_reg_add['Grade']['grade'])) {
									if (isset($course_reg_add['Grade']['used_in_gpa']) && $course_reg_add['Grade']['used_in_gpa'] == 1) {
										$credit_hour_sum += $course_reg_add['Course']['credit'];
										$grade_point_sum += ($course_reg_add['Grade']['point_value'] * $course_reg_add['Course']['credit']);
									} else if (strcasecmp($course_reg_add['Grade']['grade'], 'I') == 0) {
										$credit_hour_sum += $course_reg_add['Course']['credit'];
									}
								} else {
									$credit_hour_sum += $course_reg_add['Course']['credit'];
								}
								$takenCourses[$course_reg_add['Course']['id']] = $course_reg_add['Course']['id'];



								$color = $course_reg_add['hasEquivalentMap'] ? 'green' : 'red';

								if ($course_reg_add['hasEquivalentMap']) {

									$takenAndEquivalent[$course_reg_add['Course']['id']] = $course_reg_add['Course']['id'];
								}


							?>
                <tr>

                    <td
                        style="color:<?php echo $color; ?>">
                        <?php echo $c_count; ?>
                    </td>
                    <td
                        style="color:<?php echo $color; ?>">
                        <?php echo $course_reg_add['Course']['course_code']; ?>
                    </td>
                    <td
                        style="color:<?php echo $color; ?>">
                        <?php echo $course_reg_add['Course']['course_title']; ?>
                    </td>
                    <td
                        style="text-align:center;color:<?php echo $color; ?>">
                        <?php echo $course_reg_add['Course']['credit']; ?>
                    </td>
                    <td
                        style="text-align:center;color:<?php echo $color; ?>">
                        <?php echo (isset($course_reg_add['Grade']['grade']) ? $course_reg_add['Grade']['grade'] : '---'); ?>
                    </td>

                    <td
                        style="color:<?php echo $color; ?>"><?php echo $course_reg_add['Course']['Curriculum']['curriculum_detail'] . '<br/>';

																			if (isset($course_reg_add['Course']['Curriculum']['english_degree_nomenclature'])) {
																				echo '(From:' . $course_reg_add['Course']['Curriculum']['english_degree_nomenclature'] . ')';
																			} else {

																				debug($course_reg_add);
																				echo '---';
																			}

																			?>

                    </td>

                </tr>
                <?php
							}
							?>

                <?php
						}
						?>



                <?php
					}
					?>

                <tr>
                    <td colspan="3"
                        style="text-align:right; font-weight:bold">
                        TOTAL</td>
                    <td
                        style="text-align:center; font-weight:bold">
                        <?php echo ($credit_hour_sum != 0 ? $credit_hour_sum : '---'); ?>
                    </td>
                    <td>&nbsp;</td>

                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>Green:</td>
                    <td colspan="5">
                        Course Taken
                        from Attached
                        Curriculum and
                        Equivalency Has
                        Mapped</td>
                </tr>
                <tr>
                    <td>Red:</td>
                    <td colspan="5">
                        Course Taken
                        from Another
                        Curriculum and
                        Equivalency Not
                        Done.</td>
                </tr>
            </table>

            <?php

			}
			?>
            <?php if (isset($student_academic_profile['BasicInfo']['Curriculum']['Course']) && !empty($student_academic_profile['BasicInfo']['Curriculum']['Course'])) { ?>
            <h1>List of courses not
                taken
                from curriculum </h1>
            <table
                class="low_padding_table fs13">
                <tr>
                    <th
                        style="width:5%">
                        N<u>o</u></th>
                    <th
                        style="width:13%">
                        Course Code</th>
                    <th
                        style="width:35%">
                        Course Title
                    </th>
                    <th
                        style="width:10%; text-align:center">
                        Credit Hour</th>

                    <th>Category</th>
                </tr>
                <?php
					$c = 1;
					foreach ($student_academic_profile['BasicInfo']['Curriculum']['Course'] as $kk => $pp) {

						if (in_array($pp['id'], $takenCourses) || in_array($pp['id'], $takenAndEquivalent)) {
					?>

                <?php
						} else {
						?>

                <tr>
                    <td><?php echo $c++; ?>
                    </td>
                    <td><?php echo $pp['course_code'] ?>
                    </td>
                    <td><?php echo $pp['course_title'] ?>
                    </td>
                    <td><?php echo $pp['credit'] ?>
                    </td>
                    <td><?php echo $pp['CourseCategory']['name']; ?>
                    </td>

                </tr>

                <?php

						}
					}

					?>
            </table>

            <?php } ?>

        </div>
    </div>
</div>