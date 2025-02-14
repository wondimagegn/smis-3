<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class GradeScaleDetailsTable extends Table
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

        $this->setTable('grade_scale_details');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('GradeScales', [
            'foreignKey' => 'grade_scale_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Grades', [
            'foreignKey' => 'grade_id',
            'joinType' => 'INNER',
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
            ->numeric('minimum_result', 'Numeric value required.')
            ->greaterThanOrEqual('minimum_result', 0, 'Point value must be greater than or equal to zero.')
            ->lessThanField('minimum_result', 'maximum_result', 'Minimum result should be less than maximum result.');

        $validator
            ->numeric('maximum_result', 'Numeric value required.')
            ->greaterThanOrEqual('maximum_result', 0, 'Maximum value must be greater than or equal to zero.')
            ->lessThanOrEqual('maximum_result', 100, 'Maximum value must be less than or equal to 100.')
            ->greaterThanField('maximum_result', 'minimum_result', 'Maximum result should be greater than minimum result.');

        $validator
            ->notEmptyString('grade_id', 'This field can\'t be left blank');

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
        $rules->add($rules->existsIn(['grade_scale_id'], 'GradeScales'));
        $rules->add($rules->existsIn(['grade_id'], 'Grades'));

        return $rules;
    }
    function checkUnique($data, $fieldName)
    {

        $already = array();
        $already[] = $this->data['GradeScaleDetail']['grade_id'];

        if (in_array($data['grade_id'], $already)) {

            return false;
        }
        return true;

    }

    function field_comparison($check1, $operator, $field2)
    {
        foreach ($check1 as $key => $value1) {
            $value2 = $this->data[$this->alias][$field2];
            if (!Validation::comparison($value1, $operator, $value2)){
                return false;
            }
        }
        return true;
    }

    function check_scale_execlusiveness($data = null, $role = null)
    {
        if ($role == ROLE_COLLEGE) {
            $already_recorded_range = $this->find('all', array(
                'conditions' => array(
                    'model' => 'GradeScale.College',
                    'GradeScale.active' => 1,
                    'GradeScale.foreign_key' => $data['GradeScale']['foreign_key'],
                    'GradeScale.program_id' => $data['GradeScale']['program_id']
                )
            ));
        } else if ($role == ROLE_DEPARTMENT) {
            $already_recorded_range = $this->GradeScale->find('all', array(
                'conditions' => array(
                    'GradeScale.model' => 'Department',
                    'GradeScale.active' => 1,
                    'GradeScale.foreign_key' => $data['GradeScale']['foreign_key'],
                    'GradeScale.program_id' => $data['GradeScale']['program_id']
                )
            ));
        }

        if (!empty($data['GradeScaleDetail'])) {
            foreach ($data['GradeScaleDetail'] as $kk => $vv) {
                if (empty($vv['minimum_result']) && empty($vv['maximum_result'])) {
                    return true;
                }
            }
        }

        if (!empty($already_recorded_range)) {
            foreach ($already_recorded_range as $ar => $sr) {
                foreach ($sr['GradeScaleDetail'] as $kkkk => $vvvv) {
                    foreach ($data['GradeScaleDetail'] as $k => $v) {
                        if (!empty($v['minimum_result']) && !empty($v['maximum_result'])) {
                            if (($v['minimum_result'] <= $vvvv['minimum_result'] && $vvvv['minimum_result'] <= $v['maximum_result']) ||
                                ($v['minimum_result'] <= $vvvv['maximum_result'] && $vvvv['maximum_result'] <= $v['maximum_result']) ||
                                ($vvvv['minimum_result'] <= $v['minimum_result'] && $v['minimum_result'] <= $vvvv['maximum_result'])
                            ) {
                                $this->invalidate('minimum_maximum_result', 'The given grade range is not unique. Please make sure that "Minimum result" and/or "Maximum" is  already recorded.');
                                return false;
                            }
                        }
                    }
                }
            }
        }
        return true;
    }

    // Model validation against continutiy

    function gradeRangeContinuty($data = null)
    {
        if (!empty($data)) {

            //find the grade based on grade type and sort by point value
            $grades = $this->Grade->find('all', array(
                'conditions' => array(
                    'Grade.grade_type_id' => $data['GradeScale']['grade_type_id']
                ),
                'order' => 'Grade.point_value desc',
                'contain' => array()
            ));

            //exit(1);
            $count = 0;
            $next_maximum = 0;
            $previous_minimum = 0;

            foreach ($grades as $grade_key => $grade_value) {
                // find grade
                foreach ($data['GradeScaleDetail'] as
                         $grade_scale => $grade_scale_detail) {
                    if (!empty($grade_scale_detail['maximum_result']) && !empty($grade_scale_detail['minimum_result'])) {
                        if ($grade_value['Grade']['id'] == $grade_scale_detail['grade_id']) {
                            if ($count == 0) {
                                if ($grade_scale_detail['maximum_result'] == 100) {
                                    $next_maximum = $grade_scale_detail['minimum_result'];
                                    $count++;
                                    break 1;
                                } else {
                                    $this->invalidate('grade_range_continuty', 'The maximum result  for ' . $grade_value['Grade']['grade'] . ' is 100');
                                    return false;
                                }
                            } else {
                                if ($grade_scale_detail['maximum_result'] == ($next_maximum - 0.01)) {
                                    $next_maximum = $grade_scale_detail['minimum_result'];
                                    $count++;
                                    break 1;
                                } else {
                                    $this->invalidate('grade_range_continuty', 'The next maximum  for ' . $grade_value['Grade']['grade'] . ' is should be ' . ($next_maximum - 0.01) . '');
                                    return false;
                                }
                            }
                        }
                    } else {
                        return true;
                    }
                }
                $count++;
            }
        }
        return true;
    }

    function checkGradeIsUnique($data = null)
    {
        if (!empty($data)) {

            $already_defined = null;

            $grades = $this->Grade->find('list', array(
                'conditions' => array(
                    'Grade.grade_type_id' => $data['GradeScale']['grade_type_id']
                ),
                'order' => 'Grade.point_value desc',
                'contain' => array(),
                'fields' => array('id', 'grade')
            ));

            $frequencey_count = array();

            // Count the frequency of grade repeation and display invalidation message if grade is duplicated
            if (!empty($data['GradeScaleDetail'])) {

                foreach ($data['GradeScaleDetail'] as $grade_id => $grade_value) {
                    $frequencey_count[] = $grade_value['grade_id'];
                }

                $how_many_times = array_count_values($frequencey_count);

                if (count($how_many_times) > 0) {
                    foreach ($how_many_times as $grade_id => $frequency) {
                        if ($frequency > 1) {
                            $this->invalidate('checkGradeIsUnique', 'Grade ' . $grades[$grade_id] . ' is duplicated ' . $frequency . ' times. Please change the grade.');
                            return false;
                        }
                    }
                    return true;
                } else {
                    return true;
                }
            }
            return true;

        }
        return false;
    }
}
