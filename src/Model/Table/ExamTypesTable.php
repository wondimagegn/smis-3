<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class ExamTypesTable extends Table
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

        $this->setTable('exam_types'); // Set database table name
        $this->setPrimaryKey('id'); // Define primary key
        $this->addBehavior('Timestamp');

        // Define relationships
        $this->belongsTo('PublishedCourses', [
            'foreignKey' => 'published_course_id'
        ]);

        $this->belongsTo('Sections', [
            'foreignKey' => 'section_id'
        ]);

        $this->hasMany('ExamResults', [
            'foreignKey' => 'exam_type_id'
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
            ->notEmptyString('exam_name', 'Please enter exam type.')
            ->notEmptyString('percent', 'Please enter the percentage.')
            ->numeric('percent', 'Please enter the percent in number.')
            ->greaterThanOrEqual('percent', 1, 'Percent can not be less than 1.')
            ->lessThanOrEqual('percent', 100, 'Percent can not be greater than 100.')
            ->allowEmptyString('order')
            ->numeric('order', 'Please enter the order in number.')
            ->greaterThan('order', 0, 'Order can not be less than 1.');

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
        $rules->add($rules->existsIn(['published_course_id'], 'PublishedCourses'));
        $rules->add($rules->existsIn(['section_id'], 'Sections'));

        return $rules;
    }

    function unset_empty_rows($data=null){
        if (!empty ($data['ExamType'])) {
            $skip_first_row = 0;
            foreach ($data['ExamType'] as $k => &$v) {
                if ($skip_first_row == 0) {
                    //
                } else {
                    if (empty ($v['exam_name']) && empty ($v['percent'])) {
                        unset($data['ExamType'][$k]);
                    }
                }
                $skip_first_row++;
            }
        }
        return $data;
    }

    function getExamType($publishedCourseId)
    {
        $examTypes = $this->find('all', array(
            'conditions' => array(
                'ExamType.published_course_id' => $publishedCourseId
            ),
            'contain' => array(
                'ExamResult',
                'PublishedCourse' => array(
                    'GivenByDepartment',
                    'YearLevel',
                    'Course' => array('Curriculum'),
                    'CourseInstructorAssignment' => array(
                        'Staff' => array('Department', 'Title', 'Position')
                    )
                )
            )
        ));

        return $examTypes;
    }

    function getExamTypeReport($acadamic_year = null, $semester = null, $program_id = null, $program_type_id = null, $department_id = null, $gender = null, $year_level_id = null, $continous_ass_number = 0)
    {
        $options = array();

        if (isset ($acadamic_year) && !empty ($acadamic_year)) {
            $options['conditions']['PublishedCourse.academic_year'] = $acadamic_year;
        }

        if (isset ($semester) && !empty ($semester)) {
            $options['conditions']['PublishedCourse.semester'] = $semester;
        }

        if (isset ($department_id) && !empty ($department_id)) {
            $college_id = explode('~', $department_id);
            if (count($college_id) > 1) {
                $options['conditions'][] = 'PublishedCourse.given_by_department_id  IN (SELECT id FROM departments where college_id="' . $college_id[1] . '")';
            } else {
                $options['conditions'][] = 'PublishedCourse.given_by_department_id =' . $department_id . '';
            }
        }

        if (isset ($program_id) && !empty ($program_id)) {
            $program_ids = explode('~', $program_id);
            if (count($program_ids) > 1) {
                //$options['conditions'][] = 'PublishedCourse.program_id='.$program_ids[1];
            } else {
                $options['conditions'][] = 'PublishedCourse.program_id=' . $program_id;
            }
        }

        if (isset ($program_type_id) && !empty ($program_type_id)) {
            $program_type_ids = explode('~', $program_type_id);
            if (count($program_type_ids) > 1) {
                //$options['conditions'][] = 'PublishedCourse.program_type_id='.$program_type_ids[1];
            } else {
                $options['conditions'][] = 'PublishedCourse.program_type_id=' . $program_type_id;
            }
        }

        if (isset ($year_level_id) && !empty ($year_level_id)) {
            $year_id = explode('~', $year_level_id);
            if (count($year_id) > 1) {
                //$options['conditions'][] = 'PublishedCourse.year_level_id  IN (SELECT id FROM year_levels where name="'..'")';
            } else {
                $options['conditions'][] = 'PublishedCourse.year_level_id  IN (SELECT id FROM year_levels where name="' . $year_level_id . '")';
            }
        }

        $options['contain'] = array(
            'CourseInstructorAssignment' => array('Staff' => array('Department')),
            'GivenByDepartment' => array('fields' => array('id', 'name')),
            'Course',
            'Program' => array('fields' => array('id', 'name')),
            'ProgramType' => array('fields' => array('id', 'name')),
            'YearLevel' => array('fields' => array('id', 'name')),
            'Section' => array('fields' => array('id', 'name')),
            'ExamType'
        );

        $publishedCourses = $this->PublishedCourse->find('all', $options);
        $instructors = array();

        if (!empty ($publishedCourses)) {
            foreach ($publishedCourses as $k => $v) {
                foreach ($v['CourseInstructorAssignment'] as $ca => $cv) {
                    if (!empty ($continous_ass_number) && count($v['ExamType']) == $continous_ass_number) {
                        if ($cv['type'] == 'Lecture') {
                            $instructors[$cv['Staff']['Department']['name'] . '~' . $cv['Staff']['full_name'] . '~' . $v['Course']['course_title'] . '(' . $v['Course']['course_code'] . '-' . $v['Course']['credit'] . ')' . '~' . 'p_id' . $v['PublishedCourse']['id']] = count($v['ExamType']);
                        }
                    } else if ($continous_ass_number == 0) {
                        if ($cv['type'] == 'Lecture') {
                            $instructors[$cv['Staff']['Department']['name'] . '~' . $cv['Staff']['full_name'] . '~' . $v['Course']['course_title'] . '(' . $v['Course']['course_code'] . '-' . $v['Course']['credit'] . ')' . '~' . 'p_id' . $v['PublishedCourse']['id']] = count($v['ExamType']);
                        }
                    }
                }
            }
        }

        return $instructors;
    }

    function getAssessementDetailType($course_registration_id, $type = 1)
    {
        $resultDetail = array();

        if ($type == 1) {
            $published_course_id = $this->PublishedCourse->CourseRegistration->field('CourseRegistration.published_course_id', array('CourseRegistration.id' => $course_registration_id));
            $examTypes = $this->find('all', array('conditions' => array('ExamType.published_course_id' => $published_course_id)));
            $resultDetail = array();

            if (!empty ($examTypes)) {
                foreach ($examTypes as $vex) {
                    $resultDetail[$vex['ExamType']['exam_name'] . '(' . $vex['ExamType']['percent'] . '%)'] = ClassRegistry::init('ExamResult')->field('ExamResult.result', array('ExamResult.course_registration_id' => $course_registration_id, 'ExamResult.exam_type_id' => $vex['ExamType']['id']));
                }
            }
        } else {

            $published_course_id = $this->PublishedCourse->CourseAdd->field('CourseAdd.published_course_id', array('CourseAdd.id' => $course_registration_id));
            $examTypes = $this->find('all', array('conditions' => array('ExamType.published_course_id' => $published_course_id)));
            $resultDetail = array();

            if (!empty ($examTypes)) {
                foreach ($examTypes as $vex) {
                    $resultDetail[$vex['ExamType']['exam_name'] . '(' . $vex['ExamType']['percent'] . '%)'] = ClassRegistry::init('ExamResult')->field('ExamResult.result', array('ExamResult.course_add_id' => $course_registration_id, 'ExamResult.exam_type_id' => $vex['ExamType']['id']));
                }
            }

        }

        return $resultDetail;
    }

    // 1. exam setup is already created
    function examSetupCreation($publishedCourseId, $givenSetup)
    {
        // $examTypes = $this->find('all', array('conditions' => array('ExamType.published_course_id' => $publishedCourseId), 'recursive' => -1));

        $providedExamSetups = array();
        $count = 0;
        debug($givenSetup);

        if (!empty($givenSetup)) {
            foreach ($givenSetup as $k => $v) {
                if ($k == 0) {
                    continue;
                } else {
                    $asstype = array();
                    $asstype = explode('-', $v);

                    // check if the assessement is found and have proper percent
                    if (isset($asstype[1]) && !empty($asstype[1])) {
                        $examTypes = $this->find('first', array(
                            'conditions' => array(
                                'ExamType.published_course_id' => $publishedCourseId,
                                'ExamType.percent' => trim($asstype[1]),
                                'ExamType.exam_name' => trim($asstype[0])
                            ),
                            'recursive' => -1
                        ));

                        if (empty($examTypes)) {
                            debug($asstype[0]);
                            debug($asstype[1]);
                            debug($publishedCourseId);
                            debug($examTypes);
                            debug($asstype);
                        }

                        // check if the provide assessement is match with the system assessement
                        if (isset($examTypes) && !empty($examTypes)) {
                            // assessement is fine, so do nothing
                            $providedExamSetups['ExamType'][$count] = $examTypes;
                        } else {
                            // nothing is defined, define it and put it in array, check if percent is number
                            if (!is_numeric($asstype[1])) {
                                $this->invalidate('assessement', 'Please provide the percent "' . $asstype[1] . '" in number. If you put "%" in the weight please remove it and put only the number');
                                return false;
                            }

                            $providedExamSetups['ExamType'][$count]['ExamType']['exam_name'] = trim($asstype[0]);
                            $providedExamSetups['ExamType'][$count]['ExamType']['percent'] = trim($asstype[1]);
                            $providedExamSetups['ExamType'][$count]['ExamType']['order'] = $count + 1;
                            $providedExamSetups['ExamType'][$count]['ExamType']['published_course_id'] =  $publishedCourseId;
                        }

                    } else if (isset($asstype[0]) && !empty($asstype[0]) && !isset($asstype[1])) {
                        // the provided excel doesnt have percent, please put the weight of the assessement  after minus(-)
                        $this->invalidate('assessement', 'The assessement "' . $asstype[0] . '" doesn\'t have weight, please provide the weight for assessement after its name separated by - the weight of the assessment without percent.');
                        return false;
                    }
                }

                $count++;
            }
        }

        if (isset($providedExamSetups['ExamType']) && !empty($providedExamSetups['ExamType'])) {
            $totalWeight = 0;
            foreach ($providedExamSetups['ExamType'] as $ek => $ev) {
                $totalWeight += $ev['ExamType']['percent'];
            }

            if ($totalWeight < 100 || $totalWeight > 100) {
                $this->invalidate('assessement', 'The current total assessement  weight is ' . $totalWeight . ' it must be 100.');
                return false;
            }

            // if everthing is fine do the assessement creation and return the published course id
            if ($this->saveAll($providedExamSetups['ExamType'], array('validate' => 'first'))) {
                return $publishedCourseId;
            } else {
                //$error = $this->invalidFields();
                //debug($error);
                $this->invalidate('assessement', 'Something went wrong, please try again.');
                return false;
            }
        }
        return false;
    }

    function getAssessementDetailTypeRemedialMasterSheet($course_registration_id, $type = 1)
    {
        $resultDetail = array();

        if ($type == 1) {
            $published_course_id = $this->PublishedCourse->CourseRegistration->field('CourseRegistration.published_course_id', array('CourseRegistration.id' => $course_registration_id));
            $examTypes = $this->find('all', array('conditions' => array('ExamType.published_course_id' => $published_course_id), 'order' => array('ExamType.order')));
            $resultDetail = array();

            if (!empty ($examTypes)) {
                $cnt = 0;
                foreach ($examTypes as $vex) {
                    //$resultDetail[$vex['ExamType']['exam_name'] . '(' . $vex['ExamType']['percent'] . '%)'] = ClassRegistry::init('ExamResult')->field('ExamResult.result', array('ExamResult.course_registration_id' => $course_registration_id, 'ExamResult.exam_type_id' => $vex['ExamType']['id']));
                    $examRslt = ClassRegistry::init('ExamResult')->find('first', array('conditions' => array('ExamResult.course_registration_id' => $course_registration_id, 'ExamResult.exam_type_id' => $vex['ExamType']['id'])));
                    $resultDetail[$cnt]['ExamType'] = $vex['ExamType'];
                    $resultDetail[$cnt]['ExamResult'] = (!empty($examRslt['ExamResult']) ? $examRslt['ExamResult'] : array());
                    $cnt++;
                }
            }
        } else {

            $published_course_id = $this->PublishedCourse->CourseAdd->field('CourseAdd.published_course_id', array('CourseAdd.id' => $course_registration_id));
            $examTypes = $this->find('all', array('conditions' => array('ExamType.published_course_id' => $published_course_id), 'order' => array('ExamType.order')));
            $resultDetail = array();

            if (!empty ($examTypes)) {
                $cnt = 0;
                foreach ($examTypes as $vex) {
                    //$resultDetail[$vex['ExamType']['exam_name'] . '(' . $vex['ExamType']['percent'] . '%)'] = ClassRegistry::init('ExamResult')->field('ExamResult.result', array('ExamResult.course_add_id' => $course_registration_id, 'ExamResult.exam_type_id' => $vex['ExamType']['id']));
                    $examRslt = ClassRegistry::init('ExamResult')->find('first', array('conditions' => array('ExamResult.course_add_id' => $course_registration_id, 'ExamResult.exam_type_id' => $vex['ExamType']['id'])));
                    $resultDetail[$cnt]['ExamType'] = $vex['ExamType'];
                    $resultDetail[$cnt]['ExamResult'] = (!empty($examRslt['ExamResult']) ? $examRslt['ExamResult'] : array());
                    $cnt++;
                }
            }

        }

        return $resultDetail;
    }
}
