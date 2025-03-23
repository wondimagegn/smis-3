<div class="box">
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <h2 class="box-title">
                    <?= __('Academic Calendars'); ?>
                </h2>
            </div>
            <div class="large-12 columns">
                <dl style="float:left">
                    <dt>
                        <div class="smallheading"><?= __('Academic Calendar'); ?></div>
                    </dt>
                    <dt><?= __('Academic Year'); ?></dt>
                    <dd><?= h($academicCalendar->academic_year); ?></dd>
                    <dt><?= __('Semester'); ?></dt>
                    <dd><?= h($academicCalendar->semester); ?></dd>
                    <dt><?= __('Program'); ?></dt>
                    <dd><?= $this->Html->link(
                            $academicCalendar->program->name,
                            ['controller' => 'Programs', 'action' => 'view', $academicCalendar->program->id]
                        ); ?></dd>
                    <dt><?= __('Program Type'); ?></dt>
                    <dd><?= $this->Html->link(
                            $academicCalendar->program_type->name,
                            ['controller' => 'ProgramTypes', 'action' => 'view', $academicCalendar->program_type->id]
                        ); ?></dd>

                    <?php
                    $fields = [
                        'course_registration_start_date' => __('Course Registration Start Date'),
                        'course_registration_end_date' => __('Course Registration End Date'),
                        'course_add_start_date' => __('Course Add Start Date'),
                        'course_add_end_date' => __('Course Add End Date'),
                        'course_drop_start_date' => __('Course Drop Start Date'),
                        'course_drop_end_date' => __('Course Drop End Date'),
                        'grade_submission_start_date' => __('Grade Submission Start Date'),
                        'grade_submission_end_date' => __('Grade Submission End Date'),
                        'grade_fx_submission_end_date' => __('Fx Grade Submission End Date'),
                        'senate_meeting_date' => __('Senate Meeting Date'),
                        'graduation_date' => __('Graduation Date'),
                        'online_admission_start_date' => __('Online Admission Start Date'),
                        'online_admission_end_date' => __('Online Admission End Date'),
                    ];
                    foreach ($fields as $field => $label): ?>
                        <dt><?= $label; ?></dt>
                        <dd><?= $academicCalendar->{$field} ? $this->Time->format($academicCalendar->{$field}) : __(
                                'N/A'
                            ); ?></dd>
                    <?php
                    endforeach; ?>

                    <dt>
                        <div class="smallheading"><?= __('Year Level'); ?></div>
                    </dt>
                    <ul>
                        <?php
                        foreach ($academicCalendar->year_level_id as $yearLevel): ?>
                            <li><?= h($yearLevel); ?></li>
                        <?php
                        endforeach; ?>
                    </ul>
                </dl>
                <dl style="float:left;width:35%">
                    <dt>
                        <div class="smallheading"><?= __('College and Department which has this calendar'); ?></div>
                    </dt>
                    <ul>
                        <?php
                        foreach ($colleges as $college_id => $college_name): ?>
                            <li><?= h($college_name); ?>
                                <ul>
                                    <?php
                                    if (!empty($college_department[$college_id])): ?>
                                        <?php
                                        foreach ($college_department[$college_id] as $department_id => $department_name): ?>
                                            <li><?= h($department_name); ?></li>
                                        <?php
                                        endforeach; ?>
                                    <?php
                                    endif; ?>
                                </ul>
                            </li>
                        <?php
                        endforeach; ?>
                    </ul>
                </dl>
            </div>
        </div>
    </div>
</div>
