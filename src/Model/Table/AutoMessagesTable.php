<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\TableRegistry;
use Cake\Utility\Text;
use Cake\Event\EventInterface;



class AutoMessagesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('auto_messages');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp'); // Ensure created/modified fields are managed

        // Associations
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
        ]);
    }

    public function beforeSave(EventInterface $event, $entity, $options)
    {
        if ($entity->isNew() && (empty($entity->id) || $entity->id === '')) {
            $entity->set('id', Text::uuid());
        }
    }


    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('message')
            ->requirePresence('message', 'create')
            ->notEmptyString('message', 'Message cannot be empty');

        return $validator;
    }

    public function getMessages($user_id = null)
    {
        return $this->find()
            ->where([
                'AutoMessages.read' => 0,
                'AutoMessages.user_id' => $user_id
            ])
            ->order(['AutoMessages.created' => 'DESC'])
            ->limit(AUTO_MESSAGE_LIMIT)
            ->all()
            ->toArray();
    }

    public function sendMessage($user_id = null, $message = null, $type = null)
    {
        $autoMessage = $this->newEntity([
            'message' => $message,
            'read' => 0,
            'user_id' => $user_id
        ]);

        if ($type === 1) {
            $autoMessage->message = '<p style="text-align:justify; padding:0px; margin:0px" class="accepted">' . $autoMessage->message . '</p>';
        } elseif ($type === -1) {
            $autoMessage->message = '<p style="text-align:justify; padding:0px; margin:0px" class="rejected">' . $autoMessage->message . '</p>';
        } elseif ($type === 0) {
            $autoMessage->message = '<p style="text-align:justify; padding:0px; margin:0px" class="on-process">' . $autoMessage->message . '</p>';
        }

        $this->save($autoMessage);
    }

    public function alumniRegistrationMessage($message, $type = null)
    {
        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $usersLists = $usersTable->find('list', [
            'conditions' => ['Users.role_id' => 13],
            'keyField' => 'id',
            'valueField' => 'id'
        ])->toArray();

        if (!empty($usersLists)) {
            $autoMessages = [];
            foreach ($usersLists as $usr) {
                $msg = $message;
                if ($type === 1) {
                    $msg = '<p style="text-align:justify; padding:0px; margin:0px" class="accepted">' . $msg . '</p>';
                } elseif ($type === -1) {
                    $msg = '<p style="text-align:justify; padding:0px; margin:0px" class="rejected">' . $msg . '</p>';
                } elseif ($type === 0) {
                    $msg = '<p style="text-align:justify; padding:0px; margin:0px" class="on-process">' . $msg . '</p>';
                }

                $autoMessages[] = $this->newEntity([
                    'message' => $msg,
                    'read' => 0,
                    'user_id' => $usr
                ]);
            }
            $this->saveMany($autoMessages);
        }
    }

    public function postMessageToGroup($role_id = null, $subject = null, $message = null, $data = null)
    {
        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $message = '<h7>' . $subject . '</h7><p style="text-align:justify; padding:0px; margin:0px" class="accepted">' . nl2br(htmlentities($message)) . '</p>';

        $usersMessages = $usersTable->getListOfUsersRole($role_id, $message, $data);

        if (!empty($usersMessages['AutoMessages'])) {
            $entities = $this->newEntities($usersMessages['AutoMessages']);
            return $this->saveMany($entities);
        }

        return false;
    }

    public function sendNotificationOnRegistrarGradeConfirmation($confirmed_grades = null)
    {
        $autoMessages = [];

        if (!empty($confirmed_grades) && $confirmed_grades[0]['registrar_approval'] == 1) {
            $grade_ids = array_column($confirmed_grades, 'id');

            $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');
            $grade_details = $examGradesTable->find()
                ->where(['ExamGrades.id IN' => $grade_ids])
                ->contain([
                    'CourseAdds' => [
                        'PublishedCourses' => [
                            'fields' => ['id', 'course_id'],
                            'Courses' => ['id', 'course_code_title']
                        ],
                        'Students' => ['id', 'user_id']
                    ],
                    'CourseRegistrations' => [
                        'PublishedCourses' => [
                            'fields' => ['id', 'course_id'],
                            'Courses' => ['id', 'course_code_title']
                        ],
                        'Students' => ['id', 'user_id']
                    ]
                ])
                ->all()
                ->toArray();

            // Student notification
            if (!empty($grade_details) && defined('ALLOW_AUTO_MASSEGES_TO_BE_SENT_FOR_STUDENTS') && ALLOW_AUTO_MASSEGES_TO_BE_SENT_FOR_STUDENTS == 1) {
                foreach ($grade_details as $grade_detail) {
                    if (!empty($grade_detail->course_registration_id) && $grade_detail->course_registration_id > 0 && isset($grade_detail->course_registration->student->user_id)) {
                        $autoMessages[] = $this->newEntity([
                            'message' => 'You got <strong>' . $grade_detail->grade . '</strong> for the course <u>' . $grade_detail->course_registration->published_course->course->course_code_title . '</u>.',
                            'read' => 0,
                            'user_id' => $grade_detail->course_registration->student->user_id
                        ]);
                    } elseif (!empty($grade_detail->course_add_id) && $grade_detail->course_add_id > 0 && isset($grade_detail->course_add->student->user_id)) {
                        $autoMessages[] = $this->newEntity([
                            'message' => 'You got <strong>' . $grade_detail->grade . '</strong> for the course <u>' . $grade_detail->course_add->published_course->course->course_code_title . '</u>.',
                            'read' => 0,
                            'user_id' => $grade_detail->course_add->student->user_id
                        ]);
                    }
                }
            }

            // Instructor notification
            $publishedCoursesTable = TableRegistry::getTableLocator()->get('PublishedCourses');
            $coursesTable = TableRegistry::getTableLocator()->get('Courses');
            $sectionsTable = TableRegistry::getTableLocator()->get('Sections');

            $course_instructor = $publishedCoursesTable->getInstructorByExamGradeId($confirmed_grades[0]['id']);
            $course = $coursesTable->getCourseByExamGradeId($confirmed_grades[0]['id']);
            $section = $sectionsTable->getSectionByExamGradeId($confirmed_grades[0]['id']);
            $published_course = $publishedCoursesTable->getPublishedCourseByExamGradeId($confirmed_grades[0]['id']);

            if (!empty($course_instructor) && !empty($course_instructor['user_id'])) {
                $message = 'Your <u>' . $course['course_code_title'] . '</u> grade submission is ' . ($confirmed_grades[0]['registrar_approval'] == 1 ? 'accepted' : 'rejected') . ' by the registrar for <u>' . $section['name'] . '</u> section. <a href="/examResults/add/' . $published_course['id'] . '">View Grade</a>';

                if ($confirmed_grades[0]['registrar_approval'] == -1) {
                    $message = '<p style="text-align:justify; padding:0px; margin:0px" class="rejected">' . $message . '</p>';
                } elseif ($confirmed_grades[0]['registrar_approval'] == 1) {
                    $message = '<p style="text-align:justify; padding:0px; margin:0px" class="accepted">' . $message . '</p>';
                }

                $autoMessages[] = $this->newEntity([
                    'message' => $message,
                    'read' => 0,
                    'user_id' => $course_instructor['user_id']
                ]);
            }

            // Department notification
            $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');
            $department_approved_bys = $examGradesTable->find('list', [
                'conditions' => ['ExamGrades.id IN' => $grade_ids],
                'keyField' => 'id',
                'valueField' => 'department_approved_by'
            ])->toArray();

            $department_approved_bys = array_unique($department_approved_bys);

            if (!empty($department_approved_bys)) {
                foreach ($department_approved_bys as $department_approved_by) {
                    if (!empty($department_approved_by)) {
                        $approvalpage = isset($published_course['department_id']) && !empty($published_course['department_id']) ?
                            '/examGrades/approve_non_freshman_grade_submission' :
                            '/examGrades/approve_freshman_grade_submission';

                        $message = '<u>' . $course['course_code_title'] . '</u> course grade is ' . ($confirmed_grades[0]['registrar_approval'] == 1 ? 'accepted' : 'rejected') . ' by the registrar for <u>' . $section['name'] . '</u> section. <a href="' . $approvalpage . '/' . $published_course['id'] . '">View Grade</a>';

                        if ($confirmed_grades[0]['registrar_approval'] == -1) {
                            $message = '<p style="text-align:justify; padding:0px; margin:0px" class="rejected">' . $message . '</p>';
                        } elseif ($confirmed_grades[0]['registrar_approval'] == 1) {
                            $message = '<p style="text-align:justify; padding:0px; margin:0px" class="accepted">' . $message . '</p>';
                        }

                        $autoMessages[] = $this->newEntity([
                            'message' => $message,
                            'read' => 0,
                            'user_id' => $department_approved_by
                        ]);
                    }
                }
            }
        }

        if (!empty($autoMessages)) {
            $this->saveMany($autoMessages);
        }
    }

    public function sendNotificationOnRegistrarGradeRollback($rolledback_grades = null, $rolled_back_by = '', $rolled_back_by_id = '', $department_approved_bys = [], $pc_id = '')
    {
        $autoMessages = [];

        if (!empty($rolledback_grades) && !empty($pc_id) && $pc_id > 0) {
            $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');
            $grade_details = $examGradesTable->find()
                ->where(['ExamGrades.id IN' => $rolledback_grades])
                ->contain([
                    'CourseAdds' => [
                        'conditions' => ['CourseAdds.published_course_id' => $pc_id],
                        'PublishedCourses' => [
                            'fields' => ['id', 'course_id'],
                            'Courses' => ['id', 'course_code_title']
                        ],
                        'Students' => ['id', 'user_id']
                    ],
                    'CourseRegistrations' => [
                        'conditions' => ['CourseRegistrations.published_course_id' => $pc_id],
                        'PublishedCourses' => [
                            'fields' => ['id', 'course_id'],
                            'Courses' => ['id', 'course_code_title']
                        ],
                        'Students' => ['id', 'user_id']
                    ]
                ])
                ->all()
                ->toArray();

            $publishedCoursesTable = TableRegistry::getTableLocator()->get('PublishedCourses');
            $published_course = $publishedCoursesTable->find()
                ->where(['PublishedCourses.id' => $pc_id])
                ->contain([
                    'Courses',
                    'CourseInstructorAssignments' => [
                        'conditions' => [
                            'OR' => [
                                'CourseInstructorAssignments.type LIKE' => '%Lecture%',
                                'CourseInstructorAssignments.isprimary' => 1
                            ]
                        ],
                        'Staffs' => ['Titles'],
                        'limit' => 1
                    ],
                    'Sections' => ['YearLevels']
                ])
                ->first();

            // Student notification
            if (!empty($grade_details) && defined('ALLOW_AUTO_MASSEGES_TO_BE_SENT_FOR_STUDENTS') && ALLOW_AUTO_MASSEGES_TO_BE_SENT_FOR_STUDENTS == 1) {
                foreach ($grade_details as $grade_detail) {
                    if (!empty($grade_detail->course_registration_id) && $grade_detail->course_registration_id > 0 && isset($grade_detail->course_registration->student->user_id)) {
                        $autoMessages[] = $this->newEntity([
                            'message' => 'Exam grade you got <strong>' . $grade_detail->grade . '</strong> for the course <u>' . $grade_detail->course_registration->published_course->course->course_code_title . '</u> is rolled back for resubmission by the registrar.',
                            'read' => 0,
                            'user_id' => $grade_detail->course_registration->student->user_id
                        ]);
                    } elseif (!empty($grade_detail->course_add_id) && $grade_detail->course_add_id > 0 && isset($grade_detail->course_add->student->user_id)) {
                        $autoMessages[] = $this->newEntity([
                            'message' => 'Exam grade you got <strong>' . $grade_detail->grade . '</strong> for the course <u>' . $grade_detail->course_add->published_course->course->course_code_title . '</u> is rolled back for resubmission by the registrar.',
                            'read' => 0,
                            'user_id' => $grade_detail->course_add->student->user_id
                        ]);
                    }
                }
            }

            // Instructor notification
            $instructor_full_name = '';
            if (!empty($published_course->course_instructor_assignments[0]->staff->user_id)) {
                $instructor_full_name = !empty($published_course->course_instructor_assignments[0]->staff->full_name) ?
                    ($published_course->course_instructor_assignments[0]->staff->title->title ?? '') . ' ' . $published_course->course_instructor_assignments[0]->staff->full_name :
                    '';
            }

            $course_title_course_code = !empty($published_course->course->course_code_title) ? $published_course->course->course_code_title : '';
            $section_detail = !empty($published_course->section->id) ?
                trim(str_replace('  ', ' ', $published_course->section->name)) . '(' .
                (!empty($published_course->section->year_level->name) ? $published_course->section->year_level->name :
                    ($published_course->section->program_id == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st')) . ', ' .
                $published_course->section->academicyear . ')' : '';

            $rolledbackGrades_count = count($grade_details);

            if (!empty($published_course->course_instructor_assignments[0]->staff->user_id)) {
                $autoMessages[] = $this->newEntity([
                    'message' => 'Grade you submitted for <u>' . $course_title_course_code . '</u> for <u>' . $section_detail . '</u> section is rolled back for ' . ($rolledbackGrades_count . ' ' . ($rolledbackGrades_count == 1 ? 'student' : 'students')) . ' by ' . (!empty($rolled_back_by) ? $rolled_back_by : 'the registrar') . ' for your resubmission. Please note that you need to cancel the submitted grades in order to adjust results and submit again. <a href="/examResults/add/' . $published_course->id . '">Resubmit Grade</a>',
                    'read' => 0,
                    'user_id' => $published_course->course_instructor_assignments[0]->staff->user_id
                ]);
            }

            // Department notification
            if (!empty($department_approved_bys)) {
                foreach ($department_approved_bys as $department_approved_by) {
                    if (!empty($department_approved_by)) {
                        $approvalpage = '/examGrades/approve_non_freshman_grade_submission';
                        $autoMessages[] = $this->newEntity([
                            'message' => '<u>' . $course_title_course_code . '</u> course grade submitted ' . (!empty($instructor_full_name) ? 'by ' . $instructor_full_name : '') . ' for <u>' . $section_detail . '</u> section is rolled back for ' . ($rolledbackGrades_count . ' ' . ($rolledbackGrades_count == 1 ? 'student' : 'students')) . ' by ' . (!empty($rolled_back_by) ? $rolled_back_by : 'the registrar') . '. Please check with the instructor before approving the grade again. <a href="' . $approvalpage . '/' . $published_course->id . '">View Grade</a>',
                            'read' => 0,
                            'user_id' => $department_approved_by
                        ]);
                    }
                }
            }

            // Registrar notification
            if (!empty($rolled_back_by_id)) {
                $approvalpage = 'confirm_grade_submission';
                $autoMessages[] = $this->newEntity([
                    'message' => 'You rolled back ' . ($rolledbackGrades_count . ' ' . ($rolledbackGrades_count == 1 ? 'student' : 'students')) . ' submitted grade ' . (!empty($instructor_full_name) ? 'by ' . $instructor_full_name : '') . ' for the course <u>' . $course_title_course_code . '</u> from <u>' . $section_detail . '</u> section for grade resubmission. <a href="/examGrades/' . $approvalpage . '/' . $published_course->id . '">View Grade</a>',
                    'read' => 0,
                    'user_id' => $rolled_back_by_id
                ]);
            }
        }

        if (!empty($autoMessages)) {
            $this->saveMany($autoMessages);
        }
    }

    public function sendNotificationOnInstructorAssignment($publishedCourseId = null)
    {
        $autoMessages = [];

        if (!empty($publishedCourseId)) {
            $courseInstructorAssignmentsTable = TableRegistry::getTableLocator()->get('CourseInstructorAssignments');
            $assignment_details = $courseInstructorAssignmentsTable->find()
                ->where(['CourseInstructorAssignments.published_course_id' => $publishedCourseId])
                ->contain([
                    'PublishedCourses' => [
                        'Departments' => ['fields' => ['id', 'name', 'type']],
                        'Colleges' => ['fields' => ['id', 'name']],
                        'GivenByDepartments' => ['fields' => ['id', 'name', 'type']],
                        'Programs',
                        'ProgramTypes',
                        'Courses'
                    ],
                    'Staffs' => ['Users', 'Titles'],
                    'Sections' => ['YearLevels']
                ])
                ->all()
                ->toArray();

            if (!empty($assignment_details)) {
                foreach ($assignment_details as $assignment_detail) {
                    if (!empty($assignment_detail->staff->user_id)) {
                        $message = empty($assignment_detail->section->year_level->name) ?
                            'You are assigned as ' . ($assignment_detail->isprimary ? 'primary' : 'secondary') . ' instructor for the course <u>' . $assignment_detail->published_course->course->course_code_title . '</u> published for ' . $assignment_detail->section->name . ' section.<br />
                            Section: ' . $assignment_detail->section->name . ' <br/>
                            Department: ' . $assignment_detail->published_course->college->name . ' <br/>
                            Year Level: ' . ($assignment_detail->published_course->program_id == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st') . '<br/>
                            Program: ' . $assignment_detail->published_course->program->name . ' <br/>
                            Program Type: ' . $assignment_detail->published_course->program_type->name . '<br/>
                            Academic Year: ' . $assignment_detail->published_course->academic_year . ' <br/>
                            Semester: ' . $assignment_detail->published_course->semester :
                            'You are assigned as ' . ($assignment_detail->isprimary ? 'primary' : 'secondary') . ' instructor for the course <u>' . $assignment_detail->published_course->course->course_code_title . '</u> published for ' . $assignment_detail->section->name . ' section.<br />
                            Section: ' . $assignment_detail->section->name . ' <br/>
                            Year Level: ' . $assignment_detail->section->year_level->name . '<br/>
                            Department: ' . $assignment_detail->published_course->department->name . ' <br/>
                            Program: ' . $assignment_detail->published_course->program->name . ' <br/>
                            Program Type: ' . $assignment_detail->published_course->program_type->name . '<br/>
                            Academic Year: ' . $assignment_detail->published_course->academic_year . ' <br/>
                            Semester: ' . $assignment_detail->published_course->semester;

                        $autoMessages[] = $this->newEntity([
                            'message' => $message,
                            'read' => 0,
                            'user_id' => $assignment_detail->staff->user_id
                        ]);
                    }

                    if (empty($assignment_detail->published_course->department_id)) {
                        $usersTable = TableRegistry::getTableLocator()->get('Users');
                        $ownerDepartmentUser = $usersTable->find()
                            ->where([
                                'Users.is_admin' => 1,
                                'Users.role_id' => ROLE_COLLEGE,
                                'Users.id IN' => $usersTable->subQuery()
                                    ->select(['user_id'])
                                    ->from('staffs')
                                    ->where(['college_id' => $assignment_detail->published_course->college_id])
                            ])
                            ->first();

                        $autoMessages[] = $this->newEntity([
                            'message' => $assignment_detail->published_course->given_by_department->type . ' of ' . $assignment_detail->published_course->given_by_department->name . ' assigned ' . $assignment_detail->staff->title->title . '. ' . $assignment_detail->staff->full_name . ' as ' . ($assignment_detail->isprimary ? 'primary' : 'secondary') . ' instructor to your dispatched course <u>' . $assignment_detail->published_course->course->course_code_title . '</u> published for ' . $assignment_detail->section->name . ' section. <br/>
                            Section: ' . $assignment_detail->section->name . '<br/>
                            Year Level: ' . ($assignment_detail->published_course->program_id == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st') . '<br/>
                            Department: ' . $assignment_detail->published_course->college->name . ' <br/>
                            Program: ' . $assignment_detail->published_course->program->name . ' <br/>
                            Program Type: ' . $assignment_detail->published_course->program_type->name . '<br/>
                            Academic Year: ' . $assignment_detail->published_course->academic_year . ' <br/>
                            Semester: ' . $assignment_detail->published_course->semester,
                            'read' => 0,
                            'user_id' => $ownerDepartmentUser->id
                        ]);
                    } elseif ($assignment_detail->published_course->department_id != $assignment_detail->published_course->given_by_department_id) {
                        $usersTable = TableRegistry::getTableLocator()->get('Users');
                        $ownerDepartmentUser = $usersTable->find()
                            ->where([
                                'Users.is_admin' => 1,
                                'Users.role_id' => ROLE_DEPARTMENT,
                                'Users.id IN' => $usersTable->subQuery()
                                    ->select(['user_id'])
                                    ->from('staffs')
                                    ->where(['department_id' => $assignment_detail->published_course->department_id])
                            ])
                            ->first();

                        $autoMessages[] = $this->newEntity([
                            'message' => $assignment_detail->published_course->given_by_department->type . ' of ' . $assignment_detail->published_course->given_by_department->name . ' assigned ' . $assignment_detail->staff->title->title . '. ' . $assignment_detail->staff->full_name . ' as ' . ($assignment_detail->isprimary ? 'primary' : 'secondary') . ' instructor to your dispatched course <u>' . $assignment_detail->published_course->course->course_code_title . '</u> published for ' . $assignment_detail->section->name . ' section. <br/>
                            Section: ' . $assignment_detail->section->name . '<br/>
                            Year Level: ' . $assignment_detail->section->year_level->name . '<br/>
                            Department: ' . $assignment_detail->published_course->department->name . ' <br/>
                            Program: ' . $assignment_detail->published_course->program->name . ' <br/>
                            Program Type: ' . $assignment_detail->published_course->program_type->name . '<br/>
                            Academic Year: ' . $assignment_detail->published_course->academic_year . ' <br/>
                            Semester: ' . $assignment_detail->published_course->semester,
                            'read' => 0,
                            'user_id' => $ownerDepartmentUser->id
                        ]);
                    }
                }
            }
        }

        if (!empty($autoMessages)) {
            $this->saveMany($autoMessages);
        }
    }

    public function sendNotificationOnDepartmentGradeChangeApproval($grade_change = null)
    {
        $autoMessages = [];

        $examGradeChangesTable = TableRegistry::getTableLocator()->get('ExamGradeChanges');
        $exam_grade_change = null;

        if (isset($grade_change['id']) && !empty($grade_change['id'])) {
            $exam_grade_change = $examGradeChangesTable->find()
                ->where(['ExamGradeChanges.id' => $grade_change['id']])
                ->first();
        } elseif (isset($grade_change['ExamGradeChange']['id']) && !empty($grade_change['ExamGradeChange']['id'])) {
            $exam_grade_change = $examGradeChangesTable->find()
                ->where(['ExamGradeChanges.id' => $grade_change['ExamGradeChange']['id']])
                ->first();
        } elseif (isset($grade_change['exam_grade_id']) && !empty($grade_change['exam_grade_id'])) {
            $exam_grade_change = $examGradeChangesTable->find()
                ->where(['ExamGradeChanges.exam_grade_id' => $grade_change['exam_grade_id']])
                ->order(['ExamGradeChanges.id' => 'DESC'])
                ->first();
        } elseif (isset($grade_change['ExamGradeChange']['exam_grade_id']) && !empty($grade_change['ExamGradeChange']['exam_grade_id'])) {
            $exam_grade_change = $examGradeChangesTable->find()
                ->where(['ExamGradeChanges.exam_grade_id' => $grade_change['ExamGradeChange']['exam_grade_id']])
                ->order(['ExamGradeChanges.id' => 'DESC'])
                ->first();
        }

        if (!$exam_grade_change) {
            return;
        }

        $exam_grade_details = $this->gradeRelatedDetails($exam_grade_change->exam_grade_id);

        if (isset($exam_grade_details['Instructor']['user_id']) && !empty($exam_grade_details['Instructor']['user_id']) && $exam_grade_change->initiated_by_department == 0) {
            $message = !empty($exam_grade_change->makeup_exam_result) && !empty($exam_grade_change->makeup_exam_id) ?
                'Your makeup exam grade submission to <u>' . $exam_grade_details['Student']['full_name'] . '</u> for the course <u>' . $exam_grade_details['Course']['course_title'] . ' (' . $exam_grade_details['Course']['course_code'] . ')</u> is ' . ($grade_change['department_approval'] == 1 ? (isset($grade_change['department_reply']) ? 're-accepted' : 'accepted') : (isset($grade_change['department_reply']) ? 'rejected (after register rejection)' : 'rejected')) . ' by <u>' . (!empty($exam_grade_details['PublishedCourse']['department_id']) ? $exam_grade_details['Department']['name'] . ' Department' : $exam_grade_details['College']['name'] . ' Freshman Program') . '</u>. <a href="/examResults/add/' . $exam_grade_details['PublishedCourse']['id'] . '">View Grade</a>' :
                'Your exam grade change request to <u>' . $exam_grade_details['Student']['full_name'] . '</u> for the course <u>' . $exam_grade_details['Course']['course_title'] . ' (' . $exam_grade_details['Course']['course_code'] . ')</u> is ' . ($grade_change['department_approval'] == 1 ? 'accepted' : 'rejected') . ' by <u>' . (!empty($exam_grade_details['PublishedCourse']['department_id']) ? $exam_grade_details['Department']['name'] . ' Department' : $exam_grade_details['College']['name'] . ' Freshman Program') . '</u>. <a href="/examResults/add/' . $exam_grade_details['PublishedCourse']['id'] . '">View Grade</a>';

            if ($grade_change['department_approval'] == -1) {
                $message = '<p style="text-align:justify; padding:0px; margin:0px" class="rejected">' . $message . '</p>';
            } elseif ($grade_change['department_approval'] == 1) {
                $message = '<p style="text-align:justify; padding:0px; margin:0px" class="accepted">' . $message . '</p>';
            }

            $autoMessages[] = $this->newEntity([
                'message' => $message,
                'read' => 0,
                'user_id' => $exam_grade_details['Instructor']['user_id']
            ]);
        }

        if (!empty($autoMessages)) {
            $this->saveMany($autoMessages);
        }
    }

    public function sendNotificationOnCollegeGradeChangeApproval($grade_change = null)
    {
        $autoMessages = [];

        $examGradeChangesTable = TableRegistry::getTableLocator()->get('ExamGradeChanges');
        $exam_grade_change = $examGradeChangesTable->find()
            ->where(['ExamGradeChanges.id' => $grade_change['id']])
            ->first();

        $exam_grade_details = $this->gradeRelatedDetails($exam_grade_change->exam_grade_id);

        if (isset($exam_grade_details['Instructor']['user_id']) && !empty($exam_grade_details['Instructor']['user_id']) && $exam_grade_change->initiated_by_department == 0) {
            $message = 'Your exam grade change request to <u>' . $exam_grade_details['Student']['full_name'] . '</u> for the course <u>' . $exam_grade_details['Course']['course_title'] . ' (' . $exam_grade_details['Course']['course_code'] . ')</u> is ' . ($grade_change['college_approval'] == 1 ? 'accepted' : 'rejected') . ' by <u>' . (!empty($exam_grade_details['PublishedCourse']['department_id']) ? $exam_grade_details['Department']['College']['name'] : $exam_grade_details['College']['name']) . '</u>. <a href="/examResults/add/' . $exam_grade_details['PublishedCourse']['id'] . '">View Grade</a>';

            if ($grade_change['college_approval'] == -1) {
                $message = '<p style="text-align:justify; padding:0px; margin:0px" class="rejected">' . $message . '</p>';
            } elseif ($grade_change['college_approval'] == 1) {
                $message = '<p style="text-align:justify; padding:0px; margin:0px" class="accepted">' . $message . '</p>';
            }

            $autoMessages[] = $this->newEntity([
                'message' => $message,
                'read' => 0,
                'user_id' => $exam_grade_details['Instructor']['user_id']
            ]);
        }

        if (!empty($exam_grade_change->department_approved_by)) {
            $approvalpage = $exam_grade_change->initiated_by_department == 1 ? '/examResults/submit_grade_for_instructor' :
                (!empty($exam_grade_details['Department']) ? '/examGrades/approve_non_freshman_grade_submission' : '/examGrades/approve_freshman_grade_submission');

            $message = 'Exam grade change request to <u>' . $exam_grade_details['Student']['full_name'] . '</u> for the course <u>' . $exam_grade_details['Course']['course_title'] . ' (' . $exam_grade_details['Course']['course_code'] . ')</u> is ' . ($grade_change['college_approval'] == 1 ? 'accepted' : 'rejected') . ' by <u>' . (!empty($exam_grade_details['PublishedCourse']['department_id']) ? $exam_grade_details['Department']['College']['name'] : $exam_grade_details['College']['name']) . '</u>. <a href="' . $approvalpage . '/' . $exam_grade_details['PublishedCourse']['id'] . '">View Grade</a>';

            if ($grade_change['college_approval'] == -1) {
                $message = '<p style="text-align:justify; padding:0px; margin:0px" class="rejected">' . $message . '</p>';
            } elseif ($grade_change['college_approval'] == 1) {
                $message = '<p style="text-align:justify; padding:0px; margin:0px" class="accepted">' . $message . '</p>';
            }

            $autoMessages[] = $this->newEntity([
                'message' => $message,
                'read' => 0,
                'user_id' => $exam_grade_change->department_approved_by
            ]);
        }

        if (!empty($autoMessages)) {
            $this->saveMany($autoMessages);
        }
    }


    public function sendNotificationOnRegistrarGradeChangeApproval($grade_change = null)
    {

        $autoMessages = [];

        if (empty($grade_change) || empty($grade_change['id'])) {
            return;
        }

        $examGradeChangesTable = TableRegistry::getTableLocator()->get('ExamGradeChanges');
        $exam_grade_change = $examGradeChangesTable->find()
            ->where(['ExamGradeChanges.id' => $grade_change['id']])
            ->first();

        if (empty($exam_grade_change)) {
            return;
        }

        $exam_grade_details = $this->gradeRelatedDetails($exam_grade_change->exam_grade_id);

        if (empty($exam_grade_details) || empty($exam_grade_details['ExamGrade'])) {
            return;
        }

        $initiated_fullname = '';
        if (!empty($exam_grade_change->department_approved_by) || !empty($exam_grade_change->college_approved_by)) {
            $usersTable = TableRegistry::getTableLocator()->get('Users');
            $initiated_fullname = $usersTable->find()
                ->where(['Users.id' => ($exam_grade_change->department_approved_by ?? $exam_grade_change->college_approved_by ?? 0)])
                ->select(['first_name','middle_name','last_name'])
                ->first()->full_name ?? '';
        }

        // Instructor notification
        if (!empty($exam_grade_details['Instructor']['user_id'])) {
            $message = $exam_grade_change->initiated_by_department == 0 ?
                'Registrar ' . ($grade_change['registrar_approval'] == 1 ? 'accepted' : 'rejected') . ' <strong>' . ($exam_grade_details['ExamGrade']['grade'] ?? '') . '</strong> to <strong>' . ($exam_grade_change->grade ?? '') . '</strong> grade change' . (!empty($exam_grade_change->makeup_exam_result) ? ' (through Supplementary Exam)' : '') . ' you initiated for <u>' . ($exam_grade_details['Student']['full_name_studentnumber'] ?? '') . '</u> for the course <u>' . trim($exam_grade_details['Course']['course_title'] ?? '') . ' (' . trim($exam_grade_details['Course']['course_code'] ?? '') . ')</u> you taught in the ' . ($exam_grade_details['PublishedCourse']['semester'] == 'I' ? '1st' : ($exam_grade_details['PublishedCourse']['semester'] == 'II' ? '2nd' : '3rd')) . ' semester of ' . ($exam_grade_details['PublishedCourse']['academic_year'] ?? '') . '. <a href="/examResults/add/' . ($exam_grade_details['PublishedCourse']['id'] ?? '') . '">View Grade</a>' :
                ($exam_grade_change->college_approved_by && $exam_grade_change->college_approved_by === $exam_grade_change->department_approved_by ?
                    'Registrar ' . ($grade_change['registrar_approval'] == 1 ? 'accepted' : 'rejected') . ' <strong>' .
                    ($exam_grade_details['ExamGrade']['grade'] ?? '') . '</strong> to <strong>' . ($exam_grade_change->grade ?? '') . '</strong> grade change' . (!empty($exam_grade_change->makeup_exam_result) ? ' through Supplementary Exam' : '') . ', initiated for <u>' . ($exam_grade_details['Student']['full_name_studentnumber'] ?? '') . '</u> by ' . (!empty($initiated_fullname) ? $initiated_fullname : 'your college') . ' for the course <u>' . trim($exam_grade_details['Course']['course_title'] ?? '') . ' (' . trim($exam_grade_details['Course']['course_code'] ?? '') . ')</u> you taught in the ' . ($exam_grade_details['PublishedCourse']['semester'] == 'I' ? '1st' : ($exam_grade_details['PublishedCourse']['semester'] == 'II' ? '2nd' : '3rd')) . ' semester of ' . ($exam_grade_details['PublishedCourse']['academic_year'] ?? '') . '. <a href="/examResults/add/' . ($exam_grade_details['PublishedCourse']['id'] ?? '') . '">View Grade</a>' :
                    'Registrar ' . ($grade_change['registrar_approval'] == 1 ? 'accepted' : 'rejected') . ' <strong>' .
                    ($exam_grade_details['ExamGrade']['grade'] ?? '') . '</strong> to <strong>' . ($exam_grade_change->grade ?? '') .
                    '</strong> grade change' . (!empty($exam_grade_change->makeup_exam_result) ? ' through Supplementary Exam' : '') . ', initiated for <u>' .
                    ($exam_grade_details['Student']['full_name_studentnumber'] ?? '') . '</u> by ' .
                    (!empty($initiated_fullname) ? $initiated_fullname : 'your department') . ' for the course <u>' .
                    trim($exam_grade_details['Course']['course_title'] ?? '') . ' (' .
                    trim($exam_grade_details['Course']['course_code'] ?? '') . ')</u> you taught in the ' .
                    ($exam_grade_details['PublishedCourse']['semester'] == 'I' ? '1st' : ($exam_grade_details['PublishedCourse']['semester'] == 'II' ? '2nd' : '3rd')) .
                    ' semester of ' . ($exam_grade_details['PublishedCourse']['academic_year'] ?? '') . '. <a href="/examResults/add/' .
                    ($exam_grade_details['PublishedCourse']['id'] ?? '') . '">View Grade</a>');

            if ($grade_change['registrar_approval'] == -1) {
                $message = '<p style="text-align:justify; padding:0px; margin:0px" class="rejected">' . $message . '</p>';
            } elseif ($grade_change['registrar_approval'] == 1) {
                $message = '<p style="text-align:justify; padding:0px; margin:0px" class="accepted">' . $message . '</p>';
            }

            $autoMessages[] = $this->newEntity([
                'message' => $message,
                'is_read' => 0,
                'user_id' => $exam_grade_details['Instructor']['user_id']
            ]);
        }

        // Department notification
        if (!empty($exam_grade_change->department_approved_by)) {
            $view_grade_url = 'department_grade_view';
            $message = $exam_grade_change->initiated_by_department == 1 ?
                'Registrar ' . ($grade_change['registrar_approval'] == 1 ? 'accepted' : 'rejected') . ' <strong>' . ($exam_grade_details['ExamGrade']['grade'] ?? '') . '</strong> to <strong>' . ($exam_grade_change->grade ?? '') . '</strong> ' . (!empty($exam_grade_change->makeup_exam_result) ? 'supplementary exam' : 'exam') . ' grade change you initiated for <u>' . ($exam_grade_details['Student']['full_name_studentnumber'] ?? '') . '</u> for the course <u>' . trim($exam_grade_details['Course']['course_title'] ?? '') . ' (' . trim($exam_grade_details['Course']['course_code'] ?? '') . ')</u> the student attended in the ' . ($exam_grade_details['PublishedCourse']['semester'] == 'I' ? '1st' : ($exam_grade_details['PublishedCourse']['semester'] == 'II' ? '2nd' : '3rd')) . ' semester of ' . ($exam_grade_details['PublishedCourse']['academic_year'] ?? '') . '.' :
                'Registrar ' . ($grade_change['registrar_approval'] == 1 ? 'accepted' : 'rejected') . ' <strong>' . ($exam_grade_details['ExamGrade']['grade'] ?? '') . '</strong> to <strong>' . ($exam_grade_change->grade ?? '') . '</strong> exam grade change initiated for <u>' . ($exam_grade_details['Student']['full_name_studentnumber'] ?? '') . '</u> for the course <u>' . trim($exam_grade_details['Course']['course_title'] ?? '') . ' (' . trim($exam_grade_details['Course']['course_code'] ?? '') . ')</u> the student attended in the ' . ($exam_grade_details['PublishedCourse']['semester'] == 'I' ? '1st' : ($exam_grade_details['PublishedCourse']['semester'] == 'II' ? '2nd' : '3rd')) . ' semester of ' . ($exam_grade_details['PublishedCourse']['academic_year'] ?? '') . '.';

            if ($grade_change['registrar_approval'] == -1) {
                $message = '<p style="text-align:justify; padding:0px; margin:0px" class="rejected">' . $message . '</p>';
            } elseif ($grade_change['registrar_approval'] == 1) {
                $message = '<p style="text-align:justify; padding:0px; margin:0px" class="accepted">' . $message . '</p>';
            }

            if (!empty($view_grade_url) && !empty($exam_grade_details['PublishedCourse']['id'])) {
                $message .= ' <a href="/examGrades/' . $view_grade_url . '/' . $exam_grade_details['PublishedCourse']['id'] . '/pc">View Grade</a>';
            }

            $autoMessages[] = $this->newEntity([
                'message' => $message,
                'is_read' => 0,
                'user_id' => $exam_grade_change->department_approved_by]);
        }

        // Student Notification
        if (!empty($exam_grade_details['Student']['user_id']) && $grade_change['registrar_approval'] == 1) {
            $autoMessages[] = $this->newEntity([
                'message' => 'Your Exam grade for the course <u>' . trim($exam_grade_details['Course']['course_title'] ?? '') . ' (' . trim($exam_grade_details['Course']['course_code'] ?? '') . ')</u> you attended in the ' . ($exam_grade_details['PublishedCourse']['semester'] == 'I' ? '1st' : ($exam_grade_details['PublishedCourse']['semester'] == 'II' ? '2nd' : '3rd')) . ' semester of ' . ($exam_grade_details['PublishedCourse']['academic_year'] ?? '') . ' is changed from <strong>' . ($exam_grade_details['ExamGrade']['grade'] ?? '') . '</strong> to <strong>' . ($exam_grade_change->grade ?? '') . '</strong>' . (!empty($exam_grade_change->makeup_exam_result) ? ' through supplementary exam grade change.' : ' through exam grade change.'),
                'is_read' => 0,
                'user_id' => $exam_grade_details['Student']['user_id']
            ]
            );
        }

        // College Notification
        if (!empty($exam_grade_change->college_approved_by)) {
            $view_grade_url = (empty($exam_grade_details['PublishedCourse']['year_level_id']) || empty($exam_grade_details['Student']['department_id'])) ? 'freshman_grade_view' : 'college_grade_view';
            $message = ($exam_grade_change->initiated_by_department == 1 && $exam_grade_change->college_approved_by === $exam_grade_change->department_approved_by) ?
                'Registrar ' . ($grade_change['registrar_approval'] == 1 ? 'accepted' : 'rejected') . ' <strong>' . ($exam_grade_details['ExamGrade']['grade'] ?? '') . '</strong> to <strong>' . ($exam_grade_change->grade ?? '') . '</strong> ' . (!empty($exam_grade_change->makeup_exam_result) ? 'supplementary exam' : 'exam') . ' grade change initiated for <u>' . ($exam_grade_details['Student']['full_name_studentnumber'] ?? '') . '</u> for the course <u>' . trim($exam_grade_details['Course']['course_title'] ?? '') . ' (' . trim($exam_grade_details['Course']['course_code'] ?? '') . ')</u> the student attended in the ' . ($exam_grade_details['PublishedCourse']['semester'] == 'I' ? '1st' : ($exam_grade_details['PublishedCourse']['semester'] == 'II' ? '2nd' : '3rd')) . ' semester of ' . ($exam_grade_details['PublishedCourse']['academic_year'] ?? '') . '.' :
                'Registrar ' . ($grade_change['registrar_approval'] == 1 ? 'accepted' : 'rejected') . ' <strong>' . ($exam_grade_details['ExamGrade']['grade'] ?? '') . '</strong> to <strong>' . ($exam_grade_change->grade ?? '') . '</strong> exam grade change initiated for <u>' . ($exam_grade_details['Student']['full_name_studentnumber'] ?? '') . '</u> for the course <u>' . trim($exam_grade_details['Course']['course_title'] ?? '') . ' (' . trim($exam_grade_details['Course']['course_code'] ?? '') . ')</u> the student attended in the ' . ($exam_grade_details['PublishedCourse']['semester'] == 'I' ? '1st' : ($exam_grade_details['PublishedCourse']['semester'] == 'II' ? '2nd' : '3rd')) . ' semester of ' . ($exam_grade_details['PublishedCourse']['academic_year'] ?? '') . '.';

            if ($grade_change['registrar_approval'] == -1) {
                $message = '<p style="text-align:justify; padding:0px; margin:0px" class="rejected">' . $message . '</p>';
            } elseif ($grade_change['registrar_approval'] == 1) {
                $message = '<p style="text-align:justify; padding:0px; margin:0px" class="accepted">' . $message . '</p>';
            }

            if (!empty($view_grade_url) && !empty($exam_grade_details['PublishedCourse']['id'])) {
                $message .= ' <a href="/examGrades/' . $view_grade_url . '/' . $exam_grade_details['PublishedCourse']['id'] . '/pc">View Grade</a>';
            }

            $autoMessages[] = $this->newEntity([
                'message' => $message,
                'is_read' => 0,
                'user_id' => $exam_grade_change->college_approved_by
            ]);
        }

        // Registrar Notification
        if (!empty($exam_grade_change->registrar_approved_by)) {
            $view_grade_url = 'registrar_grade_view';
            $message = 'You ' . ($grade_change['registrar_approval'] == 1 ? 'accepted' : 'rejected') . ' <strong>' . ($exam_grade_details['ExamGrade']['grade'] ?? '') . '</strong> to <strong>' . ($exam_grade_change->grade ?? '') . '</strong> ' . (!empty($exam_grade_change->makeup_exam_result) ? 'supplementary exam' : 'exam') . ' grade change ' . ($exam_grade_change->initiated_by_department == 1 ? 'initiated by ' . (!empty($initiated_fullname) ? $initiated_fullname : 'the department/college') : '') . ' for <u>' . ($exam_grade_details['Student']['full_name_studentnumber'] ?? '') . '</u> for the course <u>' . trim($exam_grade_details['Course']['course_title'] ?? '') . ' (' . trim($exam_grade_details['Course']['course_code'] ?? '') . ')</u> the student attended in the ' . ($exam_grade_details['PublishedCourse']['semester'] == 'I' ? '1st' : ($exam_grade_details['PublishedCourse']['semester'] == 'II' ? '2nd' : '3rd')) . ' semester of ' . ($exam_grade_details['PublishedCourse']['academic_year'] ?? '') . '.';

            if ($grade_change['registrar_approval'] == -1 || $grade_change['registrar_approval'] == 1) {
                $message = '<p style="text-align:justify; padding:0px; margin:0px">' . $message . '</p>';
            }

            if (!empty($view_grade_url) && !empty($exam_grade_details['PublishedCourse']['id'])) {
                $message .= ' <a href="/examGrades/' . $view_grade_url . '/' . $exam_grade_details['PublishedCourse']['id'] . '/pc">View Grade</a>';
            }

            $autoMessages[] = $this->newEntity([
                'message' => $message,
                'is_read' => 0,
                'user_id' => $exam_grade_change->registrar_approved_by
            ]);
        }

        if (!empty($autoMessages)) {
            $autoMessages[] = $this->newEntity([
                'message' => $message,
                'is_read' => 0,
                'user_id' => $exam_grade_change->registrar_approved_by
            ]);

            if( $this->saveMany($autoMessages, ['validate' => false])){

            } else {
                debug($autoMessages);
            }

        }
    }

    public function sendNotificationOnAutoAndManualGradeChange($grade_changes = null, $privilaged_registrars = [], $use_the_new_format = 0, $converted_by_full_name = '')
    {
        $autoMessages = [];

        if (!empty($grade_changes)) {
            foreach ($grade_changes as $grade_change) {
                $examGradeChangesTable = TableRegistry::getTableLocator()->get('ExamGradeChanges');
                $exam_grade_change = $examGradeChangesTable->find()
                    ->where(['ExamGradeChanges.exam_grade_id' => $grade_change['exam_grade_id']])
                    ->order(['ExamGradeChanges.id' => 'DESC'])
                    ->first();

                $exam_grade_details = $this->gradeRelatedDetails($grade_change['exam_grade_id']);

                $change_type_and_grade = '';
                if (!empty($exam_grade_change->auto_ng_conversion)) {
                    $change_type_and_grade = 'through Auto NG to F Conversion to <strong>' . ($exam_grade_change->grade ?? $grade_change['grade'] ?? '') . '</strong> grade';
                } elseif (!empty($exam_grade_change->manual_ng_conversion)) {
                    $change_type_and_grade = 'through Manual NG Conversion to <strong>' . ($exam_grade_change->grade ?? $grade_change['grade'] ?? '') . '</strong> grade';
                } elseif (!empty($exam_grade_change->makeup_exam_result) && $exam_grade_change->initiated_by_department == 1) {
                    $change_type_and_grade = 'through Supplementary Exam' . (!empty($exam_grade_details['ExamGrade']['grade']) ? ' from <strong>' . $exam_grade_details['ExamGrade']['grade'] . '</strong>' : '') . ' to <strong>' . ($exam_grade_change->grade ?? $grade_change['grade'] ?? '') . '</strong> grade';
                } elseif (!empty($exam_grade_change->makeup_exam_result)) {
                    $change_type_and_grade = 'through Supplementary Exam' . (!empty($exam_grade_details['ExamGrade']['grade']) ? ' from <strong>' . $exam_grade_details['ExamGrade']['grade'] . '</strong>' : '') . ' to <strong>' . ($exam_grade_change->grade ?? $grade_change['grade'] ?? '') . '</strong> grade';
                } elseif (!empty($exam_grade_change->registrar_approved_by)) {
                    $change_type_and_grade = 'manually' . (!empty($exam_grade_details['ExamGrade']['grade']) ? ' from <strong>' . $exam_grade_details['ExamGrade']['grade'] . '</strong>' : '') . ' to <strong>' . ($exam_grade_change->grade ?? $grade_change['grade'] ?? '') . '</strong> grade';
                }

                // Student Notification
                if (!empty($exam_grade_details['Student']['user_id'])) {
                    $autoMessages[] = $this->newEntity([
                        'message' => 'Your Exam grade is changed ' . $change_type_and_grade . ' for the course <u>' . trim($exam_grade_details['Course']['course_title']) . ' (' . trim($exam_grade_details['Course']['course_code']) . ')</u> from ' . ($exam_grade_details['PublishedCourse']['semester'] == 'I' ? '1st' : ($exam_grade_details['PublishedCourse']['semester'] == 'II' ? '2nd' : '3rd')) . ' semester of ' . $exam_grade_details['PublishedCourse']['academic_year'] . '.',
                        'read' => 0,
                        'user_id' => $exam_grade_details['Student']['user_id']
                    ]);
                }

                // Instructor Notification
                if (!empty($exam_grade_details['Instructor']['user_id'])) {
                    $message = 'Exam grade you submitted is changed ' . $change_type_and_grade . ' for <u>' . $exam_grade_details['Student']['full_name_studentnumber'] . '</u> for the course <u>' . trim($exam_grade_details['Course']['course_title']) . ' (' . trim($exam_grade_details['Course']['course_code']) . ')</u> from ' . ($exam_grade_details['PublishedCourse']['semester'] == 'I' ? '1st' : ($exam_grade_details['PublishedCourse']['semester'] == 'II' ? '2nd' : '3rd')) . ' semester of ' . $exam_grade_details['PublishedCourse']['academic_year'] . '.' . (!empty($exam_grade_details['PublishedCourse']['id']) ? ' <a href="/examResults/add/' . $exam_grade_details['PublishedCourse']['id'] . '">View Grade</a>' : '');
                    $autoMessages[] = $this->newEntity([
                        'message' => $message,
                        'read' => 0,
                        'user_id' => $exam_grade_details['Instructor']['user_id']
                    ]);
                }

                if (!$use_the_new_format && !empty($exam_grade_change->department_approved_by)) {
                    $view_grade_url = (empty($exam_grade_details['PublishedCourse']['year_level_id']) || empty($exam_grade_details['Student']['department_id'])) ? 'freshmanGradeView' : 'departmentGradeView';
                    $message = 'Exam Grade is changed ' . $change_type_and_grade . ' for <u>' . $exam_grade_details['Student']['full_name_studentnumber'] . '</u> for the course <u>' . trim($exam_grade_details['Course']['course_title']) . ' (' . trim($exam_grade_details['Course']['course_code']) . ')</u> from ' . ($exam_grade_details['PublishedCourse']['semester'] == 'I' ? '1st' : ($exam_grade_details['PublishedCourse']['semester'] == 'II' ? '2nd' : '3rd')) . ' semester of ' . $exam_grade_details['PublishedCourse']['academic_year'] . '.';
                    if (!empty($view_grade_url) && !empty($exam_grade_details['PublishedCourse']['id'])) {
                        $message .= ' <a href="/examGrades/' . $view_grade_url . '/' . $exam_grade_details['PublishedCourse']['id'] . '/pc">View Grade Details</a>';
                    }
                    $autoMessages[] = $this->newEntity([
                        'message' => $message,
                        'read' => 0,
                        'user_id' => $exam_grade_change->department_approved_by
                    ]);
                }

                if (!empty($privilaged_registrars)) {
                    foreach ($privilaged_registrars as $privilaged_registrar) {
                        $view_grade_url = '';
                        if ($privilaged_registrar['User']['role_id'] != ROLE_STUDENT) {
                            if ($privilaged_registrar['User']['role_id'] == ROLE_REGISTRAR) {
                                $view_grade_url = 'registrar_grade_view';
                            } elseif ($privilaged_registrar['User']['role_id'] == ROLE_COLLEGE) {
                                $view_grade_url = empty($exam_grade_details['Student']['department_id']) ? 'freshman_grade_view' : 'college_grade_view';
                            } elseif ($privilaged_registrar['User']['role_id'] == ROLE_DEPARTMENT) {
                                $view_grade_url = 'department_grade_view';
                            }
                        }

                        if (!$use_the_new_format) {
                            $department_ids = !empty($privilaged_registrar['StaffAssigne']['department_id']) ? unserialize($privilaged_registrar['StaffAssigne']['department_id']) : [];
                            $college_ids = !empty($privilaged_registrar['StaffAssigne']['college_id']) ? unserialize($privilaged_registrar['StaffAssigne']['college_id']) : [];

                            if ((!empty($exam_grade_details['Student']['department_id']) && !empty($department_ids) && in_array($exam_grade_details['Student']['department_id'], $department_ids)) ||
                                (empty($exam_grade_details['Student']['department_id']) && !empty($exam_grade_details['Student']['college_id']) && !empty($college_ids) && in_array($exam_grade_details['Student']['college_id'], $college_ids))) {
                                $message = 'Exam Grade is changed ' . $change_type_and_grade . ' for <u>' . $exam_grade_details['Student']['full_name_studentnumber'] . '</u> for the course <u>' . trim($exam_grade_details['Course']['course_title']) . ' (' . trim($exam_grade_details['Course']['course_code']) . ')</u> from ' . ($exam_grade_details['PublishedCourse']['semester'] == 'I' ? '1st' : ($exam_grade_details['PublishedCourse']['semester'] == 'II' ? '2nd' : '3rd')) . ' semester of ' . $exam_grade_details['PublishedCourse']['academic_year'];
                                $message .= !empty($exam_grade_change->auto_ng_conversion) ? ' automatically.' : ' manually.';
                                if (!empty($view_grade_url)) {
                                    $message .= ' <a href="/examGrades/' . $view_grade_url . '/' . $exam_grade_details['PublishedCourse']['id'] . '/pc">View Grade</a>';
                                }
                                $autoMessages[] = $this->newEntity([
                                    'message' => $message,
                                    'read' => 0,
                                    'user_id' => $privilaged_registrar['User']['id']
                                ]);
                            }
                        } else {
                            if ($privilaged_registrar['User']['role_id'] == ROLE_REGISTRAR) {
                                $message = !empty($exam_grade_change->auto_ng_conversion) ?
                                    'Exam Grade is changed ' . $change_type_and_grade . ' for <u>' . $exam_grade_details['Student']['full_name_studentnumber'] . '</u> for the course <u>' . trim($exam_grade_details['Course']['course_title']) . ' (' . trim($exam_grade_details['Course']['course_code']) . ')</u> from ' . ($exam_grade_details['PublishedCourse']['semester'] == 'I' ? '1st' : ($exam_grade_details['PublishedCourse']['semester'] == 'II' ? '2nd' : '3rd')) . ' semester of ' . $exam_grade_details['PublishedCourse']['academic_year'] . ' automatically by the System.' :
                                    'You applied an Exam Grade change ' . $change_type_and_grade . ' for <u>' . $exam_grade_details['Student']['full_name_studentnumber'] . '</u> for the course <u>' . trim($exam_grade_details['Course']['course_title']) . ' (' . trim($exam_grade_details['Course']['course_code']) . ')</u> from ' . ($exam_grade_details['PublishedCourse']['semester'] == 'I' ? '1st' : ($exam_grade_details['PublishedCourse']['semester'] == 'II' ? '2nd' : '3rd')) . ' semester of ' . $exam_grade_details['PublishedCourse']['academic_year'];
                            } else {
                                $message = 'Exam Grade is changed ' . $change_type_and_grade . ' for <u>' . $exam_grade_details['Student']['full_name_studentnumber'] . '</u> for the course <u>' . trim($exam_grade_details['Course']['course_title']) . ' (' . trim($exam_grade_details['Course']['course_code']) . ')</u> from ' . ($exam_grade_details['PublishedCourse']['semester'] == 'I' ? '1st' : ($exam_grade_details['PublishedCourse']['semester'] == 'II' ? '2nd' : '3rd')) . ' semester of ' . $exam_grade_details['PublishedCourse']['academic_year'];
                                $message .= !empty($exam_grade_change->auto_ng_conversion) ? ', automatically by the System.' : ', manually by ' . $converted_by_full_name . '.';
                            }

                            if (!empty($view_grade_url)) {
                                $message .= ' <a href="/examGrades/' . $view_grade_url . '/' . $exam_grade_details['PublishedCourse']['id'] . '/pc">View Grade</a>';
                            }

                            $autoMessages[] = $this->newEntity([
                                'message' => $message,
                                'read' => 0,
                                'user_id' => $privilaged_registrar['User']['id']
                            ]);
                        }
                    }
                }
            }
        }

        if (!empty($autoMessages)) {
            $this->saveMany($autoMessages);
        }
    }

    public function gradeRelatedDetails($exam_grade_id = null)
    {
        $exam_grade_details = [];

        $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');
        $exam_grade_details_r = $examGradesTable->find()
            ->where(['ExamGrades.id' => $exam_grade_id])
            ->contain([
                'CourseRegistrations' => [
                    'Students',
                    'PublishedCourses' => [
                        'Courses',
                        'Sections',
                        'Departments',
                        'Colleges',
                        'CourseInstructorAssignments' => function ($query) {
                            return $query
                                ->where([
                                    'OR' => [
                                        'CourseInstructorAssignments.type LIKE' => '%Lecture%',
                                        'CourseInstructorAssignments.isprimary' => 1
                                    ]
                                ])
                                ->contain(['Staffs' => ['Titles', 'Positions']])
                                ->limit(1);
                        }
                    ]
                ],
                'CourseAdds' => [
                    'Students',
                    'PublishedCourses' => [
                        'Courses',
                        'Sections',
                        'Departments' => ['Colleges'],
                        'Colleges',
                        'CourseInstructorAssignments' => function ($query) {
                            return $query
                                ->where([
                                    'OR' => [
                                        'CourseInstructorAssignments.type LIKE' => '%Lecture%',
                                        'CourseInstructorAssignments.isprimary' => 1
                                    ]
                                ])
                                ->contain(['Staffs' => ['Titles', 'Positions']])
                                ->limit(1);
                        }
                    ]
                ],
                'MakeupExams' => [
                    'Students',
                    'PublishedCourses' => [
                        'Courses',
                        'Sections',
                        'Departments' => ['Colleges'],
                        'Colleges',
                        'CourseInstructorAssignments' => function ($query) {
                            return $query
                                ->where([
                                    'OR' => [
                                        'CourseInstructorAssignments.type LIKE' => '%Lecture%',
                                        'CourseInstructorAssignments.isprimary' => 1
                                    ]
                                ])
                                ->contain(['Staffs' => ['Titles', 'Positions']])
                                ->limit(1);
                        }
                    ]
                ]
            ])
            ->first();

        if (empty($exam_grade_details_r) || empty($exam_grade_details_r->id)) {
            return [
                'ExamGrade' => [],
                'Student' => [],
                'Course' => [],
                'Section' => [],
                'Department' => [],
                'College' => [],
                'Instructor' => [],
                'PublishedCourse' => []
            ];
        }

        $exam_grade_details['ExamGrade'] = $exam_grade_details_r->toArray();

        if (!empty($exam_grade_details_r->course_registration) && !empty($exam_grade_details_r->course_registration->id)) {
            $exam_grade_details['Student'] = !empty($exam_grade_details_r->course_registration->student) ? $exam_grade_details_r->course_registration->student->toArray() : [];
            $exam_grade_details['Course'] = !empty($exam_grade_details_r->course_registration->published_course->course) ? $exam_grade_details_r->course_registration->published_course->course->toArray() : [];
            $exam_grade_details['Section'] = !empty($exam_grade_details_r->course_registration->published_course->section) ? $exam_grade_details_r->course_registration->published_course->section->toArray() : [];
            $exam_grade_details['Department'] = !empty($exam_grade_details_r->course_registration->published_course->department) ? $exam_grade_details_r->course_registration->published_course->department->toArray() : [];
            $exam_grade_details['College'] = !empty($exam_grade_details_r->course_registration->published_course->college) ? $exam_grade_details_r->course_registration->published_course->college->toArray() : [];
            $exam_grade_details['Instructor'] = !empty($exam_grade_details_r->course_registration->published_course->course_instructor_assignments) && !empty($exam_grade_details_r->course_registration->published_course->course_instructor_assignments[0]->staff) ?
                $exam_grade_details_r->course_registration->published_course->course_instructor_assignments[0]->staff->toArray() : [];
            $exam_grade_details['PublishedCourse'] = !empty($exam_grade_details_r->course_registration->published_course) ? $exam_grade_details_r->course_registration->published_course->toArray() : [];
        } elseif (!empty($exam_grade_details_r->course_add) && !empty($exam_grade_details_r->course_add->id)) {
            $exam_grade_details['Student'] = !empty($exam_grade_details_r->course_add->student) ? $exam_grade_details_r->course_add->student->toArray() : [];
            $exam_grade_details['Course'] = !empty($exam_grade_details_r->course_add->published_course->course) ? $exam_grade_details_r->course_add->published_course->course->toArray() : [];
            $exam_grade_details['Section'] = !empty($exam_grade_details_r->course_add->published_course->section) ? $exam_grade_details_r->course_add->published_course->section->toArray() : [];
            $exam_grade_details['Department'] = !empty($exam_grade_details_r->course_add->published_course->department) ? $exam_grade_details_r->course_add->published_course->department->toArray() : [];
            $exam_grade_details['College'] = !empty($exam_grade_details_r->course_add->published_course->college) ? $exam_grade_details_r->course_add->published_course->college->toArray() : [];
            $exam_grade_details['Instructor'] = !empty($exam_grade_details_r->course_add->published_course->course_instructor_assignments) && !empty($exam_grade_details_r->course_add->published_course->course_instructor_assignments[0]->staff) ?
                $exam_grade_details_r->course_add->published_course->course_instructor_assignments[0]->staff->toArray() : [];
            $exam_grade_details['PublishedCourse'] = !empty($exam_grade_details_r->course_add->published_course) ? $exam_grade_details_r->course_add->published_course->toArray() : [];
        } elseif (!empty($exam_grade_details_r->makeup_exam) && !empty($exam_grade_details_r->makeup_exam->id)) {
            $exam_grade_details['Student'] = !empty($exam_grade_details_r->makeup_exam->student) ? $exam_grade_details_r->makeup_exam->student->toArray() : [];
            $exam_grade_details['Course'] = !empty($exam_grade_details_r->makeup_exam->published_course->course) ? $exam_grade_details_r->makeup_exam->published_course->course->toArray() : [];
            $exam_grade_details['Section'] = !empty($exam_grade_details_r->makeup_exam->published_course->section) ? $exam_grade_details_r->makeup_exam->published_course->section->toArray() : [];
            $exam_grade_details['Department'] = !empty($exam_grade_details_r->makeup_exam->published_course->department) ? $exam_grade_details_r->makeup_exam->published_course->department->toArray() : [];
            $exam_grade_details['College'] = !empty($exam_grade_details_r->makeup_exam->published_course->college) ? $exam_grade_details_r->makeup_exam->published_course->college->toArray() : [];
            $exam_grade_details['Instructor'] = !empty($exam_grade_details_r->makeup_exam->published_course->course_instructor_assignments) && !empty($exam_grade_details_r->makeup_exam->published_course->course_instructor_assignments[0]->staff) ?
                $exam_grade_details_r->makeup_exam->published_course->course_instructor_assignments[0]->staff->toArray() : [];
            $exam_grade_details['PublishedCourse'] = !empty($exam_grade_details_r->makeup_exam->published_course) ? $exam_grade_details_r->makeup_exam->published_course->toArray() : [];
        } else {
            $exam_grade_details['Student'] = [];
            $exam_grade_details['Course'] = [];
            $exam_grade_details['Section'] = [];
            $exam_grade_details['Department'] = [];
            $exam_grade_details['College'] = [];
            $exam_grade_details['Instructor'] = [];
            $exam_grade_details['PublishedCourse'] = [];
        }

        return $exam_grade_details;
    }

    public function getInstructorLatestCourseAssignment($user_id = null)
    {
        if (empty($user_id)) {
            return [];
        }

        $staffsTable = TableRegistry::getTableLocator()->get('Staffs');
        $staff = $staffsTable->find()
            ->where(['Staffs.user_id' => $user_id])
            ->first();

        if (!$staff) {
            return [];
        }

        $courseInstructorAssignmentsTable = TableRegistry::getTableLocator()->get('CourseInstructorAssignments');
        $latest_course_assignment = $courseInstructorAssignmentsTable->find()
            ->where(['CourseInstructorAssignments.staff_id' => $staff->id])
            ->order(['CourseInstructorAssignments.created' => 'DESC'])
            ->first();

        if (!$latest_course_assignment) {
            return [];
        }

        $course_assignments = $courseInstructorAssignmentsTable->find()
            ->where([
                'CourseInstructorAssignments.staff_id' => $staff->id,
                'CourseInstructorAssignments.academic_year' => $latest_course_assignment->academic_year,
                'CourseInstructorAssignments.semester' => $latest_course_assignment->semester,
                'OR' => [
                    'CourseInstructorAssignments.type LIKE' => '%Lecture%',
                    'CourseInstructorAssignments.isprimary' => 1
                ]
            ])
            ->contain([
                'PublishedCourses' => [
                    'Departments',
                    'Colleges',
                    'Sections',
                    'Courses',
                    'CourseRegistrations' => [
                        'ExamGrades' => [
                            'order' => ['ExamGrades.id' => 'DESC', 'ExamGrades.created' => 'DESC']
                        ]
                    ],
                    'CourseAdds' => [
                        'ExamGrades' => [
                            'order' => ['ExamGrades.id' => 'DESC', 'ExamGrades.created' => 'DESC']
                        ]
                    ]
                ]
            ])
            ->all()
            ->toArray();

        $ongoing_courses = [];
        foreach ($course_assignments as $course_assignment) {
            $grade_submitted = true;

            if ($course_assignment->published_course->drop == 0) {
                if (!empty($course_assignment->published_course->course_registrations)) {
                    foreach ($course_assignment->published_course->course_registrations as $course_registration) {
                        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
                        $course_dropped = $courseRegistrationsTable->isCourseDropped($course_registration->id);

                        if (!$course_dropped && (empty($course_registration->exam_grades) || $course_registration->exam_grades[0]->department_approval == -1)) {
                            $grade_submitted = false;
                            break;
                        }
                    }
                }

                if ($grade_submitted && !empty($course_assignment->published_course->course_adds)) {
                    foreach ($course_assignment->published_course->course_adds as $course_add) {
                        if (empty($course_add->exam_grades) || $course_add->exam_grades[0]->department_approval == -1) {
                            $grade_submitted = false;
                            break;
                        }
                    }
                }

                if (!$grade_submitted) {
                    $ongoing_courses[] = [
                        'Course' => $course_assignment->published_course->course->toArray(),
                        'Section' => $course_assignment->published_course->section->toArray(),
                        'Department' => $course_assignment->published_course->department->toArray(),
                        'College' => $course_assignment->published_course->college->toArray(),
                        'PublishedCourse' => array_diff_key($course_assignment->published_course->toArray(), ['course' => '', 'course_registrations' => '', 'course_adds' => ''])
                    ];
                }
            }
        }

        return $ongoing_courses;
    }

    public function sendPermissionManagementBreakAttempt($user_id = null, $message = null)
    {
        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $user = $usersTable->find()
            ->where(['Users.id' => $user_id])
            ->first();

        $sys_admins = $usersTable->find()
            ->where(['Users.role_id' => 1])
            ->all()
            ->toArray();

        $autoMessages = [];

        if (!empty($sys_admins)) {
            foreach ($sys_admins as $sys_admin) {
                $msg = $message ?: '<u>' . $user->first_name . ' ' . $user->middle_name . ' ' . $user->last_name . ' (' . $user->username . ')</u> is trying to break permission management system. Please give appropriate warning.';
                $msg = '<p style="text-align:justify; padding:0px; margin:0px" class="rejected">' . $msg . '</p>';

                $autoMessages[] = $this->newEntity([
                    'message' => $msg,
                    'read' => 0,
                    'user_id' => $sys_admin->id
                ]);
            }
        }

        if (!empty($autoMessages)) {
            $this->saveMany($autoMessages);
        }
    }

    public function sendInappropriateAccessAttempt($user_id = null, $message = null)
    {
        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $user = $usersTable->find()
            ->where(['Users.id' => $user_id])
            ->first();

        $sys_admins = $usersTable->find()
            ->where(['Users.role_id' => 1])
            ->all()
            ->toArray();

        $autoMessages = [];

        if (!empty($sys_admins)) {
            foreach ($sys_admins as $sys_admin) {
                $msg = $message ?: '<u>' . $user->first_name . ' ' . $user->middle_name . ' ' . $user->last_name . ' (' . $user->username . ')</u> is trying to access a page or a permission not allowed to access. Please give appropriate warning.';
                $msg = '<p style="text-align:justify; padding:0px; margin:0px" class="rejected">' . $msg . '</p>';

                $autoMessages[] = $this->newEntity([
                    'message' => $msg,
                    'read' => 0,
                    'user_id' => $sys_admin->id
                ]);
            }
        }

        if (!empty($autoMessages)) {
            $this->saveMany($autoMessages);
        }
    }
}
?>
