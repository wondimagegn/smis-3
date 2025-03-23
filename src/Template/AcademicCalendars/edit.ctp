<div class="box">
    <div class="box-header bg-transparent">
        <div class="box-title" style="margin-top: 10px;">
            <i class="fontello-edit" style="font-size: larger; font-weight: bold;"></i>
            <span style="font-size: medium; font-weight: bold; margin-top: 20px;">
                <?= __('Edit Academic Calendar'); ?>
            </span>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="large-12 columns">
                <div style="margin-top: -30px;">
                    <hr>
                </div>
                <?= $this->Form->create($academicCalendar); ?>
                <?= $this->Form->control('id'); ?>

                <fieldset style="padding-bottom: 15px;padding-top: 15px;">
                    <div class="row">
                        <div class="large-2 columns">
                            <?= $this->Form->control('academic_year', [
                                'id' => 'academicYear',
                                'label' => 'Academic Year:',
                                'style' => 'width:90%',
                                'type' => 'select',
                                'required' => true,
                                'options' => $acyearArrayData,
                                'empty' => '[ Select Academic Year ]',
                                'default' => $defaultAcademicYear
                            ]); ?>
                        </div>
                        <div class="large-2 columns">
                            <?= $this->Form->control('semester', [
                                'id' => 'semester',
                                'label' => 'Semester:',
                                'style' => 'width:90%',
                                'options' => Configure::read('semesters'),
                                'required' => true,
                                'empty' => '[ Select Semester ]'
                            ]); ?>
                        </div>
                        <div class="large-3 columns">
                            <?= $this->Form->control('program_id', [
                                'id' => 'programType',
                                'label' => 'Program:',
                                'style' => 'width:90%',
                                'required' => true,
                                'empty' => '[ Select ]'
                            ]); ?>
                        </div>
                        <div class="large-3 columns">
                            <?= $this->Form->control('program_type_id', [
                                'id' => 'programTypeId',
                                'label' => 'Program Type:',
                                'style' => 'width:90%',
                                'required' => true,
                                'empty' => '[ Select ]'
                            ]); ?>
                        </div>
                        <div class="large-2 columns">
                            <h6 id="validation-message_non_selected" class="text-red fs14"></h6>
                            <?= $this->Form->control('year_level_id', [
                                'type' => 'select',
                                'id' => 'yearLevels',
                                'multiple' => 'checkbox'
                            ]); ?>
                        </div>
                    </div>
                </fieldset>
                <hr>
                <table cellpadding="0" cellspacing="0" class="table">
                    <tbody>
                    <tr>
                        <td style="background-color: white;">
                            <?= $this->Form->control('department_id', [
                                'multiple' => 'checkbox',
                                'options' => $departments,
                                'label' => false,
                                'checked' => isset($academicCalendar->department_id) ? $academicCalendar->department_id : array_keys(
                                    $departments_ids
                                )
                            ]); ?>
                        </td>
                        <td style="background-color: white;">
                            <table cellpadding="0" cellspacing="0" class="table">
                                <tbody>
                                <?php
                                $fields = [
                                    'course_registration_start_date' => 'Registration Start',
                                    'course_registration_end_date' => 'Registration End',
                                    'course_add_start_date' => 'Course Add Start',
                                    'course_add_end_date' => 'Course Add End',
                                    'course_drop_start_date' => 'Course Drop Start',
                                    'course_drop_end_date' => 'Course Drop End',
                                    'grade_submission_start_date' => 'Grade Submission Start',
                                    'grade_submission_end_date' => 'Grade Submission End',
                                    'grade_fx_submission_end_date' => 'Fx Grade Submission',
                                    'senate_meeting_date' => 'Senate Meeting Date',
                                    'graduation_date' => 'Graduation Date',
                                    'online_admission_start_date' => 'Online Admission Start Date',
                                    'online_admission_end_date' => 'Online Admission End Date'
                                ];

                                foreach ($fields as $field => $label) {
                                    echo '<tr><td>' . $this->Form->control($field, [
                                            'label' => $label,
                                            'type' => 'date',
                                            'minYear' => date('Y') - 2,
                                            'maxYear' => date('Y') + 1,
                                            'style' => 'width:80px;'
                                        ]) . '</td></tr>';
                                }
                                ?>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <hr>
                <?= $this->Form->button(__('Save Changes'), ['class' => 'tiny radius button bg-blue']); ?>
                <?= $this->Form->end(); ?>
            </div>
        </div>
    </div>
</div>
