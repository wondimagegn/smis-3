<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;"><i class="fontello-info-outline"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"> <?= 'Department Study Program: ' .  (isset($departmentStudyProgram['StudyProgram']['study_program_name']) ? $departmentStudyProgram['StudyProgram']['study_program_name'] . ' (' . $departmentStudyProgram['StudyProgram']['code'] . ')'  : '') . (isset($departmentStudyProgram['ProgramModality']['modality']) && isset($departmentStudyProgram['Qualification']['qualification']) ?  ' : ' . $departmentStudyProgram['Qualification']['qualification'] . ', ' .  $departmentStudyProgram['ProgramModality']['modality'] . '' : ''); ?></span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <table cellpadding="0" cellspacing="0" class="table-borderless">
                    <tbody>
                        <tr>
                            <td><span class="text-gray" style="font-weight: bold;">Department:</span> &nbsp;&nbsp; <?= $departmentStudyProgram['Department']['name']; ?></td>
                        </tr>
                        <tr>
                            <td><span class="text-gray" style="font-weight: bold;">Study Program:</span> &nbsp;&nbsp; <?= $departmentStudyProgram['StudyProgram']['study_program_name']; ?></td>
                        </tr>
                        <tr>
                            <td><span class="text-gray" style="font-weight: bold;">Study Program Code:</span> &nbsp;&nbsp; <?= $departmentStudyProgram['StudyProgram']['code']; ?></td>
                        </tr>
                        <tr>
                            <td><span class="text-gray" style="font-weight: bold;">Qualification:</span> &nbsp;&nbsp; <?= $departmentStudyProgram['Qualification']['qualification'] . ' (' . $departmentStudyProgram['Qualification']['code'] . ')'; ?></td>
                        </tr>
                        <tr>
                            <td><span class="text-gray" style="font-weight: bold;">Program Modality:</span> &nbsp;&nbsp; <?= $departmentStudyProgram['ProgramModality']['modality'] . ' (' . $departmentStudyProgram['ProgramModality']['code'] . ')'; ?></td>
                        </tr>
                        <tr>
                            <td><span class="text-gray" style="font-weight: bold;">From Adademic Year:</span> &nbsp;&nbsp; <?= $departmentStudyProgram['DepartmentStudyProgram']['academic_year']; ?></td>
                        </tr>
                        <tr>
                            <td><span class="text-gray" style="font-weight: bold;">Applied for Current Students:</span> &nbsp;&nbsp; <?= (isset($departmentStudyProgram['DepartmentStudyProgram']['apply_for_current_students']) && $departmentStudyProgram['DepartmentStudyProgram']['apply_for_current_students'] == 1 ? 'Yes' : 'No'); ?> </td>
                        </tr>
                        <?php
                        if (!empty($departmentStudyProgram['DepartmentStudyProgram']['created']) && $departmentStudyProgram['DepartmentStudyProgram']['created'] != '0000-00-00') { ?>
                            <tr>
                                <td><span class="text-gray" style="font-weight: bold;">Created:</span> &nbsp;&nbsp; <?= $this->Time->format("M j, Y g:i A", $departmentStudyProgram['DepartmentStudyProgram']['created'], NULL, NULL); ?> </td>
                            </tr>
                            <?php
                        }
                        if (!empty($departmentStudyProgram['DepartmentStudyProgram']['modified']) && $departmentStudyProgram['DepartmentStudyProgram']['modified'] != '0000-00-00') { ?>
                            <tr>
                                <td><span class="text-gray" style="font-weight: bold;">Modified:</span> &nbsp;&nbsp; <?= $this->Time->format("M j, Y g:i A", $departmentStudyProgram['DepartmentStudyProgram']['modified'], NULL, NULL); ?> </td>
                            </tr>
                            <?php
                        } ?>
                    </tbody>
                </table>


                <?php
                //debug($associated_curriculums);
                if (!empty($associated_curriculums)) { ?>
                    <div class="related">
                        <hr>
                        <h6 class="text-gray">Attached Curriculums to  <?= $departmentStudyProgram['StudyProgram']['study_program_name'] . ' (' . $departmentStudyProgram['StudyProgram']['code'] . ') study program'; ?></h6>
                        <div style="overflow-x:auto;">
                            <table cellpadding="0" cellspacing="0" class="table">
                                <thead>
                                    <tr>
                                        <td class="center">#</td>
                                        <td class="vcenter">Curriculumm Name</td>
                                        <td class="vcenter">Introduced</td>
                                        <td class="center">Credit Type</td>
                                        <td class="center">Degree Nomenclature</td>
                                        <td class="center">Credits</td>
                                        <td class="center">Department</td>
                                        <td class="center">Program</td>
                                        <td class="center">Active</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $count1 = 1;
                                    foreach ($associated_curriculums as $associated_curriculum) { ?>
                                        <tr>
                                            <td class="center"><?= $count1++; ?></td>
                                            <td class="vcenter"><?= $associated_curriculum['Curriculum']['name']; ?></td>
                                            <td class="center"><?= $this->Time->format("M j, Y", $associated_curriculum['Curriculum']['year_introduced'], NULL, NULL); ?></td>
                                            <td class="center"><?= (count(explode('ECTS', $associated_curriculum['Curriculum']['type_credit'])) == 2 ? 'ECTS' : 'Credit'); ?></td>
                                            <td class="center"><?= $associated_curriculum['Curriculum']['english_degree_nomenclature']; ?></td>
                                            <td class="center"><?= $associated_curriculum['Curriculum']['minimum_credit_points']; ?></td>
                                            <td class="center"><?= $associated_curriculum['Department']['name']; ?></td>
                                            <td class="center"><?= (!empty($associated_curriculum['Program']['shortname']) ? $associated_curriculum['Program']['shortname'] : $associated_curriculum['Program']['name']); ?></td>
                                            <td class="center"><?= (isset($associated_curriculum['Curriculum']['active']) && $associated_curriculum['Curriculum']['active'] == 1 ? '<span style="color:green">Yes</span>' : '<span style="color:red">No</span>'); ?></td>
                                        </tr>
                                        <?php
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                        <br>
                    </div>
                <?php
                } ?>

                <?php
                //debug($associated_curriculums);
                if (!empty($similar_curriculums)) { ?>
                    <div class="related">
                        <hr>
                        <h6 class="text-gray">Related curriculums with study program: <?= $departmentStudyProgram['StudyProgram']['study_program_name'] . ' (' . $departmentStudyProgram['StudyProgram']['code'] . ')'; ?></h6>
                        <div style="overflow-x:auto;">
                            <table cellpadding="0" cellspacing="0" class="table">
                                <thead>
                                    <tr>
                                        <td class="center">#</td>
                                        <td class="vcenter">Curriculumm Name</td>
                                        <td class="vcenter">Introduced</td>
                                        <td class="center">Credit Type</td>
                                        <td class="center">Degree Nomenclature</td>
                                        <td class="center">Credits</td>
                                        <td class="center">Department</td>
                                        <td class="center">Program</td>
                                        <td class="center">Active</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $count1 = 1;
                                    foreach ($similar_curriculums as $similar_curriculum) { ?>
                                        <tr>
                                            <td class="center"><?= $count1++; ?></td>
                                            <td class="vcenter"><?= $similar_curriculum['Curriculum']['name']; ?></td>
                                            <td class="center"><?= $this->Time->format("M j, Y", $similar_curriculum['Curriculum']['year_introduced'], NULL, NULL); ?></td>
                                            <td class="center"><?= (count(explode('ECTS', $similar_curriculum['Curriculum']['type_credit'])) == 2 ? 'ECTS' : 'Credit'); ?></td>
                                            <td class="center"><?= $similar_curriculum['Curriculum']['english_degree_nomenclature']; ?></td>
                                            <td class="center"><?= $similar_curriculum['Curriculum']['minimum_credit_points']; ?></td>
                                            <td class="center"><?= $similar_curriculum['Department']['name']; ?></td>
                                            <td class="center"><?= (!empty($similar_curriculum['Program']['shortname']) ? $similar_curriculum['Program']['shortname'] : $similar_curriculum['Program']['name']); ?></td>
                                            <td class="center"><?= (isset($similar_curriculum['Curriculum']['active']) && $similar_curriculum['Curriculum']['active'] == 1 ? '<span style="color:green">Yes</span>' : '<span style="color:red">No</span>'); ?></td>
                                        </tr>
                                        <?php
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                        <br>
                    </div>
                <?php
                } ?>

            </div>
        </div>
    </div>
</div>