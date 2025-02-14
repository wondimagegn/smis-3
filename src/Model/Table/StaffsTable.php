<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class StaffsTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('staff');
        $this->setPrimaryKey('id');
        $this->setDisplayField('full_name');

        // Add Timestamp behavior for created/modified tracking
        $this->addBehavior('Timestamp');

        // Define Relationships
        $this->belongsTo('Users');
        $this->belongsTo('Colleges');
        $this->belongsTo('Positions');
        $this->belongsTo('Departments');
        $this->belongsTo('Titles');
        $this->belongsTo('Countries');
        $this->belongsTo('Regions');
        $this->belongsTo('Zones');
        $this->belongsTo('Woredas');
        $this->belongsTo('Cities');
        $this->belongsTo('ServiceWings');
        $this->belongsTo('Educations');

        $this->hasMany('Invigilators');
        $this->hasMany('ColleagueEvaluationRates');
        $this->hasMany('Contacts');
        $this->hasMany('CourseInstructorAssignments');
        $this->hasMany('InstructorClassPeriodCourseConstraints');
        $this->hasMany('InstructorExamExcludeDateConstraints');
        $this->hasMany('InstructorNumberOfExamConstraints');
        $this->hasMany('StaffForExams');
        $this->hasMany('StaffStudies');

        $this->hasMany('Attachments', [
            'foreignKey' => 'foreign_key',
            'conditions' => ['model' => 'Staff'],
            'dependent' => true,
        ]);

        $this->belongsToMany('Courses', [
            'joinTable' => 'courses_staffs',
            'foreignKey' => 'staff_id',
            'targetForeignKey' => 'course_id',
            'unique' => true,
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->notEmptyString('first_name', 'Please enter staff first name.')
            ->notEmptyString('middle_name', 'Please enter staff middle name.')
            ->notEmptyString('last_name', 'Please enter staff last name.')
            ->notEmptyString('gender', 'Please select gender.')
            ->email('email', false, 'Please enter a valid email address.')
            ->add('email', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => 'This email is already used by another staff member.',
            ])
            ->notEmptyString('phone_mobile', 'Please enter a mobile phone number.')
            ->add('phone_mobile', 'validFormat', [
                'rule' => ['custom', '/^\+251[0-9]{9}$/'],
                'message' => 'Phone number must be in +251999999999 format.',
            ])
            ->add('phone_mobile', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => 'This mobile number is already in use.',
            ])
            ->allowEmptyString('staffid')
            ->add('staffid', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => 'The Staff ID is already in use. Please check and correct it.',
            ]);

        return $validator;
    }




    function canItBeDeleted($staff_id = null)
    {
        if ($this->CourseInstructorAssignment->find('count', array('conditions' => array('CourseInstructorAssignment.staff_id' => $staff_id))) > 0) {
            return false;
        } else {
            if ($this->find('count', array('conditions' => array('Staff.user_id != ""', 'Staff.id' => $staff_id))) > 0) {
                return false;
            } else {
                return true;
            }
        }
    }

    function checkLengthPhone($data, $fieldName)
    {
        $valid = true;
        if (isset($fieldName) && $this->hasField($fieldName)) {
            $check = strlen($data[$fieldName]);
            debug($check);
            if ($check != 13) {
                $valid = false;
            }
        }
        return $valid;
    }

    function getInvigilators($college_id = null, $acadamic_year = null, $semester = null, $exam_date = null, $session = null, $year_level = null)
    {
        $staffsForExam = array();

        $department_ids = $this->Department->find('list', array(
            'conditions' => array(
                'Department.college_id' => $college_id
            ),
            'fields' => array('id')
        ));

        $staffs = $this->find('all', array(
            'conditions' => array(
                'Staff.department_id' => $department_ids,
                'Staff.active' => 1,
                'Staff.id NOT IN (SELECT staff_id FROM instructor_exam_exclude_date_constraints WHERE exam_date = \'' . $exam_date . '\' AND session = \'' . $session . '\')',
                //Exclude already assigned invigilators
                'Staff.id NOT IN (SELECT staff_id FROM invigilators WHERE exam_schedule_id IN (SELECT id FROM exam_schedules WHERE exam_date = \'' . $exam_date . '\' AND session = \'' . $session . '\'))',
            ),
            'contain' => array(
                'InstructorNumberOfExamConstraint' => array(
                    'conditions' => array(
                        'InstructorNumberOfExamConstraint.academic_year' => $acadamic_year,
                        'InstructorNumberOfExamConstraint.semester' => $semester,
                        'InstructorNumberOfExamConstraint.year_level_id' => $year_level
                    )
                )
            )
        ));

        //debug($staffs);
        $i = 0;

        if (!empty($staffs)) {
            foreach($staffs as $staff) {
                $staffsForExam[$i]['id'] = $staff['Staff']['id'];
                if (!empty($staff['InstructorNumberOfExamConstraint'])) {
                    $staffsForExam[$i]['max_number_of_exam'] = $staff['InstructorNumberOfExamConstraint'][0]['max_number_of_exam'];
                } else {
                    $staffsForExam[$i]['max_number_of_exam'] = 0;
                }
                $staffsForExam[$i]['assigned_exam'] = 0;

                //TODO: count already assigned by year level
                $assigned_exams = $this->Invigilator->ExamSchedule->find('all', array(
                    'conditions' => array(
                        'ExamSchedule.acadamic_year' => $acadamic_year,
                        'ExamSchedule.semester' => $semester,
                        'ExamSchedule.id IN (SELECT exam_schedule_id FROM invigilators WHERE staff_id = \'' . $staff['Staff']['id'] . '\')'
                    ),
                    'contain' => array(
                        'PublishedCourse' => array(
                            'Section' => array(
                                'YearLevel'
                            )
                        ),
                    )
                ));

                if (!empty($assigned_exams)) {
                    foreach($assigned_exams as $assigned_exam) {
                        if (strcasecmp($assigned_exam['PublishedCourse']['Section']['YearLevel']['name'], $year_level) == 0) {
                            $staffsForExam[$i]['assigned_exam']++;
                        }
                    }
                }
                //debug($assigned_exams);
                $i++;
            }
        }
        //debug($staffsForExam);
        return $staffsForExam;
    }

    function getInstructorReformated ($department_id = null, $college_id = null)
    {
        if (!empty($department_id)) {
            $instructors = $this->Staff->find('all', array(
                'conditions' => array(
                    'Staff.department_id' => $department_id,
                    'Staff.id  IN (SELECT id FROM staffs WHERE  user_id IN (SELECT id FROM users WHERE role_id=' . ROLE_INSTRUCTOR . ' )))'
                ),
                'contain' => array('Position' => array('id', 'position'), 'Title' => array('id', 'title'))
            ));
        } else if (!empty($college_id)) {
            $instructors = $this->Staff->find('all', array(
                'conditions' => array(
                    'Staff.college_id' => $college_id,
                    'Staff.id  IN (SELECT id FROM staffs WHERE  user_id IN (SELECT id FROM users WHERE role_id=' . ROLE_INSTRUCTOR . ' )))'
                ),
                'contain' => array('Position' => array('id', 'position'), 'Title' => array('id', 'title'))
            ));
        } else {
            $instructors = $this->Staff->find('all', array(
                'conditions' => array(
                    'Staff.id  IN (SELECT id FROM staffs WHERE  user_id IN  (SELECT id FROM users WHERE role_id=' . ROLE_INSTRUCTOR . ' )))'
                ),
                'contain' => array('Department' => array('id', 'name'), 'Position' => array('id', 'position'), 'Title' => array('id', 'title'))
            ));
        }

        $instructor_list = array();

        if(!empty($instructors)) {
            foreach ($instructors as $index => $value) {
                $instructor_list[$value['Department']['name']][$value['Staff']['id']] = $value['Title']['title'] . ' ' . $value['Staff']['full_name'] . '(' . $value['Position']['position'] . ')';
            }
        }

        return $instructor_list;
    }

    public function getDistributionStats($department_id = null, $sex = 'all')
    {
        $query = "";
        $student_ids = array();
        $options = array();
        $collegeId = false;
        $departments = array();
        $graph['data'] = array();
        $graph['labels'] = array();
        $graph['series'] = array('male', 'female');

        // list out the department
        if (isset($department_id) && !empty($department_id)) {
            $college_id = explode('~', $department_id);
            if (count($college_id) > 1) {
                $departments = $this->Department->find('all', array(
                    'conditions' => array(
                        'Department.college_id' => $college_id[1],
                        'Department.active' => 1
                    ),
                    'contain' => array('College', 'YearLevel')
                ));
            } else {
                $departments = $this->Department->find('all', array('conditions' => array('Department.id' => $department_id, 'Department.active' => 1), 'contain' => array('College', 'YearLevel')));
            }
        } else {
            $departments = $this->Department->find('all', array('conditions' => array('Department.active' => 1), 'contain' => array('College', 'YearLevel')));
        }

        $distributionStatsTeachersByGender = array();
        debug($departments);
        debug($sex);

        if (!empty($departments)) {
            foreach ($departments as $key => $value) {
                if (($sex == "male" || $sex == "female")) {

                    $numberofinstructors = $this->find('count', array(
                        'conditions' => array(
                            'Staff.user_id  IN (SELECT id FROM users WHERE role_id=' . ROLE_INSTRUCTOR . ' )',
                            'Staff.gender' => $sex,
                            'Staff.department_id' => $value['Department']['id'],
                            'Staff.active' => 1
                        )
                    ));

                    debug($numberofinstructors);
                    $distributionStatsTeachersByGender[$value['Department']['name']][strtolower($sex)] = $numberofinstructors;

                    $graph['labels'][$value['Department']['id']] = $value['Department']['name'];

                    if (strtolower($sex) == "female") {
                        $indexS = 1;
                    } else if (strtolower($sex) == "male") {
                        $indexS = 0;
                    }

                    $graph['data'][$indexS][$value['Department']['id']] += $numberofinstructors;

                } else if ($sex == "all") {

                    $sexList = array('male' => 'male', 'female' => 'female');

                    foreach ($sexList as $skey => $svalue) {

                        $numberofinstructors = $this->find('count', array(
                            'conditions' => array(
                                'Staff.user_id IN  (SELECT id FROM users WHERE role_id = ' . ROLE_INSTRUCTOR . ' )',
                                'Staff.gender' => $svalue,
                                'Staff.department_id' => $value['Department']['id'],
                                'Staff.active' => 1
                            )
                        ));

                        $distributionStatsTeachersByGender[$value['Department']['name']][strtolower($svalue)] = $numberofinstructors;
                        $graph['labels'][$value['Department']['id']] = $value['Department']['name'];

                        if (strtolower($svalue) == "female") {
                            $indexS = 1;
                        } else if (strtolower($svalue) == "male") {
                            $indexS = 0;
                        }

                        $graph['data'][$indexS][$value['Department']['id']] += $numberofinstructors;

                    }
                }
            }
        }

        $distribution['distributionStatsTeachersByGender'] = $distributionStatsTeachersByGender;
        $distribution['graph'] = $graph;

        return $distribution;

    }

    public function getDistributionStatsByAcademicRank($department_id = null,$sex='all')
    {
        $query = "";
        $student_ids = array();
        $options = array();
        $collegeId = false;
        $departments = array();
        $graph['data'] = array();
        $graph['labels'] = array();
        $graph['series'] = array('male', 'female');

        // list out the department
        if (isset($department_id) && !empty($department_id)) {
            $college_id = explode('~', $department_id);
            if (count($college_id) > 1) {
                $departments = $this->Department->find('all', array(
                    'conditions' => array(
                        'Department.college_id' => $college_id[1],
                        'Department.active' => 1
                    ),
                    'contain' => array('College', 'YearLevel')
                ));
            } else {
                $departments = $this->Department->find('all', array('conditions' => array('Department.id' => $department_id, 'Department.active' => 1), 'contain' => array('College', 'YearLevel')));
            }
        } else {
            $departments = $this->Department->find('all', array('conditions' => array('Department.active' => 1), 'contain' => array('College', 'YearLevel')));
        }

        $distributionStatsTeachersByAcademicRank = array();
        $teacherPositionLists = $this->find('list', array(
            'conditions' => array(
                'Staff.user_id  IN (SELECT id FROM users WHERE role_id=' . ROLE_INSTRUCTOR . ' )',
                'Staff.active' => 1,
            ),
            'fields' => array('Staff.position_id'),
            'group' => array('Staff.position_id')
        ));

        $positions = $this->Position->find('list', array('fields' => array('id', 'position'), 'conditions' => array('Position.id' => $teacherPositionLists)));
        debug($positions);
        $graph['series'] = $positions;

        if (!empty($departments)) {
            foreach ($departments as $key => $value) {
                if (!empty($positions)) {
                    foreach ($positions as $pk => $pvalue) {
                        if (($sex == "male" || $sex == "female")) {
                            $numberofinstructors = $this->find('count', array(
                                'conditions' => array(
                                    'Staff.user_id IN (SELECT id FROM users WHERE role_id=' . ROLE_INSTRUCTOR . ' )',
                                    'Staff.gender' => $sex,
                                    'Staff.position_id' => $pk,
                                    'Staff.department_id' => $value['Department']['id'],
                                    'Staff.active' => 1,
                                )
                            ));

                            $distributionStatsTeachersByAcademicRank[$value['Department']['name']][strtolower($sex)][$pk] = $numberofinstructors;

                            $graph['labels'][$value['Department']['id']] = $value['Department']['name'];
                            $graph['data'][$pk][$value['Department']['id']] += $numberofinstructors;

                        } else if ($sex == "all") {
                            $sexList = array('male' => 'male', 'female' => 'female');
                            foreach ($sexList as $skey => $svalue) {
                                $numberofinstructors = $this->find('count', array(
                                    'conditions' => array(
                                        'Staff.user_id IN (SELECT id FROM users WHERE role_id=' . ROLE_INSTRUCTOR . ' )',
                                        'Staff.gender' => $svalue,
                                        'Staff.position_id' => $pk,
                                        'Staff.department_id' => $value['Department']['id'],
                                        'Staff.active' => 1,
                                    )
                                ));

                                $distributionStatsTeachersByAcademicRank[$value['Department']['name']][strtolower($svalue)][$pk] = $numberofinstructors;
                                $graph['labels'][$value['Department']['id']] = $value['Department']['name'];
                                $graph['data'][$pk][$value['Department']['id']] += $numberofinstructors;
                            }
                        }
                    }
                }
            }
        }

        $distribution['distributionStatsTeachersByAcademicRank'] = $distributionStatsTeachersByAcademicRank;
        $distribution['graph'] = $graph;

        return $distribution;
    }

    public function getDistributionStatsTeacherToStudents($department_id = null,$sex='all')
    {

        $query = "";
        $student_ids = array();
        $options = array();
        $collegeId = false;
        $departments = array();
        $graph['data'] = array();
        $graph['labels'] = array();

        if (isset($department_id) && !empty($department_id)) {
            $college_id = explode('~', $department_id);
            if (count($college_id) > 1) {
                $departments = $this->Department->find('all', array(
                    'conditions' => array(
                        'Department.college_id' => $college_id[1],
                        'Department.active' => 1
                    ),
                    'contain' => array('College', 'YearLevel')
                ));
            } else {
                $departments = $this->Department->find('all', array(
                    'conditions' => array(
                        'Department.id' => $department_id,
                        'Department.active' => 1
                    ),
                    'contain' => array('College', 'YearLevel')
                ));
            }
        } else {
            $departments = $this->Department->find('all', array(
                'conditions' => array(
                    'Department.active' => 1
                ),
                'contain' => array('College', 'YearLevel')
            ));
        }

        $getDistributionStatsTeacherToStudents = array();
        $graph['series'] = array('student', 'teacher');

        if (!empty($departments)) {
            foreach ($departments as $key => $value) {

                $totalnumberofinstructors = $this->find('count', array(
                        'conditions' => array(
                            'Staff.user_id  IN (SELECT id FROM users WHERE role_id=' . ROLE_INSTRUCTOR . ' )',
                            'Staff.department_id' => $value['Department']['id'],
                            'Staff.active' => 1
                        )
                    )
                );

                $totalnumberofstudents = ClassRegistry::init('Student')->find('count', array(
                    'conditions' => array(
                        'Student.department_id' => $value['Department']['id'],
                        'Student.graduated' => 0,
                        'Student.id in (select student_id from students_sections where archive = 0 )',
                    )
                ));

                if ($totalnumberofstudents != 0) {
                    $getDistributionStatsTeacherToStudents[$value['Department']['name']]['student'] = $totalnumberofstudents;
                    $getDistributionStatsTeacherToStudents[$value['Department']['name']]['teacher'] = $totalnumberofinstructors;
                    $graph['labels'][$value['Department']['id']] = $value['Department']['name'];
                    $graph['data'][0][$value['Department']['id']] = $totalnumberofstudents;
                    $graph['data'][1][$value['Department']['id']] = $totalnumberofinstructors;
                }
            }
        }

        $distribution['getDistributionStatsTeacherToStudents'] = $getDistributionStatsTeacherToStudents;
        $distribution['graph'] = $graph;

        return $distribution;
    }

    public function getActiveStaffList($department_id = null, $sex = 'all', $active = 1)
    {

        if (isset($department_id) && !empty($department_id)) {
            $college_id = explode('~', $department_id);
            if (count($college_id) > 1) {
                $departments = $this->Department->find('all', array(
                    'conditions' => array(
                        'Department.college_id' => $college_id[1],
                        'Department.active' => 1
                    ),
                    'contain' => array('College', 'YearLevel'),
                    'recursive' => -1
                ));
            } else {
                $departments = $this->Department->find('all', array(
                    'conditions' => array(
                        'Department.id' => $department_id,
                        'Department.active' => 1
                    ),
                    'contain' => array('College', 'YearLevel'),
                    'recursive' => -1
                ));
            }
        } else {
            $departments = $this->Department->find('all', array(
                'conditions' => array(
                    'Department.active' => 1
                ),
                'contain' => array('College', 'YearLevel'),
                'recursive' => -1
            ));
        }

        if (!empty($departments)) {
            foreach ($departments as $key => $value) {
                if ($sex == 'all') {
                    $staffList = $this->find('all', array(
                        'conditions' => array(
                            'Staff.department_id' => $value['Department']['id'],
                            'Staff.active' => $active,
                        ),
                        'contain' => array('Title', 'User', 'Position'),
                        'recursive' => -1
                    ));
                } else {
                    $staffList = $this->find('all', array(
                        'conditions' => array(
                            'Staff.department_id' => $value['Department']['id'],
                            'Staff.gender' => $sex,
                            'Staff.active' => $active
                        ),
                        'contain' => array('Title', 'Position', 'User'),
                        'recursive' => -1
                    ));
                }
                $getActiveStaffList[$value['Department']['name']] = $staffList;
            }
        }

        $distribution['getActiveStaffList'] = $getActiveStaffList;

        return $distribution;
    }


    function preparedAttachment($data = null, $group = null)
    {
        if (!empty($data['Attachment'])) {
            foreach ($data['Attachment'] as $in => &$dv) {
                if (empty($dv['file']['name']) && empty($dv['file']['type']) && empty($dv['tmp_name'])) {
                    unset($data['Attachment'][$in]);
                } else {
                    $dv['model'] = 'Staff';
                    $dv['group'] = $group;
                }
            }
        }

        return $data;
    }

    public function deleteDoubleStaff($department_id = null)
    {

        if (!empty($department_id)) {
            $staffs = $this->find('all', array(
                'conditions' => array(
                    'Staff.department_id' => $department_id,
                    'Staff.user_id in (select id from users where role_id = '. ROLE_INSTRUCTOR .')'
                ),
                'contain' => array(
                    'User',
                    'CourseInstructorAssignment' => array('limit' => 1),
                    'ColleagueEvalutionRate' => array('limit' => 1),
                ),
                'recursive' => -1
            ));
        } else {
            $staffs = $this->find('all', array(
                'conditions' => array('Staff.user_id in (select id from users where role_id = '. ROLE_INSTRUCTOR .')'),
                'contain' => array(
                    'User',
                    'CourseInstructorAssignment' => array('limit' => 1),
                    'ColleagueEvalutionRate' => array('limit' => 1),
                ),
                'recursive' => -1
            ));
        }

        if (!empty($staffs)) {
            foreach ($staffs as $key => $value) {
                # check if the staff has double
                $staffDouble = $this->find('list', array(
                    'conditions' => array(
                        'Staff.first_name' => trim($value['Staff']['first_name']),
                        'Staff.middle_name' => trim($value['Staff']['middle_name']),
                        'Staff.last_name' => trim($value['Staff']['last_name']),
                        'Staff.department_id' => $value['Staff']['department_id'],
                        //'Staff.active' => 1
                    ),
                    'fields' => array('Staff.id', 'Staff.user_id')
                ));


                if (count($staffDouble) > 1) {
                    // find the user account is not used and delete it

                    debug($staffDouble);

                    foreach ($staffDouble as $stfid => $stfuserid) {
                        if (empty($stfuserid) && empty($value['ColleagueEvalutionRate']) && empty($value['CourseInstructorAssignment'])) {
                            /* $checkCourseInstructorAssignments = $this->CourseInstructorAssignment->find('count', array('conditions' => array('CourseInstructorAssignment.staff_id' => $stfid)));
                            debug($checkCourseInstructorAssignments);

                            if ($checkCourseInstructorAssignments == 0) {
                                debug($delete = $this->delete($stfid));
                            } */
                            debug($delete = $this->delete($stfid));
                        }
                    }

                    $staffDouble = $this->find('list', array(
                        'conditions' => array(
                            'Staff.first_name' => trim($value['Staff']['first_name']),
                            'Staff.middle_name' => trim($value['Staff']['middle_name']),
                            'Staff.last_name' => trim($value['Staff']['last_name']),
                            'Staff.department_id' => $value['Staff']['department_id'],
                            //'Staff.active' => 1
                        ),
                        'fields' => array('Staff.user_id', 'Staff.id')
                    ));

                    if (count($staffDouble) > 1) {

                        //find out which account is most used and move the account
                        $mostRecentAccount = $this->User->find('first', array('conditions' => array('User.id' => array_keys($staffDouble), 'User.active' => 1), 'order' => array('User.last_login' => 'DESC'), 'recursive' => -1));

                        debug($mostRecentAccount);

                        if (!empty($mostRecentAccount)) {

                            $staffIdToBeUsed = $staffDouble[$mostRecentAccount['User']['id']];

                            debug($staffIdToBeUsed);

                            foreach ($staffDouble as $skey => $svalue) {

                                if ($svalue == $staffIdToBeUsed) {
                                    continue;
                                } else {

                                    debug($svalue);
                                    //what about colleague evaluations?? Neway

                                    // update course instructor assignment
                                    /* $CourseInstructorAssignments = $this->CourseInstructorAssignment->find('list', array('conditions' => array('CourseInstructorAssignment.staff_id' => $svalue), 'fields' => array('CourseInstructorAssignment.id', 'CourseInstructorAssignment.id')));
                                    debug($CourseInstructorAssignments);

                                    if (!empty($CourseInstructorAssignments)) {
                                        foreach ($CourseInstructorAssignments as $cia => $ciaid) {
                                            //update course instructor assignment with the recent one
                                            $this->CourseInstructorAssignment->id = $ciaid;
                                            $this->CourseInstructorAssignment->saveField('staff_id', $staffIdToBeUsed);
                                        }
                                    } */

                                    $date_modified = '"'. date('Y-m-d H:i:s') . '"';

                                    $courseInstructorAssignmentsUpdate = $this->CourseInstructorAssignment->find('list', array('conditions' => array('CourseInstructorAssignment.staff_id' => $svalue), 'fields' => array('CourseInstructorAssignment.id', 'CourseInstructorAssignment.id')));
                                    debug($courseInstructorAssignmentsUpdate);

                                    if (!empty($courseInstructorAssignmentsUpdate)) {
                                        $this->CourseInstructorAssignment->recursive = -1;
                                        $this->CourseInstructorAssignment->updateAll(array('CourseInstructorAssignment.staff_id' => $staffIdToBeUsed, 'CourseInstructorAssignment.modified' => $date_modified), array('CourseInstructorAssignment.id' => $courseInstructorAssignmentsUpdate));
                                    }

                                    $colleagueEvalutionRateUpdate = $this->ColleagueEvalutionRate->find('list', array('conditions' => array('ColleagueEvalutionRate.staff_id' => $svalue), 'fields' => array('ColleagueEvalutionRate.id', 'ColleagueEvalutionRate.id')));
                                    debug($colleagueEvalutionRateUpdate);

                                    if (!empty($colleagueEvalutionRateUpdate)) {
                                        $this->ColleagueEvalutionRate->recursive = -1;
                                        $this->ColleagueEvalutionRate->updateAll(array('ColleagueEvalutionRate.staff_id' => $staffIdToBeUsed, 'ColleagueEvalutionRate.modified' => $date_modified), array('ColleagueEvalutionRate.id' => $colleagueEvalutionRateUpdate));
                                    }

                                    //$delete = $this->User->delete($skey);
                                    //$delete = $this->delete($svalue);

                                    $this->User->updateAll(array('User.active' => 0, 'User.modified' => $date_modified), array('User.id' => $skey));
                                    $this->updateAll(array('Staff.active' => 0, 'Staff.modified' => $date_modified), array('Staff.id' => $svalue));
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function updateEducationAndServiceWing($department_id = null)
    {
        if (!empty($department_id)) {
            $staffs = $this->find('all', array(
                'conditions' => array(
                    'Staff.department_id' => $department_id,
                    'Staff.active' => 1,
                    'Staff.education is null or Staff.servicewing is null'
                ),
                'contain' => array('Position', 'User' => array('Role')),
                'recursive' => -1
            ));
        } else {
            $staffs = $this->find('all', array(
                'conditions' => array(
                    'Staff.active' => 1,
                    'Staff.education is null or Staff.servicewing is null'
                ),
                'contain' => array('Position', 'User' => array('Role')),
                'recursive' => -1
            ));
        }

        if (!empty($staffs)) {
            foreach ($staffs as $skey => $svalue) {
                $updateStaffs['Staff']['id'] = $svalue['Staff']['id'];
                if ($svalue['User']['role_id'] == ROLE_INSTRUCTOR) {
                    $updateStaffs['Staff']['servicewing'] = 'Academician';
                    $updateStaffs['Staff']['service_wing_id'] = SERVICE_WING_ACADEMICIAN;
                } else if ($svalue['User']['role_id'] == ROLE_REGISTRAR) {
                    $updateStaffs['Staff']['servicewing'] = 'Registrar';
                    $updateStaffs['Staff']['service_wing_id'] = SERVICE_WING_REGISTRAR;
                } else if ($svalue['User']['role_id'] == ROLE_SYSADMIN) {
                    $updateStaffs['Staff']['servicewing'] = 'Technical Support';
                    $updateStaffs['Staff']['service_wing_id'] = SERVICE_WING_TECHNICAL_SUPPORT;
                } else {
                    $updateStaffs['Staff']['servicewing'] = 'Technical Support';
                    $updateStaffs['Staff']['service_wing_id'] = SERVICE_WING_TECHNICAL_SUPPORT;
                }

                if ($svalue['Staff']['position_id'] == 1 || $svalue['Staff']['position_id'] == 2 || $svalue['Staff']['position_id'] == 3 ) {
                    $updateStaffs['Staff']['education'] = 'Degree';
                    $updateStaffs['Staff']['education_id'] = EDUCATION_DEGREE;
                } else if ($svalue['Staff']['position_id'] == 11 || $svalue['Staff']['position_id'] == 12 || $svalue['Staff']['position_id'] == 13) {
                    $updateStaffs['Staff']['education'] = 'Diploma';
                    $updateStaffs['Staff']['education_id'] = EDUCATION_DIPLOMA;
                } else if ($svalue['Staff']['position_id'] == 5 || $svalue['Staff']['position_id'] == 6 || $svalue['Staff']['position_id'] == 7 ) {
                    $updateStaffs['Staff']['education'] = 'Doctorate';
                    $updateStaffs['Staff']['education_id'] = EDUCATION_DOCTRATE;
                } else if ($svalue['Staff']['position_id'] == 4) {
                    $updateStaffs['Staff']['education'] = 'Master';
                    $updateStaffs['Staff']['education_id'] = EDUCATION_MASTERS;
                } else {
                    $updateStaffs['Staff']['education'] = 'Diploma';
                    $updateStaffs['Staff']['education_id'] = EDUCATION_DIPLOMA;
                }

                if ($this->save($updateStaffs, array('validate' => false))) {
                    debug($updateStaffs);
                } else {
                    debug($this->invalidFields());
                }
            }
        }
    }

    public function updateGender($department_id = null)
    {
        if (!empty($department_id)) {
            $staffs = $this->find('all', array(
                'conditions' => array(
                    'Staff.department_id' => $department_id,
                    'Staff.active' => 1,
                    'Staff.gender is null '
                ),
                'contain' => array('Position', 'User' => array('Role')),
                'recursive' => -1
            ));
        } else {
            $staffs = $this->find('all', array(
                'conditions' => array(
                    'Staff.active' => 1,
                    'Staff.gender is null '
                ),
                'contain' => array('Position', 'User' => array('Role')),
                'recursive' => -1
            ));
        }

        if (!empty($staffs)) {
            foreach ($staffs as $skey => $svalue) {
                $updateStaffs['Staff']['id'] = $svalue['Staff']['id'];

                if ($svalue['Staff']['title_id'] == 2 || $svalue['Staff']['title_id'] == 4 || $svalue['Staff']['title_id'] == 6 || $svalue['Staff']['title_id'] == 7) {
                    $updateStaffs['Staff']['gender'] = 'female';
                } else {
                    $updateStaffs['Staff']['gender'] = 'male';
                }

                if ($this->save($updateStaffs, array('validate' => false))) {
                    debug($updateStaffs);
                } else {
                    debug($this->invalidFields());
                }
            }
        }
    }

    public function updateStaffId($department_id = null)
    {
        if (!empty($department_id)) {
            $staffs = $this->find('all', array(
                'conditions' => array(
                    'Staff.department_id' => $department_id,
                    'Staff.active' => 1,
                    'Staff.staffid is null '
                ),
                'contain' => array('Position', 'User' => array('Role')),
                'recursive' => -1
            ));
        } else {
            $staffs = $this->find('all', array(
                'conditions' => array(
                    'Staff.staffid is null ',
                    'Staff.active' => 1,
                ),
                'contain' => array('Position', 'User' => array('Role')),
                'recursive' => -1
            ));
        }

        $count = 1;
        if (!empty($staffs)) {
            foreach ($staffs as $skey => $svalue) {
                $updateStaffs['Staff']['id'] = $svalue['Staff']['id'];
                $updateStaffs['Staff']['staffid'] = 'AMU/' . str_pad($count, 4, "0", STR_PAD_LEFT) . '/2009';

                if ($this->save($updateStaffs, array('validate' => false))) {
                    debug($updateStaffs);
                } else {
                    debug($this->invalidFields());
                }
                $count++;
            }
        }

    }

    public function updateCountry($department_id = null)
    {
        if (!empty($department_id)) {
            $staffs = $this->find('all', array(
                'conditions' => array(
                    'Staff.department_id' => $department_id,
                    'Staff.active' => 1,
                    'Staff.country_id is null ',
                ),
                'contain' => array('Position', 'User' => array('Role')),
                'recursive' => -1
            ));
        } else {
            $staffs = $this->find('all', array(
                'conditions' => array(
                    'Staff.active' => 1,
                    'Staff.country_id is null ',
                ),
                'contain' => array('Position', 'User' => array('Role')),
                'recursive' => -1
            ));
        }

        $indians = array(2845, 2833, 2825, 2817, 2801, 2597, 2417, 2412, 2401, 2348, 2347, 2269, 2232, 2229, 2190, 2185, 2186, 2149, 2132, 2126, 2114, 2109, 2106, 2096, 2083, 2059, 2012, 59, 1852, 1597, 2109, 1342, 1861, 70, 1608, 2379, 1356, 2384, 606, 1118, 2401, 1899, 2417, 2179, 2193, 1426, 1939, 2195, 1172, 920, 731, 492, 1027, 266, 1045, 1557, 1051, 305, 1843, 837, 77, 1110, 602, 1664, 1157, 647, 1677, 1194, 187, 959, 1480, 1485, 1744, 217, 2012, 733, 742, 1766, 761, 1017, 1097);

        if (!empty($staffs)) {
            foreach ($staffs as $skey => $svalue) {
                $updateStaffs['Staff']['id'] = $svalue['Staff']['id'];
                $updateStaffs['Staff']['country_id'] = COUNTRY_ID_OF_ETHIOPIA;
                if ($this->save($updateStaffs, array('validate' => false))) {
                    debug($updateStaffs);
                } else {
                    debug($this->invalidFields());
                }
            }
        }

        if (!empty($staffs) && !empty($indians)) {
            foreach ($indians as $skey => $svalue) {
                $updateStaffs['Staff']['id'] = $svalue;
                $updateStaffs['Staff']['country_id'] = 99;
                if ($this->save($updateStaffs, array('validate' => false))) {
                    debug($updateStaffs);
                } else {
                    debug($this->invalidFields());
                }
            }
        }
    }

    public function getActiveTeacherByDegree($department_id = null, $sex = 'all')
    {
        $graph['data'] = array();
        $graph['labels'] = array();

        if (isset($department_id) && !empty($department_id)) {
            $college_id = explode('~', $department_id);
            if (count($college_id) > 1) {
                $departments = $this->Department->find('all', array(
                    'conditions' => array(
                        'Department.college_id' => $college_id[1],
                        'Department.active' => 1
                    ),
                    'contain' => array('College', 'YearLevel'),
                    'recursive' => -1
                ));
            } else {
                $departments = $this->Department->find('all', array(
                    'conditions' => array(
                        'Department.id' => $department_id,
                        'Department.active' => 1
                    ),
                    'contain' => array('College', 'YearLevel'),
                    'recursive' => -1
                ));
            }
        } else {
            $departments = $this->Department->find('all', array(
                'conditions' => array(
                    'Department.active' => 1
                ),
                'contain' => array('College', 'YearLevel'),
                'recursive' => -1
            ));
        }

        if ($sex == "all") {
            $sexList = array('male' => 'male', 'female' => 'female');
        } else {
            $sexList[$sex] = $sex;
        }

        $teachersStatisticsByDegree = array();

        $graph['series'] = array(
            'Doctorate',
            'Master',
            'Medical Doctor',
            'Degree'
        );

        $educations = array(
            'Doctorate' => 'PhD',
            'Master' => 'Master',
            'Medical Doctor' => 'Medical Doctorate',
            'Degree' => 'Degree'
        );

        $collegeDepartmentYearCount = array();

        if (!empty($departments)) {
            foreach ($departments as $key => $value) {
                foreach ($sexList as $skey => $svalue) {
                    $collegeDepartmentYearCount[$value['College']['name']] += 1;
                    foreach ($educations as $ckey => $cvalue) {

                        $teachersStatisticsByDegree[$value['College']['name']][$value['Department']['name']][$skey][$ckey]['Ethiopian'] = $this->find('count', array(
                            'conditions' => array(
                                'Staff.user_id  IN (SELECT id FROM users WHERE role_id=' . ROLE_INSTRUCTOR . ' )',
                                'Staff.gender' => $skey,
                                'Staff.education' => $ckey,
                                'Staff.country_id' => 68,
                                'Staff.department_id' => $value['Department']['id'],
                                'Staff.active' => 1,
                            )
                        ));

                        $teachersStatisticsByDegree[$value['College']['name']][$value['Department']['name']][$skey][$ckey]['Foreigner'] = $this->find('count', array(
                            'conditions' => array(
                                'Staff.user_id IN (SELECT id FROM users WHERE role_id=' . ROLE_INSTRUCTOR . ' )',
                                'Staff.gender' => $skey,
                                'Staff.education' => $ckey,
                                'Staff.country_id !=' => COUNTRY_ID_OF_ETHIOPIA,
                                'Staff.department_id' => $value['Department']['id'],
                                'Staff.active' => 1,
                            )
                        ));

                        $graph['labels'][$value['Department']['id']] = $value['Department']['name'];

                        if (strtolower($skey) == "female") {
                            $indexS = 1;
                        } else if (strtolower($skey) == "male") {
                            $indexS = 0;
                        }

                        $graph['data'][$indexS][$value['Department']['id']] += $teachersStatisticsByDegree[$value['College']['name']][$value['Department']['name']][$skey][$ckey]['Ethiopian'];

                        $graph['data'][$indexS][$value['Department']['id']] += $teachersStatisticsByDegree[$value['College']['name']][$value['Department']['name']][$skey][$ckey]['Foreigner'];

                    }
                }
                $collegeDepartmentYearCount[$value['College']['name']] += 1;
            }
        }

        $distribution['teachersStatisticsByDegree'] = $teachersStatisticsByDegree;
        $distribution['collegeRowSpan'] = $collegeDepartmentYearCount;
        $distribution['graph'] = $graph;

        return $distribution;
    }

    public function getActiveTeacherByAcademicRank($department_id = null, $sex = 'all')
    {

        $graph['data'] = array();
        $graph['labels'] = array();

        if (isset($department_id) && !empty($department_id)) {
            $college_id = explode('~', $department_id);
            if (count($college_id) > 1) {
                $departments = $this->Department->find('all', array(
                    'conditions' => array(
                        'Department.college_id' => $college_id[1],
                        'Department.active' => 1
                    ),
                    'contain' => array('College', 'YearLevel'),
                    'recursive' => -1
                ));
            } else {
                $departments = $this->Department->find('all', array(
                    'conditions' => array(
                        'Department.id' => $department_id,
                        'Department.active' => 1
                    ),
                    'contain' => array('College', 'YearLevel'),
                    'recursive' => -1
                ));
            }
        } else {
            $departments = $this->Department->find('all', array(
                'conditions' => array(
                    'Department.active' => 1
                ),
                'contain' => array('College', 'YearLevel'),
                'recursive' => -1
            ));
        }

        if ($sex == "all") {
            $sexList = array('male' => 'male', 'female' => 'female');
        } else {
            $sexList[$sex] = $sex;
        }

        $teachersStatisticsByAcademicRank = array();

        $graph['series'] = array(
            'Doctorate',
            'Master',
            'Medical Doctor',
            'Degree'
        );

        $educations = array(
            'Doctorate' => 'PhD',
            'Master' => 'Master',
            'Medical Doctor' => 'Medical Doctorate',
            'Degree' => 'Degree'
        );

        $educations = ClassRegistry::init('Education')->find('list', array('conditions' => array('Education.use_in_reports' => 1, 'Education.active' => 1), 'fields' => array('id', 'shortname')));

        //$positions = array('4' => 'Lecturer', '5' => 'Assistant Professor', '6' => 'Associate Professor', '7' => 'Professor');
        $positions = ClassRegistry::init('Position')->find('list', array('conditions' => array('Position.service_wing_id' => 1, 'Position.active' => 1), 'fields' => array('id', 'position')));

        $collegeDepartmentYearCount = array();

        if (!empty($departments)) {
            foreach ($departments as $key => $value) {
                foreach ($positions as $pkey => $pvalue) {
                    $collegeDepartmentYearCount[$value['College']['name']] += 1;
                    foreach ($educations as $ekey => $evalue) {
                        foreach ($sexList as $skey => $svalue) {
                            $teachersStatisticsByAcademicRank[$value['College']['name']][$value['Department']['name']][$evalue][$pvalue][$skey] = $this->find('count', array(
                                'conditions' => array(
                                    'Staff.user_id  IN (SELECT id FROM users WHERE role_id=' . ROLE_INSTRUCTOR . ' )',
                                    'Staff.gender' => $skey,
                                    'OR' => array(
                                        'Staff.education' => $evalue,
                                        'Staff.education_id' => $ekey,
                                    ),
                                    //'Staff.education' => $ekey,
                                    'Staff.position_id' => $pkey,
                                    'Staff.department_id' => $value['Department']['id'],
                                    'Staff.active' => 1,
                                )
                            ));
                        }
                    }
                }

            }
        }

        $distribution['teachersStatisticsByAcademicRank'] = $teachersStatisticsByAcademicRank;
        $distribution['collegeRowSpan'] = $collegeDepartmentYearCount;
        //$distribution['graph'] = $graph;

        return $distribution;

    }

    public function getTeachersOnStudyLeave($department_id = null, $sex = 'all')
    {

        $graph['data'] = array();
        $graph['labels'] = array();

        if (isset($department_id) && !empty($department_id)) {
            $college_id = explode('~', $department_id);
            if (count($college_id) > 1) {
                $departments = $this->Department->find('all', array(
                    'conditions' => array(
                        'Department.college_id' => $college_id[1],
                        'Department.active' => 1
                    ),
                    'contain' => array('College', 'YearLevel'),
                    'recursive' => -1
                ));
            } else {
                $departments = $this->Department->find('all', array(
                    'conditions' => array(
                        'Department.id' => $department_id,
                        'Department.active' => 1
                    ),
                    'contain' => array('College', 'YearLevel'),
                    'recursive' => -1
                ));
            }
        } else {
            $departments = $this->Department->find('all', array(
                'conditions' => array(
                    'Department.active' => 1
                ),
                'contain' => array('College', 'YearLevel'),
                'recursive' => -1
            ));
        }

        if ($sex == "all") {
            $sexList = array('male' => 'male', 'female' => 'female');
        } else {
            $sexList[$sex] = $sex;
        }

        $teachersStatisticsByDegree = array();
        $graph['series'] = array(
            'Doctorate',
            'Master',
            'Medical Doctor',
            'Degree'
        );
        $educations = array(
            'Doctorate' => 'PhD',
            'Master' => 'Master',
            'Medical Doctor' => 'Medical Doctorate',
            'Degree' => 'Degree'
        );

        $collegeDepartmentYearCount = array();

        if (!empty($departments)) {
            foreach ($departments as $key => $value) {
                foreach ($sexList as $skey => $svalue) {
                    $collegeDepartmentYearCount[$value['College']['name']] += 1;
                    foreach ($educations as $ckey => $cvalue) {
                        $insideEthiopiaStudies = $this->find('count', array(
                                'conditions' => array(
                                    'Staff.user_id IN (SELECT id FROM users WHERE role_id=' . ROLE_INSTRUCTOR . ' )',
                                    'Staff.gender' => $skey,
                                    'Staff.education' => $ckey,
                                    'Staff.department_id' => $value['Department']['id'],
                                    'Staff.id in (select staff_id from staff_studies where return_date > "' . date('Y-m-d') . '" and country_id=68 )'
                                )
                            )
                        );

                        $outSideEthiopiaStudies = $this->find('count', array(
                            'conditions' => array(
                                'Staff.user_id IN (SELECT id FROM users WHERE role_id=' . ROLE_INSTRUCTOR . ' )',
                                'Staff.gender' => $skey,
                                'Staff.education' => $ckey,
                                'Staff.department_id' => $value['Department']['id'],
                                'Staff.id in (select staff_id from staff_studies where return_date > "' . date('Y-m-d') . '" and country_id!=68 )'
                            )
                        ));

                        $teachersStatisticsByDegree[$value['College']['name']][$value['Department']['name']][$skey][$ckey]['In Ethiopia'] = $insideEthiopiaStudies;
                        $teachersStatisticsByDegree[$value['College']['name']][$value['Department']['name']][$skey][$ckey]['Outside Ethiopia'] = $outSideEthiopiaStudies;

                        $graph['labels'][$value['Department']['id']] = $value['Department']['name'];

                        if (strtolower($skey) == "female") {
                            $indexS = 1;
                        } else if (strtolower($skey) == "male") {
                            $indexS = 0;
                        }

                        $graph['data'][$indexS][$value['Department']['id']] += $insideEthiopiaStudies;
                        $graph['data'][$indexS][$value['Department']['id']] += $outSideEthiopiaStudies;

                    }
                }
                $collegeDepartmentYearCount[$value['College']['name']] += 1;
            }
        }

        $distribution['teachersOnStudyLeave'] = $teachersStatisticsByDegree;
        $distribution['collegeRowSpan'] = $collegeDepartmentYearCount;
        $distribution['graph'] = $graph;

        return $distribution;

    }

    public function getSpecialNeeds($department_id = null, $sex = 'all')
    {

        $graph['data'] = array();
        $graph['labels'] = array();

        if (isset($department_id) && !empty($department_id)) {
            $college_id = explode('~', $department_id);
            if (count($college_id) > 1) {
                $departments = $this->Department->find('all', array(
                    'conditions' => array(
                        'Department.college_id' => $college_id[1],
                        'Department.active' => 1
                    ),
                    'contain' => array('College', 'YearLevel'),
                    'recursive' => -1
                ));
            } else {
                $departments = $this->Department->find('all', array(
                    'conditions' => array(
                        'Department.id' => $department_id,
                        'Department.active' => 1
                    ),
                    'contain' => array('College', 'YearLevel'),
                    'recursive' => -1
                ));
            }
        } else {
            $departments = $this->Department->find('all', array(
                'conditions' => array(
                    'Department.active' => 1
                ),
                'contain' => array('College', 'YearLevel'),
                'recursive' => -1
            ));
        }

        if ($sex == "all") {
            $sexList = array('male' => 'male', 'female' => 'female');
        } else {
            $sexList[$sex] = $sex;
        }

        $studentsSpecialNeeds = array();
        $graph['series'] = array(
            'Doctorate',
            'Master',
            'Medical Doctor',
            'Degree'
        );


        foreach ($departments as $key => $value) {
            foreach ($sexList as $skey => $svalue) {

            }
        }

        //$distribution['teachersStatisticsByDegree'] = $teachersStatisticsByDegree;
        //$distribution['collegeRowSpan'] = $collegeDepartmentYearCount;
        $distribution['graph'] = $graph;

        return $distribution;

    }
    public function remove_duplicate_staff($department_id = null)
    {

        if (isset($department_id) && !empty($department_id)) {
            $sqlDuplicate = "SELECT y.id, y.`first_name` , y.`middle_name` , y.`last_name` , y.`position_id` , y.`department_id` , y.`user_id` FROM staffs AS y
			INNER JOIN (
				SELECT `first_name` , `middle_name` , `last_name` , `position_id` , `department_id` , `user_id` , count( * ) FROM `staffs` WHERE `user_id` IN ( SELECT id FROM users WHERE role_id = 2) AND department_id = $department_id
				GROUP BY `first_name` , `middle_name` , `last_name`, `department_id` HAVING count(*) > 1
			)dt ON y.first_name = dt.first_name AND y.middle_name = dt.middle_name AND y.last_name = dt.last_name AND y.department_id = dt.department_id ORDER BY `y`.`first_name` ASC ";
        } else {
            $sqlDuplicate = "SELECT y.id, y.`first_name` , y.`middle_name` , y.`last_name` , y.`position_id` , y.`department_id` , y.`user_id` FROM staffs AS y
			INNER JOIN (
				SELECT `first_name` , `middle_name` , `last_name` , `position_id` , `department_id` , `user_id` , count( * )
				FROM `staffs` WHERE `user_id` IN (SELECT id FROM users WHERE role_id = 2)
				GROUP BY `first_name`, `middle_name`, `last_name`, `department_id` HAVING count(*) > 1
			) dt ON y.first_name = dt.first_name AND y.middle_name = dt.middle_name AND y.last_name = dt.last_name AND y.department_id = dt.department_id ORDER BY `y`.`first_name` ASC ";
        }

        $result = $this->query($sqlDuplicate);
        $staff_duplicated = array();

        if (!empty($result)) {
            foreach ($result as $k => $vv) {
                $staff_duplicated[strtolower(trim($vv['y']['first_name'])) . '~' . strtolower(trim($vv['y']['middle_name'])) . '~' . strtolower(trim($vv['y']['last_name'])) . '~' . $vv['y']['department_id']][$vv['y']['id']] = $vv['y']['user_id'];
            }
        }

        if (!empty($staff_duplicated)) {
            foreach ($staff_duplicated as $s => $sd) {
                //duplicated staffs iterating

                $allinstructorAssignments = $this->CourseInstructorAssignment->find('all', array(
                    'conditions' => array('CourseInstructorAssignment.staff_id' => array_keys($sd)),
                    'order' => array('CourseInstructorAssignment.created DESC'),
                    'recursive' => -1
                ));

                $mostRecentAssignment = $this->CourseInstructorAssignment->find('first', array(
                    'conditions' => array(
                        'CourseInstructorAssignment.staff_id' => array_keys($sd)
                    ),
                    'order' => array('CourseInstructorAssignment.created DESC'),
                    'recursive' => -1
                ));

                //update assignment table with the account most actives

                foreach ($allinstructorAssignments as $assk => $asssid) {

                    $this->CourseInstructorAssignment->id = $asssid['CourseInstructorAssignment']['id'];
                    $this->CourseInstructorAssignment->saveField('staff_id', $mostRecentAssignment['CourseInstructorAssignment']['staff_id']);

                    /* if ($mostRecentAssignment['CourseInstructorAssignment']['staff_id'] != $asssid['CourseInstructorAssignment']['staff_id']) {
                        //remove staff and user
                        if (isset($asssid['CourseInstructorAssignment']['staff_id']) && !empty($asssid['CourseInstructorAssignment']['staff_id'])) {
                            $this->delete($asssid['CourseInstructorAssignment']['staff_id']);
                        }

                        $suid = $sd[$asssid['CourseInstructorAssignment']['staff_id']];

                        if (isset($suid) && !empty($suid)) {
                            $this->User->delete($suid);
                        }
                    } else {
                        //not deleted staff
                    } */
                }
            }


            foreach ($staff_duplicated as $s => $sd) {
                foreach ($sd as $sid => $suid) {
                    // check if instructor assignment is done
                    $assigned = $this->CourseInstructorAssignment->find('count', array('conditions' => array('CourseInstructorAssignment.staff_id' => $sid)));
                    if ($assigned == 0) {
                        //remove staff and user
                        $this->delete($sid);
                        if (isset($suid) && !empty($suid)) {
                            $this->User->delete($suid);
                        }
                    }
                }
            }
        }
    }
}
