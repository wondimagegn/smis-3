<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class PlacementResultSettingsTable extends Table
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

        $this->setTable('placement_result_settings');
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
            ->scalar('result_type')
            ->maxLength('result_type', 200)
            ->requirePresence('result_type', 'create')
            ->notEmptyString('result_type');

        $validator
            ->numeric('percent')
            ->requirePresence('percent', 'create')
            ->notEmptyString('percent');

        $validator
            ->requirePresence('round', 'create')
            ->notEmptyString('round');

        $validator
            ->integer('max_result')
            ->requirePresence('max_result', 'create')
            ->notEmptyString('max_result');

        $validator
            ->scalar('applied_for')
            ->maxLength('applied_for', 200)
            ->requirePresence('applied_for', 'create')
            ->notEmptyString('applied_for');

        $validator
            ->integer('group_identifier')
            ->requirePresence('group_identifier', 'create')
            ->notEmptyString('group_identifier');

        $validator
            ->scalar('academic_year')
            ->maxLength('academic_year', 30)
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
        $rules->add($rules->existsIn(['program_type_id'], 'ProgramTypes'));

        return $rules;
    }

    public function reformat($data = array())
    {

        $reformatedData = array();
        //	$group_identifier = strtotime(date('Y-m-d h:i:sa'));
        if (isset($data) && !empty($data)) {

            $firstData = $data['PlacementResultSetting'][1];

            $findSettingGroup = classRegistry::init('PlacementRoundParticipant')->find("first", array(
                'conditions' => array(
                    'PlacementRoundParticipant.applied_for' => $firstData['applied_for'],
                    'PlacementRoundParticipant.program_id' => $firstData['program_id'],
                    'PlacementRoundParticipant.program_type_id' => $firstData['program_type_id'],
                    'PlacementRoundParticipant.academic_year' => $firstData['academic_year'],
                    'PlacementRoundParticipant.placement_round' => $firstData['round'],
                ),
                'recursive' => -1
            ));

            foreach ($data['PlacementResultSetting'] as $dk => $dv) {
                $isSettingAlreadyRecorded = $this->find('first', array(
                    'conditions' => array(
                        'PlacementResultSetting.result_type' => $firstData['result_type'],
                        'PlacementResultSetting.percent' => $firstData['percent'],
                        'PlacementResultSetting.round' => $firstData['round'],
                        'PlacementResultSetting.applied_for' => $findSettingGroup['PlacementRoundParticipant']['applied_for'],
                        'PlacementResultSetting.group_identifier' => $findSettingGroup['PlacementRoundParticipant']['group_identifier'],
                        'PlacementResultSetting.academic_year' => $firstData['academic_year'],
                        'PlacementResultSetting.program_id' => $firstData['program_id'],
                        'PlacementResultSetting.program_type_id' => $firstData['program_type_id'],
                    ),
                    'recursive' => -1
                ));

                $reformatedData['PlacementResultSetting'][$dk] = $dv;

                if (isset($isSettingAlreadyRecorded['PlacementResultSetting']) && !empty($isSettingAlreadyRecorded['PlacementResultSetting'])) {
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

        // Array after removing duplicates
        //$xunique=array_unique($reformatedData);

        $reformatedDataDuplicateRemoved['PlacementResultSetting'] = array_unique($reformatedData['PlacementResultSetting'], SORT_REGULAR);

        if (count($reformatedData['PlacementResultSetting']) > count($reformatedDataDuplicateRemoved['PlacementResultSetting'])) {
            $this->invalidate('result_type', 'Please remove the duplicated rows, and try again.');
            return false;
        }

        $sumPercent = 0;

        if (!empty($reformatedData['PlacementResultSetting'])) {
            foreach ($reformatedData['PlacementResultSetting'] as $k => $v) {
                $sumPercent += $v['percent'];
            }
        }

        if ($sumPercent != 100) {
            $this->invalidate('percent', 'Please make sure the percent of result setting for all must be 100%, and try again.');
            return false;
        }

        return $reformatedData;
    }

    public function isDuplicated($data = array())
    {
        if (isset($data) && !empty($data)) {
            $firstData = $data['PlacementResultSetting'][1];
            $count = $this->find("first", array(
                'conditions' => array(
                    'PlacementResultSetting.result_type' => $firstData['result_type'],
                    // 'PlacementResultSetting.percent' => $firstData['percent'],
                    'PlacementResultSetting.applied_for' => $firstData['applied_for'],
                    'PlacementResultSetting.program_id' => $firstData['program_id'],
                    'PlacementResultSetting.program_type_id' => $firstData['program_type_id'],
                    'PlacementResultSetting.academic_year' => $firstData['academic_year'],
                    'PlacementResultSetting.round' => $firstData['round']
                ),
                'recursive' => -1
            ));

            if (!isset($count['PlacementResultSetting']['id']) && isset($count['PlacementResultSetting']['group_identifier']) && !empty($count['PlacementResultSetting']['group_identifier'])) {
                return $count['PlacementResultSetting']['group_identifier'];
            }
        }

        return false;
    }
}
