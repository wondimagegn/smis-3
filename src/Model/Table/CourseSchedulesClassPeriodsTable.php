<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CourseSchedulesClassPeriods Model
 *
 * @property \App\Model\Table\CourseSchedulesTable&\Cake\ORM\Association\BelongsTo $CourseSchedules
 * @property \App\Model\Table\ClassPeriodsTable&\Cake\ORM\Association\BelongsTo $ClassPeriods
 *
 * @method \App\Model\Entity\CourseSchedulesClassPeriod get($primaryKey, $options = [])
 * @method \App\Model\Entity\CourseSchedulesClassPeriod newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\CourseSchedulesClassPeriod[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\CourseSchedulesClassPeriod|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CourseSchedulesClassPeriod saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\CourseSchedulesClassPeriod patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\CourseSchedulesClassPeriod[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\CourseSchedulesClassPeriod findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class CourseSchedulesClassPeriodsTable extends Table
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

        $this->setTable('course_schedules_class_periods');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('CourseSchedules', [
            'foreignKey' => 'course_schedule_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('ClassPeriods', [
            'foreignKey' => 'class_period_id',
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
        $rules->add($rules->existsIn(['course_schedule_id'], 'CourseSchedules'));
        $rules->add($rules->existsIn(['class_period_id'], 'ClassPeriods'));

        return $rules;
    }
}
