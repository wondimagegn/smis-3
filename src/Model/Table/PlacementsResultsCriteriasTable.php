<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class PlacementsResultsCriteriasTable extends Table
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

        $this->setTable('placements_results_criterias');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Colleges', [
            'foreignKey' => 'college_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('ReservedPlaces', [
            'foreignKey' => 'placements_results_criteria_id',
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
            ->scalar('name')
            ->maxLength('name', 20)
            ->allowEmptyString('name');

        $validator
            ->scalar('admissionyear')
            ->maxLength('admissionyear', 255)
            ->requirePresence('admissionyear', 'create')
            ->notEmptyString('admissionyear');

        $validator
            ->boolean('prepartory_result')
            ->notEmptyString('prepartory_result');

        $validator
            ->numeric('result_from')
            ->requirePresence('result_from', 'create')
            ->notEmptyString('result_from');

        $validator
            ->numeric('result_to')
            ->requirePresence('result_to', 'create')
            ->notEmptyString('result_to');

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

        $rules->add($rules->existsIn(['college_id'], 'Colleges'));

        return $rules;
    }

    /**
     * method to check result criteria is recored for the given academic year and
     * college
     */
    public function isPlacementResultRecorded($academicyear = null, $college_id = null)
    {

        if ($college_id) {
            $isRecorded = $this->find('count', array(
                'conditions' =>
                    array(
                        'college_id' => $college_id,
                        'PlacementsResultsCriteria.admissionyear LIKE' => $academicyear . '%'
                    )
            ));

            if ($isRecorded) {
                return 1;
            } else {
                return 0;
            }
        }
    }

    /**
     * Method to check reserved place is recored for the given academic year and
     * college
     * return boolean true or false
     */
    public function isReservedPlaceRecorded($academicyear = null, $college_id = null)
    {

        if ($college_id) {
            $isRecorded = $this->ReservedPlace->find('all', array(
                'conditions' =>
                    array(
                        'ReservedPlace.college_id' => $college_id,
                        'ReservedPlace.academicyear LIKE' => $academicyear . '%'
                    )
            ));

            if ($isRecorded) {
                return 1;
            } else {
                return 0;
            }
        }
    }

    /**
     * Method to check reserved place is recored for the given academic year and
     * college
     * return boolean true or false
     */
    public function isParticipationgDepartmentRecorded($academicyear = null, $college_id = null)
    {

        if ($college_id) {
            $isRecorded = $this->ReservedPlace->ParticipatingDepartment->find('count', array(
                'conditions' =>
                    array(
                        'ParticipatingDepartment.college_id' => $college_id,
                        'ParticipatingDepartment.academic_year  LIKE' => $academicyear . '%'
                    )
            ));

            if ($isRecorded) {
                return 1;
            } else {
                return 0;
            }
        }
    }

    /**
     * Method to return what result will be considered for selection
     * prepartory or freshman
     */
    public function isPrepartoryResult($academicyear = null, $college_id = null)
    {

        if ($college_id) {
            $isDefined = $this->find(
                'first',
                array('conditions' => array('admissionyear LIKE' => $academicyear . '%', 'college_id' => $college_id))
            );

            $isPrepartory = $this->find('count', array(
                'conditions' => array(
                    'admissionyear LIKE' => $academicyear . '%',
                    'college_id' => $college_id,
                    'prepartory_result' => 1
                )
            ));

            if ($isPrepartory) {
                return 1;
            } else {
                if (
                    isset($isDefined)
                    && !empty($isDefined)
                ) {
                    return 0;
                } else {
                    return 1;
                }
            }
        }
        return 1;
    }

    public function isPrepartoryResult2($academicyear = null, $college_id = null)
    {

        if ($college_id) {
            $isPrepartory = $this->find('first', array(
                'conditions' => array(
                    'admissionyear LIKE' => $academicyear . '%',
                    'college_id' => $college_id
                )
            ));
            if (empty($isPrepartory)) {
                return -1;
            } else {
                if ($isPrepartory['PlacementsResultsCriteria']['prepartory_result'] == 1) {
                    return 1;
                } else {
                    return 0;
                }
            }
        }
        return null;
    }

    /**
     * Method to return the reserved place for each result category
     *
     * @return array
     */
    public function reservedPlaceCategory($academicyear = null, $college_id = null, $department_id = null)
    {

        if ($college_id) {
            $result = $this->ReservedPlace->find('all', array(
                'conditions' => array(
                    'ReservedPlace.college_id' => $college_id,
                    'ReservedPlace.academicyear LIKE' => $academicyear . '%',
                    'ReservedPlace.participating_department_id' => $department_id
                ),
                'recursive' => 1
            ));
            return $result;
        }
    }

    /**
     * Method to return the reserved place for each result category
     *
     * @return array
     */
    public function reservedPlaceForDepartmentByGradeRange(
        $academicyear = null,
        $college_id = null,
        $department_id = null
    ) {

        if ($college_id) {
            $result = $this->ReservedPlace->find('all', array(
                'conditions' => array(
                    'ReservedPlace.college_id' => $college_id,
                    'ReservedPlace.academicyear LIKE' => $academicyear . '%',
                    'ReservedPlace.participating_department_id' => $department_id
                ),
                'recursive' => 1
            ));
            return $result;
        }
    }

    public function resultCategoryInput($data = null, $max = null, $min = null)
    {

        $maxtmp = 0;
        $mintmp = 0;
        // debug($data);
        if (!$max || !$min) {
            $result_type = $data['prepartory_result'];
            $is_preparatory = null;
            if ($result_type) {
                $is_preparatory = 'EHEECE_total_results';
            } else {
                $is_preparatory = 'freshman_result';
            }

            $maxtmp = ClassRegistry::init('AcceptedStudent')->find(
                'first',
                array(
                    'fields' => array("MAX(" . $is_preparatory . ")"),
                    'conditions' => array(
                        'AcceptedStudent.college_id' =>
                            $data['college_id'],
                        'AcceptedStudent.academicyear' =>
                            $data['admissionyear']
                    )
                )
            );
            $mintmp = ClassRegistry::init('AcceptedStudent')->find(
                'first',
                array(
                    'fields' => array("MIN(" . $is_preparatory . ")"),
                    'conditions' => array(
                        'AcceptedStudent.college_id' => $data['college_id'],
                        'AcceptedStudent.academicyear' =>
                            $data['admissionyear']
                    )
                )
            );
            $maxtmp = $maxtmp[0]['MAX(' . $is_preparatory . ')'];
            $mintmp = $mintmp[0]['MIN(' . $is_preparatory . ')'];
        } else {
            $maxtmp = $max;
            $mintmp = $min;
        }

        if (empty($data['name'])) {
            $this->invalidate(
                'result_criteria_name',
                'Please enter the name for the result category.'
            );
            return false;
        } elseif (empty($data['result_from'])) {
            $this->invalidate(
                'result_from',
                'Please enter the result from.'
            );
            return false;
        } elseif (empty($data['result_to'])) {
            $this->invalidate(
                'result_to',
                'Please enter the result to'
            );
            return false;
        }
        if (!$this->checkUnique($data)) {
            $this->invalidate('result_criteria_name', 'The name should be unique, please change to other name.');
            return false;
        }

        if (!empty($data['result_from']) && !empty($data['result_to'])) {
            if ($maxtmp != "" && $mintmp != "") {
                if ($data['result_from'] > $maxtmp || $data['result_from'] < $mintmp) {
                    $this->invalidate(
                        'result_from_problem',
                        'The "result from" should be less than or equal to ' . $maxtmp . '
	                    result and greather than or equal to ' . $mintmp . '.'
                    );
                    return false;
                }

                if ($data['result_to'] > $maxtmp || $data['result_to'] < $mintmp) {
                    $this->invalidate(
                        'result_to_problem',
                        'The "result to" should be less
	                  than or equal to ' . $maxtmp . '  and greater than or equal to ' . $mintmp . '.'
                    );
                    return false;
                }
            }
        }

        if ($data['result_to'] < $data['result_from']) {
            $this->invalidate(
                'result_from_to',
                'The "result from" should be less than the "result to".'
            );
            return false;
        }
        $check_no_entry = $this->find(
            'count',
            array(
                'conditions' => array(
                    'PlacementsResultsCriteria.college_id' => $data['college_id'],
                    'PlacementsResultsCriteria.admissionyear' => $data['admissionyear']
                )
            )
        );
        if ($check_no_entry != 0) {
            $already_recorded_range = $this->find(
                'all',
                array(
                    'conditions' => array(
                        'PlacementsResultsCriteria.college_id' => $data['college_id'],
                        'PlacementsResultsCriteria.admissionyear' => $data['admissionyear']
                    )
                )
            );
            //debug($already_recorded_range);
            foreach ($already_recorded_range as $ar => $sr) {
                $sr = $sr['PlacementsResultsCriteria'];
                //debug($sr);
                if (
                    ($data['result_from'] <= $sr['result_from'] && $sr['result_from'] <= $data['result_to'])
                    || ($data['result_from'] <= $sr['result_to'] && $sr['result_to'] <= $data['result_to'])
                    || ($sr['result_from'] <= $data['result_from'] && $data['result_to'] <= $sr['result_to'])
                    || $data['result_from'] <= $sr['result_from'] && $sr['result_to'] <= $data['result_to']
                ) {
                    $this->invalidate(
                        'result_from_to',
                        'The given grade range is not uniqe. Please make sure that "result from" and/or "result to" is
					not already recorded.'
                    );
                    return false;
                }
            }
        }
        return true;
    }


    /**
     * Method to return the reserved place for each result category
     *
     * @return array
     */
    public function getListOfGradeCategory($academicyear = null, $college_id = null)
    {

        $result = $this->find('all', array(
            'fields' => array(
                'PlacementsResultsCriteria.id',
                'PlacementsResultsCriteria.result_from',
                'PlacementsResultsCriteria.result_to'
            ),
            'conditions' => array(
                'PlacementsResultsCriteria.college_id' => $college_id,
                'PlacementsResultsCriteria.admissionyear LIKE' => $academicyear . '%'
            ),
            'recursive' => -1
        ));
        return $result;
    }

    /**
     * Model validation against continutiy of grade
     */
    public function gradeRangeContinuty($data = null)
    {

        $check_no_entry = $this->find(
            'count',
            array(
                'conditions' => array(
                    'PlacementsResultsCriteria.college_id' => $data['college_id'],
                    'PlacementsResultsCriteria.admissionyear' => $data['admissionyear']
                )
            )
        );
        if ($check_no_entry != 0) {
            $min_from_all = $this->find(
                'first',
                array(
                    'fields' => array("MIN(result_from)"),
                    'conditions' => array(
                        'PlacementsResultsCriteria.college_id' => $data['college_id'],
                        'PlacementsResultsCriteria.admissionyear' => $data['admissionyear']
                    )
                )
            );
            $min = $min_from_all[0]["MIN(result_from)"];

            if ($data['result_to'] < $min && $data['result_to'] > $data['result_from']) {
                return true;
            } else {
                $this->invalidate(
                    'grade_range_continuty',
                    'The result To  should be less
	                  than ' . $min . '.'
                );
                return false;
            }
        } else {
            return true;
        }
    }

    public function checkUnique($data = null)
    {

        //debug($data);
        $count = $this->hasAny(array(
            'PlacementsResultsCriteria.college_id' =>
                $data['college_id'],
            'PlacementsResultsCriteria.admissionyear' => $data['admissionyear'],
            'PlacementsResultsCriteria.name' => $data['name']
        ));
        //debug($count);
        /*$count = $this->find('count',array('conditions'=>array('PlacementsResultsCriteria.college_id'=>
        $data['college_id'],
        'PlacementsResultsCriteria.admissionyear'=>$data['aadmissionyear'])));

        */
        if ($count) {
            return false;
        } else {
            return true;
        }
    }

    public function getPlacementResultCriteria($college_id, $academicYear)
    {

        $resultList = array();
        $placementResultCriteria = $this->find(
            'all',
            array(
                'conditions' => array(
                    'PlacementsResultsCriteria.college_id' => $college_id,
                    'PlacementsResultsCriteria.admissionyear' => $academicYear
                )
            )
        );

        $resultList['all'] = 'All';
        foreach ($placementResultCriteria as $k => $v) {
            $resultList[$v['PlacementsResultsCriteria']['id']] = $v['PlacementsResultsCriteria']['name'] . '(' . $v['PlacementsResultsCriteria']['result_from'] . '-' . $v['PlacementsResultsCriteria']['result_to'] . ')';
        }
        return $resultList;
    }
}
