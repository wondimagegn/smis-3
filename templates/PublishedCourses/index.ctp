<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('List Published Courses'); ?></span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <div style="margin-top: -30px;;">
                    <?= $this->Form->create('PublishedCourse', array('action' => 'index')); ?>
                    <hr>
                    <?php
                    if (!isset($search_published_course)) { ?>
                        <fieldset style="padding-bottom: 0px;padding-top: 15px;">
                            <!-- <legend>&nbsp;&nbsp; Search Filters &nbsp;&nbsp;</legend> -->
                            <div class="row">
                                <div class="large-3 columns">
                                    <?= $this->Form->input('PublishedCourse.academic_year', array('label' => 'Academic Year: ', 'style' => 'width:90%;',  'options' => $acyear_array_data, 'default' => (isset($academic_year) ? $academic_year : $defaultacademicyear))); ?>
                                </div>
                                <div class="large-3 columns">
                                    <?= $this->Form->input('PublishedCourse.semester', array('label' => 'Semester: ', 'style' => 'width:90%;', 'options' => Configure::read('semesters'), 'default' => (isset($semester) ? $semester : $defaultsemester))); ?>
                                </div>
                                <div class="large-3 columns">
                                    <?= $this->Form->input('PublishedCourse.program_id', array('label' => 'Program: ', 'style' => 'width:90%;', 'options' => $programs)); ?>
                                </div>
                                <div class="large-3 columns">
                                    <?= $this->Form->input('PublishedCourse.program_type_id', array('label' => 'Program Type: ', 'style' => 'width:90%;',  'options' => $programTypes)); ?>
                                </div>
                            </div>
                            <hr>
                            <?= $this->Form->end(array('label' => __('Search'), 'name' => 'search', 'class' => 'tiny radius button bg-blue')); ?>
                        </fieldset>
                        <?php
                    } ?>
                    <hr>

                    <?php
                    if (isset($publishedCourses) && !empty($publishedCourses)) { ?>
                        <!-- <hr>
                        <div class="row">
                            <div class="large-2 columns">
                                <?php //echo $this->Html->link($this->Html->image("xls-icon.gif", array('alt' => 'Export To Xls')), array('action' => "export_published_xls"), array('escape' => false)) . " Export"; ?>
                            </div>
                            <div class="large-2 columns">
                                <?php //echo $this->Html->link($this->Html->image("pdf_icon.gif", array("alt" => "Print to PDF")), array('action' => "print_published_pdf"), array('escape' => false)) . " Print"; ?>
                            </div>
                            <div class="large-8 columns">
                                &nbsp;
                            </div>
                        </div>
                        <hr> -->

                        <!-- <hr> -->
                        <?php
                        //debug($publishedCourses);
                        foreach ($publishedCourses as $sk => $sv) {
                            if (!empty($sv)) { ?>
                                <!-- <div class='fs15' style='font-weight:bold'> Academic Year: <?php //echo (isset($academic_year) ? $academic_year : ''); ?></div> -->
                                <!-- <div class='fs15' style='font-weight:bold'> Semester: <?php //echo (isset($sk) ? $sk : ''); ?></div> -->
                                <?php
                                $count = 1;
                                foreach ($sv as $pk => $pv) {
                                    if (!empty($pk)) { ?>
                                        <!-- <div class='fs16'> Program: <?php //echo $pk; ?></div> -->
                                        <?php
                                        foreach ($pv as $ptk => $ptv) {
                                            if (!empty($ptk)) { ?>
                                                <!-- <div class='fs16'> Program Type: <?php //echo $ptk; ?></div> -->
                                                <?php
                                                foreach ($ptv as $deptKey => $deptValue) { ?>
                                                    <!-- <div class='fs16'> Department: <?php //echo $deptKey; ?></div> -->
                                                    <?php
                                                    foreach ($deptValue as $yk => $yv) {
                                                        if (!empty($yv)) { ?>
                                                            <!--  <div class='fs16'> Year Level: <?php //echo $yk; ?></div> -->
                                                            <?php
                                                            foreach ($yv as $section_name => $section_value) { 
                                                                //debug($section_value['Semester Registered'][0]);  

                                                                $total_published_credits = 0;
                                                                //debug($section_value['Semester Registered'][0]['PublishedCourse']['program_id']); 
                                                                //debug($section_value['Semester Registered'][0]['PublishedCourse']['academic_year']);
                                                                ?>
                                                                <!-- <div class='fs16'> Section : <?php //echo $section_name; ?></div> -->
                                                                <div style="overflow-x:auto;">
                                                                    <table cellpadding="0" cellspacing="0" class="table">
                                                                        <thead>
                                                                            <tr>
                                                                                <td colspan="5" style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85); line-height: 1.5;">
                                                                                    <span style="font-size:16px;font-weight:bold; margin-top: 25px;">
                                                                                       <!--  Section:  --><?= (isset($section_name) ? $section_name  . ' ' . (isset($yk)  ?  ' (' . $yk . ', ' : (isset($publishedCourse['YearLevel']['name']) ? $publishedCourse['YearLevel']['name'] . ', ' : ' (Pre/1st, ')) : '') . (isset($academic_year) ? $academic_year : (isset($publishedCourse['academic_year']) ? $publishedCourse['academic_year'] :  '')) . ', ' . (isset($sk) ? ($sk == 'I' ? '1st Semester' : ( $sk == 'II' ? '2nd Semester' : ($sk == 'III' ? '3rd Semester' : $sk . ' Semester'))) : (isset($publishedCourse['semester']) ? $publishedCourse['semester'] :  '')) . ')'; ?>
                                                                                    </span>
                                                                                    <br>
                                                                                    <span class="text-gray" style="padding-top: 13px; font-size: 13px; font-weight: bold"> 
                                                                                        <?= (isset($deptKey) ? $deptKey : (isset($publishedCourse['Department']['name']) ? $publishedCourse['Department']['name'] :  $publishedCourse['College']['name'] . ' Pre/Freshman')); ?> &nbsp; | &nbsp; <?= (isset($pk) ? $pk : (isset($publishedCourse['Program']['name']) ? $publishedCourse['Program']['name'] : '')); ?>  &nbsp; | &nbsp; <?= (isset($ptk) ? $ptk : (isset($publishedCourse['ProgramType']['name']) ? $publishedCourse['ProgramType']['name'] : '')); ?> 
                                                                                    </span>
                                                                                    <br>
                                                                                    <!-- <span class="text-black" style="padding-top: 14px; font-size: 13px; font-weight: bold"> 
                                                                                        <?php //echo (isset($academic_year) ? $academic_year : (isset($publishedCourse['academic_year']) ? $publishedCourse['academic_year'] :  ''/* $publishedCourse['Section']['academicyear'] */)); ?> &nbsp; | &nbsp; <?= (isset($sk) ? ($sk == 'I' ? '1st Semester' : ( $sk == 'II' ? '2nd Semester' : ($sk == 'III' ? '3rd Semester' : $sk . ' Semester'))) : (isset($publishedCourse['semester']) ? $publishedCourse['semester'] :  '')); ?> <br>
                                                                                    </span> -->
                                                                                </td>
                                                                            </tr>
                                                                            <tr>
                                                                                <th class="center" style="width: 5%;">#</th>
                                                                                <th class="vcenter">Course Title</th>
                                                                                <th class="center">Course Code</th>
                                                                                <th class="center">Credit</th>
                                                                                <th class="center">L T L</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <?php
                                                                            foreach ($section_value as $type_index => $section_value_detail) { ?>
                                                                                <tr>
                                                                                    <td>&nbsp;</td>
                                                                                    <td colspan="4"><?= $type_index; ?></td>
                                                                                </tr>
                                                                                <?php
                                                                                foreach ($section_value_detail as $publishedCourse) {
                                                                                    if (!empty($publishedCourse)) { ?>
                                                                                        <tr>
                                                                                            <td class="center"><?= $count++; ?></td>
                                                                                            <td class="vcenter"><?= $this->Html->link($publishedCourse['Course']['course_title'], array(/* 'controller' => 'courses', */ 'action' => 'view', $publishedCourse['PublishedCourse']['id'])); ?></td>
                                                                                            <td class="center"><?= $publishedCourse['Course']['course_code']; ?></td>
                                                                                            <td class="center"><?= $publishedCourse['Course']['credit']; ?></td>
                                                                                            <td class="center"><?= $publishedCourse['Course']['course_detail_hours']; ?></td>
                                                                                        </tr>
                                                                                        <?php
                                                                                        if (isset($publishedCourse['Course']['credit']) && $publishedCourse['Course']['credit']) {
                                                                                            $total_published_credits += $publishedCourse['Course']['credit'];
                                                                                        }
                                                                                    }
                                                                                }
                                                                            } ?>
                                                                        </tbody>
                                                                        <tfoot>
                                                                            <tr>
                                                                                <td colspan="2"></td>
                                                                                <td class="center">Total</td>
                                                                                <td class="center"><?= $total_published_credits; ?></td>
                                                                                <td></td>
                                                                            </tr>
                                                                            <?php 
                                                                            if (isset($section_value_detail[0]['Section']['Curriculum']['name']) && !empty($section_value_detail[0]['Section']['Curriculum']['name'])) { ?>
                                                                                <tr>
                                                                                    <td></td>
                                                                                    <td colspan="4" class="vcenter" style="font-weight: normal;">
                                                                                        <?= '<b>Section Curriculum: </b> ' . ((ucwords(strtolower($section_value_detail[0]['Section']['Curriculum']['name']))) .  ' - ' . $section_value_detail[0]['Section']['Curriculum']['year_introduced'] . ' (' . (count(explode('ECTS', $section_value_detail[0]['Section']['Curriculum']['type_credit'])) >= 2? 'ECTS':'Credit') . ')'); ?>
                                                                                    </td>
                                                                                </tr>
                                                                                <?php
                                                                            } ?>
                                                                        </tfoot>
                                                                    </table>
                                                                </div>
                                                                <br><br>
                                                                <?php
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if (isset($publishedCoursesCollege) && !empty($publishedCoursesCollege)) { ?>
                        <!-- <div class='largeheading'> Academic Year: <?= $academic_year; ?></div> -->
                        <!-- <div class='largeheading'> Semester: <?= $semester; ?></div> -->
                        <?php
                        $count = 1;

                        //debug($publishedCoursesCollege);
                        foreach ($publishedCoursesCollege as $pk => $pv) {
                            if (!empty($pk)) { 
                                foreach ($pv as $ptk => $ptv) {
                                    if (!empty($ptk)) { 
                                        foreach ($ptv as $collKey => $collValue) { 
                                            foreach ($collValue as $section_name => $section_value) { 
                                                //debug($section_value['Semester Registered'][0]);  
                                                //debug($section_value['Semester Registered'][0]['PublishedCourse']['program_id']); 
                                                //debug($section_value['Semester Registered'][0]['PublishedCourse']['academic_year']); 
                                                
                                                $total_published_credits = 0;
                                                ?>
                                                <!-- <div class='fs16'> Section : <?php //echo $section_name; ?><br></div> -->
                                                <!-- <div class='fs16'> Year Level : Pre/1st </div> -->
                                                <div style="overflow-x:auto;">
                                                    <table cellpadding="0" cellspacing="0" class="table">
                                                        <thead>
                                                            <tr>
                                                                <td colspan="5" style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85); line-height: 1.5;">
                                                                    <span style="font-size:16px;font-weight:bold; margin-top: 25px;">
                                                                       <!--  Section:  --><?= (isset($section_name) ? $section_name  . (isset($section_value['Semester Registered'][0]['PublishedCourse']['program_id']) && $section_value['Semester Registered'][0]['PublishedCourse']['program_id'] == PROGRAM_REMEDIAL ?  ' (Remedial)' : '  (Pre/1st)') : ' (Pre/1st)'); ?>
                                                                    </span>
                                                                    <br>
                                                                    <span class="text-gray" style="padding-top: 13px; font-size: 13px; font-weight: bold"> 
                                                                        <?= (isset($collKey) ? $collKey . (isset($section_value['Semester Registered'][0]['PublishedCourse']['program_id']) && $section_value['Semester Registered'][0]['PublishedCourse']['program_id'] == PROGRAM_REMEDIAL ?  ' - Remedial' : ' - Pre/Freshman') : ' Pre/Freshman'); ?> &nbsp; | &nbsp; <?= (isset($pk) ? $pk : (isset($section_value['Semester Registered'][0]['Program']['name']) ? $section_value['Semester Registered'][0]['Program']['name'] : '')); ?>  &nbsp; | &nbsp; <?= (isset($ptk) ? $ptk : (isset($section_value['Semester Registered'][0]['ProgramType']['name']) ? $section_value['Semester Registered'][0]['ProgramType']['name'] : '')); ?> 
                                                                    </span>
                                                                    <br>
                                                                    <span class="text-black" style="padding-top: 14px; font-size: 13px; font-weight: bold"> 
                                                                        <?= (isset($academic_year) ? $academic_year : (isset($section_value['Semester Registered'][0]['PublishedCourse']['academic_year']) ? $section_value['Semester Registered'][0]['PublishedCourse']['academic_year'] :  '')); ?> &nbsp; | &nbsp; <?= (isset($semester) ? ($semester == 'I' ? '1st Semester' : ( $semester == 'II' ? '2nd Semester' : ($semester == 'III' ? '3rd Semester' : $semester . ' Semester'))) : (isset($section_value['Semester Registered'][0]['PublishedCourse']['semester']) ? $section_value['Semester Registered'][0]['PublishedCourse']['semester'] :  '')); ?> <br>
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th class="center" style="width: 5%;">#</th>
                                                                <th class="vcenter">Course Title</th>
                                                                <th class="center">Course Code</th>
                                                                <th class="center">Credit</th>
                                                                <th class="center">L T L</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            foreach ($section_value as $type_index => $section_value_detail) { ?>
                                                                <tr>
                                                                    <td class="center">&nbsp;</td>
                                                                    <td colspan=4><?= $type_index; ?></td>
                                                                </tr>
                                                                <?php
                                                                foreach ($section_value_detail as $publishedCourse) {
                                                                    if (!empty($publishedCourse)) { ?>
                                                                        <tr>
                                                                            <td class="center"><?= $count++; ?></td>
                                                                            <td class="vcenter"><?= $this->Html->link($publishedCourse['Course']['course_title'], array(/* 'controller' => 'courses',  */'action' => 'view', $publishedCourse['PublishedCourse']['id'])); ?></td>
                                                                            <td class="center"><?= $publishedCourse['Course']['course_code']; ?></td>
                                                                            <td class="center"><?= $publishedCourse['Course']['credit']; ?></td>
                                                                            <td class="center"><?= $publishedCourse['Course']['course_detail_hours']; ?></td>
                                                                        </tr>
                                                                        <?php
                                                                        if (isset($publishedCourse['Course']['credit']) && $publishedCourse['Course']['credit']) {
                                                                            $total_published_credits += $publishedCourse['Course']['credit'];
                                                                        }
                                                                    }
                                                                }
                                                            } ?>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <td colspan="2"></td>
                                                                <td class="center">Total</td>
                                                                <td class="center"><?= $total_published_credits; ?></td>
                                                                <td></td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                                <br><br>
                                                <?php
                                            }
                                        } 
                                    }
                                }
                            }
                        }
                    } ?>
                </div>
            </div>
        </div>
    </div>
</div>