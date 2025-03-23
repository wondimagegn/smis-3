<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class PlacementDeadlinesTable extends Table
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

        $this->setTable('placement_deadlines');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Programs', [
            'foreignKey' => 'program_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('ProgramTypes', [
            'foreignKey' => 'program_type_id',
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
            ->dateTime('deadline')
            ->requirePresence('deadline', 'create')
            ->notEmptyDateTime('deadline');

        $validator
            ->scalar('academic_year')
            ->maxLength('academic_year', 30)
            ->requirePresence('academic_year', 'create')
            ->notEmptyString('academic_year');

        $validator
            ->integer('group_identifier')
            ->requirePresence('group_identifier', 'create')
            ->notEmptyString('group_identifier');

        $validator
            ->scalar('applied_for')
            ->maxLength('applied_for', 200)
            ->requirePresence('applied_for', 'create')
            ->notEmptyString('applied_for');

        $validator
            ->integer('placement_round')
            ->requirePresence('placement_round', 'create')
            ->notEmptyString('placement_round');

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
        $rules->add($rules->existsIn(['program_type_id'], 'ProgramTypes'));

        return $rules;
    }

    // 0 - no deadline is defined at all
    // 1 - deadline defined and not passed
    // 2 - deadline defined and passed

    public function getDeadlineStatus($acceptedStudentdetail = array(), $applied_for, $placementRound, $academic_year)
    {
        $status = $this->find('first', array('conditions' => array(
            //'PlacementDeadline.program_id' => $acceptedStudentdetail['AcceptedStudent']['program_id'],
            'PlacementDeadline.program_id' => Configure::read('programs_available_for_placement_preference'),
            'PlacementDeadline.applied_for' => $applied_for,
            //'PlacementDeadline.program_type_id' => $acceptedStudentdetail['AcceptedStudent']['program_type_id'],
            'PlacementDeadline.program_type_id' => Configure::read('program_types_available_for_placement_preference'),
            'PlacementDeadline.placement_round' => $placementRound,
            'PlacementDeadline.academic_year LIKE ' => $academic_year . '%',
            // 'PlacementDeadline.deadline > ' => date("Y-m-d H:i:s")
        )));

        debug($status);

        if (!empty($status)) {
            if ($status['PlacementDeadline']['deadline'] > date("Y-m-d H:i:s")) {
                // deined not passed
                return 1;
            } elseif ($status['PlacementDeadline']['deadline'] < date("Y-m-d H:i:s")) {
                //defined and passed
                return 2;
            }
        }
        return 0;
    }

    public function isDuplicated($data = array())
    {
        if (isset($data) && !empty($data)) {
            if (isset($data['PlacementDeadline']['id'])) {
                $definedCount = $this->find("count", array(
                    'conditions' => array(
                        'PlacementDeadline.id <>' => $data['PlacementDeadline']['id'],
                        'PlacementDeadline.applied_for' => $data['PlacementDeadline']['applied_for'],
                        'PlacementDeadline.program_id' => $data['PlacementDeadline']['program_id'],
                        'PlacementDeadline.program_type_id' => $data['PlacementDeadline']['program_type_id'],
                        'PlacementDeadline.academic_year' => $data['PlacementDeadline']['academic_year'],
                        'PlacementDeadline.placement_round' => $data['PlacementDeadline']['placement_round']
                    ),
                    'recursive' => -1
                ));
            } else {
                $definedCount = $this->find("count", array(
                    'conditions' => array(
                        'PlacementDeadline.applied_for' => $data['PlacementDeadline']['applied_for'],
                        'PlacementDeadline.program_id' => $data['PlacementDeadline']['program_id'],
                        'PlacementDeadline.program_type_id' => $data['PlacementDeadline']['program_type_id'],
                        'PlacementDeadline.academic_year' => $data['PlacementDeadline']['academic_year'],
                        'PlacementDeadline.placement_round' => $data['PlacementDeadline']['placement_round']
                    ),
                    'recursive' => -1
                ));
            }

            if ($definedCount) {
                return true;
            }
        }

        return false;
    }
}
