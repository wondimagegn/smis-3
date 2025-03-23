<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;">Display Sections: (<?= (($role_id != ROLE_COLLEGE) ? $departmentname : $collegename); ?>)</span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <?= $this->Form->create('Section'); ?>
                <div style="margin-top: -30px;">
                    <hr>
                    <fieldset style="padding-bottom: 0px;padding-top: 15px;">
                        <!-- <legend>&nbsp;&nbsp; Search / Filter &nbsp;&nbsp;</legend> -->
                        <div class="row">
                            <div class="large-3 columns">
                                <?= $this->Form->input('Section.academicyear', array('options' => $acyear_array_data, 'required', 'style' => 'width:90%;')); ?>
                            </div>
                            <?php
                            if ($role_id != ROLE_COLLEGE) { ?>
                                <div class="large-3 columns">
                                    <?= $this->Form->input('Section.year_level_id', array('empty' => '[ Select Year Level ]', 'required', 'style' => 'width:90%;')); ?>
                                </div>
                                <?php
                            } ?>
                            <div class="large-3 columns">
                                <?= $this->Form->input('Section.program_id', array(/* 'empty' => "[ Select Program ]", */'required', 'style' => 'width:90%;')); ?>
                            </div>
                            <div class="large-3 columns">
                                <?= $this->Form->input('Section.program_type_id', array(/* 'empty' => "[ Select Program Type ]", */'required', 'style' => 'width:90%;')); ?>
                            </div>
                            <?php
                            if ($role_id == ROLE_COLLEGE) { ?>
                                <div class="large-3 columns">
                                    &nbsp;
                                </div>
                                <?php
                            } ?>
                        </div>
                        <hr>
                        <?= $this->Form->Submit('Search', array('name' => 'search', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?>
                    </fieldset>
                    <hr>
                </div>
            </div>

            <?php
            if (!empty($sections)) { ?>
                <div class="large-12 columns">
                    <!-- <table cellpadding="0" cellspacing="0" class="table-borderless">
                        <tr>
                            <td>Do you want to swap students?</td>
                        </tr>
                        <tr>
                            <td style="background-color: white;"><?php //echo $this->Form->input('Section.swap', array('div' => false, 'options' => $swapOptions, 'label' => ' Swap By:', 'style' => 'width:150px')); ?></td>
                        </tr>
                        <tr>
                            <td><?php //echo $this->Form->Submit('Swap', array('name' => 'swapStudentSection', 'class' => 'tiny radius button bg-blue', 'div' => false)); ?></td>
                        </tr>
                    </table> -->
                    <br>

                    <?php
                    
                    if (!empty($studentsections)) {
                        //debug($studentsections[0]);
                        foreach ($studentsections as $k => $studentsection) {
                            $students_per_section = count($studentsection['Student']); ?>
                            <div style="overflow-x:auto;">
                                <table cellpadding="0" cellspacing="0" class="table">
                                    <thead>
                                        <tr>
                                            <td colspan="4" style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85); line-height: 1.5;">
                                                <span style="font-size:16px;font-weight:bold; margin-top: 25px;"> Section: <?= $studentsection['Section']['name'] . ' ' . (isset($studentsection['YearLevel']['name'])  ?  ' (' . $studentsection['YearLevel']['name']  : ($studentsection['Program']['id'] == PROGRAM_REMEDIAL ? ' (Remedial' : ' (Pre/1st')) . ', ' . $studentsection['Section']['academicyear'] . ')'; ?></span>
                                                    <br>
                                                    <span class="text-gray" style="padding-top: 13px; font-size: 13px; font-weight: bold"> 
                                                        <?= (isset($studentsection['Department']) && !empty($studentsection['Department']['name']) ? $studentsection['Department']['name'] :  $studentsection['College']['name'] . ($studentsection['Program']['id'] == PROGRAM_REMEDIAL ? ' - Remedial Program' : ' - Pre/Freshman')); ?> <?= (isset($studentsection['Program']['name']) && !empty($studentsection['Program']['name']) ? ' &nbsp; | &nbsp; ' . $studentsection['Program']['name'] : ''); ?> <?= (isset($studentsection['ProgramType']['name']) && !empty($studentsection['ProgramType']['name']) ? ' &nbsp; | &nbsp; ' . $studentsection['ProgramType']['name'] : ''); ?> 
                                                        <br>
                                                    </span>
                                                </span>
                                                <span class="text-gray" style="padding-top: 15px; font-size: 13px; font-weight: normal"> 
                                                    Curriculum: <?= (!empty($sections_curriculum_name[$k]) ? $sections_curriculum_name[$k] : 'Pre/Freshman Section without Curriculum Attachment'); ?> <br>
                                                    Hosted: <?= ($current_sections_occupation[$k] .' '.  ($current_sections_occupation[$k] > 1 ? ' Students' : ' Student')); ?>
                                                </span>
                                            </td>
                                            <td style="text-align: right; vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85);">
                                                <?= ($students_per_section ? $this->Html->link($this->Html->image("/img/xls-icon.gif", array("alt" => "Export TO Excel")) . ' Export to Excel', array('action' => 'export', $studentsection['Section']['id']), array('escape' => false)) : '') ?>  &nbsp;&nbsp;&nbsp;&nbsp;
                                                <?php //echo $this->Html->link($this->Html->image("/img/pdf_icon.gif", array("alt" => "Print To Pdf")) . ' PDF', array('action' => 'view_pdf', $studentsection['Section']['id']), array('escape' => false)); ?> &nbsp;&nbsp;&nbsp;&nbsp;
                                            </td>
                                        </tr>
                                        <tr>
                                            <th class="center">#</th>
                                            <th class="vcenter">Student Name</th>
                                            <th class="center">Sex</th>
                                            <th class="center">Student ID</th>
                                            <th class="center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $counter = 1;
                                        for ($i = 0; $i < $students_per_section; $i++) {
                                            if ($studentsection['Student'][$i]['StudentsSection']['archive'] == 0) { 
                                                $isStudentRegisteredInThisSection = ClassRegistry::init('CourseRegistration')->find('count', array('conditions' => array('CourseRegistration.section_id' => $studentsection['Section']['id'], 'CourseRegistration.student_id' => $studentsection['Student'][$i]['id'])));
                                                //$isStudentRegisteredInThisSection = (isset($studentsection['Student'][$i]['CourseRegistration']) && !empty($studentsection['Student'][$i]['CourseRegistration']));
                                                //debug($isStudentRegisteredInThisSection); ?>
                                                <tr>
                                                    <td class="center"><?= $counter++; ?></td>
                                                    <td class="vcenter"><?= $this->Html->link($studentsection['Student'][$i]['full_name'], array('controller' => 'students', 'action' => 'student_academic_profile',  $studentsection['Student'][$i]['id'])); ?></td>
                                                    <td class="center"><?= (strcasecmp(trim($studentsection['Student'][$i]['gender']), 'male') == 0 ? 'M' :(strcasecmp(trim($studentsection['Student'][$i]['gender']), 'female') == 0 ? 'F' : $studentsection['Student'][$i]['gender'])); ?></td>
                                                    <td class="center"><?= $studentsection['Student'][$i]['studentnumber']; ?></td>
                                                    <td class="center" id="ajax_student_'<?= $i; ?>'_'<?= $k; ?>">
                                                        <?php
                                                        if ($studentsection['Student'][$i]['graduated'] == 0 && !$isStudentRegisteredInThisSection) { ?>
                                                            <?php //echo $this->Html->link('Move', '#', array('data-animation' => "fade", 'data-reveal-id' => 'myModalMove', 'data-reveal-ajax' => '/sections/move/' . str_replace('/', '-', $studentsection['Student'][$i]['studentnumber']) . '/' . $studentsection['Section']['id'])); ?>&nbsp;&nbsp;
                                                            <?= $this->Html->link(__('Delete'), array('controller' => 'Sections', 'action' => 'deleteStudentforThisSection', $studentsection['Section']['id'], str_replace('/', '-', $studentsection['Student'][$i]['studentnumber'])), null, sprintf(__('Are you sure you want to delete %s from "%s" section?'), $studentsection['Student'][$i]['full_name'] . '('. str_replace('/', '-', $studentsection['Student'][$i]['studentnumber']) .')', $studentsection['Section']['name'] )); ?>
                                                            <?php
                                                        } else {?>

                                                            <!-- <span class="text-gray">Move</span>&nbsp;&nbsp; -->
                                                            <span class="text-gray">Delete</span>&nbsp;&nbsp;

                                                            <?= $this->Html->link(__('Archieve'), array('controller' => 'Sections', 'action' => 'archieveUnarchieveStudentSection', $studentsection['Section']['id'], $studentsection['Student'][$i]['id'], 1), null, sprintf(__('Are you sure you want to Archeive %s form %s section?'), $studentsection['Student'][$i]['full_name'] . '('. str_replace('/', '-', $studentsection['Student'][$i]['studentnumber']) .')', $studentsection['Section']['name'] )); ?>
                                                            <?php //echo $this->Html->link(__('Unarchieve'), array('controller' => 'Sections', 'action' => 'archieveUnarchieveStudentSection', $studentsection['Section']['id'], $studentsection['Student'][$i]['id'], 0), null, sprintf(__('Are you sure you want to Unarcheive %s for %s section?'), $studentsection['Student'][$i]['full_name'] . '('. str_replace('/', '-', $studentsection['Student'][$i]['studentnumber']) .')', $studentsection['Section']['name'] )); ?>
                                                            <?php
                                                        } ?>
                                                    </td>
                                                </tr>
                                            <?php
                                            }
                                        } ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td class="vcenter"><?= $this->Html->link('Move', '#',  array('data-animation' => "fade", 'data-reveal-id' => 'myModalAdd', 'data-reveal-ajax' => '/sections/move_selected_student_section/' . $studentsection['Section']['id'])); ?></td>
                                            <td class="center" id="ajax_student_'<?= $k; ?>"><?= $this->Html->link('Add', '#', array('data-animation' => "fade", 'data-reveal-id' => 'myModalAdd', 'data-reveal-ajax' => '/sections/add_student_section/' . $studentsection['Section']['id'])); ?></td>
                                            <td colspan="2">&nbsp;</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <br>
                            <?php
                        }
                    } ?>
                    <?= $this->Form->end(); ?>
                </div>
                <?php
            } else if (empty($sections) && !($isbeforesearch)) { ?>
                <div class="large-12 columns">
                    <div class='info-box info-message' style="font-family: 'Times New Roman', Times, serif; font-weight: bold;"><span style='margin-right: 15px;'></span>No Active section is found with the search criteria. You can Use "List Sections" to view Archieved sections instead.</div>
                </div>
                <?php
            } ?>
        </div>
    </div>
</div>
<!-- <div class="box">
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns"> -->
                <div id="myModalMove" class="reveal-modal" data-reveal>

                </div>

                <div id="myModalAdd" class="reveal-modal" data-reveal>

                </div>
            <!-- </div>
        </div>
    </div>
</div> -->