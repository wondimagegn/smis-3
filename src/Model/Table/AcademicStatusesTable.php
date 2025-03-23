<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class AcademicStatusesTable extends Table
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

        $this->setTable('academic_statuses');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('AcademicStands', [
            'foreignKey' => 'academic_status_id',
        ]);
        $this->hasMany('HistoricalStudentExamStatuses', [
            'foreignKey' => 'academic_status_id',
            'propertyName' => 'HistoricalStudentExamStatus',
        ]);
        $this->hasMany('OtherAcademicRules', [
            'foreignKey' => 'academic_status_id',
            'propertyName' => 'OtherAcademicRule',
        ]);
        $this->hasMany('StudentExamStatuses', [
            'foreignKey' => 'academic_status_id',
            'propertyName' => 'StudentExamStatus',
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

        $validator
            ->scalar('name')
            ->maxLength('name', 20)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->requirePresence('order', 'create')
            ->notEmptyString('order');

        $validator
            ->boolean('computable')
            ->notEmptyString('computable');

        return $validator;
    }

    public function canItBeDeleted($id = null)
    {

        if ($this->StudentExamStatus->find(
                'count',
                array('conditions' => array('StudentExamStatus.academic_status_id' => $id))
            ) > 0) {
            return false;
        }
        if ($this->AcademicStand->find('count', array('conditions' => array('AcademicStand.academic_status_id' => $id))
            ) > 0) {
            return false;
        } else {
            return false;
        }
    }
}
