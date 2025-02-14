<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class StudentsSectionsTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('students_sections');
        $this->setPrimaryKey('id');

        // BelongsTo Associations
        $this->belongsTo('Sections', [
            'foreignKey' => 'section_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'INNER',
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('section_id')
            ->requirePresence('section_id', 'create')
            ->notEmptyString('section_id', 'Section ID is required');

        $validator
            ->integer('student_id')
            ->requirePresence('student_id', 'create')
            ->notEmptyString('student_id', 'Student ID is required');

        return $validator;
    }


    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['student_id'], 'Students'));
        $rules->add($rules->existsIn(['section_id'], 'Sections'));

        return $rules;
    }


    public function getMostRecentSectionPublishedCourseNotRegistered($student_id)
    {
        $section = $this->find('first', array(
            'conditions' => array(
                'StudentsSection.student_id' => $student_id,
                'StudentsSection.archive' => 0
            ),
            //'order' => array('StudentsSection.created' => 'DESC')
            'order' => array('StudentsSection.id' => 'DESC', 'StudentsSection.section_id' => 'DESC', 'StudentsSection.created' => 'DESC')
        ));

        if (isset($section['StudentsSection']) && !empty($section['StudentsSection']['section_id'])) {

            $publishedCourseNotRegistered = ClassRegistry::init('PublishedCourse')->find('all', array(
                'conditions' => array(
                    'PublishedCourse.section_id' => $section['StudentsSection']['section_id'],
                    'PublishedCourse.id not in (select id from course_registrations where student_id = ' . $student_id . ' and section_id =' . $section['StudentsSection']['section_id'] . ')'
                ),
                'contain' => array(
                    'Course' => array(
                        'Prerequisite' => array(
                            'id',
                            'prerequisite_course_id',
                            'co_requisite'
                        ),
                        'Curriculum' => array('id', 'name', 'type_credit', 'year_introduced', 'active'),
                        'fields' => array(
                            'Course.id',
                            'Course.course_code',
                            'Course.course_title',
                            'Course.lecture_hours',
                            'Course.tutorial_hours',
                            'Course.laboratory_hours',
                            'Course.credit'
                        )
                    )
                ),
                'order' => array('PublishedCourse.course_id' => 'ASC', 'PublishedCourse.created' => 'DESC')
            ));

            return $publishedCourseNotRegistered;
        }
        return array();
    }

    public function getStudentsListInSection($section_id, $name = null)
    {
        $list = array();

        $listStudents = $this->find('list', array(
            'conditions' => array(
                'StudentsSection.section_id' => $section_id
            ),
            'fields' => array(
                'StudentsSection.student_id',
                'StudentsSection.student_id'
            ),
            'group' => array(
                'StudentsSection.student_id',
                'StudentsSection.section_id'
            )
        ));

        debug($listStudents);

        if (!empty($listStudents)) {
            if (!empty($name)) {
                $list = ClassRegistry::init('Student')->find('all', array(
                    'conditions' => array(
                        'Student.id' => $listStudents,
                        'Student.first_name LIKE ' => $name . '%'
                    ),
                    'contain' => array('StudentsSection')
                ));
            } else {
                $list = ClassRegistry::init('Student')->find('all', array(
                    'conditions' => array(
                        'Student.id' => $listStudents,
                    ),
                    'contain' => array('StudentsSection')
                ));
            }
            return $list;
        }
        return $list;
    }

    public function getStudentsIdsInSection($section_id)
    {
        $listStudents = $this->find('list', array(
            'conditions' => array(
                'StudentsSection.section_id' => $section_id
            ),
            'fields' => array(
                'StudentsSection.student_id',
                'StudentsSection.student_id'
            ),
            'group' => array(
                'StudentsSection.student_id',
                'StudentsSection.section_id'
            )
        ));
        return $listStudents;
    }

    public function isSectionGraduated($section_id)
    {
        $studentIds = $this->getStudentsIdsInSection($section_id);

        if (empty($studentIds)) {
            return false;
        }

        $gradutingStudent = ClassRegistry::init('GraduateList')->find('count', array('conditions' => array('GraduateList.student_id' => $studentIds)));
        debug($gradutingStudent);

        if ($gradutingStudent > count($studentIds) / 3) {
            return true;
        } else {
            return false;
        }
    }

    public function doesTheStudentHasSection($student_id, $academicYear)
    {
        $studentSectionDetail = $this->find('all', array(
            'conditions' => array(
                'StudentsSection.student_id' => $student_id,
            ),
            'contain' => array('Section')
        ));

        $countD = 0;
        $selected = array();

        if(!empty($studentSectionDetail)) {
            foreach ($studentSectionDetail as $sk => $sv) {
                if ($sv['Section']['academicyear'] != $academicYear) {
                    $countD++;
                } else {
                    $selected = $sv;
                }
            }
        }

        debug($selected);

        if ($countD == 0) {
            return $selected; // if one semester
        } else if ($countD != 0) {
            return 1; //already in different academic not possible
        }
        return 2; //the student is not in section
    }

}
