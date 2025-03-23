<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class PreferencesTable extends Table
{

    public function initialize(array $config): void
    {

        parent::initialize($config);

        $this->setTable('preferences');
        $this->setPrimaryKey('id');

        // Associations
        $this->belongsTo('AcceptedStudents', [
            'foreignKey' => 'accepted_student_id',
        ]);

        $this->belongsTo('Colleges', [
            'foreignKey' => 'college_id',
        ]);

        $this->belongsTo('Departments', [
            'foreignKey' => 'department_id',
        ]);

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
        ]);

        // Add timestamp behavior
        $this->addBehavior('Timestamp');
    }

    public function validationDefault(Validator $validator)
    {

        $validator
            ->notEmptyString('academicyear', 'Select Academic Year.');

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {

        // Ensure valid foreign keys
        $rules->add($rules->existsIn(['accepted_student_id'], 'AcceptedStudents'));
        $rules->add($rules->existsIn(['college_id'], 'Colleges'));
        $rules->add($rules->existsIn(['department_id'], 'Departments'));
        $rules->add($rules->existsIn(['user_id'], 'Users'));

        return $rules;
    }

    /**
     *This function will validate the student has entered his preference once.
     *return boolean
     */
    public function isAlreadyEnteredPreference($acceptedStudentId = null)
    {

        /*
        $count=$this->find('count',array('conditions'=>array("OR"=>array('Preference.accepted_student_id'=>$acceptedStudentId,'Preference.user_id'=>$acceptedStudentId))));
        */
        $user = ClassRegistry::init('User')->find('first', array('conditions' => array('User.id' => $acceptedStudentId))
        );

        $count = 0;
        if (empty($user)) {
            $countAcc = $this->find(
                'count',
                array('conditions' => array('Preference.accepted_student_id' => $acceptedStudentId))
            );
        }


        $countUser = $this->find('count', array('conditions' => array('Preference.user_id' => $acceptedStudentId)));

        if (isset($countAcc)) {
            $count = $countAcc;
        } elseif ($countUser) {
            $count = $countUser;
        }

        if ($count) {
            $this->invalidate(
                'alreadypreferencerecorded',
                'Validation Error: You have already
	        recorded preference for selected student.'
            );
            return true;
        } else {
            $user = ClassRegistry::init('User')->find(
                'first',
                array('conditions' => array('User.id' => $acceptedStudentId))
            );
            if (!empty($user)) {
                $acceptedStudent = ClassRegistry::init('AcceptedStudent')->find(
                    'first',
                    array('conditions' => array('AcceptedStudent.user_id' => $user['User']['id']), 'recursive' => -1)
                );
                $count = $this->find(
                    'count',
                    array('conditions' => array('accepted_student_id' => $acceptedStudent['AcceptedStudent']['id']))
                );
                if ($count) {
                    $this->invalidate(
                        'alreadypreferencerecorded',
                        'Validation Error: You have already
			        recorded preference for selected student.'
                    );
                    return true;
                }
            }

            return false;
        }
    }

    /**
     *Department selected
     */
    public function isDepartmentSelected($data)
    {

        $arrayselected = array();
        foreach ($data as $value) {
            if (!empty($value['department_id'])) {
                $arrayselected[$value['department_id']] = $value['department_id'];
            }
        }
        return $arrayselected;
    }

    /**
     * This function will validate student has selected orderely their department choice
     * return boolean
     */
    public function isAllPreferenceDepartmentSelectedDifferent($data = null)
    {

        $array = array();
        if (!empty($data)) {
            foreach ($data as $value) {
                if (!empty($value['department_id'])) {
                    $array[] = $value['department_id'];
                } else {
                    $this->invalidate(
                        'department',
                        'Validation Error: Please select deparment  preference for each preference order.'
                    );
                    return false;
                }
            }
        }
        $arrayvaluecount = array();
        $arrayvaluecount = array_count_values($array);

        //return $arrayvaluecount;
        foreach ($arrayvaluecount as $k => $v) {
            if ($v > 1) {
                $this->invalidate(
                    'preference',
                    'Validation Error: Please select different department preference for each preference order.'
                );
                return false;
            }
        }
        return true;
    }

    public function getPreferenceStat($college_id = null, $academic_year = null, $type = null)
    {

        //Get participating departments
        $stat = array();
        $participatingDepartments = ClassRegistry::init('ParticipatingDepartment')->find(
            'all',
            array(
                'conditions' =>
                    array(
                        'ParticipatingDepartment.college_id' => $college_id,
                        'ParticipatingDepartment.academic_year' => $academic_year
                    ),
                'fields' =>
                    array(
                        'id',
                        'department_id',
                        'developing_regions_id'
                    ),
                'contain' =>
                    array(
                        'Department.name'
                    )
            )
        );
        $placementsResultsCriterias = ClassRegistry::init('PlacementsResultsCriteria')->find(
            'all',
            array(
                'conditions' =>
                    array(
                        'PlacementsResultsCriteria.college_id' => $college_id,
                        'PlacementsResultsCriteria.admissionyear' => $academic_year,
                    ),
                'recursive' => -1
            )
        );

        $isPrepartory = ClassRegistry::init('PlacementsResultsCriteria')->isPrepartoryResult(
            $academic_year,
            $college_id
        );
        //debug($participatingDepartments);
        foreach ($participatingDepartments as $participatingDepartment) {
            $index = count($stat);
            $stat[$index]['department_id'] = $participatingDepartment['ParticipatingDepartment']['department_id'];
            $stat[$index]['department_name'] = $participatingDepartment['Department']['name'];
            for ($i = 1; $i <= count($participatingDepartments); $i++) {
                $options = array(
                    'conditions' =>
                        array(
                            'Preference.department_id' => $participatingDepartment['ParticipatingDepartment']['department_id'],
                            'Preference.college_id' => $college_id,
                            'Preference.academicyear' => $academic_year,
                            'Preference.preferences_order' => $i
                        )
                );
                $options['conditions'][0] = 'Preference.accepted_student_id IN (SELECT id FROM accepted_students WHERE academicyear = \'' . $academic_year . '\' AND college_id = \'' . $college_id . '\'';
                if (strcasecmp($type, 'female') == 0) {
                    $options['conditions'][0] .= ' AND (sex = \'female\' OR sex = \'f\'))';
                } elseif (strcasecmp($type, 'disable') == 0) {
                    $options['conditions'][0] .= ' AND disability IS NOT NULL AND disability <> \'\')';
                } elseif (!empty($participatingDepartment['ParticipatingDepartment']['developing_regions_id']) && strcasecmp(
                        $type,
                        'region'
                    ) == 0) {
                    $options['conditions'][0] .= ' AND region_id IN (\'' . $participatingDepartment['ParticipatingDepartment']['developing_regions_id'] . '\'))';
                } else {
                    $options['conditions'][0] .= ')';
                }
                $stat[$index]['count'][$i]['~total~'] = $this->find('count', $options);
                foreach ($placementsResultsCriterias as $placementsResultsCriteria) {
                    $options = array(
                        'conditions' =>
                            array(
                                'Preference.department_id' => $participatingDepartment['ParticipatingDepartment']['department_id'],
                                'Preference.college_id' => $college_id,
                                'Preference.academicyear' => $academic_year,
                                'Preference.preferences_order' => $i
                            )
                    );
                    if ($isPrepartory) {
                        $options['conditions'][0] = 'Preference.accepted_student_id IN (SELECT id FROM accepted_students WHERE academicyear = \'' . $academic_year . '\' AND college_id = \'' . $college_id . '\' AND EHEECE_total_results >= ' . $placementsResultsCriteria['PlacementsResultsCriteria']['result_from'] . ' AND EHEECE_total_results <= ' . $placementsResultsCriteria['PlacementsResultsCriteria']['result_to'];
                    } else {
                        $options['conditions'][0] = 'Preference.accepted_student_id IN (SELECT id FROM accepted_students WHERE academicyear = \'' . $academic_year . '\' AND college_id = \'' . $college_id . '\' AND freshman_result >= ' . $placementsResultsCriteria['PlacementsResultsCriteria']['result_from'] . ' AND freshman_result <= ' . $placementsResultsCriteria['PlacementsResultsCriteria']['result_to'];
                    }
                    if (strcasecmp($type, 'female') == 0) {
                        $options['conditions'][0] .= ' AND (sex = \'female\' OR sex = \'f\'))';
                    } elseif (strcasecmp($type, 'disable') == 0) {
                        $options['conditions'][0] .= ' AND disability IS NOT NULL AND disability <> \'\')';
                    } elseif (!empty($participatingDepartment['ParticipatingDepartment']['developing_regions_id']) && strcasecmp(
                            $type,
                            'region'
                        ) == 0) {
                        $options['conditions'][0] .= ' AND region_id IN (\'' . $participatingDepartment['ParticipatingDepartment']['developing_regions_id'] . '\'))';
                    } else {
                        $options['conditions'][0] .= ')';
                    }
                    $stat[$index]['count'][$i][$placementsResultsCriteria['PlacementsResultsCriteria']['name']] = $this->find(
                        'count',
                        $options
                    );
                }
            }
        }

        return $stat;
    }
}
