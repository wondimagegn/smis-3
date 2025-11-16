<?php
if (isset($studentSectionExamStatus) && !empty($studentSectionExamStatus)) {
    ?>
    <table cellpadding="0" cellspacing="0" class="table">
        <tr>
            <td>
                <div class="row">
                    <div class="large-7 columns">
                        <table cellpadding="0" cellspacing="0" class="table">
                            <tr>
                                <td class="font">
                                    <span class="text-gray">Full Name: </span>
                                    <?= (isset($studentSectionExamStatus['StudentBasicInfo']['full_name']) ?
                                        $studentSectionExamStatus['StudentBasicInfo']['full_name'] .
                                        ' &nbsp; &nbsp; &nbsp; &nbsp; ' .  $this->Html->link('Open Profile',
                                            array('controller' => 'students', 'action' => 'studentAcademicProfile',
                                                $studentSectionExamStatus['StudentBasicInfo']['id'])) :
                                        (isset($studentSectionExamStatus['Student']['full_name']) ?
                                            $studentSectionExamStatus['Student']['full_name']  .
                                            ' &nbsp; &nbsp; &nbsp; &nbsp; '.  $this->Html->link('Open Profile',
                                                array('controller' => 'students', 'action' => 'studentAcademicProfile',
                                                    $studentSectionExamStatus['Student']['id'])) : '')); ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="font">
                                    <span class="text-gray">Student ID: </span>
                                    <?= (isset($studentSectionExamStatus['StudentBasicInfo']['studentnumber']) ?
                                        $studentSectionExamStatus['StudentBasicInfo']['studentnumber'] :
                                        (isset($studentSectionExamStatus['Student']['studentnumber']) ?
                                            $studentSectionExamStatus['Student']['studentnumber'] : '')); ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="font">
                                    <span class="text-gray">Sex: </span>
                                    <?= (isset($studentSectionExamStatus['StudentBasicInfo']['gender']) ?
                                        (ucfirst(strtolower(trim($studentSectionExamStatus['StudentBasicInfo']['gender']))))
                                        :  (isset($studentSectionExamStatus['Student']['gender'])
                                            ? (ucfirst(strtolower(trim($studentSectionExamStatus['Student']['gender'])))) :
                                            '')); ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="font">
                                    <span class="text-gray">
                                        <?= (isset($studentSectionExamStatus['College']['type']) ?
                                            $studentSectionExamStatus['College']['type'].': ' : 'College: '); ?>
                                    </span>
                                    <?= $studentSectionExamStatus['College']['name']; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="font">
                                    <span class="text-gray">
                                        <?= (isset($studentSectionExamStatus['Department']['type']) ?
                                            $studentSectionExamStatus['Department']['type'].': ' : 'Department: '); ?>
                                    </span>
                                    <?= (isset($studentSectionExamStatus['Department']['name']) ?
                                        $studentSectionExamStatus['Department']['name'] : 'Pre/Freshman'); ?>
                                </td>
                            </tr>
                            <?php
                            if (isset($studentSectionExamStatus['Curriculum']['name'])) { ?>
                                <tr>
                                    <td class="font">
                                        <span class="text-gray">Attached Curriculum: </span>
                                        <?= $studentSectionExamStatus['Curriculum']['name'] . ' - '.
                                        $studentSectionExamStatus['Curriculum']['year_introduced']; ?>
                                    </td>
                                </tr>
                                <?php
                            } ?>
                            <tr>
                                <td class="font">
                                    <span class="text-gray">Program: </span>
                                    <?= (isset($studentSectionExamStatus['Program']['name']) ?
                                        $studentSectionExamStatus['Program']['name'] : 'N/A'); ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="font">
                                    <span class="text-gray">Program Type: </span>
                                    <?= (isset($studentSectionExamStatus['ProgramType']['name']) ?
                                        $studentSectionExamStatus['ProgramType']['name'] : 'N/A'); ?>
                                </td>
                            </tr>
                            <?php
                            if ($studentSectionExamStatus['StudentBasicInfo']['graduated'] == 0) { ?>
                                <tr>
                                    <td class="font">
                                        <span class="text-gray">Year Level: </span>
                                        <?= (isset($studentSectionExamStatus['Section']['year_level']['name']) ?
                                            $studentSectionExamStatus['Section']['year_level']['name'] . ' (' .
                                            $studentSectionExamStatus['Section']['academic_year'] . ')' :
                                            (isset($studentSectionExamStatus['Section']) &&
                                            empty($studentSectionExamStatus['Section']['year_level']) ? 'Pre/1st' .
                                                ' (' . $studentSectionExamStatus['Section']['academic_year'] . ')' :
                                                '---')); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font">
                                        <span class="text-gray">Section: </span>
                                        <?= (isset($studentSectionExamStatus['Section']['name']) ?
                                            $studentSectionExamStatus['Section']['name'] .
                                            (!$studentSectionExamStatus['Section']['archive'] &&
                                            !$studentSectionExamStatus['Section']['archive'] ? ' &nbsp;
 (<b class="accepted"> Current </b>)' : ' &nbsp;(<span class="rejected"> Previous </span>)') : '---'); ?>
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
                        if (!empty($studentSectionExamStatus['StudentExamStatus'])) {    ?>
                            <table cellpadding="0" cellspacing="0" class="table">
                                <thead>
                                    <tr>
                                        <td class="fs13"><b>Student Academic Status</b></td>
                                    </tr>
                                </thead>
                                <tr>
                                    <td class="font">
                                        <span class="text-gray">Academic Year: </span>
                                        <?= (isset($studentSectionExamStatus['StudentExamStatus']['academic_year']) ?
                                            $studentSectionExamStatus['StudentExamStatus']['academic_year'] : '---'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font">
                                        <span class="text-gray">Semester: </span>
                                        <?= (isset($studentSectionExamStatus['StudentExamStatus']['semester']) ?
                                            $studentSectionExamStatus['StudentExamStatus']['semester'] : '---'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font">
                                        <span class="text-gray">SGPA: </span>
                                        <?= (isset($studentSectionExamStatus['StudentExamStatus']['sgpa']) ?
                                            $studentSectionExamStatus['StudentExamStatus']['sgpa'] : '---'); ?>
                                    </td>
                                </tr>

                                <?php
                                if (!empty($studentSectionExamStatus['StudentExamStatus']['sgpa'])) { ?>
                                    <tr>
                                        <td class="font">
                                            <span class="text-gray">CGPA: </span>
                                            <?= (isset($studentSectionExamStatus['StudentExamStatus']['cgpa']) ?
                                                $studentSectionExamStatus['StudentExamStatus']['cgpa'] : '---'); ?>
                                        </td>
                                    </tr>
                                    <?php
                                }

                                if (!empty($studentSectionExamStatus['StudentExamStatus']['AcademicStatus'])) { ?>
                                    <tr>
                                        <td class="font">
                                            <span class="text-gray">Academic Status: </span>
                                            <?= ( $studentSectionExamStatus['StudentBasicInfo']['graduated'] == 1 ?
                                                '<span>Graduated</span>' :
                                                (isset($studentSectionExamStatus['StudentExamStatus']['AcademicStatus']) ?
                                                    $studentSectionExamStatus['StudentExamStatus']['AcademicStatus']['name']
                                                    : '---')); ?>
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
