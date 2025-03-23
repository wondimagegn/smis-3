<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ExcludedCourseFromTranscripts Model
 *
 * @property \App\Model\Table\CourseRegistrationsTable&\Cake\ORM\Association\BelongsTo $CourseRegistrations
 * @property \App\Model\Table\CourseExemptionsTable&\Cake\ORM\Association\BelongsTo $CourseExemptions
 *
 * @method \App\Model\Entity\ExcludedCourseFromTranscript get($primaryKey, $options = [])
 * @method \App\Model\Entity\ExcludedCourseFromTranscript newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ExcludedCourseFromTranscript[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ExcludedCourseFromTranscript|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ExcludedCourseFromTranscript saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ExcludedCourseFromTranscript patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ExcludedCourseFromTranscript[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ExcludedCourseFromTranscript findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ExcludedCourseFromTranscriptsTable extends Table
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

        $this->setTable('excluded_course_from_transcripts');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('CourseRegistrations', [
            'foreignKey' => 'course_registration_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('CourseExemptions', [
            'foreignKey' => 'course_exemption_id',
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
            ->scalar('minute_number')
            ->maxLength('minute_number', 20)
            ->requirePresence('minute_number', 'create')
            ->notEmptyString('minute_number');

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
        $rules->add($rules->existsIn(['course_registration_id'], 'CourseRegistrations'));
        $rules->add($rules->existsIn(['course_exemption_id'], 'CourseExemptions'));

        return $rules;
    }
}
