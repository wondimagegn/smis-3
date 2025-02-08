<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;">Sectionless Students List (<?= (($role_id != ROLE_COLLEGE) ? $department_name : $college_name); ?>)</span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <div style="margin-top: -30px;"></div>
                <hr>
                <blockquote>
                    <h6><i class="fa fa-info"></i> &nbsp; Important Note:</h6>
                    <p style="text-align:justify;">
                        <span class="fs14 text-black">
                            Note that Sectionless Students list doesn't include graduated, disciplinary dismissed, drop out students.
                            It only lists students including readmitted ones who are elegible for Section Assignment(not assigned to any active section currently).
                        </span>
                    </p>
                </blockquote>
                <hr>

                <?= $this->Form->create('Section'); ?>

                <fieldset style="padding-bottom: 10px;padding-top: 15px;">
                    <!-- <legend>&nbsp;&nbsp; Search / Filter &nbsp;&nbsp;</legend> -->
                    <div class="row">
                        <div class="large-3 columns">
                            <?= $this->Form->input('Section.academicyear', array('options' => $acyear_array_data, 'required', 'style' => 'width:90%;')); ?>
                        </div>
                        <div class="large-3 columns">
                            <?= $this->Form->input('Section.program_id', array('required', 'style' => 'width:90%;')); ?>
                        </div>
                        <div class="large-3 columns">
                            <?= $this->Form->input('Section.program_type_id', array('required', 'style' => 'width:90%;')); ?>
                        </div>
                        <div class="large-3 columns">
                            <br>
                            <?= $this->Form->Submit('Search', array('name' => 'search', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
                        </div>
                    </div>
                </fieldset>
                <hr>

                <?php
                if ($role_id == ROLE_DEPARTMENT) { 
                    if (!empty($sectionless_students_last_sections_details)) { ?>
                        <div style="overflow-x:auto;">
                            <table cellpadding="0" cellspacing="0" class="table">
                                <thead>
                                    <tr>
                                        <th class="center">#</th>
                                        <th class="vcenter">Full Name</th>
                                        <th class="center">Student ID</th>
                                        <th class="center">Last Section</th>
                                        <th class="center">ACY</th>
                                        <th class="center">Year Level</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $count = 1;
                                    foreach ($sectionless_students_last_sections_details as $sslsdk => $sslsdv) { ?>
                                        <tr>
                                            <td class="center"><?= $count++; ?></td>
                                            <td class="vcenter"><?= isset($sslsdv['Student']) && !empty($sslsdv['Student']) ? $this->Html->link($sslsdv['Student'][0]['full_name'], array('controller' => 'students', 'action' => 'student_academic_profile', $sslsdv['Student'][0]['id'])) : debug($sslsdv); ?></td>
                                            <td class="center"><?= isset($sslsdv['Student']) && !empty($sslsdv['Student']) ? $sslsdv['Student'][0]['studentnumber'] : '' ?></td>
                                            <td class="center"><?= $sslsdv['Section']['name']; ?></td>
                                            <td class="center"><?= $sslsdv['Section']['academicyear']; ?></td>
                                            <td class="center"><?= $sslsdv['YearLevel']['name']; ?></td>
                                        </tr>
                                        <?php
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                        <br>
                        <?php
                    } else if (empty($sectionless_students_last_sections_details) && !($isbeforesearch)) { ?>
                        <div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>No Sectionless Student is found with the given search criteria.</div>
                        <?php
                    }
                } else if ($role_id == ROLE_COLLEGE) {
                    if (!empty($sectionless_students_last_sections_details)) { ?>
                        <div style="overflow-x:auto;">
                            <table cellpadding="0" cellspacing="0" class="table">
                                <thead>
                                    <tr>
                                        <th class="center">#</th>
                                        <th class="vcenter">Full Name</th>
                                        <th class="center">Student ID</th>
                                        <th class="center">Last Section</th>
                                        <th class="center">ACY</th>
                                        <th class="center">Year Level</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $count = 1;
                                    foreach ($sectionless_students_last_sections_details as $sslsdk => $sslsdv) { ?>
                                        <tr>
                                            <td class="center"><?= $count++; ?></td>
                                            <td class="vcenter"><?= $this->Html->link($sslsdv['Student'][0]['full_name'], array('controller' => 'students', 'action' => 'student_academic_profile', $sslsdv['Student'][0]['id'])); ?></td>
                                            <td class="center"><?= $sslsdv['Student'][0]['studentnumber']; ?></td>
                                            <td class="center"><?= $sslsdv['Section']['name']; ?></td>
                                            <td class="center"><?= $sslsdv['Section']['academicyear']; ?></td>
                                            <td class="center"><?= (!empty($sslsdv['YearLevel']['name']) ? $sslsdv['YearLevel']['name'] : 'Pre/1st'); ?></td>
                                        </tr>
                                        <?php
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                        <br>
                        <?php
                    } else if (empty($sectionless_students_last_sections_details) && !($isbeforesearch)) { ?>
                        <div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>No Sectionless Student is found with the given search criteria.</div>
                        <?php
                    }
                } ?>

                <?= $this->Form->end(); ?>

            </div>
        </div>
    </div>
</div>