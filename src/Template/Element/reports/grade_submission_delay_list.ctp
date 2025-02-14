<?php
if (isset($gradeSubmissionDelay) && !empty($gradeSubmissionDelay)) { ?>
    <h5><?= $headerLabel . ' <br> Date Generated: ' . $this->Time->format("F j, Y h:i:s A", date('Ymd H:i:s'), NULL, NULL); ?></h5>
    <table cellpadding="0" cellspacing="0" class="table table-hover">
        <thead>
            <tr>
                <td> # </td>
                <td style="text-align:center; vertical-align:middle"> Program </td>
                <td style="text-align:center; vertical-align:middle"> Program Type </td>
                <td style="text-align:center; vertical-align:middle"> Section </td>
                <td style="text-align:center; vertical-align:middle"> Year Level </td>
                <td style="text-align:center; vertical-align:middle"> Course </td>
                <td style="text-align:center; vertical-align:middle"> Assigned Instructor </td>
                <td style="text-align:center; vertical-align:middle"> Date Assigned </td>
                <td style="text-align:center; vertical-align:middle"> Instructor's Department </td>
                <td style="text-align:center; vertical-align:middle"> Deadline </td>
                <td style="text-align:center; vertical-align:middle"> Delay in days </td>
            </tr>
        </thead>
        <tbody>
            <?php
            $count = 0;
            foreach ($gradeSubmissionDelay as $departmentNamee => $courseList) {
                foreach ($courseList as $rkey => $rvalue) {
                    foreach ($rvalue as $mn => $ym) { ?>
                        <tr>
                            <td> <?= ++$count; ?> </td>
                            <td> <?= $ym['Section']['Program']['name']; ?> </td>
                            <td> <?= $ym['Section']['ProgramType']['name']; ?> </td>
                            <!-- <td> <?php //echo !isset($ym['Section']['YearLevel']) ? $ym['Section']['name'] . ' (Pre/1st)' : (!isset($ym['Section']['YearLevel']['name'])  ? $ym['Section']['name'] . ' (Pre/1st)' : $ym['Section']['name'] . ' (' . $ym['Section']['YearLevel']['name'] . ')'); 
                                        ?> </td> -->
                            <td style="text-align:center; vertical-align:middle"> <?= isset($ym['Section']['name']) ? $ym['Section']['name'] : 'N/A'; ?> </td>
                            <td style="text-align:center; vertical-align:middle"> <?= !isset($ym['Section']['YearLevel']) ? 'Pre/1st' : (!isset($ym['Section']['YearLevel']['name'])  ? 'Pre/1st' : $ym['Section']['YearLevel']['name']); ?> </td>
                            <td style="text-align:center; vertical-align:middle"> <?= $rkey; ?> </td>
                            <td style="text-align:center; vertical-align:middle"> <?= $ym['Staff']['Title']['title'] . ' ' . $ym['Staff']['full_name'] . ' (' . $ym['Staff']['Position']['position'] . ')'; ?> </td>
                            <td style="text-align:center; vertical-align:middle"> <?= (($ym['CourseInstructorAssignment']['created'] == $ym['CourseInstructorAssignment']['modified']) ? $this->Time->format("F j, Y h:i:s A", $ym['CourseInstructorAssignment']['created'], NULL, NULL) : ($this->Time->format("F j, Y h:i:s A", $ym['CourseInstructorAssignment']['modified'], NULL, NULL))); ?> </td>
                            <td style="text-align:center; vertical-align:middle"> <?= $departmentNamee; ?> </td>
                            <td style="text-align:center; vertical-align:middle;<?= $ym['CourseInstructorAssignment']['grade_submission_deadline'] > date('Y-m-d') ? 'color:green' : 'color:red' ?>">
                                <?= (($ym['CourseInstructorAssignment']['grade_submission_deadline'] == '0000-00-00 00:00:00' || $ym['CourseInstructorAssignment']['grade_submission_deadline'] == '' || is_null($ym['CourseInstructorAssignment']['grade_submission_deadline'])) ? 'Deadline not defined.' : ($this->Time->format("F j, Y", $ym['CourseInstructorAssignment']['grade_submission_deadline'], NULL, NULL))); ?>
                            </td>
                            <td style="text-align:center; vertical-align:middle;<?= $ym['CourseInstructorAssignment']['grade_submission_deadline'] < date('Y-m-d') ? 'color:red' : '' ?>">
                                <?php
                                if (isset($ym['CourseInstructorAssignment']['grade_submission_deadline']) && !empty($ym['CourseInstructorAssignment']['grade_submission_deadline'])) {
                                    $deadline = new DateTime($ym['CourseInstructorAssignment']['grade_submission_deadline']);
                                    $currentDate = new DateTime(date('Y-m-d'));
                                    echo (($ym['CourseInstructorAssignment']['grade_submission_deadline'] > date('Y-m-d')) ? '' : $currentDate->diff($deadline)->format("%a"));
                                }
                                ?>
                            </td>
                        </tr>
            <?php
                    }
                }
            }
            ?>
        </tbody>
    </table>
<?php
} ?>