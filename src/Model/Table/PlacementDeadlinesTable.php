<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Core\Configure;

class PlacementDeadlinesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('placement_deadlines');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Programs', [
            'foreignKey' => 'program_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('ProgramTypes', [
            'foreignKey' => 'program_type_id',
            'joinType' => 'INNER',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->dateTime('deadline', 'Invalid datetime format')
            ->notEmptyDateTime('deadline')
            ->numeric('program_id', 'Program ID must be numeric')
            ->notEmptyString('program_id')
            ->numeric('program_type_id', 'Program Type ID must be numeric')
            ->notEmptyString('program_type_id')
            ->notEmptyString('academic_year', 'Academic year is required')
            ->numeric('group_identifier', 'Group identifier must be numeric')
            ->notEmptyString('group_identifier')
            ->notEmptyString('applied_for', 'Applied for is required');

        return $validator;
    }

    public function getDeadlineStatus(array $acceptedStudentdetail = [], string $applied_for, int $placementRound, string $academic_year): int
    {
        $status = $this->find('first')
            ->where([
                'PlacementDeadlines.program_id IN' => Configure::read('programs_available_for_placement_preference'),
                'PlacementDeadlines.applied_for' => $applied_for,
                'PlacementDeadlines.program_type_id IN' => Configure::read('program_types_available_for_placement_preference'),
                'PlacementDeadlines.placement_round' => $placementRound,
                'PlacementDeadlines.academic_year LIKE' => $academic_year . '%'
            ])
            ->disableHydration()
            ->first();

        if (!empty($status)) {
            $currentDateTime = date('Y-m-d H:i:s');
            if ($status['PlacementDeadline']['deadline'] > $currentDateTime) {
                return 1; // Defined, not passed
            } elseif ($status['PlacementDeadline']['deadline'] < $currentDateTime) {
                return 2; // Defined, passed
            }
        }

        return 0; // No deadline defined
    }

    public function isDuplicated(array $data = []): bool
    {
        if (!empty($data)) {
            $conditions = [
                'PlacementDeadlines.applied_for' => $data['PlacementDeadline']['applied_for'],
                'PlacementDeadlines.program_id' => $data['PlacementDeadline']['program_id'],
                'PlacementDeadlines.program_type_id' => $data['PlacementDeadline']['program_type_id'],
                'PlacementDeadlines.academic_year' => $data['PlacementDeadline']['academic_year'],
                'PlacementDeadlines.placement_round' => $data['PlacementDeadline']['placement_round']
            ];

            if (!empty($data['PlacementDeadline']['id'])) {
                $conditions['PlacementDeadlines.id <>'] = $data['PlacementDeadline']['id'];
            }

            $definedCount = $this->find('count')
                ->where($conditions)
                ->disableHydration()
                ->count();

            return $definedCount > 0;
        }

        return false;
    }
}
