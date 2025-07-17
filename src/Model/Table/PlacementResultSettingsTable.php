<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

class PlacementResultSettingsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('placement_result_settings');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->notEmptyString('result_type', 'Please select result type')
            ->notEmptyString('percent', 'Please provide percent')
            ->notEmptyString('applied_for', 'Please select the unit you need to apply.')
            ->notEmptyString('academic_year', 'Please select academic year')
            ->numeric('round', 'Please select placement round')
            ->notEmptyString('round', 'Please select placement round')
            ->numeric('program_id', 'Please select admission level')
            ->notEmptyString('program_id', 'Please select admission level')
            ->numeric('program_type_id', 'Please select admission type')
            ->notEmptyString('program_type_id', 'Please select admission type');

        return $validator;
    }

    public function reformat(array $data = [])
    {
        $reformatedData = [];

        if (!empty($data)) {
            $firstData = $data['PlacementResultSetting'][1];

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

            foreach ($data['PlacementResultSetting'] as $dk => $dv) {
                $isSettingAlreadyRecorded = $this->find('first')
                    ->where([
                        'PlacementResultSettings.result_type' => $firstData['result_type'],
                        'PlacementResultSettings.percent' => $firstData['percent'],
                        'PlacementResultSettings.round' => $firstData['round'],
                        'PlacementResultSettings.applied_for' => $findSettingGroup['PlacementRoundParticipant']['applied_for'],
                        'PlacementResultSettings.group_identifier' => $findSettingGroup['PlacementRoundParticipant']['group_identifier'],
                        'PlacementResultSettings.academic_year' => $firstData['academic_year'],
                        'PlacementResultSettings.program_id' => $firstData['program_id'],
                        'PlacementResultSettings.program_type_id' => $firstData['program_type_id']
                    ])
                    ->disableHydration()
                    ->first();

                $reformatedData['PlacementResultSetting'][$dk] = $dv;

                if (!empty($isSettingAlreadyRecorded['PlacementResultSetting'])) {
                    $reformatedData['PlacementResultSetting'][$dk]['id'] = $isSettingAlreadyRecorded['PlacementResultSetting']['id'];
                }

                $reformatedData['PlacementResultSetting'][$dk]['group_identifier'] = $findSettingGroup['PlacementRoundParticipant']['group_identifier'];
                $reformatedData['PlacementResultSetting'][$dk]['applied_for'] = $firstData['applied_for'];
                $reformatedData['PlacementResultSetting'][$dk]['program_id'] = $firstData['program_id'];
                $reformatedData['PlacementResultSetting'][$dk]['program_type_id'] = $firstData['program_type_id'];
                $reformatedData['PlacementResultSetting'][$dk]['academic_year'] = $firstData['academic_year'];
                $reformatedData['PlacementResultSetting'][$dk]['round'] = $firstData['round'];
            }
        }

        $reformatedDataDuplicateRemoved['PlacementResultSetting'] = array_unique($reformatedData['PlacementResultSetting'], SORT_REGULAR);

        if (count($reformatedData['PlacementResultSetting']) > count($reformatedDataDuplicateRemoved['PlacementResultSetting'])) {
            $this->validationErrors['result_type'] = 'Please remove the duplicated rows, and try again.';
            return false;
        }

        $sumPercent = 0;

        if (!empty($reformatedData['PlacementResultSetting'])) {
            foreach ($reformatedData['PlacementResultSetting'] as $v) {
                $sumPercent += $v['percent'];
            }
        }

        if ($sumPercent != 100) {
            $this->validationErrors['percent'] = 'Please make sure the percent of result setting for all must be 100%, and try again.';
            return false;
        }

        return $reformatedData;
    }

    public function isDuplicated(array $data = [])
    {
        if (!empty($data)) {
            $firstData = $data['PlacementResultSetting'][1];
            $count = $this->find('first')
                ->where([
                    'PlacementResultSettings.result_type' => $firstData['result_type'],
                    'PlacementResultSettings.applied_for' => $firstData['applied_for'],
                    'PlacementResultSettings.program_id' => $firstData['program_id'],
                    'PlacementResultSettings.program_type_id' => $firstData['program_type_id'],
                    'PlacementResultSettings.academic_year' => $firstData['academic_year'],
                    'PlacementResultSettings.round' => $firstData['round']
                ])
                ->disableHydration()
                ->first();

            if (empty($count['PlacementResultSetting']['id']) && !empty($count['PlacementResultSetting']['group_identifier'])) {
                return $count['PlacementResultSetting']['group_identifier'];
            }
        }

        return false;
    }
}
