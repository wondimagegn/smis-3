<?php
if (isset($student_section_exam_status) && !empty($student_section_exam_status)) {  ?>
    <table cellpadding="0" cellspacing="0" class="table">
        <tr>
            <td>
                <div class="row">
                    <div class="large-7 columns">
                        <table cellpadding="0" cellspacing="0" class="table">
                            <tr>
                                <td class="font">
                                    <span class="text-gray">Full Name: </span>
                                    <?= (isset($student_section_exam_status['StudentBasicInfo']['full_name']) ? $student_section_exam_status['StudentBasicInfo']['full_name'] . ' &nbsp; &nbsp; &nbsp; &nbsp; ' .  $this->Html->link('Open Profile', array('controller' => 'students', 'action' => 'student_academic_profile', $student_section_exam_status['StudentBasicInfo']['id'])) : (isset($student_section_exam_status['Student']['full_name']) ? $student_section_exam_status['Student']['full_name']  . ' &nbsp; &nbsp; &nbsp; &nbsp; '.  $this->Html->link('Open Profile', array('controller' => 'students', 'action' => 'student_academic_profile', $student_section_exam_status['Student']['id'])) : '')); ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="font">
                                    <span class="text-gray">Student ID: </span>
                                    <?= (isset($student_section_exam_status['StudentBasicInfo']['studentnumber']) ? $student_section_exam_status['StudentBasicInfo']['studentnumber'] : (isset($student_section_exam_status['Student']['studentnumber']) ? $student_section_exam_status['Student']['studentnumber'] : '')); ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="font">
                                    <span class="text-gray">Sex: </span>
                                    <?= (isset($student_section_exam_status['StudentBasicInfo']['gender']) ? (ucfirst(strtolower(trim($student_section_exam_status['StudentBasicInfo']['gender'])))) :  (isset($student_section_exam_status['Student']['gender']) ? (ucfirst(strtolower(trim($student_section_exam_status['Student']['gender'])))) : '')); ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="font">
                                    <span class="text-gray">
                                        <?= (isset($student_section_exam_status['College']['type']) ? $student_section_exam_status['College']['type'].': ' : 'College: '); ?>
                                    </span>
                                    <?= $student_section_exam_status['College']['name']; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="font">
                                    <span class="text-gray">
                                        <?= (isset($student_section_exam_status['Department']['type']) ? $student_section_exam_status['Department']['type'].': ' : 'Department: '); ?>
                                    </span>
                                    <?= (isset($student_section_exam_status['Department']['name']) ? $student_section_exam_status['Department']['name'] : 'Pre/Freshman'); ?>
                                </td>
                            </tr>
                            <?php
                            if (isset($student_section_exam_status['Curriculum']['name'])) { ?>
                                <tr>
                                    <td class="font">
                                        <span class="text-gray">Attached Curriculum: </span>
                                        <?= $student_section_exam_status['Curriculum']['name'] . ' - '. $student_section_exam_status['Curriculum']['year_introduced']; ?>
                                    </td>
                                </tr>
                                <?php
                            } ?>
                            <tr>
                                <td class="font">
                                    <span class="text-gray">Program: </span>
                                    <?= (isset($student_section_exam_status['Program']['name']) ? $student_section_exam_status['Program']['name'] : 'N/A'); ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="font">
                                    <span class="text-gray">Program Type: </span>
                                    <?= (isset($student_section_exam_status['ProgramType']['name']) ? $student_section_exam_status['ProgramType']['name'] : 'N/A'); ?>
                                </td>
                            </tr>
                            <?php
                            if ($student_section_exam_status['StudentBasicInfo']['graduated'] == 0) { ?>
                                <tr>
                                    <td class="font">
                                        <span class="text-gray">Year Level: </span>
                                        <?= (isset($student_section_exam_status['Section']['YearLevel']['name']) ? $student_section_exam_status['Section']['YearLevel']['name'] . ' (' . $student_section_exam_status['Section']['academicyear'] . ')' : (isset($student_section_exam_status['Section']) && empty($student_section_exam_status['Section']['YearLevel']) ? 'Pre/1st' . ' (' . $student_section_exam_status['Section']['academicyear'] . ')' : '---')); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font">
                                        <span class="text-gray">Section: </span>
                                        <?= (isset($student_section_exam_status['Section']['name']) ? $student_section_exam_status['Section']['name'] . (!$student_section_exam_status['Section']['archive'] && !$student_section_exam_status['Section']['StudentsSection']['archive'] ? ' &nbsp;(<b class="accepted"> Current </b>)' : ' &nbsp;(<span class="rejected"> Previous </span>)') : '---'); ?>
                                    </td>
                                </tr>
                                <?php
                            }  else { ?>
                                <tr>
                                    <td class="font center">
                                        <span class="text-green">Graduated Student Profile</span>
                                    </td>
                                </tr>
                                <?php
                            } ?>
                        </table>
                        <br>
                    </div>

                    <div class="large-5 columns">
                        <?php
                        if (!empty($student_section_exam_status['StudentExamStatus'])) { ?>
                            <table cellpadding="0" cellspacing="0" class="table">
                                <thead>
                                    <tr>
                                        <td class="fs13"><b>Student Academic Status</b></td>
                                    </tr>
                                </thead>
                                <tr>
                                    <td class="font">
                                        <span class="text-gray">Academic Year: </span>
                                        <?= (isset($student_section_exam_status['StudentExamStatus']['academic_year']) ? $student_section_exam_status['StudentExamStatus']['academic_year'] : '---'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font">
                                        <span class="text-gray">Semester: </span>
                                        <?= (isset($student_section_exam_status['StudentExamStatus']['semester']) ? $student_section_exam_status['StudentExamStatus']['semester'] : '---'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font">
                                        <span class="text-gray">SGPA: </span>
                                        <?= (isset($student_section_exam_status['StudentExamStatus']['sgpa']) ? $student_section_exam_status['StudentExamStatus']['sgpa'] : '---'); ?>
                                    </td>
                                </tr>

                                <?php
                                if (!empty($student_section_exam_status['StudentExamStatus']['sgpa'])) { ?>
                                    <tr>
                                        <td class="font">
                                            <span class="text-gray">CGPA: </span>
                                            <?= (isset($student_section_exam_status['StudentExamStatus']['cgpa']) ? $student_section_exam_status['StudentExamStatus']['cgpa'] : '---'); ?>
                                        </td>
                                    </tr>
                                    <?php
                                }

                                if (!empty($student_section_exam_status['StudentExamStatus']['AcademicStatus'])) { ?>
                                    <tr>
                                        <td class="font">
                                            <span class="text-gray">Academic Status: </span>
                                            <?= ( $student_section_exam_status['StudentBasicInfo']['graduated'] == 1 ? '<span>Graduated</span>' : (isset($student_section_exam_status['StudentExamStatus']['AcademicStatus']) ? $student_section_exam_status['StudentExamStatus']['AcademicStatus']['name'] : '---')); ?>
                                        </td>
                                    </tr>
                                    <?php
                                } ?>
                            </table>
                            <?php
                        } ?>
                    </div>
                </div>
            </td>
        </tr>
    </table>
    <hr>
    <?php
} ?>