<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class GradeScalesTable extends Table
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

        $this->setTable('grade_scales');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('GradeTypes', [
            'foreignKey' => 'grade_type_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Programs', [
            'foreignKey' => 'program_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('ExamGrades', [
            'foreignKey' => 'grade_scale_id',
        ]);
        $this->hasMany('GradeScaleDetails', [
            'foreignKey' => 'grade_scale_id',
        ]);
        $this->hasMany('GradeScalePublishedCourses', [
            'foreignKey' => 'grade_scale_id',
        ]);
        $this->hasMany('PublishedCourses', [
            'foreignKey' => 'grade_scale_id',
        ]);
        $this->hasMany('RejectedExamGrades', [
            'foreignKey' => 'grade_scale_id',
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
            ->notEmptyString('name', 'Please provide a name.')
            ->add('name', 'unique', [
                'rule' => function ($value, $context) {
                    $exists = $this->find()
                        ->where(['name' => $value])
                        ->count();
                    return $exists === 0;
                },
                'message' => 'This name is already taken, use a different name.'
            ]);

        $validator
            ->numeric('program_id', 'Select a valid program type.');

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
        $rules->add($rules->existsIn(['grade_type_id'], 'GradeTypes'));
        $rules->add($rules->existsIn(['program_id'], 'Programs'));

        return $rules;
    }
}
