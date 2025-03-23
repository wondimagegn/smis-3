<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-info-outline"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= 'View Curriculum Details: ' .  (isset($curriculum['Curriculum']['name']) ? $curriculum['Curriculum']['name'] . ' - ' . $curriculum['Curriculum']['year_introduced'] : ''); ?></span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <div style="margin-top: -30px;">
                    <hr>
                    <div class="large-7 columns">
                        <table cellpadding="0" cellspacing="0" class="table">
                            <tbody>
                                <tr>
                                    <td class="vcenter"><strong class="text-gray">Department:</strong> &nbsp; <?= $curriculum['Department']['name']; ?></td>
                                </tr>
                                <tr>
                                    <td class="vcenter"><strong class="text-gray">Program:</strong> &nbsp; <?= $curriculum['Program']['name']; ?></td>
                                </tr>
                                <tr>
                                    <td class="vcenter"><strong class="text-gray">Curriculum Name:</strong> &nbsp; <br><?= $curriculum['Curriculum']['name']; ?></td>
                                </tr>
                                <tr>
                                    <td class="vcenter"><strong class="text-gray">Year Introduced:</strong> &nbsp; <?= $curriculum['Curriculum']['year_introduced']; ?></td>
                                </tr>
                                <tr>
                                    <td class="vcenter"><strong class="text-gray">Minimum Credit Points:</strong> &nbsp; <?= $curriculum['Curriculum']['minimum_credit_points']; ?></td>
                                </tr>
                                <tr>
                                    <td class="vcenter"><strong class="text-gray">Type Of Credit:</strong> &nbsp; <?= $curriculum['Curriculum']['type_credit']; ?></td>
                                </tr>
                                <tr>
                                    <td class="vcenter"><strong class="text-gray">English Degree Nomenclature:</strong> &nbsp; <br><?= $curriculum['Curriculum']['english_degree_nomenclature']; ?></td>
                                </tr>
                                <tr>
                                    <td class="vcenter"><strong class="text-gray">Amharic Degree Nomenclature:</strong> &nbsp; <br><?= $curriculum['Curriculum']['amharic_degree_nomenclature']; ?></td>
                                </tr>
                                <tr>
                                    <td class="vcenter"><strong class="text-gray">Certificate Name:</strong> &nbsp; <?= $curriculum['Curriculum']['certificate_name']; ?></td>
                                </tr>
                                <tr>
                                    <td class="vcenter"><strong class="text-gray">Created:</strong> &nbsp; <?= $this->Time->format("M j, Y g:i A", $curriculum['Curriculum']['created'], NULL, NULL); ?></td>
                                </tr>
                                <tr>
                                    <td class="vcenter"><strong class="text-gray">Modified:</strong> &nbsp; <?= $this->Time->format("M j, Y g:i A", $curriculum['Curriculum']['modified'], NULL, NULL); ?></td>
                                </tr>
                                <?php
                                if (!empty($curriculum['Attachment'])) { ?>
                                    <tr>
                                        <td class="vcenter">
                                            <table cellpadding="0" cellspacing="0" class="table">
                                                <?php
                                                foreach ($curriculum['Attachment'] as $cuk => $cuv) {
                                                    if (isset($cuv['dirname']) && isset($cuv['basename'])) {  ?>
                                                        <tr>
                                                            <td class="vcenter">PDF uploaded on: <?= $this->Time->format("M j, Y g:i A", $cuv['created'], NULL, NULL); ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td class="vcenter">
                                                                <?php 
																if ($this->Media->file($cuv['dirname'] . DS . $cuv['basename'])) { ?>
																	<a href="<?= $this->Media->url($cuv['dirname'] . DS . $cuv['basename'], true); ?>" target=_blank>View Attachment</a><br>
																	<?= $cuv['basename']; ?> (<?= $size = $this->Number->toReadableSize($this->Media->size($this->Media->file($cuv['dirname'] . DS . $cuv['basename']))); ?>)
																	<?php // $this->Media->embed($this->Media->file($cuv['dirname'] . DS . $cuv['basename']), array('width' => '144', 'height' => '144'));  ?>
																	<?php
																} else { ?>
																	<span class=" text-red">Attachment not found or deleted</span>
																	<?php
																} ?>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                    }
                                                } ?>
                                            </table>
                                        </td>
                                    </tr>
                                    <?php
                                } else { ?>
                                    <tr>
                                        <td class="vcenter"><span class=" text-red">No PDF is Attached to the Curriculum</span></td>
                                    </tr>
                                    <?php
                                } ?>
                            </tbody>
                        </table>
                        <br>
                    </div>
                </div>

                <div class="large-4 columns">
                    <table cellpadding="0" cellspacing="0" class="table">
                        <tr>
                            <th class="center">#</th>
                            <th class="vcenter">Category Name</th>
                            <th class="center">Code</th>
                            <th class="center">Mandatory Credit</th>
                            <th class="center">Total Credit</th>
                        </tr>
                        <?php
                        $cCount = 1;
                        foreach ($curriculum['CourseCategory'] as $courseCategory => $courseCategoryValue) { ?>
                            <tr>
                                <td class="center"><?= $cCount++; ?></td>
                                <td class="vcenter"><?= $courseCategoryValue['name']; ?></td>
                                <td class="center"><?= $courseCategoryValue['code']; ?></td>
                                <td class="center"><?= $courseCategoryValue['mandatory_credit']; ?></td>
                                <td class="center"><?= $courseCategoryValue['total_credit']; ?></td>
                            </tr>
                        <?php
                        }
                        ?>
                    </table>
                    <br>
                    <table cellpadding="0" cellspacing="0" class="table">
                        <tbody>
                            <?php
                            if (!empty($curriculum['DepartmentStudyProgram']['study_program_id'])) { ?>
                                <tr>
                                    <td class="vcenter"><strong class="text-gray">Study Program:</strong> &nbsp; <br><?= $curriculum['DepartmentStudyProgram']['StudyProgram']['study_program_name']; ?></td>
                                </tr>
                                <tr>
                                    <td class="vcenter"><strong class="text-gray">Qualification:</strong> &nbsp; <?= $curriculum['DepartmentStudyProgram']['Qualification']['qualification']; ?></td>
                                </tr>
                                <tr>
                                    <td class="vcenter"><strong class="text-gray">Program Modality:</strong> &nbsp; <?= $curriculum['DepartmentStudyProgram']['ProgramModality']['modality']; ?></td>
                                </tr>
                                <?php
                            } else { ?>
                                <tr>
                                    <td class="center">No study program is attached to the curriculum.</td>
                                </tr>
                                <?php  
                            } ?>
                        </tbody>
                    </table>
                </div>

                <div class="large-12 columns">
                    <?php
                    if (!empty($curriculum['Course'])) { ?>
                        <hr>
                        <h6 class="fs14 text-gray">Related Courses</h6>
                        <br>
                        <?php
                        if (!empty($curriculum['Course'])) { ?>
                            <div style="overflow-x:auto;">
                                <table cellpadding="0" cellspacing="0" class="table">
                                    <thead>
                                        <tr>
                                            <th class="center">#</th>
                                            <th class="center">Year</th>
                                            <th class="center">SEM</th>
                                            <th class="vcenter">Course Title</th>
                                            <th class="center">Course Code</th>
                                            <th class="center">Credit</th>
                                            <th class="center">Course Category</th>
                                            <!-- <th class="center">Lecture Attendance Requirement</th>
                                            <th class="center">Lab Attendance Requirement</th> -->
                                            <th class="center">Grade Type</th>
                                            <th class="center">L T L</th>
                                            <th class="center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $count = 1;
                                        foreach ($curriculum['Course'] as $course) { ?>
                                            <tr>
                                                <td class="center"><?= $count++; ?></td>
                                                <td class="center"><?= $course['YearLevel']['name']; ?></td>
                                                <td class="center"><?= $course['semester']; ?></td>
                                                <td class="vcenter"><?= $course['course_title']; ?></td>
                                                <td class="center"><?= $course['course_code']; ?></td>
                                                <td class="center"><?= $course['credit']; ?></td>
                                                <td class="center"><?= $course['CourseCategory']['name']; ?></td>
                                                <!-- <td class="center"><?php // $course['lecture_attendance_requirement']; ?></td>
                                                <td class="center"><?php // $course['lab_attendance_requirement']; ?></td> -->
                                                <td class="center"><?= $course['GradeType']['type']; ?></td>
                                                <td class="center"><?= $course['course_detail_hours']; ?></td>
                                                <td class="center">
                                                    <?= $this->Html->link(__(''), array('controller' => 'courses', 'action' => 'view', $course['id']), array('class' => 'fontello-eye', 'title' => 'View')); ?>&nbsp;
                                                    <?php
                                                    if ($this->Session->read('Auth.User')['role_id'] == ROLE_DEPARTMENT) { ?>
                                                        <?= $this->Html->link(__(''), array('controller' => 'courses', 'action' => 'edit', $course['id']), array('class' => 'fontello-pencil', 'title' => 'Edit')); ?>&nbsp;
                                                        <?= $this->Html->link(__(''), array('controller' => 'courses', 'action' => 'delete', $course['id']), array('class' => 'fontello-trash', 'title' => 'Delete'), sprintf(__('Are you sure you want to delete %s from %s curriculum ?'), $course['course_title'], $curriculum['Curriculum']['name'])); ?>
                                                        <?php
                                                    } ?>
                                                </td>
                                            </tr>
                                            <?php
                                        } ?>
                                    </tbody>
                                </table>
                            </div>
                            <br>
                            <?php
                        } ?>
                        <?php
                    } ?>
                </div>
            </div>
        </div>
    </div>
</div>