<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\Query;
use Cake\Utility\Text;
use Cake\I18n\Time;
use Cake\Database\Expression\QueryExpression;

class StudentsTable extends Table
{

    public function initialize(array $config)
    {

        parent::initialize($config);

        $this->setTable('students');
        $this->setDisplayField('full_name');
        $this->setPrimaryKey('id');

        // BelongsTo Associations
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('AcceptedStudents', [
            'foreignKey' => 'accepted_student_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('Curriculums', [
            'foreignKey' => 'curriculum_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('Programs', [
            'foreignKey' => 'program_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('ProgramTypes', [
            'foreignKey' => 'program_type_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('Countries', [
            'foreignKey' => 'country_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('Regions', [
            'foreignKey' => 'region_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('Zones', [
            'foreignKey' => 'zone_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('Woredas', [
            'foreignKey' => 'woreda_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('Specializations', [
            'foreignKey' => 'specialization_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('Departments', [
            'foreignKey' => 'department_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('Colleges', [
            'foreignKey' => 'college_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('Cities', [
            'foreignKey' => 'city_id',
            'joinType' => 'LEFT',
        ]);

        // HasMany Associations
        $this->hasMany('CostSharingPayments', [
            'foreignKey' => 'student_id',
            'dependent' => false,
        ]);
        $this->hasMany('StudentNameHistories', [
            'foreignKey' => 'student_id',
            'dependent' => false,
        ]);
        $this->hasMany('DropOuts', [
            'foreignKey' => 'student_id',
            'dependent' => false,
        ]);
        $this->hasMany('CourseExemptions', [
            'foreignKey' => 'student_id',
            'dependent' => false,
        ]);
        $this->hasMany('GraduationWorks', [
            'foreignKey' => 'student_id',
            'dependent' => false,
        ]);
        $this->hasMany('ExitExams', [
            'foreignKey' => 'student_id',
            'dependent' => false,
        ]);
        $this->hasMany('Otps', [
            'foreignKey' => 'student_id',
            'dependent' => false,
        ]);
        $this->hasMany('ApplicablePayments', [
            'foreignKey' => 'student_id',
            'dependent' => false,
        ]);
        $this->hasMany('ExceptionMealAssignments', [
            'foreignKey' => 'student_id',
            'dependent' => false,
        ]);
        $this->hasMany('CostShares', [
            'foreignKey' => 'student_id',
            'dependent' => false,
        ]);
        $this->hasMany('Payments', [
            'foreignKey' => 'student_id',
            'dependent' => false,
        ]);
        $this->hasMany('MakeupExams', [
            'foreignKey' => 'student_id',
            'dependent' => false,
        ]);
        $this->hasMany('ResultEntryAssignments', [
            'foreignKey' => 'student_id',
            'dependent' => false,
        ]);
        $this->hasMany('Contacts', [
            'foreignKey' => 'student_id',
            'dependent' => false,
        ]);
        $this->hasMany('ProgramTypeTransfers', [
            'foreignKey' => 'student_id',
            'dependent' => false,
        ]);
        $this->hasMany('Clearances', [
            'foreignKey' => 'student_id',
            'dependent' => false,
        ]);
        $this->hasMany('Withdrawals', [
            'foreignKey' => 'student_id',
            'dependent' => false,
        ]);
        $this->hasMany('DepartmentTransfers', [
            'foreignKey' => 'student_id',
            'dependent' => false,
        ]);
        $this->hasMany('Readmissions', [
            'foreignKey' => 'student_id',
            'dependent' => false,
        ]);
        $this->hasMany('CurriculumAttachments', [
            'foreignKey' => 'student_id',
            'dependent' => false,
        ]);
        $this->hasMany('Attendances', [
            'foreignKey' => 'student_id',
            'dependent' => false,
        ]);
        $this->hasMany('EslceResults', [
            'foreignKey' => 'student_id',
            'dependent' => false,
        ]);
        $this->hasMany('EheeceResults', [
            'foreignKey' => 'student_id',
            'dependent' => false,
        ]);
        $this->hasMany('Attachments', [
            'foreignKey' => 'foreign_key',
            'conditions' => ['Attachments.model' => 'Student'],
            'dependent' => true,
        ]);
        $this->hasMany('HigherEducationBackgrounds', [
            'foreignKey' => 'student_id',
            'dependent' => false,
        ]);
        $this->hasMany('HighSchoolEducationBackgrounds', [
            'foreignKey' => 'student_id',
            'dependent' => false,
        ]);
        $this->hasMany('StudentExamStatuses', [
            'foreignKey' => 'student_id',
            'dependent' => false,
        ]);
        $this->hasMany('CourseRegistrations', [
            'foreignKey' => 'student_id',
            'dependent' => false,
        ]);
        $this->hasMany('CourseDrops', [
            'foreignKey' => 'student_id',
            'dependent' => false,
        ]);
        $this->hasMany('CourseAdds', [
            'foreignKey' => 'student_id',
            'dependent' => false,
        ]);
        $this->hasMany('SenateLists', [
            'foreignKey' => 'student_id',
            'dependent' => false,
        ]);
        $this->hasMany('Dismissals', [
            'foreignKey' => 'student_id',
            'dependent' => false,
        ]);
        $this->hasMany('TakenProperties', [
            'foreignKey' => 'student_id',
            'dependent' => false,
        ]);
        $this->hasMany('DormitoryAssignments', [
            'foreignKey' => 'student_id',
            'dependent' => false,
        ]);
        $this->hasMany('MealHallAssignments', [
            'foreignKey' => 'student_id',
            'dependent' => false,
        ]);
        $this->hasMany('MealAttendances', [
            'foreignKey' => 'student_id',
            'dependent' => false,
        ]);
        $this->hasMany('Disciplines', [
            'foreignKey' => 'student_id',
            'dependent' => false,
        ]);
        $this->hasMany('StudentRanks', [
            'foreignKey' => 'student_id',
            'dependent' => false,
        ]);
        $this->hasMany('PlacementPreferences', [
            'foreignKey' => 'student_id',
            'dependent' => false,
        ]);


        // HasOne Associations
        $this->hasOne('GraduateLists', [
            'foreignKey' => 'student_id',
            'dependent' => false,
        ]);
        $this->hasOne('Alumnus', [
            'foreignKey' => 'student_id',
            'dependent' => false,
        ]);

        // BelongsToMany Associations
        $this->belongsToMany('Courses', [
            'joinTable' => 'courses_students',
            'foreignKey' => 'student_id',
            'targetForeignKey' => 'course_id',
        ]);
        $this->belongsToMany('CourseSplitSections', [
            'joinTable' => 'students_course_split_sections',
            'foreignKey' => 'student_id',
            'targetForeignKey' => 'course_split_section_id',
        ]);

        $this->belongsToMany('Sections', [
            'foreignKey' => 'student_id',
            'targetForeignKey' => 'section_id',
            'joinTable' => 'students_sections',
            'through' => 'StudentsSections',
        ]);
        $this->hasMany('StudentsSections', [
            'foreignKey' => 'student_id'
        ]);


    }

    public function validationDefault(Validator $validator)
    {

        $validator
            ->scalar('first_name')
            ->notEmptyString('first_name', __('Please enter first name'))
            ->scalar('middle_name')
            ->notEmptyString('middle_name', __('Please enter middle name'))
            ->scalar('last_name')
            ->notEmptyString('last_name', __('Please enter last name'))
            ->email('email', false, __('Please enter a valid email address'))
            ->add('email', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => __('This email is already in use.')
            ])
            ->scalar('studentnumber')
            ->notEmptyString('studentnumber', __('Student ID Number is required'))
            ->add('studentnumber', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => __('This student number is already taken.')
            ])
            ->scalar('phone_mobile')
            ->notEmptyString('phone_mobile', __('Please enter mobile phone number'))
            ->add('phone_mobile', 'format', [
                'rule' => function ($value) {

                    return preg_match('/^\+251\d{9}$/', $value);
                },
                'message' => __('Phone number must be in +251999999999 format.')
            ])
            ->scalar('region_id')
            ->notEmptyString('region_id', __('Region is required'))
            ->scalar('zone_id')
            ->notEmptyString('zone_id', __('Zone is required'))
            ->scalar('woreda_id')
            ->notEmptyString('woreda_id', __('Woreda is required'));

        return $validator;
    }

    public function isUniqueStudentNumber($studentnumber, $id = null)
    {

        $query = $this->find()->where(['studentnumber LIKE' => trim($studentnumber) . '%']);
        if ($id) {
            $query->andWhere(['id !=' => $id]);
        }
        return $query->count() === 0;
    }

    public function checkLength($data, $fieldName)
    {

        if ($this->hasField($fieldName) && isset($data[$fieldName])) {
            return strlen($data[$fieldName]) <= 15;
        }
        return true;
    }

    public function checkLengthPhone($data, $fieldName)
    {

        if ($this->hasField($fieldName) && isset($data[$fieldName]) && !empty($data[$fieldName])) {
            $length = strlen($data[$fieldName]);
            return $length >= 9 && $length === 13;
        }
        return true;
    }

    public function checkUnique($data, $fieldName)
    {

        if ($this->hasField($fieldName) && isset($data[$fieldName])) {
            return $this->find()->where([$fieldName => $data[$fieldName]])->count() === 0;
        }
        return true;
    }

    public function readAllById($id = null)
    {

        if (!$id) {
            return [];
        }
        return $this->get($id, ['contain' => []])->toArray();
    }

    public function countId($collegeId = null, $year = null)
    {

        return $this->find()
            ->where([
                'admissionyear LIKE' => $year . '%',
                'college_id' => $collegeId,
                'studentnumber IS NOT NULL',
                'studentnumber !=' => ['', 'NULL']
            ])
            ->count();
    }

    public function admittedMoreThanOneProgram($departmentId = null)
    {

        $query = $this->find()
            ->select([
                'studentnumber',
                'first_name',
                'middle_name',
                'last_name',
                'department_id',
                'program_type_id',
                'program_id',
                'count' => $this->query()->func()->count('*')
            ])
            ->group(['first_name', 'middle_name', 'last_name'])
            ->having(['count >' => 1]);

        if ($departmentId) {
            $collegeId = explode('~', $departmentId);
            if (count($collegeId) > 1) {
                $query->where(['college_id' => $collegeId[1]]);
            } else {
                $query->where(['department_id' => $departmentId]);
            }
        }

        $students = $query->toArray();
        $formattedList = [];

        foreach ($students as $key => $student) {
            $sameAdmitted = $this->find()
                ->where([
                    'first_name' => $student['first_name'],
                    'middle_name' => $student['middle_name'],
                    'last_name' => $student['last_name']
                ])
                ->contain(['Departments', 'Programs', 'ProgramTypes'])
                ->toArray();
            if (!empty($sameAdmitted)) {
                $formattedList[$key] = $sameAdmitted;
            }
        }

        return $formattedList;
    }

    public function isAdmitted($id = null)
    {

        if (!$id) {
            return false;
        }
        return $this->find()->where(['accepted_student_id' => $id])->count() > 0;
    }

    public function studentAcademicDetail($id = null, $academicYear = null)
    {

        if (!$id) {
            return [];
        }

        $query = $this->find()
            ->select([
                'id',
                'studentnumber',
                'full_name',
                'curriculum_id',
                'department_id',
                'college_id',
                'program_id',
                'program_type_id',
                'gender',
                'graduated',
                'academicyear'
            ])
            ->where(['id' => $id])
            ->contain([
                'Departments' => ['fields' => ['id', 'name', 'type']],
                'Colleges' => ['fields' => ['id', 'name', 'type', 'campus_id', 'stream']],
                'Programs' => ['fields' => ['id', 'name']],
                'ProgramTypes' => ['fields' => ['id', 'name']],
                'Curriculums' => ['fields' => ['id', 'name', 'year_introduced', 'type_credit', 'active']],
                'StudentExamStatuses' => function (Query $q) use ($academicYear) {

                    return $q->where(['academic_year LIKE' => $academicYear . '%'])
                        ->contain(['AcademicStatuses' => ['fields' => ['name']]]);
                },
                'StudentsSections' => ['conditions' => ['archive' => 0]],
                'Sections' => [
                    'fields' => ['id', 'name', 'year_level_id'],
                    'YearLevels' => ['fields' => ['id', 'name']]
                ],
                'Courses' => [
                    'fields' => ['id', 'course_code', 'lecture_hours', 'tutorial_hours', 'credit', 'laboratory_hours'],
                    'PublishedCourses' => ['fields' => ['course_id', 'semester', 'academic_year']],
                    'YearLevels' => ['fields' => ['id', 'name']]
                ],
                'CourseRegistrations' => [
                    'order' => ['semester' => 'ASC'],
                    'ExamGrades' => ['fields' => ['id', 'grade', 'course_registration_id']],
                    'PublishedCourses.Courses' => [
                        'fields' => [
                            'id',
                            'course_title',
                            'course_code',
                            'credit',
                            'lecture_hours',
                            'tutorial_hours',
                            'laboratory_hours'
                        ]
                    ],
                    'YearLevels' => ['fields' => ['id', 'name']],
                    'Sections' => ['fields' => ['id', 'name']]
                ]
            ]);

        $studentSection = $query->first();

        if ($studentSection && !empty($studentSection->students_sections)) {
            $sectionsTable = TableRegistry::getTableLocator()->get('Sections');
            $section = $sectionsTable->find()->where(['id' => $studentSection->students_sections[0]->section_id]
            )->first();
            if ($section) {
                $studentSection->section = [$section->toArray()];
            }
        }

        return $studentSection ? $studentSection->toArray() : [];
    }

    public function getStudentsCurriculumForSection(
        $academicYear = null,
        $collegeId = null,
        $departmentId = null,
        $roleId = null,
        $selectedProgram = null,
        $selectedProgramType = null
    ) {

        $programTypesTable = TableRegistry::getTableLocator()->get('ProgramTypes');
        $programTypeIds = [$selectedProgramType];
        $equivalentIds = $programTypesTable->find()->select(['equivalent_to_id'])->where(['id' => $selectedProgramType]
        )->first();

        if ($equivalentIds && !empty($equivalentIds->equivalent_to_id)) {
            $programTypeIds = array_merge($programTypeIds, unserialize($equivalentIds->equivalent_to_id));
        }

        $conditions = [
            'OR' => [
                'AcceptedStudents.academicyear' => $academicYear,
                'academicyear' => $academicYear
            ],
            'program_id' => $selectedProgram,
            'program_type_id IN' => $programTypeIds,
            'curriculum_id IS NOT NULL',
            'curriculum_id !=' => [0, '']
        ];

        if ($roleId == ROLE_DEPARTMENT) {
            $conditions['department_id'] = $departmentId;
        } else {
            $conditions['college_id'] = $collegeId;
            $conditions['OR'][] = ['department_id IS NULL', 'department_id' => ''];
        }

        return $this->find()
            ->select(['id', 'curriculum_id'])
            ->where($conditions)
            ->contain(['Sections', 'AcceptedStudents' => ['fields' => ['id', 'academicyear']]])
            ->toArray();
    }

    public function getStudentsForCountSectionlessStudent(
        $collegeId = null,
        $roleId = null,
        $departmentId = null,
        $year = null,
        $selectedProgram = null,
        $selectedProgramType = null,
        $selectedCurriculum = null
    ) {

        $curriculumPattern = $selectedCurriculum ?? '%';
        $programTypesTable = TableRegistry::getTableLocator()->get('ProgramTypes');
        $programTypeIds = [$selectedProgramType];
        $equivalentIds = $programTypesTable->find()->select(['equivalent_to_id'])->where(['id' => $selectedProgramType]
        )->first();

        if ($equivalentIds && !empty($equivalentIds->equivalent_to_id)) {
            $programTypeIds = array_merge($programTypeIds, unserialize($equivalentIds->equivalent_to_id));
        }

        $conditions = [
            'OR' => [
                'AcceptedStudents.academicyear' => $year,
                'academicyear' => $year
            ],
            'program_id' => $selectedProgram,
            'program_type_id IN' => $programTypeIds,
            'curriculum_id LIKE' => $curriculumPattern,
            'graduated' => 0
        ];

        if ($roleId != ROLE_COLLEGE) {
            $conditions['department_id'] = $departmentId;
        } else {
            $conditions['college_id'] = $collegeId;
            $conditions['OR'][] = ['department_id IS NULL', 'department_id' => ''];
        }

        return $this->find()
            ->select(['id', 'full_name', 'studentnumber', 'gender'])
            ->where($conditions)
            ->contain(
                ['Sections' => ['fields' => ['id', 'name']], 'AcceptedStudents' => ['fields' => ['id', 'academicyear']]]
            )
            ->toArray();
    }

    public function unsetEmpty($data = null)
    {

        if (empty($data)) {
            return $data;
        }

        $studentId = $data['Student']['id'] ?? null;

        if (!empty($data['HighSchoolEducationBackground'])) {
            $saveHighschool = false;
            foreach ($data['HighSchoolEducationBackground'] as $k => &$v) {
                if (empty($v['name']) && empty($v['region_id']) && empty($v['town']) && empty($v['zone']) && empty($v['school_level'])) {
                    unset($data['HighSchoolEducationBackground'][$k]);
                } else {
                    if (empty($v['student_id']) && $studentId) {
                        $v['student_id'] = $studentId;
                    }
                    $saveHighschool = true;
                }
            }
            if (!$saveHighschool) {
                unset($data['HighSchoolEducationBackground']);
            }
        }

        if (!empty($data['HigherEducationBackground'])) {
            foreach ($data['HigherEducationBackground'] as $hk => &$hv) {
                if (empty($hv['name']) && empty($hv['field_of_study']) && empty($hv['cgpa_at_graduation'])) {
                    unset($data['HigherEducationBackground'][$hk]);
                } else {
                    if (empty($hv['student_id']) && $studentId) {
                        $hv['student_id'] = $studentId;
                    }
                }
            }
            if (empty($data['HigherEducationBackground'])) {
                unset($data['HigherEducationBackground']);
            }
        }

        if (!empty($data['EslceResult'])) {
            $extraEslceResult = false;
            foreach ($data['EslceResult'] as $ebk => &$ebv) {
                if (empty($ebv['subject']) && empty($ebv['grade'])) {
                    unset($data['EslceResult'][$ebk]);
                } else {
                    if (empty($ebv['student_id']) && $studentId) {
                        $ebv['student_id'] = $studentId;
                    }
                    $extraEslceResult = true;
                    if (isset($data['EslceResult'][0]['exam_year'])) {
                        $ebv['exam_year'] = $data['EslceResult'][0]['exam_year'];
                    }
                }
            }
            if (!$extraEslceResult) {
                unset($data['EslceResult']);
            }
        }

        if (!empty($data['EheeceResult'])) {
            $extraEheeceResult = false;
            foreach ($data['EheeceResult'] as $hbk => &$hbv) {
                if (empty($hbv['subject']) || (empty($hbv['mark']) || (int)$hbv['mark'] > 100)) {
                    unset($data['EheeceResult'][$hbk]);
                } else {
                    if (empty($hbv['student_id']) && $studentId) {
                        $hbv['student_id'] = $studentId;
                    }
                    $extraEheeceResult = true;
                    if (isset($data['EheeceResult'][0]['exam_year'])) {
                        $examYear = is_array($data['EheeceResult'][0]['exam_year'])
                            ? $data['EheeceResult'][0]['exam_year']
                            : (strlen($data['EheeceResult'][0]['exam_year']) == 4
                                ? $data['EheeceResult'][0]['exam_year'] . '-01-01'
                                : $data['EheeceResult'][0]['exam_year']);
                        $hbv['exam_year'] = $examYear;
                    } elseif (isset($hbv['exam_year']) && strlen($hbv['exam_year']) == 4) {
                        $hbv['exam_year'] = $hbv['exam_year'] . '-01-01';
                    } else {
                        $hbv['exam_year'] = date('Y-m-d');
                    }
                }
            }
            if (!$extraEheeceResult) {
                unset($data['EheeceResult']);
            }
        }

        if (!empty($data['Attachment'])) {
            foreach ($data['Attachment'] as $k => &$dv) {
                if (empty($dv['file']['name']) && empty($dv['file']['type']) && empty($dv['tmp_name'])) {
                    unset($data['Attachment'][$k]);
                } else {
                    $studentnumber = $this->find()->select(['studentnumber'])->where(['id' => $studentId])->first(
                    )->studentnumber;
                    $dv['group'] = 'profile';
                    $ext = pathinfo($dv['file']['name'], PATHINFO_EXTENSION);
                    $dv['file']['name'] = str_replace('/', '-', strtoupper($studentnumber)) . '.' . strtolower($ext);
                    $dv['model'] = 'Student';
                }
            }
        }

        return $data;
    }

    public function calculateStudentLoad($studentId = null, $semester = null, $academicYear = null, $detail = 0)
    {

        if (!$studentId || !$semester || !$academicYear) {
            return $detail ? ['registered' => 0, 'added' => 0, 'total' => 0] : 0;
        }

        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $courseAddsTable = TableRegistry::getTableLocator()->get('CourseAdds');

        $creditSumRegistered = $courseRegistrationsTable->find()
            ->where([
                'student_id' => $studentId,
                'academic_year' => $academicYear,
                'semester' => $semester
            ])
            ->contain([
                'PublishedCourses' => [
                    'Courses' => ['fields' => ['credit']],
                    'conditions' => [
                        'PublishedCourses.drop' => 0,
                        'PublishedCourses.semester' => $semester,
                        'PublishedCourses.academic_year' => $academicYear
                    ]
                ],
                'CourseDrops'
            ])
            ->where(['CourseDrops.id IS NULL'])
            ->sumOf('PublishedCourses.Courses.credit');

        $creditSumAdded = $courseAddsTable->find()
            ->where([
                'student_id' => $studentId,
                'academic_year' => $academicYear,
                'semester' => $semester,
                'department_approval' => 1,
                'registrar_confirmation' => 1
            ])
            ->contain([
                'PublishedCourses' => [
                    'Courses' => ['fields' => ['credit']],
                    'conditions' => [
                        'PublishedCourses.drop' => 0,
                        'PublishedCourses.semester' => $semester,
                        'PublishedCourses.academic_year' => $academicYear
                    ]
                ]
            ])
            ->sumOf('PublishedCourses.Courses.credit');

        $creditSumRegistered = $creditSumRegistered ?? 0;
        $creditSumAdded = $creditSumAdded ?? 0;

        if ($detail) {
            return [
                'registered' => $creditSumRegistered,
                'added' => $creditSumAdded,
                'total' => $creditSumRegistered + $creditSumAdded
            ];
        }

        return $creditSumRegistered + $creditSumAdded;
    }

    public function calculateCumulativeStudentRegisteredAddedCredit(
        $studentId = null,
        $all = 0,
        $semester = null,
        $academicYear = null,
        $detail = 0
    ) {

        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $courseAddsTable = TableRegistry::getTableLocator()->get('CourseAdds');
        $courseExemptionsTable = TableRegistry::getTableLocator()->get('CourseExemptions');
        $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');

        $conditions = ['student_id' => $studentId];
        if (!$all && $semester && $academicYear) {
            $conditions['academic_year'] = $academicYear;
            $conditions['semester'] = $semester;
        }

        $creditSumRegistered = 0;
        $courseRegistrations = $courseRegistrationsTable->find()
            ->where($conditions)
            ->contain([
                'PublishedCourses' => [
                    'Courses' => ['fields' => ['credit']],
                    'conditions' => ['PublishedCourses.drop' => 0]
                ],
                'CourseDrops'
            ])
            ->where(['CourseDrops.id IS NULL'])
            ->order(['academic_year' => 'ASC', 'semester' => 'ASC']);

        foreach ($courseRegistrations as $registration) {
            if ($all && $semester && $academicYear) {
                $lastRegistration = $courseRegistrationsTable->find()
                    ->select(['id'])
                    ->where([
                        'student_id' => $studentId,
                        'academic_year' => $academicYear,
                        'semester' => $semester
                    ])
                    ->order(['id' => 'DESC'])
                    ->first();
                if ($lastRegistration && $registration->id <= $lastRegistration->id) {
                    $isFirstTime = $examGradesTable->isRegistrationAddForFirstTime($registration->id, 1, 1);
                    if ($isFirstTime) {
                        $creditSumRegistered += $registration->published_course->course->credit;
                    }
                }
            } else {
                if ($registration->published_course->semester === $semester && $registration->published_course->academic_year === $academicYear) {
                    $creditSumRegistered += $registration->published_course->course->credit;
                }
            }
        }

        $creditSumAdded = 0;
        $courseAdds = $courseAddsTable->find()
            ->where(
                array_merge($conditions, [
                    'department_approval' => 1,
                    'registrar_confirmation' => 1
                ])
            )
            ->contain([
                'PublishedCourses' => [
                    'Courses' => ['fields' => ['credit']],
                    'conditions' => ['PublishedCourses.drop' => 0]
                ]
            ])
            ->order(['academic_year' => 'ASC', 'semester' => 'ASC']);

        foreach ($courseAdds as $add) {
            if ($all && $semester && $academicYear) {
                $lastAdd = $courseAddsTable->find()
                    ->select(['id'])
                    ->where([
                        'student_id' => $studentId,
                        'academic_year' => $academicYear,
                        'semester' => $semester,
                        'department_approval' => 1,
                        'registrar_confirmation' => 1
                    ])
                    ->order(['id' => 'DESC'])
                    ->first();
                if ($lastAdd && $add->id <= $lastAdd->id) {
                    $isFirstTime = $examGradesTable->isRegistrationAddForFirstTime($add->id, 0, 1);
                    if ($isFirstTime) {
                        $creditSumAdded += $add->published_course->course->credit;
                    }
                }
            } else {
                if ($add->published_course->semester === $semester && $add->published_course->academic_year === $academicYear) {
                    $creditSumAdded += $add->published_course->course->credit;
                }
            }
        }

        $creditSumExempted = 0;
        if ($all) {
            $creditSumExempted = $courseExemptionsTable->find()
                ->where([
                    'student_id' => $studentId,
                    'department_accept_reject' => 1,
                    'registrar_confirm_deny' => 1
                ])
                ->contain(['Courses' => ['fields' => ['credit']]])
                ->sumOf('Courses.credit');
        }

        $creditSumExempted = $creditSumExempted ?? 0;

        if ($detail) {
            return [
                'registered' => $creditSumRegistered,
                'added' => $creditSumAdded,
                'exempted' => $creditSumExempted,
                'total' => $creditSumRegistered + $creditSumAdded + $creditSumExempted
            ];
        }

        return $creditSumRegistered + $creditSumAdded + $creditSumExempted;
    }

    public function maxCreditExcludingI($studentId = null, $semester = null, $academicYear = null)
    {

        if (!$studentId || !$semester || !$academicYear) {
            return 0;
        }

        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $courseAddsTable = TableRegistry::getTableLocator()->get('CourseAdds');
        $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');

        $creditSumRegistered = 0;
        $creditSumI = 0;
        $registrations = $courseRegistrationsTable->find()
            ->where([
                'student_id' => $studentId,
                'academic_year' => $academicYear,
                'semester' => $semester
            ])
            ->contain([
                'PublishedCourses' => [
                    'Courses' => ['fields' => ['credit']],
                    'conditions' => [
                        'PublishedCourses.drop' => 0,
                        'PublishedCourses.semester' => $semester,
                        'PublishedCourses.academic_year' => $academicYear
                    ]
                ],
                'CourseDrops'
            ])
            ->where(['CourseDrops.id IS NULL']);

        foreach ($registrations as $registration) {
            $creditSumRegistered += $registration->published_course->course->credit;
            $grade = $examGradesTable->getApprovedGrade($registration->id, 1);
            if ($grade && strcasecmp($grade['grade'], 'I') === 0) {
                $creditSumI += $registration->published_course->course->credit;
            }
        }

        $creditSumAdded = 0;
        $adds = $courseAddsTable->find()
            ->where([
                'student_id' => $studentId,
                'academic_year' => $academicYear,
                'semester' => $semester,
                'department_approval' => 1,
                'registrar_confirmation' => 1
            ])
            ->contain([
                'PublishedCourses' => [
                    'Courses' => ['fields' => ['credit']],
                    'conditions' => [
                        'PublishedCourses.drop' => 0,
                        'PublishedCourses.semester' => $semester,
                        'PublishedCourses.academic_year' => $academicYear
                    ]
                ]
            ]);

        foreach ($adds as $add) {
            $creditSumAdded += $add->published_course->course->credit;
            $grade = $examGradesTable->getApprovedGrade($add->id, 0);
            if ($grade && strcasecmp($grade['grade'], 'I') === 0) {
                $creditSumI += $add->published_course->course->credit;
            }
        }

        $totalCredits = $creditSumRegistered + $creditSumAdded;
        return $totalCredits > $creditSumI ? $totalCredits - $creditSumI : $creditSumI;
    }

    public function checkAllowedMaxCreditLoadPerSemester($studentId = null, $semester = null, $academicYear = null)
    {
        if (!$studentId || !$semester || !$academicYear) {
            return 0;
        }

        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $courseAddsTable = TableRegistry::getTableLocator()->get('CourseAdds');

        // Fetch student with curriculum in a single query
        $student = $this->find()
            ->select([
                'id',
                'accepted_student_id',
                'college_id',
                'department_id',
                'studentnumber',
                'program_id',
                'program_type_id',
                'curriculum_id',
                'graduated'
            ])
            ->where(['id' => $studentId])
            ->contain([
                'Curriculums' => ['fields' => ['id', 'type_credit']]
            ])
            ->first();

        if (!$student || !$student->curriculum || empty($student->curriculum->type_credit)) {
            return 0;
        }

        // Determine max credit based on curriculum type
        $maxCredit = stripos($student->curriculum->type_credit, 'ECTS') !== false ? CREDIT_ECTS * 30 : 20;

        // Calculate registered credits
        $creditSumRegistered = $courseRegistrationsTable->find()
            ->select(['credit' => 'PublishedCourses.Courses.credit'])
            ->where([
                'CourseRegistrations.student_id' => $studentId,
                'CourseRegistrations.academic_year' => $academicYear,
                'CourseRegistrations.semester' => $semester
            ])
            ->contain([
                'PublishedCourses' => [
                    'Courses' => ['fields' => ['credit']],
                    'conditions' => [
                        'PublishedCourses.drop' => 0,
                        'PublishedCourses.semester' => $semester,
                        'PublishedCourses.academic_year' => $academicYear
                    ]
                ]
            ])
            ->where([
                function (QueryExpression $exp) use ($courseRegistrationsTable) {
                    return $exp->notExists(
                        $courseRegistrationsTable->find('CourseDrops')
                            ->select([1])
                            ->where(['CourseDrops.course_registration_id = CourseRegistrations.id'])
                    );
                }
            ])
            ->sumOf('PublishedCourses.Courses.credit') ?? 0;

        // Calculate added credits
        $creditSumAdded = $courseAddsTable->find()
            ->select(['credit' => 'PublishedCourses.Courses.credit'])
            ->where([
                'CourseAdds.student_id' => $studentId,
                'CourseAdds.academic_year' => $academicYear,
                'CourseAdds.semester' => $semester,
                'CourseAdds.department_approval' => 1,
                'CourseAdds.registrar_confirmation' => 1
            ])
            ->contain([
                'PublishedCourses' => [
                    'Courses' => ['fields' => ['credit']],
                    'conditions' => [
                        'PublishedCourses.drop' => 0,
                        'PublishedCourses.semester' => $semester,
                        'PublishedCourses.academic_year' => $academicYear
                    ]
                ]
            ])
            ->sumOf('PublishedCourses.Courses.credit') ?? 0;

        $totalCredits = $creditSumRegistered + $creditSumAdded;

        // Return 0 if total exceeds max, otherwise return total
        return $totalCredits <= $maxCredit ? $totalCredits : 0;
    }


    /**
     * Retrieves a student's section details for a given academic year and semester.
     *
     * @param int|null $studentId The student ID.
     * @param string|null $academicYear The academic year (e.g., '2024/25').
     * @param string|null $semester The semester (e.g., 'I', 'II').
     * @return array Student section details or empty array if not found.
     */

    /**
     * Retrieves a student's section details for a given academic year and semester
     *
     * @param int|null $studentId Student ID
     * @param string|null $academicYear Academic year
     * @param string|null $semester Semester
     * @return array Student section details
     */
    public function getStudentSection($studentId = null, $academicYear = null, $semester = null): array
    {
        if (!$studentId) {
            return [];
        }

        $query = $this->find()
            ->select([
                'Students.studentnumber',
                'Students.first_name',
                'Students.middle_name',
                'Students.last_name',
                'Students.curriculum_id',
                'Students.department_id',
                'Students.college_id',
                'Students.program_id',
                'Students.program_type_id',
                'Students.gender',
                'Students.graduated',
                'Students.academicyear',
                'Students.admissionyear'
            ])
            ->where(['Students.id' => $studentId])
            ->contain([
                'Departments' => ['fields' => ['id', 'name', 'type']],
                'Colleges' => ['fields' => ['id', 'name', 'type', 'campus_id', 'stream']],
                'Curriculums' => ['fields' => ['id', 'name', 'year_introduced', 'type_credit', 'active']],
                'Programs' => ['fields' => ['id', 'name']],
                'ProgramTypes' => ['fields' => ['id', 'name']],
                'Sections' => [
                    'fields' => ['id', 'name', 'year_level_id', 'academicyear', 'archive'],
                    'Students' => function ($q) {
                        return $q
                            ->select(['Students.id'])
                            ->where(['StudentsSections.archive' => 0]);
                    },
                    'YearLevels' => ['fields' => ['id', 'name']]
                ]
            ]);

        $studentSection = $query->first();

        if (!$studentSection) {
            return [];
        }

        $studentExamStatusesTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');
        $examStatusConditions = ['student_id' => $studentId];
        if ($academicYear && $semester) {
            $examStatusConditions['academic_year LIKE'] = $academicYear . '%';
            $examStatusConditions['semester'] = $semester;
        }

        $studentLatestStatus = $studentExamStatusesTable->find()
            ->where($examStatusConditions)
            ->order([
                'StudentExamStatuses.academic_year' => 'DESC',
                'StudentExamStatuses.semester' => 'DESC',
                'StudentExamStatuses.created' => 'DESC'
            ])
            ->contain(['AcademicStatuses' => ['fields' => ['id', 'name', 'computable']]])
            ->first();

        if (!$studentLatestStatus && $academicYear && $semester) {
            $previousSemester = $studentExamStatusesTable->getPreviousSemester($academicYear, $semester);
            $studentLatestStatus = $studentExamStatusesTable->find()
                ->where(['student_id' => $studentId, 'academic_year LIKE' => $previousSemester['academic_year'] . '%'])
                ->order(['StudentExamStatuses.created' => 'DESC'])
                ->contain(['AcademicStatuses' => ['fields' => ['id', 'name', 'computable']]])
                ->first();
        }

        $sectionDetail = [
            'StudentBasicInfo' => [
                'studentnumber' => $studentSection->studentnumber,
                'first_name' => $studentSection->first_name,
                'middle_name' => $studentSection->middle_name,
                'last_name' => $studentSection->last_name,
                'full_name' => $studentSection->full_name,
                'curriculum_id' => $studentSection->curriculum_id,
                'department_id' => $studentSection->department_id,
                'college_id' => $studentSection->college_id,
                'program_id' => $studentSection->program_id,
                'program_type_id' => $studentSection->program_type_id,
                'gender' => $studentSection->gender,
                'graduated' => $studentSection->graduated,
                'academic_year' => $studentSection->academicyear,
                'admissionyear' => $studentSection->admissionyear
            ],
            'Department' => $studentSection->department,
            'College' => $studentSection->college,
            'Program' => $studentSection->program,
            'ProgramType' => $studentSection->program_type,
            'Curriculum' => $studentSection->curriculum
        ];

        if (!empty($studentSection->sections)) {
            foreach ($studentSection->sections as $section) {
                if (!empty($section->students) && $section->students[0]->_joinData->archive == 0) {
                    $sectionDetail['Section'] = [
                        'id' => $section->id,
                        'name' => $section->name,
                        'year_level_id' => $section->year_level_id,
                        'academic_year' => $section->academicyear,
                        'archive' => $section->archive,
                        'year_level' => $section->year_level
                    ];
                    break;
                }
            }
        }

        if ($studentLatestStatus) {
            $sectionDetail['StudentExamStatus'] = [
                'id' => $studentLatestStatus->id,
                'student_id' => $studentLatestStatus->student_id,
                'academic_year' => $studentLatestStatus->academic_year,
                'semester' => $studentLatestStatus->semester,
                'academic_status_id' => $studentLatestStatus->academic_status_id,

                'AcademicStatus' => [
                    'id' => $studentLatestStatus->academic_status->id,
                    'name' => $studentLatestStatus->academic_status->name,
                    'computable' => $studentLatestStatus->academic_status->computable
                ]
            ];
        }

        return $sectionDetail;
    }

    public function getStudentRegisteredAndAddCourses($studentId = "")
    {

        if (empty($studentId) || $studentId == 0) {
            return [];
        }

        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $student = $this->find()
            ->where(['id' => $studentId])
            ->contain([
                'CourseExemptions' => [
                    'conditions' => ['department_accept_reject' => 1, 'registrar_confirm_deny' => 1],
                    'Courses'
                ],
                'CourseAdds' => [
                    'conditions' => ['department_approval' => 1, 'registrar_confirmation' => 1],
                    'PublishedCourses' => ['Courses']
                ],
                'CourseRegistrations' => ['PublishedCourses' => ['Courses']]
            ])
            ->first();

        $courses = [];

        if ($student && !empty($student->course_registrations)) {
            foreach ($student->course_registrations as $registration) {
                if ($registration->published_course->drop == 0 && !$courseRegistrationsTable->isCourseDropped(
                        $registration->id
                    )) {
                    $courses['Course Registered'][$registration->id . '~register'] = sprintf(
                        '%s (%s) - [%s Academic Year %s Semester]',
                        $registration->published_course->course->course_title,
                        $registration->published_course->course->course_code,
                        $registration->published_course->academic_year,
                        $registration->published_course->semester
                    );
                }
            }
        }

        if ($student && !empty($student->course_adds)) {
            foreach ($student->course_adds as $add) {
                if ($add->published_course->drop == 0) {
                    $courses['Course Added'][$add->id . '~add'] = sprintf(
                        '%s (%s) - [%s Academic Year %s Semester]',
                        $add->published_course->course->course_title,
                        $add->published_course->course->course_code,
                        $add->published_course->academic_year,
                        $add->published_course->semester
                    );
                }
            }
        }

        return $courses;
    }

    public function getPossibleStudentRegisteredAndAddCoursesForSup($studentId = "")
    {

        if (empty($studentId) || $studentId == 0) {
            return [];
        }

        $student = $this->find()
            ->select(['id', 'department_id', 'college_id', 'program_id', 'graduated', 'academicyear'])
            ->where(['id' => $studentId])
            ->first();

        if (!$student) {
            return [];
        }

        $sectionsTable = TableRegistry::getTableLocator()->get('Sections');
        $graduateListsTable = TableRegistry::getTableLocator()->get('GraduateLists');
        $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');
        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');

        $sectionIds = ['0', '0'];
        if ($student->department_id) {
            $sectionIds = $sectionsTable->find()
                ->select(['id'])
                ->where(['department_id' => $student->department_id, 'program_id' => $student->program_id])
                ->extract('id')
                ->toArray();
        } elseif ($student->college_id) {
            $sectionIds = $sectionsTable->find()
                ->select(['id'])
                ->where(
                    [
                        'college_id' => $student->college_id,
                        'program_id' => $student->program_id,
                        'academicyear' => $student->academicyear
                    ]
                )
                ->extract('id')
                ->toArray();
        }

        $allowedRepetitionGrades = $student->program_id == PROGRAM_POST_GRADUATE
            ? ['C', 'C+', 'D', 'F', 'NG', 'FAIL', 'I']
            : ['C-', 'D', 'F', 'NG', 'FAIL', 'I'];

        $courses = [];
        $registrations = $this->find()
            ->where(['id' => $studentId])
            ->contain([
                'CourseAdds' => [
                    'conditions' => ['department_approval' => 1, 'registrar_confirmation' => 1],
                    'PublishedCourses' => ['Courses'],
                    'Students' => ['fields' => ['id', 'graduated']]
                ],
                'CourseRegistrations' => [
                    'conditions' => ['section_id IN' => $sectionIds],
                    'PublishedCourses' => ['Courses'],
                    'Students' => ['fields' => ['id', 'graduated']]
                ]
            ])
            ->first();

        if ($registrations && !empty($registrations->course_registrations)) {
            foreach ($registrations->course_registrations as $registration) {
                if (!$registration->student->graduated) {
                    $graded = $examGradesTable->getApprovedNotChangedGrade($registration->id, 1);
                    if (
                        $registration->published_course->drop == 0 &&
                        !$courseRegistrationsTable->isCourseDropped($registration->id) &&
                        !$graduateListsTable->isGraduated($registration->student_id) &&
                        $graded &&
                        ($graded['allow_repetition'] || in_array($graded['grade'], $allowedRepetitionGrades))
                    ) {
                        $semesterLabel = $registration->published_course->semester == 'I' ? '1st' : ($registration->published_course->semester == 'II' ? '2nd' : '3rd');
                        $courses['Course Registered'][$registration->id . '~register'] = sprintf(
                            '%s (%s), Registered: %s semester, %s',
                            $registration->published_course->course->course_title,
                            $registration->published_course->course->course_code,
                            $semesterLabel,
                            $registration->published_course->academic_year
                        );
                    }
                }
            }
        }

        if ($registrations && !empty($registrations->course_adds)) {
            foreach ($registrations->course_adds as $add) {
                if (!$add->student->graduated) {
                    $graded = $examGradesTable->getApprovedNotChangedGrade($add->id, 0);
                    if (
                        $add->published_course->drop == 0 &&
                        !$graduateListsTable->isGraduated($add->student_id) &&
                        $graded &&
                        ($graded['allow_repetition'] || in_array($graded['grade'], $allowedRepetitionGrades))
                    ) {
                        $semesterLabel = $add->published_course->semester == 'I' ? '1st' : ($add->published_course->semester == 'II' ? '2nd' : '3rd');
                        $courses['Course Added'][$add->id . '~add'] = sprintf(
                            '%s (%s), Added: %s semester, %s',
                            $add->published_course->course->course_title,
                            $add->published_course->course->course_code,
                            $semesterLabel,
                            $add->published_course->academic_year
                        );
                    }
                }
            }
        }

        return $courses;
    }

    public function getStudentDetails($studentId = "")
    {

        if (empty($studentId)) {
            return [];
        }

        return $this->find()
            ->where(['id' => $studentId])
            ->contain(['Departments', 'Colleges', 'Programs', 'ProgramTypes'])
            ->first()
            ->toArray();
    }

    public function getListOfDepartmentStudentsByYearLevel(
        $collegeId = null,
        $departmentId = null,
        $programId = null,
        $programTypeId = null,
        $yearLevelId = null,
        $plusOne = 1,
        $gender = null,
        $studentIds = null,
        $acceptedStudentIds = null,
        $limit = 100
    ) {

        $academicYearsTable = TableRegistry::getTableLocator()->get('AcademicYears');
        $currentAcademicYear = $academicYearsTable->currentAcademicYear();
        $givenYearLevel = null;

        if ($yearLevelId) {
            if (is_numeric($yearLevelId)) {
                $yearLevelsTable = TableRegistry::getTableLocator()->get('YearLevels');
                $yearLevel = $yearLevelsTable->find()->where(['id' => $yearLevelId])->first();
                $givenYearLevel = substr($yearLevel->name, 0, -2);
            } else {
                $givenYearLevel = substr($yearLevelId, 0, -2);
            }
        }

        $conditions = [
            'NOT' => ['id IN' => (array)$studentIds],
            'id NOT IN' => $this->GraduateLists->find()->select(['student_id'])
        ];

        if ($departmentId) {
            $conditions['department_id'] = $departmentId;
        } elseif ($collegeId) {
            $conditions['college_id'] = $collegeId;
        }
        if ($programId) {
            $conditions['program_id'] = $programId;
        }
        if ($programTypeId) {
            $conditions['program_type_id'] = $programTypeId;
        }
        if ($gender) {
            $conditions['gender'] = $gender;
        }

        $students = $this->find()
            ->select(['id', 'studentnumber', 'full_name'])
            ->where($conditions)
            ->order(['admissionyear' => 'DESC'])
            ->limit($limit)
            ->toArray();

        $admittedStudents = [];
        foreach ($students as &$student) {
            $sectionsTable = TableRegistry::getTableLocator()->get('Sections');
            $yearLevel = $sectionsTable->getStudentYearLevel($student->id);
            $studentExamStatusesTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');
            $eligible = $studentExamStatusesTable->isEligibleForService($student->id, $currentAcademicYear);
            if ((empty($givenYearLevel) || (int)$yearLevel['year'] == $givenYearLevel) && $eligible) {
                $student->fxinlaststatus = $studentExamStatusesTable->checkFxPresenceInStatus(
                    $student->id
                ) == 0 ? 'Yes' : 'No';
                $admittedStudents[] = ['Student' => $student->toArray()];
            }
        }

        $nonAdmittedStudents = [];
        if (empty($givenYearLevel) || $givenYearLevel == 1) {
            $acceptedStudentsTable = TableRegistry::getTableLocator()->get('AcceptedStudents');
            $nonAdmittedConditions = [
                'NOT' => ['id IN' => (array)$acceptedStudentIds],
                'id NOT IN' => $this->find()->select(['accepted_student_id'])
            ];
            if ($departmentId) {
                $nonAdmittedConditions['department_id'] = $departmentId;
            } elseif ($collegeId) {
                $nonAdmittedConditions['college_id'] = $collegeId;
            }
            if ($programId) {
                $nonAdmittedConditions['program_id'] = $programId;
            }
            if ($programTypeId) {
                $nonAdmittedConditions['program_type_id'] = $programTypeId;
            }
            if ($gender) {
                $nonAdmittedConditions['sex'] = $gender;
            }
            $nonAdmittedStudents = $acceptedStudentsTable->find()
                ->select(['id', 'studentnumber', 'full_name'])
                ->where($nonAdmittedConditions)
                ->toArray();
        }

        return [
            'student' => $admittedStudents,
            'accepted_student' => $nonAdmittedStudents
        ];
    }

    public function getListOfDepartmentNonAssignedStudents(
        $collegeId = null,
        $programId = null,
        $programTypeId = null,
        $gender = null,
        $studentIds = null,
        $acceptedStudentIds = null,
        $limit = 100
    ) {

        $acceptedStudentsTable = TableRegistry::getTableLocator()->get('AcceptedStudents');
        $conditions = [
            'department_id IS NULL',
            'NOT' => ['id IN' => (array)$acceptedStudentIds],
            'id NOT IN' => $this->find()->select(['accepted_student_id'])
        ];

        if ($collegeId) {
            $conditions['college_id'] = $collegeId;
        }
        if ($programId) {
            $conditions['program_id'] = $programId;
        }
        if ($programTypeId) {
            $conditions['program_type_id'] = $programTypeId;
        }
        if ($gender) {
            $conditions['sex'] = $gender;
        }

        $nonAdmittedStudents = $acceptedStudentsTable->find()
            ->select(['id', 'studentnumber', 'full_name'])
            ->where($conditions)
            ->toArray();

        $studentConditions = [
            'department_id IS NULL',
            'NOT' => ['id IN' => (array)$studentIds]
        ];
        if ($collegeId) {
            $studentConditions['college_id'] = $collegeId;
        }
        if ($programId) {
            $studentConditions['program_id'] = $programId;
        }
        if ($programTypeId) {
            $studentConditions['program_type_id'] = $programTypeId;
        }
        if ($gender) {
            $studentConditions['gender'] = $gender;
        }

        $admittedStudents = $this->find()
            ->select(['id', 'studentnumber', 'full_name'])
            ->where($studentConditions)
            ->limit($limit)
            ->toArray();

        return [
            'accepted_student' => $nonAdmittedStudents,
            'student' => $admittedStudents
        ];
    }

    public function getStudentDetailsForHealth($studentnumber = null)
    {

        if (empty($studentnumber)) {
            return [];
        }

        return $this->find()
            ->select(['id', 'studentnumber', 'full_name', 'card_number', 'gender', 'birthdate'])
            ->where(['studentnumber' => $studentnumber])
            ->contain([
                'Colleges' => ['fields' => ['name']],
                'Departments' => ['fields' => ['name']],
                'Programs' => ['fields' => ['name']],
                'ProgramTypes' => ['fields' => ['name']]
            ])
            ->first()
            ->toArray();
    }



    public function getStudentRegisteredAddDropCurriculumResult(
        $studentId = "",
        $currentAcademicYear = null,
        $forDocument = 0,
        $includeBasicProfile = 1,
        $includeResult = 1,
        $includeExemption = 0
    ) {

        if (empty($studentId) || $studentId == 0) {
            return [];
        }

        $coursesTable = TableRegistry::getTableLocator()->get('Courses');
        $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');
        $curriculumsTable = TableRegistry::getTableLocator()->get('Curriculums');


        $student = $this->find()
            ->where(['Students.id' => $studentId])
            ->contain([
                'CourseExemptions' => [
                    'conditions' => ['CourseExemptions.department_accept_reject' => 1, 'CourseExemptions.registrar_confirm_deny' => 1],
                    'Courses',
                    'sort' => ['CourseExemptions.id' => 'ASC', 'CourseExemptions.course_id' => 'ASC']
                ],
                'CourseAdds' => [
                    'sort' => ['CourseAdds.academic_year' => 'ASC', 'CourseAdds.semester' => 'ASC', 'CourseAdds.id' => 'ASC'],
                    'conditions' => ['CourseAdds.department_approval' => 1, 'CourseAdds.registrar_confirmation' => 1],
                    'PublishedCourses' => [
                        'Courses' => [
                            'Curriculums',
                            'CourseBeSubstituted' => [
                                'fields' => [
                                    'CourseBeSubstituted.course_for_substituted_id',
                                    'CourseBeSubstituted.course_be_substituted_id'
                                ]
                            ]
                        ],
                        'Sections'
                    ]
                ],
                'CourseRegistrations' => [
                    'sort' => ['CourseRegistrations.academic_year' => 'ASC', 'CourseRegistrations.semester' => 'ASC', 'CourseRegistrations.id' => 'ASC'],
                    'PublishedCourses' => [
                        'Courses' => [
                            'Curriculums',
                            'Departments',
                            'CourseBeSubstituted' => [
                                'fields' => [
                                    'CourseBeSubstituted.course_for_substituted_id',
                                    'CourseBeSubstituted.course_be_substituted_id'
                                ]
                            ]
                        ],
                        'Sections'
                    ]
                ],
                'CostShares',
                'CostSharingPayments',
                'ApplicablePayments',
                'Payments',
                'CourseExemptions'
            ])
            ->first();

        $courses = [];
        $curriculumId = $this->find()->select(['curriculum_id'])->where(['id' => $studentId])->first()->curriculum_id;

        $curriculumCourses = $curriculumId
            ? $coursesTable->find()->where(['curriculum_id' => $curriculumId])->extract('id')->toArray()
            : [];

        if ($student && !empty($student->course_registrations)) {
            foreach ($student->course_registrations as $registration) {
                $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
                if ($registration->published_course->drop == 0 && !$courseRegistrationsTable->isCourseDropped(
                        $registration->id
                    )) {
                    $courseData = [
                        'course_title' => sprintf(
                            '%s (%s)',
                            $registration->published_course->course->course_title,
                            $registration->published_course->course->course_code
                        ),
                        'credit' => $registration->published_course->course->credit,
                        'curriculum_id' => $registration->published_course->course->curriculum_id,
                        'curriculumname' => sprintf(
                            '%s %s',
                            $registration->published_course->course->curriculum->name,
                            $registration->published_course->course->curriculum->year_introduced
                        ),
                        'acadamic_year' => $registration->published_course->academic_year,
                        'semester' => $registration->published_course->semester,
                        'sectionName' => $registration->published_course->section->name,
                        'course_id' => $registration->published_course->course->id
                    ];
                    if ($curriculumId != $registration->published_course->course->curriculum_id) {
                        foreach ($registration->published_course->course->course_be_substituted as $sub) {
                            if (in_array($sub->course_for_substituted_id, $curriculumCourses)) {
                                $courseData['mapped'] = $sub->course_for_substituted_id;
                            }
                            $courseData['otherCurriculum'] = 1;
                        }
                    }
                    $courses['Course Registered'][$registration->id . '~register'] = $courseData;
                } elseif ($courseRegistrationsTable->isCourseDropped($registration->id)) {
                    $courseData = [
                        'course_title' => sprintf(
                            '%s (%s)',
                            $registration->published_course->course->course_title,
                            $registration->published_course->course->course_code
                        ),
                        'acadamic_year' => $registration->published_course->academic_year,
                        'semester' => $registration->published_course->semester,
                        'credit' => $registration->published_course->course->credit,
                        'curriculum_id' => $registration->published_course->course->curriculum_id,
                        'curriculumName' => sprintf(
                            '%s %s',
                            $registration->published_course->course->curriculum->name,
                            $registration->published_course->course->curriculum->year_introduced
                        ),
                        'sectionName' => $registration->published_course->section->name,
                        'course_id' => $registration->published_course->course->id
                    ];
                    $courses['Course Dropped'][$registration->id . '~register'] = $courseData;
                }
            }
        }

        if ($student && !empty($student->course_adds)) {
            foreach ($student->course_adds as $add) {
                if ($add->published_course->drop == 0) {
                    $courseData = [
                        'course_title' => sprintf(
                            '%s (%s)',
                            $add->published_course->course->course_title,
                            $add->published_course->course->course_code
                        ),
                        'credit' => $add->published_course->course->credit,
                        'acadamic_year' => $add->published_course->academic_year,
                        'semester' => $add->published_course->semester,
                        'curriculum_id' => $add->published_course->course->curriculum_id,
                        'curriculumName' => sprintf(
                            '%s %s',
                            $add->published_course->course->curriculum->name,
                            $add->published_course->course->curriculum->year_introduced
                        ),
                        'sectionName' => $add->published_course->section->name,
                        'course_id' => $add->published_course->course->id
                    ];
                    if ($curriculumId != $add->published_course->course->curriculum_id) {
                        foreach ($add->published_course->course->course_be_substituted as $sub) {
                            if (in_array($sub->course_for_substituted_id, $curriculumCourses)) {
                                $courseData['mapped'] = $sub->course_for_substituted_id;
                            }
                            $courseData['otherCurriculum'] = 1;
                        }
                    }
                    $courses['Course Added'][$add->id . '~add'] = $courseData;
                }
            }
        }

        if ($includeExemption && $student && !empty($student->course_exemptions)) {
            foreach ($student->course_exemptions as $exemption) {
                $courses['Course Exempted'][$exemption->id . '~exempt'] = [
                    'transfer_from' => $exemption->transfer_from,
                    'taken_course_title' => $exemption->taken_course_title,
                    'taken_course_code' => $exemption->taken_course_code,
                    'course_taken_credit' => $exemption->course_taken_credit,
                    'course_title' => sprintf(
                        '%s (%s)',
                        $exemption->course->course_title,
                        $exemption->course->course_code
                    ),
                    'credit' => $exemption->course->credit,
                    'grade' => $exemption->grade,
                    'curriculum_id' => $exemption->course->curriculum_id,
                    'curriculumName' => sprintf(
                        '%s %s',
                        $exemption->course->curriculum->name,
                        $exemption->course->curriculum->year_introduced
                    ),
                    'registrar_approve_by' => $exemption->registrar_approve_by,
                    'request_date' => $exemption->request_date,
                    'registrar_approve_date' => $exemption->modified,
                    'course_id' => $exemption->course->id
                ];
            }
        }

        if ($includeBasicProfile) {
            $basic = $this->find()
                ->where(['Students.id' => $studentId])
                ->contain([
                    'Attachments' => function ($q) {
                        return $q
                            ->where(['Attachments.model' => 'Student'])
                            ->order(['Attachments.created' => 'DESC']);
                    },
                    'Curriculums' => [
                        'Departments',
                        'Courses' => [
                            'CourseCategories',
                            'Prerequisites' => ['PrerequisiteCourses'],
                            'GradeTypes',
                            'YearLevels' => ['fields' => ['id', 'name']]
                        ],
                        'CourseCategories',
                        'Programs' => ['fields' => ['id', 'name']]
                    ],
                    'Programs',
                    'ProgramTypes',
                    'Users',
                    'Countries',
                    'Regions',
                    'Zones',
                    'Woredas',
                    'Cities',
                    'Departments',
                    'Colleges',
                    'CostShares',
                    'ApplicablePayments',
                    'CourseExemptions'
                ])
                ->first();



            if ($basic) {

                $courses['BasicInfo'] = [
                    'Student' => $basic->toArray(),
                    'Attachment' => $basic->attachments,
                    'Program' => $basic->program,
                    'Department' => $basic->department,
                    'College' => $basic->college,
                    'ProgramType' => $basic->program_type,
                    'Country' => $basic->country,
                    'Region' => $basic->region,
                  //  'Curriculum' => $basic->curriculum,
                    'User' => $basic->user
                ];
                $courses['CourseExemption'] = $student->course_exemptions;
                $courses['CostShare'] = $student->cost_shares;
                $courses['CostSharingPayment'] = $student->cost_sharing_payments;
                $courses['ApplicablePayment'] = $student->applicable_payments;
                $courses['Payment'] = $student->payments;
                $courses['Curriculum'] = $curriculumsTable->organizedCourseOfCurriculumByYearSemester(
                    $basic->curriculum->toArray()
                );

            }
        }

        if ($includeResult) {
            $aySemesterList = $forDocument
                ? $examGradesTable->getListOfAyAndSemester($studentId, null, null)
                : $examGradesTable->getListOfAyAndSemester($studentId, $currentAcademicYear);

            if (!empty($aySemesterList)) {
                foreach ($aySemesterList as $ayS) {
                    $courses['Exam Result'][$ayS['academic_year'] . '~' . $ayS['semester']] = $examGradesTable->getStudentACProfile(
                        $studentId,
                        $ayS['academic_year'],
                        $ayS['semester']
                    );
                }
            }
        }

        return $courses;
    }

    public function getStudentLists($studentIds = [])
    {

        $list = $this->find()->where(['id IN' => $studentIds])->toArray();
        $stuList = [];
        foreach ($list as $student) {
            $stuList[$student->id] = sprintf('%s (%s)', $student->full_name, $student->studentnumber);
        }
        return $stuList;
    }

    public function getProfileNotBuildList($maxNotBuildTime = null, $departmentIds = null, $collegeIds = null, $programIds = null, $programTypeIds = null)
    {
        $contactsTable = TableRegistry::getTableLocator()->get('Contacts');

        // Validate and calculate not_build_for date
        $maxNotBuildTime = is_numeric($maxNotBuildTime) && $maxNotBuildTime > 0 ? (int)$maxNotBuildTime : DAYS_BACK_PROFILE;
        $notBuildFor = (new Time())->modify("-{$maxNotBuildTime} days")->toDateString();

        // Handle PROGRAM_REMEDIAL exclusion
        if ($programIds) {
            $programIds = (array)$programIds;
            if (in_array(PROGRAM_REMEDIAL, $programIds)) {
                $programIds = array_diff($programIds, [PROGRAM_REMEDIAL]);
            }
            if (count($programIds) === 1 && reset($programIds) == PROGRAM_REMEDIAL) {
                return [];
            }
        }

        // Early return if no department or college IDs provided
        if (empty($departmentIds) && empty($collegeIds)) {
            return [];
        }

        // Base conditions
        $conditions = [
            'graduated' => 0,
            'created >=' => $notBuildFor,
            function (QueryExpression $exp) use ($contactsTable) {
                return $exp->notExists(
                    $contactsTable->find()
                        ->select([1])
                        ->where(['Contacts.student_id = Students.id'])
                );
            }
        ];

        // Add filter conditions
        if ($programIds) {
            $conditions['program_id IN'] = $programIds;
        }
        if ($programTypeIds) {
            $conditions['program_type_id IN'] = (array)$programTypeIds;
        }

        // Department or college filter
        if ($departmentIds) {
            $conditions['department_id IN'] = (array)$departmentIds;
        } elseif ($collegeIds) {
            $conditions['department_id IS'] = null;
            $conditions['college_id IN'] = (array)$collegeIds;
        }

        return $this->find()
            ->select([
                'id',
                'full_name',
                'studentnumber',
                'program_id',
                'program_type_id',
                'department_id',
                'college_id',
                'created'
            ])
            ->where($conditions)
            ->contain([
                'Programs' => ['fields' => ['id', 'name']],
                'ProgramTypes' => ['fields' => ['id', 'name']],
                'Departments' => ['fields' => ['id', 'name']],
                'Colleges' => ['fields' => ['id', 'name']]
            ])
            ->toArray();
    }

    public function getStudentPassword($studentIds)
    {

        $returnStudents = [];
        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $acceptedStudentsTable = TableRegistry::getTableLocator()->get('AcceptedStudents');

        foreach ($studentIds as $studentId) {
            $student = $this->find()->where(['id' => $studentId['student_id']])->contain(['Users'])->first();
            if (!$student) {
                continue;
            }

            $userData = [
                'role_id' => ROLE_STUDENT,
                'is_admin' => 0,
                'username' => trim($student->studentnumber),
                'password' => $studentId['hashed_password'],
                'first_name' => $student->first_name,
                'middle_name' => $student->middle_name,
                'last_name' => $student->last_name,
                'email' => $student->email ?: (str_replace(
                        '/',
                        '.',
                        strtolower(trim($student->studentnumber))
                    ) . INSTITUTIONAL_EMAIL_SUFFIX),
                'last_password_change_date' => $student->created,
                'force_password_change' => 1
            ];

            if ($student->user && $student->user->id && $student->studentnumber == $student->user->username) {
                $userData['id'] = $student->user_id;
            } else {
                $existingUser = $usersTable->find()->where(['username' => $student->studentnumber])->first();
                if ($existingUser && $student->studentnumber == $existingUser->username) {
                    $userData['id'] = $existingUser->id;
                }
            }

            $user = $usersTable->newEntity($userData);
            if ($usersTable->save($user)) {
                if (empty($student->user_id)) {
                    $this->updateAll(['user_id' => $user->id], ['id' => $studentId['student_id']]);
                    if (empty($student->email)) {
                        $this->updateAll(['email' => $userData['email']], ['id' => $studentId['student_id']]);
                    }
                    $acceptedStudentsTable->updateAll(['user_id' => $user->id], ['id' => $student->accepted_student_id]
                    );
                }

                $studentDetails = $this->find()
                    ->where(['id' => $studentId['student_id']])
                    ->contain(['Colleges' => ['Campuses'], 'Departments', 'Programs', 'ProgramTypes', 'Users'])
                    ->first();

                if ($studentDetails) {
                    if (!empty($studentId['flat_password'])) {
                        $studentDetails->password_flat = $studentId['flat_password'];
                    }
                    $universitiesTable = TableRegistry::getTableLocator()->get('Universities');
                    $studentDetails->university = $universitiesTable->getStudentUniversity($studentId['student_id']);
                    $returnStudents[] = $studentDetails->toArray();
                }
            }
        }

        return $returnStudents;
    }

    public function generatePassword($length = '')
    {

        $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $length = $length ?: rand(8, 12);
        return Text::random($str, $length);
    }

    public function listStudentByAdmissionYear(
        $departmentId = null,
        $collegeId = null,
        $year = null,
        $name = null,
        $excludeGraduated = ''
    ) {

        $graduated = empty($excludeGraduated) ? [0, 1] : [$excludeGraduated];
        $conditions = [
            'academicyear' => $year,
            'graduated IN' => $graduated
        ];

        if ($collegeId && empty($departmentId)) {
            $conditions['college_id'] = $collegeId;
            $conditions['department_id IS NULL'];
        } elseif ($departmentId) {
            $conditions['department_id'] = $departmentId;
        }

        if ($name) {
            $conditions['OR'] = [
                'first_name LIKE' => '%' . trim($name) . '%',
                'middle_name LIKE' => '%' . trim($name) . '%',
                'last_name LIKE' => '%' . trim($name) . '%',
                'studentnumber LIKE' => '%' . trim($name) . '%'
            ];
        }

        return $this->find()
            ->where($conditions)
            ->contain([
                'StudentsSections' => ['conditions' => ['archive' => 0]],
                'Sections' => ['conditions' => ['academicyear' => $year, 'archive' => 0]]
            ])
            ->toArray();
    }
    public function getAttrationRate($academicYear, $semester, $programId = null, $programTypeId = null, $departmentId = null, $yearLevelId = null, $regionId = null, $sex = null)
    {
        $yearLevelsTable = TableRegistry::getTableLocator()->get('YearLevels');
        $studentExamStatusesTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');
        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');

        $conditions = ['Students.graduated' => 1];
        $matchingYearIds = [];

        if ($departmentId) {
            $collegeId = explode('~', $departmentId);
            if (count($collegeId) > 1) {
                $conditions['Students.college_id'] = $collegeId[1];
            } else {
                $conditions['Students.department_id'] = $departmentId;
                if ($yearLevelId) {
                    $matchingYearIds = $yearLevelsTable->find()
                        ->where(['name' => $yearLevelId, 'department_id' => $departmentId])
                        ->extract('id')
                        ->toArray();
                }
            }
        }

        if ($programId) {
            $programIds = explode('~', $programId);
            $conditions['Students.program_id'] = count($programIds) > 1 ? $programIds[1] : $programId;
        }

        if ($programTypeId) {
            $programTypeIds = explode('~', $programTypeId);
            $conditions['Students.program_type_id'] = count($programTypeIds) > 1 ? $programTypeIds[1] : $programTypeId;
        }

        if ($regionId) {
            $conditions['Students.region_id'] = $regionId;
        }

        if ($sex && $sex !== 'all') {
            $conditions['Students.gender'] = $sex;
        }

        if ($academicYear && $semester) {
            $conditions['Students.id IN'] = $studentExamStatusesTable->find()
                ->select(['student_id'])
                ->where(['academic_year' => $academicYear, 'semester' => $semester]);
            $courseRegistrationSubquery = $courseRegistrationsTable->find()
                ->select(['student_id'])
                ->where(['academic_year' => $academicYear, 'semester' => $semester]);
            if (!empty($matchingYearIds)) {
                $courseRegistrationSubquery->andWhere(['year_level_id IN' => $matchingYearIds]);
            }
            $conditions['Students.id IN'] = $courseRegistrationSubquery;
        } else {
            $conditions['Students.id IN'] = $studentExamStatusesTable->find()->select(['student_id']);
            if (!empty($matchingYearIds)) {
                $conditions['Students.id IN'] = $courseRegistrationsTable->find()
                    ->select(['student_id'])
                    ->where(['year_level_id IN' => $matchingYearIds]);
            }
        }

        $students = $this->find()
            ->select([
                'full_name',
                'first_name',
                'middle_name',
                'last_name',
                'studentnumber',
                'admissionyear',
                'gender',
                'academicyear',
                'graduated'
            ])
            ->where($conditions)
            ->contain([
                'StudentExamStatuses' => function (Query $q) {
                    return $q->order(['created' => 'DESC']);
                },
                'CourseRegistrations' => function (Query $q) {
                    return $q->limit(1)
                        ->order(['created' => 'DESC'])
                        ->contain([
                            'Sections' => [
                                'YearLevels' => ['fields' => ['id', 'name']],
                                'Departments' => ['fields' => ['id', 'name']],
                                'Colleges' => ['fields' => ['id', 'name']],
                                'Programs' => ['fields' => ['id', 'name']],
                                'ProgramTypes' => ['fields' => ['id', 'name']]
                            ]
                        ]);
                }
            ])
            ->order(['first_name' => 'ASC', 'middle_name' => 'ASC', 'last_name' => 'ASC'])
            ->toArray();

        $attractionRate = [];
        $yearLevelCount = [];

        foreach ($students as $student) {
            $section = null;
            if (!empty($student->course_registrations) && !empty($student->course_registrations[0]->section)) {
                $section = $student->course_registrations[0]->section;
            }

            if (!$section || empty($section->program->name) || empty($section->program_type->name)) {
                continue;
            }

            $yearLevelName = $section->year_level->name ?? '1st';
            if ($yearLevelId && $yearLevelName === $yearLevelId) {
                $yearLevelCount[$yearLevelId] = $yearLevelId;
            } elseif (!$yearLevelId) {
                $yearLevelCount[$yearLevelName] = $yearLevelName;
            }

            // Initialize and increment counts
            $programName = $section->program->name;
            $programTypeName = $section->program_type->name;
            $collegeName = $section->college->name;
            $departmentName = $section->department->name ?? 'Pre Engineering';

            // Department level
            if ($yearLevelName && $departmentName && $collegeName) {
                $deptPath = &$attractionRate[$programName][$programTypeName][$collegeName][$departmentName][$yearLevelName];
                $deptPath['total'] = ($deptPath['total'] ?? 0) + 1;
                $deptPath['female_total'] = $deptPath['female_total'] ?? 0;
                $deptPath['male_total'] = $deptPath['male_total'] ?? 0;
            }

            // College level
            if ($yearLevelName && $collegeName) {
                $collegePath = &$attractionRate[$programName][$programTypeName][$collegeName]['College'][$yearLevelName];
                $collegePath['total'] = ($collegePath['total'] ?? 0) + 1;
                $collegePath['female_total'] = $collegePath['female_total'] ?? 0;
                $collegePath['male_total'] = $collegePath['male_total'] ?? 0;
            }

            // University level
            if ($yearLevelName) {
                $uniPath = &$attractionRate['University'][$programName][$programTypeName][$yearLevelName];
                $uniPath['total'] = ($uniPath['total'] ?? 0) + 1;
                $uniPath['female_total'] = $uniPath['female_total'] ?? 0;
                $uniPath['male_total'] = $uniPath['male_total'] ?? 0;
            }

            // Pre-engineering department
            if (!$yearLevelName && $collegeName) {
                $preEngPath = &$attractionRate[$programName][$programTypeName][$collegeName]['Pre Engineering']['1st'];
                $preEngPath['total'] = ($preEngPath['total'] ?? 0) + 1;
                $preEngPath['female_total'] = $preEngPath['female_total'] ?? 0;
                $preEngPath['male_total'] = $preEngPath['male_total'] ?? 0;
            }

            // Pre-engineering college
            if (!$yearLevelName && $collegeName) {
                $preCollegePath = &$attractionRate[$programName][$programTypeName][$collegeName]['College']['1st'];
                $preCollegePath['total'] = ($preCollegePath['total'] ?? 0) + 1;
                $preCollegePath['female_total'] = $preCollegePath['female_total'] ?? 0;
                $preCollegePath['male_total'] = $preCollegePath['male_total'] ?? 0;
            }

            // Pre-engineering university
            if (!$yearLevelName) {
                $preUniPath = &$attractionRate['University'][$programName][$programTypeName]['1st'];
                $preUniPath['total'] = ($preUniPath['total'] ?? 0) + 1;
                $preUniPath['female_total'] = $preUniPath['female_total'] ?? 0;
                $preUniPath['male_total'] = $preUniPath['male_total'] ?? 0;
            }

            // Increment gender counts for dismissed students
            if (!empty($student->student_exam_statuses) && $student->student_exam_statuses[0]->academic_status_id == 4) {
                $gender = strtolower($student->gender);
                if (!$section->department_id) {
                    $preEngPath[$gender . '_total'] += 1;
                } else {
                    if ($yearLevelName) {
                        $deptPath[$gender . '_total'] += 1;
                    }
                }
                if ($yearLevelName) {
                    $collegePath[$gender . '_total'] += 1;
                    $uniPath[$gender . '_total'] += 1;
                } else {
                    $preCollegePath[$gender . '_total'] += 1;
                    $preUniPath[$gender . '_total'] += 1;
                }
            }
        }

        return [
            'attractionRate' => $attractionRate,
            'YearLevel' => $yearLevelCount
        ];
    }

    public function getStudentListName($admissionYear, $programId, $programTypeId, $departmentId, $yearLevelId = null, $studentNumber = null, $studentName = null)
    {
        $conditions = [
            'program_id' => $programId,
            'department_id' => $departmentId,
            'curriculum_id IS NOT NULL',
            'curriculum_id !=' => 0
        ];

        if ($programTypeId) {
            $conditions['program_type_id'] = $programTypeId;
        }

        if ($admissionYear) {
            $conditions['academicyear'] = $admissionYear;
        }

        if ($studentName) {
            $conditions['full_name LIKE'] = $studentName . '%';
        }

        if ($studentNumber) {
            $conditions = ['studentnumber' => $studentNumber];
        }

        return $this->find()
            ->select([
                'id',
                'curriculum_id',
                'full_name',
                'first_name',
                'middle_name',
                'last_name',
                'studentnumber',
                'admissionyear',
                'gender',
                'academicyear',
                'graduated'
            ])
            ->where($conditions)
            ->order(['first_name' => 'ASC', 'middle_name' => 'ASC', 'last_name' => 'ASC'])
            ->toArray();
    }
    public function regenerateAcademicStatusByBatch($departmentCollegeId, $admissionAcademicYear = null, $statusAcademicYear = null, $semester = null, $allCollegeDept = 0, $pre = 0, $programId = null, $programTypeId = null)
    {
        $academicYearsTable = TableRegistry::getTableLocator()->get('AcademicYears');
        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $courseAddsTable = TableRegistry::getTableLocator()->get('CourseAdds');
        $studentExamStatusesTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');
        $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');
        $examGradeChangesTable = TableRegistry::getTableLocator()->get('ExamGradeChanges');

        $conditions = ['graduated' => 0];

        if ($departmentCollegeId !== 'all' && $pre == 1) {
            $conditions['college_id'] = $departmentCollegeId;
            $conditions['department_id IS NULL'];
        }

        if ($programId) {
            $conditions['program_id'] = $programId;
        }

        if ($programTypeId) {
            $conditions['program_type_id'] = $programTypeId;
        }

        if ($departmentCollegeId !== 'all' && $pre == 0) {
            if ($allCollegeDept == 0) {
                $conditions['department_id'] = $departmentCollegeId;
            } else {
                $conditions['college_id'] = $departmentCollegeId;
            }
        }

        if ($admissionAcademicYear !== 'all') {
            $conditions['academicyear'] = $admissionAcademicYear;
        }

        if ($statusAcademicYear && $semester) {
            $conditions['id IN'] = $courseRegistrationsTable->find()
                ->select(['student_id'])
                ->where(['academic_year' => $statusAcademicYear, 'semester' => $semester]);
        }

        $students = $this->find()
            ->select([
                'id',
                'curriculum_id',
                'full_name',
                'first_name',
                'middle_name',
                'last_name',
                'studentnumber',
                'admissionyear',
                'gender',
                'academicyear',
                'graduated'
            ])
            ->distinct(['id'])
            ->where($conditions)
            ->order(['admissionyear' => 'ASC'])
            ->limit(1000000)
            ->toArray();

        foreach ($students as $student) {
            $regConditions = ['student_id' => $student->id];
            $addConditions = ['student_id' => $student->id];

            if ($statusAcademicYear) {
                $regConditions['academic_year'] = $statusAcademicYear;
                $addConditions['academic_year'] = $statusAcademicYear;
            }
            if ($semester) {
                $regConditions['semester'] = $semester;
                $addConditions['semester'] = $semester;
            }

            $courseRegistrations = $courseRegistrationsTable->find()
                ->where($regConditions)
                ->contain(['PublishedCourses'])
                ->order(['academic_year' => 'ASC', 'semester' => 'ASC'])
                ->toArray();

            $courseAdds = $courseAddsTable->find()
                ->where($addConditions)
                ->contain(['PublishedCourses'])
                ->order(['academic_year' => 'ASC', 'semester' => 'ASC'])
                ->toArray();

            foreach ($courseRegistrations as $registration) {
                $statusExists = $studentExamStatusesTable->find()
                    ->where([
                        'student_id' => $student->id,
                        'academic_year' => $registration->published_course->academic_year,
                        'semester' => $registration->published_course->semester
                    ])
                    ->count();

                if (!$statusExists) {
                    $forwardStatus = $studentExamStatusesTable->find()
                        ->where([
                            'student_id' => $student->id,
                            'academic_year >' => $registration->published_course->academic_year
                        ])
                        ->first();

                    if ($forwardStatus) {
                        $studentExamStatusesTable->deleteAll([
                            'student_id' => $student->id,
                            'academic_year >' => $registration->published_course->academic_year
                        ]);
                    }

                    $statusGenerated = $studentExamStatusesTable->updateAcdamicStatusByPublishedCourse($registration->published_course->id);
                } else {
                    $gradeChange = $examGradesTable->getApprovedGrade($registration->id, 1);
                    if ($gradeChange) {
                        $examGradeChange = $examGradeChangesTable->find()
                            ->where(['exam_grade_id' => $gradeChange['grade_id']])
                            ->first();
                        if ($examGradeChange) {
                            $studentExamStatusesTable->updateAcdamicStatusForGradeChange($examGradeChange->id);
                        }
                    }
                }
            }

            foreach ($courseAdds as $add) {
                $statusExists = $studentExamStatusesTable->find()
                    ->where([
                        'student_id' => $student->id,
                        'academic_year' => $add->published_course->academic_year,
                        'semester' => $add->published_course->semester
                    ])
                    ->count();

                if (!$statusExists) {
                    $forwardStatus = $studentExamStatusesTable->find()
                        ->where([
                            'student_id' => $student->id,
                            'academic_year >' => $add->published_course->academic_year
                        ])
                        ->first();

                    if ($forwardStatus) {
                        $studentExamStatusesTable->deleteAll(['student_id' => $student->id]);
                    }

                    $statusGenerated = $studentExamStatusesTable->updateAcdamicStatusByPublishedCourseOfStudent($add->published_course->id, $student->id);
                } else {
                    $gradeChange = $examGradesTable->getApprovedGrade($add->id, 1);
                    if ($gradeChange) {
                        $examGradeChange = $examGradeChangesTable->find()
                            ->where(['exam_grade_id' => $gradeChange['grade_id']])
                            ->first();
                        if ($examGradeChange) {
                            $studentExamStatusesTable->updateAcdamicStatusForGradeChange($examGradeChange->id);
                        }
                    } else {
                        $studentExamStatusesTable->updateAcdamicStatusForGradeChange($add->id, null);
                    }
                }
            }
        }

        return 'DONE';
    }
    public function updateAcademicStatusByBatch($departmentCollegeId, $admissionAcademicYear = null, $statusAcademicYear = null, $semester = null, $allCollegeDept = 0, $pre = 0, $programId = null, $programTypeId = null)
    {
        $academicYearsTable = TableRegistry::getTableLocator()->get('AcademicYears');
        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $courseAddsTable = TableRegistry::getTableLocator()->get('CourseAdds');
        $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');
        $studentExamStatusesTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');

        $conditions = ['graduated' => 0];

        if ($departmentCollegeId !== 'all' && $pre == 1) {
            $conditions['college_id'] = $departmentCollegeId;
            $conditions['department_id IS NULL'];
        }

        if ($programId) {
            $conditions['program_id'] = $programId;
        }

        if ($programTypeId) {
            $conditions['program_type_id'] = $programTypeId;
        }

        if ($departmentCollegeId !== 'all' && $pre == 0) {
            if ($allCollegeDept == 0) {
                $conditions['department_id'] = $departmentCollegeId;
            } else {
                $conditions['college_id'] = $departmentCollegeId;
            }
        }

        if ($admissionAcademicYear !== 'all') {
            $conditions['academicyear'] = $admissionAcademicYear;
        }

        $students = $this->find()
            ->select([
                'id',
                'curriculum_id',
                'full_name',
                'first_name',
                'middle_name',
                'last_name',
                'studentnumber',
                'admissionyear',
                'gender',
                'academicyear',
                'graduated'
            ])
            ->distinct(['id'])
            ->where($conditions)
            ->order(['admissionyear' => 'ASC'])
            ->limit(10000)
            ->toArray();

        foreach ($students as $student) {
            $regConditions = ['student_id' => $student->id];
            $addConditions = ['student_id' => $student->id];

            if ($statusAcademicYear) {
                $regConditions['academic_year'] = $statusAcademicYear;
                $addConditions['academic_year'] = $statusAcademicYear;
            }
            if ($semester) {
                $regConditions['semester'] = $semester;
                $addConditions['semester'] = $semester;
            }

            $courseRegisteredIds = $courseRegistrationsTable->find()
                ->select(['id'])
                ->where($regConditions)
                ->order(['academic_year' => 'ASC', 'semester' => 'ASC'])
                ->extract('id')
                ->toArray();

            $courseAddedIds = $courseAddsTable->find()
                ->select(['id'])
                ->where($addConditions)
                ->order(['academic_year' => 'ASC', 'semester' => 'ASC'])
                ->extract('id')
                ->toArray();

            foreach ($courseRegisteredIds as $regId) {
                $gradeChange = $examGradesTable->getApprovedGrade($regId, 1);
                if ($gradeChange) {
                    $studentExamStatusesTable->updateAcdamicStatusForGradeChange($gradeChange['grade_id']);
                } else {
                    $studentExamStatusesTable->updateAcdamicStatusForGradeChange($regId, null);
                }
            }

            foreach ($courseAddedIds as $addId) {
                $gradeChange = $examGradesTable->getApprovedGrade($addId, 0);
                if ($gradeChange) {
                    $studentExamStatusesTable->updateAcdamicStatusForGradeChange($gradeChange['grade_id']);
                } else {
                    $studentExamStatusesTable->updateAcdamicStatusForGradeChange($addId, 'add');
                }
            }
        }

        return true;
    }
    public function updateAcademicStatus($startingFrom = 2)
    {
        $examGradeChangesTable = TableRegistry::getTableLocator()->get('ExamGradeChanges');
        $studentExamStatusesTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');

        $recentGradeChangeDateFrom = Time::now()->modify("-$startingFrom days")->toDateTimeString();
        $recentGradeChangeDateTo = Time::now()->toDateTimeString();

        $recentGradeChanges = $examGradeChangesTable->find()
            ->where([
                'registrar_approval' => 1,
                'created >=' => $recentGradeChangeDateFrom,
                'created <=' => $recentGradeChangeDateTo
            ])
            ->contain([
                'ExamGrades' => [
                    'CourseRegistrations',
                    'CourseAdds',
                    'MakeupExams'
                ]
            ])
            ->toArray();

        foreach ($recentGradeChanges as $gradeChange) {
            $studentExamStatusesTable->updateAcdamicStatusForGradeChange($gradeChange->id);
        }

        return true;
    }

    public function updateMissingAcademicStatus($studentId)
    {
        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $studentExamStatusesTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');
        $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');

        $semesters = ['I', 'II', 'III'];
        $acRange = $examGradesTable->getListOfAyAndSemester($studentId);

        if (empty($acRange)) {
            return false;
        }

        foreach ($acRange as $ac) {
            foreach ($semesters as $semester) {
                $registrations = $courseRegistrationsTable->find()
                    ->select(['student_id', 'academic_year', 'semester', 'published_course_id'])
                    ->where([
                        'student_id' => $studentId,
                        'academic_year' => $ac['academic_year'],
                        'semester' => $semester,
                        function (QueryExpression $exp) use ($studentExamStatusesTable, $studentId, $ac, $semester) {
                            return $exp->notExists(
                                $studentExamStatusesTable->find()
                                    ->select([1])
                                    ->where([
                                        'student_id' => $studentId,
                                        'academic_year' => $ac['academic_year'],
                                        'semester' => $semester
                                    ])
                            );
                        }
                    ])
                    ->order(['academic_year' => 'DESC', 'semester' => 'DESC'])
                    ->toArray();

                foreach ($registrations as $registration) {
                    $forwardStatus = $studentExamStatusesTable->find()
                        ->where([
                            'student_id' => $registration->student_id,
                            'academic_year >' => $registration->academic_year
                        ])
                        ->first();

                    if ($forwardStatus) {
                        $studentExamStatusesTable->deleteAll(['student_id' => $forwardStatus->student_id]);
                    }

                    $statusExists = $studentExamStatusesTable->find()
                        ->where([
                            'student_id' => $registration->student_id,
                            'academic_year' => $registration->academic_year,
                            'semester' => $registration->semester
                        ])
                        ->count();

                    if (!$statusExists) {
                        $studentExamStatusesTable->updateAcdamicStatusByStudent(
                            $registration->student_id,
                            $registration->published_course_id
                        );
                    }
                }
            }
        }

        return true;
    }

    public function rankUpdate($academicYear, $departmentId = 0)
    {
        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $studentExamStatusesTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');
        $studentRanksTable = TableRegistry::getTableLocator()->get('StudentRanks');

        $semesters = ['I', 'II', 'III'];
        $rankCategories = ['cgpa', 'sgpa'];

        $conditions = [
            'id IN' => $courseRegistrationsTable->find()
                ->select(['student_id'])
                ->where(['academic_year' => $academicYear])
        ];

        if ($departmentId) {
            $conditions['department_id'] = $departmentId;
        }

        foreach ($rankCategories as $category) {
            foreach ($semesters as $semester) {
                $students = $this->find()
                    ->where(array_merge($conditions, [
                        'id IN' => $courseRegistrationsTable->find()
                            ->select(['student_id'])
                            ->where(['academic_year' => $academicYear, 'semester' => $semester])
                    ]))
                    ->toArray();

                foreach ($students as $student) {
                    $rank = $studentExamStatusesTable->getACSemRank($student->id, $academicYear, $semester, $category);
                    if ($rank) {
                        $existingRank = $studentRanksTable->find()
                            ->where([
                                'student_id' => $student->id,
                                'semester' => $semester,
                                'academicyear' => $academicYear,
                                'category' => $category
                            ])
                            ->first();

                        $rankData = $rank['Rank'];
                        if ($existingRank) {
                            $rankData['id'] = $existingRank->id;
                        }

                        $rankEntity = $studentRanksTable->newEntity($rankData);
                        if (!$existingRank) {
                            $studentRanksTable->create();
                        }
                        $studentRanksTable->save($rankEntity);
                    }
                }
            }
        }

        return true;
    }

    public function getProfileNotBuildListCount($maxNotBuildTime = null, $departmentIds = null, $collegeIds = null, $programIds = null, $programTypeIds = null)
    {
        $contactsTable = TableRegistry::getTableLocator()->get('Contacts');
        $notBuildFor = Time::now()->modify('-' . ($maxNotBuildTime ?? DAYS_BACK_PROFILE) . ' days')->toDateString();

        if (!empty($programIds)) {
            if (is_array($programIds) && in_array(PROGRAM_REMEDIAL, $programIds)) {
                $programIds = array_diff($programIds, [PROGRAM_REMEDIAL]);
            } elseif (!is_array($programIds) && $programIds == PROGRAM_REMEDIAL) {
                return 0;
            }
        }

        if (empty($departmentIds) && empty($collegeIds)) {
            return 0;
        }

        $query = $this->find()
            ->where([
                'program_id IN' => (array)$programIds,
                'program_type_id IN' => (array)$programTypeIds,
                'graduated' => 0,
                'created >=' => $notBuildFor,
                function (QueryExpression $exp) use ($contactsTable) {
                    return $exp->notIn('Students.id', $contactsTable->find()->select(['student_id']));
                }
            ]);

        if (!empty($departmentIds)) {
            $query->andWhere(['department_id IN' => (array)$departmentIds]);
        } elseif (!empty($collegeIds)) {
            $query->andWhere(['department_id IS' => null, 'college_id IN' => (array)$collegeIds]);
        }

        return $query->count();
    }
    public function updateDepartmentTransferFromField()
    {
        $departmentTransfersTable = TableRegistry::getTableLocator()->get('DepartmentTransfers');

        $transfers = $departmentTransfersTable->find()
            ->where(['from_department_id IS' => null])
            ->contain(['Students' => ['fields' => ['id', 'department_id']]])
            ->toArray();

        if (empty($transfers)) {
            return true;
        }

        $entities = array_map(function ($transfer) {
            return [
                'id' => $transfer->id,
                'from_department_id' => $transfer->student->department_id
            ];
        }, $transfers);

        $departmentTransfersTable->saveMany($departmentTransfersTable->newEntities($entities), ['validate' => false]);

        return true;
    }

    public function updateDepartmentInStudentTable()
    {
        $students = $this->find()
            ->where(['department_id IS' => null])
            ->contain(['AcceptedStudents' => ['fields' => ['id', 'department_id']]])
            ->toArray();

        if (empty($students)) {
            return true;
        }

        $entities = array_map(function ($student) {
            return [
                'id' => $student->id,
                'department_id' => $student->accepted_student->department_id
            ];
        }, $students);

        $this->saveMany($this->newEntities($entities), ['validate' => false]);

        return true;
    }

    public function getDistributionStats($academicYear, $programId = null, $programTypeId = null, $departmentId = null, $sex = 'all', $yearLevelId = null, $regionId = null, $freshman = 0, $excludeGraduated = 0, $semester = '')
    {
        $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
        $collegesTable = TableRegistry::getTableLocator()->get('Colleges');
        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $regionsTable = TableRegistry::getTableLocator()->get('Regions');

        $conditions = [];
        $graph = ['data' => [[], []], 'labels' => [], 'series' => ['Male', 'Female']];
        $distributionByDepartmentYearLevel = [];

        if ($regionId) {
            $conditions['Students.region_id'] = $regionId;
        }
        if ($excludeGraduated) {
            $conditions['Students.graduated'] = 0;
        }
        if ($programId) {
            $conditions['Students.program_id IN'] = (array)$programId;
        }
        if ($programTypeId) {
            $conditions['Students.program_type_id IN'] = (array)$programTypeId;
        }

        $deptConditions = ['Departments.active' => 1];
        $collegeIds = [];
        if ($departmentId) {
            $collegeId = explode('~', $departmentId);
            if (count($collegeId) > 1) {
                $deptConditions['Departments.college_id'] = $collegeId[1];
                $collegeIds[$collegeId[1]] = $collegeId[1];
            } else {
                $deptConditions['Departments.id'] = $departmentId;
            }
        } else {
            $collegeIds = $collegesTable->find()
                ->where(['active' => 1])
                ->extract('id')
                ->toArray();
        }

        $departments = $departmentsTable->find()
            ->where($deptConditions)
            ->contain(['Colleges', 'YearLevels'])
            ->toArray();

        if (!$freshman) {
            foreach ($departments as $department) {
                foreach ($department->year_levels as $yearLevel) {
                    if ($yearLevelId && $yearLevel->name !== $yearLevelId) {
                        continue;
                    }

                    $query = $this->find()
                        ->select(['count' => $this->query()->func()->count('DISTINCT Students.studentnumber')])
                        ->innerJoinWith('StudentsSections.Sections', function ($q) use ($department, $yearLevel, $academicYear, $semester) {
                            $sectionConditions = ['Sections.department_id' => $department->id, 'Sections.year_level_id' => $yearLevel->id];
                            if ($academicYear) {
                                $sectionConditions['Sections.academicyear'] = $academicYear;
                            }
                            return $q->where($sectionConditions);
                        })
                        ->innerJoinWith('CourseRegistrations', function ($q) use ($academicYear, $semester) {
                            $regConditions = [];
                            if ($academicYear) {
                                $regConditions['CourseRegistrations.academic_year'] = $academicYear;
                            }
                            if ($semester) {
                                $regConditions['CourseRegistrations.semester'] = $semester;
                            }
                            return $q->where($regConditions);
                        })
                        ->where($conditions);

                    $sexes = ($sex !== 'all') ? [(string)$sex] : ['male', 'female'];
                    foreach ($sexes as $gender) {
                        $genderQuery = clone $query;
                        $count = $genderQuery->andWhere(['Students.gender' => $gender])->first()->count ?? 0;
                        $distributionByDepartmentYearLevel[$department->name][$yearLevel->name][strtolower($gender)] = $count;

                        $graph['labels'][$department->id] = $department->name;
                        $index = strtolower($gender) === 'female' ? 1 : 0;
                        $graph['data'][$index][$department->id] = ($graph['data'][$index][$department->id] ?? 0) + $count;
                    }
                }
            }
        }

        foreach ($collegeIds as $collegeId) {
            $college = $collegesTable->find()->where(['id' => $collegeId])->first();
            if (!$college) {
                continue;
            }

            $query = $this->find()
                ->select(['count' => $this->query()->func()->count('DISTINCT Students.studentnumber')])
                ->innerJoinWith('StudentsSections.Sections', function ($q) use ($collegeId, $academicYear, $semester) {
                    $sectionConditions = ['Sections.college_id' => $collegeId, 'Sections.department_id IS' => null, 'OR' => [
                        'Sections.year_level_id IS' => null,
                        'Sections.year_level_id' => 0,
                        'Sections.year_level_id' => ''
                    ]];
                    if ($academicYear) {
                        $sectionConditions['Sections.academicyear'] = $academicYear;
                    }
                    return $q->where($sectionConditions);
                })
                ->innerJoinWith('CourseRegistrations', function ($q) use ($academicYear, $semester) {
                    $regConditions = [];
                    if ($academicYear) {
                        $regConditions['CourseRegistrations.academic_year'] = $academicYear;
                    }
                    if ($semester) {
                        $regConditions['CourseRegistrations.semester'] = $semester;
                    }
                    return $q->where($regConditions);
                })
                ->where(array_merge($conditions, ['Students.department_id IS' => null]));

            $sexes = ($sex !== 'all') ? [(string)$sex] : ['male', 'female'];
            foreach ($sexes as $gender) {
                $genderQuery = clone $query;
                $count = $genderQuery->andWhere(['Students.gender' => $gender])->first()->count ?? 0;
                $distributionByDepartmentYearLevel[$college->name . ' Freshman']['1st'][strtolower($gender)] = $count;

                $graph['labels'][$college->id] = $college->name . ' Freshman';
                $index = strtolower($gender) === 'female' ? 1 : 0;
                $graph['data'][$index][$college->id] = ($graph['data'][$index][$college->id] ?? 0) + $count;
            }
        }

        return [
            'distributionByDepartmentYearLevel' => $distributionByDepartmentYearLevel,
            'graph' => $graph
        ];
    }
    public function distributionStatsLetterGrade($academicYear, $semester, $programId = null, $programTypeId = null, $departmentId = null, $sex = 'all', $yearLevelId = null, $regionId = null, $freshman = 0)
    {
        if (empty($academicYear) || empty($semester)) {
            return [];
        }

        $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
        $collegesTable = TableRegistry::getTableLocator()->get('Colleges');
        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $publishedCoursesTable = TableRegistry::getTableLocator()->get('PublishedCourses');
        $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');

        $conditions = [];
        if ($regionId) {
            $conditions['Students.region_id'] = $regionId;
        }
        if ($programId) {
            $programIds = explode('~', $programId);
            $conditions['Students.program_id'] = count($programIds) > 1 ? $programIds[1] : $programId;
        }
        if ($programTypeId) {
            $programTypeIds = explode('~', $programTypeId);
            $conditions['Students.program_type_id'] = count($programTypeIds) > 1 ? $programTypeIds[1] : $programTypeId;
        }

        $deptConditions = [];
        $collegeIds = [];
        if ($departmentId) {
            $collegeId = explode('~', $departmentId);
            if (count($collegeId) > 1) {
                $deptConditions['Departments.college_id'] = $collegeId[1];
                $collegeIds[$collegeId[1]] = $collegeId[1];
            } else {
                $deptConditions['Departments.id'] = $departmentId;
            }
        } else {
            $collegeIds = $collegesTable->find()->extract('id')->toArray();
        }

        $departments = $departmentsTable->find()
            ->where($deptConditions)
            ->contain(['Colleges', 'YearLevels' => function ($q) {
                return $q->order(['name' => 'ASC']);
            }])
            ->toArray();

        $distributionLetterGrade = [];

        if (!$freshman) {
            foreach ($departments as $department) {
                foreach ($department->year_levels as $yearLevel) {
                    if ($yearLevelId && $yearLevel->name !== $yearLevelId) {
                        continue;
                    }

                    $publishedCourseIds = $publishedCoursesTable->find()
                        ->select(['id'])
                        ->where([
                            'academic_year' => $academicYear,
                            'semester' => $semester,
                            'department_id' => $department->id,
                            'year_level_id' => $yearLevel->id
                        ])
                        ->extract('id')
                        ->toArray();

                    $sexes = ($sex !== 'all') ? [(string)$sex] : ['male', 'female'];
                    foreach ($sexes as $gender) {
                        $query = $examGradesTable->find()
                            ->select([
                                'grade',
                                'published_course_id' => 'CourseRegistrations.published_course_id',
                                'gcount' => $this->query()->func()->count('ExamGrades.grade')
                            ])
                            ->innerJoinWith('CourseRegistrations.Students', function ($q) use ($conditions, $gender) {
                                return $q->where(array_merge($conditions, ['Students.gender' => $gender]));
                            })
                            ->where([
                                'ExamGrades.course_registration_id = CourseRegistrations.id',
                                'ExamGrades.registrar_approval' => 1,
                                'ExamGrades.department_approval' => 1,
                                'CourseRegistrations.published_course_id IN' => $publishedCourseIds,
                                'ExamGrades.grade IS NOT NULL'
                            ])
                            ->group(['ExamGrades.grade', 'CourseRegistrations.published_course_id']);

                        foreach ($query as $result) {
                            $publishedCourse = $publishedCoursesTable->find()
                                ->where(['id' => $result->published_course_id])
                                ->contain(['Courses'])
                                ->first();
                            if ($publishedCourse && $result->grade) {
                                $courseKey = $publishedCourse->course->course_title . ' ' . $publishedCourse->course->course_code;
                                $distributionLetterGrade[$department->name][$courseKey][strtolower($gender)][$result->grade] =
                                    ($distributionLetterGrade[$department->name][$courseKey][strtolower($gender)][$result->grade] ?? 0) + $result->gcount;
                            }
                        }
                    }
                }
            }
        }

        foreach ($collegeIds as $collegeId) {
            $college = $collegesTable->find()->where(['id' => $collegeId])->first();
            if (!$college) {
                continue;
            }

            $publishedCourseIds = $publishedCoursesTable->find()
                ->select(['id'])
                ->where([
                    'academic_year' => $academicYear,
                    'semester' => $semester,
                    'college_id' => $collegeId,
                    'department_id IS' => null,
                    'OR' => ['year_level_id IS' => null, 'year_level_id' => 0]
                ])
                ->extract('id')
                ->toArray();

            $sexes = ($sex !== 'all') ? [(string)$sex] : ['male', 'female'];
            foreach ($sexes as $gender) {
                $query = $examGradesTable->find()
                    ->select([
                        'grade',
                        'published_course_id' => 'CourseRegistrations.published_course_id',
                        'gcount' => $this->query()->func()->count('ExamGrades.grade')
                    ])
                    ->innerJoinWith('CourseRegistrations.Students', function ($q) use ($conditions, $gender) {
                        return $q->where(array_merge($conditions, ['Students.gender' => $gender, 'Students.department_id IS' => null]));
                    })
                    ->where([
                        'ExamGrades.course_registration_id = CourseRegistrations.id',
                        'ExamGrades.registrar_approval' => 1,
                        'ExamGrades.department_approval' => 1,
                        'CourseRegistrations.published_course_id IN' => $publishedCourseIds,
                        'ExamGrades.grade IS NOT NULL'
                    ])
                    ->group(['ExamGrades.grade', 'CourseRegistrations.published_course_id']);

                foreach ($query as $result) {
                    $publishedCourse = $publishedCoursesTable->find()
                        ->where(['id' => $result->published_course_id])
                        ->contain(['Courses'])
                        ->first();
                    if ($publishedCourse && $result->grade) {
                        $courseKey = $publishedCourse->course->course_title . ' ' . $publishedCourse->course->course_code;
                        $distributionLetterGrade[$college->name . ' Freshman'][$courseKey][strtolower($gender)][$result->grade] =
                            ($distributionLetterGrade[$college->name . ' Freshman'][$courseKey][strtolower($gender)][$result->grade] ?? 0) + $result->gcount;
                    }
                }
            }
        }

        return ['distributionLetterGrade' => $distributionLetterGrade];
    }

    public function listStudentByLetterGrade($academicYear = '', $semester = '', $programId = null, $programTypeId = null, $departmentId = null, $sex = 'all', $yearLevelId = null, $regionId = null, $grade, $freshman = 0)
    {
        if (empty($academicYear) || empty($semester)) {
            return [];
        }

        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $courseAddsTable = TableRegistry::getTableLocator()->get('CourseAdds');
        $publishedCoursesTable = TableRegistry::getTableLocator()->get('PublishedCourses');
        $yearLevelsTable = TableRegistry::getTableLocator()->get('YearLevels');
        $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
        $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');

        $publishedConditions = ['academic_year' => $academicYear, 'semester' => $semester];
        $studentConditions = ['id IS NOT NULL'];

        if ($programTypeId) {
            $publishedConditions['program_type_id'] = $programTypeId;
            $studentConditions['program_type_id'] = $programTypeId;
        }
        if ($programId) {
            $publishedConditions['program_id'] = $programId;
            $studentConditions['program_id'] = $programId;
        }
        if ($regionId) {
            $studentConditions['region_id'] = $regionId;
        }
        if ($sex !== 'all') {
            $studentConditions['gender'] = $sex;
        }

        $collegeId = null;
        if (!$freshman) {
            if ($departmentId) {
                $collegeIds = explode('~', $departmentId);
                if (count($collegeIds) > 1) {
                    $collegeId = $collegeIds[1];
                    $deptIds = $departmentsTable->find()
                        ->where(['college_id' => $collegeId, 'active' => 1])
                        ->extract('id')
                        ->toArray();
                    $publishedConditions['department_id IN'] = $deptIds;
                    $studentConditions['department_id IN'] = $deptIds;
                } else {
                    $publishedConditions['department_id'] = $departmentId;
                    $studentConditions['department_id'] = $departmentId;
                }
            }
        } else {
            $collegeIds = explode('~', $departmentId);
            if (!empty($collegeIds[1])) {
                $publishedConditions['department_id IS'] = null;
                $publishedConditions['college_id'] = $collegeIds[1];
                $studentConditions['department_id IS'] = null;
                $studentConditions['college_id'] = $collegeIds[1];
            }
        }

        if ($yearLevelId) {
            $yearLevelConditions = ['name' => $yearLevelId];
            if ($collegeId) {
                $yearLevelConditions['department_id IN'] = $departmentsTable->find()
                    ->where(['college_id' => $collegeId])
                    ->extract('id')
                    ->toArray();
            } elseif ($departmentId && !is_array($departmentId)) {
                $yearLevelConditions['department_id'] = $departmentId;
            }
            $yearLevelIds = $yearLevelsTable->find()
                ->where($yearLevelConditions)
                ->extract('id')
                ->toArray();
            if ($yearLevelIds) {
                $publishedConditions['year_level_id IN'] = $yearLevelIds;
            } elseif (!$freshman) {
                $publishedConditions['OR'] = ['year_level_id IS' => null, 'year_level_id' => 0, 'year_level_id' => ''];
            }
        }

        $publishedCourseIds = $publishedCoursesTable->find()
            ->select(['id'])
            ->where($publishedConditions)
            ->extract('id')
            ->toArray();

        $registrations = $courseRegistrationsTable->find()
            ->where([
                'academic_year' => $academicYear,
                'semester' => $semester,
                'published_course_id IN' => $publishedCourseIds,
                'student_id IN' => $this->find()->select(['id'])->where($studentConditions),
                function (QueryExpression $exp) use ($examGradesTable, $grade) {
                    return $exp->exists(
                        $examGradesTable->find()
                            ->select([1])
                            ->where([
                                'course_registration_id = CourseRegistrations.id',
                                'grade' => $grade,
                                'registrar_approval' => 1,
                                'department_approval' => 1
                            ])
                    );
                }
            ])
            ->contain([
                'Students',
                'ExamGrades',
                'PublishedCourses' => [
                    'Sections',
                    'YearLevels',
                    'Programs',
                    'ProgramTypes',
                    'Courses',
                    'Departments',
                    'CourseInstructorAssignments' => function ($q) {
                        return $q->where(['isprimary' => 1])
                            ->order(['isprimary' => 'DESC'])
                            ->contain([
                                'Staffs' => [
                                    'Departments',
                                    'Titles' => ['fields' => ['id', 'title']],
                                    'Positions' => ['fields' => ['id', 'position']]
                                ]
                            ]);
                    }
                ]
            ])
            ->toArray();

        $adds = $courseAddsTable->find()
            ->where([
                'academic_year' => $academicYear,
                'semester' => $semester,
                'published_course_id IN' => $publishedCourseIds,
                'student_id IN' => $this->find()->select(['id'])->where($studentConditions),
                function (QueryExpression $exp) use ($examGradesTable, $grade) {
                    return $exp->exists(
                        $examGradesTable->find()
                            ->select([1])
                            ->where([
                                'course_add_id = CourseAdds.id',
                                'grade' => $grade,
                                'registrar_approval' => 1,
                                'department_approval' => 1
                            ])
                    );
                }
            ])
            ->contain([
                'Students',
                'ExamGrades',
                'PublishedCourses' => [
                    'Sections',
                    'YearLevels',
                    'Programs',
                    'ProgramTypes',
                    'Courses',
                    'Departments',
                    'CourseInstructorAssignments' => function ($q) {
                        return $q->where(['isprimary' => 1])
                            ->order(['isprimary' => 'DESC'])
                            ->contain([
                                'Staffs' => [
                                    'Departments',
                                    'Titles' => ['fields' => ['id', 'title']],
                                    'Positions' => ['fields' => ['id', 'position']]
                                ]
                            ]);
                    }
                ]
            ])
            ->toArray();

        $organizedCourses = [];
        $courses = array_merge($registrations, $adds);

        foreach ($courses as $course) {
            $isRegistration = isset($course->course_registration_id);
            $gradeData = $examGradesTable->getApprovedGrade(
                $isRegistration ? $course->id : $course->id,
                $isRegistration ? 1 : 0
            );

            if ($gradeData['grade'] !== $grade) {
                continue;
            }

            $yearLevelName = $course->published_course->year_level->name ?? (
            $course->published_course->program->id == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st'
            );

            $instructor = 'Not Assigned';
            if (!empty($course->published_course->course_instructor_assignments[0]->staff->id)) {
                $staff = $course->published_course->course_instructor_assignments[0]->staff;
                $instructor = sprintf(
                    '%s. %s (%s)',
                    $staff->title->title,
                    $staff->full_name,
                    $staff->position->position
                );
            }

            $key = sprintf(
                '%s~%s~%s~%s (%s)~%s~%s',
                $course->published_course->program->name,
                $course->published_course->program_type->name,
                $course->published_course->section->name,
                $course->published_course->course->course_title,
                $course->published_course->course->course_code,
                $yearLevelName,
                $instructor
            );

            $organizedCourses[$key]['studentList'][] = $course->student->toArray();
        }

        return $organizedCourses;
    }
    public function getStudentIdsNotRegisteredPublishedCourse($publishedCourseId)
    {
        $publishedCoursesTable = TableRegistry::getTableLocator()->get('PublishedCourses');
        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $studentsSectionsTable = TableRegistry::getTableLocator()->get('StudentsSections');

        $publishedDetails = $publishedCoursesTable->find()
            ->where(['id' => $publishedCourseId])
            ->first();

        if (!$publishedDetails) {
            return [];
        }

        $registeredStudentIds = $courseRegistrationsTable->find()
            ->select(['student_id'])
            ->where(['published_course_id' => $publishedCourseId])
            ->extract('student_id')
            ->toArray();

        $sectionStudentIds = $studentsSectionsTable->getStudentsIdsInSection($publishedDetails->section_id);

        $sectionStudentIds = array_filter($sectionStudentIds, function ($sid) {
            return !$this->find()->where(['id' => $sid, 'graduated' => 1])->count();
        });

        return array_diff($sectionStudentIds, $registeredStudentIds);
    }

    public function getStudentNotRegisteredPublishedCourse($publishedCourseId)
    {
        $publishedCoursesTable = TableRegistry::getTableLocator()->get('PublishedCourses');
        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $studentsSectionsTable = TableRegistry::getTableLocator()->get('StudentsSections');

        $publishedDetails = $publishedCoursesTable->find()
            ->where(['id' => $publishedCourseId])
            ->first();

        if (!$publishedDetails) {
            return [];
        }

        $registeredStudentIds = $courseRegistrationsTable->find()
            ->select(['student_id'])
            ->where(['published_course_id' => $publishedCourseId])
            ->extract('student_id')
            ->toArray();

        $sectionStudentIds = $studentsSectionsTable->getStudentsIdsInSection($publishedDetails->section_id);

        $sectionStudentIds = array_filter($sectionStudentIds, function ($sid) {
            return !$this->find()->where(['id' => $sid, 'graduated' => 1])->count();
        });

        $notRegisteredStudentIds = array_diff($sectionStudentIds, $registeredStudentIds);

        return $this->find()
            ->where(['id IN' => $notRegisteredStudentIds, 'graduated' => 0])
            ->contain(['Departments', 'Colleges', 'Programs', 'ProgramTypes'])
            ->toArray();
    }

    public function cancelStudentFxAutomaticallyConvertedChange($academicYear, $semester, $programId, $programTypeId, $departmentId, $yearLevelId = 'All')
    {
        if (empty($academicYear) || empty($semester)) {
            return [];
        }

        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $courseAddsTable = TableRegistry::getTableLocator()->get('CourseAdds');
        $publishedCoursesTable = TableRegistry::getTableLocator()->get('PublishedCourses');
        $yearLevelsTable = TableRegistry::getTableLocator()->get('YearLevels');
        $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
        $examGradesTable = TableRegistry::getTableLocator()->get('ExamGrades');
        $examGradeChangesTable = TableRegistry::getTableLocator()->get('ExamGradeChanges');
        $senateListsTable = TableRegistry::getTableLocator()->get('SenateLists');

        $publishedConditions = ['academic_year' => $academicYear, 'semester' => $semester];
        $studentConditions = ['id IS NOT NULL'];

        if ($programTypeId) {
            $publishedConditions['program_type_id'] = $programTypeId;
            $studentConditions['program_type_id'] = $programTypeId;
        }
        if ($programId) {
            $publishedConditions['program_id'] = $programId;
            $studentConditions['program_id'] = $programId;
        }

        $collegeId = null;
        if ($departmentId) {
            $collegeIds = explode('~', $departmentId);
            if (count($collegeIds) > 1) {
                $collegeId = $collegeIds[1];
                $deptIds = $departmentsTable->find()
                    ->where(['college_id' => $collegeId])
                    ->extract('id')
                    ->toArray();
                $publishedConditions['department_id IN'] = $deptIds;
                $studentConditions['department_id IN'] = $deptIds;
            } else {
                $publishedConditions['department_id'] = $departmentId;
                $studentConditions['department_id'] = $departmentId;
            }
        }

        if ($yearLevelId && $yearLevelId !== 'All') {
            $yearLevelConditions = ['name' => $yearLevelId];
            if ($collegeId) {
                $yearLevelConditions['department_id IN'] = $departmentsTable->find()
                    ->where(['college_id' => $collegeId])
                    ->extract('id')
                    ->toArray();
            } elseif ($departmentId) {
                $yearLevelConditions['department_id'] = $departmentId;
            }
            $yearLevelIds = $yearLevelsTable->find()
                ->where($yearLevelConditions)
                ->extract('id')
                ->toArray();
            if ($yearLevelIds) {
                $publishedConditions['year_level_id IN'] = $yearLevelIds;
            }
        } elseif ($yearLevelId === 'All' && ($collegeId || $departmentId)) {
            $yearLevelConditions = [];
            if ($collegeId) {
                $yearLevelConditions['department_id IN'] = $departmentsTable->find()
                    ->where(['college_id' => $collegeId])
                    ->extract('id')
                    ->toArray();
            } elseif ($departmentId) {
                $yearLevelConditions['department_id'] = $departmentId;
            }
            $yearLevelIds = $yearLevelsTable->find()
                ->where($yearLevelConditions)
                ->extract('id')
                ->toArray();
            if ($yearLevelIds) {
                $publishedConditions['year_level_id IN'] = $yearLevelIds;
            }
        }

        $publishedCourseIds = $publishedCoursesTable->find()
            ->select(['id'])
            ->where($publishedConditions)
            ->extract('id')
            ->toArray();

        $registrations = $courseRegistrationsTable->find()
            ->where([
                'academic_year' => $academicYear,
                'semester' => $semester,
                'published_course_id IN' => $publishedCourseIds,
                'student_id IN' => $this->find()->select(['id'])->where($studentConditions),
                'student_id NOT IN' => $senateListsTable->find()->select(['student_id']),
                function (QueryExpression $exp) use ($examGradesTable) {
                    return $exp->exists(
                        $examGradesTable->find()
                            ->select([1])
                            ->where([
                                'course_registration_id = CourseRegistrations.id',
                                'grade' => 'FX',
                                'registrar_approval' => 1,
                                'department_approval' => 1
                            ])
                    );
                }
            ])
            ->contain([
                'Students',
                'ExamGrades',
                'PublishedCourses' => ['Sections', 'YearLevels', 'Programs', 'ProgramTypes', 'Courses', 'Departments']
            ])
            ->toArray();

        $adds = $courseAddsTable->find()
            ->where([
                'academic_year' => $academicYear,
                'semester' => $semester,
                'published_course_id IN' => $publishedCourseIds,
                'student_id IN' => $this->find()->select(['id'])->where($studentConditions),
                'student_id NOT IN' => $senateListsTable->find()->select(['student_id']),
                function (QueryExpression $exp) use ($examGradesTable) {
                    return $exp->exists(
                        $examGradesTable->find()
                            ->select([1])
                            ->where([
                                'course_add_id = CourseAdds.id',
                                'grade' => 'FX',
                                'registrar_approval' => 1,
                                'department_approval' => 1
                            ])
                    );
                }
            ])
            ->contain([
                'Students',
                'ExamGrades',
                'PublishedCourses' => ['Sections', 'YearLevels', 'Programs', 'ProgramTypes', 'Courses', 'Departments']
            ])
            ->toArray();

        $courses = array_merge($registrations, $adds);

        foreach ($courses as $course) {
            $isRegistration = isset($course->course_registration_id);
            $gradeData = $examGradesTable->getApprovedNotChangedGrade(
                $isRegistration ? $course->id : $course->id,
                $isRegistration ? 1 : 0
            );

            if (strcasecmp($gradeData['grade'], 'FX') !== 0) {
                continue;
            }

            foreach ($course->exam_grades as $examGrade) {
                $gradeChange = $examGradeChangesTable->find()
                    ->where(['exam_grade_id' => $examGrade->id, 'auto_ng_conversion' => 1])
                    ->first();
                if ($gradeChange) {
                    $examGradeChangesTable->delete($gradeChange);
                }
            }
        }

        return true;
    }
    public function getDistributionStatsOfRegion($academicYear, $programId = null, $programTypeId = null, $departmentId = null, $sex = 'all', $yearLevelId = null, $regionId = null, $freshman = 0)
    {
        $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
        $collegesTable = TableRegistry::getTableLocator()->get('Colleges');
        $regionsTable = TableRegistry::getTableLocator()->get('Regions');

        $conditions = [];
        $graph = ['data' => [], 'series' => [], 'labels' => []];
        $distributionByRegionYearLevel = [];

        if ($regionId) {
            $conditions['Students.region_id'] = $regionId;
        }
        if ($programId) {
            $programIds = explode('~', $programId);
            $conditions['Students.program_id'] = count($programIds) > 1 ? $programIds[1] : $programId;
        }
        if ($programTypeId) {
            $programTypeIds = explode('~', $programTypeId);
            $conditions['Students.program_type_id'] = count($programTypeIds) > 1 ? $programTypeIds[1] : $programTypeId;
        }
        if ($academicYear) {
            $conditions['Sections.academicyear'] = $academicYear;
        }

        $regions = $regionsTable->find()->select(['id', 'name'])->toArray();
        $regionMap = array_column($regions, 'name', 'id');

        $deptConditions = [];
        if ($departmentId) {
            $collegeId = explode('~', $departmentId);
            if (count($collegeId) > 1) {
                $deptConditions['Departments.college_id'] = $collegeId[1];
            } else {
                $deptConditions['Departments.id'] = $departmentId;
            }
        }

        $departments = $departmentsTable->find()
            ->where($deptConditions)
            ->contain(['Colleges', 'YearLevels' => function ($q) {
                return $q->order(['name' => 'ASC']);
            }])
            ->toArray();

        if (!$freshman) {
            foreach ($departments as $department) {
                foreach ($department->year_levels as $yearLevel) {
                    if ($yearLevelId && $yearLevel->name !== $yearLevelId) {
                        continue;
                    }

                    $sexes = ($sex !== 'all') ? [(string)$sex] : ['male', 'female'];
                    foreach ($sexes as $gender) {
                        $query = $this->find()
                            ->select([
                                'count' => $this->query()->func()->count('DISTINCT Students.studentnumber'),
                                'region_id'
                            ])
                            ->innerJoinWith('StudentsSections.Sections', function ($q) use ($department, $yearLevel, $academicYear) {
                                return $q->where([
                                    'Sections.department_id' => $department->id,
                                    'Sections.year_level_id' => $yearLevel->id,
                                    'Sections.academicyear' => $academicYear
                                ]);
                            })
                            ->where(array_merge($conditions, ['Students.gender' => $gender]))
                            ->group(['Students.region_id'])
                            ->order(['Students.region_id' => 'ASC']);

                        foreach ($query as $result) {
                            if ($result->region_id && isset($regionMap[$result->region_id])) {
                                $distributionByRegionYearLevel[$department->name][$regionMap[$result->region_id]][strtolower($gender)][$yearLevel->name] = $result->count;
                                $graph['series'][$result->region_id] = $regionMap[$result->region_id];
                                $graph['labels'][$department->id] = $department->name;
                                $graph['data'][$result->region_id][$department->id] = ($graph['data'][$result->region_id][$department->id] ?? 0) + $result->count;
                            }
                        }
                    }
                }
            }
        } else {
            $collegeId = explode('~', $departmentId)[1] ?? null;
            $colleges = $collegeId
                ? $collegesTable->find()->where(['id' => $collegeId])->toArray()
                : $collegesTable->find()->toArray();

            foreach ($colleges as $college) {
                $sexes = ($sex !== 'all') ? [(string)$sex] : ['male', 'female'];
                foreach ($sexes as $gender) {
                    $query = $this->find()
                        ->select([
                            'count' => $this->query()->func()->count('DISTINCT Students.studentnumber'),
                            'region_id'
                        ])
                        ->innerJoinWith('StudentsSections.Sections', function ($q) use ($college, $academicYear) {
                            return $q->where([
                                'Sections.college_id' => $college->id,
                                'Sections.department_id IS' => null,
                                'OR' => ['Sections.year_level_id IS' => null, 'Sections.year_level_id' => 0],
                                'Sections.academicyear' => $academicYear
                            ]);
                        })
                        ->where(array_merge($conditions, ['Students.gender' => $gender]))
                        ->group(['Students.region_id'])
                        ->order(['Students.region_id' => 'ASC']);

                    foreach ($query as $result) {
                        if ($result->region_id && isset($regionMap[$result->region_id])) {
                            $distributionByRegionYearLevel[$college->name][$regionMap[$result->region_id]][strtolower($gender)]['1st'] = $result->count;
                            $graph['series'][$result->region_id] = $regionMap[$result->region_id];
                            $graph['labels'][$college->id] = $college->name;
                            $graph['data'][$result->region_id][$college->id] = ($graph['data'][$result->region_id][$college->id] ?? 0) + $result->count;
                        }
                    }
                }
            }
        }

        return [
            'distributionByRegionYearLevel' => $distributionByRegionYearLevel,
            'graph' => $graph
        ];
    }
    public function findAttrationRate($academicYear, $semester, $programId = null, $programTypeId = null, $departmentId = null, $yearLevelId = null, $regionId = null, $sex = 'all', $freshman = 0)
    {
        $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
        $collegesTable = TableRegistry::getTableLocator()->get('Colleges');
        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $studentExamStatusesTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');
        $programsTable = TableRegistry::getTableLocator()->get('Programs');
        $programTypesTable = TableRegistry::getTableLocator()->get('ProgramTypes');
        $publishedCoursesTable= TableRegistry::getTableLocator()->get('PublishedCourses');

        $conditions = ['StudentExamStatuses.academic_status_id' => 4];
        $registerConditions = ['id IS NOT NULL'];

        if ($regionId) {
            $conditions['Students.region_id'] = $regionId;
            $registerConditions['region_id'] = $regionId;
        }
        if ($programId) {
            $programIds = explode('~', $programId);
            $conditions['Students.program_id'] = count($programIds) > 1 ? $programIds[1] : $programId;
            $registerConditions['program_id'] = count($programIds) > 1 ? $programIds[1] : $programId;
        }
        if ($programTypeId) {
            $programTypeIds = explode('~', $programTypeId);
            $conditions['Students.program_type_id'] = count($programTypeIds) > 1 ? $programTypeIds[1] : $programTypeId;
            $registerConditions['program_type_id'] = count($programTypeIds) > 1 ? $programTypeIds[1] : $programTypeId;
        }
        if ($academicYear) {
            $conditions['StudentExamStatuses.academic_year'] = $academicYear;
        }
        if ($semester) {
            $conditions['StudentExamStatuses.semester'] = $semester;
        }
        if ($sex !== 'all') {
            $conditions['Students.gender'] = $sex;
        }

        $deptConditions = [];
        $collegeIds = [];
        if ($departmentId) {
            $collegeId = explode('~', $departmentId);
            if (count($collegeId) > 1) {
                $deptConditions['Departments.college_id'] = $collegeId[1];
                $collegeIds[$collegeId[1]] = $collegeId[1];
            } else {
                $deptConditions['Departments.id'] = $departmentId;
            }
        } else {
            $collegeIds = $collegesTable->find()->extract('id')->toArray();
        }

        $departments = $departmentsTable->find()
            ->where($deptConditions)
            ->contain(['Colleges', 'YearLevels' => function ($q) {
                return $q->order(['name' => 'ASC']);
            }])
            ->toArray();

        $programs = $programsTable->find()->select(['id', 'name'])->toArray();
        $programMap = array_column($programs, 'name', 'id');
        $programTypes = $programTypesTable->find()->select(['id', 'name'])->toArray();
        $programTypeMap = array_column($programTypes, 'name', 'id');

        $attritionRateByYearLevel = [];

        if (!$freshman) {
            foreach ($departments as $department) {
                $yearLevels = $yearLevelId
                    ? array_filter($department->year_levels, fn($yl) => strcasecmp($yl->name, $yearLevelId) === 0)
                    : $department->year_levels;

                foreach ($yearLevels as $yearLevel) {
                    $registeredStudentIds = $courseRegistrationsTable->find()
                        ->select(['student_id'])
                        ->where([
                            'year_level_id' => $yearLevel->id,
                            'semester' => $semester,
                            'academic_year' => $academicYear,
                            'student_id IN' => $this->find()->select(['id'])->where($registerConditions),
                            'published_course_id IN' => $publishedCoursesTable->find()
                                ->select(['id'])
                                ->where(['year_level_id' => $yearLevel->id, 'department_id' => $department->id])
                        ])
                        ->distinct(['student_id'])
                        ->extract('student_id')
                        ->toArray();

                    $totalRegistered = count($registeredStudentIds);

                    if ($totalRegistered > 0) {
                        $query = $this->find()
                            ->select([
                                'count' => $this->query()->func()->count('DISTINCT Students.studentnumber'),
                                'gender',
                                'program_id',
                                'program_type_id'
                            ])
                            ->innerJoinWith('StudentExamStatuses', function ($q) use ($conditions) {
                                return $q->where($conditions);
                            })
                            ->where(['Students.id IN' => $registeredStudentIds, 'Students.department_id' => $department->id])
                            ->group(['Students.gender', 'Students.program_id']);

                        foreach ($query as $result) {
                            $programKey = $programMap[$result->program_id] . '~' . $programTypeMap[$result->program_type_id];
                            $attritionRateByYearLevel[$programKey][$department->college->name][$department->name][$yearLevel->name][strtolower($result->gender)] = $result->count;
                            $attritionRateByYearLevel[$programKey][$department->college->name][$department->name][$yearLevel->name]['total'] = $totalRegistered;
                        }
                    }
                }
            }
        } else {
            foreach ($collegeIds as $collegeId) {
                $registeredStudentIds = $courseRegistrationsTable->find()
                    ->select(['student_id'])
                    ->where([
                        'OR' => ['year_level_id IS' => null, 'year_level_id' => 0],
                        'semester' => $semester,
                        'academic_year' => $academicYear,
                        'student_id IN' => $this->find()->select(['id'])->where($registerConditions),
                        'published_course_id IN' => $publishedCoursesTable->find()
                            ->select(['id'])
                            ->where([
                                'OR' => ['year_level_id IS' => null, 'year_level_id' => 0, 'year_level_id' => ''],
                                'OR' => ['department_id IS' => null, 'department_id' => 0, 'department_id' => ''],
                                'college_id' => $collegeId
                            ])
                    ])
                    ->distinct(['student_id'])
                    ->extract('student_id')
                    ->toArray();

                $totalRegistered = count($registeredStudentIds);

                if ($totalRegistered > 0) {
                    $query = $this->find()
                        ->select([
                            'count' => $this->query()->func()->count('DISTINCT Students.studentnumber'),
                            'program_id',
                            'program_type_id',
                            'college_id',
                            'gender'
                        ])
                        ->innerJoinWith('StudentExamStatuses', function ($q) use ($conditions) {
                            return $q->where($conditions);
                        })
                        ->where(['Students.id IN' => $registeredStudentIds])
                        ->group(['Students.gender']);

                    foreach ($query as $result) {
                        $college = $collegesTable->find()->where(['id' => $result->college_id])->first();
                        if ($college) {
                            $programKey = $programMap[$result->program_id] . '~' . $programTypeMap[$result->program_type_id];
                            $attritionRateByYearLevel[$programKey][$college->name]['Freshman']['1st'][strtolower($result->gender)] = $result->count;
                            $attritionRateByYearLevel[$programKey][$college->name]['Freshman']['1st']['total'] = $totalRegistered;
                        }
                    }
                }
            }
        }

        return $attritionRateByYearLevel;
    }
    public function getDistributionStatsOfStatus($academicYear, $semester, $programId = null, $programTypeId = null, $departmentId = null, $sex = 'all', $yearLevelId = null, $regionId = null, $freshman = 0)
    {
        $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
        $collegesTable = TableRegistry::getTableLocator()->get('Colleges');
        $academicStatusesTable = TableRegistry::getTableLocator()->get('AcademicStatuses');

        $conditions = [];
        $graph = ['data' => [], 'series' => [], 'labels' => []];
        $distributionByStatusYearLevel = [];

        if ($regionId) {
            $conditions['Students.region_id'] = $regionId;
        }
        if ($programId) {
            $programIds = explode('~', $programId);
            $conditions['Students.program_id'] = count($programIds) > 1 ? $programIds[1] : $programId;
        }
        if ($programTypeId) {
            $programTypeIds = explode('~', $programTypeId);
            $conditions['Students.program_type_id'] = count($programTypeIds) > 1 ? $programTypeIds[1] : $programTypeId;
        }
        if ($academicYear) {
            $conditions['Sections.academicyear'] = $academicYear;
            $conditions['StudentExamStatuses.academic_year'] = $academicYear;
        }
        if ($semester) {
            $conditions['StudentExamStatuses.semester'] = $semester;
        }

        $academicStatuses = $academicStatusesTable->find()->select(['id', 'name'])->toArray();
        $statusMap = array_column($academicStatuses, 'name', 'id');

        $deptConditions = [];
        if ($departmentId) {
            $collegeId = explode('~', $departmentId);
            if (count($collegeId) > 1) {
                $deptConditions['Departments.college_id'] = $collegeId[1];
            } else {
                $deptConditions['Departments.id'] = $departmentId;
            }
        }

        $departments = $departmentsTable->find()
            ->where($deptConditions)
            ->contain(['Colleges', 'YearLevels' => function ($q) {
                return $q->order(['name' => 'ASC']);
            }])
            ->toArray();

        if (!$freshman) {
            foreach ($departments as $department) {
                foreach ($department->year_levels as $yearLevel) {
                    if ($yearLevelId && $yearLevel->name !== $yearLevelId) {
                        continue;
                    }

                    $sexes = ($sex !== 'all') ? [(string)$sex] : ['male', 'female'];
                    foreach ($sexes as $gender) {
                        $query = $this->find()
                            ->select([
                                'count' => $this->query()->func()->count('DISTINCT Students.studentnumber'),
                                'academic_status_id' => 'StudentExamStatuses.academic_status_id'
                            ])
                            ->innerJoinWith('StudentsSections.Sections', function ($q) use ($department, $yearLevel, $academicYear) {
                                return $q->where([
                                    'Sections.department_id' => $department->id,
                                    'Sections.year_level_id' => $yearLevel->id,
                                    'Sections.academicyear' => $academicYear
                                ]);
                            })
                            ->innerJoinWith('StudentExamStatuses')
                            ->where(array_merge($conditions, ['Students.gender' => $gender]))
                            ->group(['StudentExamStatuses.academic_status_id']);

                        foreach ($query as $result) {
                            if ($result->academic_status_id && isset($statusMap[$result->academic_status_id])) {
                                $distributionByStatusYearLevel[$department->name][$statusMap[$result->academic_status_id]][strtolower($gender)][$yearLevel->name] = $result->count;
                                $graph['series'][$result->academic_status_id] = $statusMap[$result->academic_status_id];
                                $graph['labels'][$department->id] = $department->name;
                                $graph['data'][$result->academic_status_id][$department->id] = ($graph['data'][$result->academic_status_id][$department->id] ?? 0) + $result->count;
                            }
                        }
                    }
                }
            }
        } else {
            $collegeId = explode('~', $departmentId)[1] ?? null;
            $colleges = $collegeId
                ? $collegesTable->find()->where(['id' => $collegeId])->toArray()
                : $collegesTable->find()->toArray();

            foreach ($colleges as $college) {
                $sexes = ($sex !== 'all') ? [(string)$sex] : ['male', 'female'];
                foreach ($sexes as $gender) {
                    $query = $this->find()
                        ->select([
                            'count' => $this->query()->func()->count('DISTINCT Students.studentnumber'),
                            'academic_status_id' => 'StudentExamStatuses.academic_status_id'
                        ])
                        ->innerJoinWith('StudentsSections.Sections', function ($q) use ($college, $academicYear) {
                            return $q->where([
                                'Sections.college_id' => $college->id,
                                'Sections.department_id IS' => null,
                                'OR' => ['Sections.year_level_id IS' => null, 'Sections.year_level_id' => 0],
                                'Sections.academicyear' => $academicYear
                            ]);
                        })
                        ->innerJoinWith('StudentExamStatuses')
                        ->where(array_merge($conditions, ['Students.gender' => $gender]))
                        ->group(['StudentExamStatuses.academic_status_id']);

                    foreach ($query as $result) {
                        if ($result->academic_status_id && isset($statusMap[$result->academic_status_id])) {
                            $distributionByStatusYearLevel[$college->name][$statusMap[$result->academic_status_id]][strtolower($gender)]['1st'] = $result->count;
                            $graph['series'][$result->academic_status_id] = $statusMap[$result->academic_status_id];
                            $graph['labels'][$college->id] = $college->name;
                            $graph['data'][$result->academic_status_id][$college->id] = ($graph['data'][$result->academic_status_id][$college->id] ?? 0) + $result->count;
                        }
                    }
                }
            }
        }

        return [
            'distributionByStatusYearLevel' => $distributionByStatusYearLevel,
            'graph' => $graph
        ];
    }

    public function getDistributionStatsOfGraduate($academicYear, $programId, $programTypeId, $departmentId, $sex = 'all', $regionId)
    {
        $academicYearsTable = TableRegistry::getTableLocator()->get('AcademicYears');
        $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
        $regionsTable = TableRegistry::getTableLocator()->get('Regions');
        $academicStatusesTable = TableRegistry::getTableLocator()->get('AcademicStatuses');
        $graduateListsTable = TableRegistry::getTableLocator()->get('GraduateLists');
        $studentExamStatusesTable=TableRegistry::getTableLocator()->get('StudentExamStatuses');

        $conditions = [];
        $distributionByDepartmentGraduate = [];

        if ($regionId) {
            $conditions['Students.region_id'] = $regionId;
        }
        if ($programId) {
            $programIds = explode('~', $programId);
            $conditions['Students.program_id'] = count($programIds) > 1 ? $programIds[1] : $programId;
        }
        if ($programTypeId) {
            $programTypeIds = explode('~', $programTypeId);
            $conditions['Students.program_type_id'] = count($programTypeIds) > 1 ? $programTypeIds[1] : $programTypeId;
        }
        $graduateDate = $academicYearsTable->getAcademicYearBegainingDate($academicYear);

        $regions = $regionsTable->find()->select(['id', 'name'])->toArray();
        $regionMap = array_column($regions, 'name', 'id');
        $academicStatuses = $academicStatusesTable->find()->select(['id', 'name'])->toArray();
        $statusMap = array_column($academicStatuses, 'name', 'id');

        $deptConditions = [];
        if ($departmentId) {
            $collegeId = explode('~', $departmentId);
            if (count($collegeId) > 1) {
                $deptConditions['Departments.college_id'] = $collegeId[1];
            } else {
                $deptConditions['Departments.id'] = $departmentId;
            }
        }

        $departments = $departmentsTable->find()
            ->where($deptConditions)
            ->contain(['Colleges', 'YearLevels' => function ($q) {
                return $q->order(['name' => 'ASC']);
            }])
            ->toArray();

        $sexes = ($sex !== 'all') ? [(string)$sex] : ['male', 'female'];
        foreach ($departments as $department) {
            foreach ($sexes as $gender) {
                $subQuery = $studentExamStatusesTable->find()
                    ->select(['student_id', 'created' => 'MAX(created)'])
                    ->group(['student_id']);

                $query = $this->find()
                    ->select([
                        'count' => $this->query()->func()->count('DISTINCT Students.studentnumber'),
                        'academic_status_id' => 'StudentExamStatuses.academic_status_id',
                        'region_id' => 'Students.region_id'
                    ])
                    ->innerJoinWith('StudentExamStatuses', function ($q) use ($subQuery) {
                        return $q->innerJoinWith(['subQuery' => $subQuery], function ($q2) {
                            return $q2->where([
                                'StudentExamStatuses.student_id = subQuery.student_id',
                                'StudentExamStatuses.created = subQuery.created'
                            ]);
                        });
                    })
                    ->innerJoinWith('GraduateLists', function ($q) use ($graduateDate) {
                        return $q->where(['graduate_date >=' => $graduateDate]);
                    })
                    ->where(array_merge($conditions, [
                        'Students.department_id' => $department->id,
                        'Students.gender' => $gender
                    ]))
                    ->group(['StudentExamStatuses.academic_status_id', 'Students.region_id']);

                foreach ($query as $result) {
                    if ($result->academic_status_id && $result->region_id && isset($statusMap[$result->academic_status_id], $regionMap[$result->region_id])) {
                        $distributionByDepartmentGraduate[$department->name][$regionMap[$result->region_id]][strtolower($gender)][$statusMap[$result->academic_status_id]] = $result->count;
                    }
                }
            }
        }

        return ['distributionByDepartmentGraduate' => $distributionByDepartmentGraduate];
    }

    public function getNotRegisteredList($academicYear, $semester, $programId = 0, $programTypeId = 0, $departmentId = null, $sex = 'all', $yearLevelId = null, $regionId = null, $freshman = 0, $excludeGraduated = '')
    {
        $studentExamStatusesTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');

        $previousSemester = $studentExamStatusesTable->getPreviousSemester($academicYear, $semester);
        $anotherPreviousSemester = $studentExamStatusesTable->getPreviousSemester(
            $previousSemester['academic_year'],
            $previousSemester['semester']
        );

        $listOfPrevious = [$previousSemester, $anotherPreviousSemester];
        $activeList = [];

        foreach ($listOfPrevious as $previous) {
            $activeList += $studentExamStatusesTable->getActiveStudentNotRegistered(
                $previous['academic_year'],
                $previous['semester'],
                $programId,
                $programTypeId,
                $departmentId,
                $sex,
                $yearLevelId,
                $regionId,
                $academicYear,
                $semester,
                $freshman,
                $excludeGraduated
            );
        }

        return $activeList;
    }

    public function isPhoneValid($mobilePhoneNumber)
    {
        $staffTable = TableRegistry::getTableLocator()->get('Staff');
        $contactsTable = TableRegistry::getTableLocator()->get('Contacts');

        if ($this->find()->where(['phone_mobile' => $mobilePhoneNumber])->count()) {
            return true;
        }

        if ($staffTable->find()->where(['phone_mobile' => $mobilePhoneNumber])->count()) {
            return true;
        }

        if ($contactsTable->find()->where(['phone_mobile' => $mobilePhoneNumber])->count()) {
            return true;
        }

        return false;
    }

    public function updateAllAcademicStatus($academicYear, $includingGraduate = 0)
    {
        $academicYearsTable = TableRegistry::getTableLocator()->get('AcademicYears');
        $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $studentExamStatusesTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');

        $admissionYear = $academicYearsTable->getAcademicYearBegainingDate($academicYear);
        $departments = $departmentsTable->find()
            ->where(['active' => 1])
            ->extract('id')
            ->toArray();

        foreach ($departments as $departmentId) {
            $conditions = [
                'department_id' => $departmentId,
                'OR' => [
                    'academicyear' => $academicYear,
                    'admissionyear' => $admissionYear
                ]
            ];
            if (!$includingGraduate) {
                $conditions['graduated'] = 0;
            }

            $students = $this->find()
                ->where($conditions)
                ->toArray();

            foreach ($students as $student) {
                $studentExamStatusesTable->deleteAll(['student_id' => $student->id]);

                $registrations = $courseRegistrationsTable->find()
                    ->select(['published_course_id', 'student_id', 'academic_year', 'semester'])
                    ->where(['student_id' => $student->id])
                    ->order(['academic_year' => 'ASC', 'semester' => 'ASC', 'id' => 'ASC'])
                    ->distinct(['academic_year', 'semester'])
                    ->toArray();

                foreach ($registrations as $registration) {
                    $studentExamStatusesTable->updateAcdamicStatusByPublishedCourseOfStudent(
                        $registration->published_course_id,
                        $registration->student_id
                    );
                }
            }
        }

        return true;
    }

    public function getIDPrintCount($data, $type = 'count')
    {
        $academicYearsTable = TableRegistry::getTableLocator()->get('AcademicYears');
        $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
        $collegesTable = TableRegistry::getTableLocator()->get('Colleges');
        $programsTable = TableRegistry::getTableLocator()->get('Programs');
        $programTypesTable = TableRegistry::getTableLocator()->get('ProgramTypes');
        $graduateListsTable = TableRegistry::getTableLocator()->get('GraduateLists');
        $sectionsTable = TableRegistry::getTableLocator()->get('Sections');

        $conditions = [
            'id NOT IN' => $graduateListsTable->find()->select(['student_id'])
        ];
        $distributionIDPrinting = ['distributionIDPrintingCount' => [], 'IDPrintingList' => []];
        $graph = ['data' => [[], []], 'labels' => [], 'series' => ['male', 'female']];

        if (!empty($data['program_id'])) {
            $programIds = explode('~', $data['program_id']);
            $conditions['program_id'] = count($programIds) > 1 ? $programIds[1] : $data['program_id'];
        }
        if (!empty($data['program_type_id'])) {
            $programTypeIds = explode('~', $data['program_type_id']);
            $conditions['program_type_id'] = count($programTypeIds) > 1 ? $programTypeIds[1] : $data['program_type_id'];
        }
        if (!empty($data['acadamic_year'])) {
            $admittedYear = $academicYearsTable->getAcademicYearBegainingDate($data['acadamic_year']);
            if (empty($data['year_level_id'])) {
                $conditions['admissionyear <='] = $admittedYear;
            } else {
                $admittedConverted = (new Time($admittedYear))->modify('-' . (int)$data['year_level_id'] . ' years')->toDateString();
                $conditions['admissionyear <='] = $admittedYear;
                $conditions['admissionyear >='] = $admittedConverted;
            }
        }
        if (isset($data['printed_count']) && $data['printed_count'] !== '') {
            $conditions['print_count'] = (int)$data['printed_count'];
        }
        if (!empty($data['gender']) && $data['gender'] !== 'all') {
            $conditions['gender'] = $data['gender'];
        }

        $programs = $programsTable->find()->select(['id', 'name'])->toArray();
        $programMap = array_column($programs, 'name', 'id');
        $programTypes = $programTypesTable->find()->select(['id', 'name'])->toArray();
        $programTypeMap = array_column($programTypes, 'name', 'id');

        $deptConditions = [];
        $collegeId = null;
        if (!empty($data['department_id'])) {
            $collegeIds = explode('~', $data['department_id']);
            if (count($collegeIds) > 1) {
                $deptConditions['college_id'] = $collegeIds[1];
                $collegeId = $collegeIds[1];
            } else {
                $deptConditions['id'] = $data['department_id'];
            }
        }

        $departments = $departmentsTable->find()
            ->where($deptConditions)
            ->contain(['Colleges', 'YearLevels'])
            ->toArray();

        foreach ($departments as $department) {
            $query = $this->find()
                ->where(array_merge($conditions, ['department_id' => $department->id]))
                ->toArray();

            foreach ($query as $student) {
                $yearLevel = $sectionsTable->getStudentYearLevel($student->id);
                $year = $yearLevel['year'] ?? '1st';

                if (
                    (!empty($data['year_level_id']) && $data['year_level_id'] == $year) ||
                    (empty($data['year_level_id']))
                ) {
                    if ($type === 'count') {
                        $distributionIDPrinting['distributionIDPrintingCount'][$department->name][$student->print_count][strtolower($student->gender)][$year] =
                            ($distributionIDPrinting['distributionIDPrintingCount'][$department->name][$student->print_count][strtolower($student->gender)][$year] ?? 0) + 1;
                    } else {
                        $key = sprintf(
                            '%s~%s~%s~%s~%d',
                            $department->name,
                            $programMap[$student->program_id],
                            $programTypeMap[$student->program_type_id],
                            $year,
                            $student->print_count
                        );
                        $distributionIDPrinting['IDPrintingList'][$key][] = $student->toArray();
                    }
                }
            }
        }

        if ($collegeId) {
            $colleges = $collegesTable->find()->where(['id' => $collegeId])->toArray();
            $query = $this->find()
                ->where(array_merge($conditions, ['department_id IS' => null, 'college_id' => $collegeId]))
                ->toArray();

            foreach ($query as $student) {
                $yearLevel = $sectionsTable->getStudentYearLevel($student->id);
                $year = $yearLevel['year'] ?? '1st';

                if (
                    (!empty($data['year_level_id']) && $data['year_level_id'] == $year) ||
                    (empty($data['year_level_id']))
                ) {
                    if ($type === 'count') {
                        $distributionIDPrinting['distributionIDPrintingCount'][$colleges[0]->name . ' Pre'][$student->print_count][strtolower($student->gender)][$year] =
                            ($distributionIDPrinting['distributionIDPrintingCount'][$colleges[0]->name . ' Pre'][$student->print_count][strtolower($student->gender)][$year] ?? 0) + 1;
                    } else {
                        $key = sprintf(
                            '%s~%s~%s~%s~%d',
                            $colleges[0]->name . ' Pre',
                            $programMap[$student->program_id],
                            $programTypeMap[$student->program_type_id],
                            $year,
                            $student->print_count
                        );
                        $distributionIDPrinting['IDPrintingList'][$key][] = $student->toArray();
                    }
                }
            }
        }

        return [
            'distributionIDPrintingCount' => $distributionIDPrinting['distributionIDPrintingCount'],
            'IDPrintingList' => $distributionIDPrinting['IDPrintingList']
        ];
    }

    public function getListOfDepartmentStudentsByYearLevelAndSection(
        $collegeId = null,
        $departmentId = null,
        $programId = null,
        $programTypeId = null,
        $yearLevelId = null,
        $plusOne = 1,
        $gender = null,
        $studentIds = null,
        $acceptedStudentIds = null,
        $sectionId = 0
    ) {
        $academicYearsTable = TableRegistry::getTableLocator()->get('AcademicYears');
        $sectionsTable = TableRegistry::getTableLocator()->get('Sections');
        $studentExamStatusesTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');
        $acceptedStudentsTable = TableRegistry::getTableLocator()->get('AcceptedStudents');
        $graduateListsTable = TableRegistry::getTableLocator()->get('GraduateLists');
        $studentsSectionsTable = TableRegistry::getTableLocator()->get('StudentsSections');
        $yearLevelsTable=TableRegistry::getTableLocator()->get('YearLevels');

        $currentAcademicYear = $academicYearsTable->current_academicyear();
        $givenYearLevel = null;

        if ($yearLevelId) {
            $givenYearLevel = is_numeric($yearLevelId)
                ? intval($yearLevelsTable->find()->where(['id' => $yearLevelId])->first()->name ?? 0)
                : intval($yearLevelId);
        }

        $conditions = [
            'NOT' => ['id' => (array)$studentIds],
            'id NOT IN' => $graduateListsTable->find()->select(['student_id'])
        ];
        if ($departmentId) {
            $conditions['department_id'] = $departmentId;
        } elseif ($collegeId) {
            $conditions['college_id'] = $collegeId;
        }
        if ($programId) {
            $conditions['program_id'] = $programId;
        }
        if ($programTypeId) {
            $conditions['program_type_id'] = $programTypeId;
        }
        if ($gender) {
            $conditions['gender'] = $gender;
        }
        if ($sectionId) {
            $sectionStudentIds = $studentsSectionsTable->find()
                ->where(['section_id' => $sectionId])
                ->extract('student_id')
                ->toArray();
            if ($sectionStudentIds) {
                $conditions['id IN'] = $sectionStudentIds;
            }
        }

        $students = $this->find()
            ->select(['id', 'studentnumber', 'full_name'])
            ->where($conditions)
            ->order(['admissionyear' => 'DESC'])
            ->toArray();

        $admittedStudents = [];
        foreach ($students as &$student) {
            $yearLevel = $sectionsTable->getStudentYearLevel($student->id);
            $year = $yearLevel['year'] ?? 0;
            $eligible = $studentExamStatusesTable->isElegibleForService($student->id, $currentAcademicYear);
            if ((empty($givenYearLevel) || $year == $givenYearLevel) && $eligible) {
                $student->fxinlaststatus = $studentExamStatusesTable->checkFxPresenseInStatus($student->id) == 0 ? 'Yes' : 'No';
                $admittedStudents[] = ['Student' => $student->toArray()];
            }
        }

        $nonAdmittedStudents = [];
        if (empty($givenYearLevel) || $givenYearLevel == 1) {
            $nonAdmittedConditions = [
                'NOT' => ['id' => (array)$acceptedStudentIds],
                'id NOT IN' => $this->find()->select(['accepted_student_id'])
            ];
            if ($departmentId) {
                $nonAdmittedConditions['department_id'] = $departmentId;
            } elseif ($collegeId) {
                $nonAdmittedConditions['college_id'] = $collegeId;
            }
            if ($programId) {
                $nonAdmittedConditions['program_id'] = $programId;
            }
            if ($programTypeId) {
                $nonAdmittedConditions['program_type_id'] = $programTypeId;
            }
            if ($gender) {
                $nonAdmittedConditions['sex'] = $gender;
            }

            $nonAdmittedStudents = $acceptedStudentsTable->find()
                ->select(['id', 'studentnumber', 'full_name'])
                ->where($nonAdmittedConditions)
                ->toArray();
        }

        return [
            'student' => $admittedStudents,
            'accepted_student' => $nonAdmittedStudents
        ];
    }

    public function checkAdmissionTransaction($studentId)
    {
        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $studentsSectionsTable = TableRegistry::getTableLocator()->get('StudentsSections');

        $isRegistered = $courseRegistrationsTable->find()
                ->where(['student_id' => $studentId])
                ->count() > 0;

        $isInSection = $studentsSectionsTable->find()
                ->where(['student_id' => $studentId])
                ->count() > 0;

        return ($isRegistered || $isInSection) ? 1 : 0;
    }

    public function getActiveStudentStatistics($academicYear, $semester, $departmentId, $regionId = null, $programId, $programTypeId, $sex = 'all')
    {
        $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
        $programsTable = TableRegistry::getTableLocator()->get('Programs');
        $programTypesTable = TableRegistry::getTableLocator()->get('ProgramTypes');
        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');

        $deptConditions = [];
        if ($departmentId) {
            $collegeId = explode('~', $departmentId);
            if (count($collegeId) > 1) {
                $deptConditions['Departments.college_id'] = $collegeId[1];
            } else {
                $deptConditions['Departments.id'] = $departmentId;
            }
        }

        $departments = $departmentsTable->find()
            ->where($deptConditions)
            ->contain(['Colleges', 'YearLevels'])
            ->order(['Departments.college_id' => 'ASC', 'Departments.name' => 'ASC', 'Departments.id' => 'ASC'])
            ->toArray();

        $programConditions = $programId ? ['id' => $programId] : [];
        $programs = $programsTable->find()
            ->select(['id', 'name'])
            ->where($programConditions)
            ->toArray();
        $programMap = array_column($programs, 'name', 'id');

        $programTypeConditions = $programTypeId ? ['id' => $programTypeId] : [];
        $programTypes = $programTypesTable->find()
            ->select(['id', 'name'])
            ->where($programTypeConditions)
            ->toArray();
        $programTypeMap = array_column($programTypes, 'name', 'id');

        $sexList = $sex === 'all' ? ['male' => 'male', 'female' => 'female'] : [$sex => $sex];

        $activeListStatistics = [];
        $collegeDepartmentYearCount = [];

        foreach ($departments as $department) {
            foreach ($department->year_levels as $yearLevel) {
                $collegeDepartmentYearCount[$department->college->name] = ($collegeDepartmentYearCount[$department->college->name] ?? 0) + 1;

                foreach ($programMap as $programId => $programName) {
                    foreach ($programTypeMap as $typeId => $typeName) {
                        $total = 0;
                        foreach ($sexList as $genderKey => $genderValue) {
                            $count = $this->find()
                                ->where([
                                    'department_id' => $department->id,
                                    'program_id' => $programId,
                                    'program_type_id' => $typeId,
                                    'gender' => $genderKey,
                                    'graduated' => 0
                                ])
                                ->innerJoinWith('CourseRegistrations', function ($q) use ($academicYear, $semester, $yearLevel) {
                                    return $q->where([
                                        'academic_year' => $academicYear,
                                        'semester' => $semester,
                                        'year_level_id' => $yearLevel->id
                                    ]);
                                })
                                ->count();

                            $activeListStatistics[$programName][$department->college->name][$department->name][$yearLevel->name][$typeName][$genderKey] = $count;
                            $total += $count;
                        }
                        $activeListStatistics[$programName][$department->college->name][$department->name][$yearLevel->name][$typeName]['total'] = $total;
                    }
                }
            }
        }

        return [
            'result' => $activeListStatistics,
            'collegeRowSpan' => $collegeDepartmentYearCount
        ];
    }
    public function getStudentConsistencyByAgeRangeStatistics($academicYear, $semester, $departmentId, $regionId = null, $programId, $programTypeId, $sex = 'all')
    {
        $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
        $programsTable = TableRegistry::getTableLocator()->get('Programs');
        $programTypesTable = TableRegistry::getTableLocator()->get('ProgramTypes');
        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');

        $deptConditions = [];
        if ($departmentId) {
            $collegeId = explode('~', $departmentId);
            if (count($collegeId) > 1) {
                $deptConditions['Departments.college_id'] = $collegeId[1];
            } else {
                $deptConditions['Departments.id'] = $departmentId;
            }
        }

        $departments = $departmentsTable->find()
            ->where($deptConditions)
            ->contain(['Colleges', 'YearLevels'])
            ->order(['Departments.college_id' => 'DESC'])
            ->toArray();

        $programConditions = $programId ? ['id' => $programId] : [];
        $programs = $programsTable->find()
            ->select(['id', 'name'])
            ->where($programConditions)
            ->toArray();
        $programMap = array_column($programs, 'name', 'id');

        $programTypeConditions = $programTypeId ? ['id' => $programTypeId] : [];
        $programTypes = $programTypesTable->find()
            ->select(['id', 'name'])
            ->where($programTypeConditions)
            ->toArray();
        $programTypeMap = array_column($programTypes, 'name', 'id');

        $sexList = $sex === 'all' ? ['male' => 'male', 'female' => 'female'] : [$sex => $sex];

        $activeListStatistics = [];
        $ageRanges = [
            '<18' => 18,
            '18' => 18,
            '19' => 19,
            '20' => 20,
            '21' => 21,
            '22' => 22,
            '23' => 23,
            '24' => 24,
            '25' => 25,
            '26' => 26,
            '>26' => 26
        ];

        foreach ($departments as $department) {
            foreach ($programMap as $programId => $programName) {
                foreach ($programTypeMap as $typeId => $typeName) {
                    foreach ($sexList as $genderKey => $genderValue) {
                        foreach ($ageRanges as $ageKey => $ageValue) {
                            $calculatedDate = (new Time())->modify("-$ageValue years")->toDateString();
                            // Fixed: Replaced match with if-elseif
                            if ($ageKey === '<18') {
                                $birthdateCondition = ['birthdate >' => $calculatedDate];
                            } elseif ($ageKey === '>26') {
                                $birthdateCondition = ['birthdate <' => $calculatedDate];
                            } else {
                                $birthdateCondition = [
                                    'birthdate <=' => $calculatedDate,
                                    'birthdate >' => (new Time())->modify("-{$ageValue} years -12 months")->toDateString()
                                ];
                            }

                            $count = $this->find()
                                ->where(array_merge([
                                    'department_id' => $department->id,
                                    'program_id' => $programId,
                                    'program_type_id' => $typeId,
                                    'gender' => $genderKey
                                ], $birthdateCondition))
                                ->innerJoinWith('CourseRegistrations', function ($q) use ($academicYear, $semester) {
                                    return $q->where([
                                        'academic_year' => $academicYear,
                                        'semester' => $semester
                                    ]);
                                })
                                ->count();

                            $activeListStatistics[$programName][$ageKey][$typeName][$genderKey] = ($activeListStatistics[$programName][$ageKey][$typeName][$genderKey] ?? 0) + $count;
                        }
                    }
                }
            }
        }

        return $activeListStatistics;
    }
    public function getAge($dob)
    {
        $dobDate = new Time($dob);
        $now = new Time();
        $difference = $now->diff($dobDate);
        return $difference->y;
    }

    public function synckoha($collegeId = 0)
    {
        $academicYearsTable = TableRegistry::getTableLocator()->get('AcademicYears');
        $collegesTable = TableRegistry::getTableLocator()->get('Colleges');
        $studentExamStatusesTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');
        $kohaDb = ConnectionManager::get('koha');
        $httpClient = new \Cake\Http\Client();

        $branchCodes = [
            2 => 'AAES',
            1 => 'Main',
            3 => 'AHMAD',
            4 => 'AAGSL',
            6 => 'ASSH'
        ];

        $collegeConditions = $collegeId ? ['id' => $collegeId] : [];
        $colleges = $collegesTable->find()
            ->where($collegeConditions)
            ->toArray();

        foreach ($colleges as $college) {
            $students = $studentExamStatusesTable->getMostRecentStudentStatus($college->id);

            foreach ($students as $student) {
                $studentNumber = $student->studentnumber;
                $studentNumberFormatted = str_replace('/', '-', $studentNumber);
                $source = "http://smis.amu.edu.et/media/transfer/img/{$studentNumberFormatted}.jpg";

                $expirationDate = $student->status_academic_year && $student->status_semester
                    ? (new Time($academicYearsTable->getAcademicYearBegainingDate($student->status_academic_year, $student->status_semester)))
                        ->modify('+3 months')
                        ->toDateString()
                    : (new Time())->modify('+3 months')->toDateString();

                $borrower = $kohaDb->newQuery()
                    ->from('borrowers')
                    ->select(['borrowernumber'])
                    ->where([
                        'branchcode' => $branchCodes[$college->id] ?? 'Main',
                        'categorycode' => 'ST',
                        'cardnumber' => $studentNumber
                    ])
                    ->execute()
                    ->fetch('assoc');

                if (!$borrower) {
                    $insertData = [
                        'cardnumber' => $studentNumber,
                        'surname' => $student->last_name,
                        'firstname' => $student->first_name . ' ' . $student->middle_name,
                        'address' => implode(' ', array_filter([$student->woreda, $student->kebele, $student->house_number])),
                        'city' => $student->city->name ?? '',
                        'branchcode' => $branchCodes[$college->id] ?? 'Main',
                        'email' => $student->email,
                        'mobile' => $student->phone_mobile,
                        'dateofbirth' => $student->birthdate,
                        'categorycode' => 'ST',
                        'dateenrolled' => $student->admissionyear,
                        'dateexpiry' => $expirationDate,
                        'sex' => $student->gender,
                        // SECURITY NOTE: MD5 is insecure; consider using a secure hashing method like bcrypt.
                        'password' => md5($studentNumber),
                        'userid' => $studentNumber
                    ];

                    $kohaDb->insert('borrowers', $insertData);

                    $borrowerNumber = $kohaDb->newQuery()
                        ->from('borrowers')
                        ->select(['borrowernumber'])
                        ->where(['cardnumber' => $studentNumber])
                        ->limit(1)
                        ->execute()
                        ->fetch('assoc')['borrowernumber'];

                    $response = $httpClient->get($source, [], ['timeout' => 5]);
                    if ($response->isOk()) {
                        $imageData = $response->getBody()->getContents();
                        $kohaDb->insert('patronimage', [
                            'borrowernumber' => $borrowerNumber,
                            'mimetype' => 'image/jpeg',
                            'imagefile' => $imageData
                        ]);
                    }
                } else {
                    $kohaDb->update('borrowers', ['dateexpiry' => $expirationDate], ['borrowernumber' => $borrower['borrowernumber']]);
                }
            }
        }

        return true;
    }

    public function extendKohaBorrowerExpireDate($studentIds = [])
    {
        $academicYearsTable = TableRegistry::getTableLocator()->get('AcademicYears');
        $studentExamStatusesTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');
        $kohaDb = ConnectionManager::get('koha');
        $httpClient = new \Cake\Http\Client();

        $branchCodes = [
            2 => 'AAES',
            1 => 'Main',
            3 => 'AHMAD',
            4 => 'AAGSL',
            6 => 'ASSH'
        ];

        $students = $studentExamStatusesTable->getMostRecentStudentStatusForKoha($studentIds);

        foreach ($students as $student) {
            $studentNumber = $student->studentnumber;
            $studentNumberFormatted = str_replace('/', '-', $studentNumber);
            $source = "http://smis.amu.edu.et/media/transfer/img/{$studentNumberFormatted}.jpg";

            $expirationDate = $student->status_academic_year && $student->status_semester
                ? (new Time($academicYearsTable->getAcademicYearBegainingDate($student->status_academic_year, $student->status_semester)))
                    ->modify('+3 months')
                    ->toDateString()
                : (new Time())->modify('+3 months')->toDateString();

            $borrower = $kohaDb->newQuery()
                ->from('borrowers')
                ->select(['borrowernumber'])
                ->where([
                    'branchcode' => $branchCodes[$student->college->id] ?? 'Main',
                    'categorycode' => 'ST',
                    'cardnumber' => $studentNumber
                ])
                ->execute()
                ->fetch('assoc');

            if (!$borrower) {
                $insertData = [
                    'cardnumber' => $studentNumber,
                    'surname' => $student->last_name,
                    'firstname' => $student->first_name . ' ' . $student->middle_name,
                    'address' => implode(' ', array_filter([$student->woreda, $student->kebele, $student->house_number])),
                    'city' => $student->city->name ?? '',
                    'branchcode' => $branchCodes[$student->college->id] ?? 'Main',
                    'email' => $student->email,
                    'mobile' => $student->phone_mobile,
                    'dateofbirth' => $student->birthdate,
                    'categorycode' => 'ST',
                    'dateenrolled' => $student->admissionyear,
                    'dateexpiry' => $expirationDate,
                    'sex' => $student->gender,
                    // SECURITY NOTE: MD5 is insecure; consider using a secure hashing method like bcrypt.
                    'password' => md5($studentNumber),
                    'userid' => $studentNumber
                ];

                $kohaDb->insert('borrowers', $insertData);

                $borrowerNumber = $kohaDb->newQuery()
                    ->from('borrowers')
                    ->select(['borrowernumber'])
                    ->where(['cardnumber' => $studentNumber])
                    ->limit(1)
                    ->execute()
                    ->fetch('assoc')['borrowernumber'];

                $response = $httpClient->get($source, [], ['timeout' => 5]);
                if ($response->isOk()) {
                    $imageData = $response->getBody()->getContents();
                    $kohaDb->insert('patronimage', [
                        'borrowernumber' => $borrowerNumber,
                        'mimetype' => 'image/jpeg',
                        'imagefile' => $imageData
                    ]);
                }
            } else {
                $kohaDb->update('borrowers', ['dateexpiry' => $expirationDate], ['borrowernumber' => $borrower['borrowernumber']]);
            }
        }

        return true;
    }

    public function getRegisteredStudentList($academicYear, $semester, $programId = null, $programTypeId = null, $departmentId = null, $sex = 'all', $yearLevelId = null, $regionId = null, $freshman = 0, $excludeGraduated = '')
    {
        $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
        $collegesTable = TableRegistry::getTableLocator()->get('Colleges');
        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $sectionsTable = TableRegistry::getTableLocator()->get('Sections');
        $studentsSectionsTable = TableRegistry::getTableLocator()->get('StudentsSections');
        $graduateListsTable = TableRegistry::getTableLocator()->get('GraduateLists');

        $conditions = [];
        $registrationConditions = [];

        if ($regionId) {
            $conditions['Students.region_id'] = $regionId;
        }
        if ($programId) {
            $programIds = explode('~', $programId);
            $conditions['Students.program_id'] = count($programIds) > 1 ? $programIds[1] : $programId;
        }
        if ($programTypeId) {
            $programTypeIds = explode('~', $programTypeId);
            $conditions['Students.program_type_id'] = count($programTypeIds) > 1 ? $programTypeIds[1] : $programTypeId;
        }
        if ($excludeGraduated == 1) {
            $conditions['Students.graduated'] = 0;
        }
        if ($academicYear) {
            $registrationConditions['CourseRegistrations.academic_year'] = $academicYear;
        }
        if ($semester) {
            $registrationConditions['CourseRegistrations.semester'] = $semester;
        }

        $deptConditions = ['Departments.active' => 1];
        $collegeIds = [];
        if ($departmentId) {
            $collegeId = explode('~', $departmentId);
            if (count($collegeId) > 1) {
                $deptConditions['Departments.college_id'] = $collegeId[1];
                $collegeIds[$collegeId[1]] = $collegeId[1];
            } else {
                $deptConditions['Departments.id'] = $departmentId;
            }
        }

        $departments = $freshman ? [] : $departmentsTable->find()
            ->where($deptConditions)
            ->contain(['Colleges', 'YearLevels' => function ($q) {
                return $q->order(['name' => 'ASC']);
            }])
            ->toArray();

        $colleges = $collegesTable->find()
            ->where(['id IN' => array_keys($collegeIds)])
            ->toArray();

        $studentListNotRegistered = [];
        $sexList = $sex === 'all' ? ['male' => 'male', 'female' => 'female'] : [$sex => $sex];

        if (!$freshman) {
            foreach ($departments as $department) {
                foreach ($department->year_levels as $yearLevel) {
                    if ($yearLevelId && $yearLevel->name !== $yearLevelId) {
                        continue;
                    }

                    foreach ($sexList as $genderKey => $genderValue) {
                        $registeredStudentIds = $courseRegistrationsTable->find()
                            ->select(['student_id'])
                            ->innerJoinWith('Sections', function ($q) use ($department, $yearLevel, $academicYear) {
                                return $q->where([
                                    'Sections.department_id' => $department->id,
                                    'Sections.year_level_id' => $yearLevel->id,
                                    'Sections.academicyear' => $academicYear
                                ]);
                            })
                            ->where(array_merge($registrationConditions, [
                                'student_id NOT IN' => $graduateListsTable->find()->select(['student_id']),
                                'student_id IN' => $this->find()->select(['id'])->where(array_merge($conditions, ['Students.gender' => $genderKey]))
                            ]))
                            ->distinct(['student_id'])
                            ->extract('student_id')
                            ->toArray();

                        foreach ($registeredStudentIds as $studentId) {
                            $registrations = $courseRegistrationsTable->find()
                                ->where([
                                    'student_id' => $studentId,
                                    'semester' => $semester,
                                    'academic_year' => $academicYear
                                ])
                                ->contain([
                                    'PublishedCourses.Courses' => ['fields' => ['credit']],
                                    'Sections' => ['fields' => ['name']]
                                ])
                                ->toArray();

                            if ($registrations) {
                                $studentDetail = $this->find()
                                    ->select(['id', 'full_name', 'department_id', 'gender', 'studentnumber'])
                                    ->where(['id' => $studentId])
                                    ->contain([
                                        'Programs' => ['fields' => ['id', 'name']],
                                        'ProgramTypes' => ['fields' => ['id', 'name']],
                                        'Colleges' => ['fields' => ['id', 'name']]
                                    ])
                                    ->first();

                                if ($studentDetail->department_id == $department->id) {
                                    $studentDetail->credithour = 0;
                                    foreach ($registrations as $registration) {
                                        $studentDetail->credithour += $registration->published_course->course->credit;
                                        $studentDetail->sectionName = $registration->section->name;
                                    }

                                    $key = sprintf(
                                        '%s~%s~%s~%s~%s',
                                        $studentDetail->college->name,
                                        $department->name,
                                        $studentDetail->program->name,
                                        $studentDetail->program_type->name,
                                        $yearLevel->name
                                    );
                                    $studentListNotRegistered[$key][$studentId] = $studentDetail->toArray();
                                }
                            }
                        }
                    }
                }
            }
        } else {
            foreach ($colleges as $college) {
                $sectionIds = $sectionsTable->find()
                    ->select(['id'])
                    ->where([
                        'college_id' => $college->id,
                        'academicyear' => $academicYear,
                        'department_id IS' => null
                    ])
                    ->extract('id')
                    ->toArray();

                foreach ($sexList as $genderKey => $genderValue) {
                    $registeredStudentIds = $courseRegistrationsTable->find()
                        ->select(['student_id'])
                        ->innerJoinWith('Sections', function ($q) use ($college, $academicYear) {
                            return $q->where([
                                'Sections.college_id' => $college->id,
                                'Sections.department_id IS' => null,
                                'OR' => ['Sections.year_level_id IS' => null, 'Sections.year_level_id' => 0],
                                'Sections.academicyear' => $academicYear
                            ]);
                        })
                        ->where(array_merge($registrationConditions, [
                            'student_id NOT IN' => $graduateListsTable->find()->select(['student_id']),
                            'student_id IN' => $this->find()->select(['id'])->where(array_merge($conditions, ['Students.gender' => $genderKey]))
                        ]))
                        ->distinct(['student_id'])
                        ->extract('student_id')
                        ->toArray();

                    foreach ($registeredStudentIds as $studentId) {
                        $registrations = $courseRegistrationsTable->find()
                            ->where([
                                'student_id' => $studentId,
                                'semester' => $semester,
                                'academic_year' => $academicYear
                            ])
                            ->contain([
                                'PublishedCourses.Courses' => ['fields' => ['credit']],
                                'Sections' => ['fields' => ['name']]
                            ])
                            ->toArray();

                        if ($registrations) {
                            $studentDetail = $this->find()
                                ->select(['id', 'full_name', 'college_id', 'gender', 'studentnumber'])
                                ->where(['id' => $studentId])
                                ->contain([
                                    'Programs' => ['fields' => ['id', 'name']],
                                    'ProgramTypes' => ['fields' => ['id', 'name']],
                                    'Colleges' => ['fields' => ['id', 'name']]
                                ])
                                ->first();

                            $studentDetail->credithour = 0;
                            foreach ($registrations as $registration) {
                                $studentDetail->credithour += $registration->published_course->course->credit;
                                $studentDetail->sectionName = $registration->section->name;
                            }

                            $key = sprintf(
                                '%s~%s~%s~%s~%s',
                                $studentDetail->college->name,
                                'Fresh',
                                $studentDetail->program->name,
                                $studentDetail->program_type->name,
                                '1st Year'
                            );
                            $studentListNotRegistered[$key][$studentId] = $studentDetail->toArray();
                        }
                    }
                }
            }
        }

        return $studentListNotRegistered;
    }

    public function updateAcademicYear($departmentId = null)
    {
        $conditions = ['academicyear IS' => null];
        if ($departmentId) {
            $conditions['department_id'] = $departmentId;
        }

        $students = $this->find()
            ->where($conditions)
            ->contain(['AcceptedStudents' => ['fields' => ['id', 'academicyear']]])
            ->toArray();

        $entities = array_filter(array_map(function ($student) {
            if (!empty($student->accepted_student->academicyear)) {
                return [
                    'id' => $student->id,
                    'academicyear' => $student->accepted_student->academicyear
                ];
            }
            return null;
        }, $students));

        if ($entities) {
            $this->saveMany($this->newEntities($entities), ['validate' => false]);
        }

        return true;
    }

    public function updateGraduated($departmentId = null)
    {
        $conditions = ['graduated' => 0];
        if ($departmentId) {
            $conditions['department_id'] = $departmentId;
        }

        $students = $this->find()
            ->where($conditions)
            ->contain(['GraduateLists' => ['fields' => ['student_id']]])
            ->toArray();

        $entities = array_filter(array_map(function ($student) {
            if (!empty($student->graduate_list->student_id)) {
                return [
                    'id' => $student->id,
                    'graduated' => 1
                ];
            }
            return null;
        }, $students));

        if ($entities) {
            $this->saveMany($this->newEntities($entities), ['validate' => false]);
        }

        return true;
    }
}
