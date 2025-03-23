<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * HistoricalStudentExamStatuses Model
 *
 * @property \App\Model\Table\StudentsTable&\Cake\ORM\Association\BelongsTo $Students
 * @property \App\Model\Table\AcademicStatusesTable&\Cake\ORM\Association\BelongsTo $AcademicStatuses
 *
 * @method \App\Model\Entity\HistoricalStudentExamStatus get($primaryKey, $options = [])
 * @method \App\Model\Entity\HistoricalStudentExamStatus newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\HistoricalStudentExamStatus[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\HistoricalStudentExamStatus|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\HistoricalStudentExamStatus saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\HistoricalStudentExamStatus patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\HistoricalStudentExamStatus[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\HistoricalStudentExamStatus findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class HistoricalStudentExamStatusesTable extends Table
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

        $this->setTable('historical_student_exam_statuses');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('AcademicStatuses', [
            'foreignKey' => 'academic_status_id',
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
            ->scalar('semester')
            ->maxLength('semester', 3)
            ->requirePresence('semester', 'create')
            ->notEmptyString('semester');

        $validator
            ->scalar('academic_year')
            ->maxLength('academic_year', 9)
            ->requirePresence('academic_year', 'create')
            ->notEmptyString('academic_year');

        $validator
            ->numeric('grade_point_sum')
            ->requirePresence('grade_point_sum', 'create')
            ->notEmptyString('grade_point_sum');

        $validator
            ->numeric('credit_hour_sum')
            ->requirePresence('credit_hour_sum', 'create')
            ->notEmptyString('credit_hour_sum');

        $validator
            ->numeric('m_grade_point_sum')
            ->requirePresence('m_grade_point_sum', 'create')
            ->notEmptyString('m_grade_point_sum');

        $validator
            ->numeric('m_credit_hour_sum')
            ->requirePresence('m_credit_hour_sum', 'create')
            ->notEmptyString('m_credit_hour_sum');

        $validator
            ->numeric('sgpa')
            ->requirePresence('sgpa', 'create')
            ->notEmptyString('sgpa');

        $validator
            ->numeric('cgpa')
            ->allowEmptyString('cgpa');

        $validator
            ->numeric('mcgpa')
            ->requirePresence('mcgpa', 'create')
            ->notEmptyString('mcgpa');

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
        $rules->add($rules->existsIn(['student_id'], 'Students'));
        $rules->add($rules->existsIn(['academic_status_id'], 'AcademicStatuses'));

        return $rules;
    }
}
