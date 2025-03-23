<?php

namespace App\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class ExamRoomNumberOfInvigilatorsTable extends Table
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

        $this->setTable('exam_room_number_of_invigilators');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('ClassRooms', [
            'foreignKey' => 'class_room_id',
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
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('academic_year')
            ->maxLength('academic_year', 9)
            ->requirePresence('academic_year', 'create')
            ->notEmptyString('academic_year');

        $validator
            ->scalar('semester')
            ->maxLength('semester', 3)
            ->requirePresence('semester', 'create')
            ->notEmptyString('semester');

        $validator
            ->requirePresence('number_of_invigilator', 'create')
            ->notEmptyString('number_of_invigilator');

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

        $rules->add($rules->existsIn(['class_room_id'], 'ClassRooms'));

        return $rules;
    }


    public function is_class_room_used($id = null)
    {

        $count = $this->find(
            'count',
            array('conditions' => array('ExamRoomNumberOfInvigilator.class_room_id' => $id), 'limit' => 2)
        );
        return $count;
    }
}
