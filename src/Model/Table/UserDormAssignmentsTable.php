<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class UserDormAssignmentsTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('user_dorm_assignments');
        $this->setPrimaryKey('id');

        // Define associations
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('DormitoryBlocks', [
            'foreignKey' => 'dormitory_block_id',
            'joinType' => 'INNER',
        ]);
    }

    /**
     * Validation rules.
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->notEmptyString('user_id', 'User ID is required')
            ->notEmptyString('dormitory_block_id', 'Dormitory Block ID is required');

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
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['dormitory_block_id'], 'DormitoryBlocks'));

        return $rules;
    }

    /**
     * Check for duplicate dormitory assignments.
     */
    public function checkDuplicationAssignment($data = null)
    {
        if (empty($data['UserDormAssignment'])) {
            return true;
        }

        foreach ($data['UserDormAssignment'] as $id => $value) {
            $dorm = $this->DormitoryBlocks->find()
                ->select(['block_name'])
                ->where(['id' => $value['dormitory_block_id']])
                ->first();

            $check = $this->find()
                ->where([
                    'user_id' => $value['user_id'],
                    'dormitory_block_id' => $value['dormitory_block_id']
                ])
                ->count();

            if ($check > 0) {
                return __('The selected user has already been assigned to the {0} dormitory block.', $dorm->block_name);
            }
        }

        return true;
    }

    /**
     * Get organized dormitory block assignments grouped by campus.
     */
    public function dormitoryBlocksAssignmentOrganizedByCampus()
    {
        $assignments = $this->find()
            ->contain(['Users' => ['fields' => ['id', 'full_name']], 'DormitoryBlocks' => ['Campuses']])
            ->toArray();

        $organizedAssignments = [];

        foreach ($assignments as $assignment) {
            $campusName = $assignment->dormitory_block->campus->name;
            $organizedAssignments[$campusName][] = [
                'User' => $assignment->user,
                'DormitoryBlock' => $assignment->dormitory_block,
                'UserDormAssignment' => $assignment,
            ];
        }

        return $organizedAssignments;
    }
}
