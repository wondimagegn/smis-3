<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class CampusesTable extends Table
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

        $this->setTable('campuses');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('AcceptedStudents', [
            'foreignKey' => 'campus_id',
            'propertyName' => 'AcceptedStudent'
        ]);
        $this->hasMany('ClassRoomBlocks', [
            'foreignKey' => 'campus_id',
            'propertyName' => 'ClassRoomBlock'
        ]);
        $this->hasMany('Colleges', [
            'foreignKey' => 'campus_id',
            'propertyName' => 'College'
        ]);
        $this->hasMany('DormitoryBlocks', [
            'foreignKey' => 'campus_id',
            'propertyName' => 'DormitoryBlock'
        ]);
        $this->hasMany('MealHalls', [
            'foreignKey' => 'campus_id',
            'propertyName' => 'MealHall'
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
            ->maxLength('name', 200)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('description')
            ->allowEmptyString('description');

        $validator
            ->integer('male_capacity')
            ->notEmptyString('male_capacity');

        $validator
            ->integer('female_capacity')
            ->notEmptyString('female_capacity');

        $validator
            ->integer('available_for_college')
            ->notEmptyString('available_for_college');

        $validator
            ->boolean('active')
            ->notEmptyString('active');

        $validator
            ->scalar('campus_code')
            ->maxLength('campus_code', 100)
            ->allowEmptyString('campus_code');

        return $validator;
    }

    function checkUnique()
    {
        $count = 0;
        if (!empty($this->data['Campus']['id'])) {
            $count = $this->find('count', array('conditions' => array('Campus.id <> ' => $this->data['Campus']['id'], 'Campus.name' => trim($this->data['Campus']['name']))));
        } else {
            $count = $this->find('count', array('conditions' => array('Campus.name' => trim($this->data['Campus']['name']))));
        }

        if ($count > 0) {
            return false;
        }

        return true;
    }

    function canItBeDeleted($campus_id = null)
    {
        if ($this->College->find('count', array('conditions' => array('College.campus_id' => $campus_id))) > 0) {
            return false;
        } else {
            return true;
        }
    }
}
