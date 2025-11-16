
<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;">
            <i class="fontello-th-list" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;"><?= __('List Published Courses') ?></span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <div style="margin-top: -30px;">
                    <?= $this->Form->create(null, ['url' => ['action' => 'index'], 'type' => 'post']) ?>
                    <hr>
                    <?php if (!isset($search_published_course)) : ?>
                        <fieldset style="padding-bottom: 0px; padding-top: 15px;">
                            <div class="row">
                                <div class="large-3 columns">
                                    <?= $this->Form->control('PublishedCourse.academic_year', [
                                        'label' => 'Academic Year: ',
                                        'style' => 'width:90%;',
                                        'type' => 'select',
                                        'options' => $acyear_array_data,
                                        'default' => isset($academic_year) ? $academic_year : $defaultacademicyear
                                    ]) ?>
                                </div>
                                <div class="large-3 columns">
                                    <?= $this->Form->control('PublishedCourse.semester', [
                                        'label' => 'Semester: ',
                                        'style' => 'width:90%;',
                                        'type' => 'select',
                                        'options' => \Cake\Core\Configure::read('semesters'),
                                        'default' => isset($semester) ? $semester : $defaultsemester
                                    ]) ?>
                                </div>
                                <div class="large-3 columns">
                                    <?= $this->Form->control('PublishedCourse.program_id', [
                                        'label' => 'Program: ',
                                        'style' => 'width:90%;',
                                        'type' => 'select',
                                        'options' => $programs
                                    ]) ?>
                                </div>
                                <div class="large-3 columns">
                                    <?= $this->Form->control('PublishedCourse.program_type_id', [
                                        'label' => 'Program Type: ',
                                        'style' => 'width:90%;',
                                        'type' => 'select',
                                        'options' => $programTypes
                                    ]) ?>
                                </div>
                            </div>
                            <hr>
                            <?= $this->Form->button(__('Search'), ['name' => 'search'
                                ,'value'=>'search', 'class' => 'tiny radius button bg-blue']) ?>
                        </fieldset>
                    <?php endif; ?>
                    <?= $this->Form->end() ?>
                    <hr>

                    <?php if (isset($publishedCourses) && !empty($publishedCourses)) : ?>
                    <div style="overflow-x:auto;">
                        <?php foreach ($publishedCourses as $sk => $sv) : ?>
                        <?php if (!empty($sv)) : ?>
                        <?php
                        $count = 1;
                        foreach ($sv as $pk => $pv) :
                        if (!empty($pk)) :
                        foreach ($pv as $ptk => $ptv) :
                        if (!empty($ptk)) :
                        foreach ($ptv as $deptKey => $deptValue) :
                        foreach ($deptValue as $yk => $yv) :
                        if (!empty($yv)) :
                        foreach ($yv as $section_name => $section_value) :
                        $total_published_credits = 0;
                        ?>
                        <table cellpadding="0" cellspacing="0" class="table">
                            <thead>
                            <tr>
                                <td colspan="5" style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85); line-height: 1.5;">
                                                                                    <span style="font-size:16px; font-weight:bold; margin-top: 25px;">
                                                                                        <?= h(isset($section_name) ? $section_name . ' ' . (isset($yk) ? ' (' . $yk . ', ' : (isset($section_value[0]->section->year_level->name) ? $section_value[0]->section->year_level->name . ', ' : ' (Pre/1st, ')) : '') . (isset($academic_year) ? $academic_year : (isset($section_value[0]->academic_year) ? $section_value[0]->academic_year : '')) . ', ' . (isset($sk) ? ($sk == 'I' ? '1st Semester' : ($sk == 'II' ? '2nd Semester' : ($sk == 'III' ? '3rd Semester' : $sk . ' Semester'))) : (isset($section_value[0]->semester) ? $section_value[0]->semester : '')) . ')'; ?>
                                                                                    </span>
                                    <br>
                                    <span class="text-gray" style="padding-top: 13px; font-size: 13px; font-weight: bold">
                                                                                        <?= h(isset($deptKey) ? $deptKey : (isset($section_value[0]->department->name) ? $section_value[0]->department->name : $section_value[0]->college->name . ' Pre/Freshman')); ?>
                                                                                        &nbsp; | &nbsp;
                                                                                        <?= h(isset($pk) ? $pk : (isset($section_value[0]->program->name) ? $section_value[0]->program->name : '')); ?>
                                                                                        &nbsp; | &nbsp;
                                                                                        <?= h(isset($ptk) ? $ptk : (isset($section_value[0]->program_type->name) ? $section_value[0]->program_type->name : '')); ?>
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
                            <?php foreach ($section_value as $type_index => $section_value_detail) : ?>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td colspan="4"><?= h($type_index) ?></td>
                                </tr>
                                <?php foreach ($section_value_detail as $publishedCourse) : ?>
                                    <?php if (!empty($publishedCourse)) : ?>
                                        <tr>
                                            <td class="center"><?= $count++ ?></td>
                                            <td class="vcenter">
                                                <?= $this->Html->link(
                                                    h($publishedCourse->course->course_title),
                                                    ['action' => 'view', $publishedCourse->id]
                                                ) ?>
                                            </td>
                                            <td class="center"><?= h($publishedCourse->course->course_code) ?></td>
                                            <td class="center"><?= h($publishedCourse->course->credit) ?></td>
                                            <td class="center"><?= h($publishedCourse->course->course_detail_hours) ?></td>
                                        </tr>
                                        <?php if (!empty($publishedCourse->course->credit)) : ?>
                                            <?php $total_published_credits += $publishedCourse->course->credit ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="2"></td>
                                <td class="center">Total</td>
                                <td class="center"><?= $total_published_credits ?></td>
                                <td></td>
                            </tr>
                            <?php if (!empty($section_value_detail[0]->section->curriculum->name)) : ?>
                                <tr>
                                    <td></td>
                                    <td colspan="4" class="vcenter" style="font-weight: normal;">
                                        <?= '<b>'.h('Section Curriculum:') .'</b>'. h(ucwords(strtolower($section_value_detail[0]->section->curriculum->name)) . ' - ' . $section_value_detail[0]->section->curriculum->year_introduced . ' (' . (str_contains($section_value_detail[0]->section->curriculum->type_credit, 'ECTS') ? 'ECTS' : 'Credit') . ')') ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            </tfoot>
                        </table>
                    </div>
                <br><br>
                <?php endforeach; ?>
                <?php endif; ?>
                <?php endforeach; ?>
                <?php endforeach; ?>
                <?php endif; ?>
                <?php endforeach; ?>
                <?php endif; ?>
                <?php endforeach; ?>
                <?php endif; ?>
                <?php endforeach; ?>
                </div>
                <?php endif; ?>

                    <?php if (isset($publishedCoursesCollege) && !empty($publishedCoursesCollege)) : ?>
                    <div style="overflow-x:auto;">
                        <?php
                        $count = 1;
                        foreach ($publishedCoursesCollege as $pk => $pv) :
                            if (!empty($pk)) :
                                foreach ($pv as $ptk => $ptv) :
                                    if (!empty($ptk)) :
                                        foreach ($ptv as $collKey => $collValue) :
                                            foreach ($collValue as $section_name => $section_value) :
                                                $total_published_credits = 0;
                                                ?>
                                                <table cellpadding="0" cellspacing="0" class="table">
                                                    <thead>
                                                    <tr>
                                                        <td colspan="5" style="vertical-align:middle; border-bottom-width: 2px; border-bottom-style: solid; border-bottom-color: rgb(85, 85, 85); line-height: 1.5;">
                                                                    <span style="font-size:16px; font-weight:bold; margin-top: 25px;">
                                                                        <?= h(isset($section_name) ? $section_name . (isset($section_value['Semester Registered'][0]->program_id) && $section_value['Semester Registered'][0]->program_id == PROGRAM_REMEDIAL ? ' (Remedial)' : ' (Pre/1st)') : ' (Pre/1st)') ?>
                                                                    </span>
                                                            <br>
                                                            <span class="text-gray" style="padding-top: 13px; font-size: 13px; font-weight: bold">
                                                                        <?= h(isset($collKey) ? $collKey . (isset($section_value['Semester Registered'][0]->program_id) && $section_value['Semester Registered'][0]->program_id == PROGRAM_REMEDIAL ? ' - Remedial' : ' - Pre/Freshman') : ' Pre/Freshman') ?>
                                                                        &nbsp; | &nbsp;
                                                                        <?= h(isset($pk) ? $pk : (isset($section_value['Semester Registered'][0]->program->name) ? $section_value['Semester Registered'][0]->program->name : '')) ?>
                                                                        &nbsp; | &nbsp;
                                                                        <?= h(isset($ptk) ? $ptk : (isset($section_value['Semester Registered'][0]->program_type->name) ? $section_value['Semester Registered'][0]->program_type->name : '')) ?>
                                                                    </span>
                                                            <br>
                                                            <span class="text-black" style="padding-top: 14px; font-size: 13px; font-weight: bold">
                                                                        <?= h(isset($academic_year) ? $academic_year : (isset($section_value['Semester Registered'][0]->academic_year) ? $section_value['Semester Registered'][0]->academic_year : '')) ?>
                                                                        &nbsp; | &nbsp;
                                                                        <?= h(isset($semester) ? ($semester == 'I' ? '1st Semester' : ($semester == 'II' ? '2nd Semester' : ($semester == 'III' ? '3rd Semester' : $semester . ' Semester'))) : (isset($section_value['Semester Registered'][0]->semester) ? $section_value['Semester Registered'][0]->semester : '')) ?>
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
                                                    <?php foreach ($section_value as $type_index => $section_value_detail) : ?>
                                                        <tr>
                                                            <td class="center">&nbsp;</td>
                                                            <td colspan="4"><?= h($type_index) ?></td>
                                                        </tr>
                                                        <?php foreach ($section_value_detail as $publishedCourse) : ?>
                                                            <?php if (!empty($publishedCourse)) : ?>
                                                                <tr>
                                                                    <td class="center"><?= $count++ ?></td>
                                                                    <td class="vcenter">
                                                                        <?= $this->Html->link(
                                                                            h($publishedCourse->course->course_title),
                                                                            ['action' => 'view', $publishedCourse->id]
                                                                        ) ?>
                                                                    </td>
                                                                    <td class="center"><?= h($publishedCourse->course->course_code) ?></td>
                                                                    <td class="center"><?= h($publishedCourse->course->credit) ?></td>
                                                                    <td class="center"><?= h($publishedCourse->course->course_detail_hours) ?></td>
                                                                </tr>
                                                                <?php if (!empty($publishedCourse->course->credit)) : ?>
                                                                    <?php $total_published_credits += $publishedCourse->course->credit ?>
                                                                <?php endif; ?>
                                                            <?php endif; ?>
                                                        <?php endforeach; ?>
                                                    <?php endforeach; ?>
                                                    </tbody>
                                                    <tfoot>
                                                    <tr>
                                                        <td colspan="2"></td>
                                                        <td class="center">Total</td>
                                                        <td class="center"><?= $total_published_credits ?></td>
                                                        <td></td>
                                                    </tr>
                                                    </tfoot>
                                                </table>
                                                <br><br>
                                            <?php endforeach; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                </div>
            </div>
        </div>
</div>
