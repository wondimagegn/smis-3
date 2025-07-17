<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\TableRegistry;

class ExamTypesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('exam_types');
        $this->setDisplayField('exam_name');
        $this->setPrimaryKey('id');

        // Associations
        $this->belongsTo('PublishedCourses', [
            'foreignKey' => 'published_course_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('Sections', [
            'foreignKey' => 'section_id',
            'joinType' => 'INNER',
        ]);

        $this->hasMany('ExamResults', [
            'foreignKey' => 'exam_type_id',
            'dependent' => false,
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->requirePresence('exam_name', 'create')
            ->notEmptyString('exam_name', 'Please enter exam type.')
            ->requirePresence('percent', 'create')
            ->notEmptyString('percent', 'Please enter the percentage.')
            ->numeric('percent', 'Please enter the percent in number.')
            ->lessThanOrEqual('percent', 100, 'Percent cannot be greater than 100.')
            ->greaterThan('percent', 0, 'Percent cannot be less than 1.')
            ->allowEmptyString('order', true)
            ->numeric('order', 'Please enter the order in number.')
            ->greaterThan('order', 0, 'Order cannot be less than 1.');

        return $validator;
    }

    public function unsetEmptyRows($data = null)
    {
        if (!empty($data['ExamType'])) {
            $skipFirstRow = 0;
            foreach ($data['ExamType'] as $k => &$v) {
                if ($skipFirstRow == 0) {
                    // Skip first row
                } else {
                    if (empty($v['exam_name']) && empty($v['percent'])) {
                        unset($data['ExamType'][$k]);
                    }
                }
                $skipFirstRow++;
            }
        }
        return $data;
    }

    public function getExamType($publishedCourseId)
    {
        return $this->find()
            ->where(['ExamTypes.published_course_id' => $publishedCourseId])
            ->contain([
                'ExamResults',
                'PublishedCourses' => [
                    'GivenByDepartments',
                    'YearLevels',
                    'Courses' => ['Curriculums'],
                    'CourseInstructorAssignments' => [
                        'Staff' => ['Departments', 'Titles', 'Positions'],
                    ],
                ],
            ])
            ->all()
            ->toArray();
    }

    public function getExamTypeReport(
        $academicYear = null,
        $semester = null,
        $programId = null,
        $programTypeId = null,
        $departmentId = null,
        $gender = null,
        $yearLevelId = null,
        $continuousAssNumber = 0
    ) {
        $options = [];

        if (!empty($academicYear)) {
            $options['conditions']['PublishedCourses.academic_year'] = $academicYear;
        }

        if (!empty($semester)) {
            $options['conditions']['PublishedCourses.semester'] = $semester;
        }

        if (!empty($departmentId)) {
            $collegeId = explode('~', $departmentId);
            if (count($collegeId) > 1) {
                $options['conditions'][] = 'PublishedCourses.given_by_department_id IN (SELECT id FROM departments WHERE college_id = :collegeId)';
                $options['bind'][':collegeId'] = $collegeId[1];
            } else {
                $options['conditions']['PublishedCourses.given_by_department_id'] = $departmentId;
            }
        }

        if (!empty($programId)) {
            $programIds = explode('~', $programId);
            if (count($programIds) > 1) {
                // No condition added as per original logic
            } else {
                $options['conditions']['PublishedCourses.program_id'] = $programId;
            }
        }

        if (!empty($programTypeId)) {
            $programTypeIds = explode('~', $programTypeId);
            if (count($programTypeIds) > 1) {
                // No condition added as per original logic
            } else {
                $options['conditions']['PublishedCourses.program_type_id'] = $programTypeId;
            }
        }

        if (!empty($yearLevelId)) {
            $yearId = explode('~', $yearLevelId);
            if (count($yearId) > 1) {
                // No condition added as per original logic
            } else {
                $options['conditions']['PublishedCourses.year_level_id IN (SELECT id FROM year_levels WHERE name = :yearLevelId)'] = [':yearLevelId' => $yearLevelId];
            }
        }

        $publishedCourses = $this->PublishedCourses->find()
            ->select([
                'PublishedCourses.id',
                'PublishedCourses.academic_year',
                'PublishedCourses.semester',
                'PublishedCourses.given_by_department_id',
                'PublishedCourses.program_id',
                'PublishedCourses.program_type_id',
                'PublishedCourses.year_level_id',
                'GivenByDepartments.id',
                'GivenByDepartments.name',
                'Courses.id',
                'Courses.course_title',
                'Courses.course_code',
                'Courses.credit',
                'Programs.id',
                'Programs.name',
                'ProgramTypes.id',
                'ProgramTypes.name',
                'YearLevels.id',
                'YearLevels.name',
                'Sections.id',
                'Sections.name',
            ])
            ->contain([
                'CourseInstructorAssignments' => ['Staff' => ['Departments']],
                'GivenByDepartments',
                'Courses',
                'Programs',
                'ProgramTypes',
                'YearLevels',
                'Sections',
                'ExamTypes',
            ])
            ->where($options['conditions'] ?? [])
            ->bind($options['bind'] ?? [])
            ->all()
            ->toArray();

        $instructors = [];
        if (!empty($publishedCourses)) {
            foreach ($publishedCourses as $v) {
                foreach ($v->course_instructor_assignments as $cv) {
                    if (!empty($continuousAssNumber) && count($v->exam_types) == $continuousAssNumber) {
                        if ($cv->type === 'Lecture') {
                            $key = "{$cv->staff->department->name}~{$cv->staff->full_name}~{$v->course->course_title}({$v->course->course_code}-{$v->course->credit})~p_id{$v->id}";
                            $instructors[$key] = count($v->exam_types);
                        }
                    } elseif ($continuousAssNumber == 0) {
                        if ($cv->type === 'Lecture') {
                            $key = "{$cv->staff->department->name}~{$cv->staff->full_name}~{$v->course->course_title}({$v->course->course_code}-{$v->course->credit})~p_id{$v->id}";
                            $instructors[$key] = count($v->exam_types);
                        }
                    }
                }
            }
        }

        return $instructors;
    }

    public function getAssessmentDetailType($courseRegistrationId, $type = 1)
    {
        $resultDetail = [];

        if ($type == 1) {
            $publishedCourseId = TableRegistry::getTableLocator()->get('CourseRegistrations')
                ->find()
                ->select(['published_course_id'])
                ->where(['id' => $courseRegistrationId])
                ->first()
                ->published_course_id;

            $examTypes = $this->find()
                ->where(['published_course_id' => $publishedCourseId])
                ->all();

            if (!empty($examTypes)) {
                foreach ($examTypes as $vex) {
                    $result = TableRegistry::getTableLocator()->get('ExamResults')
                        ->find()
                        ->select(['result'])
                        ->where([
                            'course_registration_id' => $courseRegistrationId,
                            'exam_type_id' => $vex->id,
                        ])
                        ->first();

                    $resultDetail["{$vex->exam_name}({$vex->percent}%)"] = $result ? $result->result : null;
                }
            }
        } else {
            $publishedCourseId = TableRegistry::getTableLocator()->get('CourseAdds')
                ->find()
                ->select(['published_course_id'])
                ->where(['id' => $courseRegistrationId])
                ->first()
                ->published_course_id;

            $examTypes = $this->find()
                ->where(['published_course_id' => $publishedCourseId])
                ->all();

            if (!empty($examTypes)) {
                foreach ($examTypes as $vex) {
                    $result = TableRegistry::getTableLocator()->get('ExamResults')
                        ->find()
                        ->select(['result'])
                        ->where([
                            'course_add_id' => $courseRegistrationId,
                            'exam_type_id' => $vex->id,
                        ])
                        ->first();

                    $resultDetail["{$vex->exam_name}({$vex->percent}%)"] = $result ? $result->result : null;
                }
            }
        }

        return $resultDetail;
    }

    public function examSetupCreation($publishedCourseId, $givenSetup)
    {
        $providedExamSetups = [];
        $count = 0;

        if (!empty($givenSetup)) {
            foreach ($givenSetup as $k => $v) {
                if ($k == 0) {
                    continue;
                }

                $assType = explode('-', $v);

                if (isset($assType[1]) && !empty($assType[1])) {
                    $examType = $this->find()
                        ->where([
                            'published_course_id' => $publishedCourseId,
                            'percent' => trim($assType[1]),
                            'exam_name' => trim($assType[0]),
                        ])
                        ->first();

                    if ($examType) {
                        $providedExamSetups['ExamType'][$count] = $examType;
                    } else {
                        if (!is_numeric($assType[1])) {
                            $this->validationErrors['assessement'] = [
                                'Please provide the percent "' . $assType[1] . '" in number. If you put "%" in the weight please remove it and put only the number',
                            ];
                            return false;
                        }

                        $providedExamSetups['ExamType'][$count] = [
                            'exam_name' => trim($assType[0]),
                            'percent' => trim($assType[1]),
                            'order' => $count + 1,
                            'published_course_id' => $publishedCourseId,
                        ];
                    }
                } elseif (isset($assType[0]) && !empty($assType[0]) && !isset($assType[1])) {
                    $this->validationErrors['assessement'] = [
                        'The assessement "' . $assType[0] . '" doesn\'t have weight, please provide the weight for assessement after its name separated by - the weight of the assessment without percent.',
                    ];
                    return false;
                }

                $count++;
            }
        }

        if (!empty($providedExamSetups['ExamType'])) {
            $totalWeight = array_sum(array_column($providedExamSetups['ExamType'], 'percent'));

            if ($totalWeight != 100) {
                $this->validationErrors['assessement'] = [
                    'The current total assessement weight is ' . $totalWeight . ' it must be 100.',
                ];
                return false;
            }

            if ($this->saveMany($this->newEntities($providedExamSetups['ExamType'], ['validate' => 'default']))) {
                return $publishedCourseId;
            }

            $this->validationErrors['assessement'] = ['Something went wrong, please try again.'];
            return false;
        }

        return false;
    }

    public function getAssessmentDetailTypeRemedialMasterSheet($courseRegistrationId, $type = 1)
    {
        $resultDetail = [];

        if ($type == 1) {
            $publishedCourseId = TableRegistry::getTableLocator()->get('CourseRegistrations')
                ->find()
                ->select(['published_course_id'])
                ->where(['id' => $courseRegistrationId])
                ->first()
                ->published_course_id;

            $examTypes = $this->find()
                ->where(['published_course_id' => $publishedCourseId])
                ->order(['order' => 'ASC'])
                ->all();

            if (!empty($examTypes)) {
                $cnt = 0;
                foreach ($examTypes as $vex) {
                    $examResult = TableRegistry::getTableLocator()->get('ExamResults')
                        ->find()
                        ->where([
                            'course_registration_id' => $courseRegistrationId,
                            'exam_type_id' => $vex->id,
                        ])
                        ->first();

                    $resultDetail[$cnt]['ExamType'] = $vex->toArray();
                    $resultDetail[$cnt]['ExamResult'] = $examResult ? $examResult->toArray() : [];
                    $cnt++;
                }
            }
        } else {
            $publishedCourseId = TableRegistry::getTableLocator()->get('CourseAdds')
                ->find()
                ->select(['published_course_id'])
                ->where(['id' => $courseRegistrationId])
                ->first()
                ->published_course_id;

            $examTypes = $this->find()
                ->where(['published_course_id' => $publishedCourseId])
                ->order(['order' => 'ASC'])
                ->all();

            if (!empty($examTypes)) {
                $cnt = 0;
                foreach ($examTypes as $vex) {
                    $examResult = TableRegistry::getTableLocator()->get('ExamResults')
                        ->find()
                        ->where([
                            'course_add_id' => $courseRegistrationId,
                            'exam_type_id' => $vex->id,
                        ])
                        ->first();

                    $resultDetail[$cnt]['ExamType'] = $vex->toArray();
                    $resultDetail[$cnt]['ExamResult'] = $examResult ? $examResult->toArray() : [];
                    $cnt++;
                }
            }
        }

        return $resultDetail;
    }
}
