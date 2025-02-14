<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class EquivalentCoursesTable extends Table
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

        $this->setTable('equivalent_courses'); // Set database table name
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        // Define relationships
        $this->belongsTo('CoursesForSubstituted', [
            'className' => 'Courses',
            'foreignKey' => 'course_for_substitued_id'
        ]);

        $this->belongsTo('CoursesBeSubstituted', [
            'className' => 'Courses',
            'foreignKey' => 'course_be_substitued_id'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

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
        $rules->add($rules->existsIn(['course_for_substitued_id'], 'CourseForSubstitueds'));
        $rules->add($rules->existsIn(['course_be_substitued_id'], 'CourseBeSubstitueds'));

        return $rules;
    }

    function isSimilarCurriculum($data = null)
    {
        if (empty($data['EquivalentCourse']['curriculum_id']) || empty($data['EquivalentCourse']['curriculum_id'])) {
            return true;
        }
        //other_curriculum_id
        if (!empty($data['EquivalentCourse']['curriculum_id']) && !empty($data['EquivalentCourse']['other_curriculum_id'])) {
            if ($data['EquivalentCourse']['curriculum_id'] == $data['EquivalentCourse']['other_curriculum_id']) {
                $this->invalidate('error', 'You are trying to map similar curriculum courses. You can not map similar curriculum courses.');
                return false;
            }
        }
        return true;
    }

    // Do not allow deletion of mapped course if the equivalent course has
    function checkStudentTakeingEquivalentCourseAndDenyDelete($id = null, $department_id = null)
    {
        $equivalent_course_id = $this->field(
            'course_be_substitued_id',
            array('EquivalentCourse.id' => $id)
        );

        $curriculum_id = ClassRegistry::init('Course')->field('curriculum_id', array('Course.id' => $equivalent_course_id));

        $course_ids = ClassRegistry::init('Course')->find('list', array(
            'conditions' => array('Course.curriculum_id' => $curriculum_id),
            'fields' => array('Course.id', 'Course.id')
        ));

        $published_course_ids = ClassRegistry::init('PublishedCourse')->find('list', array(
            'conditions' => array(
                'PublishedCourse.course_id' => $course_ids,
                'PublishedCourse.department_id' => $department_id
            ),
            'fields' => array('PublishedCourse.id', 'PublishedCourse.id')
        ));

        if (!empty($published_course_ids)) {
            foreach ($published_course_ids as $in => $pu) {
                $grade_submitted = ClassRegistry::init('ExamGrade')->is_grade_submitted($pu);
                if ($grade_submitted > 0) {
                    return false;
                }
            }
        }

        return true;
    }

    function equivalentCreditOfCourse($course_id, $studentAttachedCurriculum)
    {
        if ($studentAttachedCurriculum) {
            $courseCredit = ClassRegistry::init('Course')->find('first', array(
                'conditions' => array(
                    'Course.curriculum_id' => $studentAttachedCurriculum,
                    'Course.id' => $course_id
                )
            ));
        } else {
            $courseCredit = array();
        }

        if (!empty($courseCredit)) {
            return $courseCredit['Course']['credit'];
        } else {
            // does it have equivalence
            $equivalentCourseIds = $this->find('list', array(
                'conditions' => array(
                    'EquivalentCourse.course_be_substitued_id' => $course_id
                ),
                'fields' => array('course_for_substitued_id', 'course_for_substitued_id')
            ));

            if (!empty($equivalentCourseIds)) {
                if ($studentAttachedCurriculum) {
                    $courseCredit = ClassRegistry::init('Course')->find('first', array(
                        'conditions' => array(
                            'Course.curriculum_id' => $studentAttachedCurriculum,
                            'Course.id' => $equivalentCourseIds
                        )
                    ));
                } else {
                    $courseCredit = array();
                }

                if (!empty($courseCredit)) {
                    return $courseCredit['Course']['credit'];
                }
            }
        }
        return 0;
    }

    function checkCourseHasEquivalentCourse($course_id, $studentAttachedCurriculum)
    {
        if ($studentAttachedCurriculum) {
            $doesItExistInAttachedCurriculum = ClassRegistry::init('Course')->field('id', array(
                'Course.curriculum_id' => $studentAttachedCurriculum,
                'Course.id' => $course_id
            ));
        } else {
            $doesItExistInAttachedCurriculum = '';
        }

        if (!empty($doesItExistInAttachedCurriculum)) {
            return true;
        } else {
            // does it have equivalence
            $equivalentCourseIds = $this->find('list', array(
                'conditions' => array(
                    'EquivalentCourse.course_be_substitued_id' => $course_id
                ),
                'fields' => array('course_for_substitued_id', 'course_for_substitued_id')
            ));

            //debug($course_id);
            //debug($equivalentCourseIds);

            if (!empty($equivalentCourseIds)) {

                if ($studentAttachedCurriculum) {
                    $doesItExistInAttachedCurriculum = ClassRegistry::init('Course')->field('id', array(
                        'Course.curriculum_id' => $studentAttachedCurriculum,
                        'Course.id' => $equivalentCourseIds
                    ));
                } else {
                    $doesItExistInAttachedCurriculum = '';
                }

                if (!empty($doesItExistInAttachedCurriculum)) {
                    return true;
                }
                return false;
            }
        }
        return false;
    }

    function validEquivalentCourse($course_id, $studentAttachedCurriculum, $type = 1)
    {
        if ($studentAttachedCurriculum) {
            $doesItExistInAttachedCurriculum = ClassRegistry::init('Course')->field('id', array(
                'Course.curriculum_id' => $studentAttachedCurriculum,
                'Course.id' => $course_id
            ));
        } else {
            $doesItExistInAttachedCurriculum = 0;
        }
        //debug($doesItExistInAttachedCurriculum);

        // does it have equivalence
        if ($doesItExistInAttachedCurriculum) {

            $equivalentCourseIds1 = $this->find('list', array(
                'conditions' => array(
                    'EquivalentCourse.course_for_substitued_id' => $course_id
                ),
                'fields' => array('course_be_substitued_id', 'course_be_substitued_id')
            ));

            $equivalentCourseIds = $equivalentCourseIds1;

            if (!empty($equivalentCourseIds)) {
                $courseLists = ClassRegistry::init('Course')->find('list', array(
                    'conditions' => array(
                        'Course.id' => $equivalentCourseIds
                    ),
                    'fields' => array('id', 'id')
                ));
                //debug($courseLists);
                return $courseLists;
            }
        } else {

            $equivalentCourseIds1 = $this->find('list', array(
                'conditions' => array(
                    'EquivalentCourse.course_be_substitued_id' => $course_id
                ),
                'fields' => array('course_for_substitued_id', 'course_for_substitued_id')
            ));

            $equivalentCourseIds = $equivalentCourseIds1;

            if (!empty($equivalentCourseIds)) {
                if ($studentAttachedCurriculum) {
                    $courseLists = ClassRegistry::init('Course')->find('list', array(
                        'conditions' => array(
                            'Course.curriculum_id' => $studentAttachedCurriculum,
                            'Course.id' => $equivalentCourseIds
                        ),
                        'fields' => array('id', 'id')
                    ));
                } else {
                    $courseLists = array();
                }

                if (isset($courseLists) && !empty($courseLists)) {
                    $equivalentCourseIds1 = $this->find('list', array(
                        'conditions' => array(
                            'EquivalentCourse.course_for_substitued_id' => $courseLists
                        ),
                        'fields' => array('course_be_substitued_id', 'course_be_substitued_id')
                    ));
                }

                $merged = $courseLists + $equivalentCourseIds1;
                //debug($merged);
                return $merged;
                // return $equivalentCourseIds;
            }
        }
        return array();
    }

    function courseEquivalentCategory($course_id, $studentAttachedCurriculum)
    {
        $equivalentCourseIds = $this->find('list', array(
            'conditions' => array(
                'EquivalentCourse.course_be_substitued_id' => $course_id
            ),
            'fields' => array('course_for_substitued_id', 'course_for_substitued_id')
        ));

        //debug($equivalentCourseIds);
        //debug($course_id);

        if ($studentAttachedCurriculum) {
            $course = ClassRegistry::init('Course')->find('first', array(
                'conditions' => array(
                    'Course.curriculum_id' => $studentAttachedCurriculum,
                    'Course.id' => $equivalentCourseIds
                ),
                'contain' => array('CourseCategory')
            ));

            //debug($course['CourseCategory']['name']);
            if (!empty($course)) {
                return $course['CourseCategory']['name'];
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    function isEquivalentCourseMajor($course_id, $studentAttachedCurriculum)
    {
        $equivalentCourseIds = $this->find('list', array(
            'conditions' => array(
                'EquivalentCourse.course_be_substitued_id' => $course_id
            ),
            'fields' => array('course_for_substitued_id', 'course_for_substitued_id')
        ));

        if ($studentAttachedCurriculum) {
            $course = ClassRegistry::init('Course')->find('first', array(
                'conditions' => array(
                    'Course.curriculum_id' => $studentAttachedCurriculum,
                    'Course.id' => $equivalentCourseIds
                ),
                'contain' => array('CourseCategory')
            ));

            if (isset($course['Course']) && !empty($course['Course']) && $course['Course']['major']) {
                return 1;
            }
        }

        return 0;
    }

}
