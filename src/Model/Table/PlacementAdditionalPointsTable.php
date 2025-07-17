<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

class PlacementAdditionalPointsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('placement_additional_points');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Programs', [
            'foreignKey' => 'program_id',
            'joinType' => 'INNER'
        ]);

        $this->belongsTo('ProgramTypes', [
            'foreignKey' => 'program_type_id',
            'joinType' => 'INNER'
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->notEmptyString('type', 'Please select type')
            ->notEmptyString('point', 'Please provide point value')
            ->notEmptyString('applied_for', 'Please select the unit to apply')
            ->notEmptyString('academic_year', 'Please select academic year')
            ->numeric('round', 'Please select placement round')
            ->notEmptyString('round', 'Please select placement round')
            ->numeric('program_id', 'Please select program')
            ->notEmptyString('program_id', 'Please select program')
            ->numeric('program_type_id', 'Please select program type')
            ->notEmptyString('program_type_id', 'Please select program type');

        return $validator;
    }

    public function reformat(array $data = [])
    {
        $reformatedData = [];

        if (!empty($data)) {
            $firstData = $data['PlacementAdditionalPoint'][1];

            $findSettingGroup = TableRegistry::getTableLocator()->get('PlacementRoundParticipants')->find('first')
                ->where([
                    'PlacementRoundParticipants.applied_for' => $firstData['applied_for'],
                    'PlacementRoundParticipants.program_id' => $firstData['program_id'],
                    'PlacementRoundParticipants.program_type_id' => $firstData['program_type_id'],
                    'PlacementRoundParticipants.academic_year' => $firstData['academic_year'],
                    'PlacementRoundParticipants.placement_round' => $firstData['round']
                ])
                ->disableHydration()
                ->first();

            foreach ($data['PlacementAdditionalPoint'] as $dk => $dv) {
                $isSettingAlreadyRecorded = $this->find('first')
                    ->where([
                        'PlacementAdditionalPoints.type' => $firstData['type'],
                        'PlacementAdditionalPoints.point' => $firstData['point'],
                        'PlacementAdditionalPoints.round' => $firstData['round'],
                        'PlacementAdditionalPoints.applied_for' => $firstData['applied_for'],
                        'PlacementAdditionalPoints.academic_year' => $firstData['academic_year'],
                        'PlacementAdditionalPoints.program_id' => $firstData['program_id'],
                        'PlacementAdditionalPoints.program_type_id' => $firstData['program_type_id']
                    ])
                    ->disableHydration()
                    ->first();

                $reformatedData['PlacementAdditionalPoint'][$dk] = $dv;

                if (!empty($isSettingAlreadyRecorded['PlacementAdditionalPoint'])) {
                    $reformatedData['PlacementAdditionalPoint'][$dk]['id'] = $isSettingAlreadyRecorded['PlacementAdditionalPoint']['id'];
                }

                $reformatedData['PlacementAdditionalPoint'][$dk]['group_identifier'] = $findSettingGroup['PlacementRoundParticipant']['group_identifier'];
                $reformatedData['PlacementAdditionalPoint'][$dk]['applied_for'] = $firstData['applied_for'];
                $reformatedData['PlacementAdditionalPoint'][$dk]['program_id'] = $firstData['program_id'];
                $reformatedData['PlacementAdditionalPoint'][$dk]['program_type_id'] = $firstData['program_type_id'];
                $reformatedData['PlacementAdditionalPoint'][$dk]['academic_year'] = $firstData['academic_year'];
                $reformatedData['PlacementAdditionalPoint'][$dk]['round'] = $firstData['round'];
            }
        }

        $reformatedDataDuplicateRemoved['PlacementAdditionalPoint'] = array_unique($reformatedData['PlacementAdditionalPoint'], SORT_REGULAR);

        if (count($reformatedData['PlacementAdditionalPoint']) > count($reformatedDataDuplicateRemoved['PlacementAdditionalPoint'])) {
            $this->validationErrors['result_type'] = 'Please remove the duplicated rows, and try again.';
            return false;
        }

        return $reformatedData;
    }

    public function isDuplicated(array $data = [])
    {
        if (!empty($data)) {
            $firstData = $data['PlacementAdditionalPoint'][1];
            $count = $this->find('first')
                ->where([
                    'PlacementAdditionalPoints.type' => $firstData['type'],
                    'PlacementAdditionalPoints.applied_for' => $firstData['applied_for'],
                    'PlacementAdditionalPoints.program_id' => $firstData['program_id'],
                    'PlacementAdditionalPoints.program_type_id' => $firstData['program_type_id'],
                    'PlacementAdditionalPoints.academic_year' => $firstData['academic_year'],
                    'PlacementAdditionalPoints.round' => $firstData['round']
                ])
                ->disableHydration()
                ->first();

            if (!empty($count['PlacementAdditionalPoint']['group_identifier'])) {
                return $count['PlacementAdditionalPoint']['group_identifier'];
            }
        }

        return false;
    }
}
