<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class CourseCategoriesTable extends Table
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

        $this->setTable('course_categories');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Curriculums', [
            'foreignKey' => 'curriculum_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('Courses', [
            'foreignKey' => 'course_category_id',
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
            ->notEmptyString('name', 'Please provide name, it is required.')
            ->numeric('total_credit', 'Please provide total credit, it is required.')
            ->greaterThanOrEqual('total_credit', 0, 'Total credit must be greater than or equal to 0.')
            ->numeric('mandatory_credit', 'Please provide mandatory credit, it is required.')
            ->greaterThanOrEqual('mandatory_credit', 0, 'Mandatory credit must be greater than or equal to 0.');

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
        $rules->add($rules->existsIn(['curriculum_id'], 'Curriculums'));

        return $rules;
    }
}
