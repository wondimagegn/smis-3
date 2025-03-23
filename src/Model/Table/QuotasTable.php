<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class QuotasTable extends Table
{

    public function initialize(array $config)
    {

        parent::initialize($config);

        $this->setTable('quotas');
        $this->setPrimaryKey('id');

        // Define associations
        $this->belongsTo('Colleges', [
            'foreignKey' => 'college_id',
            'joinType' => 'INNER',
        ]);

        $this->addBehavior('Timestamp');
    }

    public function validationDefault(Validator $validator)
    {

        $validator
            ->requirePresence('female', 'create')
            ->notEmptyString('female', 'The female quota cannot be empty.')
            ->numeric('female', 'Please enter a numeric value.')
            ->greaterThan('female', 0, 'Please enter a number greater than zero.');

        $validator
            ->requirePresence('regions', 'create')
            ->notEmptyString('regions', 'The regions quota cannot be empty.')
            ->numeric('regions', 'Please enter a numeric value.')
            ->greaterThan('regions', 0, 'Please enter a number greater than zero.');

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {

        $rules->add($rules->existsIn(['college_id'], 'Colleges'));

        return $rules;
    }

    /**
     *Check given number is greather than zero
     *
     * @return boolean
     */
    public function checkQuotaPositiveAndGreaterThanZero($data, $fieldName)
    {

        if (isset($fieldName)) {
            if ($data < 0) {
                return false;
            }
        }
        return true;
    }

    /**
     *This method validate the  female qutoa should be less than or equal to total
     * accepted female students.
     *
     * @return boolean
     */
    public function checkAvailableFemaleInTheGivenAcademicYear(
        $data,
        $college_id = null,
        $academicyear = null
    ) {

        $female = $this->College->AcceptedStudent->find('count', array(
            'conditions' => array(
                'AcceptedStudent.sex' => 'female',
                'AcceptedStudent.college_id' => $college_id
            )
        ));
        if ($female <= $data['female']) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *This method validate the  regions qutoa should be less than or equal to total
     * students in the given region.
     *
     * @return boolean
     */
    public function checkAvailableRegionStudentInTheGivenAcademicYear(
        $data,
        $college_id = null,
        $region_ids,
        $academicyear = null
    ) {

        $regions = $this->College->AcceptedStudent->find('count', array(
            'conditions' => array(
                'AcceptedStudent.region_id' => array($region_ids),
                'AcceptedStudent.college_id' => $college_id
            )
        ));
        if ($regions <= $data['regions']) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * This method count the number of quota for the given academic year and college
     * @ return true or false
     */
    public function isQuotaRecorded($academicyear = null, $college_id = null)
    {

        if ($college_id) {
            $count = $this->find(
                'count',
                array(
                    'conditions' => array(
                        'Quota.college_id' => $college_id,
                        'Quota.academicyear LIKE ' => $academicyear . '%'
                    )
                )
            );
            if ($count) {
                return 1;
            } else {
                return 0;
            }
        }
        return 0;
    }

    /**
     * Method to return the quota
     *
     * @return array
     */
    public function quotaCategory($academicyear = null, $college_id = null)
    {

        if ($college_id) {
            $result = $this->find(
                'all',
                array(
                    'conditions' => array(
                        'Quota.college_id' => $college_id,
                        'Quota.academicyear LIKE ' => $academicyear . '%'
                    )
                )
            );
            return $result;
        }
    }

    /**
     * Method to return the quota
     *
     * @return array
     */
    public function quotaNameAndValue($academicyear = null, $college_id = null)
    {

        if ($college_id) {
            $result = $this->find(
                'first',
                array(
                    'conditions' => array(
                        'Quota.college_id' => $college_id,
                        'Quota.academicyear LIKE ' => $academicyear . '%'
                    )
                )
            );
            return $result;
        }
    }
}
