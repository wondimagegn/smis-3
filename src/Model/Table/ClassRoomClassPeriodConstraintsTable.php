<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ClassRoomClassPeriodConstraints Model
 *
 * @property \App\Model\Table\ClassRoomsTable&\Cake\ORM\Association\BelongsTo $ClassRooms
 * @property \App\Model\Table\ClassPeriodsTable&\Cake\ORM\Association\BelongsTo $ClassPeriods
 *
 * @method \App\Model\Entity\ClassRoomClassPeriodConstraint get($primaryKey, $options = [])
 * @method \App\Model\Entity\ClassRoomClassPeriodConstraint newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ClassRoomClassPeriodConstraint[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ClassRoomClassPeriodConstraint|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ClassRoomClassPeriodConstraint saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ClassRoomClassPeriodConstraint patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ClassRoomClassPeriodConstraint[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ClassRoomClassPeriodConstraint findOrCreate($search, callable $callback = null, $options = [])
 */
class ClassRoomClassPeriodConstraintsTable extends Table
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

        $this->setTable('class_room_class_period_constraints');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('ClassRooms', [
            'foreignKey' => 'class_room_id',
            'joinType' => 'INNER',
            'propertyName' => 'ClassRoom',
        ]);
        $this->belongsTo('ClassPeriods', [
            'foreignKey' => 'class_period_id',
            'joinType' => 'INNER',
            'propertyName' => 'ClassPeriod',
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
            ->boolean('active')
            ->allowEmptyString('active');

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
        $rules->add($rules->existsIn(['class_period_id'], 'ClassPeriods'));

        return $rules;
    }
}
