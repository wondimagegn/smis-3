<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class StudentStatusPatternsTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('student_status_patterns');
        $this->setPrimaryKey('id');

        // BelongsTo Associations
        $this->belongsTo('Programs', [
            'foreignKey' => 'program_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('ProgramTypes', [
            'foreignKey' => 'program_type_id',
            'joinType' => 'INNER',
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('program_id')
            ->requirePresence('program_id', 'create')
            ->notEmptyString('program_id', 'Program ID is required');

        $validator
            ->integer('program_type_id')
            ->requirePresence('program_type_id', 'create')
            ->notEmptyString('program_type_id', 'Program Type ID is required');

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
        $rules->add($rules->existsIn(['program_id'], 'Programs'));
        $rules->add($rules->existsIn(['program_type_id'], 'ProgramTypes'));

        return $rules;
    }


    function getProgramTypePattern($program_id = null, $program_type_id = null, $acadamic_year = null)
    {
        $status_patterns = $this->find('all', array(
            'conditions' => array(
                'StudentStatusPattern.program_id' => $program_id,
                'StudentStatusPattern.program_type_id' => $program_type_id
            ),
            'order' => array('StudentStatusPattern.application_date' => 'ASC'),
            'recursive' => -1
        ));

        if (!empty($status_patterns)) {
            $pattern = $status_patterns[0]['StudentStatusPattern']['pattern'];
            $sys_acadamic_year = $status_patterns[0]['StudentStatusPattern']['acadamic_year'];
            //If it is introduced latelly
            if (substr($sys_acadamic_year, 0, 4) > substr($acadamic_year, 0, 4)) {
                return 1;
            } else {
                do {
                    foreach ($status_patterns as $key => $status_pattern) {
                        if ($sys_acadamic_year == $status_pattern['StudentStatusPattern']['acadamic_year']) {
                            $pattern = $status_pattern['StudentStatusPattern']['pattern'];
                        }
                    }

                    if (strcasecmp($acadamic_year, $sys_acadamic_year) != 0) {
                        $sys_acadamic_year = (substr($sys_acadamic_year, 0, 4) + 1) . '/' . substr((substr($sys_acadamic_year, 0, 4) + 2), 2, 2);
                    } else {
                        return $pattern;
                    }
                } while ($sys_acadamic_year != '3000/01');
            }
            return $pattern;
        } else {
            return 1;
        }
    }

    function isLastSemesterInCurriculum($student_id)
    {
        $minimumPointofCurriculum = ClassRegistry::init('Student')->find('first', array(
            'conditions' => array(
                'Student.id' => $student_id
            ),
            'contain' => array(
                'Curriculum'
            )
        ));

        if (!empty($minimumPointofCurriculum) && empty($minimumPointofCurriculum['Student']['curriculum_id'])) {
            return false;
        }

        debug($minimumPointofCurriculum['Student']['curriculum_id']);

        $last_year_level_id = ClassRegistry::init('Course')->find('list', array('conditions' => array('Course.curriculum_id' => $minimumPointofCurriculum['Student']['curriculum_id']), 'group' => array('Course.year_level_id', 'Course.semester'), 'order' => array('Course.year_level_id' => 'DESC', 'Course.semester' => 'DESC'), 'fields' => array('Course.year_level_id', 'Course.year_level_id'), 'limit' => 1));
        debug($last_year_level_id);

        $allAdded = ClassRegistry::init('CourseAdd')->find('all', array(
            'conditions' => array(
                'CourseAdd.student_id' => $student_id,
                'CourseAdd.department_approval' => 1,
                'CourseAdd.registrar_confirmation' => 1,
            ),
            'contain' => array(
                'PublishedCourse' => array(
                    'Course' => array(
                        'CourseCategory',
                        'Curriculum'
                    )
                )
            )
        ));

        $allRegistered = ClassRegistry::init('CourseRegistration')->find('all', array(
            'conditions' => array(
                'CourseRegistration.student_id' => $student_id,
            ),
            'contain' => array(
                'PublishedCourse' => array(
                    'Course' => array(
                        'CourseCategory',
                        'Curriculum'
                    )
                )
            )
        ));

        $check_registered_last_year_level_courses_from_curriculum = ClassRegistry::init('CourseRegistration')->find('count', array(
            'conditions' => array(
                'CourseRegistration.student_id' => $student_id,
                'CourseRegistration.year_level_id' => $last_year_level_id
            )
        ));

        debug(
            $minimumPointofCurriculum['Student']['full_name_studentnumber'] . ' (DB ID: ' . $minimumPointofCurriculum['Student']['id'] . ')'
        );

        if ($check_registered_last_year_level_courses_from_curriculum) {
            debug($check_registered_last_year_level_courses_from_curriculum);

            $lastCreditSum = 0;

            if (!empty($allRegistered)) {
                foreach ($allRegistered as $lk => $lv) {
                    if (isset($lv['PublishedCourse']['Course']['credit']) && !empty($lv['PublishedCourse']['Course']['credit'])) {
                        $lastCreditSum += $lv['PublishedCourse']['Course']['credit'];
                    }
                }
            }

            if (!empty($allAdded)) {
                foreach ($allAdded as $lk => $lv) {
                    if (isset($lv['PublishedCourse']['Course']['credit']) && !empty($lv['PublishedCourse']['Course']['credit'])) {
                        $lastCreditSum += $lv['PublishedCourse']['Course']['credit'];
                    }
                }
            }

            debug($minimumPointofCurriculum['Curriculum']['minimum_credit_points']);
            debug($lastCreditSum);

            if ($lastCreditSum >= $minimumPointofCurriculum['Curriculum']['minimum_credit_points']) {
                //debug($minimumPointofCurriculum['Student']['full_name_studentnumber'] . ' (DB ID: '. $minimumPointofCurriculum['Student']['id'].')' . ' completed minimum required credits & took last year courses from curriculum');
                return true;
            } else {
                debug(
                    $minimumPointofCurriculum['Student']['full_name_studentnumber'] . ' (DB ID: ' . $minimumPointofCurriculum['Student']['id'] . ')' . ' doesnot completed minimum required credits but took last year courses from curriculum'
                );
            }
        } else {
            //debug($minimumPointofCurriculum['Student']['full_name_studentnumber'] . ' (DB ID: '. $minimumPointofCurriculum['Student']['id'].')' . ' doesnot took any last year courses from curriculum');
        }

        return false;
    }
}
