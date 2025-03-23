<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class GraduationRequirementsTable extends Table
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

        $this->setTable('graduation_requirements');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Programs', [
            'foreignKey' => 'program_id',
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
            ->numeric('cgpa')
            ->requirePresence('cgpa', 'create')
            ->notEmptyString('cgpa');

        $validator
            ->scalar('academic_year')
            ->requirePresence('academic_year', 'create')
            ->notEmptyString('academic_year');

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

        $rules->add($rules->existsIn(['program_id'], 'Programs'));

        return $rules;
    }


    public function getMinimumGraduationCGPA($program_id = null, $admission_year = null)
    {

        $gr_detail = $this->find(
            'first',
            array(
                'conditions' =>
                    array(
                        'GraduationRequirement.program_id' => $program_id,
                        'GraduationRequirement.academic_year <= ' . substr($admission_year, 0, 4)
                    ),
                'order' =>
                    array(
                        'GraduationRequirement.academic_year DESC'
                    ),
                'recursive' => -1
            )
        );
        if (isset($gr_detail['GraduationRequirement']['cgpa'])) {
            return $gr_detail['GraduationRequirement']['cgpa'];
        } else {
            return 0;
        }
    }
}
