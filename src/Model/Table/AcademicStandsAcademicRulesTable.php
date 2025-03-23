<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AcademicStandsAcademicRules Model
 *
 * @property \App\Model\Table\AcademicStandsTable&\Cake\ORM\Association\BelongsTo $AcademicStands
 * @property \App\Model\Table\AcademicRulesTable&\Cake\ORM\Association\BelongsTo $AcademicRules
 *
 * @method \App\Model\Entity\AcademicStandsAcademicRule get($primaryKey, $options = [])
 * @method \App\Model\Entity\AcademicStandsAcademicRule newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\AcademicStandsAcademicRule[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\AcademicStandsAcademicRule|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\AcademicStandsAcademicRule saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\AcademicStandsAcademicRule patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\AcademicStandsAcademicRule[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\AcademicStandsAcademicRule findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class AcademicStandsAcademicRulesTable extends Table
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

        $this->setTable('academic_stands_academic_rules');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('AcademicStands', [
            'foreignKey' => 'academic_stand_id',
            'joinType' => 'INNER',
            'propertyName'=>'AcademicStand'
        ]);
        $this->belongsTo('AcademicRules', [
            'foreignKey' => 'academic_rule_id',
            'joinType' => 'INNER',
            'propertyName'=>'AcademicRule'
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
            ->boolean('archive')
            ->requirePresence('archive', 'create')
            ->notEmptyString('archive');

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
        $rules->add($rules->existsIn(['academic_stand_id'], 'AcademicStands'));
        $rules->add($rules->existsIn(['academic_rule_id'], 'AcademicRules'));

        return $rules;
    }
}
