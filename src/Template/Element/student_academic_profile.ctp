<script src="/js/clipboard.min.js"></script>
<script>
    $(function() {
        new Clipboard('.copy-text');
    });
</script>
<?php

use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\I18n\FrozenTime;

$graduated = $studentAcademicProfile['BasicInfo']['Student']['graduated'];
$admissionYear = $studentAcademicProfile['BasicInfo']['Student']['admissionyear'] ?? null;

$userRole = $this->request->getSession()->read('Auth.User.role_id');
$isAdmin=$this->request->getSession()->read('Auth.User.is_admin') ;
if (isset($studentSectionExamStatus['StudentBasicInfo'])
    && is_null($studentSectionExamStatus['StudentBasicInfo']['curriculum_id'])
    && !is_null($studentSectionExamStatus['StudentBasicInfo']['department_id'])) {

    if ($userRole == ROLE_STUDENT) { ?>
        <div class='warning-box warning-message'><span style='margin-right: 15px;'></span>
            <i style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
                Your profile is not attached to any curriculum. Please communicate your department to attach a curriculum to your profile.</i>
        </div>
        <?php
    } else if ($userRole == ROLE_DEPARTMENT) { ?>
        <div class='warning-box warning-message'><span style='margin-right: 15px;'></span><i style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><?= $studentAcademicProfile['BasicInfo']['Student']['full_name'] . ' ('. $studentAcademicProfile['BasicInfo']['Student']['studentnumber'] .')'; ?> is not attached to any curriculum. Please <a href="/acceptedStudents/attach_curriculum" target="_blank">Attach a Curriculum to student's profile</a>, set the filters: <?= $studentSectionExamStatus['StudentBasicInfo']['academicyear'] ?> as admission year, <?= $studentAcademicProfile['BasicInfo']['Program']['name']; ?> as program and <?= $studentAcademicProfile['BasicInfo']['ProgramType']['name']; ?> as program type.</i></div>
        <?php
    } else if ($userRole== ROLE_REGISTRAR) { ?>
        <div class='warning-box warning-message'><span style='margin-right: 15px;'></span><i style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><?= $studentAcademicProfile['BasicInfo']['Student']['full_name'] . ' ('. $studentAcademicProfile['BasicInfo']['Student']['studentnumber'] .')'; ?> is not attached to any curriculum. Please communicate student department to attach a curriculum to student's profile.</i></div>
        <?php
    } else { ?>
        <div class='warning-box warning-message'><span style='margin-right: 15px;'></span><i style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><?= $studentAcademicProfile['BasicInfo']['Student']['full_name'] . ' ('. $studentAcademicProfile['BasicInfo']['Student']['studentnumber'] .')'; ?> is not attached to any curriculum.</i></div>
        <?php
    }

}


if (isset($studentSectionExamStatus) && $isTheStudentDismissed && !$isTheStudentReadmitted) {
    if ($userRole == ROLE_STUDENT) { ?>
        <div class='info-box info-message'><span style='margin-right: 15px;'></span><i style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">You're dismmised in <?= ($studentSectionExamStatus['StudentExamStatus']['semester'] == 'I' ? '1st' : ($studentSectionExamStatus['StudentExamStatus']['semester'] == 'II' ? '2nd' : ($studentSectionExamStatus['StudentExamStatus']['semester'] == 'III' ? '3rd' : $studentSectionExamStatus['StudentExamStatus']['semester']))) ; ?> semester of <?= $studentSectionExamStatus['StudentExamStatus']['academic_year']; ?> academic year. Please advise the registrar for readmission if applicable.</i></div>
        <?php
    } else if ($userRole== ROLE_DEPARTMENT) { ?>
        <div class='info-box info-message'><span style='margin-right: 15px;'></span><i style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><?= $studentAcademicProfile['BasicInfo']['Student']['full_name'] . ' ('. $studentAcademicProfile['BasicInfo']['Student']['studentnumber'] .')'; ?> is  dismmised in <?= ($studentSectionExamStatus['StudentExamStatus']['semester'] == 'I' ? '1st' : ($studentSectionExamStatus['StudentExamStatus']['semester'] == 'II' ? '2nd' : ($studentSectionExamStatus['StudentExamStatus']['semester'] == 'III' ? '3rd' : $studentSectionExamStatus['StudentExamStatus']['semester']))) ; ?> semester of <?= $studentSectionExamStatus['StudentExamStatus']['academic_year']; ?> academic year. Please advise the student for readmission if applicable.</i></div>
        <?php
    } else if ($userRole == ROLE_REGISTRAR) { ?>
        <div class='info-box info-message'><span style='margin-right: 15px;'></span><i style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><?= $studentAcademicProfile['BasicInfo']['Student']['full_name'] . ' ('. $studentAcademicProfile['BasicInfo']['Student']['studentnumber'] .')'; ?> is  dismmised in <?= ($studentSectionExamStatus['StudentExamStatus']['semester'] == 'I' ? '1st' : ($studentSectionExamStatus['StudentExamStatus']['semester'] == 'II' ? '2nd' : ($studentSectionExamStatus['StudentExamStatus']['semester'] == 'III' ? '3rd' : $studentSectionExamStatus['StudentExamStatus']['semester']))) ; ?> semester of <?= $studentSectionExamStatus['StudentExamStatus']['academic_year']; ?> academic year. Please advise the student for readmission if applicable.</i></div>
        <?php
    } else { ?>
        <div class='info-box info-message'><span style='margin-right: 15px;'></span><i style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><?= $studentAcademicProfile['BasicInfo']['Student']['full_name'] . ' ('. $studentAcademicProfile['BasicInfo']['Student']['studentnumber'] .')'; ?> is  dismmised in <?= ($studentSectionExamStatus['StudentExamStatus']['semester'] == 'I' ? '1st' : ($studentSectionExamStatus['StudentExamStatus']['semester'] == 'II' ? '2nd' : ($studentSectionExamStatus['StudentExamStatus']['semester'] == 'III' ? '3rd' : $studentSectionExamStatus['StudentExamStatus']['semester']))) ; ?> semester of <?= $studentSectionExamStatus['StudentExamStatus']['academic_year']; ?> academic year. Please advise the student for readmission if applicable.</i></div>
        <?php
    }

} else if (isset($studentSectionExamStatus) && empty($studentSectionExamStatus['Section']) && !$graduated) {

    if (isset($studentSectionExamStatus['StudentExamStatus']) && !empty($studentSectionExamStatus['StudentExamStatus']['academic_year'])) {
        if ($academicYR == $studentSectionExamStatus['StudentExamStatus']['academic_year']) {
            // nothing
        } else {
            $academicYR = (explode('/', $studentSectionExamStatus['StudentExamStatus']['academic_year'])[0] + 1) . '/'. (explode('/', $studentSectionExamStatus['StudentExamStatus']['academic_year'])[1] + 1);
        }
    }

    if ($userRole== ROLE_STUDENT) { ?>
        <div class='info-box info-message'><span style='margin-right: 15px;'></span><i style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">You're Section-less for <?= $academicYR; ?> academic year. Please, advise your <?= is_null($studentAcademicProfile['BasicInfo']['Student']['department_id']) || empty($studentAcademicProfile['BasicInfo']['Student']['department_id']) ? 'freshman coordinator' :   'department'; ?> for section assignment.</i></div>
        <?php
    } else if ($userRole == ROLE_DEPARTMENT) { ?>
        <div class='info-box info-message'><span style='margin-right: 15px;'></span><i style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><?= $studentAcademicProfile['BasicInfo']['Student']['full_name'] . ' ('. $studentAcademicProfile['BasicInfo']['Student']['studentnumber'] .')'; ?> is  Section-less for <?= $academicYR; ?>. Please assign the student in appropraite section created for <?= $academicYR; ?> academic year.</i></div>
        <?php
    } else if ($userRole== ROLE_REGISTRAR) { ?>
        <div class='info-box info-message'><span style='margin-right: 15px;'></span><i style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><?= $studentAcademicProfile['BasicInfo']['Student']['full_name'] . ' ('. $studentAcademicProfile['BasicInfo']['Student']['studentnumber'] .')'; ?> is  Section-less for <?= $academicYR; ?>. The student must have an appropraite section assignment in section created for <?= $academicYR; ?> academic year.</i></div>
        <?php
    } else { ?>
        <div class='info-box info-message'><span style='margin-right: 15px;'></span><i style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><?= $studentAcademicProfile['BasicInfo']['Student']['full_name'] . ' ('. $studentAcademicProfile['BasicInfo']['Student']['studentnumber'] .')'; ?> is  Section-less for <?= $academicYR; ?>. The student must have an appropraite section assignment in section created for <?= $academicYR; ?> academic year.</i></div>
        <?php
    }
}
?>

<div class="row">
    <div class="large-12 columns">
        <?php
        $credit_type = 'Credit';

        if (isset($studentAcademicProfile['Curriculum']['type_credit']) && !empty($studentAcademicProfile['Curriculum']['type_credit'])) {
            $crtype = explode('ECTS',$studentAcademicProfile['Curriculum']['type_credit']);
            //debug($crtype);
            if (count($crtype) >= 2){
                $credit_type = 'ECTS';
            }
        }

        ?>
        <!-- tabs -->
        <ul class="tabs" data-tab>
            <li class="tab-title active">
                <a href="#basicinformation">Basic</a>
            </li>
            <li class="tab-title">
                <a href="#exemption">Exemptions</a>
            </li>
            <li class="tab-title">
                <a href="#registration">Registrations</a>
            </li>
            <li class="tab-title">
                <a href="#addcourses">Course Adds</a>
            </li>
            <li class="tab-title">
                <a href="#dropcourses">Course Drops</a>
            </li>
            <li class="tab-title">
                <a href="#examresults">Results</a>
            </li>
            <li class="tab-title">
                <a href="#curriculum">Curriculum</a>
            </li>
            <li class="tab-title">
                <a href="#Billing">Billing</a>
            </li>
            <?php
            if (SHOW_OTP_TAB_ON_STUDENT_ACADEMIC_PROFILE_FOR_STUDENTS == 1 &&
                ($userRole == ROLE_STUDENT ||
                    $userRole== ROLE_SYSADMIN ||
                    $userRole == ROLE_DEPARTMENT ||
                    $userRole== ROLE_REGISTRAR ||
                    $userRole == ROLE_GENERAL) && isset($otps) && !empty($otps)) { ?>
                <li class="tab-title">
                    <a href="#OTP">OTPs</a>
                </li>
                <?php
            } ?>
        </ul>

        <div class="tabs-content edumix-tab-horz">
            <div class="content active" id="basicinformation" style="padding-left: 0px; padding-right: 0px;">
                <hr style="margin-top: -10px;">
                <?php
                if (!empty($studentAcademicProfile)) { ?>
                    <div class="row">
                        <!-- <div class="AddTab"> -->
                        <!-- <table cellspacing="0" cellpading="0" class="table-borderless">
                            <tr>
                                <td> -->
                        <div class="large-6 columns" style="padding: 0.7rem;">
                            <table cellspacing="0" cellpading="0" class="table">
                                <tbody>
                                <tr>
                                    <td colspan=2><strong>Demographic Information</strong></td>
                                </tr>
                                <tr>
                                    <td style="padding-left:30px;">First Name: <strong id="copySTFN" class="copy-text" data-clipboard-target="#copySTFN" title="Click here once to copy text"><?= $studentAcademicProfile['BasicInfo']['Student']['first_name']; ?></strong></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td style="padding-left:30px;">ስም: <strong id="copySTAFN" class="copy-text" data-clipboard-target="#copySTAFN" title="Click here once to copy text"><?= $studentAcademicProfile['BasicInfo']['Student']['amharic_first_name']; ?></strong></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td style="padding-left:30px;">Middle Name: <strong id="copySTMN" class="copy-text" data-clipboard-target="#copySTMN" title="Click here once to copy text"><?= $studentAcademicProfile['BasicInfo']['Student']['middle_name']; ?></strong></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td style="padding-left:30px;">የአባት ስም: <strong id="copySTAMN" class="copy-text" data-clipboard-target="#copySTAMN" title="Click here once to copy text"><?= $studentAcademicProfile['BasicInfo']['Student']['amharic_middle_name']; ?></strong></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td style="padding-left:30px;">Last Name: <strong id="copySTLN" class="copy-text" data-clipboard-target="#copySTLN" title="Click here once to copy text"><?= $studentAcademicProfile['BasicInfo']['Student']['last_name']; ?></strong></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td style="padding-left:30px;">የአያት ስም: <strong id="copySTALN" class="copy-text" data-clipboard-target="#copySTALN" title="Click here once to copy text"><?= $studentAcademicProfile['BasicInfo']['Student']['amharic_last_name']; ?></strong></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td style="padding-left:30px;">Sex: <strong><?= (strcasecmp(trim($studentAcademicProfile['BasicInfo']['Student']['gender']), 'male') == 0 ?  'Male' : ((strcasecmp(trim($studentAcademicProfile['BasicInfo']['Student']['gender']), 'female') == 0) ? 'Female' : ''/* $studentAcademicProfile['BasicInfo']['Student']['gender'] */)); ?></strong></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td style="padding-left:30px;">Student ID: <strong id="copySTID" class="copy-text" data-clipboard-target="#copySTID" title="Click here once to copy text"><?= $studentAcademicProfile['BasicInfo']['Student']['studentnumber']; ?></strong></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td style="padding-left:30px;">Birth Date: <?= (isset($studentAcademicProfile['BasicInfo']['Student']['birthdate']) ? $this->Time->format("M j, Y", $studentAcademicProfile['BasicInfo']['Student']['birthdate'], NULL, NULL) : '---'); ?></td>
                                    <td></td>
                                </tr>
                                <?php
                                if ($this->Session->read('Auth.User')['role_id'] != ROLE_STUDENT) { ?>
                                    <tr>
                                        <td style="padding-left:30px;">ID Card Printed: <?= ((!isset($studentAcademicProfile['BasicInfo']['Student']['print_count']) && !empty($studentAcademicProfile['BasicInfo']['Student']['print_count'])) || (isset($studentAcademicProfile['BasicInfo']['Student']['print_count']) && $studentAcademicProfile['BasicInfo']['Student']['print_count'] == 0 ) ?  'No' : (($studentAcademicProfile['BasicInfo']['Student']['print_count'] == 1) ? '1 time' : $studentAcademicProfile['BasicInfo']['Student']['print_count'] . ' times')); ?></td>
                                        <td></td>
                                    </tr>
                                    <?php
                                } ?>
                                <tr>
                                    <td style="padding-left:30px;">National Student ID: <?= (!empty($studentAcademicProfile['BasicInfo']['Student']['student_national_id']) ? '<strong id="copySTNTID" class="copy-text" data-clipboard-target="#copySTNTID" title="Click here once to Student National ID(MoE)">' . $studentAcademicProfile['BasicInfo']['Student']['student_national_id'] . '</strong>' : '---'); ?>

                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td style="padding-left:30px;">Fayda ID (FAN): <?= (!empty($studentAcademicProfile['BasicInfo']['Student']['faida_alias_number']) ? '<strong id="copySTFAN" class="copy-text" data-clipboard-target="#copySTFAN" title="Click here once Fayda FAN">' . $studentAcademicProfile['BasicInfo']['Student']['faida_alias_number'] . '</strong>' : 'N/A'); ?></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td style="padding-left:30px;">Fayda ID (FIN): <?= (!empty($studentAcademicProfile['BasicInfo']['Student']['faida_identification_number']) ? '<strong id="copySTFIN" class="copy-text" data-clipboard-target="#copySTFIN" title="Click here once Fayda FIN">' . $studentAcademicProfile['BasicInfo']['Student']['faida_identification_number'] . '</strong>' : 'N/A'); ?></td>
                                    <td></td>
                                </tr>
                                <?php
                                if (($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR || $this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN)  && !empty($studentAcademicProfile['BasicInfo']['Student']['region_id'])) {
                                    if (!empty($studentAcademicProfile['BasicInfo']['Student']['country_id']) && $studentAcademicProfile['BasicInfo']['Student']['country_id'] != COUNTRY_ID_OF_ETHIOPIA) { ?>
                                        <tr>
                                            <td style="padding-left:30px;">Country: <?= $studentAcademicProfile['BasicInfo']['Country']['name']; ?></td>
                                            <td></td>
                                        </tr>
                                        <?php
                                    } ?>
                                    <tr>
                                        <td style="padding-left:30px;">Region: <?= $studentAcademicProfile['BasicInfo']['Region']['name']; ?></td>
                                        <td></td>
                                    </tr>
                                    <?php
                                }

                                $prevSection = array();
                                $movedOrDeletedSectionsFromRegistration = array();
                                $sectionLess = true;
                                $section_ids_with_reg = array();
                                //$graduated = $studentAcademicProfile['BasicInfo']['Student']['graduated'];
                                //debug($graduated);

                                if (isset($studentAcademicProfile['BasicInfo']['Student']['id'])) {
                                    // Retrieve section IDs for the student from CourseRegistrations
                                    $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
                                    $section_ids_with_reg = $movedOrDeletedSectionsFromRegistration =
                                        $courseRegistrationsTable->getAllSectionIdsForStudentFromCourseRegistrations(
                                            $studentAcademicProfile['BasicInfo']['Student']['id']);

                                }



                                if(isset($studentAttendedSections) && !empty($studentAttendedSections)){ ?>
                                    <tr>
                                        <td colspan=2><strong>Attended Sections</strong></td>
                                    </tr>
                                    <?php
                                    foreach ($studentAttendedSections as $index => $student_copys) {

                                        if ($prevSection != $student_copys->id) { ?>
                                            <tr>
                                                <td style="padding-left: 30px;" class="vcenter">
                                                    <?php
                                                    if (!empty($student_copys->year_level->name)) {
                                                        echo '<span id="copySECTION_' . $index . '" class="copy-text" data-clipboard-target="#copySECTION_' . $index . '" >'
                                                            . $student_copys->name.
                                                            '</span> (' .
                                                            $student_copys->year_level->name . ', ' .
                                                            (!empty($student_copys->academicyear) ? '' .
                                                                $student_copys->academicyear . ')' : '');
                                                    } else {
                                                        echo '<span id="copySECTION_' . $index . '" class="copy-text" data-clipboard-target="#copySECTION_' . $index . '" >'.
                                                            $student_copys->name . '</span> (Pre/1st, ' .
                                                            (!empty($student_copys->academicyear) ? '' .
                                                                $student_copys->academicyear . ')' : '');
                                                    }
                                                    echo $student_copys->archive == true ? ' Previous' : ' <b>Current</b>';
                                                    ?>
                                                </td>
                                                <td style="padding-left: 30px;" class="vcenter">
                                                    <?php
                                                    if ($student_copys->archive == false) {
                                                        $sectionLess = false;
                                                    }

                                                    if (!$graduated && in_array($student_copys->id,
                                                            $section_ids_with_reg) &&
                                                        ($userRole == ROLE_REGISTRAR || $userRole == ROLE_COLLEGE ||
                                                            $userRole == ROLE_DEPARTMENT ||
                                                            $userRole == ROLE_SYSADMIN)) {
                                                        if (!$student_copys->archive) {


                                                            echo $this->Html->link(__('Archieve'), [
                                                                'controller' => 'Sections',
                                                                'action' => 'archieveUnarchieveStudentSection',
                                                                $student_copys->id,
                                                                $studentAcademicProfile['BasicInfo']['Student']['id'],
                                                                1
                                                            ], [
                                                                'confirm' => sprintf(__('Are you sure you want to archive %s from %s section? The current section will be labeled as (Previous) and student will be section-less so that you can add them to a new section.'),
                                                                    $studentAcademicProfile['BasicInfo']['Student']['full_name'] . ' (' . $studentAcademicProfile['BasicInfo']['Student']['studentnumber'] . ')',
                                                                    $student_copys->name
                                                                )
                                                            ]);
                                                            echo '<br/>';


                                                        }
                                                    }

                                                    if (!$graduated && !in_array($student_copys->id, $section_ids_with_reg)
                                                        && ($userRole == ROLE_REGISTRAR ||
                                                            $userRole == ROLE_COLLEGE || $userRole == ROLE_DEPARTMENT
                                                            || $userRole == ROLE_SYSADMIN)) {
                                                        if (!$student_copys->archive) {
                                                            echo $this->Html->link('Move', '#',
                                                                array('data-animation' => "fade", 'data-reveal-id' => 'myModalAdd',
                                                                    'data-reveal-ajax' => '/sections/moveStudentSectionToNew/' .
                                                                        $student_copys->id . '/' .
                                                                        $studentAcademicProfile['BasicInfo']['Student']['id']));
                                                            echo '<br/>';
                                                        }

                                                        echo $this->Html->link(
                                                            __('Delete'),
                                                            [
                                                                'controller' => 'Sections',
                                                                'action' => 'deleteStudent',
                                                                $student_copys->id,
                                                                str_replace('/', '-', $studentAcademicProfile['BasicInfo']['Student']['studentnumber'])
                                                            ],
                                                            [
                                                                'confirm' => __(
                                                                    'Are you sure you want to delete {0} from {1} section?',
                                                                    $studentAcademicProfile['BasicInfo']['Student']['full_name'] . ' (' .
                                                                    $studentAcademicProfile['BasicInfo']['Student']['studentnumber'] . ')',
                                                                    $student_copys->name
                                                                )
                                                            ]
                                                        );
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php
                                            $prevSection = $student_copys->id;
                                        }

                                        if (! empty($section_ids_with_reg) && in_array($student_copys->id,
                                                $section_ids_with_reg)) {
                                            if (! empty($movedOrDeletedSectionsFromRegistration)) {
                                                foreach ($movedOrDeletedSectionsFromRegistration as $sec_key => $sec_value) {
                                                    if ($sec_value == $student_copys->id) {
                                                        unset($movedOrDeletedSectionsFromRegistration[$sec_key]);
                                                    }
                                                    //debug($movedOrDeletedSectionsFromRegistration);
                                                }
                                            }
                                        }

                                    }
                                }

                                if (!empty($movedOrDeletedSectionsFromRegistration) &&
                                    isset($studentAttendedSections) && !empty($studentAttendedSections)) {
                                    foreach ($movedOrDeletedSectionsFromRegistration as $seckey => $sevalue) {

                                        // Retrieve the deleted section with associated YearLevel
                                        $sectionsTable = TableRegistry::getTableLocator()->get('Sections');
                                        $deletedSection = $sectionsTable->find()
                                            ->where(['Sections.id' => $sevalue])
                                            ->contain(['YearLevels'])
                                            ->first();

                                        if (!empty($deletedSection)) { ?>
                                            <tr>
                                                <td style="padding-left: 30px;" class="vcenter rejected">
                                                    <?php
                                                    if (!empty($deletedSection->year_level->name)) {
                                                        echo $deletedSection->name . ' (' .
                                                            $deletedSection->year_level->name . ')' . ', ' .
                                                            (!empty($deletedSection->academicyear) ? '' .
                                                                $deletedSection->academicyear . ')' : '');
                                                    } else {
                                                        echo $deletedSection->name . ' (Pre/1st, ' .
                                                            (!empty($deletedSection->academicyear) ? '' .
                                                                $deletedSection->academicyear . ')' : '');
                                                    }
                                                    $archieve_value = ($deletedSection->archive == true ?  1 : 0);
                                                    ?>
                                                </td>

                                                <td style="padding-left: 30px;" class="vcenter">
                                                    <?php



                                                    if (!$graduated && ($userRole == ROLE_REGISTRAR ||
                                                            $userRole == ROLE_COLLEGE || $userRole == ROLE_DEPARTMENT
                                                            || $userRole == ROLE_SYSADMIN)) {
                                                        $fullName = h(
                                                            $studentAcademicProfile['BasicInfo']['Student']['full_name'] ?? ''
                                                        );
                                                        $studentNumber = h(
                                                            $studentAcademicProfile['BasicInfo']['Student']['studentnumber'] ?? ''
                                                        );
                                                        $confirmMessage = sprintf(
                                                            __('You are about to restore %s as %s section for %s? %s'),
                                                            h($deletedSection->name ?? ''),
                                                            $archieve_value ? 'previous' : 'current',
                                                            $fullName . ' (' . $studentNumber . ')',
                                                            $archieve_value ? '' : 'Restoring this section as a current section will archive any other existing current sections of the student as previous. Are you sure you want to proceed?'
                                                        );

                                                        echo $this->Html->link(
                                                            __('Restore'),
                                                            [
                                                                'controller' => 'Sections',
                                                                'action' => 'restore_student_section',
                                                                $deletedSection->id,
                                                                $studentAcademicProfile['BasicInfo']['Student']['id'],
                                                                $archieve_value
                                                            ],
                                                            ['confirm' => $confirmMessage]
                                                        );



                                                    } ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                }

                                if ($userRole== ROLE_DEPARTMENT || $userRole == ROLE_COLLEGE  || $userRole == ROLE_REGISTRAR ||
                                    ($userRole == ROLE_SYSADMIN && $isAdmin == 1)) {
                                    if ($sectionLess && !$graduated && (!$isTheStudentDismissed || $isTheStudentReadmitted) /* && isset($studentAttendedSections) && !empty($studentAttendedSections) */ && ((!empty($studentAcademicProfile['BasicInfo']['Student']['department_id']) && !empty($studentAcademicProfile['BasicInfo']['Student']['curriculum_id'])) || (empty($studentAcademicProfile['BasicInfo']['Student']['department_id']) && empty($studentAcademicProfile['BasicInfo']['Student']['curriculum_id'])))) { ?>
                                        <tr>
                                            <td style="padding-left: 30px;" class="vcenter">

                                                <?=$this->Html->link(
                                                    'Add Student To Section',
                                                    '#',
                                                    [
                                                        'data-animation' => 'fade',
                                                        'data-reveal-id' => 'myModalAdd',
                                                        'data-reveal-ajax' => $this->Url->build([
                                                            'controller' => 'Sections',
                                                            'action' => 'addStudentToSection',
                                                            $studentAcademicProfile['BasicInfo']['Student']['id']
                                                        ])
                                                    ]
                                                ); ?></td>
                                            <td></td>
                                        </tr>
                                        <?php
                                    }
                                }

                                if ($userRole== ROLE_REGISTRAR && !$graduated && isset($studentAttendedSections) && !empty($studentAttendedSections)) {
                                    if ((!$isTheStudentDismissed || $isTheStudentReadmitted) && !empty($section_ids_with_reg)) { ?>
                                        <tr>
                                            <td style="padding-left: 30px;" class="vcenter">


                                                <?=$this->Html->link(
                                                    'Manage Missing Registration & NG',
                                                    '#',
                                                    [
                                                        'data-animation' => 'fade',
                                                        'data-reveal-id' => 'myModalReg',
                                                        'data-reveal-ajax' => $this->Url->build([
                                                            'controller' => 'CourseRegistrations',
                                                            'action' => 'manageMissingRegistration',
                                                            $studentAcademicProfile['BasicInfo']['Student']['id']
                                                        ])
                                                    ]
                                                ); ?>



                                            </td>
                                            <td></td>
                                        </tr>
                                        <?php
                                    }
                                    if (isset($studentAcademicProfile['BasicInfo']['Student']['department_id'])
                                        && !is_null($studentAcademicProfile['BasicInfo']['Student']['department_id']) &&
                                        (!$isTheStudentDismissed || $isTheStudentReadmitted) &&
                                        $studentAcademicProfile['BasicInfo']['Student']['program_id'] != PROGRAM_REMEDIAL) { ?>
                                        <tr>
                                            <td style="padding-left: 30px;" class="vcenter">



                                                <?=$this->Html->link(
                                                    'Add Transferred Courses from other University',
                                                    '#',
                                                    [
                                                        'data-animation' => 'fade',
                                                        'data-reveal-id' => 'myModalAdd',
                                                        'data-reveal-ajax' => $this->Url->build([
                                                            'controller' => 'CourseExemptions',
                                                            'action' => 'addStudentExemptedCourse',
                                                            $studentAcademicProfile['BasicInfo']['Student']['id']
                                                        ])
                                                    ]
                                                ); ?>

                                            </td>
                                            <td></td>
                                        </tr>
                                        <?php
                                    }
                                    if (($isTheStudentDismissed || $isTheStudentReadmitted || (isset($isStudentEverReadmitted)
                                                && !empty($isStudentEverReadmitted) && $isStudentEverReadmitted > 0))
                                        && $studentAcademicProfile['BasicInfo']['Student']['program_id'] != PROGRAM_REMEDIAL) { ?>
                                        <tr>
                                            <td style="padding-left: 30px;" class="vcenter"><?= $this->Html->link(
                                                    'Maintain Readmission',
                                                    '#',
                                                    [
                                                        'data-animation' => 'fade',
                                                        'data-reveal' => 'myModalAdd',
                                                        'data-reveal-ajax' => $this->Url->build([
                                                            'controller' => 'Readmissions',
                                                            'action' => 'ajax_readmitted_year',
                                                            $studentAcademicProfile['BasicInfo']['Student']['id']
                                                        ])
                                                    ]
                                                );?></td>
                                            <td></td>
                                        </tr>
                                        <?php
                                    }
                                }

                                if ($graduated) {
                                    // Retrieve the graduate list record for the student
                                    $graduateListsTable = TableRegistry::getTableLocator()->get('GraduateLists');
                                    $checkInGraduateList = $graduateListsTable->find()
                                        ->where(
                                            ['GraduateLists.student_id' => $studentAcademicProfile['BasicInfo']['Student']['id']]
                                        )
                                        ->disableHydration()
                                        ->first();


                                    if (!empty($checkInGraduateList)) {
                                        if ($userRole == ROLE_SYSADMIN || ($userRole== ROLE_REGISTRAR && $isAdmin == 1)) {

                                            ?>
                                            <tr>
                                                <td colspan=2>
                                                    <div class="warning-box warning-message" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
                                                        <span style='margin-right: 15px;'></span> You are accessing graduated student profile<br/>

                                                        <p>

                                                        <ul>
                                                            <li>Date Graduated: <b><?=$this->Time->format($checkInGraduateList['graduate_date'], 'MMM d, yyyy'); ?></b></li>
                                                            <li>Minute No: <b><?= $checkInGraduateList['minute_number']; ?></b></li>
                                                            <li>Date Added: <b><?= $this->Time->format($checkInGraduateList['created'], 'MMM d, yyyy');; ?></b></li>
                                                        </ul>
                                                        </p>

                                                        <?php

                                                        $graduation_date = $checkInGraduateList['graduate_date'];

                                                        if (Configure::read('Calendar.graduateApprovalInPast')) {
                                                            //debug(Configure::read('Calendar.graduateApprovalInPast'));
                                                            $days_back = Configure::read('Calendar.graduateApprovalInPast') * 365;
                                                        } else {
                                                            $days_back = 1 * 365;
                                                        }
                                                        echo '<pre>';
                                                        print_r($days_back);
                                                        echo '</pre>';
                                                      //  debug($days_back);

                                                        $minimum_allowed_graduation_date_to_delete = date('Y-m-d',
                                                            strtotime("-" . $days_back . " day "));
                                                        //debug($minimum_allowed_graduation_date_to_delete);

                                                        echo '<pre>';
                                                        print_r($minimum_allowed_graduation_date_to_delete);
                                                        echo '</pre>';

                                                        if ($minimum_allowed_graduation_date_to_delete < $graduation_date) {
                                                            $fullName = h($studentAcademicProfile['BasicInfo']['Student']['full_name'] ?? '');
                                                            $studentNumber = h($studentAcademicProfile['BasicInfo']['Student']['studentnumber'] ?? '');
                                                            $gender = isset($studentAcademicProfile['BasicInfo']['Student']['gender']) ?
                                                                (strtolower(trim($studentAcademicProfile['BasicInfo']['Student']['gender'])) === 'male' ? 'him' : 'her') : 'them';
                                                            $confirmMessage = sprintf(
                                                                __('Are you sure you want to delete "%s (%s)" from both Graduate and Senate Lists? Deleting the student from these lists requires recording the minute number and graduation dates for adding %s back to the lists if you are deleting the student for some sort of correction. Are you sure you want to proceed?'),
                                                                $fullName,
                                                                $studentNumber,
                                                                $gender
                                                            );

                                                            echo $this->Html->link(
                                                                __('Delete Student from Graduate List'),
                                                                [
                                                                    'controller' => 'Students', // Adjust if action is in a different controller
                                                                    'action' => 'delete_student_from_graduate_list_for_correction',
                                                                    $studentAcademicProfile['BasicInfo']['Student']['id']
                                                                ],
                                                                ['confirm' => $confirmMessage]
                                                            );
                                                        } ?>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                        } else { ?>
                                            <tr>
                                                <td colspan=2>
                                                    <div class="warning-box warning-message" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
                                                        <span style='margin-right: 15px;'></span> You are accessing graduated student profile<br/>
                                                        <p>
                                                        <ul>
                                                            <li>Date Graduated: <b><?= $this->Time->format("M j, Y", $checkInGraduateList['graduate_date'], NULL, NULL); ?></b></li>
                                                        </ul>
                                                        </p>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    } else { ?>
                                        <tr>
                                            <td colspan=2>
                                                <div class="error-box error-message" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;">
                                                    <span style='margin-right: 15px;'></span>There is a student profile error, please contact system administrator for a fix.
                                                </div>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="large-6 columns" style="padding: 0.7rem;">
                            <table cellspacing="0" cellpading="0" class="table">
                                <tbody>
                                <tr>
                                    <td><strong>Student Photo</strong></td>
                                </tr>
                                <?php
                                if (isset($studentAcademicProfile['BasicInfo']['Attachment']) && !empty($studentAcademicProfile['BasicInfo']['Attachment'])) {
                                    ?>
                                    <tr>
                                        <td class="vcenter" style="background-color: white;">
                                            <?php

                                            if (
                                            !empty($studentAcademicProfile['BasicInfo']['Attachment'][0]['dirname']) &&
                                            !empty($studentAcademicProfile['BasicInfo']['Attachment'][0]['basename']) &&
                                            file_exists(WWW_ROOT . $studentAcademicProfile['BasicInfo']['Attachment'][0]['dirname'] . DS . $studentAcademicProfile['BasicInfo']['Attachment'][0]['basename'])
                                            ) {
                                            echo $this->Html->image(
                                            $studentAcademicProfile['BasicInfo']['Attachment'][0]['dirname'] . DS . $studentAcademicProfile['BasicInfo']['Attachment'][0]['basename'],
                                            ['width' => '144', 'class' => 'profile-picture', 'alt' => 'Student Profile']
                                            );
                                            } else {
                                            echo $this->Html->image(
                                            '/img/noimage.jpg',
                                            ['width' => '144', 'class' => 'profile-picture', 'alt' => 'No Image']
                                            );
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                    /* }
                                } */
                                } else { ?>
                                    <tr>
                                        <td class="vcenter" style="background-color: white;"><img src="/img/noimage.jpg" width="144" class="profile-picture"></td>
                                    </tr>
                                    <?php
                                } ?>
                                <tr>
                                    <td><strong>Access Information</strong></td>
                                </tr>
                                <?php
                                if (isset($studentAcademicProfile['BasicInfo']['User']['username'])) { ?>
                                    <tr>
                                        <td style="padding-left:30px;">Username: <?= $studentAcademicProfile['BasicInfo']['User']['username']; ?></td>
                                    </tr>
                                    <tr>
                                        <td style="padding-left:30px;">Last Login: <?= (($studentAcademicProfile['BasicInfo']['User']['last_login'] == '' ||  $studentAcademicProfile['BasicInfo']['User']['last_login'] == '0000-00-00 00:00:00' || is_null($studentAcademicProfile['BasicInfo']['User']['last_login'])) ? '<span class="rejected">Never loggedin</span>' : $this->Time->timeAgoInWords($studentAcademicProfile['BasicInfo']['User']['last_login'], array('format' => 'M j, Y', 'end' => '1 year', 'accuracy' => array('month' => 'month')))); ?></td>
                                    </tr>
                                    <tr>
                                        <td style="padding-left:30px;">Last Password Change: <?= (($studentAcademicProfile['BasicInfo']['User']['last_password_change_date'] == '' ||  $studentAcademicProfile['BasicInfo']['User']['last_password_change_date'] == '0000-00-00 00:00:00' || is_null($studentAcademicProfile['BasicInfo']['User']['last_password_change_date'])) ? '<span class="rejected">Never Changed</span>' : ($studentAcademicProfile['BasicInfo']['User']['force_password_change'] == 1 ?  '<span class="rejected">Not changed since last password issue/reset.</span>' : $this->Time->timeAgoInWords($studentAcademicProfile['BasicInfo']['User']['last_password_change_date'], array('format' => 'M j, Y', 'end' => '1 year', 'accuracy' => array('month' => 'month'))))); ?></td>
                                    </tr>
                                    <tr>
                                        <td style="padding-left:30px;">Failed Logins: <?= (isset($studentAcademicProfile['BasicInfo']['User']['failed_login']) && $studentAcademicProfile['BasicInfo']['User']['failed_login'] != 0  ?  $studentAcademicProfile['BasicInfo']['User']['failed_login'] : '---'); ?></td>
                                    </tr>
                                    <tr>
                                        <td style="padding-left:30px;">Ecardnumber: <?= (isset($studentAcademicProfile['BasicInfo']['Student']['ecardnumber']) && !empty($studentAcademicProfile['BasicInfo']['Student']['ecardnumber']) ? $studentAcademicProfile['BasicInfo']['Student']['ecardnumber'] : '---'); ?></td>
                                    </tr>
                                    <?php
                                    if (($this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR || $this->Session->read('Auth.User')['role_id'] == ROLE_SYSADMIN) && !$graduated) { ?>
                                        <tr>
                                            <td class="center"><?= ($studentAcademicProfile['BasicInfo']['User']['active'] == 1 ? $this->Html->link(__('Disable System Access', true), array('action' => 'activate_deactivate_profile', $studentAcademicProfile['BasicInfo']['Student']['id']), array('confirm' => __('Are you sure you want revoke system access of %s (%s)? Disabling System Access will prevent the student from logging in to ' . Configure::read('ApplicationShortName'). ' and perform some basic task like Registring for courses, adding courses, viewing results and evaluating instructors etc. Are you sure you want proceed?', $studentAcademicProfile['BasicInfo']['Student']['full_name'], $studentAcademicProfile['BasicInfo']['Student']['studentnumber']))) : $this->Html->link(__('Enable System Access', true), array('action' => 'activate_deactivate_profile', $studentAcademicProfile['BasicInfo']['Student']['id']), array('confirm' => __('Are you sure you want grant back system access of %s (%s)? Enabling System Access will allow the student to logging in to ' . Configure::read('ApplicationShortName'). ' as before and perform some basic task like Registring for courses, adding courses, viewing results and evaluating instructors etc. Are you sure you want proceed?', $studentAcademicProfile['BasicInfo']['Student']['full_name'], $studentAcademicProfile['BasicInfo']['Student']['studentnumber'])))); ?></td>
                                        </tr>
                                        <?php
                                    }
                                } else { ?>
                                    <tr>
                                        <td style="padding-left:30px;" class="on-process">Username and password is not issued by the <?= (!is_null($studentAcademicProfile['BasicInfo']['Student']['department_id']) ? (isset($studentAcademicProfile['BasicInfo']['Department']['type']) && !empty($studentAcademicProfile['BasicInfo']['Department']['type']) ? $studentAcademicProfile['BasicInfo']['Department']['type'] : 'Department') : ((isset($studentAcademicProfile['BasicInfo']['College']['type']) && !empty($studentAcademicProfile['BasicInfo']['College']['type']) ? $studentAcademicProfile['BasicInfo']['College']['type'] : 'College'))); ?></td>
                                    </tr>
                                    <?php
                                }

                                $preEngineeringColleges = Configure::read('preengineering_college_ids');

                                if ($studentAcademicProfile['BasicInfo']['Student']['program_id'] == PROGRAM_REMEDIAL) {
                                    $stream = 'Remedial Program';
                                } else if (isset($studentAcademicProfile['BasicInfo']['College']['stream']) && $studentAcademicProfile['BasicInfo']['College']['stream'] == STREAM_NATURAL && in_array($studentAcademicProfile['BasicInfo']['Student']['college_id'], $preEngineeringColleges)) {
                                    $stream = 'Freshman - Pre Engineering';
                                } else if (isset($studentAcademicProfile['BasicInfo']['College']['stream']) && $studentAcademicProfile['BasicInfo']['College']['stream'] == STREAM_NATURAL) {
                                    $stream = 'Freshman - Natural Stream';
                                } else if (isset($studentAcademicProfile['BasicInfo']['College']['stream']) && $studentAcademicProfile['BasicInfo']['College']['stream'] == STREAM_SOCIAL) {
                                    $stream = 'Freshman - Social Stream';
                                } else {
                                    $stream = '---';
                                } ?>

                                <tr>
                                    <td><strong>Classification of Admission</strong></td>
                                </tr>
                                <tr>
                                    <td style="padding-left:30px;">Program: <?= $studentAcademicProfile['BasicInfo']['Program']['name']; ?></td>
                                </tr>
                                <tr>
                                    <td style="padding-left:30px;">Program Type: <?= $studentAcademicProfile['BasicInfo']['ProgramType']['name']; ?></td>
                                </tr>
                                <tr>
                                    <td style="padding-left:30px;"><?= (isset($studentAcademicProfile['BasicInfo']['College']['type']) && !empty($studentAcademicProfile['BasicInfo']['College']['type']) ? $studentAcademicProfile['BasicInfo']['College']['type'].': ' : 'College: '); ?><span id="copySTCOL" class="copy-text" data-clipboard-target="#copySTCOL" title="Click here once to copy text"><?= $studentAcademicProfile['BasicInfo']['College']['name']; ?></span></td>
                                </tr>
                                <tr>
                                    <td style="padding-left:30px;"><?= (isset($studentAcademicProfile['BasicInfo']['Department']['type']) && !empty($studentAcademicProfile['BasicInfo']['Department']['type']) ? $studentAcademicProfile['BasicInfo']['Department']['type'].': ' : 'Department: '); ?><span id="copySTDEPT" class="copy-text" data-clipboard-target="#copySTDEPT" title="Click here once to copy text"><?= (isset($studentAcademicProfile['BasicInfo']['Student']['department_id']) && !is_null($studentAcademicProfile['BasicInfo']['Student']['department_id']) ? $studentAcademicProfile['BasicInfo']['Department']['name'] : $stream); ?></span></td>
                                </tr>
                                <tr>
                                    <td style="padding-left:30px;">Admission Year: <?= (isset($studentAcademicProfile['BasicInfo']['Student']['academicyear']) ? $studentAcademicProfile['BasicInfo']['Student']['academicyear'] : '---'); ?></td>
                                </tr>
                                <tr>
                                    <td style="padding-left:30px;">Date Admitted: <?= $admissionYear
                                            ? (new FrozenTime($admissionYear))->format('M j, Y')
                                            : 'N/A' ?></td>
                                </tr>
                                <tr>
                                    <td class="center"><?= ($studentAcademicProfile['BasicInfo']['Student']['admissionyear'] < '2019-09-20' ? $this->Html->link('View Preferences', '#', array('data-animation' => "fade",
                                            'data-reveal-id' => 'myModalPref',
                                            'data-reveal-ajax' => '/preferences/get_student_preference/' . $studentAcademicProfile['BasicInfo']['Student']['accepted_student_id'])) : $this->Html->link('View Preferences', '#',
                                            array('data-animation' => "fade", 'data-reveal-id' => 'myModalPref',
                                                'data-reveal-ajax' => '/placementPreferences/getStudentPreference/' .
                                                    $studentAcademicProfile['BasicInfo']['Student']['id']))); ?></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                    <?php
                } ?>
            </div>

            <div class="content" id="exemption" style="padding-left: 0px; padding-right: 0px;">
                <hr style="margin-top: -10px;">
                <!-- <div class="AddTab"> -->
                <?php
                if (!empty($studentAcademicProfile['CourseExemption'])) { ?>
                    <div style="overflow-x:auto;">
                        <table cellpadding="0" cellspacing="0" class="table">
                            <thead>
                            <?php
                            if (!empty($studentAcademicProfile['CourseExemption'][0])) { ?>
                                <tr>
                                    <th colspan="6">From: <?= strtoupper($studentAcademicProfile['CourseExemption'][0]['transfer_from']); ?>
                                </tr>
                                <?php
                            } else { ?>
                                <tr>
                                    <th colspan="6">Course Transfered University is not added, <a href="#"> Add University </a>
                                </tr>
                                <?php
                            } ?>
                            <tr>
                                <th class="center">#</th>
                                <th class="vcenter">Taken Course</th>
                                <th class="center">Cr.</th>
                                <th class="center">Gr.</th>
                                <th class="vcenter">Exempted By</th>
                                <th class="center">Cr.</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $exemtcnt = 1;
                            $exempted_course_sum = 0;
                            foreach ($studentAcademicProfile['CourseExemption'] as $in => $value) { ?>
                                <tr>
                                    <td class="center"><?= $exemtcnt++; ?></td>
                                    <td class="vcenter"><?= (trim($value['taken_course_title']) . ' (' . (trim($value['taken_course_code'])) . ')'); ?></td>
                                    <td class="center"><?= $value['course_taken_credit']; ?></td>
                                    <td class="center"><?= (isset($value['grade']) && !empty($value['grade']) ? ($value['grade']) : ''); ?></td>
                                    <td class="venter"><?= (trim($value['Course']['course_title']) . ' (' . (trim($value['Course']['course_code'])) . ')'); ?></td>
                                    <td class="center"><?= $value['Course']['credit']; ?></td>
                                </tr>
                                <?php
                                if (is_numeric($value['Course']['credit'])) {
                                    $exempted_course_sum += $value['Course']['credit'];
                                }
                            } ?>
                            <tr>
                                <td colspan="5" style="text-align:right;font-weight: bold">Total:</td><td style="text-align:center;font-weight: bold"><?= $exempted_course_sum; ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <?php
                } else { ?>
                    <div class="info-box info-message" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style="margin-right: 15px;"></span>There is no record of course exemption for the selected student.</div>
                    <?php
                } ?>
                <!-- </div> -->
            </div>

            <div class="content" id="registration" style="padding-left: 0px; padding-right: 0px;">
                <hr style="margin-top: -10px;">
                <!-- <div class="AddTab"> -->
                <?php
                if (!empty($studentAcademicProfile['Course Registered'])) { ?>
                    <div style="overflow-x:auto;">
                        <table cellpadding="0" cellspacing="0" class="table">
                            <thead>
                            <tr>
                                <td class="center">#</td>
                                <td class="vcenter">Course</td>
                                <td class="center"><?= $credit_type; ?></td>
                                <td class="center">ACY</td>
                                <td class="center">Semester</td>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $total_registered_credits = 0;
                            $regcnt = 1;
                            foreach ($studentAcademicProfile['Course Registered'] as $in => $value) { ?>
                                <tr>
                                    <td class="center"><?= $regcnt++; ?></td>
                                    <td class="vcenter"><?= $value['course_title']; ?></td>
                                    <td class="center"><?= $value['credit']; ?></td>
                                    <td class="center"><?= $value['acadamic_year']; ?></td>
                                    <td class="center"><?= $value['semester']; ?></td>
                                </tr>
                                <?php
                                if (is_numeric($value['credit'])) {
                                    $total_registered_credits += $value['credit'];
                                }
                            } ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td>&nbsp;</td>
                                <td style="text-align: right; vertical-align: middle;">Total</td>
                                <td class="center"><?= $total_registered_credits; ?></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                    <?php
                } else { ?>
                    <div class="info-box info-message" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style="margin-right: 15px;"></span>There is no record of course registration for the selected student.</div>
                    <?php
                } ?>
                <!-- </div> -->
            </div>

            <div class="content" id="addcourses" style="padding-left: 0px; padding-right: 0px;">
                <hr style="margin-top: -10px;">
                <!-- <div class="AddTab"> -->
                <?php
                if (!empty($studentAcademicProfile['Course Added'])) { ?>
                    <div style="overflow-x:auto;">
                        <table cellpadding="0" cellspacing="0" class="table">
                            <thead>
                            <tr>
                                <td class="center">#</td>
                                <td class="vcenter">Course</td>
                                <td class="center"><?= $credit_type; ?></td>
                                <td class="center">ACY</td>
                                <td class="center">Semester</td>
                                <td class="center">Section</td>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $total_added_credits = 0;
                            $addcnt = 1;
                            foreach ($studentAcademicProfile['Course Added'] as $in => $value) { ?>
                                <tr>
                                    <td class="center"><?= $addcnt++; ?></td>
                                    <td class="vcenter"><?= $value['course_title']; ?></td>
                                    <td class="center"><?= $value['credit']; ?></td>
                                    <td class="center"><?= $value['acadamic_year']; ?></td>
                                    <td class="center"><?= $value['semester']; ?></td>
                                    <td class="vcenter"><?= $value['sectionName'] . ' (' . $value['curriculumName'] . ')'; ?></td>
                                </tr>
                                <?php
                                if (is_numeric($value['credit'])) {
                                    $total_added_credits += $value['credit'];
                                }
                            } ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td>&nbsp;</td>
                                <td style="text-align: right; vertical-align: middle;">Total</td>
                                <td class="center"><?= $total_added_credits; ?></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                    <?php
                } else { ?>
                    <div class="info-box info-message" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style="margin-right: 15px;"></span>There is no record of course add for the selected student.</div>
                    <?php
                } ?>
                <!-- </div> -->
            </div>

            <div class="content" id="dropcourses" style="padding-left: 0px; padding-right: 0px;">
                <hr style="margin-top: -10px;">
                <!-- <div class="AddTab"> -->
                <?php
                if (!empty($studentAcademicProfile['Course Dropped'])) { ?>
                    <div style="overflow-x:auto;">
                        <table cellpadding="0" cellspacing="0" class="table">
                            <thead>
                            <tr>
                                <td class="center">#</td>
                                <td class="vcenter">Course</td>
                                <td class="center"><?= $credit_type; ?></td>
                                <td class="center">ACY</td>
                                <td class="center">Semester</td>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $total_dropped_credits = 0;
                            $dropcnt = 1;
                            foreach ($studentAcademicProfile['Course Dropped'] as $in => $value) { ?>
                                <tr>
                                    <td class="center"><?= $dropcnt++; ?></td>
                                    <td class="venter"><?= $value['course_title']; ?></td>
                                    <td class="center"><?= $value['credit']; ?></td>
                                    <td class="center"><?= $value['acadamic_year']; ?></td>
                                    <td class="center"><?= $value['semester']; ?></td>
                                </tr>
                                <?php
                                if (is_numeric($value['credit'])) {
                                    $total_dropped_credits += $value['credit'];
                                }
                            } ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td>&nbsp;</td>
                                <td style="text-align: right; vertical-align: middle;">Total</td>
                                <td class="center"><?= $total_dropped_credits; ?></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                    <?php
                } else { ?>
                    <div class="info-box info-message" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style="margin-right: 15px;"></span>There is no record of course drop for the selected student.</div>
                    <?php
                } ?>
                <!-- </div> -->
            </div>

            <div class="content" id="examresults" style="padding-left: 0px; padding-right: 0px;">
                <hr style="margin-top: -10px;">
                <!-- <div class="AddTab"> -->
                <?php
                if (($userRole == ROLE_STUDENT && isset($show_results_tab) && $show_results_tab) ||
                    $userRole != ROLE_STUDENT) {


                    if (!empty($studentAcademicProfile['Course Registered']) || !empty($studentAcademicProfile['Course Added'])
                        || !empty($student_copys)) {
                        ?>
                        <?= $this->element('grade_report_organized_by_ac_semester'); ?>
                        <?php
                    } else { ?>
                        <div class="info-box info-message" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style="margin-right: 15px;"></span>There is no exam result record for the selected student.</div>
                        <?php
                    }
                } else if ($userRole == ROLE_STUDENT && isset($show_results_tab) && !$show_results_tab) { ?>
                    <div class="info-box info-message" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style="margin-right: 15px;"></span>Please <a href="/studentEvalutionRates/add">evaluate your instructors</a> first before checking your latest results!</div>
                    <?php
                } ?>
                <!-- </div> -->
            </div>

            <div class="content" id="curriculum" style="padding-left: 0px; padding-right: 0px;">
                <hr style="margin-top: -10px;">
                <!-- <div class="AddTab"> -->
                <?php

                if (!empty($studentAcademicProfile['BasicInfo']['Student']['curriculum_id'])) {

                    ?>
                    <div style="overflow-x:auto;">
                        <fieldset style="padding-bottom: 10px;padding-top: 10px;">
                            <legend>&nbsp;&nbsp; <span class="fs14 text-gray">Curriculum Details</span> &nbsp;&nbsp;</legend>
                            <span class="fs15 text-black">
									<strong class="fs14 text-gray">Curriculum Name:</strong> &nbsp;<?= $studentAcademicProfile['Curriculum']['name']; ?> <br>
									<strong class="fs14 text-gray">English Degree Nomenclature:</strong> &nbsp;<?=$studentAcademicProfile['Curriculum']['english_degree_nomenclature']; ?> <br>
									<strong class="fs14 text-gray">Amharic Degree Nomenclature:</strong> &nbsp;<?= $studentAcademicProfile['Curriculum']['amharic_degree_nomenclature']; ?> <br>
									<strong class="fs14 text-gray">Program:</strong> &nbsp;<?= $studentAcademicProfile['Curriculum']['program']['name']; ?> <br>
									<strong class="fs14 text-gray">Specialization:</strong> &nbsp;<?= $studentAcademicProfile['Curriculum']['specialization_english_degree_nomenclature']; ?> <br>
									<strong class="fs14 text-gray">Year Introduced:</strong> &nbsp;<?= $studentAcademicProfile['Curriculum']['year_introduced']; ?> <br>
									<strong class="fs14 text-gray">Program:</strong> &nbsp;<?= $studentAcademicProfile['Curriculum']['program']['name']; ?> <br>
									<strong class="fs14 text-gray">Type Of Credit:</strong> &nbsp;<?=$studentAcademicProfile['Curriculum']['type_credit']; ?> <br>
									<strong class="fs14 text-gray">Minimum <?= $studentAcademicProfile['Curriculum']['type_credit']; ?> for Graduation:</strong> &nbsp;<?=$studentAcademicProfile['Curriculum']['minimum_credit_points']; ?> <br>
								</span>
                        </fieldset>
                    </div>
                    <?= $this->element('curriculum_organized_semester_courses'); ?>
                    <?php
                } else { ?>
                    <div class="info-box info-message" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style="margin-right: 15px;"></span>The student is not yet attached to any curriculum.</div>
                    <?php
                } ?>
                <!-- </div> -->
            </div>

            <div class="content" id="Billing" style="padding-left: 0px; padding-right: 0px;">
                <hr style="margin-top: -10px;">
                <!-- <div class="AddTab"> -->
                <?= $this->element('billing'); ?>
                <!-- </div> -->
            </div>

            <?php
            if (SHOW_OTP_TAB_ON_STUDENT_ACADEMIC_PROFILE_FOR_STUDENTS == 1 && ($userRole == ROLE_STUDENT ||
                   $userRole == ROLE_SYSADMIN || $userRole == ROLE_DEPARTMENT || $userRole== ROLE_REGISTRAR ||
                    $userRole == ROLE_GENERAL) && isset($otps) && !empty($otps)) { ?>
                <div class="content" id="OTP" style="padding-left: 0px; padding-right: 0px;">
                    <hr style="margin-top: -10px;">
                    <div class="row">
                        <!-- <div class="info-box info-message" style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style="margin-right: 15px;"></span>One time password is only valid until you change the passoword on the specified web address, once changed, you will use the new password you set for the site.</div> -->
                        <?php
                        $otp_services_option = Configure::read('otp_services_option');
                        $changed_otp_password = false;
                        foreach ($otps as $key => $otp) { ?>
                            <div class="large-6 columns">
                                <fieldset style="padding-bottom: 15px; padding-top: 5px;">
                                    <legend>&nbsp;&nbsp; <span class="fs15 text-black">One Time Password for <?= $otp_services_option[$otp['Otp']['service']]; ?></span> &nbsp;&nbsp;</legend>
                                    <div class="row" style="line-height: 2;">
                                        <?php
                                        if ($otp['Otp']['service'] == 'Elearning' && empty($otp['Otp']['portal'])) { ?>
                                            <div class="large-12 columns">
                                                <br/>
                                                <span class="fs15 text-black">Username: <strong id="copyOTPusername<?= $key ?>" class="copy-text" data-clipboard-target="#copyOTPusername<?= $key ?>" title="Click here once to copy <?= $otp_services_option[$otp['Otp']['service']] ?> OTP username"><?= $otp['Otp']['username']; ?></strong></span><br/>
                                            </div>
                                            <div class="large-12 columns">
                                                <?php
                                                if (!empty($moodleUserDetails['MoodleUser']['username']) && $moodleUserDetails['MoodleUser']['created'] == $moodleUserDetails['MoodleUser']['modified']) { ?>
                                                    <span class="fs15 text-black">Password: <strong id="copyOTPpassword<?= $key ?>" class="copy-text" data-clipboard-target="#copyOTPpassword<?= $key ?>" title="Click here once to copy <?= $otp_services_option[$otp['Otp']['service']] ?> OTP password"><?= $otp['Otp']['password']; ?></strong></span><br/>
                                                    <?php
                                                } else {
                                                    $changed_otp_password = true;
                                                    ?>
                                                    <span class="fs15 text-black">Password: <i class="accepted">The same password used for SMiS</i></span><br/>
                                                    <?php
                                                } ?>
                                            </div>
                                            <div class="large-12 columns">
                                                <hr/>
                                                <span class="fs15 text-black"><?= (!empty($moodleUserDetails['MoodleUser']['username']) && $moodleUserDetails['MoodleUser']['created'] == $moodleUserDetails['MoodleUser']['modified']) ? 'Last Updated' : 'Last Password Change'; ?>: &nbsp;<?= $this->Time->timeAgoInWords((isset($moodleUserDetails['MoodleUser']['modified']) && !empty($moodleUserDetails['MoodleUser']['modified']) ? $moodleUserDetails['MoodleUser']['modified'] : $otp['Otp']['modified']), array('format' => 'M j, Y', 'end' => '1 year', 'accuracy' => array('month' => 'month'))); ?></span><br/>
                                            </div>
                                            <?php
                                        } else { ?>
                                            <div class="large-12 columns">
                                                <br/>
                                                <span class="fs15 text-black">Username: <strong id="copyOTPusername<?= $key ?>" class="copy-text" data-clipboard-target="#copyOTPusername<?= $key ?>" title="Click here once to copy <?= $otp_services_option[$otp['Otp']['service']] ?> OTP username"><?= $otp['Otp']['username']; ?></strong></span><br/>
                                            </div>
                                            <div class="large-12 columns">
                                                <span class="fs15 text-black">Password: <strong id="copyOTPpassword<?= $key ?>" class="copy-text" data-clipboard-target="#copyOTPpassword<?= $key ?>" title="Click here once to copy <?= $otp_services_option[$otp['Otp']['service']] ?> OTP password"><?= $otp['Otp']['password']; ?></strong></span><br/>
                                            </div>
                                            <div class="large-12 columns">
                                                <hr/>
                                                <span class="fs15 text-black">Last Updated: &nbsp;<?= $this->Time->timeAgoInWords($otp['Otp']['modified'], array('format' => 'M j, Y', 'end' => '1 year', 'accuracy' => array('month' => 'month'))); ?></span><br/>
                                            </div>
                                            <?php
                                        }

                                        if (isset($otp['Otp']['exam_center']) && !empty($otp['Otp']['exam_center'])) { ?>
                                            <div class="large-12 columns">
                                                <span class="fs15 text-black">Exam Center:  &nbsp;<?= $otp['Otp']['exam_center']; ?></span><br/>
                                            </div>
                                            <?php
                                        }

                                        if ((isset($otp['Otp']['portal']) && !empty($otp['Otp']['portal'])) || $otp['Otp']['service'] == 'Office365' || $otp['Otp']['service'] == 'Elearning') { ?>
                                            <div class="large-12 columns">
                                                <span class="fs15 text-black" ><?= isset($otp['Otp']['portal']) && !empty($otp['Otp']['portal']) ? 'Web URL:  &nbsp;' . $otp['Otp']['portal'] . ' &nbsp; &nbsp; <a href="' . $otp['Otp']['portal'] . '" target="_blank">Open Web Address</a><br/>' : ($otp['Otp']['service'] == 'Office365' ? ('Outlook URL:  &nbsp;' . OTP_OFFICE_365_OUTLOOK_URL . ' &nbsp; &nbsp; <a href="' . OTP_OFFICE_365_OUTLOOK_URL . '" target="_blank">Open Outlook (Email)</a><br/>Office 365 URL:  &nbsp;' . OTP_OFFICE_365_MAIN_URL . ' &nbsp; &nbsp; <a href="' .  OTP_OFFICE_365_MAIN_URL . '" target="_blank">Open Office 365 Main Page</a><br/>') : ('E-Learning Portal:  &nbsp;' . MOODLE_SITE_URL . ' &nbsp; &nbsp; <a href="' . MOODLE_SITE_URL . '" target="_blank">Open E-Learning Portal</a><br/>')); ?></span>
                                            </div>
                                            <?php
                                        }

                                        if ($otp['Otp']['service'] == 'Elearning' && empty($otp['Otp']['portal'])) { ?>
                                            <?php
                                            if ($userRole == ROLE_STUDENT && isset($moodleUserDetails) && !empty($moodleUserDetails['MoodleUser']['username']) && $moodleUserDetails['MoodleUser']['created'] == $moodleUserDetails['MoodleUser']['modified']) { ?>
                                                <div class="large-12 columns">
                                                    <div class="warning-box fs15" style="font-family: 'Times New Roman', Times, serif; font-weight: normal; text-align: justify;">Change your SMiS  password to update the default OTP password set for <?= MOODLE_SITE_URL; ?>, <a href="/users/changePwd">Click here to update your SMiS Password</a> which will secure your elearning account and also sets the same password for both SMiS and elearning portal.</div>
                                                </div>
                                                <?php
                                            } else if ($userRole == ROLE_STUDENT && !$changed_otp_password) { ?>
                                                <div class="large-12 columns">
                                                    <div class="info-box fs15" style="font-family: 'Times New Roman', Times, serif; font-weight: normal; text-align: justify;">If you never changed your SMiS password since you started using the E-learning portal, Please change your SMiS password to change the default initial OTP password (<?= $otp['Otp']['password']; ?>) which was set for <b><?= MOODLE_SITE_URL; ?></b> so that you can use the same password for both sites and secure your E-Learning account from being used by someone else. If you already done that, ignore this notification message.</div>
                                                </div>
                                                <?php
                                            }
                                        } else { ?>
                                            <div class="large-12 columns">
                                                <div class="info-box fs15" style="font-family: 'Times New Roman', Times, serif; font-weight: normal; text-align: justify;">This One time password (OTP) is only valid until you change the password on the specified web address, once changed, you are required to remember the new password you set for the site and use that password afterwards. If you already done that, ignore this notification message.</div>
                                            </div>
                                            <?php
                                        } ?>
                                    </div>
                                </fieldset>
                            </div>
                            <?php
                        } ?>
                    </div>
                </div>
                <?php
            } ?>
        </div>
    </div>
</div>

<!-- <a class="close-reveal-modal">&#215;</a> -->

<div class="row">
    <div class="large-12 columns">
        <div id="myModalMove" class="reveal-modal" data-reveal>

        </div>

        <div id="myModalAdd" class="reveal-modal" data-reveal>

        </div>

        <div id="myModalReg" class="reveal-modal" data-reveal>

        </div>

        <div id="myModalPref" class="reveal-modal" data-reveal>

        </div>

    </div>
</div>
