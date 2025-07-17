<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\TableRegistry;

/**
 * ClassRooms Table
 */
class ClassRoomsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('class_rooms');
        $this->setDisplayField('room_code');
        $this->setPrimaryKey('id');

        $this->belongsTo('ClassRoomBlocks', [
            'foreignKey' => 'class_room_block_id',
            'joinType' => 'LEFT'
        ]);

        $this->hasMany('ProgramProgramTypeClassRooms', [
            'foreignKey' => 'class_room_id',
            'dependent' => false
        ]);

        $this->hasMany('ExamSchedules', [
            'foreignKey' => 'class_room_id',
            'dependent' => false
        ]);

        $this->hasMany('ClassRoomClassPeriodConstraints', [
            'foreignKey' => 'class_room_id',
            'dependent' => false
        ]);

        $this->hasMany('ClassRoomCourseConstraints', [
            'foreignKey' => 'class_room_id',
            'dependent' => false
        ]);

        $this->hasMany('ExamRoomConstraints', [
            'foreignKey' => 'class_room_id',
            'dependent' => false
        ]);

        $this->hasMany('ExamRoomCourseConstraints', [
            'foreignKey' => 'class_room_id',
            'dependent' => false
        ]);

        $this->hasMany('ExamRoomNumberOfInvigilators', [
            'foreignKey' => 'class_room_id',
            'dependent' => false
        ]);

        $this->hasMany('CourseSchedules', [
            'foreignKey' => 'class_room_id',
            'dependent' => false
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param Validator $validator Validator instance.
     * @return Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->allowEmptyString('id', null, 'create')
            ->scalar('room_code')
            ->requirePresence('room_code', 'create')
            ->notEmptyString('room_code', 'Room code should not be empty, Please provide valid room code.')
            ->add('room_code', 'unique', [
                'rule' => function ($value, $context) {
                    $classRoomBlocksTable = TableRegistry::getTableLocator()->get('ClassRoomBlocks');
                    $classRoomBlockData = $classRoomBlocksTable->sendClassRoomBlockData();
                    $classRoomBlock = $classRoomBlocksTable->find()
                        ->where([
                            'ClassRoomBlocks.campus_id' => $classRoomBlockData['ClassRoomBlock']['campus_id'] ?? null,
                            'ClassRoomBlocks.college_id' => $classRoomBlockData['ClassRoomBlock']['college_id'] ?? null,
                            'ClassRoomBlocks.block_code' => $classRoomBlockData['ClassRoomBlock']['block_code'] ?? null
                        ])
                        ->first();

                    if (empty($classRoomBlock)) {
                        return true;
                    }

                    $count = $this->find()
                        ->where([
                            'ClassRooms.class_room_block_id' => $classRoomBlock->id,
                            'ClassRooms.room_code' => $value
                        ])
                        ->count();

                    return $count === 0;
                },
                'message' => 'You have already entered room code. Please provide unique name.'
            ])
            ->numeric('lecture_capacity')
            ->allowEmptyNumber('lecture_capacity')
            ->add('lecture_capacity', 'numeric', [
                'rule' => 'numeric',
                'message' => 'Lecture capacity should only numeric. Please Provide class room lecture capacity in number.'
            ])
            ->numeric('exam_capacity')
            ->allowEmptyNumber('exam_capacity')
            ->add('exam_capacity', 'numeric', [
                'rule' => 'numeric',
                'message' => 'Exam capacity should only numeric. Please Provide class room Exam capacity in number.'
            ]);

        return $validator;
    }

    /**
     * Checks if a class room is used in related tables
     *
     * @param int|null $id Class room ID
     * @return array Result with usage status and error message
     */
    public function isClassRoomUsedInOtherTables($id = null): array
    {
        if (!$id) {
            return ['used' => false, 'error' => null];
        }

        $tables = [
            'ClassRoomClassPeriodConstraints' => 'class room class period constraints',
            'ClassRoomCourseConstraints' => 'class room course constraints',
            'ExamRoomCourseConstraints' => 'exam room course constraints',
            'ExamRoomConstraints' => 'exam room session constraints',
            'ExamRoomNumberOfInvigilators' => 'exam room number of invigilator',
            'CourseSchedules' => 'course schedule'
        ];

        foreach ($tables as $tableName => $errorMessage) {
            $table = TableRegistry::getTableLocator()->get($tableName);
            $count = $table->isClassRoomUsed($id);
            if ($count > 0) {
                return ['used' => true, 'error' => "The class room is used in {$errorMessage}."];
            }
        }

        return ['used' => false, 'error' => null];
    }

    /**
     * Retrieves class rooms available for an exam
     *
     * @param int|null $collegeId College ID
     * @param array|null $publishedCourseIds Published course IDs
     * @param int|null $sectionId Section ID
     * @param string|null $examDate Exam date (Y-m-d)
     * @param string|null $session Exam session
     * @param string|null $academicYear Academic year
     * @param string|null $semester Semester
     * @return array Available exam rooms
     */
    public function getClassRoomsForExam($collegeId = null, $publishedCourseIds = null, $sectionId = null, $examDate = null, $session = null, $academicYear = null, $semester = null): array
    {
        if (!$collegeId || empty($publishedCourseIds) || !$examDate || !$session || !$academicYear || !$semester) {
            return [];
        }

        $examRoomsAll = [];
        $sectionIds = [];

        $sectionSplitForExamsTable = TableRegistry::getTableLocator()->get('SectionSplitForExams');
        $studentsSectionsTable = TableRegistry::getTableLocator()->get('StudentsSections');
        $studentsExamSplitSectionsTable = TableRegistry::getTableLocator()->get('StudentsExamSplitSections');
        $publishedCoursesTable = TableRegistry::getTableLocator()->get('PublishedCourses');
        $examRoomCourseConstraintsTable = TableRegistry::getTableLocator()->get('ExamRoomCourseConstraints');

        if (count($publishedCourseIds) <= 1) {
            $checkSplit = $sectionSplitForExamsTable->find()
                ->where(['SectionSplitForExams.published_course_id' => $publishedCourseIds[0]])
                ->contain(['ExamSplitSections' => ['StudentsExamSplitSections']])
                ->first();

            if (!empty($checkSplit)) {
                foreach ($checkSplit->exam_split_sections as $splitSection) {
                    $sectionIds[] = ['id' => $splitSection->id, 'type' => 2];
                }
            } else {
                $sectionIds[] = ['id' => $sectionId, 'type' => 1];
            }
        } else {
            $sectionIdsTmp = $publishedCoursesTable->find()
                ->select(['section_id'])
                ->where(['PublishedCourses.id IN' => $publishedCourseIds])
                ->toArray();

            $sectionIds[] = [
                'id' => array_column($sectionIdsTmp, 'section_id'),
                'type' => 1
            ];
        }

        foreach ($sectionIds as $sectionIdData) {
            $examRooms = [];
            $sectionId = $sectionIdData['id'];
            $sectionType = $sectionIdData['type'];

            $numberOfStudents = $sectionType == 1
                ? $studentsSectionsTable->find()->where(['StudentsSections.section_id' => $sectionId])->count()
                : $studentsExamSplitSectionsTable->find()->where(['StudentsExamSplitSections.exam_split_section_id' => $sectionId])->count();

            $examRoomCourseConstraints = $examRoomCourseConstraintsTable->find()
                ->where(['ExamRoomCourseConstraints.published_course_id' => $publishedCourseIds[0]])
                ->contain([
                    'ClassRooms' => [
                        'ExamRoomNumberOfInvigilators' => [
                            'conditions' => [
                                'ExamRoomNumberOfInvigilators.academic_year' => $academicYear,
                                'ExamRoomNumberOfInvigilators.semester' => $semester
                            ]
                        ]
                    ]
                ])
                ->toArray();

            $examRoomCourseConstraintActive = false;
            foreach ($examRoomCourseConstraints as $constraint) {
                if ($constraint->active == 1) {
                    $examRoomCourseConstraintActive = true;
                    break;
                }
            }

            if ($examRoomCourseConstraintActive) {
                foreach ($examRoomCourseConstraints as $constraint) {
                    if ($constraint->active == 1 && !in_array($constraint->class_room_id, array_column($examRooms, 'id'))) {
                        $examRooms[] = [
                            'id' => $constraint->class_room_id,
                            'capacity' => $constraint->class_room->exam_capacity,
                            'number_of_invigilator' => !empty($constraint->class_room->exam_room_number_of_invigilators)
                                ? $constraint->class_room->exam_room_number_of_invigilators[0]->number_of_invigilator
                                : 0
                        ];
                    }
                }
            } else {
                $classRooms = $this->find()
                    ->where([
                        'ClassRooms.exam_capacity IS NOT NULL',
                        'ClassRooms.exam_capacity >=' => $numberOfStudents,
                        'ClassRoomBlocks.college_id' => $collegeId,
                        'ClassRooms.available_for_exam' => 1,
                        'ClassRooms.id NOT IN' => $this->ExamSchedules->find()
                            ->select(['class_room_id'])
                            ->where(['exam_date' => $examDate, 'session' => $session])
                    ])
                    ->order(['ClassRooms.exam_capacity' => 'ASC'])
                    ->contain([
                        'ClassRoomBlocks',
                        'ExamRoomNumberOfInvigilators' => [
                            'conditions' => [
                                'ExamRoomNumberOfInvigilators.academic_year' => $academicYear,
                                'ExamRoomNumberOfInvigilators.semester' => $semester
                            ]
                        ]
                    ])
                    ->toArray();

                foreach ($classRooms as $classRoom) {
                    $examRooms[] = [
                        'id' => $classRoom->id,
                        'capacity' => $classRoom->exam_capacity,
                        'number_of_invigilator' => !empty($classRoom->exam_room_number_of_invigilators)
                            ? $classRoom->exam_room_number_of_invigilators[0]->number_of_invigilator
                            : 0
                    ];
                }

                foreach ($examRoomCourseConstraints as $constraint) {
                    if ($constraint->active == 0) {
                        $searchKey = array_search($constraint->class_room_id, array_column($examRooms, 'id'));
                        if ($searchKey !== false) {
                            unset($examRooms[$searchKey]);
                        }
                    }
                }
                $examRooms = array_values($examRooms);
            }

            usort($examRooms, function ($a, $b) {
                return $a['capacity'] <=> $b['capacity'];
            });

            $examRoomsAll[] = [
                'section_id' => $sectionId,
                'exam_rooms' => $examRooms
            ];
        }

        return $examRoomsAll;
    }
}
