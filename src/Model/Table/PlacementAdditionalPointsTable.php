<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class PlacementAdditionalPointsTable extends Table
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

        $this->setTable('placement_additional_points');
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
            ->scalar('type')
            ->maxLength('type', 200)
            ->requirePresence('type', 'create')
            ->notEmptyString('type');

        $validator
            ->numeric('point')
            ->requirePresence('point', 'create')
            ->notEmptyString('point');

        $validator
            ->requirePresence('round', 'create')
            ->notEmptyString('round');

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
        debug($data);
        $reformatedData = array();
        //	$group_identifier = strtotime(date('Y-m-d h:i:sa'));
        if (isset($data) && !empty($data)) {
            $firstData = $data['PlacementAdditionalPoint'][1];
            $findSettingGroup = classRegistry::init('PlacementRoundParticipant')->find("first", array(
                'conditions' => array(

                    'PlacementRoundParticipant.applied_for' => $firstData['applied_for'],
                    'PlacementRoundParticipant.program_id' => $firstData['program_id'],
                    'PlacementRoundParticipant.program_type_id'
                    => $firstData['program_type_id'],

                    'PlacementRoundParticipant.academic_year' => $firstData['academic_year'],
                    'PlacementRoundParticipant.placement_round' => $firstData['round']
                ),
                'recursive' => -1
            ));

            foreach ($data['PlacementAdditionalPoint'] as $dk => $dv) {

                $isSettingAlreadyRecorded=$this->find('first',array('conditions'=>array('PlacementAdditionalPoint.type'=>$firstData['type'],
                    'PlacementAdditionalPoint.point'=>$firstData['point'],
                    'PlacementAdditionalPoint.round'=>$firstData['round'],
                    'PlacementAdditionalPoint.applied_for'=>$firstData['applied_for'],

                    'PlacementAdditionalPoint.academic_year'=>$firstData['academic_year'],
                    'PlacementAdditionalPoint.program_id'=>$firstData['program_id'],
                    'PlacementAdditionalPoint.program_type_id'=>$firstData['program_type_id'],
                ),
                    'recursive'=>-1));
                $reformatedData['PlacementAdditionalPoint'][$dk] = $dv;
                if(isset($isSettingAlreadyRecorded['PlacementAdditionalPoint']) && !empty($isSettingAlreadyRecorded['PlacementAdditionalPoint'])){
                    $reformatedData['PlacementAdditionalPoint'][$dk]['id']=$isSettingAlreadyRecorded['PlacementAdditionalPoint']['id'];
                }

                $reformatedData['PlacementAdditionalPoint'][$dk]['group_identifier'] = $findSettingGroup['PlacementRoundParticipant']['group_identifier'];
                $reformatedData['PlacementAdditionalPoint'][$dk]['applied_for'] = $firstData['applied_for'];
                $reformatedData['PlacementAdditionalPoint'][$dk]['program_id'] = $firstData['program_id'];
                $reformatedData['PlacementAdditionalPoint'][$dk]['program_type_id'] = $firstData['program_type_id'];
                $reformatedData['PlacementAdditionalPoint'][$dk]['academic_year'] = $firstData['academic_year'];
                $reformatedData['PlacementAdditionalPoint'][$dk]['round'] = $firstData['round'];
            }
        }
        // Array after removing duplicates
        //$xunique=array_unique($reformatedData);

        $reformatedDataDuplicateRemoved['PlacementAdditionalPoint'] = array_unique($reformatedData['PlacementAdditionalPoint'], SORT_REGULAR);
        if (count($reformatedData['PlacementAdditionalPoint']) > count($reformatedDataDuplicateRemoved['PlacementAdditionalPoint'])) {
            $this->invalidate(
                'result_type',
                'Please remove the duplicated rows, and try again.'
            );
            return false;
        }


        return $reformatedData;
    }
    public function isDuplicated($data = array())
    {

        if (isset($data) && !empty($data)) {
            $firstData = $data['PlacementAdditionalPoint'][1];
            $count = $this->find("first", array(
                'conditions' => array(
                    'PlacementAdditionalPoint.type' =>
                        $firstData['type'],

                    'PlacementAdditionalPoint.applied_for' => $firstData['applied_for'],
                    'PlacementAdditionalPoint.program_id' => $firstData['program_id'],
                    'PlacementAdditionalPoint.program_type_id'
                    => $firstData['program_type_id'],

                    'PlacementAdditionalPoint.academic_year' => $firstData['academic_year'],
                    'PlacementAdditionalPoint.round' => $firstData['round']
                ),
                'recursive' => -1
            ));
            if (isset($count['PlacementAdditionalPoint']['group_identifier']) && !empty($count['PlacementAdditionalPoint']['group_identifier'])) {
                return $count['PlacementAdditionalPoint']['group_identifier'];
            }
        }
        return false;
    }
}
